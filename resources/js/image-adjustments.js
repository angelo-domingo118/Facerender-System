/**
 * Image Adjustments with Fabric.js
 * This file contains algorithms for adjusting images on canvas
 */
import * as fabric from 'fabric';

class ImageAdjustments {
    constructor(canvas) {
        this.canvas = canvas;
        this.fabric = fabric; // Store fabric reference for later use
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
            }
        });
    }

    handleAdjustmentUpdate(data) {
        console.log('Processing adjustment update:', data);
        
        // Handle both direct data and array-wrapped data (common in Livewire events)
        if (Array.isArray(data) && data.length > 0) {
            data = data[0];
        }
        
        // Handle nested data structure (where data might be in data.data)
        if (data && data.data && (data.data.layerId || data.data.adjustments)) {
            data = data.data;
        }
        
        if (!data || !data.layerId || !data.adjustments) {
            console.error('Invalid adjustment data received:', data);
            return;
        }

        const layerId = data.layerId;
        const adjustments = data.adjustments;
        
        console.log(`Applying adjustments to layer ${layerId}:`, adjustments);
        
        // Find the object on canvas with matching ID
        const targetObject = this.findObjectById(layerId);
        
        if (!targetObject) {
            console.error(`Object with ID ${layerId} not found on canvas`);
            return;
        }
        
        // Apply adjustments to the object
        this.applyAdjustments(targetObject, adjustments);
    }
    
    findObjectById(id) {
        const objects = this.canvas.getObjects();
        console.log(`Searching for object with ID ${id} among ${objects.length} objects`);
        
        // Debug all objects to check their data structure
        objects.forEach((obj, index) => {
            console.log(`Object ${index}:`, obj.data);
        });
        
        const foundObject = objects.find(obj => obj.data && obj.data.featureId === id);
        console.log('Found object:', foundObject);
        return foundObject;
    }
    
    applyAdjustments(object, adjustments) {
        console.log('Applying adjustments to object:', object);
        
        // Check if this is a Fabric.js image object
        if (!object.filters && object.type !== 'image') {
            console.error('Object is not an image that supports filters:', object);
            return;
        }
        
        // For Fabric.js images
        if (object.type === 'image') {
            // Initialize filters array if needed
            if (!object.filters) {
                object.filters = [];
            }
            
            // We'll focus on contrast for this implementation
            const contrastValue = this.calculateContrastValue(adjustments.contrast);
            console.log(`Calculated contrast value: ${contrastValue} from slider value: ${adjustments.contrast}`);
            
            try {
                // Debug Fabric.js availability
                console.log('Fabric global:', window.fabric);
                console.log('this.fabric:', this.fabric);
                console.log('fabric from import:', fabric);
                
                // Remove any existing contrast filters to avoid stacking
                object.filters = object.filters.filter(filter => 
                    filter.type !== 'Contrast' && 
                    filter.constructor.name !== 'Contrast'
                );
                
                // Create a contrast filter directly
                // Use a more direct approach that doesn't rely on fabric.Image
                let contrastFilter;
                
                // Try different ways to access the Contrast filter constructor
                if (fabric && fabric.Image && fabric.Image.filters && fabric.Image.filters.Contrast) {
                    console.log('Using imported fabric');
                    contrastFilter = new fabric.Image.filters.Contrast({
                        contrast: contrastValue
                    });
                } else if (window.fabric && window.fabric.Image && window.fabric.Image.filters && window.fabric.Image.filters.Contrast) {
                    console.log('Using global window.fabric');
                    contrastFilter = new window.fabric.Image.filters.Contrast({
                        contrast: contrastValue
                    });
                } else {
                    // As a fallback, create a basic filter
                    console.log('Using fallback filter implementation');
                    contrastFilter = {
                        type: 'Contrast',
                        contrast: contrastValue,
                        applyTo: function(canvasEl) {
                            const context = canvasEl.getContext('2d');
                            const imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height);
                            const data = imageData.data;
                            const contrast = 1 + this.contrast;
                            const intercept = 128 * (1 - contrast);
                            
                            for (let i = 0; i < data.length; i += 4) {
                                data[i] = data[i] * contrast + intercept;
                                data[i + 1] = data[i + 1] * contrast + intercept;
                                data[i + 2] = data[i + 2] * contrast + intercept;
                            }
                            
                            context.putImageData(imageData, 0, 0);
                        }
                    };
                }
                
                object.filters.push(contrastFilter);
                console.log('Added contrast filter to object:', contrastFilter);
                
                // Apply filters and render
                console.log('Applying filters to object');
                object.applyFilters();
                this.canvas.renderAll();
                console.log('Applied filters and rendered canvas');
                
                // Store adjustment values on the object for future reference
                if (!object.data) {
                    object.data = {};
                }
                object.data.adjustments = adjustments;
                
                console.log(`Successfully applied contrast ${contrastValue} to object ${object.data.featureId}`);
            } catch (error) {
                console.error('Error applying contrast filter:', error);
                // Log the full stack trace for debugging
                console.error(error.stack);
            }
        } else {
            console.warn('Object is not an image, cannot apply filter adjustments');
        }
    }
    
    /**
     * Calculate contrast value from the 0-100 scale to Fabric.js scale (-1 to 1)
     * 
     * This is our custom algorithm:
     * - 50 is neutral (no contrast change)
     * - 0 to 50 reduces contrast (maps to -1 to 0)
     * - 50 to 100 increases contrast (maps to 0 to 1)
     */
    calculateContrastValue(contrast) {
        // Convert from 0-100 scale to -1 to 1 scale 
        // where 50 is the neutral point (0)
        if (contrast === 50) {
            return 0; // Neutral - no contrast change
        } else if (contrast < 50) {
            // Map 0-50 to -1-0 (decrease contrast)
            return -1 + (contrast / 50);
        } else {
            // Map 50-100 to 0-1 (increase contrast)
            return (contrast - 50) / 50;
        }
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