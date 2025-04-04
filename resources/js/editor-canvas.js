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
            
            // Process features in chunks of 2 to avoid blocking main thread
            const features = updateData.selectedFeatures;
            const processFeatures = (index) => {
                if (index >= features.length) {
                    // All features processed, render canvas and ensure grid is behind
                    gridLines.forEach(line => {
                        canvas.sendObjectToBack(line);
                    });
                    canvas.renderAll();
                    return;
                }
                
                const feature = features[index];
                // Process current feature
                const existingObject = canvas.getObjects().find(obj => 
                    obj.data && obj.data.featureId === feature.id
                );
                
                if (!existingObject) {
                    log(`Feature ${feature.id} not found on canvas, attempting to add.`);
                    addFeatureToCanvas(feature);
                } else {
                    log(`Feature ${feature.id} already exists, ensuring properties are up-to-date.`);
                    existingObject.set({
                        visible: feature.visible !== undefined ? feature.visible : true,
                        opacity: feature.opacity ? feature.opacity / 100 : 1,
                    });
                }
                
                // Process next feature in next frame
                requestAnimationFrame(() => processFeatures(index + 1));
            };

            // Start processing features
            processFeatures(0);
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
    
    // Update wheel event handling with passive compatibility
    canvas.wrapperEl.addEventListener('wheel', function(e) {
        // Only capture wheel events with Ctrl/Cmd key pressed for zooming
        if (e.ctrlKey || e.metaKey) {
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
        }
    }, { passive: true }); // Explicitly mark as passive even though our patch already does this
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
            
            // In Fabric.js, objects added last appear on top (highest z-index)
            // The layers array from the backend LayerPanel is ordered top-to-bottom visually.
            // So, we need to reverse this array before adding to Fabric.js canvas to match stacking.
            const reversedLayers = [...orderedLayers].reverse(); // Create a reversed copy
            
            log('Adding features in correct stacking order (Fabric.js):');
            log('Bottom layers first, top layers last');
            
            // Display the layer order we're processing (bottom to top for Fabric)
            const featureIds = reversedLayers.map(l => l.id);
            log('Layer order (bottom to top):', featureIds);
            
            // Process each feature in the reversed order (bottom to top for Fabric)
            for (let i = 0; i < reversedLayers.length; i++) {
                const layer = reversedLayers[i];
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
        document.getElementById('zoom-in')?.addEventListener('click', function() {
            const canvasContainer = document.querySelector('.relative.bg-white.shadow-md');
            const currentScale = parseFloat(canvasContainer.getAttribute('data-scale') || '1');
            const newScale = Math.min(currentScale * 1.1, 3); // Limit max zoom to 3x
            
            applyCanvasZoom(newScale);
            component.call('zoomIn', newScale);
        });
        
        document.getElementById('zoom-out')?.addEventListener('click', function() {
            const canvasContainer = document.querySelector('.relative.bg-white.shadow-md');
            const currentScale = parseFloat(canvasContainer.getAttribute('data-scale') || '1');
            const newScale = Math.max(currentScale * 0.9, 0.5); // Limit min zoom to 0.5x
            
            applyCanvasZoom(newScale);
            component.call('zoomOut', newScale);
        });
        
        document.getElementById('reset-zoom')?.addEventListener('click', function() {
            applyCanvasZoom(1);
            component.call('resetZoom');
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
                    document.getElementById('zoom-in')?.click();
                } else if (e.key === '-') {
                    e.preventDefault();
                    document.getElementById('zoom-out')?.click();
                } else if (e.key === '0') {
                    e.preventDefault();
                    document.getElementById('reset-zoom')?.click();
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

        // Scale the image initially (e.g., fit within 200x200)
        const maxWidth = 200;
        const maxHeight = 200;
        if (fabricImage.width > maxWidth || fabricImage.height > maxHeight) {
            const scaleFactor = Math.min(
                maxWidth / fabricImage.width,
                maxHeight / fabricImage.height
            );
            fabricImage.scale(scaleFactor);
        }

        // Apply specific scale from feature data if available
        if (feature.position?.scale) {
            // Note: Fabric.js scale is multiplicative. If we scaled above,
            // we need to account for that, or reset and apply this scale.
            // Let's assume feature.position.scale is the desired final scale.
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