import * as fabric from 'fabric';

// Debug Livewire events and connections
console.log('Editor canvas script loaded');

// Add a global flag to control console logging
const DEBUG_LOGS = true;

// Custom logger function to reduce console noise
function log(message, ...args) {
    if (DEBUG_LOGS) {
        console.log(message, ...args);
    }
}

// Patch to fix non-passive wheel event listeners in Fabric.js
(function patchEventListeners() {
    // Store original method
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    
    // Override addEventListener to make wheel events passive by default
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Check if it's a wheel event
        if (type === 'wheel' || type === 'mousewheel' || type === 'DOMMouseScroll') {
            // If options is a boolean, it's the useCapture parameter
            if (typeof options === 'boolean') {
                // Use an object with the original boolean for capture and add passive true
                options = { 
                    capture: options,
                    passive: true 
                };
            } else if (typeof options === 'object') {
                // If options already exists as an object but doesn't specify passive
                if (options.passive === undefined) {
                    options = { 
                        ...options, 
                        passive: true 
                    };
                }
            } else {
                // No options provided, make it passive
                options = { passive: true };
            }
        }
        
        // Call original method with possibly modified options
        return originalAddEventListener.call(this, type, listener, options);
    };
    
    console.log('Patched addEventListener for wheel events to be passive by default');
})();

// Use "load" event instead of "DOMContentLoaded" for non-critical initialization
// This will run after all resources have loaded, reducing the impact on critical render path
window.addEventListener('load', function() {
    console.log('DOM content loaded, initializing Fabric.js canvas');
    
    // Initialize important event listeners immediately
    initializeEventListeners();
    
    // Start a progressive initialization sequence
    requestAnimationFrame(() => {
        initializeCanvas(() => {
            // After canvas is initialized, wait for next frame to set up wheel handlers
            requestAnimationFrame(() => {
                initializeWheelHandlers();
                
                // Wait for next frame to set up Livewire handlers
                requestAnimationFrame(() => {
                    if (typeof Livewire !== 'undefined') {
                        setupLivewireHandlers();
                    } else {
                        window.addEventListener('livewire:initialized', setupLivewireHandlers);
                    }
                    
                    // Dispatch canvas initialized event for other modules
                    window.dispatchEvent(new CustomEvent('canvas:initialized', {
                        detail: { 
                            canvas,
                            fabric  // Include the Fabric.js instance as well
                        }
                    }));
                    
                    // Set up grid after everything else is ready - lowest priority
                    setTimeout(setupGrid, 100);
                });
            });
        });
    });
});

// Global variables - moved outside functions for shared access
let canvas;
const gridLines = [];
const loadingFeatures = new Set();
const pendingSelectionRequests = new Set();
let gridSize = 20;
let canvasWidth = 600;
let canvasHeight = 600;
let initialLoadingComplete = false; // Track if initial loading is complete

// Track processed events to prevent duplicates
const processedEvents = new Set();

function initializeEventListeners() {
    // Set up global event debug - but only at DEBUG level
    if (DEBUG_LOGS) {
        const eventsToWatch = [
            'feature-selected',
            'direct-update-canvas',
            'update-canvas',
            'layer-visibility-changed',
            'layer-opacity-changed',
            'layer-lock-changed',
            'toggle-move-mode'
        ];
        
        eventsToWatch.forEach(eventName => {
            window.addEventListener(eventName, (event) => {
                log(`Caught ${eventName} event on window:`, event.detail);
            });
        });
    }
    
    // Listen for ONLY standard browser events (not Livewire events) to prevent duplication
    window.addEventListener('feature-selected', (event) => {
        // Generate a unique ID for this event to prevent duplicate processing
        const eventId = `feature-selected-${Date.now()}-${JSON.stringify(event.detail)}`;
        if (processedEvents.has(eventId)) return;
        processedEvents.add(eventId);
        
        // Cleanup old events (keep last 20)
        if (processedEvents.size > 20) {
            const toRemove = Array.from(processedEvents).slice(0, processedEvents.size - 20);
            toRemove.forEach(id => processedEvents.delete(id));
        }
        
        log('Feature selected event caught in main-canvas:', event.detail);
        
        const featureId = Array.isArray(event.detail) ? event.detail[0] : event.detail;
        
        if (canvas) {
            handleFeatureSelected(featureId);
        } else {
            pendingSelectionRequests.add(featureId);
        }
    });
    
    // Handle direct canvas updates - only listen on window level to avoid duplicates
    function handleDirectUpdateCanvas(detail, source) {
        // Only process if canvas is initialized
        if (!canvas) return;
        
        // Generate a unique ID for this event to prevent duplicate processing
        const eventId = `direct-update-canvas-${Date.now()}-${JSON.stringify(detail)}`;
        if (processedEvents.has(eventId)) return;
        processedEvents.add(eventId);
        
        // Cleanup old events (keep last 20)
        if (processedEvents.size > 20) {
            const toRemove = Array.from(processedEvents).slice(0, processedEvents.size - 20);
            toRemove.forEach(id => processedEvents.delete(id));
        }
        
        log(`Processing direct update canvas data from ${source}:`, detail);
        
        let featureData = null;
        
        // Try different data formats that might be sent
        if (detail && detail.feature) {
            featureData = detail.feature;
        } else if (Array.isArray(detail) && detail.length > 0) {
            if (detail[0].feature) {
                featureData = detail[0].feature;
            } else if (detail[0].id && detail[0].image_path) {
                featureData = detail[0];
            }
        } else if (detail && detail.id && detail.image_path) {
            featureData = detail;
        }
        
        if (featureData) {
            addFeatureToCanvas(featureData);
            } else {
            console.error('Could not extract feature data from event:', detail);
        }
    }
    
    // Use ONE primary event listener for direct-update-canvas events
    window.addEventListener('direct-update-canvas', (event) => {
        handleDirectUpdateCanvas(event.detail, 'window');
    });
    
    // Handle livewire update-canvas event
    document.addEventListener('livewire:update-canvas', (event) => {
        // Only process if canvas is initialized
        if (!canvas) return;
        
        // Generate a unique ID for this event to prevent duplicate processing
        const eventId = `livewire-update-canvas-${Date.now()}-${JSON.stringify(event.detail)}`;
        if (processedEvents.has(eventId)) return;
        processedEvents.add(eventId);
        
        // Cleanup old events (keep last 20)
        if (processedEvents.size > 20) {
            const toRemove = Array.from(processedEvents).slice(0, processedEvents.size - 20);
            toRemove.forEach(id => processedEvents.delete(id));
        }
        
        log('Livewire update-canvas event caught. Detail:', event.detail);
        
        let updateData = event.detail;
        
        // Ensure we have the actual data, not wrapped in an array sometimes
        if (Array.isArray(updateData) && updateData.length > 0 && updateData[0]?.selectedFeatures) {
            updateData = updateData[0];
        }
        
        if (updateData && updateData.selectedFeatures) {
            log('Processing update-canvas event with features:', updateData.selectedFeatures);
            
            // --- Rebuild Canvas based on received order --- 
            // No sorting or reversing - simply use features in the order they were added
            const incomingFeatures = updateData.selectedFeatures;
            const updateZOrder = updateData.updateZOrder === true;
            
            log('Processing features in original order:', incomingFeatures.map(f => `${f.name} (type: ${f.feature_type})`));
            log('Update Z order flag:', updateZOrder);
            
            const existingObjectsMap = {};
            
            // 1. Map existing feature objects by ID for quick lookup
            canvas.getObjects().forEach(obj => {
                if (obj.data && obj.data.featureId) {
                    existingObjectsMap[obj.data.featureId] = obj;
                }
            });
            
            if (updateZOrder) {
                // When updateZOrder is true, we're handling layer reordering from the panel
                // 2. Remove all *existing* feature objects from the canvas
                Object.values(existingObjectsMap).forEach(obj => {
                    canvas.remove(obj);
                });
                
                log('Removed existing features for reordering, preparing to re-add in order.');
                
                // 3. Add features in the exact order provided by the backend
                // The order in incomingFeatures already represents the desired stacking order in the canvas
                incomingFeatures.forEach((feature, index) => {
                    log(`Adding/Updating feature at index ${index}: ${feature.id} (${feature.name})`);
                    const existingObject = existingObjectsMap[feature.id];
                    
                    if (existingObject) {
                        // Re-add the existing object instance (preserves transformations)
                        existingObject.set({
                            visible: feature.visible !== undefined ? feature.visible : true,
                            opacity: feature.opacity ? feature.opacity / 100 : 1,
                            locked: feature.locked || false,
                            hasControls: feature.locked ? false : moveEnabled,
                            hasBorders: feature.locked ? false : moveEnabled,
                            selectable: feature.locked ? false : moveEnabled,
                            evented: feature.locked ? false : moveEnabled,
                            lockMovementX: feature.locked ? true : !moveEnabled,
                            lockMovementY: feature.locked ? true : !moveEnabled,
                            lockRotation: feature.locked ? true : !moveEnabled,
                            lockScalingX: feature.locked ? true : !moveEnabled,
                            lockScalingY: feature.locked ? true : !moveEnabled,
                        });
                        canvas.add(existingObject);
                    } else {
                        // Feature is new, add it to the canvas
                        addFeatureToCanvas(feature);
                    }
                });
            } else {
                // Standard update without reordering
                incomingFeatures.forEach((feature, index) => {
                    log(`Processing feature at index ${index}: ${feature.id} (${feature.name})`);
                    const existingObject = existingObjectsMap[feature.id];
                    
                    if (existingObject) {
                        // Just update properties without changing z-order
                        existingObject.set({
                            visible: feature.visible !== undefined ? feature.visible : true,
                            opacity: feature.opacity ? feature.opacity / 100 : 1,
                            locked: feature.locked || false,
                            hasControls: feature.locked ? false : moveEnabled,
                            hasBorders: feature.locked ? false : moveEnabled,
                            selectable: feature.locked ? false : moveEnabled,
                            evented: feature.locked ? false : moveEnabled,
                            lockMovementX: feature.locked ? true : !moveEnabled,
                            lockMovementY: feature.locked ? true : !moveEnabled,
                            lockRotation: feature.locked ? true : !moveEnabled,
                            lockScalingX: feature.locked ? true : !moveEnabled,
                            lockScalingY: feature.locked ? true : !moveEnabled,
                        });
                        canvas.renderAll();
                    } else {
                        // Feature is new, add it to the canvas
                        addFeatureToCanvas(feature);
                    }
                });
            }
            
            // 4. Ensure grid lines are behind everything
            gridLines.forEach(line => {
                canvas.sendObjectToBack(line);
            });

            // 5. Render the canvas with the updated order and features
            canvas.renderAll();
            log('Canvas re-rendered with updated feature order.');
        }
    });
    
    // Additional event listeners for layer panel interactions
    document.addEventListener('livewire:update-feature-visibility', (event) => {
        // Only process if canvas is initialized
        if (!canvas) return;
        
        log('Update feature visibility event caught:', event.detail);
        const featureId = event.detail.featureId;
        const isVisible = event.detail.visible;
        
        // Find the object(s) with this feature ID
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        objects.forEach(obj => {
            obj.visible = isVisible;
        });
        
        canvas.renderAll();
    });
    
    document.addEventListener('livewire:update-feature-opacity', (event) => {
        // Only process if canvas is initialized
        if (!canvas) return;
        
        log('Update feature opacity event caught:', event.detail);
        const featureId = event.detail.featureId;
        const opacity = event.detail.opacity / 100; // Convert from 0-100 to 0-1
        
        // Find the object(s) with this feature ID
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        objects.forEach(obj => {
            obj.opacity = opacity;
        });
        
        canvas.renderAll();
    });
}

