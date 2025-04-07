import * as fabric from 'fabric';

// Debug Livewire events and connections
console.log('Editor canvas script loaded');

// Add a global flag to control console logging
const DEBUG_LOGS = false;

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
            
            log('Processing features in original order:', incomingFeatures.map(f => `${f.name} (type: ${f.feature_type})`));
            
            const existingObjectsMap = {};
            
            // 1. Map existing feature objects by ID for quick lookup
            canvas.getObjects().forEach(obj => {
                if (obj.data && obj.data.featureId) {
                    existingObjectsMap[obj.data.featureId] = obj;
                }
            });
            
            // 2. Remove all *existing* feature objects from the canvas
            //    (We don't remove gridlines or other non-feature objects)
            Object.values(existingObjectsMap).forEach(obj => {
                canvas.remove(obj);
            });
            
            log('Removed existing features, preparing to re-add in order.');
            
            // 3. Add features back in the order received from the backend.
            //    Fabric.js adds objects to the top, so this order naturally creates the correct stacking.
            incomingFeatures.forEach((feature, index) => {
                log(`Adding/Updating feature at index ${index}: ${feature.id} (${feature.name})`);
                const existingObject = existingObjectsMap[feature.id];
                
                if (existingObject) {
                    // Re-add the existing object instance (preserves transformations)
                    // Update properties like visibility/opacity if needed
                    existingObject.set({
                        visible: feature.visible !== undefined ? feature.visible : true,
                        opacity: feature.opacity ? feature.opacity / 100 : 1,
                        // Add other properties to update if necessary (e.g., lock state)
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
            
            // 4. Ensure grid lines are behind everything
            gridLines.forEach(line => {
                canvas.sendObjectToBack(line);
            });

            // 5. Render the canvas with the updated order and features
            canvas.renderAll();
            log('Canvas re-rendered with updated feature order.');
            
            // --- Old processing logic (commented out/removed) ---
            /*
            const features = updateData.selectedFeatures;
            const processFeatures = (index) => {
                // ... (old logic removed) ...
            };
            processFeatures(0);
            */
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
        backgroundColor: '#ffffff',
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
    
    log('Setting up grid');
    
    // Clear existing grid lines
    gridLines.forEach(line => {
        canvas.remove(line);
    });
    gridLines.length = 0;
    
    // Create grid lines efficiently - create and add them in batches
    const batchSize = 5; // Process 5 lines at a time
    let verticalLinesDone = 0;
    let horizontalLinesDone = 0;
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
            const line = new fabric.Line([verticalLinesDone * gridSize, 0, verticalLinesDone * gridSize, canvasHeight], {
                stroke: '#e5e7eb',
                selectable: false,
                evented: false,
                hoverCursor: 'default'
            });
            gridLines.push(line);
            canvas.add(line);
        }
        
        // Create horizontal grid lines batch
        for (let i = 0; i < batchSize && horizontalLinesDone < totalHorizontalLines; i++) {
            horizontalLinesDone++;
            const line = new fabric.Line([0, horizontalLinesDone * gridSize, canvasWidth, horizontalLinesDone * gridSize], {
                stroke: '#e5e7eb',
                selectable: false,
                evented: false,
                hoverCursor: 'default'
            });
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
            
            // Dispatch event to update the layer panel
            document.dispatchEvent(new CustomEvent('fabricjs:object-selected', {
                detail: {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY
                }
            }));
            console.log('FABRIC POSITION: Dispatched fabricjs:object-selected event');
            
            // Also notify Livewire components
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('fabricjs:object-selected', {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY
                });
                console.log('FABRIC POSITION: Dispatched Livewire fabricjs:object-selected event');
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
            
            // Dispatch event with position data
            document.dispatchEvent(new CustomEvent('fabricjs:object-modified', {
                detail: {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top,
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY
                }
            }));
            console.log('FABRIC POSITION: Dispatched fabricjs:object-modified event');
            
            // Also notify Livewire components
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('fabricjs:object-modified', {
                    id: obj.data.featureId,
                    left: obj.left,
                    top: obj.top, 
                    angle: obj.angle,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY
                });
                console.log('FABRIC POSITION: Dispatched Livewire fabricjs:object-modified event');
            }
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
            objects[0].set('opacity', opacity / 100);
            canvas.renderAll();
            log(`Set opacity for layer ${layerId} to ${opacity / 100}`);
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
            
            // Only update scale if width/height values were explicitly provided
            // Otherwise, preserve the current scale during position-only movements
            if (!isPositionOnlyUpdate && transform.width !== undefined && transform.height !== undefined) {
                // Get the original width/height (without scaling)
                const originalWidth = fabricObject.width;
                const originalHeight = fabricObject.height;
                
                if (originalWidth && originalHeight) {
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
            } else {
                // For position-only updates, maintain the current scale
                log(`Position-only update, preserving current scale (${currentScaleX})`);
            }
            
            fabricObject.setCoords();
            canvas.renderAll();
            
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
        const orderedLayers = data.layers || (data[0] ? data[0].layers : null);
        
        if (!orderedLayers) {
            console.error('Missing layers data in layers-reordered event');
            return;
        }
        
        log('Handling layer reordering...');
        
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
            // Note: We assume reordering only happens with existing objects.
            // If a new object appeared during reorder, it would be missed here.
            // The `update-canvas` listener should handle adding truly new objects.
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
            const canvasContainer = document.querySelector('.relative.bg-white.shadow-md');
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
            const canvasContainer = document.querySelector('.relative.bg-white.shadow-md');
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
        const canvasContainer = document.querySelector('.relative.bg-white.shadow-md');
        if (!canvasContainer) return;
        
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

    // Call the async loading function
    loadImageAndSetupFabricImage(feature, moveEnabled, imagePath, operationId);
}

/**
 * Sets up a Fabric.js image object using a provided HTMLImageElement.
 * This is the core logic shared by both pre-loaded and async paths.
 */
function setupFabricImageFromElement(imgElement, feature, moveEnabled) {
    try {
        log(`Setting up Fabric image for feature ${feature.id} from element.`);

        const fabricImage = new fabric.Image(imgElement, {
            left: feature.position?.x || canvas.width / 2,
            top: feature.position?.y || canvas.height / 2,
            angle: feature.position?.rotation || 0,
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
            opacity: feature.opacity ? feature.opacity / 100 : 1,
            cornerColor: '#2C3E50',
            cornerSize: 10,
            transparentCorners: false,
            borderColor: '#2C3E50',
            borderScaleFactor: 1.5, // Slightly thicker border
            data: {
                featureId: feature.id,
                featureType: feature.feature_type,
                locked: feature.locked || false
            }
        });

        // Scale the image initially based on feature type
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
                case 5: // ears
                    maxWidth = 100;
                    maxHeight = 130;
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
        
        if (fabricImage.width > maxWidth || fabricImage.height > maxHeight) {
            const scaleFactor = Math.min(
                maxWidth / fabricImage.width,
                maxHeight / fabricImage.height
            );
            fabricImage.scale(scaleFactor);
            
            // Log the scaling applied
            log(`Applied scaling of ${scaleFactor.toFixed(2)} to feature ${feature.id} (type: ${featureTypeId})`);
            
            // Store this as the feature's initial scale in the position object if it exists
            if (feature.position) {
                feature.position.scale = scaleFactor;
            }
        }

        // Apply specific scale from feature data if available, but only if it's not the default value of 1
        // This prevents overriding our type-specific scaling with the default scale
        if (feature.position?.scale && feature.position.scale !== 1) {
            log(`Applying custom scale from position data: ${feature.position.scale}`);
            fabricImage.scale(feature.position.scale);
        }

        canvas.add(fabricImage);

        // Ensure grid lines are behind
        gridLines.forEach(line => canvas.sendObjectToBack(line));

        // Select the newly added feature
        canvas.setActiveObject(fabricImage);
        canvas.renderAll();

        log(`✅ Feature ${feature.id} added and canvas rendered successfully.`);

        // Remove from loading set *after* successful addition
        loadingFeatures.delete(feature.id);

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

    const imgElement = new Image();
    imgElement.crossOrigin = 'Anonymous'; // Important for canvas tainting

    // Define error handling
    imgElement.onerror = function(error) {
        console.error(`❌ Error loading image for feature ${feature.id}:`, error);
        console.error('Image path that failed:', imagePath);

        // Simple error handling: just log and remove from loading
        // (Could implement retry logic here if needed)
        loadingFeatures.delete(feature.id);
        processedEvents.delete(operationId); // Clean up the processed event tracker
    };

    // Define success handling
    imgElement.onload = function() {
        log(`Image loaded successfully via async for feature ${feature.id}`);
        // Now call the common setup function using the loaded element
        setupFabricImageFromElement(imgElement, feature, moveEnabled);
        // Clean up the processed event tracker after successful async load and setup
        processedEvents.delete(operationId);
    };

    // Set the src to start loading
    imgElement.src = imagePath;
} 