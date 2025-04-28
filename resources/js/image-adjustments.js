/**
 * Image Adjustments with Fabric.js
 * This file contains algorithms for adjusting images on canvas
 */
import * as fabric from 'fabric';

class ImageAdjustments {
    constructor(canvas) {
        this.canvas = canvas;
        this.fabric = fabric; // Store fabric reference for later use
        this.originalImages = new Map(); // Track original images for reset
        this.initListeners();
        console.log('Image adjustments initialized with canvas:', canvas);
    }

    initListeners() {
        // Listen for both the regular and Livewire specific event formats
        window.addEventListener('livewire:layer-adjustments-updated', (event) => {
            console.log('Received livewire:layer-adjustments-updated event:', event.detail);
            this.handleAdjustmentUpdate(event.detail);
        });

        // Also listen for regular DOM events (non-Livewire)
        window.addEventListener('layer-adjustments-updated', (event) => {
            console.log('Received layer-adjustments-updated event:', event.detail);
            this.handleAdjustmentUpdate(event.detail);
        });

        // Livewire 3 specific event format
        document.addEventListener('livewire:initialized', () => {
            // Listen directly to Livewire events
            if (typeof window.Livewire !== 'undefined') {
                window.Livewire.on('layer-adjustments-updated', (data) => {
                    console.log('Received direct Livewire.on event:', data);
                    this.handleAdjustmentUpdate(data);
                });
                
                window.Livewire.on('reset-layer-adjustments', (data) => {
                    console.log('Received reset layer adjustments event:', data);
                    this.handleResetAdjustments(data);
                });
            }
        });
        
        // Listen for update-feature-adjustments from Livewire
        window.addEventListener('update-feature-adjustments', (event) => {
            console.log('Received update-feature-adjustments event:', event.detail);
            this.handleAdjustmentUpdate(event.detail);
        });
        
        // Listen for reset-layer-adjustments from Livewire
        window.addEventListener('reset-layer-adjustments', (event) => {
            console.log('Received reset-layer-adjustments event:', event.detail);
            this.handleResetAdjustments(event.detail);
        });
    }

    handleAdjustmentUpdate(data) {
        console.log('Processing adjustment update:', data);
        
        // Handle both direct data and array-wrapped data (common in Livewire events)
        if (Array.isArray(data) && data.length > 0) {
            data = data[0];
        }
        
        // Handle nested data structure (where data might be in data.data)
        if (data && data.data && (data.data.layerId || data.data.adjustments || data.data.featureId)) {
            data = data.data;
        }
        
        // Handle different property names (layerId/featureId)
        const layerId = data.layerId || data.featureId;
        const adjustments = data.adjustments;
        
        if (!layerId || !adjustments) {
            console.error('Invalid adjustment data received:', data);
            return;
        }
        
        console.log(`Applying adjustments to layer ${layerId}:`, adjustments);
        
        // Find the object on canvas with matching ID
        const targetObject = this.findObjectById(layerId);
        
        if (!targetObject) {
            console.error(`Object with ID ${layerId} not found on canvas`);
            return;
        }
        
        // Apply adjustments to the object
        this.applyDirectPixelManipulation(targetObject, adjustments);
    }
    
    handleResetAdjustments(data) {
        // Handle both direct data and array-wrapped data
        if (Array.isArray(data) && data.length > 0) {
            data = data[0];
        }
        
        // Handle nested data structure
        if (data && data.data) {
            data = data.data;
        }
        
        const layerId = data.layerId || data.featureId;
        
        if (!layerId) {
            console.error('Invalid reset data received:', data);
            return;
        }
        
        console.log(`Resetting adjustments for layer ${layerId}`);
        this.resetImageAdjustments(layerId);
    }
    
    findObjectById(id) {
        const objects = this.canvas.getObjects();
        console.log(`Searching for object with ID ${id} among ${objects.length} objects`);
        
        // First try to find object with data.featureId
        let foundObject = objects.find(obj => obj.data && obj.data.featureId === id);
        
        // If not found, try finding object by other ID properties
        if (!foundObject) {
            foundObject = objects.find(obj => 
                obj.id === id || 
                (obj.data && (obj.data.id === id || obj.data.layerId === id))
            );
        }
        
        console.log('Found object:', foundObject);
        return foundObject;
    }
    