function handleFeatureSelected(featureId) {
    // Only process if canvas is initialized
    if (!canvas) return;
        
    // Try to find it on canvas first
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
    log(`Found ${objects.length} objects with feature ID ${featureId} on canvas`);
        
    if (objects.length > 0) {
        // Feature exists, select it
        log('Selecting existing feature on canvas');
        canvas.setActiveObject(objects[0]);
        canvas.renderAll();
    } else {
        // Feature doesn't exist yet, add to pending selection
        pendingSelectionRequests.add(featureId);
        log(`Added feature ${featureId} to pending selection requests. Current pending:`, 
            Array.from(pendingSelectionRequests));
        
        // Request feature to be added
        if (typeof Livewire !== 'undefined') {
            const component = Livewire.find(
                document.getElementById('main-canvas-component')?.getAttribute('wire:id')
            );
            
            if (component) {
                log('Dispatching request to get feature data');
                component.call('requestFeatureData', featureId);
            }
        }
    }
}

function initializeCanvas(callback) {
    // Check if the canvas element exists
    const canvasElement = document.getElementById('editor-canvas');
    if (!canvasElement) {
        console.error('Canvas element not found');
        return;
    }
    
    // Initialize Fabric.js canvas
    canvas = new fabric.Canvas('editor-canvas', {
        backgroundColor: '#334155', // Reverted to slate-700 for a medium-dark background
        selection: true,
        preserveObjectStacking: true,
        width: 600,
        height: 600
    });
    
    log('Canvas initialized:', canvas);
    
    // Set canvas dimensions
    canvasWidth = canvas.width;
    canvasHeight = canvas.height;
    
    // Fire callback when done
    if (typeof callback === 'function') {
        callback();
    }
}

function setupGrid() {
    if (!canvas) {
        console.error('Cannot setup grid - canvas not initialized');
        return;
    }
    
    log('Setting up grid with medium-dark background and lighter lines');
    
    // Clear existing grid lines
    gridLines.forEach(line => {
        canvas.remove(line);
    });
    gridLines.length = 0;
    
    // Grid line properties for dark background
    const lineOptions = {
        stroke: '#64748b', // Changed to slate-500 for lighter, distinct lines
        selectable: false,
        evented: false,
        hoverCursor: 'default',
        excludeFromExport: true // Grid lines should not be part of exported image
    };
    
    // Create grid lines efficiently - create and add them in batches
    const batchSize = 10; // Process lines in batches
    let verticalLinesDone = 0;
    let horizontalLinesDone = 0;
    // Ensure canvasWidth and canvasHeight are up-to-date if canvas can resize
    // For now, assuming they are set correctly during initializeCanvas
    const totalVerticalLines = Math.floor(canvasWidth / gridSize);
    const totalHorizontalLines = Math.floor(canvasHeight / gridSize);
    
    function createNextLinesBatch() {
        // Check if all lines have been created
        if (verticalLinesDone >= totalVerticalLines && horizontalLinesDone >= totalHorizontalLines) {
            // All done - send grid lines to the back and render
            gridLines.forEach(line => {
                canvas.sendObjectToBack(line);
            });
            canvas.renderAll();
            log('Grid setup complete. Total grid lines:', gridLines.length);
            return;
        }
        
        // Create vertical grid lines batch
        for (let i = 0; i < batchSize && verticalLinesDone < totalVerticalLines; i++) {
            verticalLinesDone++;
            const line = new fabric.Line(
                [verticalLinesDone * gridSize, 0, verticalLinesDone * gridSize, canvasHeight], 
                lineOptions
            );
            gridLines.push(line);
            canvas.add(line);
        }
        
        // Create horizontal grid lines batch
        for (let i = 0; i < batchSize && horizontalLinesDone < totalHorizontalLines; i++) {
            horizontalLinesDone++;
            const line = new fabric.Line(
                [0, horizontalLinesDone * gridSize, canvasWidth, horizontalLinesDone * gridSize], 
                lineOptions
            );
            gridLines.push(line);
            canvas.add(line);
        }
        
        // Schedule next batch for next frame
        requestAnimationFrame(createNextLinesBatch);
    }
    
    // Start creating grid lines
    createNextLinesBatch();
}

function initializeWheelHandlers() {
    if (!canvas) {
        console.error('Cannot initialize wheel handlers - canvas not initialized');
        return;
    }
    
    let wheelDebounceTimeout;
    const wheelDebounceTime = 50; // milliseconds - shorter than buttons for responsiveness
    
    // Update wheel event handling with passive compatibility
    canvas.wrapperEl.addEventListener('wheel', function(e) {
        // Only capture wheel events with Ctrl/Cmd key pressed for zooming
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            
            if (wheelDebounceTimeout) {
                clearTimeout(wheelDebounceTimeout);
            }
            
            wheelDebounceTimeout = setTimeout(() => {
                // Calculate zoom direction based on wheel delta
                const delta = e.deltaY;
                const zoom = canvas.getZoom();
                const newZoom = zoom * Math.pow(0.995, delta);
                
                // Limit zoom to reasonable bounds (0.5x to 3x)
                const boundedZoom = Math.min(Math.max(newZoom, 0.5), 3);
                
                // Apply zoom centered on cursor position
                canvas.zoomToPoint({ x: e.offsetX, y: e.offsetY }, boundedZoom);
                
                // Update Livewire component with new zoom level
                if (typeof Livewire !== 'undefined') {
                    const component = Livewire.find(
                        document.getElementById('main-canvas-component')?.getAttribute('wire:id')
                    );
                    if (component) {
                        component.call('zoomIn', boundedZoom);
                    }
                }
                
                // Update the zoom indicator if it exists
                const zoomIndicator = document.getElementById('zoom-level');
                if (zoomIndicator) {
                    zoomIndicator.textContent = `${Math.round(boundedZoom * 100)}%`;
                }
                
                wheelDebounceTimeout = null;
            }, wheelDebounceTime);
        }
    }, { passive: false }); // Changed to false since we're using preventDefault
}

