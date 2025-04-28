<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Image Adjustment Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .upload-container {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .upload-container:hover {
            border-color: #aaa;
        }
        .controls-container {
            margin-bottom: 20px;
        }
        .slider-container {
            margin-bottom: 15px;
        }
        .slider-container label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        .slider-container input {
            width: 300px;
            vertical-align: middle;
        }
        .slider-container span {
            display: inline-block;
            width: 40px;
            text-align: right;
            margin-left: 10px;
        }
        .image-container {
            text-align: center;
            margin-top: 20px;
        }
        #preview-image {
            max-width: 100%;
            display: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .reset-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .reset-button:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
        }
        .nav-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563EB;
            font-weight: bold;
        }
        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Image Adjustment Test Page</h1>
    
    <div class="upload-container" id="upload-area">
        <p>Click here or drag and drop an image to upload</p>
        <input type="file" id="file-input" accept="image/*" class="hidden">
    </div>
    
    <div class="controls-container" id="controls" style="display: none;">
        <div class="slider-container">
            <label for="contrast">Contrast:</label>
            <input type="range" id="contrast" min="0" max="200" value="100" step="1">
            <span id="contrast-value">100%</span>
        </div>
        
        <div class="slider-container">
            <label for="saturation">Saturation:</label>
            <input type="range" id="saturation" min="0" max="200" value="100" step="1">
            <span id="saturation-value">100%</span>
        </div>
        
        <div class="slider-container">
            <label for="sharpness">Sharpness:</label>
            <input type="range" id="sharpness" min="0" max="500" value="0" step="1">
            <span id="sharpness-value">0%</span>
        </div>
        
        <button class="reset-button" id="reset-button">Reset All</button>
    </div>
    
    <div class="image-container">
        <canvas id="preview-canvas" style="display: none;"></canvas>
        <img id="preview-image" alt="Preview">
    </div>
    
    <a href="{{ route('dashboard') }}" class="nav-link">← Back to Dashboard</a>
    
    <script>
        // DOM elements
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file-input');
        const controls = document.getElementById('controls');
        const previewImage = document.getElementById('preview-image');
        const previewCanvas = document.getElementById('preview-canvas');
        const ctx = previewCanvas.getContext('2d');
        
        // Sliders
        const contrastSlider = document.getElementById('contrast');
        const saturationSlider = document.getElementById('saturation');
        const sharpnessSlider = document.getElementById('sharpness');
        
        // Value displays
        const contrastValue = document.getElementById('contrast-value');
        const saturationValue = document.getElementById('saturation-value');
        const sharpnessValue = document.getElementById('sharpness-value');
        
        // Reset button
        const resetButton = document.getElementById('reset-button');
        
        // Original image for reset functionality
        let originalImage = null;
        
        // Event listeners
        uploadArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFileSelect);
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('drop', handleDrop);
        
        contrastSlider.addEventListener('input', applyFilters);
        saturationSlider.addEventListener('input', applyFilters);
        sharpnessSlider.addEventListener('input', applyFilters);
        
        resetButton.addEventListener('click', resetFilters);
        
        // File handling functions
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && file.type.match('image.*')) {
                processImage(file);
            }
        }
        
        function handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            uploadArea.style.borderColor = '#45a049';
        }
        
        function handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            uploadArea.style.borderColor = '#ccc';
            
            const file = event.dataTransfer.files[0];
            if (file && file.type.match('image.*')) {
                processImage(file);
            }
        }
        
        function processImage(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                originalImage = new Image();
                originalImage.onload = function() {
                    // Set canvas dimensions to match image
                    previewCanvas.width = originalImage.width;
                    previewCanvas.height = originalImage.height;
                    
                    // Show controls
                    controls.style.display = 'block';
                    
                    // Draw the original image
                    ctx.drawImage(originalImage, 0, 0);
                    
                    // Show the preview image
                    previewImage.src = previewCanvas.toDataURL();
                    previewImage.style.display = 'inline-block';
                    
                    // Apply filters with default values
                    applyFilters();
                };
                originalImage.src = e.target.result;
            };
            
            reader.readAsDataURL(file);
        }
        
        // Filter application
        function applyFilters() {
            if (!originalImage) return;
            
            // Get current values
            const contrast = contrastSlider.value;
            const saturation = saturationSlider.value;
            const sharpness = sharpnessSlider.value;
            
            // Update display values
            contrastValue.textContent = `${contrast}%`;
            saturationValue.textContent = `${saturation}%`;
            sharpnessValue.textContent = `${sharpness}%`;
            
            // Clear canvas
            ctx.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
            
            // Draw the original image
            ctx.drawImage(originalImage, 0, 0);
            
            // Get image data for manipulation
            const imageData = ctx.getImageData(0, 0, previewCanvas.width, previewCanvas.height);
            const data = imageData.data;
            
            // Apply contrast
            const factor = (259 * (contrast - 0) + 255) / (255 * (259 - contrast));
            
            // Apply saturation
            const saturationFactor = saturation / 100;
            
            for (let i = 0; i < data.length; i += 4) {
                // Apply contrast
                data[i] = truncate(factor * (data[i] - 128) + 128);
                data[i + 1] = truncate(factor * (data[i + 1] - 128) + 128);
                data[i + 2] = truncate(factor * (data[i + 2] - 128) + 128);
                
                // Apply saturation
                const gray = 0.2989 * data[i] + 0.5870 * data[i + 1] + 0.1140 * data[i + 2];
                data[i] = truncate(gray + saturationFactor * (data[i] - gray));
                data[i + 1] = truncate(gray + saturationFactor * (data[i + 1] - gray));
                data[i + 2] = truncate(gray + saturationFactor * (data[i + 2] - gray));
            }
            
            // Put the manipulated image data back
            ctx.putImageData(imageData, 0, 0);
            
            // Apply sharpness if needed
            if (sharpness > 0) {
                applySharpness(sharpness);
            }
            
            // Update preview image
            previewImage.src = previewCanvas.toDataURL();
        }
        
        function applySharpness(amount) {
            const weights = [
                0, -1, 0,
                -1, 5, -1,
                0, -1, 0
            ];
            
            const side = Math.round(Math.sqrt(weights.length));
            const halfSide = Math.floor(side / 2);
            
            const srcData = ctx.getImageData(0, 0, previewCanvas.width, previewCanvas.height);
            const src = srcData.data;
            const sw = srcData.width;
            const sh = srcData.height;
            
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = sw;
            tempCanvas.height = sh;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.putImageData(srcData, 0, 0);
            
            const output = ctx.createImageData(sw, sh);
            const dst = output.data;
            
            // Apply convolution for sharpening
            const alphaFactor = amount / 100;
            
            for (let y = 0; y < sh; y++) {
                for (let x = 0; x < sw; x++) {
                    const sy = y;
                    const sx = x;
                    const dstOff = (y * sw + x) * 4;
                    
                    let r = 0, g = 0, b = 0;
                    
                    for (let cy = 0; cy < side; cy++) {
                        for (let cx = 0; cx < side; cx++) {
                            const scy = sy + cy - halfSide;
                            const scx = sx + cx - halfSide;
                            
                            if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {
                                const srcOff = (scy * sw + scx) * 4;
                                const wt = weights[cy * side + cx];
                                r += src[srcOff] * wt;
                                g += src[srcOff + 1] * wt;
                                b += src[srcOff + 2] * wt;
                            }
                        }
                    }
                    
                    // Apply the convolution result with alpha blending
                    dst[dstOff] = truncate(src[dstOff] * (1 - alphaFactor) + r * alphaFactor);
                    dst[dstOff + 1] = truncate(src[dstOff + 1] * (1 - alphaFactor) + g * alphaFactor);
                    dst[dstOff + 2] = truncate(src[dstOff + 2] * (1 - alphaFactor) + b * alphaFactor);
                    dst[dstOff + 3] = src[dstOff + 3]; // Keep original alpha
                }
            }
            
            ctx.putImageData(output, 0, 0);
        }
        
        // Helper functions
        function truncate(value) {
            return Math.min(255, Math.max(0, value));
        }
        
        function resetFilters() {
            contrastSlider.value = 100;
            saturationSlider.value = 100;
            sharpnessSlider.value = 0;
            
            applyFilters();
        }
    </script>
</body>
</html>
