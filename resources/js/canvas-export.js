/**
 * Canvas Export Functionality
 * Implements print and download for the Fabric.js canvas
 */

// Add a global flag to control console logging
const DEBUG_LOGS = false;

document.addEventListener('DOMContentLoaded', function() {
    if (DEBUG_LOGS) {
        console.log('Canvas export module loaded');
    } else {
        // Keep a minimal console log for important events
        console.log('Canvas export module loaded');
    }
    
    // Wait until the canvas is ready
    window.addEventListener('canvas:initialized', function(event) {
        const canvas = event.detail.canvas;
        if (DEBUG_LOGS) {
            console.log('Canvas export module detected initialized canvas:', canvas);
        }
        
        // Set up print button
        const printButton = document.getElementById('print-canvas-btn');
        if (printButton) {
            if (DEBUG_LOGS) {
                console.log('Print button found, attaching event listener');
            }
            printButton.addEventListener('click', function() {
                printCanvas(canvas);
            });
        } else {
            console.warn('Print button not found with ID: print-canvas-btn');
            // Try with the tooltip selector as fallback
            const fallbackPrintButton = document.querySelector('button[x-tooltip="\'Print (Ctrl+P)\'"]');
            if (fallbackPrintButton) {
                console.log('Print button found using fallback selector, attaching event listener');
                fallbackPrintButton.addEventListener('click', function() {
                    printCanvas(canvas);
                });
            }
        }
        
        // Set up download button
        const downloadButton = document.getElementById('download-canvas-btn');
        if (downloadButton) {
            console.log('Download button found, attaching event listener');
            downloadButton.addEventListener('click', function() {
                showExportModal(canvas);
            });
        } else {
            console.warn('Download button not found with ID: download-canvas-btn');
            // Try with the tooltip selector as fallback
            const fallbackDownloadButton = document.querySelector('button[x-tooltip="\'Download\'"]');
            if (fallbackDownloadButton) {
                console.log('Download button found using fallback selector, attaching event listener');
                fallbackDownloadButton.addEventListener('click', function() {
                    showExportModal(canvas);
                });
            }
        }
        
        // Set up export modal controls
        setupExportModalControls(canvas);
    });
});

/**
 * Set up the export modal controls
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function setupExportModalControls(canvas) {
    // Get modal elements
    const modal = document.getElementById('export-options-modal');
    const closeBtn = document.getElementById('close-export-modal');
    const cancelBtn = document.getElementById('cancel-export');
    const confirmBtn = document.getElementById('confirm-export');
    const fileFormatInputs = document.querySelectorAll('input[name="file-format"]');
    const transparencyOption = document.getElementById('transparency-option');
    const qualityOption = document.getElementById('quality-option');
    const qualitySlider = document.getElementById('quality-slider');
    const qualityValue = document.getElementById('quality-value');
    
    if (!modal) {
        console.error('Export modal not found in the DOM');
        return;
    }
    
    // Close modal functions
    const closeModal = () => {
        modal.classList.add('hidden');
    };
    
    // Close modal event listeners
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    // Toggle options based on file format
    fileFormatInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'png') {
                transparencyOption.classList.remove('hidden');
                qualityOption.classList.add('hidden');
            } else {
                transparencyOption.classList.add('hidden');
                qualityOption.classList.remove('hidden');
            }
        });
    });
    
    // Update quality value
    if (qualitySlider && qualityValue) {
        qualitySlider.addEventListener('input', function() {
            qualityValue.textContent = this.value;
        });
    }
    
    // Handle export confirmation
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            // Get selected options
            const fileFormat = document.querySelector('input[name="file-format"]:checked').value;
            const keepTransparency = document.querySelector('input[name="transparency"]:checked').value === 'keep';
            const showGrid = document.querySelector('input[name="grid"]:checked').value === 'show';
            const quality = parseInt(document.getElementById('quality-slider').value) / 100;
            const exportSize = parseFloat(document.getElementById('export-size').value);
            
            // Perform export with options
            exportCanvas(canvas, {
                format: fileFormat,
                quality: quality,
                multiplier: exportSize,
                keepTransparency: keepTransparency,
                showGrid: showGrid
            });
            
            // Close modal
            closeModal();
        });
    }
}

/**
 * Show the export options modal
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function showExportModal(canvas) {
    const modal = document.getElementById('export-options-modal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error('Export modal not found, falling back to direct download');
        downloadCanvas(canvas);
    }
}

/**
 * Export the canvas with the specified options
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 * @param {Object} options - Export options
 */