function setupLivewireHandlers() {
    log('Livewire initialized');
    
    // Log all Livewire components for debugging
    log('Available Livewire components:', Livewire.all());
    
    const component = Livewire.find(
        document.getElementById('main-canvas-component')?.getAttribute('wire:id')
    );
    
    if (!component) {
        console.error('Cannot find main-canvas component. Element:', document.getElementById('main-canvas-component'));
        console.error('Element wire:id:', document.getElementById('main-canvas-component')?.getAttribute('wire:id'));
        return;
    }
    
    log('Found main-canvas component:', component);
    
    // Get initial moveEnabled state and apply to any existing canvas objects
    try {
        const moveEnabled = component.get('moveEnabled');
        log('Initial moveEnabled state:', moveEnabled);
        
        // Apply this state to any objects already on the canvas
        updateCanvasObjectsMoveState(moveEnabled);
        
        // Also initialize zoom level from component
        const zoomLevel = component.get('zoomLevel');
        if (zoomLevel) {
            const scale = zoomLevel / 100; // Convert from percentage to decimal
            log('Initial zoom level:', zoomLevel, '%, scale:', scale);
            applyCanvasZoom(scale);
        }
        
        // Load initial features if they exist
        console.log('Loading initial features...');
        loadExistingFeatures();
        
        // Add a helper event that can be dispatched to reload features at any time
        Livewire.on('reload-canvas-features', () => {
            console.log('Reload canvas features event received');
            loadExistingFeatures();
        });
    } catch (error) {
        console.error('Error getting initial state:', error);
    }
    
    // Setup event listeners for object selection
    canvas.on('object:selected', function(e) {
        const obj = e.target;
        if (obj && obj.data?.featureId) {
            log(`Object selected on canvas: ${obj.data.featureId}`);
            console.log('FABRIC POSITION: Object selected', {
                featureId: obj.data.featureId,
                left: obj.left,
                top: obj.top,
                angle: obj.angle
            });
            
            // Calculate width and height with scaling
            const width = obj.width * obj.scaleX;
            const height = obj.height * obj.scaleY;
            
            console.log('DIMENSION DEBUG: Object dimensions:', {
                originalWidth: obj.width,
                originalHeight: obj.height,
                scaleX: obj.scaleX,
                scaleY: obj.scaleY,
                calculatedWidth: width,
                calculatedHeight: height
            });
            
            // Dispatch event to update the layer panel
            document.dispatchEvent(new CustomEvent('fabricjs:object-selected', {
                detail: {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    width: obj.width,
                    height: obj.height
                }
            }));
            console.log('FABRIC POSITION: Dispatched fabricjs:object-selected event with width/height');
            
            // Also notify Livewire components
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('fabricjs:object-selected', {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    width: obj.width,
                    height: obj.height
                });
                console.log('FABRIC POSITION: Dispatched Livewire fabricjs:object-selected event with width/height');
            }
        }
    });
    
    // Setup object:modified event to capture position changes
    canvas.on('object:modified', function(e) {
        const obj = e.target;
        if (obj && obj.data?.featureId) {
            log(`Object modified on canvas: ${obj.data.featureId}`);
            console.log('FABRIC POSITION: Object modified', {
                featureId: obj.data.featureId,
                left: obj.left,
                top: obj.top,
                angle: obj.angle
            });
            
            // Calculate width and height with scaling
            const width = obj.width * obj.scaleX;
            const height = obj.height * obj.scaleY;
            
            console.log('DIMENSION DEBUG: Modified object dimensions:', {
                originalWidth: obj.width,
                originalHeight: obj.height,
                scaleX: obj.scaleX,
                scaleY: obj.scaleY,
                calculatedWidth: width,
                calculatedHeight: height
            });
            
            // Dispatch event with position data
            document.dispatchEvent(new CustomEvent('fabricjs:object-modified', {
                detail: {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    width: obj.width,
                    height: obj.height
                }
            }));
            console.log('FABRIC POSITION: Dispatched fabricjs:object-modified event with width/height');
            
            // Also notify Livewire components
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('fabricjs:object-modified', {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top, 
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    width: obj.width,
                    height: obj.height
                });
                console.log('FABRIC POSITION: Dispatched Livewire fabricjs:object-modified event with width/height');
            }
        }
    });
    
    // Add a scaling event listener to capture dimensions during scaling
    canvas.on('object:scaling', function(e) {
        const obj = e.target;
        if (obj && obj.data?.featureId) {
            log(`Object scaling on canvas: ${obj.data.featureId}`);
            
            // Calculate width and height with scaling
            const width = obj.width * obj.scaleX;
            const height = obj.height * obj.scaleY;
            
            console.log('DIMENSION DEBUG: Scaling object dimensions:', {
                originalWidth: obj.width,
                originalHeight: obj.height,
                scaleX: obj.scaleX,
                scaleY: obj.scaleY,
                calculatedWidth: width,
                calculatedHeight: height
            });
            
            // Dispatch event with position and dimension data
            document.dispatchEvent(new CustomEvent('fabricjs:object-scaling', {
                detail: {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    width: obj.width,
                    height: obj.height
                }
            }));
            console.log('FABRIC POSITION: Dispatched fabricjs:object-scaling event with width/height');
        }
    });
    
    // Listener for layer visibility change
    Livewire.on('layer-visibility-changed', (data) => {
        log('Layer visibility changed event:', data);
        const layerId = data.layerId || (data[0] ? data[0].layerId : null);
        const visible = data.visible !== undefined ? data.visible : (data[0] ? data[0].visible : true);
        
        if (layerId === null) {
            console.error('Missing layerId in layer-visibility-changed event');
            return;
        }
        
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === layerId);
        if (objects.length > 0) {
            objects[0].set('visible', visible);
            canvas.renderAll();
            log(`Set visibility for layer ${layerId} to ${visible}`);
        } else {
            log(`Layer ${layerId} not found on canvas for visibility change.`);
        }
    });
    
    // Listener for layer opacity change
    Livewire.on('layer-opacity-changed', (data) => {
        log('Layer opacity changed event:', data);
        const layerId = data.layerId || (data[0] ? data[0].layerId : null);
        const opacity = data.opacity !== undefined ? data.opacity : (data[0] ? data[0].opacity : 100);
        
        if (layerId === null) {
            console.error('Missing layerId in layer-opacity-changed event');
            return;
        }
        
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === layerId);
        if (objects.length > 0) {
            // Convert opacity from 0-100 to 0-1
            const fabricOpacity = opacity / 100;
            objects[0].set('opacity', fabricOpacity);
            canvas.renderAll();
            log(`Set opacity for layer ${layerId} to ${opacity/100} (from UI value ${opacity}%)`);
        } else {
            log(`Layer ${layerId} not found on canvas for opacity change.`);
        }
    });
    
    // Listener for layer lock change
    Livewire.on('layer-lock-changed', (data) => {
        log('Layer lock changed event:', data);
        const layerId = data.layerId || (data[0] ? data[0].layerId : null);
        const isLocked = data.locked !== undefined ? data.locked : (data[0] ? data[0].locked : false);
        
        if (layerId === null) {
            console.error('Missing layerId in layer-lock-changed event');
            return;
        }
        
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === layerId);
        if (objects.length > 0) {
            const fabricObject = objects[0];
            fabricObject.set({
                hasControls: !isLocked,
                hasBorders: !isLocked,
                selectable: !isLocked,
                evented: !isLocked, // Prevents events like 'selected' when locked
                lockMovementX: isLocked,
                lockMovementY: isLocked,
                lockRotation: isLocked,
                lockScalingX: isLocked,
                lockScalingY: isLocked,
                lockSkewingX: isLocked,
                lockSkewingY: isLocked
            });
            
            // If the object is currently selected and gets locked, deselect it
            if (isLocked && canvas.getActiveObject() === fabricObject) {
                canvas.discardActiveObject();
            }
            
            canvas.renderAll();
            log(`Set lock state for layer ${layerId} to ${isLocked}`);
        } else {
            log(`Layer ${layerId} not found on canvas for lock change.`);
        }
    });
    
    // Add listener for feature-removed event
    Livewire.on('feature-removed', (featureId) => {
        log('Feature removed event received:', featureId);
        
        // Handle both direct value and array formats for compatibility
        const id = typeof featureId === 'object' ? (featureId[0] || featureId) : featureId;
        
        // Find and remove the object from canvas
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId == id);
        if (objects.length > 0) {
            objects.forEach(obj => {
                canvas.remove(obj);
                log(`Removed object with feature ID ${id} from canvas`);
            });
            canvas.renderAll();
        } else {
            log(`No object found with feature ID ${id} on canvas`);
        }
    });
    
    // Listener for layer transform update
    Livewire.on('layer-transform-updated', (data) => {
        log('Layer transform updated event:', data);
        const layerId = data.layerId || (data[0] ? data[0].layerId : null);
        const transform = data.transform || (data[0] ? data[0].transform : null);
        
        if (!layerId || !transform) {
            console.error('Missing layerId or transform data in layer-transform-updated event', data);
            return;
        }
        
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === layerId);
        if (objects.length > 0) {
            const fabricObject = objects[0];
            
            // Keep track of current state before applying changes
            const currentScaleX = fabricObject.scaleX;
            const currentScaleY = fabricObject.scaleY;
            
            // Determine if this is just a position/rotation update or if it includes size changes
            const isPositionOnlyUpdate = transform.x !== undefined && 
                transform.y !== undefined && 
                (transform.width === undefined || transform.height === undefined);
            
            // Update position
            if (transform.x !== undefined && transform.y !== undefined) {
                fabricObject.set({
                    left: transform.x,
                    top: transform.y
                });
            }
            
            // Update rotation
            if (transform.rotation !== undefined) {
                fabricObject.set('angle', transform.rotation);
            }
            
            // Handle size updates when width/height are provided
            if (!isPositionOnlyUpdate && transform.width !== undefined && transform.height !== undefined) {
                // Get the original width/height (without scaling)
                const originalWidth = fabricObject.width;
                const originalHeight = fabricObject.height;
                
                if (originalWidth && originalHeight) {
                    // Check if we should scale independently (non-proportional)
                    const scaleIndependently = transform.scaleToWidth === false;
                    
                    if (scaleIndependently) {
                        // Calculate new scale factors independently
                        const scaleX = transform.width / originalWidth;
                        const scaleY = transform.height / originalHeight;
                        
                        // Apply different scale values for each dimension
                        fabricObject.set({
                            scaleX: scaleX,
                            scaleY: scaleY
                        });
                        
                        log(`Updated scale independently for layer ${layerId}: scaleX=${scaleX}, scaleY=${scaleY}`);
                    } else {
                        // Calculate new scale factors
                        const scaleX = transform.width / originalWidth;
                        const scaleY = transform.height / originalHeight;
                        
                        // Use the same scale value for both dimensions to preserve aspect ratio
                        const scale = Math.min(scaleX, scaleY);
                        
                        fabricObject.set({
                            scaleX: scale,
                            scaleY: scale
                        });
                        
                        log(`Updated scale for layer ${layerId} to ${scale} (preserved aspect ratio)`);
                    }
                }
            } else {
                // For position-only updates, maintain the current scale
                log(`Position-only update, preserving current scale (${currentScaleX})`);
            }
            
            // Update the coordinates to ensure the object is correctly positioned and selectable
            fabricObject.setCoords();
            canvas.renderAll();
            
            // Dispatch an object modified event to update any UI that's monitoring this object
            canvas.fire('object:modified', { target: fabricObject });
            
            log(`Updated transform for layer ${layerId}:`, transform);
        } else {
            log(`Layer ${layerId} not found on canvas for transform update.`);
        }
    });
    
    // Listener for selecting a feature on the canvas (triggered by LayerPanel click)
    Livewire.on('select-feature-on-canvas', (data) => {
        log('Select feature on canvas event:', data);
        const featureId = data.featureId || (data[0] ? data[0].featureId : null);
        
        if (!featureId) {
            console.error('Missing featureId in select-feature-on-canvas event');
            return;
        }
        
        const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === featureId);
        if (objects.length > 0) {
            canvas.setActiveObject(objects[0]);
            
            // Manually dispatch dimension info when selecting from layer panel
            dispatchDimensionInfo(objects[0]);
            
            canvas.renderAll();
            log(`Selected feature ${featureId} on canvas.`);
        } else {
            log(`Feature ${featureId} not found on canvas for selection. Adding to pending selection requests.`);
            // Store this as a pending selection request
            pendingSelectionRequests.add(featureId);
        }
    });
    
    // Listener for reordering layers
    Livewire.on('layers-reordered', (data) => {
        log('Layers reordered event:', data);
        
        // Support multiple data formats
        let orderedLayers = null;
        
        // Try to get layers data from the event
        if (data.layers) {
            orderedLayers = data.layers;
        } else if (data[0] && data[0].layers) {
            orderedLayers = data[0].layers;
        }
        
        // If no layers data was found, try to reconstruct from selectedFeatures
        if (!orderedLayers) {
            log('No layer data found in layers-reordered event. Using existing canvas objects.');
            
            // Keep track of the active object to reselect it later
            const activeObject = canvas.getActiveObject();
            let activeObjectId = null;
            if (activeObject && activeObject.data && activeObject.data.featureId) {
                activeObjectId = activeObject.data.featureId;
                log(`Currently active object ID: ${activeObjectId}`);
            }
            
            // Get the ordering from newOrder if available
            const newOrder = data.newOrder || (data[0] && data[0].newOrder);
            
            if (!newOrder) {
                console.error('Missing layers data or newOrder in layers-reordered event');
            return;
        }
        
            // Get all feature objects on the canvas
            const featureObjects = canvas.getObjects().filter(obj => 
                obj.data && obj.data.featureId
            );
            
            // Remove all feature objects from canvas (excluding grid lines)
            featureObjects.forEach(obj => {
                canvas.remove(obj);
            });
            
            // Create a map of existing objects by feature ID
            const existingObjectsMap = {};
            featureObjects.forEach(obj => {
                if (obj.data && obj.data.featureId) {
                    existingObjectsMap[obj.data.featureId] = obj;
                }
            });
            
            // Add objects back in the new order
            const reversedOrder = [...newOrder].reverse();
            log('Adding features in canvas stacking order:', reversedOrder);
            
            // Add each object back to the canvas in the new order
            reversedOrder.forEach(featureId => {
                if (existingObjectsMap[featureId]) {
                    canvas.add(existingObjectsMap[featureId]);
                    log(`Re-added feature ID ${featureId} to canvas in new order`);
                }
            });
            
            // Reselect the active object if it existed
            if (activeObjectId && existingObjectsMap[activeObjectId]) {
                canvas.setActiveObject(existingObjectsMap[activeObjectId]);
                log(`Reselected feature ID ${activeObjectId}`);
            }
            
            // Ensure grid lines are behind everything
            gridLines.forEach(line => {
                canvas.sendObjectToBack(line);
            });
            
            canvas.renderAll();
            log('Layer reordering complete (using newOrder).');
            return;
        }
        
        log('Handling layer reordering with full layer data...');
        
        // Keep track of the active object to reselect it later
        const activeObject = canvas.getActiveObject();
        let activeObjectId = null;
        if (activeObject && activeObject.data && activeObject.data.featureId) {
            activeObjectId = activeObject.data.featureId;
            log(`Currently active object ID: ${activeObjectId}`);
        }
        
        // Get a map of existing objects on the canvas by feature ID
        const existingObjectsMap = {};
        canvas.getObjects().forEach(obj => {
            if (obj.data && obj.data.featureId) {
                existingObjectsMap[obj.data.featureId] = obj;
            }
        });
        
        // Remove all feature objects from canvas (excluding grid lines)
        canvas.getObjects().forEach(obj => {
            if (obj.data && obj.data.featureId) {
                canvas.remove(obj);
            }
        });
        
        // IMPORTANT: In Fabric.js, the layers are stacked with first items behind, last items on top
        // The layer panel sends layers in top-to-bottom order visual order
        // To match Fabric.js stacking order, we need to reverse the layers
        const orderedLayersForCanvas = [...orderedLayers].reverse();
        
        log('Adding features in canvas stacking order:');
        log('Canvas order (bottom to top):', orderedLayersForCanvas.map(l => `${l.name} (${l.id})`));
        
        // Process each feature in the order (bottom to top for Fabric)
        for (let i = 0; i < orderedLayersForCanvas.length; i++) {
            const layer = orderedLayersForCanvas[i];
            const featureId = layer.id;
            log(`Processing feature at Fabric index ${i}: ${featureId} (${layer.name})`);
            
            if (existingObjectsMap[featureId]) {
                canvas.add(existingObjectsMap[featureId]);
                log(`Re-added feature ID ${featureId} to canvas in new order (position ${i})`);
            }
        }
        
        // Reselect the active object if it existed
        if (activeObjectId && existingObjectsMap[activeObjectId]) {
            canvas.setActiveObject(existingObjectsMap[activeObjectId]);
            log(`Reselected feature ID ${activeObjectId}`);
        }
        
        // Ensure grid lines are behind everything
        gridLines.forEach(line => {
            canvas.sendObjectToBack(line);
        });
        
        canvas.renderAll();
        log('Layer reordering complete.');
    });
    
    // Tool handlers
    // Note: move-tool is now handled by Livewire wire:click="toggleMoveMode"
    
    document.getElementById('delete-selected')?.addEventListener('click', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            canvas.remove(activeObject);
            canvas.renderAll();
            
            if (activeObject.data && activeObject.data.featureId) {
                component.call('removeFeature', activeObject.data.featureId);
            }
        }
    });
    
    document.getElementById('clear-canvas')?.addEventListener('click', function() {
        // Remove all objects except grid lines
        const objects = canvas.getObjects().filter(obj => !gridLines.includes(obj));
        objects.forEach(obj => canvas.remove(obj));
        
        component.call('clearFeatures');
        canvas.renderAll();
    });
    
    // Zoom controls
    // Add debounce functionality to prevent rapid clicks from causing issues
    let zoomDebounceTimeout;
    const zoomDebounceTime = 200; // milliseconds
    
    document.getElementById('zoom-in')?.addEventListener('click', function() {
        if (zoomDebounceTimeout) {
            clearTimeout(zoomDebounceTimeout);
        }
        
        zoomDebounceTimeout = setTimeout(() => {
            const canvasContainer = document.querySelector('#canvas-viewport > div[data-scale]'); // Updated selector
            if (!canvasContainer) {
                console.error('Zoom-in: Canvas container not found with selector #canvas-viewport > div[data-scale]');
                return;
            }
            const currentScale = parseFloat(canvasContainer.getAttribute('data-scale') || '1');
            const newScale = Math.min(currentScale * 1.1, 3); // Limit max zoom to 3x
            
            applyCanvasZoom(newScale);
            component.call('zoomIn', newScale);
            
            zoomDebounceTimeout = null;
        }, zoomDebounceTime);
    });
    
    document.getElementById('zoom-out')?.addEventListener('click', function() {
        if (zoomDebounceTimeout) {
            clearTimeout(zoomDebounceTimeout);
        }
        
        zoomDebounceTimeout = setTimeout(() => {
            const canvasContainer = document.querySelector('#canvas-viewport > div[data-scale]'); // Updated selector
            if (!canvasContainer) {
                console.error('Zoom-out: Canvas container not found with selector #canvas-viewport > div[data-scale]');
                return;
            }
            const currentScale = parseFloat(canvasContainer.getAttribute('data-scale') || '1');
            const newScale = Math.max(currentScale * 0.9, 0.5); // Limit min zoom to 0.5x
            
            applyCanvasZoom(newScale);
            component.call('zoomOut', newScale);
            
            zoomDebounceTimeout = null;
        }, zoomDebounceTime);
    });
    
    document.getElementById('reset-zoom')?.addEventListener('click', function() {
        if (zoomDebounceTimeout) {
            clearTimeout(zoomDebounceTimeout);
        }
        
        zoomDebounceTimeout = setTimeout(() => {
            applyCanvasZoom(1);
            component.call('resetZoom');
            
            zoomDebounceTimeout = null;
        }, zoomDebounceTime);
    });
    
    // Function to apply zoom to the canvas container
    function applyCanvasZoom(scale) {
        const canvasContainer = document.querySelector('#canvas-viewport > div[data-scale]'); // Updated selector
        if (!canvasContainer) {
            console.error('applyCanvasZoom: Canvas container not found with selector #canvas-viewport > div[data-scale]');
            return;
        }
        
        // Store the scale for future reference
        canvasContainer.setAttribute('data-scale', scale);
        
        // Apply the scale transform
        canvasContainer.style.transform = `scale(${scale})`;
        
        // Adjust the container's parent padding to accommodate the scaled size
        const parentContainer = canvasContainer.parentElement;
        if (parentContainer) {
            const originalWidth = 600;
            const originalHeight = 600;
            const scaledWidth = originalWidth * scale;
            const scaledHeight = originalHeight * scale;
            
            // Calculate padding to center the scaled canvas
            const horizPadding = Math.max(0, (scaledWidth - originalWidth) / 2);
            const vertPadding = Math.max(0, (scaledHeight - originalHeight) / 2);
            
            parentContainer.style.padding = `${vertPadding + 24}px ${horizPadding + 24}px`;
        }
        
        // Update zoom indicator if it exists
        const zoomIndicator = document.getElementById('zoom-level');
        if (zoomIndicator) {
            zoomIndicator.textContent = `${Math.round(scale * 100)}%`;
        }
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Delete' || e.key === 'Backspace') {
            const activeObject = canvas.getActiveObject();
            if (activeObject && !gridLines.includes(activeObject)) {
                canvas.remove(activeObject);
                canvas.renderAll();
                
                if (activeObject.data && activeObject.data.featureId) {
                    component.call('removeFeature', activeObject.data.featureId);
                }
            }
        }
        
        // Add keyboard shortcuts for zoom
        if (e.ctrlKey || e.metaKey) {
            if (e.key === '=' || e.key === '+') {
                e.preventDefault();
                // Directly trigger the button's event handler to use debounce
                const zoomInButton = document.getElementById('zoom-in');
                if (zoomInButton) {
                    const clickEvent = new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    zoomInButton.dispatchEvent(clickEvent);
                }
            } else if (e.key === '-') {
                e.preventDefault();
                // Directly trigger the button's event handler to use debounce
                const zoomOutButton = document.getElementById('zoom-out');
                if (zoomOutButton) {
                    const clickEvent = new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    zoomOutButton.dispatchEvent(clickEvent);
                }
            } else if (e.key === '0') {
                e.preventDefault();
                // Directly trigger the button's event handler to use debounce
                const resetZoomButton = document.getElementById('reset-zoom');
                if (resetZoomButton) {
                    const clickEvent = new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        view: window
                    });
                    resetZoomButton.dispatchEvent(clickEvent);
                }
            }
        }
    });
    
    // Listen for zoom level changes from Livewire
    Livewire.on('zoom-level-changed', function(data) {
        const zoomLevel = data.zoomLevel || (Array.isArray(data) && data.length > 0 ? data[0].zoomLevel : 100);
        const scale = zoomLevel / 100;
        applyCanvasZoom(scale);
    });
    
    // Handle toggle-move-mode event
    window.addEventListener('toggle-move-mode', function(e) {
        const enabled = e.detail && typeof e.detail.enabled !== 'undefined' 
            ? e.detail.enabled 
            : (e.detail && e.detail[0] && typeof e.detail[0].enabled !== 'undefined' 
                ? e.detail[0].enabled 
                : null);
        
        log('Toggle move mode event received:', enabled);
        
        // If enabled is null, we couldn't extract the value from the event
        if (enabled === null) {
            console.error('Could not determine toggle state from event:', e);
            return;
        }
        
        updateCanvasObjectsMoveState(enabled);
    });
    
    // Also listen via Livewire event system
    Livewire.on('toggle-move-mode', function(data) {
        const enabled = data && typeof data.enabled !== 'undefined' 
            ? data.enabled 
            : (Array.isArray(data) && data.length > 0 && typeof data[0].enabled !== 'undefined' 
                ? data[0].enabled 
                : null);
        
        log('Livewire toggle-move-mode event received:', enabled);
        
        // If enabled is null, we couldn't extract the value from the event
        if (enabled === null) {
            console.error('Could not determine toggle state from Livewire event:', data);
            return;
        }
        
        updateCanvasObjectsMoveState(enabled);
    });
    
    // Function to update canvas objects based on move state
    function updateCanvasObjectsMoveState(enabled) {
        // Set all objects to be selectable or not based on the move mode
        canvas.getObjects().forEach(function(obj) {
            // Don't change the grid lines
            if (gridLines.includes(obj)) return;
            
            // If the object is locked specifically, don't change its state
            if (obj.data && obj.data.locked) return;
            
            obj.selectable = enabled;
            obj.evented = enabled;
            obj.hasControls = enabled;
            obj.hasBorders = enabled;
            obj.lockMovementX = !enabled;
            obj.lockMovementY = !enabled;
            obj.lockRotation = !enabled;
            obj.lockScalingX = !enabled;
            obj.lockScalingY = !enabled;
        });
        
        // If disabling move mode, deselect all objects
        if (!enabled) {
            canvas.discardActiveObject();
        }
        
        canvas.renderAll();
    }

    // Handle direct position updates from the layer panel
    document.addEventListener('livewire:direct-update-object-position', function(event) {
        log('Direct position update event:', event.detail);
        console.log('FABRIC POSITION: direct-update-object-position event received', event.detail);
        
        const { featureId, x, y } = event.detail;
        
        // Find the object on the canvas
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        console.log('FABRIC POSITION: Found objects matching featureId', {
            featureId,
            objectsCount: objects.length,
            objects
        });
        
        if (objects.length > 0) {
            const fabricObject = objects[0];
            
            console.log('FABRIC POSITION: Updating object position', {
                featureId,
                from: { x: fabricObject.left, y: fabricObject.top },
                to: { x, y }
            });
            
            // Update position
            fabricObject.set({
                left: x,
                top: y
            });
            
            // If this is the active object, update controls
            if (canvas.getActiveObject() === fabricObject) {
                canvas.setActiveObject(fabricObject);
            }
            
            canvas.renderAll();
            log(`Updated position for object ${featureId} to x:${x}, y:${y}`);
            
            // Log position for debugging
            console.log('FABRIC POSITION: Position updated from panel:', { x, y });
        } else {
            log(`Object with ID ${featureId} not found on canvas for position update`);
            console.log('FABRIC POSITION: Object not found for position update', { featureId });
        }
    });
}

