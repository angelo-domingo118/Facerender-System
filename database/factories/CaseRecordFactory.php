<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseRecord>
 */
class CaseRecordFactory extends Factory
{
    protected $model = CaseRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $incidentTypes = [
            'Theft',
            'Physical Assault',
            'Robbery',
            'Vandalism',
            'Online Scam',
            'Harassment',
            'Trespassing',
            'Property Damage',
            'Identity Theft',
            'Cybercrime',
            'Carnapping',
            'Estafa',
            'Drug-related',
            'Homicide',
            'Kidnapping'
        ];

        $locations = [
            'Quezon City - Commonwealth Avenue',
            'Manila - Tondo District',
            'Makati - Ayala Avenue',
            'Pasig - Ortigas Center',
            'Taguig - BGC',
            'Pasay - EDSA Extension',
            'Mandaluyong - Shaw Boulevard',
            'San Juan - Greenhills',
            'Caloocan - Monumento',
            'Parañaque - BF Homes',
            'Marikina - Riverbanks',
            'Muntinlupa - Alabang',
            'Las Piñas - SM Southmall area',
            'Valenzuela - North Expressway',
            'Navotas - Fish Port Complex',
            'Malabon - Panghulo'
        ];

        $caseTitles = [
            'Cellphone snatching at ',
            'Motorcycle theft in ',
            'Physical assault incident at ',
            'Home burglary in ',
            'Vehicle carnapping at ',
            'Online shopping scam victim from ',
            'ATM skimming case at ',
            'Store robbery in ',
            'Workplace harassment complaint from ',
            'Estafa case filed in ',
            'Property damage report from ',
            'Physical altercation at ',
            'Hit and run incident along ',
            'Pickpocketing victim at ',
            'Identity theft complaint from resident of '
        ];

        $locationKey = fake()->randomElement(array_keys($locations));
        $location = $locations[$locationKey];
        $title = fake()->randomElement($caseTitles) . $location;
        
        $incidentType = fake()->randomElement($incidentTypes);
        
        $caseDescriptions = [
            'Theft' => "Victim reported that their personal belongings were stolen at the specified location. Items missing include phone, wallet, and other valuables. No suspects identified yet.",
            'Physical Assault' => "Complainant was attacked by unidentified person(s) at the location. Victim sustained injuries and was treated at nearby hospital. Investigation ongoing to identify suspects.",
            'Robbery' => "Armed individuals forcibly took victim's belongings at the location. Witnesses reported seeing suspects flee on a motorcycle. CCTV footage being reviewed.",
            'Vandalism' => "Property was defaced with graffiti and sustained other damages. Building security reported incident. Reviewing security footage to identify perpetrators.",
            'Online Scam' => "Victim was defrauded through an online marketplace transaction. Paid for items that were never delivered. Transaction records being reviewed.",
            'Harassment' => "Individual reports repeated unwanted contact from known person. Victim feels threatened and unsafe. Restraining order being considered.",
            'Trespassing' => "Unknown individuals entered private property without authorization. Nothing reported stolen but security breach noted. Enhancing security measures.",
            'Property Damage' => "Complainant's property was damaged during an incident at the location. Photographic evidence collected. Insurance claim being processed.",
            'Identity Theft' => "Victim discovered unauthorized accounts opened in their name. Credit report shows suspicious activity. Financial institutions notified.",
            'Cybercrime' => "Individual's social media accounts were hacked and used for fraudulent activities. Digital evidence being collected. Coordinating with cybercrime division.",
            'Carnapping' => "Vehicle was stolen from parking area at the specified location. Car details added to alert system. Reviewing CCTV footage from surrounding areas.",
            'Estafa' => "Victim was deceived in a business transaction involving significant funds. Documentation of transaction collected. Financial trail being investigated.",
            'Drug-related' => "Suspicious activities observed at location potentially involving illegal substances. Surveillance operation being coordinated with narcotics division.",
            'Homicide' => "Deceased individual discovered at the location. Crime scene secured and evidence collected. Autopsy ordered. Witnesses being interviewed.",
            'Kidnapping' => "Individual reported missing under suspicious circumstances. Last seen at specified location. Family received ransom demand. Anti-kidnapping unit engaged."
        ];
        
        $description = $caseDescriptions[$incidentType] ?? "Detailed investigation ongoing regarding the $incidentType incident that occurred at $location. Multiple witnesses have provided statements and evidence is being processed.";
        
        $notes = "Follow-up investigation scheduled. Coordinating with local barangay officials for additional information. Victim advised on safety precautions and case progress reporting procedures.";

        return [
            'title' => $title,
            'description' => $description,
            'reference_number' => 'CASE-' . strtoupper(fake()->bothify('??####')),
            'status' => fake()->randomElement(['open', 'closed', 'pending', 'archived']),
            'incident_type' => $incidentType,
            'incident_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'incident_time' => fake()->dateTimeBetween('-23 hours', 'now')->format('H:i:s'),
            'location' => $location,
            'notes' => $notes,
            'user_id' => User::factory(),
        ];
    }
}