function exportCanvas(canvas, options) {
    try {
        console.log('Export canvas with options:', options);
        
        if (!canvas) {
            console.error('Cannot export - canvas is not defined');
            return;
        }
        
        // Save the current state of the canvas
        const originalObjects = [...canvas.getObjects()];
        const gridLines = originalObjects.filter(obj => 
            obj.type === 'line' && 
            obj.stroke === '#e5e7eb' && 
            !obj.selectable && 
            !obj.evented
        );
        
        // If we need to hide the grid, temporarily remove grid lines
        if (!options.showGrid && gridLines.length > 0) {
            console.log(`Temporarily hiding ${gridLines.length} grid lines for export`);
            gridLines.forEach(line => canvas.remove(line));
        }
        
        // Set background color for PNG transparency removal if needed
        const originalBackgroundColor = canvas.backgroundColor;
        if (options.format === 'png') {
            if (options.keepTransparency) {
                // Set background to transparent for PNG export
                canvas.backgroundColor = null; // or '' could also work
                console.log('Setting canvas background to transparent for PNG export');
            } else {
                // Set background to white if transparency is not kept
                canvas.backgroundColor = '#ffffff';
                console.log('Setting canvas background to white for PNG export');
            }
        } else {
            // For JPEG, always use a white background
            canvas.backgroundColor = '#ffffff';
            console.log('Setting canvas background to white for JPEG export');
        }
        
        // Force canvas re-rendering with the potential new background color
        canvas.renderAll();
        
        // Get composite title if available for the filename
        let filenameBase = 'canvas-export';
        const titleElement = document.querySelector('.composite-title, h2[title]');
        if (titleElement) {
            const rawTitle = titleElement.textContent || '';
            // Clean up the title for a filename
            filenameBase = rawTitle.trim()
                .replace(/[^a-z0-9]/gi, '-') // Replace non-alphanumeric with dash
                .replace(/-+/g, '-')        // Replace multiple dashes with single
                .toLowerCase();
        }
        
        // Generate a filename with date
        const now = new Date();
        const timestamp = now.toISOString().replace(/[:.]/g, '-').substring(0, 19);
        const filename = `${filenameBase}-${timestamp}.${options.format}`;
        
        // Create a data URL of the canvas
        const dataUrl = canvas.toDataURL({
            format: options.format,
            quality: options.quality,
            multiplier: options.multiplier
        });
        
        // Create a temporary link element and trigger the download
        const link = document.createElement('a');
        link.download = filename;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('Canvas exported successfully as:', filename);
        
        // Restore canvas state
        if (!options.showGrid && gridLines.length > 0) {
            console.log('Restoring grid lines after export');
            gridLines.forEach(line => canvas.add(line));
            // Send grid lines to back
            gridLines.forEach(line => canvas.sendObjectToBack(line));
        }
        
        // Restore original background color
        canvas.backgroundColor = originalBackgroundColor;
        console.log('Restored original canvas background color:', originalBackgroundColor);
        
        // Re-render the canvas
        canvas.renderAll();
        
    } catch (error) {
        console.error('Error during canvas export:', error);
        alert('Failed to export the canvas. Please try again.');
    }
}