function addFeatureToCanvas(feature) {
    // Generate a unique ID for this feature add operation to prevent duplicate processing
    const operationId = `add-feature-${feature.id}-${Date.now()}`;
    if (processedEvents.has(operationId)) {
        log(`Skipping duplicate add request for feature ${feature.id}`);
        return;
    }
    processedEvents.add(operationId);

    // Cleanup old events (keep last 20)
    if (processedEvents.size > 20) {
        const toRemove = Array.from(processedEvents).slice(0, processedEvents.size - 20);
        toRemove.forEach(id => processedEvents.delete(id));
    }

    log('Attempting to add feature to canvas:', feature);

    // Check for adjustments
    if (feature.adjustments && Object.keys(feature.adjustments).length > 0) {
        console.log('Feature has adjustments that will be applied after loading:', feature.adjustments);
    }

    // --- Pre-checks ---
    if (!feature.image_path) {
        console.error('Feature image_path is missing or invalid:', feature);
        processedEvents.delete(operationId); // Clean up since we failed
        return;
    }

    if (!canvas) {
        console.error('Canvas not initialized, cannot add feature.');
        processedEvents.delete(operationId);
        return;
    }

    // Check if this feature already exists on the canvas
    const existingObjects = canvas.getObjects().filter(obj =>
        obj.data && obj.data.featureId === feature.id
    );
    if (existingObjects.length > 0) {
        log('Feature with ID', feature.id, 'already exists on canvas. Selecting it...');
        canvas.setActiveObject(existingObjects[0]);
        canvas.renderAll();
        
        // Even if the feature exists, apply any adjustments it may have
        if (feature.adjustments && Object.keys(feature.adjustments).length > 0) {
            console.log('Applying adjustments to existing feature:', feature.adjustments);
            if (window.imageAdjustments) {
                window.imageAdjustments.handleAdjustmentUpdate({
                    layerId: feature.id,
                    adjustments: feature.adjustments
                });
            }
        }
        
        processedEvents.delete(operationId); // Clean up since we didn't add
        return;
    }

    // Check for existing features of the same type and remove them
    if (feature.feature_type) {
        const objectsOfSameType = canvas.getObjects().filter(obj =>
            obj.data && obj.data.featureType === feature.feature_type
        );
        if (objectsOfSameType.length > 0) {
            log(`Found ${objectsOfSameType.length} existing feature(s) of type ${feature.feature_type}. Removing them.`);
            objectsOfSameType.forEach(obj => {
                const removedFeatureId = obj.data?.featureId;
                canvas.remove(obj);
                // Notify Livewire component about the removal
                if (removedFeatureId && typeof Livewire !== 'undefined') {
                    const component = Livewire.find(document.getElementById('main-canvas-component')?.getAttribute('wire:id'));
                if (component) {
                        component.call('removeFeature', removedFeatureId);
            }
        }
    });
        }
    }

    // Check if already loading
    if (loadingFeatures.has(feature.id)) {
        log(`Feature with ID ${feature.id} is already being loaded. Skipping...`);
        processedEvents.delete(operationId); // Clean up duplicate load attempt
        return;
    }
    loadingFeatures.add(feature.id); // Add to loading set *before* async operations
    log(`Added feature ID ${feature.id} to loading set. Current loading:`, Array.from(loadingFeatures));

    // --- Determine Move State ---
    let moveEnabled = true; // Default
    if (typeof Livewire !== 'undefined') {
        const component = Livewire.find(document.getElementById('main-canvas-component')?.getAttribute('wire:id'));
        if (component) {
            try {
                moveEnabled = component.get('moveEnabled');
            } catch (e) {
                log('Could not get moveEnabled state from component, using default.');
            }
        }
    }

    // --- Optimization: Try using pre-loaded image from Feature Library ---
    let preloadedImageElement = null;
    try {
        // Query for the specific image within the library panel based on wire:key
        // Ensure we select the actual image, not a container
        preloadedImageElement = document.querySelector(`div[wire\:key="feature-${feature.id}"] img.feature-library-image`);

        if (preloadedImageElement) {
            // Check if the image is actually loaded and has dimensions
            if (preloadedImageElement.complete && preloadedImageElement.naturalHeight !== 0) {
                log(`Using preloaded image element for feature ${feature.id}`);
                // Proceed synchronously using the found element
                setupFabricImageFromElement(preloadedImageElement, feature, moveEnabled);
                // We successfully added it synchronously, remove the processed event ID
                processedEvents.delete(operationId);
                return; // Exit function, we're done
        } else {
                log(`Found preloaded image for feature ${feature.id}, but it's not fully loaded/valid. Falling back.`);
                preloadedImageElement = null; // Reset to null, proceed to fallback
            }
        } else {
             log(`Preloaded image element not found for feature ${feature.id}. Falling back.`);
        }
    } catch (e) {
        log(`Error finding preloaded image for feature ${feature.id}:`, e);
        preloadedImageElement = null; // Ensure fallback on error
    }

    // --- Fallback: Load image asynchronously ---
    log(`Falling back to async loading for feature ${feature.id}`);
    let imagePath = `/storage/${feature.image_path}`;
    // Basic path correction (adjust if needed)
    if (feature.image_path.startsWith('/storage/')) {
        imagePath = feature.image_path;
    } else if (feature.image_path.startsWith('storage/')) {
        imagePath = `/${feature.image_path}`;
    }

    // Store the adjustments in the feature.data property so they're available after loading
    if (!feature.data) feature.data = {};
    if (feature.adjustments) {
        feature.data.pendingAdjustments = feature.adjustments;
    }

    // Call the async loading function
    loadImageAndSetupFabricImage(feature, moveEnabled, imagePath, operationId);
}

