<?php

namespace App\Livewire\Editor;

use Livewire\Component;

class FeatureTransformPanel extends Component
{
    // Feature properties for transformation
    public $featureId = null;
    public $featureName = 'Eyes'; // Default for demo
    public $positionX = 0;
    public $positionY = 0;
    public $width = 100;
    public $height = 100;
    public $rotation = 0;
    public $aspectRatioLocked = false;
    public $flipHorizontal = false;
    public $flipVertical = false;
    public $moveIncrement = 5; // Default increment for position adjustments
    
    // Methods to handle transformations
    public function updatePosition($x, $y)
    {
        $this->positionX = $x;
        $this->positionY = $y;
        $this->applyTransform();
    }
    
    public function moveRelative($direction, $amount = null)
    {
        // If no amount is specified, use the moveIncrement property
        $amount = $amount ?? $this->moveIncrement;
        
        switch ($direction) {
            case 'north':
            case 'up':
                $this->positionY -= $amount;
                break;
            case 'south':
            case 'down':
                $this->positionY += $amount;
                break;
            case 'west':
            case 'left':
                $this->positionX -= $amount;
                break;
            case 'east':
            case 'right':
                $this->positionX += $amount;
                break;
            case 'northwest':
                $this->positionX -= $amount;
                $this->positionY -= $amount;
                break;
            case 'northeast':
                $this->positionX += $amount;
                $this->positionY -= $amount;
                break;
            case 'southwest':
                $this->positionX -= $amount;
                $this->positionY += $amount;
                break;
            case 'southeast':
                $this->positionX += $amount;
                $this->positionY += $amount;
                break;
        }
        
        $this->applyTransform();
    }
    
    public function updateSize($width, $height)
    {
        $this->width = $width;
        
        if ($this->aspectRatioLocked) {
            // Calculate height based on the aspect ratio
            $aspectRatio = $this->width / $this->height;
            $this->height = $width / $aspectRatio;
        } else {
            $this->height = $height;
        }
        
        $this->applyTransform();
    }
    
    public function adjustSize($dimension, $amount)
    {
        if ($dimension === 'width') {
            $newWidth = $this->width + $amount;
            if ($newWidth >= 10) { // Minimum size check
                $this->width = $newWidth;
                
                if ($this->aspectRatioLocked) {
                    $aspectRatio = $this->width / $this->height;
                    $this->height = $this->width / $aspectRatio;
                }
            }
        } else if ($dimension === 'height') {
            $newHeight = $this->height + $amount;
            if ($newHeight >= 10) { // Minimum size check
                $this->height = $newHeight;
                
                if ($this->aspectRatioLocked) {
                    $aspectRatio = $this->width / $this->height;
                    $this->width = $this->height * $aspectRatio;
                }
            }
        }
        
        $this->applyTransform();
    }
    
    public function toggleAspectRatio()
    {
        $this->aspectRatioLocked = !$this->aspectRatioLocked;
    }
    
    public function updateRotation($angle)
    {
        $this->rotation = $angle;
        $this->applyTransform();
    }
    
    public function adjustRotation($amount)
    {
        $this->rotation += $amount;
        
        // Normalize rotation to -180 to 180 range
        if ($this->rotation > 180) {
            $this->rotation -= 360;
        } else if ($this->rotation < -180) {
            $this->rotation += 360;
        }
        
        $this->applyTransform();
    }
    
    public function setRotation($angle)
    {
        $this->rotation = $angle;
        $this->applyTransform();
    }
    
    public function toggleFlipHorizontal()
    {
        $this->flipHorizontal = !$this->flipHorizontal;
        $this->applyTransform();
    }
    
    public function toggleFlipVertical()
    {
        $this->flipVertical = !$this->flipVertical;
        $this->applyTransform();
    }
    
    public function resetPosition()
    {
        $this->positionX = 0;
        $this->positionY = 0;
        $this->applyTransform();
    }
    
    public function resetSize()
    {
        $this->width = 100;
        $this->height = 100;
        $this->applyTransform();
    }
    
    public function resetRotation()
    {
        $this->rotation = 0;
        $this->flipHorizontal = false;
        $this->flipVertical = false;
        $this->applyTransform();
    }
    
    protected function applyTransform()
    {
        // In a real app, this would apply the transformation to the feature
        // For now, this is just a placeholder
        // You would typically emit an event or call a service to update the feature
    }
    
    public function render()
    {
        return view('livewire.editor.feature-transform-panel');
    }
}