/**
 * Print the canvas content, including composite details
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
async function printCanvas(canvas) {
    try {
        console.log('Print canvas function called');
        
        // Check if canvas exists
        if (!canvas) {
            console.error('Cannot print - canvas is not defined');
            return;
        }
        
        let compositeDetails = {};
        let detailsFound = false;
        
        // Using Livewire 3 approach to find components and call methods
        if (typeof Livewire !== 'undefined') {
            console.log('Searching for CompositeDetailsPanel component...');
            
            // First approach - Find by DOM element with specific key pattern for CompositeDetailsPanel
            const detailsPanelElement = document.querySelector('[wire\\:key^="details-panel-"]');
            if (detailsPanelElement) {
                try {
                    const wireId = detailsPanelElement.getAttribute('wire:id');
                    if (wireId) {
                        console.log('Found CompositeDetailsPanel with wire:id:', wireId);
                        
                        // In Livewire 3, we might need to use the component with the specific ID
                        const component = Livewire.find(wireId);
                        if (component) {
                            console.log('Found Livewire component, attempting to get details from CompositeDetailsPanel');
                            
                            try {
                                // Try with the correct method name
                                const details = await component.call('getCompositeDetailsForPrint');
                                console.log('Successfully fetched composite details from CompositeDetailsPanel:', details);
                                
                                if (details && Object.keys(details).length > 0) {
                                    compositeDetails = details;
                                    detailsFound = true;
                                }
                            } catch (error) {
                                console.error('Error calling getCompositeDetailsForPrint on CompositeDetailsPanel:', error);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error accessing CompositeDetailsPanel component:', error);
                }
            }
            
            // If we still don't have details (e.g. call failed or component not found by key), try to extract data directly from the form fields
            if (!detailsFound) {
                console.log('Attempting to extract form data directly from the DOM as a fallback...');
                try {
                    const title = document.querySelector('#title')?.value || 
                                     document.querySelector('input[wire\\:model\\.live="title"]')?.value;
                    
                    const description = document.querySelector('#description')?.value || 
                                       document.querySelector('textarea[wire\\:model\\.live="description"]')?.value;
                    
                    const witnessDropdown = document.querySelector('#witness');
                    let witnessName = null;
                    if (witnessDropdown && witnessDropdown.value) {
                        const selectedOption = witnessDropdown.options[witnessDropdown.selectedIndex];
                        if (selectedOption) {
                            witnessName = selectedOption.text;
                        }
                    }

                    const suspectGender = document.querySelector('#gender')?.value || 
                                          document.querySelector('select[wire\\:model\\.live="suspectGender"]')?.value;
                    
                    const suspectEthnicity = document.querySelector('#ethnicity')?.value || 
                                            document.querySelector('input[wire\\:model\\.live="suspectEthnicity"]')?.value;
                    
                    const suspectAgeRange = document.querySelector('#age-range')?.value || 
                                           document.querySelector('input[wire\\:model\\.live="suspectAgeRange"]')?.value;
                    
                    const suspectHeight = document.querySelector('#height')?.value || 
                                         document.querySelector('input[wire\\:model\\.live="suspectHeight"]')?.value;
                    
                    const suspectBodyBuild = document.querySelector('#body-build')?.value || 
                                            document.querySelector('select[wire\\:model\\.live="suspectBodyBuild"]')?.value;
                    
                    const suspectAdditionalNotes = document.querySelector('#additional-notes')?.value || 
                                                  document.querySelector('textarea[wire\\:model\\.live="suspectAdditionalNotes"]')?.value;
                    
                    // Fallback for created_at and id if not available from Livewire call
                    const createdAt = compositeDetails.created_at; // Preserve if already set by a partial success from another method
                    const compositeId = compositeDetails.id;

                    if (title || description || witnessName) { // Check if any primary detail is found
                        compositeDetails = {
                            title: title || compositeDetails.title, // Prioritize new over potentially stale
                            description: description || compositeDetails.description,
                            witness_name: witnessName || compositeDetails.witness_name,
                            suspect_gender: suspectGender || compositeDetails.suspect_gender,
                            suspect_ethnicity: suspectEthnicity || compositeDetails.suspect_ethnicity,
                            suspect_age_range: suspectAgeRange || compositeDetails.suspect_age_range,
                            suspect_height: suspectHeight || compositeDetails.suspect_height,
                            suspect_body_build: suspectBodyBuild || compositeDetails.suspect_body_build,
                            suspect_additional_notes: suspectAdditionalNotes || compositeDetails.suspect_additional_notes,
                            created_at: createdAt, // Carry over
                            id: compositeId // Carry over
                        };
                        
                        console.log('Successfully extracted/merged form data from DOM:', compositeDetails);
                            detailsFound = true;
                        }
                    } catch (error) {
                    console.error('Error extracting form data from DOM:', error);
                }
            }
            
            // Try alternative approach with Livewire.all() if still no details
            // This approach might be less reliable for getting all formatted data like witness_name
            if (!detailsFound && typeof Livewire.all === 'function') {
                console.log('Attempting to find component using Livewire.all() as a further fallback...');
                try {
                    const components = Livewire.all();
                    let detailsComponent = null;
                    
                    for (const component of components) {
                        const componentName = component.name || 
                                           (component.fingerprint && component.fingerprint.name) || 
                                           (component.fingerprint && component.fingerprint.method) || 
                                           '';
                        if (componentName.includes('composite-details-panel')) {
                            detailsComponent = component;
                            break;
                        }
                    }
                    
                    if (detailsComponent) {
                        console.log('Found CompositeDetailsPanel via Livewire.all(), attempting to get details via call or properties');
                        try {
                             // Attempt to call the method first on this found component
                            const details = await detailsComponent.call('getCompositeDetailsForPrint');
                            console.log('Successfully fetched details via Livewire.all() component call:', details);
                            if (details && Object.keys(details).length > 0) {
                                compositeDetails = details;
                                detailsFound = true;
                            }
                        } catch (callError) {
                            console.warn('Call to getCompositeDetailsForPrint failed on component from Livewire.all():', callError);
                            console.log('Falling back to direct property access from Livewire.all() component.');
                            // Fallback to accessing properties directly if call fails
                            if (typeof detailsComponent.$wire !== 'undefined') {
                                compositeDetails = {
                                    title: detailsComponent.$wire.get('title') || compositeDetails.title,
                                    description: detailsComponent.$wire.get('description') || compositeDetails.description,
                                    // witness_name not directly available, witnessId would be
                                    suspect_gender: detailsComponent.$wire.get('suspectGender') || compositeDetails.suspect_gender,
                                    suspect_ethnicity: detailsComponent.$wire.get('suspectEthnicity') || compositeDetails.suspect_ethnicity,
                                    suspect_age_range: detailsComponent.$wire.get('suspectAgeRange') || compositeDetails.suspect_age_range,
                                    suspect_height: detailsComponent.$wire.get('suspectHeight') || compositeDetails.suspect_height,
                                    suspect_body_build: detailsComponent.$wire.get('suspectBodyBuild') || compositeDetails.suspect_body_build,
                                    suspect_additional_notes: detailsComponent.$wire.get('suspectAdditionalNotes') || compositeDetails.suspect_additional_notes,
                                    created_at: compositeDetails.created_at, // Preserve if set
                                    id: compositeDetails.id // Preserve if set
                                };
                                detailsFound = Object.values(compositeDetails).some(v => v !== undefined && v !== null);
                            } else {
                                // Livewire 2 style direct property access (less likely for recent versions)
                                compositeDetails = {
                                    title: detailsComponent.title || compositeDetails.title,
                                    description: detailsComponent.description || compositeDetails.description,
                                    suspect_gender: detailsComponent.suspectGender || compositeDetails.suspect_gender,
                                    // ... and so on for other properties
                                    created_at: compositeDetails.created_at,
                                    id: compositeDetails.id
                                };
                                detailsFound = Object.values(compositeDetails).some(v => v !== undefined && v !== null);
                            }
                            if(detailsFound) console.log('Extracted component data from Livewire.all() properties:', compositeDetails);
                        }
                    }
                } catch (error) {
                    console.error('Error finding components using Livewire.all():', error);
                }
            }
        }
        
        // If we still don't have details, get basic info from DOM
        if (!detailsFound) {
            console.warn('Could not find any Livewire components with data. Using DOM fallback.');
            
            // Try to get basic information from the DOM as fallback
            const titleElement = document.querySelector('h2[title]');
            const idElement = document.querySelector('.text-xs.bg-slate-600.px-2.py-0\\.5.rounded-full.text-slate-400');
            
                compositeDetails = {
                title: titleElement ? titleElement.textContent.trim() : (document.title || 'Composite Print')
            };
            
            if (idElement) {
                const idText = idElement.textContent.trim();
                const idMatch = idText.match(/ID:\s*(\d+)/);
                if (idMatch && idMatch[1]) {
                    compositeDetails.id = idMatch[1];
                }
            }
            
            console.log('Using basic details from DOM:', compositeDetails);
        }
        
        // --- Temporarily modify canvas for printing --- 
        
        // Store original state
        const originalObjects = [...canvas.getObjects()];
        const originalBackgroundColor = canvas.backgroundColor;
        
        // Identify and remove grid lines for printing
        // Use a more robust method to identify grid lines
        const gridLines = originalObjects.filter(obj => {
            return (obj.type === 'line' && 
                   !obj.selectable && 
                   !obj.evented) || 
                   (obj.gridLine === true);  // Check for custom property if you're using it
        });
        
        console.log(`Found ${gridLines.length} grid lines to hide for printing`);
        
        // Remove grid lines temporarily
            gridLines.forEach(line => canvas.remove(line));
        
        // Set background to white for printing
        canvas.backgroundColor = '#ffffff';
        
        // Force canvas re-rendering
        canvas.renderAll();
        
        // Create a data URL of the modified canvas
        const dataUrl = canvas.toDataURL({
            format: 'png',
            quality: 1.0,
            multiplier: 2 // Higher quality for printing
        });
        
        // --- Restore canvas state --- 
        
        // Restore grid lines if needed
            gridLines.forEach(line => canvas.add(line));
            gridLines.forEach(line => canvas.sendObjectToBack(line));
        
        // Restore original background color
        canvas.backgroundColor = originalBackgroundColor;
        
        // Re-render the canvas to its original state
        canvas.renderAll();
        
        // --- Create Print Window --- 
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow pop-ups to print the canvas');
            return;
        }
        
        const title = compositeDetails.title || 'Composite Print';
        
        // Helper function to safely render details
        const renderDetail = (label, value) => {
            return value ? `<div class="detail-item"><span class="label">${label}:</span> <span class="value">${value}</span></div>` : '';
        };
        
        // Write HTML content to the new window
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print - ${title}</title>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600&display=swap');

                        @page {
                            size: auto;
                            margin: 0.75in; /* Slightly reduced margin */
                        }
                        * {
                            box-sizing: border-box;
                        }
                        body {
                            font-family: 'Open Sans', sans-serif;
                            margin: 0;
                            padding: 0;
                            color: #212529; /* Darker text for better contrast */
                            line-height: 1.5;
                            background-color: white;
                        }
                        .print-container {
                            width: 100%;
                            max-width: 100%;
                            margin: 0 auto;
                        }
                        .header {
                            text-align: left;
                            margin-bottom: 25px;
                            padding-bottom: 15px;
                            border-bottom: 2px solid #007bff; /* Accent color for header */
                            display: flex;
                            justify-content: space-between;
                            align-items: flex-end;
                        }
                        .organization {
                            font-family: 'Roboto', sans-serif;
                            font-size: 26px;
                            font-weight: 700;
                            color: #0056b3; /* Darker blue */
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }
                        .header-title h1 {
                            font-family: 'Roboto', sans-serif;
                            margin: 0;
                            font-size: 22px;
                            color: #343a40;
                            font-weight: 500;
                            text-align: right;
                        }
                        .document-meta {
                            font-size: 11px;
                            color: #495057;
                            text-align: right;
                            margin-top: 5px;
                        }
                        .image-container {
                            text-align: center;
                            margin-bottom: 25px;
                            border: 1px solid #dee2e6; /* Lighter border */
                            padding: 15px;
                            border-radius: 4px;
                            background-color: #f8f9fa; /* Light background for image area */
                            page-break-inside: avoid;
                        }
                        .image-container img {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 0 auto;
                            border-radius: 3px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                        }
                        .details-container {
                            padding: 0;
                        }
                        .details-container h2 {
                            font-family: 'Roboto', sans-serif;
                            font-size: 18px;
                            font-weight: 500;
                            color: #007bff;
                            padding-bottom: 8px;
                            border-bottom: 1px solid #ced4da;
                            margin-top: 0;
                            margin-bottom: 18px;
                            letter-spacing: 0.25px;
                        }
                        .detail-item {
                            margin-bottom: 10px;
                            font-size: 13px; /* Slightly smaller base font for details */
                            display: flex;
                        }
                        .detail-item .label {
                            font-weight: 600;
                            color: #495057; /* Subtler label color */
                            min-width: 140px; /* Adjusted width */
                            flex-shrink: 0;
                        }
                        .detail-item .value {
                            color: #212529;
                            word-break: break-word;
                        }
                        .description, .notes {
                            margin-top: 20px;
                            font-size: 13px;
                            line-height: 1.6;
                            background-color: #f8f9fa;
                            padding: 12px 15px;
                            border-radius: 4px;
                            border: 1px solid #e9ecef;
                            page-break-inside: avoid;
                        }
                        .description strong, .notes strong {
                            font-family: 'Roboto', sans-serif;
                            font-weight: 500;
                            color: #343a40;
                            display: block;
                            margin-bottom: 6px;
                        }
                        .no-details-message {
                            text-align: center;
                            color: #6c757d;
                            font-style: italic;
                            padding: 15px;
                            background-color: #f8f9fa;
                            border: 1px dashed #ced4da;
                            border-radius: 4px;
                            margin-top: 15px;
                        }
                        .footer {
                            margin-top: 30px;
                            padding-top: 15px;
                            border-top: 1px solid #dee2e6;
                            text-align: center;
                            font-size: 10px;
                            color: #6c757d;
                        }
                        .controls {
                            display: none; /* Hidden by default for print */
                        }

                        /* Default: Portrait Layout (image above details) */
                        .content {
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                        }
                        .image-container img {
                           max-height: 45vh; /* Adjusted for portrait aesthetics */
                        }

                        @media print {
                            body {
                                background-color: white;
                            }
                            .print-container {
                                box-shadow: none;
                                border: none;
                            }
                            .controls { display: none !important; }
                            .footer {
                                position: fixed;
                                bottom: 0.25in;
                                left: 0.75in;
                                right: 0.75in;
                                font-size: 9px;
                            }
                            
                            @media (orientation: portrait) {
                                .image-container img {
                                    max-height: 40vh; /* Fine-tune for print portrait */
                                }
                                .details-container {
                                    margin-top: 20px;
                                }
                            }

                            @media (orientation: landscape) {
                                .content {
                                    flex-direction: row;
                                    gap: 25px; /* Slightly more gap */
                                }
                                .image-container {
                                    flex: 0 0 45%; /* Slightly less for image to give more to text */
                                    max-width: 45%;
                                    margin-bottom: 0;
                                    align-self: flex-start;
                                }
                                .image-container img {
                                    max-height: 70vh; /* Adjusted for landscape print */
                                }
                                .details-container {
                                    flex: 1;
                                    padding-left: 20px;
                                    border-left: 1px solid #dee2e6;
                                }
                                .details-container h2 {
                                    margin-top: 0;
                                }
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="header">
                            <div class="organization">FaceRender</div>
                            <div>
                                <div class="header-title"><h1>${title}</h1></div>
                                <div class="document-meta">
                                    Generated: ${compositeDetails.created_at || new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
                                </div>
                            </div>
                        </div>
                        
                        <div class="content">
                            <div class="image-container">
                                <img src="${dataUrl}" alt="Composite Image">
                            </div>
                            <div class="details-container">
                                <h2>Composite Information</h2>
                                ${compositeDetails.witness_name || compositeDetails.case_title || compositeDetails.description ? 
                                    `
                                    ${renderDetail('Witness', compositeDetails.witness_name)}
                                    ${renderDetail('Case Title', compositeDetails.case_title)}
                                    ${compositeDetails.description ? 
                                        `<div class="description"><strong>Narrative Description:</strong><p>${compositeDetails.description}</p></div>` : ''}
                                    ` : 
                                    `<div class="no-details-message">No specific composite information available.</div>`
                                }
                                
                                <h2 style="margin-top: 25px;">Suspect Profile</h2>
                                ${compositeDetails.suspect_gender || compositeDetails.suspect_ethnicity || 
                                  compositeDetails.suspect_age_range || compositeDetails.suspect_height || 
                                  compositeDetails.suspect_body_build || compositeDetails.suspect_additional_notes ? 
                                    `
                                    ${renderDetail('Gender', compositeDetails.suspect_gender)}
                                    ${renderDetail('Ethnicity', compositeDetails.suspect_ethnicity)}
                                    ${renderDetail('Est. Age Range', compositeDetails.suspect_age_range)}
                                    ${renderDetail('Est. Height', compositeDetails.suspect_height)}
                                    ${renderDetail('Body Build', compositeDetails.suspect_body_build)}
                                    ${compositeDetails.suspect_additional_notes ? 
                                        `<div class="notes"><strong>Additional Characteristics:</strong><p>${compositeDetails.suspect_additional_notes}</p></div>` : ''}
                                    ` : 
                                    `<div class="no-details-message">No specific suspect profile details available.</div>`
                                }
                            </div>
                        </div>
                        
                        <div class="footer">
                            CONFIDENTIAL DOCUMENT &bull; Property of FaceRender &bull; Generated on ${new Date().toLocaleString('en-US', { dateStyle: 'full', timeStyle: 'short' })}
                        </div>
                        
                        <div class="controls">
                            <button onclick="window.print()">Print Document</button>
                            <button onclick="window.close()">Close</button>
                        </div>
                    </div>
                    <script>
                        // Auto print when loaded
                        window.onload = function() {
                            // Give a moment for the page to render fully
                            setTimeout(function() {
                                window.print();
                            }, 500);
                        };
                    </script>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        console.log('Print window created successfully with composite details');
        
    } catch (error) {
        console.error('Error during print canvas operation:', error);
        alert('Failed to print the canvas. Please try again.');
    }
}

/**
 * Download the canvas content as an image (legacy method, kept for fallback)
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function downloadCanvas(canvas) {
    try {
        console.log('Legacy download canvas function called');
        
        // Check if canvas exists
        if (!canvas) {
            console.error('Cannot download - canvas is not defined');
            return;
        }
        
        exportCanvas(canvas, {
            format: 'png',
            quality: 1.0,
            multiplier: 2,
            keepTransparency: true,
            showGrid: false
        });
        
    } catch (error) {
        console.error('Error during download canvas operation:', error);
        alert('Failed to download the canvas. Please try again.');
    }
} 