// Add this as a helper function to dispatch dimension info for newly added objects
function dispatchDimensionInfo(fabricObject) {
    if (!fabricObject || !fabricObject.data || !fabricObject.data.featureId) return;
    
    // Extract feature ID and dimensions
    const featureId = fabricObject.data.featureId;
    const originalWidth = fabricObject.width || 0;
    const originalHeight = fabricObject.height || 0;
    const scaleX = fabricObject.scaleX || 1;
    const scaleY = fabricObject.scaleY || 1;
    
    // Calculate the displayed dimensions (original × scale)
    const calculatedWidth = originalWidth * scaleX;
    const calculatedHeight = originalHeight * scaleY;
    
    // Only log in debug mode
    if (DEBUG_LOGS) {
        console.log('DIMENSION DEBUG: Initial object dimensions for new feature:', {
            featureId,
            originalWidth,
            originalHeight,
            scaleX,
            scaleY,
            calculatedWidth,
            calculatedHeight
        });
    }
    
    // Create event detail object with all necessary properties
    const eventDetail = {
        id: fabricObject.data.featureId,
        left: fabricObject.left,
        top: fabricObject.top,
        angle: fabricObject.angle,
        scaleX: fabricObject.scaleX,
        scaleY: fabricObject.scaleY,
        width: fabricObject.width,
        height: fabricObject.height
    };
    
    // Dispatch DOM event to update the layer panel
    document.dispatchEvent(new CustomEvent('fabricjs:object-selected', {
        detail: eventDetail
    }));
    
    // Also notify Livewire components - but just dispatch the event
    // Don't try to call any specific methods on the component
    if (typeof Livewire !== 'undefined') {
        try {
            Livewire.dispatch('fabricjs:object-selected', eventDetail);
            if (DEBUG_LOGS) {
                console.log('FABRIC POSITION: Dispatched initial dimensions for new feature');
            }
        } catch (error) {
            console.error('Error dispatching dimensions to Livewire:', error);
        }
    }
}

