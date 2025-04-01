import * as fabric from 'fabric';

// Debug Livewire events and connections
console.log('Editor canvas script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded, initializing Fabric.js canvas');
    
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
    
    // Make wheel event listeners passive
    canvas.wrapperEl.addEventListener('wheel', function(e) {
        // Handle zooming if needed
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
            console.log('Feature with ID', feature.id, 'already exists on canvas. Skipping...');
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
        
        // Use absolute URL to prevent path issues
        const imagePath = `/storage/${feature.image_path}`;
        console.log('Attempting to load image from:', imagePath);
        
        // Create an image element first to check if it loads
        const imgElement = new Image();
        
        // Important: Set crossOrigin before setting src
        imgElement.crossOrigin = 'Anonymous';
        
        imgElement.onload = function() {
            console.log('Image loaded successfully:', imgElement.width, 'x', imgElement.height);
            
            try {
                // Now create the fabric.js image
                const fabricImage = new fabric.Image(imgElement, {
                    left: feature.position?.x || canvas.width / 2,
                    top: feature.position?.y || canvas.height / 2,
                    angle: feature.position?.rotation || 0,
                    hasControls: feature.locked ? false : true,
                    hasBorders: feature.locked ? false : true,
                    selectable: feature.locked ? false : true,
                    evented: feature.locked ? false : true,
                    lockMovementX: feature.locked ? true : false,
                    lockMovementY: feature.locked ? true : false,
                    lockRotation: feature.locked ? true : false,
                    lockScalingX: feature.locked ? true : false,
                    lockScalingY: feature.locked ? true : false,
                    visible: feature.visible !== undefined ? feature.visible : true,
                    opacity: feature.opacity ? feature.opacity / 100 : 1, // Convert from 0-100 to 0-1
                    cornerColor: '#2C3E50',
                    cornerSize: 10,
                    transparentCorners: false,
                    data: {
                        featureId: feature.id,
                        featureType: feature.feature_type // Store the feature type for future reference
                    }
                });
                
                // Apply blend mode if specified
                if (feature.blend_mode) {
                    const blendModeMap = {
                        'Normal': 'source-over',
                        'Multiply': 'multiply',
                        'Screen': 'screen',
                        'Overlay': 'overlay',
                        'Darken': 'darken',
                        'Lighten': 'lighten'
                    };
                    fabricImage.globalCompositeOperation = blendModeMap[feature.blend_mode] || 'source-over';
                }
                
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
            } catch (error) {
                console.error('Error creating fabric image:', error);
            } finally {
                // Remove this feature from the loading set
                loadingFeatures.delete(feature.id);
                console.log('Removed feature ID', feature.id, 'from loading set. Current loading:', Array.from(loadingFeatures));
            }
        };
        
        imgElement.onerror = function(error) {
            console.error('❌ Error loading image:', error);
            console.error('Image path that failed:', imagePath);
            
            // Try to load the image again with a timestamp to bypass cache
            console.log('Attempting to load with cache-busting...');
            imgElement.src = `${imagePath}?t=${new Date().getTime()}`;
            
            // Remove this feature from the loading set if it fails
            loadingFeatures.delete(feature.id);
            console.log('Removed feature ID', feature.id, 'from loading set due to error. Current loading:', Array.from(loadingFeatures));
        };
        
        // Set src after defining event handlers
        imgElement.src = imagePath;
    }
    
    // Set up Livewire event handlers
    console.log('Setting up Livewire event listeners');
    
    // Listen for feature-selected directly as well
    document.addEventListener('livewire:feature-selected', (event) => {
        console.log('Direct livewire:feature-selected event caught:', event.detail);
    });
    
    // Listen for direct DOM events
    document.addEventListener('direct-update-canvas', (event) => {
        console.log('DOM direct-update-canvas event caught. Detail:', event.detail);
        let featureData = event.detail;
        // Check if detail is an array and extract the first element if necessary
        if (Array.isArray(event.detail) && event.detail.length > 0) {
            featureData = event.detail[0];
        }
        
        if (featureData && featureData.feature) {
            console.log('Calling addFeatureToCanvas from DOM direct-update-canvas listener.');
            addFeatureToCanvas(featureData.feature);
        } else {
            console.warn('DOM direct-update-canvas event did not contain expected feature data:', featureData);
        }
    });
    
    // Also listen for traditional Livewire events
    document.addEventListener('livewire:direct-update-canvas', (event) => {
        console.log('Livewire direct-update-canvas event caught. Detail:', event.detail);
        let featureData = event.detail;
        // Check if detail is an array and extract the first element if necessary
        if (Array.isArray(event.detail) && event.detail.length > 0) {
            featureData = event.detail[0];
        }
        
        if (featureData && featureData.feature) {
            console.log('Calling addFeatureToCanvas from Livewire direct-update-canvas listener.');
            addFeatureToCanvas(featureData.feature);
        } else {
            console.warn('Livewire direct-update-canvas event did not contain expected feature data:', featureData);
        }
    });
    
    document.addEventListener('livewire:update-canvas', (event) => {
        console.log('Livewire update-canvas event caught. Detail:', event.detail);
        let updateData = event.detail;
        // Check if detail is an array and extract the first element if necessary
        if (Array.isArray(event.detail) && event.detail.length > 0) {
            updateData = event.detail[0];
        }
        
        if (updateData && updateData.selectedFeatures) {
            const latestFeature = updateData.selectedFeatures[updateData.selectedFeatures.length - 1];
            if (latestFeature) {
                console.log('Calling addFeatureToCanvas from Livewire update-canvas listener.');
                addFeatureToCanvas(latestFeature);
            } else {
                 console.warn('Livewire update-canvas event had selectedFeatures, but the last item was empty.');
            }
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
        
        // Listen for feature-selected event from Livewire
        Livewire.on('update-canvas', (data) => {
            console.log('Canvas update received through Livewire.on. Data:', data);
            let updateData = data;
            // Check if data is an array and extract the first element if necessary
            if (Array.isArray(data) && data.length > 0) {
                 updateData = data[0];
            }
            
            // Check if there's a new feature to add
            if (updateData && updateData.selectedFeatures && updateData.selectedFeatures.length > 0) {
                // First check if we have more than one feature - could be a reordering operation
                if (updateData.selectedFeatures.length > 1) {
                    // Handle reordering by rebuilding the canvas in the correct order
                    console.log('Handling potential layer reordering...');
                    
                    // Get a map of existing objects on the canvas by feature ID
                    const existingObjectsMap = {};
                    canvas.getObjects().forEach(obj => {
                        if (obj.data && obj.data.featureId) {
                            existingObjectsMap[obj.data.featureId] = obj;
                        }
                    });
                    
                    // Remove all feature objects from canvas
                    canvas.getObjects().forEach(obj => {
                        if (obj.data && obj.data.featureId) {
                            canvas.remove(obj);
                        }
                    });
                    
                    // In Fabric.js, objects added last appear on top (highest z-index)
                    // The selected features array from the backend is ordered bottom-to-top
                    // So we add them in the same order to maintain correct visual stacking
                    console.log('Adding features in correct stacking order:');
                    console.log('Bottom layers first, top layers last');
                    
                    // Display the layer order we're processing
                    const featureIds = updateData.selectedFeatures.map(f => f.id);
                    console.log('Layer order (bottom to top):', featureIds);
                    
                    // Process each feature in order (bottom to top)
                    for (let i = 0; i < updateData.selectedFeatures.length; i++) {
                        const feature = updateData.selectedFeatures[i];
                        const featureId = feature.id;
                        console.log(`Processing feature at index ${i}: ${featureId} (${feature.name})`);
                        
                        if (existingObjectsMap[featureId]) {
                            canvas.add(existingObjectsMap[featureId]);
                            console.log(`Re-added feature ID ${featureId} to canvas in new order (position ${i})`);
                        } else {
                            // If object doesn't exist, add it
                            console.log(`Feature ID ${featureId} not found in existing objects, adding new`);
                            addFeatureToCanvas(feature);
                        }
                    }
                    
                    canvas.renderAll();
                } else {
                    // Just a single feature, handle as before
                    const latestFeature = updateData.selectedFeatures[updateData.selectedFeatures.length - 1];
                    console.log('Calling addFeatureToCanvas from Livewire.on update-canvas listener.');
                    addFeatureToCanvas(latestFeature);
                }
            } else {
                console.warn('Livewire.on update-canvas did not contain expected selectedFeatures data:', updateData);
            }
        });
        
        // Tool handlers
        document.getElementById('move-tool')?.addEventListener('click', function() {
            canvas.isDrawingMode = false;
            component.call('setTool', 'move');
        });
        
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
            const zoom = canvas.getZoom();
            canvas.setZoom(zoom * 1.1);
            component.call('zoomIn');
        });
        
        document.getElementById('zoom-out')?.addEventListener('click', function() {
            const zoom = canvas.getZoom();
            canvas.setZoom(zoom * 0.9);
            component.call('zoomOut');
        });
        
        document.getElementById('reset-zoom')?.addEventListener('click', function() {
            canvas.setZoom(1);
            component.call('resetZoom');
        });
        
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
        });
    }
}); 