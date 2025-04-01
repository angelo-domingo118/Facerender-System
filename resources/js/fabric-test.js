import * as fabric from 'fabric';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Fabric.js canvas
    const canvas = new fabric.Canvas('fabric-canvas', {
        backgroundColor: '#f8f9fa',
        selection: true,
        preserveObjectStacking: true
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
        
        // Read file as data URL
        const reader = new FileReader();
        reader.onload = function(event) {
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
        canvas.setBackgroundColor('#f8f9fa', canvas.renderAll.bind(canvas));
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
}); 