/**
 * Sets up a Fabric.js image object using a provided HTMLImageElement.
 * This is the core logic shared by both pre-loaded and async paths.
 */
function setupFabricImageFromElement(imgElement, feature, moveEnabled) {
    try {
        log(`Setting up Fabric image for feature ${feature.id} from element.`);

        // Handle opacity - ensure it's never 0 by default
        let featureOpacity = 1; // Default to 100% opacity
        if (feature.opacity !== undefined) {
            // If opacity is in 0-100 range (UI scale)
            if (feature.opacity > 1) {
                featureOpacity = feature.opacity / 100;
            } 
            // If opacity is in 0-1 range (Fabric.js scale)
            else {
                featureOpacity = feature.opacity;
            }
            
            // Ensure minimum opacity of 1% to avoid invisible features
            if (featureOpacity <= 0) {
                console.warn(`Feature ${feature.id} had 0 opacity, defaulting to 100%`);
                featureOpacity = 1; // Default to 100% if it was 0
            }
        }

        // Get position and scale from feature data
        const positionX = feature.position?.x || getDefaultPositionForFeature(feature.feature_type).x;
        const positionY = feature.position?.y || getDefaultPositionForFeature(feature.feature_type).y;
        const rotation = feature.position?.rotation || 0;
        const scale = feature.position?.scale || 1.0;

        // Log position and scale data for debugging
        console.log(`Feature ${feature.id} position data:`, {
            x: positionX,
            y: positionY,
            rotation: rotation,
            scale: scale
        });

        const fabricImage = new fabric.Image(imgElement, {
            left: positionX,
            top: positionY,
            angle: rotation,
            originX: 'center', // Center origin for easier positioning/rotation
            originY: 'center',
            hasControls: feature.locked ? false : moveEnabled,
            hasBorders: feature.locked ? false : moveEnabled,
            selectable: feature.locked ? false : moveEnabled,
            evented: feature.locked ? false : moveEnabled,
            lockMovementX: feature.locked ? true : !moveEnabled,
            lockMovementY: feature.locked ? true : !moveEnabled,
            lockRotation: feature.locked ? true : !moveEnabled,
            lockScalingX: feature.locked ? true : !moveEnabled,
            lockScalingY: feature.locked ? true : !moveEnabled,
            lockSkewingX: true, // Disable skewing
            lockSkewingY: true,
            visible: feature.visible !== undefined ? feature.visible : true,
            opacity: featureOpacity, // Use our sanitized opacity value
            cornerColor: '#3498DB',
            cornerSize: 10,
            transparentCorners: false,
            borderColor: '#3498DB',
            borderScaleFactor: 1.5, // Slightly thicker border
            data: {
                featureId: feature.id,
                featureType: feature.feature_type,
                locked: feature.locked || false
            }
        });

        // Apply the position scale explicitly
        if (scale && scale !== 1) {
            log(`Applying explicit scale from position data: ${scale}`);
            fabricImage.scaleX = scale;
            fabricImage.scaleY = scale;
            
            // Store this scale in the feature's position object
            if (feature.position) {
                feature.position.scale = scale;
            }
        } else {
            // Scale the image initially based on feature type if no explicit scale is provided
            let maxWidth = 250;  // Default max width
            let maxHeight = 250; // Default max height
            
            // Get feature type ID and adjust sizing based on type
            const featureTypeId = feature.feature_type;
            if (featureTypeId) {
                // Adjust sizing based on feature type ID
                // These values control the initial size of different feature types
                switch (parseInt(featureTypeId)) {
                    case 1: // eyes
                        maxWidth = 180;
                        maxHeight = 70;
                        break;
                    case 2: // eyebrows
                        maxWidth = 180;
                        maxHeight = 50;
                        break;
                    case 3: // nose
                        maxWidth = 100;
                        maxHeight = 120;
                        break;
                    case 4: // mouth
                        maxWidth = 120;
                        maxHeight = 70;
                        break;
                    case 5: // ears - Set specific width
                        const targetWidth = 370;
                        if (fabricImage.width && fabricImage.width > 0) { // Check if width is valid
                            const scaleFactor = targetWidth / fabricImage.width;
                            fabricImage.scale(scaleFactor);
                            log(`Applied specific width scaling (${scaleFactor.toFixed(2)}) for ears (feature ${feature.id}) to target width ${targetWidth}px`);
                            // Store this scale factor in the feature data if needed
                            if (feature.position) {
                                feature.position.scale = scaleFactor;
                            }
                        } else {
                            log(`Skipping scaling for ears (feature ${feature.id}) due to invalid initial width: ${fabricImage.width}`);
                        }
                        // Skip the general scaling logic below for ears
                        // We set a flag or modify maxWidth/maxHeight to prevent double scaling
                        maxWidth = 0; // Set to 0 to ensure the general scaling below doesn't apply
                        break;
                    case 6: // hair
                        maxWidth = 350;
                        maxHeight = 350;
                        break;
                    case 7: // face
                        maxWidth = 400;
                        maxHeight = 450;
                        break;
                    case 8: // neck
                        maxWidth = 150;
                        maxHeight = 200;
                        break;
                    case 9: // accessories
                        maxWidth = 200;
                        maxHeight = 200;
                        break;
                    default:
                        // Use defaults for any other feature types
                        break;
                }
            }
            
            // Apply general scaling ONLY if not handled by a specific case (like ears)
            if (maxWidth > 0 && (fabricImage.width * fabricImage.scaleX > maxWidth || fabricImage.height * fabricImage.scaleY > maxHeight)) {
                // Use current scaled width/height for calculation if already scaled by a specific case (but ears case prevents this)
                const currentWidth = fabricImage.width * fabricImage.scaleX;
                const currentHeight = fabricImage.height * fabricImage.scaleY;
                
                const scaleFactor = Math.min(
                    maxWidth / currentWidth, 
                    maxHeight / currentHeight
                );
                
                // Apply scaling relative to current scale
                fabricImage.scaleX *= scaleFactor;
                fabricImage.scaleY *= scaleFactor;
                
                log(`Applied general scaling of ${scaleFactor.toFixed(2)} to feature ${feature.id} (type: ${featureTypeId})`);
                
                // Store this as the feature's initial scale in the position object if it exists
                if (feature.position) {
                    feature.position.scale = fabricImage.scaleX; // Assuming uniform scaling
                }
            }
        }

        canvas.add(fabricImage);

        // Ensure grid lines are behind
        gridLines.forEach(line => canvas.sendObjectToBack(line));

        // Select the newly added feature
        canvas.setActiveObject(fabricImage);
        
        // Log dimensions after scaling for debugging
        console.log(`Feature ${feature.id} final dimensions:`, {
            width: Math.round(fabricImage.width * fabricImage.scaleX),
            height: Math.round(fabricImage.height * fabricImage.scaleY), 
            scaleX: fabricImage.scaleX,
            scaleY: fabricImage.scaleY
        });
        
        // Manually dispatch dimension info to update the panel immediately
        dispatchDimensionInfo(fabricImage);
        
        // Apply adjustments immediately after adding to canvas if they exist
        if ((feature.adjustments && Object.keys(feature.adjustments).length > 0) ||
            (feature.data && feature.data.pendingAdjustments)) {
            
            // Use the adjustments directly from feature or from pendingAdjustments
            const adjustmentsToApply = feature.adjustments || 
                (feature.data ? feature.data.pendingAdjustments : null);
            
            if (adjustmentsToApply) {
                console.log(`Applying stored adjustments to newly added feature ${feature.id}:`, adjustmentsToApply);
                
                // Apply adjustments via the ImageAdjustments class if available
                if (window.imageAdjustments) {
                    // Short timeout to ensure the feature is fully initialized
                    setTimeout(() => {
                        window.imageAdjustments.handleAdjustmentUpdate({
                            layerId: feature.id,
                            adjustments: adjustmentsToApply
                        });
                    }, 100);
                }
            }
        }
        
        canvas.renderAll();

        log(`✅ Feature ${feature.id} added and canvas rendered successfully.`);

        // Remove from loading set *after* successful addition
        loadingFeatures.delete(feature.id);
        
        // If all features are loaded and this is initial load, update loading state
        if (initialLoadingComplete === false && loadingFeatures.size === 0) {
            log(`All initial features loaded, showing canvas`);
            updateCanvasLoadingState(false);
        }

        // --- Add Event Listener for modifications ---
        if (typeof Livewire !== 'undefined') {
            const component = Livewire.find(document.getElementById('main-canvas-component')?.getAttribute('wire:id'));
            if (component) {
                fabricImage.on('modified', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.data?.featureId) {
                        const positionData = {
                            x: Math.round(obj.left),
                            y: Math.round(obj.top),
                            rotation: Math.round(obj.angle),
                            // Use scaleX assuming uniform scaling
                            scale: Math.round(obj.scaleX * 100) / 100
                        };
                        log(`Feature ${obj.data.featureId} modified, updating position:`, positionData);
                        component.call('updateFeaturePosition', {
                            featureId: obj.data.featureId,
                            position: positionData
                        });
                    }
                });
            }
        }

        // --- Process Pending Selection ---
        if (pendingSelectionRequests.has(feature.id)) {
            log(`Processing pending selection request for feature ${feature.id}`);
            // Already selected above with setActiveObject, just remove from set
            pendingSelectionRequests.delete(feature.id);
        }

    } catch (error) {
        console.error(`Error setting up Fabric image for feature ${feature.id}:`, error);
        // Ensure removal from loading set on error during setup
        loadingFeatures.delete(feature.id);
    }
}

