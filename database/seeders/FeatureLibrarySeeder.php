<?php

namespace Database\Seeders;

use App\Models\FacialFeature;
use App\Models\FeatureCategory;
use App\Models\FeatureType;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class FeatureLibrarySeeder extends Seeder
{
    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the console command to output information
        $this->command = $this->command ?? new class extends Command {
            protected $signature = 'feature:seed';
            public function info($string, $verbosity = null) { parent::info($string, $verbosity); }
            public function warn($string, $verbosity = null) { parent::warn($string, $verbosity); }
        };
        
        $this->command->info('Starting feature library seeding...');
        $this->command->info('Creating feature types and categories...');
        
        // Clear existing data for fresh seed
        Schema::disableForeignKeyConstraints();
        FacialFeature::truncate();
        FeatureCategory::truncate();
        FeatureType::truncate();
        Schema::enableForeignKeyConstraints();

        // Create feature types
        $eyesType = FeatureType::create(['name' => 'eyes']);
        $eyebrowsType = FeatureType::create(['name' => 'eyebrows']);
        $noseType = FeatureType::create(['name' => 'nose']);
        $mouthType = FeatureType::create(['name' => 'mouth']);
        $earsType = FeatureType::create(['name' => 'ears']);
        $hairType = FeatureType::create(['name' => 'hair']);
        $faceType = FeatureType::create(['name' => 'face']);
        $neckType = FeatureType::create(['name' => 'neck']);
        $accessoriesType = FeatureType::create(['name' => 'accessories']);
        
        // Create feature categories for eyes
        $almondEyes = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Almond']);
        $roundEyes = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Round']);
        $hoodedEyes = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Hooded']);
        $monolid = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Monolid']);
        $upturnedEyes = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Upturned']);
        $downturnedEyes = FeatureCategory::create(['feature_type_id' => $eyesType->id, 'name' => 'Downturned']);
        
        // Create feature categories for eyebrows
        $thinEyebrows = FeatureCategory::create(['feature_type_id' => $eyebrowsType->id, 'name' => 'Thin']);
        $thickEyebrows = FeatureCategory::create(['feature_type_id' => $eyebrowsType->id, 'name' => 'Thick']);
        $archedEyebrows = FeatureCategory::create(['feature_type_id' => $eyebrowsType->id, 'name' => 'Arched']);
        $straightEyebrows = FeatureCategory::create(['feature_type_id' => $eyebrowsType->id, 'name' => 'Straight']);
        
        // Create feature categories for nose
        $flatNose = FeatureCategory::create(['feature_type_id' => $noseType->id, 'name' => 'Flat']);
        $asianNose = FeatureCategory::create(['feature_type_id' => $noseType->id, 'name' => 'Asian']);
        $snubNose = FeatureCategory::create(['feature_type_id' => $noseType->id, 'name' => 'Snub']);
        
        // Create feature categories for hair
        $shortHair = FeatureCategory::create(['feature_type_id' => $hairType->id, 'name' => 'Short']);
        $mediumHair = FeatureCategory::create(['feature_type_id' => $hairType->id, 'name' => 'Medium']);
        $longHair = FeatureCategory::create(['feature_type_id' => $hairType->id, 'name' => 'Long']);
        
        // Create feature categories for mouth/lips
        $thinLips = FeatureCategory::create(['feature_type_id' => $mouthType->id, 'name' => 'Thin']);
        $fullLips = FeatureCategory::create(['feature_type_id' => $mouthType->id, 'name' => 'Full']);
        $wideLips = FeatureCategory::create(['feature_type_id' => $mouthType->id, 'name' => 'Wide']);
        
        // Create feature categories for face
        $roundFace = FeatureCategory::create(['feature_type_id' => $faceType->id, 'name' => 'Round']);
        $ovalFace = FeatureCategory::create(['feature_type_id' => $faceType->id, 'name' => 'Oval']);
        $squareFace = FeatureCategory::create(['feature_type_id' => $faceType->id, 'name' => 'Square']);
        $heartFace = FeatureCategory::create(['feature_type_id' => $faceType->id, 'name' => 'Heart']);
        
        // Create feature categories for accessories
        $glasses = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Glasses']);
        $hats = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Hats']);
        $jewelry = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Jewelry']);
        $moles = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Moles']);
        $shades = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Shades']);
        $facialHair = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Facial Hair']);
        $skinMarks = FeatureCategory::create(['feature_type_id' => $accessoriesType->id, 'name' => 'Skin Marks']);
        
        // Create feature categories for ears
        $smallEars = FeatureCategory::create(['feature_type_id' => $earsType->id, 'name' => 'Small']);
        $mediumEars = FeatureCategory::create(['feature_type_id' => $earsType->id, 'name' => 'Medium']);
        $largeEars = FeatureCategory::create(['feature_type_id' => $earsType->id, 'name' => 'Large']);
        
        // Create feature categories for neck
        $shortNeck = FeatureCategory::create(['feature_type_id' => $neckType->id, 'name' => 'Short']);
        $mediumNeck = FeatureCategory::create(['feature_type_id' => $neckType->id, 'name' => 'Medium']);
        $longNeck = FeatureCategory::create(['feature_type_id' => $neckType->id, 'name' => 'Long']);
        
        $this->command->info('Seeding facial features from storage/app/public/features directory...');
        
        $this->seedEyeFeatures($eyesType, $almondEyes, $roundEyes, $hoodedEyes, $monolid, $upturnedEyes, $downturnedEyes);
        $this->seedEyebrowFeatures($eyebrowsType, $thinEyebrows, $thickEyebrows, $archedEyebrows, $straightEyebrows);
        $this->seedNoseFeatures($noseType, $flatNose, $asianNose, $snubNose);
        $this->seedMouthFeatures($mouthType, $thinLips, $fullLips, $wideLips);
        $this->seedHairFeatures($hairType, $shortHair, $mediumHair, $longHair);
        $this->seedFaceFeatures($faceType, $roundFace, $ovalFace, $squareFace, $heartFace);
        $this->seedEarFeatures($earsType, $smallEars, $mediumEars, $largeEars);
        $this->seedNeckFeatures($neckType, $shortNeck, $mediumNeck, $longNeck);
        $this->seedAccessoryFeatures($accessoriesType, $glasses, $hats, $jewelry, $moles, $shades, $facialHair, $skinMarks);
        
        $this->command->info('Feature library seeding completed!');
    }
    
    /**
     * Seed eye features using actual files from storage
     */
    private function seedEyeFeatures($eyesType, $almondEyes, $roundEyes, $hoodedEyes, $monolid, $upturnedEyes, $downturnedEyes): void
    {
        // Log to check what files are being found
        $eyeFiles = Storage::disk('public')->files('features/eyes');
        $this->command->info('Found ' . count($eyeFiles) . ' eye files');
        
        if (empty($eyeFiles)) {
            $this->command->warn('No eye files found in storage/app/public/features/eyes');
            return;
        }
        
        foreach ($eyeFiles as $eyeFile) {
            $fileName = basename($eyeFile);
            $this->command->info('Processing eye file: ' . $fileName);
            
            if (!preg_match('/^(almond|round|hooded|monolid|upturned|downturned)-e(\d+)-(m|f)\.png$/', $fileName, $matches)) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match expected format
            }
            
            $categoryName = $matches[1];
            $itemNumber = $matches[2];
            $gender = $matches[3] === 'm' ? 'male' : 'female';
            
            // Determine the category ID based on the file name prefix
            switch ($categoryName) {
                case 'almond':
                    $categoryId = $almondEyes->id;
                    break;
                case 'round':
                    $categoryId = $roundEyes->id;
                    break;
                case 'hooded':
                    $categoryId = $hoodedEyes->id;
                    break;
                case 'monolid':
                    $categoryId = $monolid->id;
                    break;
                case 'upturned':
                    $categoryId = $upturnedEyes->id;
                    break;
                case 'downturned':
                    $categoryId = $downturnedEyes->id;
                    break;
                default:
                    $this->command->warn('Unknown category: ' . $categoryName);
                    continue 2; // Skip to next iteration of the foreach loop
            }
            
            $namePrefix = ucfirst($categoryName);
            $name = "{$namePrefix} Eyes {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($categoryName, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "EYE" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists
            if (FacialFeature::where('feature_code', $featureCode)->exists()) {
                $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                continue;
            }
            
            // Store only the relative path
            $path = 'features/eyes/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $eyesType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $this->command->info("Created eye feature: {$name}, Code: {$featureCode}");
        }
    }
    
    /**
     * Seed nose features using actual files from storage
     */
    private function seedNoseFeatures($noseType, $flatNose, $asianNose, $snubNose): void
    {
        // Log to check what files are being found
        $noseFiles = Storage::disk('public')->files('features/nose');
        $this->command->info('Found ' . count($noseFiles) . ' nose files');
        
        if (empty($noseFiles)) {
            $this->command->warn('No nose files found in storage/app/public/features/nose');
            return;
        }
        
        // Track used feature codes to prevent duplicates
        $usedFeatureCodes = [];
        $processedCount = 0;
        
        // Create a mapping for nose categories
        $categoryMapping = [
            'flat' => $flatNose->id,
            'asian' => $asianNose->id,
            'snub' => $snubNose->id,
            'button' => $snubNose->id, // Map button to snub
            'roman' => $flatNose->id, // Map roman to flat
            'default' => $flatNose->id // Default category
        ];
        
        foreach ($noseFiles as $noseFile) {
            $fileName = basename($noseFile);
            $this->command->info('Processing nose file: ' . $fileName);
            
            $matched = false;
            $categoryName = 'default';
            $itemNumber = null;
            $gender = 'male'; // Default gender
            
            // Remove trailing (1) or other duplicates from filenames
            $cleanFileName = preg_replace('/\(\d+\)\.png$/', '.png', $fileName);
            
            // Pattern 1: Standard format (flat|asian|snub)-n(\d+)-(m|f)
            if (preg_match('/^(flat|asian|snub|button|roman)-n(\d+)-(m|f)\.png$/i', $cleanFileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' ? 'male' : 'female';
                $matched = true;
            }
            // Pattern 2: Handle format like "nose001-f.png" or "nose-001-m.png"
            elseif (preg_match('/^nose[-_\.]?(\d+)[-_\.]?(m|f|male|female)?\.png$/i', $cleanFileName, $matches)) {
                $itemNumber = $matches[1];
                // Distribute evenly - every 3rd item goes to a different category
                $categoryIndex = $processedCount % 3;
                $categories = ['flat', 'asian', 'snub'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[2]) ? (strtolower($matches[2]) === 'm' || strtolower($matches[2]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 3: Any PNG file in the nose folder - last resort
            elseif (preg_match('/^(.+?)[-_\.]?(\d+)?[-_\.]?(m|f|male|female)?.*\.png$/i', $cleanFileName, $matches)) {
                // Extract a nose type if it's in the known categories
                $possibleCategory = strtolower($matches[1]);
                if (array_key_exists($possibleCategory, $categoryMapping)) {
                    $categoryName = $possibleCategory;
                } else {
                    // Distribute the remaining files evenly across categories
                    $categoryIndex = $processedCount % 3;
                    $categories = ['flat', 'asian', 'snub'];
                    $categoryName = $categories[$categoryIndex];
                }
                
                // If we can extract a number, use it, otherwise generate one
                $itemNumber = !empty($matches[2]) ? $matches[2] : $processedCount + 1;
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            
            if (!$matched) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match any pattern
            }
            
            // Force lowercase for consistency
            $categoryName = strtolower($categoryName);
            
            // Map special nose types to standard categories
            if ($categoryName === 'button') {
                $displayCategory = 'Snub';
                $categoryId = $snubNose->id;
            } elseif ($categoryName === 'roman') {
                $displayCategory = 'Flat';
                $categoryId = $flatNose->id;
            } else {
                // Get category ID from mapping
                $categoryId = isset($categoryMapping[$categoryName]) 
                    ? $categoryMapping[$categoryName] 
                    : $categoryMapping['default'];
                $displayCategory = ucfirst($categoryName);
            }
            
            // Normalize gender format
            if ($gender === 'm') $gender = 'male';
            if ($gender === 'f') $gender = 'female';
            
            $name = "{$displayCategory} Nose {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($displayCategory, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "NOSE" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists or has been used in this session
            if (FacialFeature::where('feature_code', $featureCode)->exists() || in_array($featureCode, $usedFeatureCodes)) {
                // Try to generate an alternative unique code by adding a suffix
                $suffix = 1;
                $newFeatureCode = $featureCode . $suffix;
                while (FacialFeature::where('feature_code', $newFeatureCode)->exists() || in_array($newFeatureCode, $usedFeatureCodes)) {
                    $suffix++;
                    $newFeatureCode = $featureCode . $suffix;
                    if ($suffix > 10) {  // Prevent infinite loop
                        $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                        continue 2;  // Skip to next file
                    }
                }
                $featureCode = $newFeatureCode;
            }
            
            // Track used codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/nose/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $noseType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $processedCount++;
            $this->command->info("Created nose feature: {$name}, Code: {$featureCode}");
        }
        
        $this->command->info("Processed {$processedCount} out of " . count($noseFiles) . " nose files");
    }
    
    /**
     * Seed hair features using actual files from storage
     */
    private function seedHairFeatures($hairType, $shortHair, $mediumHair, $longHair): void
    {
        // Log to check what files are being found
        $hairFiles = Storage::disk('public')->files('features/hair');
        $this->command->info('Found ' . count($hairFiles) . ' hair files');
        
        if (empty($hairFiles)) {
            $this->command->warn('No hair files found in storage/app/public/features/hair');
            return;
        }
        
        // Track used feature codes to prevent duplicates
        $usedFeatureCodes = [];
        $processedCount = 0;
        
        // Create a mapping for hair categories
        $categoryMapping = [
            'short' => $shortHair->id,
            'medium' => $mediumHair->id,
            'long' => $longHair->id,
            'default' => $mediumHair->id // Default category
        ];
        
        foreach ($hairFiles as $hairFile) {
            $fileName = basename($hairFile);
            $this->command->info('Processing hair file: ' . $fileName);
            
            $matched = false;
            $categoryName = 'default';
            $itemNumber = null;
            $gender = 'male'; // Default gender
            
            // Pattern 1: Standard format (short|medium|long)-h(\d+)-(m|f)
            if (preg_match('/^(short|medium|long)-h(\d+)-(m|f)\.png$/i', $fileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' ? 'male' : 'female';
                $matched = true;
            }
            // Pattern 2: Handle space issues like "short- h041-f.png"
            elseif (preg_match('/^(short|medium|long)\s*-\s*h(\d+)-(m|f)\.png$/i', $fileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' ? 'male' : 'female';
                $matched = true;
            }
            // Pattern 3: Just "hair" with number
            elseif (preg_match('/^hair[-_\.]?(\d+)[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                $itemNumber = $matches[1];
                // Distribute evenly - every 3rd item goes to a different category
                $categoryIndex = $processedCount % 3;
                $categories = ['short', 'medium', 'long'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[2]) ? (strtolower($matches[2]) === 'm' || strtolower($matches[2]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 4: Any PNG file in the hair folder - last resort
            elseif (preg_match('/^(.+?)[-_\.]?(\d+)?[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                // Extract a hair type if it's in the known categories
                $possibleCategory = strtolower($matches[1]);
                if (array_key_exists($possibleCategory, $categoryMapping)) {
                    $categoryName = $possibleCategory;
                } else {
                    // Distribute the remaining files evenly across categories
                    $categoryIndex = $processedCount % 3;
                    $categories = ['short', 'medium', 'long'];
                    $categoryName = $categories[$categoryIndex];
                }
                
                // If we can extract a number, use it, otherwise generate one
                $itemNumber = !empty($matches[2]) ? $matches[2] : $processedCount + 1;
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            
            if (!$matched) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match any pattern
            }
            
            // Force lowercase for consistency
            $categoryName = strtolower($categoryName);
            
            // Normalize gender format
            if ($gender === 'm') $gender = 'male';
            if ($gender === 'f') $gender = 'female';
            
            // Get category ID from mapping
            $categoryId = isset($categoryMapping[$categoryName]) 
                ? $categoryMapping[$categoryName] 
                : $categoryMapping['default'];
            
            $namePrefix = ucfirst($categoryName);
            $name = "{$namePrefix} Hair {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($categoryName, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "HAIR" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists or has been used in this session
            if (FacialFeature::where('feature_code', $featureCode)->exists() || in_array($featureCode, $usedFeatureCodes)) {
                // Try to generate an alternative unique code by adding a suffix
                $suffix = 1;
                $newFeatureCode = $featureCode . $suffix;
                while (FacialFeature::where('feature_code', $newFeatureCode)->exists() || in_array($newFeatureCode, $usedFeatureCodes)) {
                    $suffix++;
                    $newFeatureCode = $featureCode . $suffix;
                    if ($suffix > 10) {  // Prevent infinite loop
                        $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                        continue 2;  // Skip to next file
                    }
                }
                $featureCode = $newFeatureCode;
            }
            
            // Track used codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/hair/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $hairType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $processedCount++;
            $this->command->info("Created hair feature: {$name}, Code: {$featureCode}");
        }
        
        $this->command->info("Processed {$processedCount} out of " . count($hairFiles) . " hair files");
    }
    
    /**
     * Seed ear features using actual files from storage
     */
    private function seedEarFeatures($earsType, $smallEars, $mediumEars, $largeEars): void
    {
        // Log to check what files are being found
        $earFiles = Storage::disk('public')->files('features/ears');
        $this->command->info('Found ' . count($earFiles) . ' ear files');
        
        if (empty($earFiles)) {
            $this->command->warn('No ear files found in storage/app/public/features/ears');
            return;
        }
        
        foreach ($earFiles as $earFile) {
            $fileName = basename($earFile);
            $this->command->info('Processing ear file: ' . $fileName);
            
            if (!preg_match('/^(male|female)-(\d+)\.png$/', $fileName, $matches)) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match expected format
            }
            
            $gender = $matches[1] === 'male' ? 'male' : 'female';
            $itemNumber = $matches[2];
            
            // Assign to category based on item number (example logic - adjust as needed)
            $categoryId = null;
            $itemNum = intval($itemNumber);
            
            if ($itemNum <= 20) {
                $categoryId = $smallEars->id;
                $categoryPrefix = 'SM';
            } elseif ($itemNum <= 45) {
                $categoryId = $mediumEars->id;
                $categoryPrefix = 'ME';
            } else {
                $categoryId = $largeEars->id;
                $categoryPrefix = 'LA';
            }
            
            $namePrefix = ucfirst($gender);
            $name = "{$namePrefix} Ears {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "EAR" . $categoryPrefix . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists
            if (FacialFeature::where('feature_code', $featureCode)->exists()) {
                $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                continue;
            }
            
            // Store only the relative path
            $path = 'features/ears/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $earsType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $this->command->info("Created ear feature: {$name}, Code: {$featureCode}");
        }
    }
    
    /**
     * Seed neck features using actual files from storage
     */
    private function seedNeckFeatures($neckType, $shortNeck, $mediumNeck, $longNeck): void
    {
        // Log to check what files are being found
        $neckFiles = Storage::disk('public')->files('features/neck');
        $this->command->info('Found ' . count($neckFiles) . ' neck files');
        
        if (empty($neckFiles)) {
            $this->command->warn('No neck files found in storage/app/public/features/neck');
            return;
        }
        
        foreach ($neckFiles as $neckFile) {
            $fileName = basename($neckFile);
            $this->command->info('Processing neck file: ' . $fileName);
            
            if (!preg_match('/^(male|female)-(\d+)(?:\s+\(\d+\))?\.png$/', $fileName, $matches)) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match expected format
            }
            
            $gender = $matches[1] === 'male' ? 'male' : 'female';
            $itemNumber = $matches[2];
            
            // Assign to category based on item number (example logic - adjust as needed)
            $categoryId = null;
            $itemNum = intval($itemNumber);
            
            if ($itemNum <= 20) {
                $categoryId = $shortNeck->id;
                $categoryPrefix = 'SH';
            } elseif ($itemNum <= 40) {
                $categoryId = $mediumNeck->id;
                $categoryPrefix = 'ME';
            } else {
                $categoryId = $longNeck->id;
                $categoryPrefix = 'LO';
            }
            
            $namePrefix = ucfirst($gender);
            $name = "{$namePrefix} Neck {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "NECK" . $categoryPrefix . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists
            if (FacialFeature::where('feature_code', $featureCode)->exists()) {
                $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                continue;
            }
            
            // Store only the relative path
            $path = 'features/neck/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $neckType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $this->command->info("Created neck feature: {$name}, Code: {$featureCode}");
        }
    }
    
    /**
     * Seed accessory features using actual files from storage
     */
    private function seedAccessoryFeatures($accessoriesType, $glasses, $hats, $jewelry, $moles, $shades, $facialHair, $skinMarks): void
    {
        // Log to check what files are being found
        $accessoryFiles = Storage::disk('public')->files('features/accessories');
        $this->command->info('Found ' . count($accessoryFiles) . ' accessory files');
        
        if (empty($accessoryFiles)) {
            $this->command->warn('No accessory files found in storage/app/public/features/accessories');
            return;
        }
        
        $categoryMap = [
            'glasses' => $glasses->id,
            'hat' => $hats->id,
            'earnings' => $jewelry->id,
            'mole' => $moles->id,
            'shades' => $shades->id,
            'shaved-mustache' => $facialHair->id,
            'skin-discoloration' => $skinMarks->id
        ];

        // Track used feature codes to avoid duplicates
        $usedFeatureCodes = [];
        
        foreach ($accessoryFiles as $accessoryFile) {
            $fileName = basename($accessoryFile);
            $this->command->info('Processing accessory file: ' . $fileName);
            
            // Special handling for specific files that need custom codes
            if ($fileName === 'shades-1a.png') {
                $accessoryType = 'shades';
                $itemNumber = '2';
                $categoryId = $shades->id;
                $name = "Shades 2";
                $featureCode = "ACCSHA002";
            } elseif ($fileName === 'shaved-mustache.png') {
                $accessoryType = 'facial-hair';
                $itemNumber = '1';
                $categoryId = $facialHair->id;
                $name = "Facial Hair 1";
                $featureCode = "ACCFAC001";
            } else {
                // Extract the accessory type and item number
                $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                $parts = explode('-', $baseName);
                
                $accessoryType = $parts[0];
                $itemNumber = isset($parts[1]) ? $parts[1] : '1';
                
                // For cases like "skin-discoloration" where we need the full name
                if ($accessoryType === 'skin' && isset($parts[1]) && $parts[1] === 'discoloration') {
                    $accessoryType = 'skin-discoloration';
                    $itemNumber = '1';
                } else if ($accessoryType === 'shaved' && isset($parts[1]) && $parts[1] === 'mustache') {
                    $accessoryType = 'shaved-mustache';
                    $itemNumber = '1';
                    // Skip this since we have a special case for it above
                    continue;
                }
                
                // Handle cases like earnings.png (without numbers)
                if (!is_numeric($itemNumber)) {
                    $itemNumber = '1';
                }
                
                // Determine category ID from the mapping
                $categoryId = null;
                foreach ($categoryMap as $key => $id) {
                    if (strpos($accessoryType, $key) !== false) {
                        $categoryId = $id;
                        break;
                    }
                }
                
                // If no category match found, use the first category
                if ($categoryId === null) {
                    $this->command->warn("No category found for accessory type: {$accessoryType}, using default");
                    $categoryId = $glasses->id;
                }
                
                // Get the friendly name for the accessory
                $accessoryName = ucfirst($accessoryType);
                if (is_numeric($itemNumber)) {
                    $name = "{$accessoryName} {$itemNumber}";
                } else {
                    $name = $accessoryName;
                }
                
                // Create a unique feature code
                $typePrefix = strtoupper(substr(str_replace('-', '', $accessoryType), 0, 3));
                $featureCode = "ACC" . $typePrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            }
            
            // Check if this feature code has already been used
            if (in_array($featureCode, $usedFeatureCodes) || FacialFeature::where('feature_code', $featureCode)->exists()) {
                $this->command->warn("Skipping duplicate feature code: {$featureCode} for file: {$fileName}");
                continue;
            }
            
            // Add to used feature codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/accessories/' . $fileName;
            
            // Accessories can be used by either gender, so we'll set to both
            // For the database, we'll use 'male' as the default since 'unisex' is not allowed
            $gender = 'male';
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $accessoriesType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $this->command->info("Created accessory feature: {$name}, Code: {$featureCode}");
        }
    }
    
    /**
     * Seed eyebrow features using actual files from storage
     */
    private function seedEyebrowFeatures($eyebrowsType, $thinEyebrows, $thickEyebrows, $archedEyebrows, $straightEyebrows): void
    {
        // Log to check what files are being found
        $eyebrowFiles = Storage::disk('public')->files('features/eyebrows');
        $this->command->info('Found ' . count($eyebrowFiles) . ' eyebrow files');
        
        if (empty($eyebrowFiles)) {
            $this->command->warn('No eyebrow files found in storage/app/public/features/eyebrows');
            return;
        }
        
        // Track used feature codes to prevent duplicates
        $usedFeatureCodes = [];
        $processedCount = 0;
        
        // Distribute files more evenly across categories
        $categoryDistribution = [
            'thin' => $thinEyebrows->id,
            'thick' => $thickEyebrows->id,
            'arched' => $archedEyebrows->id,
            'straight' => $straightEyebrows->id,
            'flat' => $straightEyebrows->id, // Map flat to straight
            'default' => $straightEyebrows->id // Default category
        ];
        
        foreach ($eyebrowFiles as $eyebrowFile) {
            $fileName = basename($eyebrowFile);
            $this->command->info('Processing eyebrow file: ' . $fileName);
            
            // Try multiple patterns to match different file naming conventions
            $matched = false;
            $categoryName = 'default';
            $itemNumber = null;
            $gender = 'male'; // Default gender
            
            // Pattern 1: Standard format (thin|thick|arched|straight)-eb(\d+)-(m|f)
            if (preg_match('/^(thin|thick|arched|straight|flat)-eb(\d+)[-_\.]?(m|f|male|female).*\.png$/i', $fileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female';
                $matched = true;
            } 
            // Pattern 2: eb first format eb(\d+)-(thin|thick|arched|straight)-(m|f)
            elseif (preg_match('/^eb[-_\.]?(\d+)[-_\.]?(thin|thick|arched|straight|flat)?[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                $itemNumber = $matches[1];
                $categoryName = !empty($matches[2]) ? strtolower($matches[2]) : 'default';
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 3: Just eyebrows with number
            elseif (preg_match('/^eyebrows?[-_\.]?(\d+)[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                $itemNumber = $matches[1];
                // Distribute evenly - every 4th item goes to a different category
                $categoryIndex = $processedCount % 4;
                $categories = ['thin', 'thick', 'arched', 'straight'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[2]) ? (strtolower($matches[2]) === 'm' || strtolower($matches[2]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 4: Any PNG file in the eyebrows folder - last resort
            elseif (preg_match('/^(.+?)[-_\.]?(\d+)?[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                // If we can extract a number, use it, otherwise generate one
                $itemNumber = !empty($matches[2]) ? $matches[2] : $processedCount + 1;
                // Distribute the remaining files evenly across categories
                $categoryIndex = $processedCount % 4;
                $categories = ['thin', 'thick', 'arched', 'straight'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            
            if (!$matched) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match any pattern
            }
            
            // Force lowercase for consistency
            $categoryName = strtolower($categoryName);
            
            // Map flat eyebrows to straight category
            if ($categoryName === 'flat') {
                $categoryName = 'straight';
            }
            
            // Normalize gender format
            if ($gender === 'm') $gender = 'male';
            if ($gender === 'f') $gender = 'female';
            
            // Get category ID from distribution map
            $categoryId = isset($categoryDistribution[$categoryName]) 
                ? $categoryDistribution[$categoryName] 
                : $categoryDistribution['default'];
            
            $namePrefix = ucfirst($categoryName);
            $name = "{$namePrefix} Eyebrows {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($categoryName, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "EBRW" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists or has been used in this session
            if (FacialFeature::where('feature_code', $featureCode)->exists() || in_array($featureCode, $usedFeatureCodes)) {
                // Try to generate an alternative unique code by adding a suffix
                $suffix = 1;
                $newFeatureCode = $featureCode . $suffix;
                while (FacialFeature::where('feature_code', $newFeatureCode)->exists() || in_array($newFeatureCode, $usedFeatureCodes)) {
                    $suffix++;
                    $newFeatureCode = $featureCode . $suffix;
                    if ($suffix > 10) {  // Prevent infinite loop
                        $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                        continue 2;  // Skip to next file
                    }
                }
                $featureCode = $newFeatureCode;
            }
            
            // Track used codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/eyebrows/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $eyebrowsType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $processedCount++;
            $this->command->info("Created eyebrow feature: {$name}, Code: {$featureCode}");
        }
        
        $this->command->info("Processed {$processedCount} out of " . count($eyebrowFiles) . " eyebrow files");
    }
    
    /**
     * Seed mouth features using actual files from storage
     */
    private function seedMouthFeatures($mouthType, $thinLips, $fullLips, $wideLips): void
    {
        // Log to check what files are being found
        $mouthFiles = Storage::disk('public')->files('features/mouth');
        $this->command->info('Found ' . count($mouthFiles) . ' mouth files');
        
        if (empty($mouthFiles)) {
            $this->command->warn('No mouth files found in storage/app/public/features/mouth');
            return;
        }
        
        // Track used feature codes to prevent duplicates
        $usedFeatureCodes = [];
        $processedCount = 0;
        
        // Create a mapping for mouth categories
        $categoryMapping = [
            'thin' => $thinLips->id,
            'full' => $fullLips->id,
            'wide' => $wideLips->id,
            'bow' => $fullLips->id, // Map bow to full category
            'smile' => $wideLips->id, // Map smile to wide category
            'pout' => $fullLips->id, // Map pout to full category
            'default' => $fullLips->id // Default category
        ];
        
        foreach ($mouthFiles as $mouthFile) {
            $fileName = basename($mouthFile);
            $this->command->info('Processing mouth file: ' . $fileName);
            
            $matched = false;
            $categoryName = 'default';
            $itemNumber = null;
            $gender = 'male'; // Default gender
            
            // Pattern 1: Standard format (thin|full|wide)-m(\d+)-(m|f)
            if (preg_match('/^(thin|full|wide|bow|smile|pout)-m(\d+)-(m|f)\.png$/i', $fileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' ? 'male' : 'female';
                $matched = true;
            }
            // Pattern 2: Handle format like "mouth001-f.png" or "mouth-001-m.png"
            elseif (preg_match('/^mouth[-_\.]?(\d+)[-_\.]?(m|f|male|female)?\.png$/i', $fileName, $matches)) {
                $itemNumber = $matches[1];
                // Distribute evenly - every 3rd item goes to a different category
                $categoryIndex = $processedCount % 3;
                $categories = ['thin', 'full', 'wide'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[2]) ? (strtolower($matches[2]) === 'm' || strtolower($matches[2]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 3: Any PNG file in the mouth folder - last resort
            elseif (preg_match('/^(.+?)[-_\.]?(\d+)?[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                // Extract a mouth type if it's in the known categories
                $possibleCategory = strtolower($matches[1]);
                if (array_key_exists($possibleCategory, $categoryMapping)) {
                    $categoryName = $possibleCategory;
                } else {
                    // Distribute the remaining files evenly across categories
                    $categoryIndex = $processedCount % 3;
                    $categories = ['thin', 'full', 'wide'];
                    $categoryName = $categories[$categoryIndex];
                }
                
                // If we can extract a number, use it, otherwise generate one
                $itemNumber = !empty($matches[2]) ? $matches[2] : $processedCount + 1;
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            
            if (!$matched) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match any pattern
            }
            
            // Force lowercase for consistency
            $categoryName = strtolower($categoryName);
            
            // Map special mouth types to standard categories
            if ($categoryName === 'bow' || $categoryName === 'pout') {
                $displayCategory = 'Full';
                $categoryId = $fullLips->id;
            } elseif ($categoryName === 'smile') {
                $displayCategory = 'Wide';
                $categoryId = $wideLips->id;
            } else {
                // Get category ID from mapping
                $categoryId = isset($categoryMapping[$categoryName]) 
                    ? $categoryMapping[$categoryName] 
                    : $categoryMapping['default'];
                $displayCategory = ucfirst($categoryName);
            }
            
            // Normalize gender format
            if ($gender === 'm') $gender = 'male';
            if ($gender === 'f') $gender = 'female';
            
            $name = "{$displayCategory} Lips {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($displayCategory, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "LIPS" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists or has been used in this session
            if (FacialFeature::where('feature_code', $featureCode)->exists() || in_array($featureCode, $usedFeatureCodes)) {
                // Try to generate an alternative unique code by adding a suffix
                $suffix = 1;
                $newFeatureCode = $featureCode . $suffix;
                while (FacialFeature::where('feature_code', $newFeatureCode)->exists() || in_array($newFeatureCode, $usedFeatureCodes)) {
                    $suffix++;
                    $newFeatureCode = $featureCode . $suffix;
                    if ($suffix > 10) {  // Prevent infinite loop
                        $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                        continue 2;  // Skip to next file
                    }
                }
                $featureCode = $newFeatureCode;
            }
            
            // Track used codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/mouth/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $mouthType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $processedCount++;
            $this->command->info("Created mouth feature: {$name}, Code: {$featureCode}");
        }
        
        $this->command->info("Processed {$processedCount} out of " . count($mouthFiles) . " mouth files");
    }
    
    /**
     * Seed face features using actual files from storage
     */
    private function seedFaceFeatures($faceType, $roundFace, $ovalFace, $squareFace, $heartFace): void
    {
        // Log to check what files are being found
        $faceFiles = Storage::disk('public')->files('features/face');
        $this->command->info('Found ' . count($faceFiles) . ' face files');
        
        if (empty($faceFiles)) {
            $this->command->warn('No face files found in storage/app/public/features/face');
            return;
        }
        
        // Track used feature codes to prevent duplicates
        $usedFeatureCodes = [];
        $processedCount = 0;
        
        // Create a mapping for oblong face shape (map to oval)
        $categoryMapping = [
            'round' => $roundFace->id,
            'oval' => $ovalFace->id,
            'square' => $squareFace->id,
            'heart' => $heartFace->id,
            'oblong' => $ovalFace->id, // Map oblong to oval category
            'Square' => $squareFace->id, // Handle capitalized name
            'default' => $ovalFace->id // Default category
        ];
        
        foreach ($faceFiles as $faceFile) {
            $fileName = basename($faceFile);
            $this->command->info('Processing face file: ' . $fileName);
            
            $matched = false;
            $categoryName = 'default';
            $itemNumber = null;
            $gender = 'male'; // Default gender
            
            // Pattern 1: Standard format (round|oval|square|heart)-f(\d+)-(m|f)
            if (preg_match('/^(round|oval|square|heart|oblong|Square)-f(\d+)[-_\.]?(m|f|male|female)\.png$/i', $fileName, $matches)) {
                $categoryName = strtolower($matches[1]);
                $itemNumber = $matches[2];
                $gender = strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female';
                $matched = true;
            }
            // Pattern 2: Just "face" with number
            elseif (preg_match('/^face[-_\.]?(\d+)[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                $itemNumber = $matches[1];
                // Distribute evenly - every 4th item goes to a different category
                $categoryIndex = $processedCount % 4;
                $categories = ['round', 'oval', 'square', 'heart'];
                $categoryName = $categories[$categoryIndex];
                $gender = !empty($matches[2]) ? (strtolower($matches[2]) === 'm' || strtolower($matches[2]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            // Pattern 3: Any PNG file in the face folder - last resort
            elseif (preg_match('/^(.+?)[-_\.]?(\d+)?[-_\.]?(m|f|male|female)?.*\.png$/i', $fileName, $matches)) {
                // Extract a face shape if it's in the known categories
                $possibleCategory = strtolower($matches[1]);
                if (array_key_exists($possibleCategory, $categoryMapping)) {
                    $categoryName = $possibleCategory;
                } else {
                    // Distribute the remaining files evenly across categories
                    $categoryIndex = $processedCount % 4;
                    $categories = ['round', 'oval', 'square', 'heart'];
                    $categoryName = $categories[$categoryIndex];
                }
                
                // If we can extract a number, use it, otherwise generate one
                $itemNumber = !empty($matches[2]) ? $matches[2] : $processedCount + 1;
                $gender = !empty($matches[3]) ? (strtolower($matches[3]) === 'm' || strtolower($matches[3]) === 'male' ? 'male' : 'female') : 'male';
                $matched = true;
            }
            
            if (!$matched) {
                $this->command->warn('Skipping file with non-matching format: ' . $fileName);
                continue; // Skip if filename doesn't match any pattern
            }
            
            // Force lowercase for consistency
            $categoryName = strtolower($categoryName);
            
            // Map oblong face to oval (or other mappings if needed)
            if ($categoryName === 'oblong') {
                $categoryName = 'oval';
            } else if ($categoryName === 'square') {
                $categoryName = 'square';
            }
            
            // Normalize gender format
            if ($gender === 'm') $gender = 'male';
            if ($gender === 'f') $gender = 'female';
            
            // Get category ID from mapping
            $categoryId = isset($categoryMapping[$categoryName]) 
                ? $categoryMapping[$categoryName] 
                : $categoryMapping['default'];
            
            $namePrefix = ucfirst($categoryName);
            $name = "{$namePrefix} Face {$itemNumber}";
            
            // Include category and gender in the feature code to ensure uniqueness
            $categoryPrefix = substr($categoryName, 0, 2);
            $genderPrefix = ($gender === 'male') ? 'M' : 'F';
            $featureCode = "FACE" . strtoupper($categoryPrefix) . $genderPrefix . str_pad($itemNumber, 3, '0', STR_PAD_LEFT);
            
            // Check if feature code already exists or has been used in this session
            if (FacialFeature::where('feature_code', $featureCode)->exists() || in_array($featureCode, $usedFeatureCodes)) {
                // Try to generate an alternative unique code by adding a suffix
                $suffix = 1;
                $newFeatureCode = $featureCode . $suffix;
                while (FacialFeature::where('feature_code', $newFeatureCode)->exists() || in_array($newFeatureCode, $usedFeatureCodes)) {
                    $suffix++;
                    $newFeatureCode = $featureCode . $suffix;
                    if ($suffix > 10) {  // Prevent infinite loop
                        $this->command->warn("Skipping duplicate feature code: {$featureCode}");
                        continue 2;  // Skip to next file
                    }
                }
                $featureCode = $newFeatureCode;
            }
            
            // Track used codes
            $usedFeatureCodes[] = $featureCode;
            
            // Store only the relative path
            $path = 'features/face/' . $fileName;
            
            // Create the facial feature record
            FacialFeature::create([
                'feature_type_id' => $faceType->id,
                'feature_category_id' => $categoryId,
                'feature_code' => $featureCode,
                'name' => $name,
                'image_path' => $path,
                'gender' => $gender,
            ]);
            
            $processedCount++;
            $this->command->info("Created face feature: {$name}, Code: {$featureCode}");
        }
        
        $this->command->info("Processed {$processedCount} out of " . count($faceFiles) . " face files");
    }
}