    // Apply direct pixel manipulation instead of Fabric.js filters
    applyDirectPixelManipulation(imgObject, adjustments) {
        console.log("Direct pixel manipulation with:", adjustments);
        
        if (!imgObject || imgObject.type !== 'image') {
            console.error("Object is not an image that can be manipulated");
            return;
        }
        
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
            if (!this.originalImages.has(imgObject.data?.featureId)) {
                console.log("Capturing original image for the first time");
                
                // Save original source
                if (imgElement.src) {
                    // Create a new image from the original source
                    const originalImg = new Image();
                    originalImg.src = imgElement.src;
                    originalImg.crossOrigin = "Anonymous";
                    originalImg.width = imgElement.width;
                    originalImg.height = imgElement.height;
                    
                    // Store the original image for this object
                    this.originalImages.set(imgObject.data?.featureId, originalImg);
                    imgElement._originalSrc = imgElement.src;
                    console.log("Original image captured and stored");
                } else if (imgElement._originalElement && imgElement._originalElement.src) {
                    this.originalImages.set(imgObject.data?.featureId, imgElement._originalElement);
                    imgElement._originalSrc = imgElement._originalElement.src;
                    console.log("Original element source saved");
                } else {
                    console.warn("Could not find original source to save");
                }
            }
            
            // Get the original image
            const originalImage = this.originalImages.get(imgObject.data?.featureId) || imgElement;
            
            // Create a temporary canvas for manipulation
            const tempCanvas = document.createElement('canvas');
            // Use the dimensions from the original image if available, otherwise use current
            const sourceWidth = originalImage ? (originalImage.naturalWidth || originalImage.width) : (imgElement.naturalWidth || imgElement.width);
            const sourceHeight = originalImage ? (originalImage.naturalHeight || originalImage.height) : (imgElement.naturalHeight || imgElement.height);
            tempCanvas.width = sourceWidth;
            tempCanvas.height = sourceHeight;
            
            if (tempCanvas.width === 0 || tempCanvas.height === 0) {
                console.error("Invalid canvas dimensions:", tempCanvas.width, tempCanvas.height);
                throw new Error("Invalid canvas dimensions");
            }
            
            console.log("Created temp canvas with dimensions:", tempCanvas.width, "x", tempCanvas.height);
            
            const tempCtx = tempCanvas.getContext('2d', { willReadFrequently: true });
            
            // Draw the ORIGINAL image (not the filtered one)
            const sourceImage = originalImage || imgElement;
            console.log("Drawing from source:", sourceImage === originalImage ? "original stored image" : "current image");
            tempCtx.drawImage(sourceImage, 0, 0);
            console.log("Drew image to temp canvas");
            
            // Get the image data
            let imageData;
            try {
                imageData = tempCtx.getImageData(0, 0, tempCanvas.width, tempCanvas.height);
                console.log("Got image data:", imageData.width, "x", imageData.height);
            } catch (e) {
                console.error("Error getting image data:", e);
                throw new Error("Cannot get image data: " + e.message);
            }
            
            const data = imageData.data;
            
            // Get adjustment values - map from 0-100 scale to appropriate processing ranges
            const contrastValue = parseInt(adjustments.contrast); 
            const saturationValue = parseInt(adjustments.saturation);
            const sharpnessValue = parseInt(adjustments.sharpness);
            
            // Calculate adjustment factors - for 0 values, these should have no effect
            
            // Contrast: 0 = normal (1.0), negative = less contrast, positive = more contrast
            const contrastFactor = contrastValue === 0 ? 1.0 : 
                contrastValue > 0 
                    ? (259 * (contrastValue + 255)) / (255 * (259 - contrastValue))  // Positive values - full range
                    : 1.0 - (Math.abs(contrastValue) / 200);  // Negative values - gentler curve
            
            // Saturation: 0 = normal (1.0), negative = less saturation, positive = more saturation
            const saturationFactor = saturationValue === 0 ? 1.0 : 
                saturationValue > 0
                    ? (saturationValue + 100) / 100  // Positive values - full range 
                    : Math.max(0.2, 1.0 - (Math.abs(saturationValue) / 125));  // Negative values - limit to 0.2 min
            
            console.log("Processing adjustments - contrast:", contrastValue, "saturation:", saturationValue, "sharpness:", sharpnessValue);
            console.log("Calculated factors - contrastFactor:", contrastFactor, "saturationFactor:", saturationFactor);
            
            // Create a copy of the image data for sharpness processing (if needed)
            let sharpenedData = null;
            if (sharpnessValue > 0) {
                // Only create a copy and process if sharpness is positive
                sharpenedData = this.applySharpnessFilter(imageData, sharpnessValue);
            }
            
            // Process each pixel
            for (let i = 0; i < data.length; i += 4) {
                let r = data[i];
                let g = data[i + 1];
                let b = data[i + 2];
                
                // If we have sharpened data and sharpness is positive, blend with the sharpened version
                if (sharpenedData && sharpnessValue > 0) {
                    // Get sharpened pixel values
                    const sr = sharpenedData[i];
                    const sg = sharpenedData[i + 1];
                    const sb = sharpenedData[i + 2];
                    
                    // Calculate blend amount based on sharpness (0 to 1 range)
                    const blendAmount = Math.min(sharpnessValue / 100, 1);
                    
                    // Blend between original and sharpened
                    r = r * (1 - blendAmount) + sr * blendAmount;
                    g = g * (1 - blendAmount) + sg * blendAmount;
                    b = b * (1 - blendAmount) + sb * blendAmount;
                }
                
                // Apply contrast (only if contrast value is not 0)
                if (contrastValue !== 0) {
                    r = this.truncate((r - 128) * contrastFactor + 128);
                    g = this.truncate((g - 128) * contrastFactor + 128);
                    b = this.truncate((b - 128) * contrastFactor + 128);
                }
                
                // Apply saturation (only if saturation value is not 0)
                if (saturationValue !== 0) {
                    const gray = 0.2989 * r + 0.5870 * g + 0.1140 * b;
                    r = this.truncate(gray + saturationFactor * (r - gray));
                    g = this.truncate(gray + saturationFactor * (g - gray));
                    b = this.truncate(gray + saturationFactor * (b - gray));
                }
                
                // Store the processed values back to the image data
                data[i] = r;
                data[i + 1] = g;
                data[i + 2] = b;
            }
            
            // Put the modified image data back
            tempCtx.putImageData(imageData, 0, 0);
            console.log("Put modified image data back to canvas");
            
            // Create a new image from our canvas
            const newImgURL = tempCanvas.toDataURL();
            console.log("Created new image URL from canvas");
            
            // DIRECT APPROACH: Create a new image element and update the fabric object directly
            const newImg = new Image();
            newImg.onload = () => {
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
                this.canvas.renderAll();
                
                console.log("Image updated directly with filtered version");
            };
            
            // Set the source of the new image to our filtered canvas
            newImg.src = newImgURL;
            
            // If the image is already loaded, manually trigger the onload handler
            if (newImg.complete) {
                console.log("New image already loaded, triggering handler manually");
                newImg.onload();
            }
            
            // Store adjustment values on the object for future reference
            if (!imgObject.data) {
                imgObject.data = {};
            }
            imgObject.data.adjustments = adjustments;
            
        } catch (error) {
            console.error("Error in direct pixel manipulation:", error);
            console.error(error.stack);
        }
    }
    
    // Apply sharpness filter using convolution
    applySharpnessFilter(originalImageData, sharpnessValue) {
        // Skip processing if sharpness is zero
        if (sharpnessValue <= 0) {
            return originalImageData.data.slice();
        }
        
        const width = originalImageData.width;
        const height = originalImageData.height;
        const src = originalImageData.data;
        
        // Create output array for the result
        const dst = new Uint8ClampedArray(src.length);
        
        // Use the same 3x3 kernel as in image-adjustments.blade.php
        const weights = [
            0, -1, 0,
            -1, 5, -1,
            0, -1, 0
        ];
        
        const side = 3; // 3x3 kernel
        const halfSide = Math.floor(side / 2);
        
        // Calculate alpha factor for blending between original and sharpened
        // Map from 0-100 to 0-1 range for blending factor
        const alphaFactor = sharpnessValue / 100;
        
        console.log("Applying sharpness with alpha factor:", alphaFactor);
        
        // Apply convolution for each pixel
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                const dstOff = (y * width + x) * 4;
                
                // Process RGB channels with convolution
                let r = 0, g = 0, b = 0;
                
                for (let cy = 0; cy < side; cy++) {
                    for (let cx = 0; cx < side; cx++) {
                        const scy = y + cy - halfSide;
                        const scx = x + cx - halfSide;
                        
                        // Skip pixels outside the image boundary
                        if (scy >= 0 && scy < height && scx >= 0 && scx < width) {
                            const srcOff = (scy * width + scx) * 4;
                            const wt = weights[cy * side + cx];
                            r += src[srcOff] * wt;
                            g += src[srcOff + 1] * wt;
                            b += src[srcOff + 2] * wt;
                        }
                    }
                }
                
                // Apply the convolution result with alpha blending
                dst[dstOff] = this.truncate(src[dstOff] * (1 - alphaFactor) + r * alphaFactor);
                dst[dstOff + 1] = this.truncate(src[dstOff + 1] * (1 - alphaFactor) + g * alphaFactor);
                dst[dstOff + 2] = this.truncate(src[dstOff + 2] * (1 - alphaFactor) + b * alphaFactor);
                dst[dstOff + 3] = src[dstOff + 3]; // Keep original alpha
            }
        }
        
        return dst;
    }
    
    // Helper function to reset filters
    resetImageAdjustments(layerId) {
        const imgObject = this.findObjectById(layerId);
        
        if (!imgObject || imgObject.type !== 'image') {
            console.error("Cannot reset - object not found or not an image");
            return;
        }
        
        console.log("Resetting filters for image:", imgObject);
        
        // If we have the original image element stored, use it directly
        if (this.originalImages.has(imgObject.data?.featureId)) {
            console.log("Resetting using stored original image element");
            
            const originalImage = this.originalImages.get(imgObject.data?.featureId);
            
            // Create a fresh image
            const freshImg = new Image();
            freshImg.src = originalImage.src;
            freshImg.onload = () => {
                // Store the old image's properties
                const oldLeft = imgObject.left;
                const oldTop = imgObject.top;
                const oldScaleX = imgObject.scaleX;
                const oldScaleY = imgObject.scaleY;
                const oldAngle = imgObject.angle;
                
                // Update the fabric object with our fresh image
                imgObject.setElement(freshImg);
                
                // Ensure the position and scale are maintained
                imgObject.set({
                    left: oldLeft,
                    top: oldTop,
                    scaleX: oldScaleX,
                    scaleY: oldScaleY,
                    angle: oldAngle
                });
                
                // Mark the object as dirty to force re-render
                imgObject.dirty = true;
                
                // Re-render the canvas
                this.canvas.renderAll();
                
                console.log("Image reset to original using stored element");
            };
            
            if (freshImg.complete) {
                console.log("Fresh image already loaded, triggering onload");
                freshImg.onload();
            }
            return;
        }
        
        console.warn("Cannot reset - no original image available");
    }
    
    // Helper function to ensure pixel values are in valid range
    truncate(value) {
        return Math.min(255, Math.max(0, value));
    }
}

// Initialize the adjustments when the canvas is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM content loaded, waiting for canvas initialization');
    
    // Wait for Livewire and canvas to be fully initialized
    window.addEventListener('canvas:initialized', (event) => {
        console.log('Canvas initialized event received:', event.detail);
        if (event.detail && event.detail.canvas) {
            const canvas = event.detail.canvas;
            const fabricInstance = event.detail.fabric || fabric || window.fabric;
            
            if (!fabricInstance) {
                console.error('No Fabric.js instance found!');
                return;
            }
            
            // Make fabric globally available if it's not already
            if (!window.fabric) {
                window.fabric = fabricInstance;
            }
            
            window.imageAdjustments = new ImageAdjustments(canvas);
            console.log('ImageAdjustments instance created and attached to window');
        } else {
            console.error('Canvas initialized event missing canvas object');
        }
    });
});

export default ImageAdjustments; 