/**
 * Loads an image asynchronously and then calls setupFabricImageFromElement.
 */
function loadImageAndSetupFabricImage(feature, moveEnabled, imagePath, operationId) {
    log(`Loading image async for feature ${feature.id} from path: ${imagePath}`);
    
    // Add timeout to prevent indefinite loading
    const loadingTimeout = setTimeout(() => {
        console.error(`Loading timeout for feature ${feature.id}`);
        loadingFeatures.delete(feature.id);
        processedEvents.delete(operationId);
    }, 10000); // 10 second timeout

    const imgElement = new Image();
    imgElement.crossOrigin = 'Anonymous'; // Important for canvas tainting

    // Define error handling
    imgElement.onerror = function(error) {
        clearTimeout(loadingTimeout);
        console.error(`❌ Error loading image for feature ${feature.id}:`, error);
        console.error('Image path that failed:', imagePath);

        // Simple error handling: just log and remove from loading
        loadingFeatures.delete(feature.id);
        processedEvents.delete(operationId); // Clean up the processed event tracker
    };

    // Define success handling
    imgElement.onload = function() {
        clearTimeout(loadingTimeout);
        log(`Image loaded successfully via async for feature ${feature.id}`);
        // Now call the common setup function using the loaded element
        setupFabricImageFromElement(imgElement, feature, moveEnabled);
        // Clean up the processed event tracker after successful async load and setup
        processedEvents.delete(operationId);
    };

    // Set the src to start loading
    imgElement.src = imagePath;
}

/**
 * Helper function to calculate anatomically correct default positions for facial features.
 * Uses the center point (300,300) and a standard face shape size (324×450).
 * @param {number} featureTypeId - The type of facial feature.
 * @return {Object} - The x,y coordinates.
 */
function getDefaultPositionForFeature(featureTypeId) {
    // Canvas center point
    const centerX = 300;
    const centerY = 300;
    
    // Average face dimensions from the UI
    const faceWidth = 324;
    const faceHeight = 450;
    
    // Default to center if not a recognized feature
    const defaultPosition = { x: centerX, y: centerY };
    
    // Return anatomically positioned coordinates based on feature type
    if (!featureTypeId) return defaultPosition;
    
    switch (parseInt(featureTypeId)) {
        case 1: // eyes - upper third of face, horizontally spaced
            return { 
                x: centerX, 
                y: centerY - faceHeight * 0.15 // 15% up from center
            };
            
        case 2: // eyebrows - above eyes
            return { 
                x: centerX, 
                y: centerY - faceHeight * 0.22 // 22% up from center
            };
            
        case 3: // nose - middle of face
            return { 
                x: centerX, 
                y: centerY + faceHeight * 0.05 // 5% down from center
            };
            
        case 4: // mouth - lower third of face
            return { 
                x: centerX, 
                y: centerY + faceHeight * 0.18 // 18% down from center
            };
            
        case 5: // ears - sides of face
            return { 
                x: centerX + (faceWidth * 0.48), // Right side of face
                y: centerY
            };
            
        case 6: // hair - slightly above top of face
            return { 
                x: centerX, 
                y: centerY - faceHeight * 0.28 // 28% up from center
            };
            
        case 7: // face - dead center
            return defaultPosition;
            
        case 8: // neck - below face
            return { 
                x: centerX, 
                y: centerY + faceHeight * 0.45 // 45% down from center
            };
            
        case 9: // accessories - depends on accessory type, center by default
            return defaultPosition;
            
        default:
            return defaultPosition;
    }
}

// Helper function to get the Livewire component instance
function getLivewireComponent() {
    if (typeof Livewire === 'undefined') {
        console.error('Livewire is not defined');
        return null;
    }
    
    const componentElement = document.getElementById('main-canvas-component');
    if (!componentElement) {
        console.error('Main canvas component element not found');
        return null;
    }
    
    const wireId = componentElement.getAttribute('wire:id');
    if (!wireId) {
        console.error('Main canvas component has no wire:id attribute');
        return null;
    }
    
    try {
        return Livewire.find(wireId);
    } catch (e) {
        console.error('Error finding Livewire component:', e);
        return null;
    }
}

// Add a force reload function that can be called manually in console if needed
window.forceReloadCanvasFeatures = function() {
    console.log('Force reload canvas features called manually');
    
    // Get the Livewire component
    const component = getLivewireComponent();
    if (!component) {
        console.error('Could not find Livewire component for force reload');
        return;
    }
    
    // Request features from Livewire
    component.call('getSelectedFeatures').then(features => {
        if (features && Array.isArray(features)) {
            console.log('Retrieved features from Livewire component:', features);
            
            // Dispatch a force reload event
            Livewire.dispatch('force-reload-canvas', {
                features: features
            });
        } else {
            console.error('No features returned from Livewire or invalid format');
        }
    }).catch(error => {
        console.error('Error getting features from Livewire:', error);
    });
};

// Function to load existing features from Livewire component
function loadExistingFeatures() {
    // Show loading state before attempting to load features
    updateCanvasLoadingState(true);
    
    // Get the Livewire component
    const livewireComponent = getLivewireComponent();
    if (livewireComponent) {
        // If there are features in Livewire, request them
        livewireComponent.call('getSelectedFeatures').then(features => {
            if (features && features.length > 0) {
                console.log('Loading initial features from Livewire component:', features);
                
                // The features are already in correct canvas order from the server
                // Canvas draws first items at the bottom, matching the z_index ascending order
                // No need to reverse the order
                
                // Log the feature order for debugging
                log('Feature order for canvas loading:', 
                    features.map(f => `${f.name} (ID: ${f.id}, Type: ${f.feature_type}, z-index: ${f.z_index || 0})`)
                );
                
                // Clear canvas before adding features
                canvas.clear();
                setupGrid();
                
                // Keep track of loading features
                const featuresToLoad = features.length;
                let featuresLoaded = 0;
                
                // Create a promise that resolves when all features are loaded
                const allFeaturesPromise = new Promise((resolve) => {
                    // Monitor the loadingFeatures set for changes
                    const checkInterval = setInterval(() => {
                        if (loadingFeatures.size === 0 && featuresLoaded === featuresToLoad) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 100);
                    
                    // Safety timeout in case some features fail to load
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        resolve();
                    }, 5000); // 5 second max wait
                });
                
                // Force update with current features
                updateCanvasFeatures({
                    selectedFeatures: features,
                    forceUpdate: true
                });
                
                // When all features are loaded, hide loading overlay
                allFeaturesPromise.then(() => {
                    initialLoadingComplete = true; // Mark that structural loading is done

                    const adjustmentGracePeriod = 750; // Milliseconds. Increased slightly for safety.
                    log(`All features structurally loaded. Waiting ${adjustmentGracePeriod}ms for adjustments to complete...`);

                    setTimeout(() => {
                        log('Adjustment grace period ended. Finalizing canvas display.');
                        updateCanvasLoadingState(false); // Now hide the loading overlay
                        diagnosticCheckCanvasState();    // Run diagnostics
                    }, adjustmentGracePeriod);
                });
            } else {
                // No features to load, just hide loading state
                initialLoadingComplete = true;
                updateCanvasLoadingState(false);
            }
        }).catch(err => {
            console.error('Error loading features:', err);
            // Even on error, hide loading state
            initialLoadingComplete = true;
            updateCanvasLoadingState(false);
        });
    } else {
        // No Livewire component, hide loading state
        initialLoadingComplete = true;
        updateCanvasLoadingState(false);
    }
}

