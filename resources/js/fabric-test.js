import * as fabric from 'fabric';

document.addEventListener('DOMContentLoaded', function() {
    // Check if WebGL is available
    function checkWebGLSupport() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            const isWebGLSupported = gl && gl instanceof WebGLRenderingContext;
            
            console.log("%c WebGL Support: " + (isWebGLSupported ? "YES ✅" : "NO ❌"), 
                       "background: " + (isWebGLSupported ? "#4CAF50" : "#F44336") + 
                       "; color: white; padding: 5px; border-radius: 3px; font-weight: bold;");
            
            if (isWebGLSupported) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (debugInfo) {
                    const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
                    console.log("WebGL Renderer:", renderer);
                }
            }
            
            return isWebGLSupported;
        } catch (e) {
            console.error("Error checking WebGL support:", e);
            return false;
        }
    }
    
    // Check fabric filter backend
    function checkFabricFilterBackend() {
        if (fabric.filterBackend) {
            console.log("%c Fabric.js Filter Backend: " + 
                       (fabric.filterBackend instanceof fabric.WebglFilterBackend ? "WebGL ✅" : 
                        fabric.filterBackend instanceof fabric.Canvas2dFilterBackend ? "Canvas 2D" : "Unknown"), 
                       "background: #2196F3; color: white; padding: 5px; border-radius: 3px; font-weight: bold;");
        } else {
            console.log("%c Fabric.js Filter Backend: Not initialized", 
                       "background: #F44336; color: white; padding: 5px; border-radius: 3px; font-weight: bold;");
        }
    }
    
    // Initialize Fabric.js canvas
    const canvas = new fabric.Canvas('fabric-canvas', {
        backgroundColor: '#f8f9fa',
        selection: true,
        preserveObjectStacking: true
    });
    
    // Log WebGL and filter backend support
    checkWebGLSupport();
    checkFabricFilterBackend();

    // DOM Elements for Adjustments
    const contrastSlider = document.getElementById('contrast-slider');
    const saturationSlider = document.getElementById('saturation-slider');
    const contrastValueDisplay = document.getElementById('contrast-value');
    const saturationValueDisplay = document.getElementById('saturation-value');
    const resetAdjustmentsButton = document.getElementById('reset-adjustments');
    const objectSelector = document.getElementById('object-selector');

    // Track original image data for adjustments
    let currentImageObject = null;
    let originalImageElement = null; // Store the original image element

    // Function to apply direct pixel manipulation to the selected image
    function applyImageFilters() {
        const activeObject = canvas.getActiveObject();
        
        console.log("applyImageFilters called", {
            activeObject: activeObject ? {
                type: activeObject.type,
                width: activeObject.width,
                height: activeObject.height,
                src: activeObject.type === 'image' && activeObject._element ? (activeObject._element.src ? activeObject._element.src.substring(0, 50) + '...' : 'no src') : 'not an image'
            } : null,
            contrastValue: contrastSlider.value,
            saturationValue: saturationSlider.value,
            sliderElements: {
                contrastExists: Boolean(contrastSlider),
                saturationExists: Boolean(saturationSlider),
                contrastId: contrastSlider.id,
                saturationId: saturationSlider.id
            }
        });
        
        if (activeObject && activeObject.type === 'image') {
            console.log("Image Adjustment");
            console.log("Applying adjustments to image:", activeObject);
            // Get current values
            const contrastValue = parseInt(contrastSlider.value);
            const saturationValue = parseInt(saturationSlider.value);
            
            console.log(`Adjustment values - contrast: ${contrastValue}% (${contrastSlider.min} to ${contrastSlider.max}), saturation: ${saturationValue}% (${saturationSlider.min} to ${saturationSlider.max})`);
            
            // Update display values
            contrastValueDisplay.textContent = `${contrastValue}%`;
            saturationValueDisplay.textContent = `${saturationValue}%`;
            
            try {
                console.log("Applying direct pixel manipulation");
                // Use performance timing to measure method speed
                const startTime = performance.now();
                
                // Apply the filter
                applyManualFilters(activeObject, contrastValue, saturationValue);
                
                // Calculate and display performance
                const endTime = performance.now();
                const duration = endTime - startTime;
                console.log(`Direct pixel manipulation completed in ${duration.toFixed(2)}ms`);
                console.log("✅ Filter applied successfully");
            } catch (error) {
                console.error("Error applying filter:", error);
                alert("Unable to apply image adjustments. Please try a different image.");
            }
        } else {
            if (!canvas.getActiveObject()) {
                console.log("No object selected. Please select an image first.");
            } else if (canvas.getActiveObject().type !== 'image') {
                console.log("Selected object is not an image. Please select an image to adjust.", 
                            "Current selection type:", canvas.getActiveObject().type);
            }
            console.log("Select an image to apply adjustments.");
        }
    }
    
    // Apply manual filter using direct pixel manipulation
    function applyManualFilters(imgObject, contrastValue, saturationValue) {
        console.log("Direct pixel manipulation with contrast:", contrastValue, "saturation:", saturationValue);
        
        try {
            // Get the image element
            const imgElement = imgObject.getElement();
            if (!imgElement) {
                console.error("No image element found");
                throw new Error("No image element found in the fabric object");
            }
            
            console.log("Image element properties:", {
                width: imgElement.width, 
                height: imgElement.height,
                complete: imgElement.complete,
                naturalWidth: imgElement.naturalWidth,
                naturalHeight: imgElement.naturalHeight
            });
            
            // Store the original image when first processing
            if (!originalImageElement) {
                console.log("Capturing original image for the first time");
                
                // Save original source
                if (imgElement.src) {
                    // Create a new image from the original source
                    originalImageElement = new Image();
                    originalImageElement.src = imgElement.src;
                    originalImageElement.crossOrigin = "Anonymous";
                    originalImageElement.width = imgElement.width;
                    originalImageElement.height = imgElement.height;
                    
                    // Store the URL for resets
                    imgElement._originalSrc = imgElement.src;
                    console.log("Original image captured and stored");
                } else if (imgElement._originalElement && imgElement._originalElement.src) {
                    originalImageElement = imgElement._originalElement;
                    imgElement._originalSrc = imgElement._originalElement.src;
                    console.log("Original element source saved");
                } else {
                    console.warn("Could not find original source to save");
                }
            }
            
            // Create a temporary canvas for manipulation
            const tempCanvas = document.createElement('canvas');
            // Use the dimensions from the original image if available, otherwise use current
            const sourceWidth = originalImageElement ? (originalImageElement.naturalWidth || originalImageElement.width) : (imgElement.naturalWidth || imgElement.width);
            const sourceHeight = originalImageElement ? (originalImageElement.naturalHeight || originalImageElement.height) : (imgElement.naturalHeight || imgElement.height);
            tempCanvas.width = sourceWidth;
            tempCanvas.height = sourceHeight;
            
            if (tempCanvas.width === 0 || tempCanvas.height === 0) {
                console.error("Invalid canvas dimensions:", tempCanvas.width, tempCanvas.height);
                throw new Error("Invalid canvas dimensions");
            }
            
            console.log("Created temp canvas with dimensions:", tempCanvas.width, "x", tempCanvas.height);
            
            const tempCtx = tempCanvas.getContext('2d', { willReadFrequently: true });
            
            // Draw the ORIGINAL image (not the filtered one)
            const sourceImage = originalImageElement || imgElement;
            console.log("Drawing from source:", sourceImage === originalImageElement ? "original stored image" : "current image");
            tempCtx.drawImage(sourceImage, 0, 0);
            console.log("Drew image to temp canvas");
            
            // Get the image data
            let imageData;
            try {
                imageData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
                console.log("Got image data:", imageData.width, "x", imageData.height);
            } catch (e) {
                console.error("Error getting image data:", e);
                // Try a different approach to get the image
                if (imgObject._element && imgObject._element.currentSrc) {
                    console.log("Attempting to use _element.currentSrc");
                    const img = new Image();
                    img.src = imgObject._element.currentSrc;
                    tempCtx.drawImage(img, 0, 0);
                    imageData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
                } else {
                    throw new Error("Cannot get image data: " + e.message);
                }
            }
            
            const data = imageData.data;
            
            // Calculate adjustment factors
            const normalizedContrast = contrastValue + 100;
            const factor = (259 * (normalizedContrast - 0) + 255) / (255 * (259 - normalizedContrast));
            
            const normalizedSaturation = saturationValue + 100;
            const saturationFactor = normalizedSaturation / 100;
            
            console.log("Processing factors - contrast:", factor, "saturation:", saturationFactor);
            
            // Process each pixel
            for (let i = 0; i < data.length; i += 4) {
                // Apply contrast
                if (contrastValue !== 0) {
                    data[i] = truncate(factor * (data[i] - 128) + 128);     // R
                    data[i + 1] = truncate(factor * (data[i + 1] - 128) + 128); // G
                    data[i + 2] = truncate(factor * (data[i + 2] - 128) + 128); // B
                }
                
                // Apply saturation
                if (saturationValue !== 0) {
                    const gray = 0.2989 * data[i] + 0.5870 * data[i + 1] + 0.1140 * data[i + 2];
                    data[i] = truncate(gray + saturationFactor * (data[i] - gray));     // R
                    data[i + 1] = truncate(gray + saturationFactor * (data[i + 1] - gray)); // G
                    data[i + 2] = truncate(gray + saturationFactor * (data[i + 2] - gray)); // B
                }
            }
            
            // Put the modified image data back
            tempCtx.putImageData(imageData, 0, 0);
            console.log("Put modified image data back to canvas");
            
            // Create a new image from our canvas
            const newImgURL = tempCanvas.toDataURL();
            console.log("Created new image URL from canvas");
            
            // DIRECT APPROACH: Create a new image element and update the fabric object directly
            const newImg = new Image();
            newImg.onload = function() {
                console.log("New image loaded, updating fabric object directly");
                
                // Store the old image's properties before replacing it
                const oldLeft = imgObject.left;
                const oldTop = imgObject.top;
                const oldScaleX = imgObject.scaleX;
                const oldScaleY = imgObject.scaleY;
                const oldAngle = imgObject.angle;
                
                // Update the fabric object's element with our filtered image
                imgObject.setElement(newImg);
                
                // Ensure the position and scale are maintained
                imgObject.set({
                    left: oldLeft,
                    top: oldTop,
                    scaleX: oldScaleX,
                    scaleY: oldScaleY,
                    angle: oldAngle
                });
                
                // This is critical - mark the object as dirty to force a re-render
                imgObject.dirty = true;
                
                // Force the canvas to re-render
                canvas.renderAll();
                
                console.log("Image updated directly with filtered version");
            };
            
            // Set the source of the new image to our filtered canvas
            newImg.src = newImgURL;
            
            // If the image is already loaded, manually trigger the onload handler
            if (newImg.complete) {
                console.log("New image already loaded, triggering handler manually");
                newImg.onload();
            }
        } catch (error) {
            console.error("Error in manual filter application:", error);
            throw error; // Rethrow for the caller to handle
        }
    }

    // Helper function to ensure pixel values are in valid range
    function truncate(value) {
        return Math.min(255, Math.max(0, value));
    }

    // Function to reset filters for the selected image
    function resetImageFilters() {
        const activeObject = canvas.getActiveObject();

        if (activeObject && activeObject.type === 'image') {
            console.log("Resetting filters for image:", activeObject);
            
            // Reset sliders to default values
            contrastSlider.value = 0;
            saturationSlider.value = 0;
            contrastValueDisplay.textContent = '0%';
            saturationValueDisplay.textContent = '0%';
            
            // If we have the original image element stored, use it directly
            if (originalImageElement) {
                console.log("Resetting using stored original image element");
                
                // Create a fresh image
                const freshImg = new Image();
                freshImg.src = originalImageElement.src;
                freshImg.onload = function() {
                    // Store the old image's properties
                    const oldLeft = activeObject.left;
                    const oldTop = activeObject.top;
                    const oldScaleX = activeObject.scaleX;
                    const oldScaleY = activeObject.scaleY;
                    const oldAngle = activeObject.angle;
                    
                    // Update the fabric object with our fresh image
                    activeObject.setElement(freshImg);
                    
                    // Ensure the position and scale are maintained
                    activeObject.set({
                        left: oldLeft,
                        top: oldTop,
                        scaleX: oldScaleX,
                        scaleY: oldScaleY,
                        angle: oldAngle
                    });
                    
                    // Mark the object as dirty to force re-render
                    activeObject.dirty = true;
                    
                    // Re-render the canvas
                    canvas.renderAll();
                    
                    console.log("Image reset to original using stored element");
                };
                
                if (freshImg.complete) {
                    console.log("Fresh image already loaded, triggering onload");
                    freshImg.onload();
                }
                return;
            }
            
            // Get the image element
            const imgElement = activeObject.getElement();
            
            // If we have the original source, use it to create a fresh image
            if (imgElement && imgElement._originalSrc) {
                console.log("Resetting image using stored original source");
                
                const freshImg = new Image();
                freshImg.src = imgElement._originalSrc;
                freshImg.onload = function() {
                    // Store the old image's properties
                    const oldLeft = activeObject.left;
                    const oldTop = activeObject.top;
                    const oldScaleX = activeObject.scaleX;
                    const oldScaleY = activeObject.scaleY;
                    const oldAngle = activeObject.angle;
                    
                    // Update the fabric object with our fresh image
                    activeObject.setElement(freshImg);
                    
                    // Ensure the position and scale are maintained
                    activeObject.set({
                        left: oldLeft,
                        top: oldTop,
                        scaleX: oldScaleX,
                        scaleY: oldScaleY,
                        angle: oldAngle
                    });
                    
                    // Mark the object as dirty to force re-render
                    activeObject.dirty = true;
                    
                    // Re-render the canvas
                    canvas.renderAll();
                    
                    console.log("Image reset to original using _originalSrc");
                };
                
                if (freshImg.complete) {
                    console.log("Fresh image already loaded, triggering onload");
                    freshImg.onload();
                }
                return;
            }
            
            // If we don't have an original source but have fabric filters
            if (activeObject.filters && activeObject.filters.length > 0) {
                console.log("Removing Fabric.js filters");
                
                // Remove all filters
                activeObject.filters = [];
                
                try {
                    // Apply the empty filters array
                    activeObject.applyFilters();
                    canvas.renderAll();
                    console.log("Fabric.js filters removed and canvas rendered");
                    return;
                } catch (err) {
                    console.error("Error resetting Fabric.js filters:", err);
                }
            }
            
            console.warn("Cannot reset - no reliable method available");
            alert("To fully reset this image, please refresh the page or remove and re-add the image.");
        } else {
            console.log("Select an image to reset adjustments.");
        }
    }

    // Event Listeners for Sliders
    contrastSlider?.addEventListener('input', applyImageFilters);
    saturationSlider?.addEventListener('input', applyImageFilters);

    // Event Listener for Reset Button
    resetAdjustmentsButton?.addEventListener('click', resetImageFilters);

    // Reset sliders when selection changes
    canvas.on('selection:created', function(e) {
        if (e.selected[0].type === 'image') {
            console.log("New image selected - resetting adjustment tracking");
            
            // Reset the original image reference when selecting a new image
            if (currentImageObject !== e.selected[0]) {
                originalImageElement = null;
                currentImageObject = e.selected[0];
                
                // Reset sliders to default values
                contrastSlider.value = 0;
                saturationSlider.value = 0;
                contrastValueDisplay.textContent = '0%';
                saturationValueDisplay.textContent = '0%';
            }
        }
    });
    
    canvas.on('selection:cleared', function() {
        // Clear all references when nothing is selected
        currentImageObject = null;
        originalImageElement = null;
        
        // Reset sliders
        contrastSlider.value = 0;
        saturationSlider.value = 0;
        contrastValueDisplay.textContent = '0%';
        saturationValueDisplay.textContent = '0%';
    });

    // Add Rectangle
    document.getElementById('add-rectangle')?.addEventListener('click', function() {
        const rect = new fabric.Rect({
            left: 100,
            top: 100,
            fill: '#3B82F6',
            width: 100,
            height: 80,
            objectCaching: false,
            stroke: '#2563EB',
            strokeWidth: 2,
            cornerColor: '#2563EB',
            cornerSize: 10,
            transparentCorners: false
        });
        
        canvas.add(rect);
        canvas.setActiveObject(rect);
        canvas.renderAll();
    });

    // Add Circle
    document.getElementById('add-circle')?.addEventListener('click', function() {
        const circle = new fabric.Circle({
            left: 250,
            top: 100,
            fill: '#10B981',
            radius: 50,
            objectCaching: false,
            stroke: '#059669',
            strokeWidth: 2,
            cornerColor: '#059669',
            cornerSize: 10,
            transparentCorners: false
        });
        
        canvas.add(circle);
        canvas.setActiveObject(circle);
        canvas.renderAll();
    });

    // Add Triangle
    document.getElementById('add-triangle')?.addEventListener('click', function() {
        const triangle = new fabric.Triangle({
            left: 400,
            top: 100,
            fill: '#F59E0B',
            width: 100,
            height: 100,
            objectCaching: false,
            stroke: '#D97706',
            strokeWidth: 2,
            cornerColor: '#D97706',
            cornerSize: 10,
            transparentCorners: false
        });
        
        canvas.add(triangle);
        canvas.setActiveObject(triangle);
        canvas.renderAll();
    });

    // Add Text
    document.getElementById('add-text')?.addEventListener('click', function() {
        const text = new fabric.IText('Edit Me', {
            left: 550,
            top: 100,
            fontFamily: 'Arial',
            fill: '#8B5CF6',
            fontSize: 30,
            fontWeight: 'bold',
            objectCaching: false,
            cornerColor: '#7C3AED',
            cornerSize: 10,
            transparentCorners: false
        });
        
        canvas.add(text);
        canvas.setActiveObject(text);
        canvas.renderAll();
    });

    // Image Upload
    document.getElementById('image-upload')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Display file name
        const imageNameElement = document.getElementById('image-name');
        if (imageNameElement) {
            imageNameElement.textContent = file.name;
        }
        
        // Clear previous original image references
        originalImageElement = null;
        
        // Read file as data URL
        const reader = new FileReader();
        reader.onload = function(event) {
            // Store the original data URL for reset purposes
            const originalDataUrl = event.target.result;
            
            const imgObj = new window.Image();
            imgObj.src = event.target.result;
            imgObj.onload = function() {
                // Create fabric image
                const img = new fabric.Image(imgObj, {
                    left: 300,
                    top: 300,
                    cornerColor: '#4F46E5',
                    cornerSize: 10,
                    transparentCorners: false
                });
                
                // Store the original source for reset functionality
                imgObj._originalSrc = originalDataUrl;
                img._originalSrc = originalDataUrl;
                
                // Create a separate original image element for filters
                originalImageElement = new Image();
                originalImageElement.src = originalDataUrl;
                originalImageElement.crossOrigin = "Anonymous"; 
                originalImageElement.width = imgObj.width;
                originalImageElement.height = imgObj.height;
                
                // Scale image to fit within canvas
                const maxWidth = 300;
                const maxHeight = 300;
                if (img.width > maxWidth || img.height > maxHeight) {
                    const scaleFactor = Math.min(
                        maxWidth / img.width,
                        maxHeight / img.height
                    );
                    img.scale(scaleFactor);
                }
                
                canvas.add(img);
                canvas.setActiveObject(img);
                canvas.renderAll();
                
                // Track this image as current
                currentImageObject = img;
                
                console.log("Image added with original source preserved");
                // Clear sliders
                contrastSlider.value = 0;
                saturationSlider.value = 0;
                contrastValueDisplay.textContent = '0%';
                saturationValueDisplay.textContent = '0%';
            };
        };
        reader.readAsDataURL(file);
    });

    // Delete Selected Object
    document.getElementById('delete-selected')?.addEventListener('click', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            canvas.remove(activeObject);
            canvas.renderAll();
        }
    });

    // Clear Canvas
    document.getElementById('clear-canvas')?.addEventListener('click', function() {
        canvas.clear();
        // Set the background color in a way compatible with all Fabric.js versions
        canvas.backgroundColor = '#f8f9fa';
        canvas.renderAll();
        
        // Reset adjustment variables when clearing
        currentImageObject = null;
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Delete' || e.key === 'Backspace') {
            const activeObject = canvas.getActiveObject();
            if (activeObject) {
                canvas.remove(activeObject);
                canvas.renderAll();
            }
        }
    });

    // ---- Object Selector Logic ----

    function updateObjectSelector() {
        if (!objectSelector) return;
        const objects = canvas.getObjects();
        const currentSelection = canvas.getActiveObject();
        let currentSelectionIndex = -1;
        
        // Find the index of the currently selected object
        if (currentSelection) {
            currentSelectionIndex = objects.findIndex(obj => obj === currentSelection);
        }

        // Clear existing options
        objectSelector.innerHTML = '<option value="">-- Select an object --</option>';

        // Populate with objects
        objects.forEach((obj, index) => {
            const option = document.createElement('option');
            option.value = index; // Use index as the value
            
            // Generate a descriptive name
            let name = obj.type || 'object';
            if (obj.type === 'i-text' && obj.text) {
                name = `Text: "${obj.text.substring(0, 15)}${obj.text.length > 15 ? '...' : '"'}`;
            } else if (obj.type === 'image') {
                name = `Image ${index + 1}`;
            } else {
                 name = `${name.charAt(0).toUpperCase() + name.slice(1)} ${index + 1}`;
            }
            option.textContent = name;
            
            // Mark the option as selected if it matches the current canvas selection
            if (index === currentSelectionIndex) {
                option.selected = true;
            }
            
            objectSelector.appendChild(option);
        });
    }

    // Update selector when objects are added or removed
    canvas.on('object:added', updateObjectSelector);
    canvas.on('object:removed', updateObjectSelector);
    
    // Handle selection change from the dropdown
    objectSelector?.addEventListener('change', function() {
        const selectedIndex = parseInt(this.value);
        if (!isNaN(selectedIndex) && selectedIndex >= 0) {
            const objects = canvas.getObjects();
            if (objects[selectedIndex]) {
                canvas.setActiveObject(objects[selectedIndex]);
                canvas.renderAll();
            }
        } else {
            canvas.discardActiveObject(); // Deselect if the default option is chosen
            canvas.renderAll();
        }
    });

    // Update selector when selection changes on the canvas
    canvas.on('selection:created', updateObjectSelector);
    canvas.on('selection:updated', updateObjectSelector);
    canvas.on('selection:cleared', updateObjectSelector);

    // Initialize the object selector (empty at first)
    updateObjectSelector();
    
    // Show a helpful message
    console.log("Fabric.js test initialized. Add shapes or upload an image, then use the selector dropdown to choose objects.");

    // ---- End Object Selector Logic ----
}); 