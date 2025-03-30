<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\Composite;
use App\Models\User;
use App\Models\Witness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Composite>
 */
class CompositeFactory extends Factory
{
    protected $model = Composite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['Male', 'Female'];
        $ethnicities = [
            'Filipino',
            'Ilocano',
            'Cebuano',
            'Tagalog',
            'Bicolano',
            'Waray',
            'Kapampangan',
            'Pangasinense',
            'Chinese-Filipino',
            'Korean-Filipino',
            'Indian-Filipino',
            'Foreign'
        ];
        $ageRanges = [
            '18-25',
            '26-35',
            '36-45',
            '46-55',
            '56-65'
        ];
        $heights = [
            '4\'11" - 5\'1"',
            '5\'2" - 5\'4"',
            '5\'5" - 5\'7"',
            '5\'8" - 5\'10"',
            '5\'11" - 6\'1"',
            '6\'2" and above'
        ];
        $bodyBuilds = [
            'Slim',
            'Average',
            'Athletic',
            'Stocky',
            'Heavy-set',
            'Muscular'
        ];
        
        $gender = fake()->randomElement($genders);
        $ethnicity = fake()->randomElement($ethnicities);
        $ageRange = fake()->randomElement($ageRanges);
        $height = fake()->randomElement($heights);
        $bodyBuild = fake()->randomElement($bodyBuilds);
        
        $facialFeatures = [
            'Round face with high cheekbones',
            'Oval face with prominent jawline',
            'Square face with broad forehead',
            'Heart-shaped face with pointed chin',
            'Diamond-shaped face with angular features',
            'Long face with narrow chin',
            'Rectangular face with strong features'
        ];
        
        $eyeTypes = [
            'Almond-shaped eyes',
            'Round eyes',
            'Hooded eyes',
            'Downturned eyes',
            'Upturned eyes',
            'Deep-set eyes',
            'Small eyes'
        ];
        
        $noseTypes = [
            'Flat nose bridge',
            'Prominent nose bridge',
            'Aquiline nose',
            'Bulbous nose tip',
            'Wide nostril base',
            'Narrow nostril base',
            'Short nose'
        ];
        
        $lipTypes = [
            'Full lips',
            'Thin lips',
            'Wide lips',
            'Heart-shaped lips',
            'Downturned lips',
            'Asymmetrical lips'
        ];
        
        $hairTypes = [
            'Short straight black hair',
            'Medium-length wavy black hair',
            'Long straight black hair',
            'Short curly black hair',
            'Balding with receding hairline',
            'Completely bald',
            'Crew cut',
            'Undercut style',
            'Bob cut',
            'Layered hair',
            'Dyed brown/blonde hair',
            'Dyed red hair'
        ];
        
        $distinctiveFeatures = [
            'Small mole on right cheek',
            'Scar on left eyebrow',
            'Dimples when smiling',
            'Tattoo on neck',
            'Visible birthmark on face',
            'Gap between front teeth',
            'Crooked nose (possibly broken previously)',
            'Facial hair - goatee',
            'Facial hair - full beard',
            'Facial hair - mustache only',
            'Thick eyebrows',
            'Thin eyebrows',
            'Glasses - thick frame',
            'Glasses - thin frame',
            'Ear piercing(s)',
            'Facial piercing(s)'
        ];
        
        $facialFeature = fake()->randomElement($facialFeatures);
        $eyeType = fake()->randomElement($eyeTypes);
        $noseType = fake()->randomElement($noseTypes);
        $lipType = fake()->randomElement($lipTypes);
        $hairType = fake()->randomElement($hairTypes);
        $distinctiveFeature1 = fake()->randomElement($distinctiveFeatures);
        $distinctiveFeatures = array_diff($distinctiveFeatures, [$distinctiveFeature1]);
        $distinctiveFeature2 = fake()->boolean(40) ? fake()->randomElement($distinctiveFeatures) : null;
        
        $additionalNotes = "Suspect has $facialFeature with $eyeType and $noseType. $lipType and $hairType. Distinctive features include $distinctiveFeature1" . 
            ($distinctiveFeature2 ? " and $distinctiveFeature2." : ".");
            
        if (fake()->boolean(30)) {
            $additionalNotes .= " Witness noted the suspect spoke with " . fake()->randomElement([
                'a Tagalog accent',
                'a Cebuano accent',
                'an Ilocano accent',
                'a Bicolano accent',
                'a foreign accent',
                'a Chinese accent',
                'a distinctive deep voice',
                'a soft, quiet voice'
            ]) . ".";
        }
        
        $titleFormats = [
            "Composite of $gender suspect in ",
            "Facial sketch of $ageRange year old $gender from ",
            "$ethnicity $gender suspect in ",
            "Suspect composite for ",
            "Witness identification of suspect in "
        ];
        
        $case = CaseRecord::factory()->create();
        $caseTitle = str_replace('CASE-', '', $case->reference_number) . ' - ' . $case->title;
        $title = fake()->randomElement($titleFormats) . $caseTitle;
        
        $description = "This composite sketch was created based on witness description of the suspect involved in case " . 
            $case->reference_number . ". The suspect is described as a $ageRange year old $gender of $ethnicity descent, " . 
            "with $height height and $bodyBuild build. " . $additionalNotes;

        return [
            'case_id' => $case->id,
            'witness_id' => Witness::factory()->create(['case_id' => $case->id]),
            'user_id' => User::factory(),
            'title' => $title,
            'description' => $description,
            'canvas_width' => 800,
            'canvas_height' => 600,
            'final_image_path' => null,
            'suspect_gender' => $gender,
            'suspect_ethnicity' => $ethnicity,
            'suspect_age_range' => $ageRange,
            'suspect_height' => $height,
            'suspect_body_build' => $bodyBuild,
            'suspect_additional_notes' => $additionalNotes,
        ];
    }
}
