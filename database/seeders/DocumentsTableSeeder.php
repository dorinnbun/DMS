<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $records = [];
        for ($i = 0; $i < 100; $i++) {
            $records[] = [
                'book_id'                     => $faker->uuid,
                'madeAt'                      => $faker->dateTimeThisDecade,
                'formula'                     => $faker->word,
                'last_name'                   => $faker->lastName,
                'first_name'                  => $faker->firstName,
                'nickname'                    => $faker->userName,
                'dob'                         => $faker->date,
                'pob_province'                => $faker->state,
                'pob_district'                => $faker->city,
                'pob_commune'                 => $faker->citySuffix,
                'ethnicity'                   => $faker->randomElement(['Ethnicity X', 'Ethnicity Y', 'Ethnicity Z']),
                'nationality'                 => $faker->country,
                'religion'                    => $faker->randomElement(['Religion A', 'Religion B', 'Religion C']),
                'previous_occupation'         => $faker->jobTitle,
                'occupation'                  => $faker->jobTitle,
                'current_address'             => $faker->address,
                'province'                    => $faker->state,
                'district'                    => $faker->city,
                'commune'                     => $faker->citySuffix,
                'identity'                    => $faker->uuid,
                'height'                      => $faker->randomFloat(2, 4.5, 6.5), // Height between 4.5 and 6.5 feet
                'spouse'                      => $faker->name,
                'spouse_address'              => $faker->address,
                'father_name'                 => $faker->name('male'),
                'father_address'              => $faker->address,
                'mother_name'                 => $faker->name('female'),
                'mother_address'              => $faker->address,
                'private_certificate_officer' => $faker->name,
                'supervision_officer'         => $faker->name,
                'scheduling_research_officer' => $faker->name,
                'all_left_fingers'            => $faker->word,
                'all_right_fingers'           => $faker->word,
                'right_thumb_print'           => $faker->word,
                'right_index_print'           => $faker->word,
                'right_middle_print'          => $faker->word,
                'right_ring_print'            => $faker->word,
                'right_pinky_print'           => $faker->word,
                'left_thumb_print'            => $faker->word,
                'left_index_print'            => $faker->word,
                'left_middle_print'           => $faker->word,
                'left_ring_print'             => $faker->word,
                'left_pinky_print'            => $faker->word,
                'front_body_photo'            => $faker->word,
                'right_profile_photo'         => $faker->word,
                'left_profile_photo'          => $faker->word,
                'four_left_fingers_print'     => $faker->word,
                'left_thumb_print01'          => $faker->word,
                'right_thumb_print01'         => $faker->word,
                'four_right_fingers_print'    => $faker->word,
                'left_palm_print'             => $faker->word,
                'right_palm_print'            => $faker->word,
                'special_mark1'               => $faker->word,
                'special_mark2'               => $faker->word,
                'special_mark3'               => $faker->word,
                'number'                      => $faker->randomNumber(9, true),
                'upload_by'                   => $faker->numberBetween(1, 3),
                'uuid'                          => $faker->uuid(),
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ];
        }

        DB::table('documents')->insert($records);
    }
}
