import * as fabric from 'fabric';

// Debug Livewire events and connections
console.log('Editor canvas script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded, initializing Fabric.js canvas');
    
    // Add debug info
    console.log('Setting up event listeners for Livewire events');
    
    // Set up global event debug
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
            console.log(`Caught ${eventName} event on window:`, event.detail);
        });
    });
    
    // Check if the canvas element exists
    const canvasElement = document.getElementById('editor-canvas');
    if (!canvasElement) {
        console.error('Canvas element not found');
        return;
    }
    
    // Initialize Fabric.js canvas
    const canvas = new fabric.Canvas('editor-canvas', {
        backgroundColor: '#ffffff',
        selection: true,
        preserveObjectStacking: true,
        width: 600,
        height: 600
    });
    
    console.log('Canvas initialized:', canvas);
    
    // Canvas grid options
    const gridSize = 20;
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    
    // Set up grid background - track grid objects
    const gridLines = [];
    
    // Keep track of features currently being loaded to prevent duplicates
    const loadingFeatures = new Set();
    
    // Keep track of features that need to be selected after loading
    const pendingSelectionRequests = new Set();
    
    function setupGrid() {
        console.log('Setting up grid');
        // Clear existing grid lines
        gridLines.forEach(line => {
            canvas.remove(line);
        });
        gridLines.length = 0;
        
        // Create vertical grid lines
        for (let i = 1; i < (canvasWidth / gridSize); i++) {
            const line = new fabric.Line([i * gridSize, 0, i * gridSize, canvasHeight], {
                stroke: '#e5e7eb',
                selectable: false,
                evented: false,
                hoverCursor: 'default'
            });
            gridLines.push(line);
            canvas.add(line);
        }
        
        // Create horizontal grid lines
        for (let i = 1; i < (canvasHeight / gridSize); i++) {
            const line = new fabric.Line([0, i * gridSize, canvasWidth, i * gridSize], {
                stroke: '#e5e7eb',
                selectable: false,
                evented: false,
                hoverCursor: 'default'
            });
            gridLines.push(line);
            canvas.add(line);
        }
        
        // Send grid lines to the back
        gridLines.forEach(line => {
            canvas.sendObjectToBack(line);
        });
        
        canvas.renderAll();
        console.log('Grid setup complete. Total grid lines:', gridLines.length);
    }
    
    // Set up initial grid
    setupGrid();
    
    // Fix wheel event listener warning by properly setting passive flag
    // First remove any existing wheel listeners
    const oldWheelHandler = function(e) {};
    canvas.wrapperEl.removeEventListener('wheel', oldWheelHandler);
    
    // Add new passive wheel event listener
    canvas.wrapperEl.addEventListener('wheel', function(e) {
        // This is a passive event listener that doesn't block scrolling
    }, { passive: true });
    
    // Function to add a feature to the canvas - defined here to have access to canvas
    function addFeatureToCanvas(feature) {
        console.log('Adding feature to canvas:', feature);
        
        // Make sure image_path is valid
        if (!feature.image_path) {
            console.error('Feature image_path is missing or invalid:', feature);
            return;
        }
        
        // Check if this feature already exists on the canvas
        const existingObjects = canvas.getObjects().filter(obj => 
            obj.data && 
            obj.data.featureId === feature.id
        );
        
        if (existingObjects.length > 0) {
            console.log('Feature with ID', feature.id, 'already exists on canvas. Selecting it...');
            canvas.setActiveObject(existingObjects[0]);
            canvas.renderAll();
            return;
        }
        
        // Check for existing features of the same type and remove them
        if (feature.feature_type) {
            const objectsOfSameType = canvas.getObjects().filter(obj => 
                obj.data && 
                obj.data.featureType === feature.feature_type
            );
            
            if (objectsOfSameType.length > 0) {
                console.log(`Found ${objectsOfSameType.length} existing feature(s) of type ${feature.feature_type}. Removing them before adding new one.`);
                objectsOfSameType.forEach(obj => {
                    canvas.remove(obj);
                    
                    // Notify Livewire component about the removal
                    if (typeof Livewire !== 'undefined') {
                        const component = Livewire.find(
                            document.getElementById('main-canvas-component')?.getAttribute('wire:id')
                        );
                        
                        if (component && obj.data && obj.data.featureId) {
                            component.call('removeFeature', obj.data.featureId);
                        }
                    }
                });
            }
        }
        
        // Check if this feature is currently loading
        if (loadingFeatures.has(feature.id)) {
            console.log('Feature with ID', feature.id, 'is already being loaded. Skipping...');
            return;
        }
        
        // Add this feature to the loading set
        loadingFeatures.add(feature.id);
        console.log('Added feature ID', feature.id, 'to loading set. Current loading:', Array.from(loadingFeatures));
        
        // Modify the image loading part with better path handling
        let imagePath = `/storage/${feature.image_path}`;
        
        // Check if the path already has /storage at the beginning
        if (feature.image_path.startsWith('/storage/')) {
            imagePath = feature.image_path;
        } else if (feature.image_path.startsWith('storage/')) {
            imagePath = `/${feature.image_path}`;
        }
        
        console.log('Attempting to load image from:', imagePath);
        
        // Get the current moveEnabled state from the Livewire component
        let moveEnabled = true; // Default to true if we can't find the component
        if (typeof Livewire !== 'undefined') {
            const component = Livewire.find(
                document.getElementById('main-canvas-component')?.getAttribute('wire:id')
            );
            if (component) {
                moveEnabled = component.get('moveEnabled');
            }
        }
        
        // Create an image element first to check if it loads
        const imgElement = new Image();
        
        // Important: Set crossOrigin before setting src
        imgElement.crossOrigin = 'Anonymous';
        
        // Better error handling and retry logic
        imgElement.onerror = function(error) {
            console.error('❌ Error loading image:', error);
            console.error('Image path that failed:', imagePath);
            
            // Try alternate paths in sequence
            const pathsToTry = [
                `/storage${feature.image_path}`,
                feature.image_path,
                `/storage/${feature.image_path.replace(/^\/+|^storage\/+/g, '')}`,
                `${imagePath}?t=${new Date().getTime()}`  // Cache busting as last resort
            ];
            
            const nextPath = pathsToTry.shift();
            if (nextPath && !imgElement.dataset.retriesCount) {
                // Start retry count
                imgElement.dataset.retriesCount = '1';
                console.log(`Retry ${imgElement.dataset.retriesCount} with path:`, nextPath);
                imgElement.src = nextPath;
            } else if (nextPath && parseInt(imgElement.dataset.retriesCount) < pathsToTry.length) {
                // Increment retry count
                imgElement.dataset.retriesCount = (parseInt(imgElement.dataset.retriesCount) + 1).toString();
                console.log(`Retry ${imgElement.dataset.retriesCount} with path:`, nextPath);
                imgElement.src = nextPath;
            } else {
                console.error('All image loading attempts failed for feature:', feature);
                loadingFeatures.delete(feature.id);
            }
        };
        
        imgElement.onload = function() {
            console.log('Image loaded successfully:', imgElement.width, 'x', imgElement.height);
            
            try {
                // Now create the fabric.js image
                const fabricImage = new fabric.Image(imgElement, {
                    left: feature.position?.x || canvas.width / 2,
                    top: feature.position?.y || canvas.height / 2,
                    angle: feature.position?.rotation || 0,
                    hasControls: feature.locked ? false : moveEnabled,
                    hasBorders: feature.locked ? false : moveEnabled,
                    selectable: feature.locked ? false : moveEnabled,
                    evented: feature.locked ? false : moveEnabled,
                    lockMovementX: feature.locked ? true : !moveEnabled,
                    lockMovementY: feature.locked ? true : !moveEnabled,
                    lockRotation: feature.locked ? true : !moveEnabled,
                    lockScalingX: feature.locked ? true : !moveEnabled,
                    lockScalingY: feature.locked ? true : !moveEnabled,
                    visible: feature.visible !== undefined ? feature.visible : true,
                    opacity: feature.opacity ? feature.opacity / 100 : 1, // Convert from 0-100 to 0-1
                    cornerColor: '#2C3E50',
                    cornerSize: 10,
                    transparentCorners: false,
                    data: {
                        featureId: feature.id,
                        featureType: feature.feature_type, // Store the feature type for future reference
                        locked: feature.locked || false
                    }
                });
                
                // Scale the image to fit within a reasonable size
                const maxWidth = 200;
                const maxHeight = 200;
                
                if (fabricImage.width > maxWidth || fabricImage.height > maxHeight) {
                    const scaleFactor = Math.min(
                        maxWidth / fabricImage.width,
                        maxHeight / fabricImage.height
                    );
                    fabricImage.scale(scaleFactor);
                }
                
                // Scale based on the feature's scale property if it exists
                if (feature.position?.scale) {
                    fabricImage.scale(fabricImage.scaleX * feature.position.scale);
                }
                
                // Add the image to the canvas
                canvas.add(fabricImage);
                
                // Make sure grid lines stay in the back
                gridLines.forEach(line => {
                    canvas.sendObjectToBack(line);
                });
                
                // Set the image as the active object
                canvas.setActiveObject(fabricImage);
                canvas.renderAll();
                
                console.log('✅ Feature added and canvas rendered successfully');
                
                // Remove this feature from the loading set
                loadingFeatures.delete(feature.id);
                
                // Restore modified event handler
                if (typeof Livewire !== 'undefined') {
                    const component = Livewire.find(
                        document.getElementById('main-canvas-component')?.getAttribute('wire:id')
                    );
                    
                    if (component) {
                        // Send position data back to Livewire component when object is modified
                        fabricImage.on('modified', function() {
                            const obj = canvas.getActiveObject();
                            if (obj) {
                                component.call('updateFeaturePosition', {
                                    featureId: obj.data.featureId,
                                    position: {
                                        x: Math.round(obj.left),
                                        y: Math.round(obj.top),
                                        rotation: Math.round(obj.angle),
                                        scale: Math.round((obj.scaleX + obj.scaleY) / 2 * 100) / 100
                                    }
                                });
                            }
                        });
                    }
                }
                
                // Process any pending selection requests for this feature
                if (pendingSelectionRequests.has(feature.id)) {
                    console.log(`Processing pending selection request for feature ${feature.id}`);
                    canvas.setActiveObject(fabricImage);
                    canvas.renderAll();
                    pendingSelectionRequests.delete(feature.id);
                }
            } catch (error) {
                console.error('Error creating fabric image:', error);
                loadingFeatures.delete(feature.id);
            }
        };
        
        // Set src after defining event handlers
        imgElement.src = imagePath;
    }
    
    // Set up Livewire event handlers
    console.log('Setting up Livewire event listeners');
    
    document.addEventListener('livewire:update-canvas', (event) => {
        console.log('Livewire update-canvas event caught. Detail:', event.detail);
        let updateData = event.detail;
        
        // Ensure we have the actual data, not wrapped in an array sometimes
        if (Array.isArray(updateData) && updateData.length > 0 && updateData[0]?.selectedFeatures) {
            updateData = updateData[0];
        }
        
        if (updateData && updateData.selectedFeatures) {
            console.log('Processing update-canvas event with features:', updateData.selectedFeatures);
            
            // Process all features sent in the update
            updateData.selectedFeatures.forEach(feature => {
                // Check if the feature already exists on the canvas
                const existingObject = canvas.getObjects().find(obj => 
                    obj.data && obj.data.featureId === feature.id
                );
                
                if (!existingObject) {
                    console.log(`Feature ${feature.id} not found on canvas, attempting to add.`);
                    addFeatureToCanvas(feature);
                } else {
                    console.log(`Feature ${feature.id} already exists, ensuring properties are up-to-date.`);
                    // Optional: Update properties of existing object if needed (e.g., visibility, opacity)
                    existingObject.set({
                        visible: feature.visible !== undefined ? feature.visible : true,
                        opacity: feature.opacity ? feature.opacity / 100 : 1,
                        // Update other relevant properties if they can change via this event
                    });
                }
            });
            
            // After processing adds/updates, handle potential removals
            const currentFeatureIds = updateData.selectedFeatures.map(f => f.id);
            canvas.getObjects().forEach(obj => {
                if (obj.data && obj.data.featureId && !currentFeatureIds.includes(obj.data.featureId)) {
                    console.log(`Removing feature ${obj.data.featureId} as it's no longer in selectedFeatures.`);
                    canvas.remove(obj);
                }
            });
            
            // Ensure correct stacking order after updates
            // Note: This assumes selectedFeatures is ordered bottom-to-top
            updateData.selectedFeatures.forEach((feature, index) => {
                const fabricObject = canvas.getObjects().find(obj => obj.data && obj.data.featureId === feature.id);
                if (fabricObject) {
                    canvas.moveTo(fabricObject, index);
                }
            });

            // Ensure grid lines are behind everything
            gridLines.forEach(line => {
                canvas.sendObjectToBack(line);
            });

            canvas.renderAll();
        } else {
            console.warn('Livewire update-canvas event did not contain expected selectedFeatures data:', updateData);
        }
    });
    
    // Additional event listeners for layer panel interactions
    document.addEventListener('livewire:update-feature-visibility', (event) => {
        console.log('Update feature visibility event caught:', event.detail);
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
        console.log('Update feature opacity event caught:', event.detail);
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
    
    document.addEventListener('livewire:update-feature-blend-mode', (event) => {
        console.log('Update feature blend mode event caught:', event.detail);
        const featureId = event.detail.featureId;
        const blendMode = event.detail.blendMode;
        
        // Map Livewire blend mode names to fabric.js globalCompositeOperation values
        const blendModeMap = {
            'Normal': 'source-over',
            'Multiply': 'multiply',
            'Screen': 'screen',
            'Overlay': 'overlay',
            'Darken': 'darken',
            'Lighten': 'lighten'
        };
        
        // Find the object(s) with this feature ID
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        objects.forEach(obj => {
            obj.globalCompositeOperation = blendModeMap[blendMode] || 'source-over';
        });
        
        canvas.renderAll();
    });
    
    document.addEventListener('livewire:select-feature', (event) => {
        console.log('Select feature event caught:', event.detail);
        const featureId = event.detail.featureId;
        
        // Find the object with this feature ID
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        if (objects.length > 0) {
            // Select the first found object
            canvas.setActiveObject(objects[0]);
            canvas.renderAll();
            console.log('Selected feature on canvas:', objects[0]);
        }
    });
    
    // Check if Livewire is initialized already
    if (typeof Livewire !== 'undefined') {
        setupLivewireHandlers();
    } else {
        window.addEventListener('livewire:initialized', setupLivewireHandlers);
    }
    
    function setupLivewireHandlers() {
        console.log('Livewire initialized');
        
        // Log all Livewire components for debugging
        console.log('Available Livewire components:', Livewire.all());
        
        const component = Livewire.find(
            document.getElementById('main-canvas-component')?.getAttribute('wire:id')
        );
        
        if (!component) {
            console.error('Cannot find main-canvas component. Element:', document.getElementById('main-canvas-component'));
            console.error('Element wire:id:', document.getElementById('main-canvas-component')?.getAttribute('wire:id'));
            return;
        }
        
        console.log('Found main-canvas component:', component);
        
        // Get initial moveEnabled state and apply to any existing canvas objects
        try {
            const moveEnabled = component.get('moveEnabled');
            console.log('Initial moveEnabled state:', moveEnabled);
            
            // Apply this state to any objects already on the canvas
            updateCanvasObjectsMoveState(moveEnabled);
            
            // Also initialize zoom level from component
            const zoomLevel = component.get('zoomLevel');
            if (zoomLevel) {
                const scale = zoomLevel / 100; // Convert from percentage to decimal
                console.log('Initial zoom level:', zoomLevel, '%, scale:', scale);
                applyCanvasZoom(scale);
            }
        } catch (error) {
            console.error('Error getting initial state:', error);
        }
        
        // Listener for layer visibility change
        Livewire.on('layer-visibility-changed', (data) => {
            console.log('Layer visibility changed event:', data);
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
                console.log(`Set visibility for layer ${layerId} to ${visible}`);
            } else {
                console.warn(`Layer ${layerId} not found on canvas for visibility change.`);
            }
        });
        
        // Listener for layer opacity change
        Livewire.on('layer-opacity-changed', (data) => {
            console.log('Layer opacity changed event:', data);
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
                console.log(`Set opacity for layer ${layerId} to ${opacity / 100}`);
            } else {
                console.warn(`Layer ${layerId} not found on canvas for opacity change.`);
            }
        });
        
        // Listener for layer lock change
        Livewire.on('layer-lock-changed', (data) => {
            console.log('Layer lock changed event:', data);
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
                console.log(`Set lock state for layer ${layerId} to ${isLocked}`);
            } else {
                console.warn(`Layer ${layerId} not found on canvas for lock change.`);
            }
        });
        
        // Listener for selecting a feature on the canvas (triggered by LayerPanel click)
        Livewire.on('select-feature-on-canvas', (data) => {
            console.log('Select feature on canvas event:', data);
            const featureId = data.featureId || (data[0] ? data[0].featureId : null);
            
            if (!featureId) {
                console.error('Missing featureId in select-feature-on-canvas event');
                return;
            }
            
            const objects = canvas.getObjects().filter(obj => obj.data && obj.data.featureId === featureId);
            if (objects.length > 0) {
                canvas.setActiveObject(objects[0]);
                canvas.renderAll();
                console.log(`Selected feature ${featureId} on canvas.`);
            } else {
                console.warn(`Feature ${featureId} not found on canvas for selection. Adding to pending selection requests.`);
                // Store this as a pending selection request
                pendingSelectionRequests.add(featureId);
            }
        });
        
        // Listener for reordering layers
        Livewire.on('layers-reordered', (data) => {
            console.log('Layers reordered event:', data);
            const orderedLayers = data.layers || (data[0] ? data[0].layers : null);
            
            if (!orderedLayers) {
                console.error('Missing layers data in layers-reordered event');
                return;
            }
            
            console.log('Handling layer reordering...');
            
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
            
            console.log('Adding features in correct stacking order (Fabric.js):');
            console.log('Bottom layers first, top layers last');
            
            // Display the layer order we're processing (bottom to top for Fabric)
            const featureIds = reversedLayers.map(l => l.id);
            console.log('Layer order (bottom to top):', featureIds);
            
            // Process each feature in the reversed order (bottom to top for Fabric)
            for (let i = 0; i < reversedLayers.length; i++) {
                const layer = reversedLayers[i];
                const featureId = layer.id;
                console.log(`Processing feature at Fabric index ${i}: ${featureId} (${layer.name})`);
                
                if (existingObjectsMap[featureId]) {
                    canvas.add(existingObjectsMap[featureId]);
                    console.log(`Re-added feature ID ${featureId} to canvas in new order (position ${i})`);
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
            console.log('Layer reordering complete.');
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
            
            console.log('Toggle move mode event received:', enabled);
            
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
            
            console.log('Livewire toggle-move-mode event received:', enabled);
            
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

    // When a feature is selected, make sure it's added to the canvas first
    window.addEventListener('feature-selected', (event) => {
        console.log('Feature selected event caught in main-canvas:', event.detail);
        console.log('Event type:', event.type);
        console.log('Event target:', event.target);
        
        const featureId = Array.isArray(event.detail) ? event.detail[0] : event.detail;
        console.log('Extracted feature ID:', featureId);
        
        // Try to find it on canvas first
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        console.log(`Found ${objects.length} objects with feature ID ${featureId} on canvas`);
        
        if (objects.length > 0) {
            // Feature exists, select it
            console.log('Selecting existing feature on canvas');
            canvas.setActiveObject(objects[0]);
            canvas.renderAll();
        } else {
            // Feature doesn't exist yet, add to pending selection
            pendingSelectionRequests.add(featureId);
            console.log(`Added feature ${featureId} to pending selection requests. Current pending:`, 
                Array.from(pendingSelectionRequests));
            
            // Request feature to be added
            if (typeof Livewire !== 'undefined') {
                const component = Livewire.find(
                    document.getElementById('main-canvas-component')?.getAttribute('wire:id')
                );
                
                if (component) {
                    console.log('Dispatching request to get feature data');
                    component.call('requestFeatureData', featureId);
                }
            }
        }
    });

    // Make sure we listen for the Livewire version of the event too
    document.addEventListener('livewire:feature-selected', (event) => {
        console.log('Livewire feature-selected event caught:', event.detail);
        const featureId = Array.isArray(event.detail) ? event.detail[0] : event.detail;
        
        // Try to find it on canvas first
        const objects = canvas.getObjects().filter(obj => 
            obj.data && obj.data.featureId === featureId
        );
        
        if (objects.length > 0) {
            // Feature exists, select it
            canvas.setActiveObject(objects[0]);
            canvas.renderAll();
        } else {
            // Feature doesn't exist yet, add to pending selection
            pendingSelectionRequests.add(featureId);
            console.log(`Added feature ${featureId} to pending selection requests from Livewire event.`);
        }
    });

    // Update to listen for both window and document events for direct-update-canvas
    window.addEventListener('direct-update-canvas', (event) => {
        console.log('Window direct-update-canvas event caught:', event.detail);
        handleDirectUpdateCanvas(event.detail);
    });

    document.addEventListener('direct-update-canvas', (event) => {
        console.log('Document direct-update-canvas event caught:', event.detail);
        handleDirectUpdateCanvas(event.detail);
    });
    
    document.addEventListener('livewire:direct-update-canvas', (event) => {
        console.log('Livewire direct-update-canvas event caught:', event.detail);
        handleDirectUpdateCanvas(event.detail);
    });
    
    // Centralized handler for direct-update-canvas events
    function handleDirectUpdateCanvas(detail) {
        // Inspect data closely
        console.log('Processing direct update canvas data:', detail);
        
        let featureData = null;
        
        // Try different data formats that might be sent
        if (detail && detail.feature) {
            console.log('Found feature in direct format:', detail.feature);
            featureData = detail.feature;
        } else if (Array.isArray(detail) && detail.length > 0) {
            if (detail[0].feature) {
                console.log('Found feature in array[0].feature format:', detail[0].feature);
                featureData = detail[0].feature;
            } else if (detail[0].id && detail[0].image_path) {
                console.log('Found feature directly in array[0]:', detail[0]);
                featureData = detail[0];
            }
        } else if (detail && detail.id && detail.image_path) {
            console.log('Found feature directly in detail:', detail);
            featureData = detail;
        }
        
        if (featureData) {
            console.log('Adding feature to canvas:', featureData);
            addFeatureToCanvas(featureData);
        } else {
            console.error('Could not extract feature data from event:', detail);
        }
    }
}); 