// Listen for the canvas-reset event
document.addEventListener('livewire:initialized', () => {
    Livewire.on('canvas-reset', () => {
        console.log('Canvas reset event received');
        // Clear the canvas
        canvas.clear();
        
        // Re-add grid
        setupGrid();
    });
    
    // Enhance the update-canvas event handling
    Livewire.on('update-canvas', (data) => {
        console.log('Update canvas event received:', data);
        updateCanvasFeatures(data);
        
        // Add a diagnostic check after update
        setTimeout(diagnosticCheckCanvasState, 500);
    });
    
    // New event listener for force-reload of canvas features
    Livewire.on('force-reload-canvas', (data) => {
        console.log('Force reload canvas event received');
        if (canvas) {
            // Keep only grid lines
            const nonFeatureObjects = canvas.getObjects().filter(obj => 
                gridLines.includes(obj) || !obj.data || !obj.data.featureId
            );
            
            // Clear the canvas
            canvas.clear();
            
            // Re-add non-feature objects
            nonFeatureObjects.forEach(obj => canvas.add(obj));
            
            // Force update with data
            updateCanvasFeatures({
                selectedFeatures: data.features || [], 
                forceUpdate: true
            });
            
            // Add a diagnostic check after update
            setTimeout(diagnosticCheckCanvasState, 500);
        }
    });
});

function updateCanvasFeatures(data) {
    if (!canvas) {
        console.error('Canvas not initialized yet');
        // Initialize the canvas if it doesn't exist
        initializeCanvas();
        // Delay the update to ensure canvas is ready
        setTimeout(() => updateCanvasFeatures(data), 100);
        return;
    }
    
    const forceUpdate = data.forceUpdate || false;
    
    if (forceUpdate) {
        // Clear the canvas for a full refresh
        canvas.clear();
        
        console.log('Force updating canvas with features:', data.selectedFeatures);
        if (data.selectedFeatures && data.selectedFeatures.length > 0) {
            console.log('Feature order for canvas (first drawn = bottom-most):',
                data.selectedFeatures.map(f => `${f.name} (ID: ${f.id}, Type: ${f.feature_type}, z-index: ${f.z_index || 'unset'})`));
        }
        
        // Create all objects fresh
        if (data.selectedFeatures && data.selectedFeatures.length > 0) {
            for (const feature of data.selectedFeatures) {
                addFeatureToCanvas(feature);
            }
        }
    } else {
        // Fix for handleCanvasUpdate not defined error
        // Process features directly instead of calling a non-existent function
        console.log('Standard update with features:', data.selectedFeatures);
        if (data.selectedFeatures && data.selectedFeatures.length > 0) {
            console.log('Feature order for canvas (first = bottom-most):',
                data.selectedFeatures.map(f => `${f.name} (ID: ${f.id}, Type: ${f.feature_type}, z-index: ${f.z_index || 'unset'})`));
        }
        
        // Get existing objects on canvas
        const existingObjectsMap = {};
        canvas.getObjects().forEach(obj => {
            if (obj.data && obj.data.featureId) {
                existingObjectsMap[obj.data.featureId] = obj;
            }
        });
        
        // Add or update features
        if (data.selectedFeatures && data.selectedFeatures.length > 0) {
            data.selectedFeatures.forEach(feature => {
                const existingObject = existingObjectsMap[feature.id];
                
                if (existingObject) {
                    // Update existing object
                    log(`Updating existing feature ${feature.id}`);
                    
                    // Process opacity to ensure it's in the correct range (0-1)
                    let processedOpacity = 1; // Default to 100%
                    if (feature.opacity !== undefined) {
                        // Convert from percentage (0-100) to decimal (0-1) if needed
                        processedOpacity = feature.opacity > 1 ? 
                            feature.opacity / 100 : feature.opacity;
                        
                        // Ensure minimum opacity of 1% to avoid invisible features
                        if (processedOpacity <= 0) {
                            console.warn(`Feature ${feature.id} had 0 opacity, defaulting to 100%`);
                            processedOpacity = 1;
                        }
                    }
                    
                    existingObject.set({
                        left: feature.position?.x || existingObject.left,
                        top: feature.position?.y || existingObject.top,
                        angle: feature.position?.rotation || existingObject.angle,
                        visible: feature.visible !== undefined ? feature.visible : true,
                        opacity: processedOpacity
                    });
                    
                    // Apply scaling if needed
                    if (feature.position?.scale && feature.position.scale !== 1) {
                        existingObject.scale(feature.position.scale);
                    }
                    
                    existingObject.setCoords();
                } else {
                    // Add new feature
                    log(`Adding new feature ${feature.id}`);
                    addFeatureToCanvas(feature);
                }
            });
        }
    }
    
    // Ensure grid lines are behind
    gridLines.forEach(line => {
        canvas.sendObjectToBack(line);
    });
    
    // Log the current canvas order for debugging
    const featureObjects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId);
    if (featureObjects.length > 0) {
        console.log('Current canvas layer order (bottom to top):',
            featureObjects.map(obj => {
                const featureId = obj.data.featureId;
                const featureType = obj.data.featureType;
                const name = obj.data.name || `Feature ${featureId}`;
                return `${name} (ID: ${featureId}, Type: ${featureType})`;
            })
        );
    }
    
    // Ensure canvas is rendered
    canvas.renderAll();
}

// Fix for addImageToCanvas function being called but not defined
function addImageToCanvas(feature) {
    if (!feature || !feature.image_path) {
        console.error('Invalid feature object or missing image path:', feature);
        return null;
    }
    
    console.log('Add image to canvas called for feature:', feature);
    
    // If the feature doesn't have a position, create one using our default position function
    if (!feature.position) {
        feature.position = {
            x: getDefaultPositionForFeature(feature.feature_type).x,
            y: getDefaultPositionForFeature(feature.feature_type).y,
            rotation: 0,
            scale: 1
        };
        console.log('Created default position for feature:', feature.position);
    }
    
    // Now delegate to our existing addFeatureToCanvas function
    return addFeatureToCanvas(feature);
}

// Diagnostic function to check canvas state
function diagnosticCheckCanvasState() {
    if (!canvas) return;
    
    const objects = canvas.getObjects();
    const featureObjects = objects.filter(obj => obj.data && obj.data.featureId);
    
    console.log('CANVAS DIAGNOSTIC:');
    console.log(`- Total objects on canvas: ${objects.length}`);
    console.log(`- Feature objects: ${featureObjects.length}`);
    console.log(`- Grid lines: ${gridLines.length}`);
    
    if (featureObjects.length > 0) {
        console.log('Feature objects:');
        featureObjects.forEach(obj => {
            console.log(`  - Feature ID: ${obj.data.featureId}, Type: ${obj.data.featureType}, Visible: ${obj.visible}, Position: (${Math.round(obj.left)}, ${Math.round(obj.top)})`);
        });
    } else {
        console.log('WARNING: No feature objects on canvas');
    }
    
    // Check for loading features that haven't completed
    if (loadingFeatures.size > 0) {
        console.log(`WARNING: ${loadingFeatures.size} features still loading:`, Array.from(loadingFeatures));
    }
} 

// Add this new function to show/hide loading overlay
function updateCanvasLoadingState(isLoading) {
    const canvasViewport = document.getElementById('canvas-viewport');
    const canvasContainer = canvasViewport?.querySelector('[data-scale]');
    
    if (!canvasViewport || !canvasContainer) return;
    
    // Find or create the loading overlay
    let loadingOverlay = document.getElementById('canvas-loading-overlay');
    if (!loadingOverlay && isLoading) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'canvas-loading-overlay';
        loadingOverlay.className = 'absolute inset-0 flex flex-col items-center justify-center bg-slate-700 z-10'; // Use flex-col
        
        // Improved Spinner
        const spinner = document.createElement('div');
        spinner.className = 'animate-spin rounded-full h-16 w-16 border-4 border-slate-600 border-t-[#3498DB]'; // Larger, themed colors
        
        // Loading Text
        const loadingText = document.createElement('p');
        loadingText.className = 'text-slate-300 text-md mt-4';
        loadingText.textContent = 'Loading Editor...';
        
        loadingOverlay.appendChild(spinner);
        loadingOverlay.appendChild(loadingText); // Add text to overlay
        canvasViewport.appendChild(loadingOverlay);
    } else if (loadingOverlay && !isLoading) {
        // Remove the loading overlay with a slight delay for smooth transition
        setTimeout(() => {
            if (loadingOverlay.parentNode) {
                loadingOverlay.parentNode.removeChild(loadingOverlay);
            }
        }, 100); // Keep a small delay, or adjust as needed
    }
    
    // Show/hide canvas based on loading state
    canvasContainer.style.visibility = isLoading ? 'hidden' : 'visible';
} 