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
        try {
            
            $faker = Faker::create();
            $list_name = ["សុខា","ប៊ុនធឿន","រឿទ្ធី","សំណាង","វុទ្ធី","ស្រីនាង","ចន្ទថូ","សុខុនធី","ដាវី","សុខជា"];
            $list_occupation = ["គ្រូ","វេជ្ជបណ្ឌិត","វិស្វករ","អ្នកចាត់ការរដ្ឋបាល","ជាងសំណង់","អ្នកចម្រៀង","នាយករដ្ឋបាល","ស្ត្រីចុងភៅ","ប៉ូលិស","យោធា","អ្នកកាសែត","អ្នកស្រាវជ្រាវ","គិលានុបដ្ឋាយិកា","អ្នកលក់","អ្នកបកប្រែ","អ្នកទេសចរណ៍","ជាងថតរូប","អ្នកអភិវឌ្ឍន៍កម្មវិធី"];
            
            $json_address = file_get_contents('init/address.json');
            $addresses = json_decode($json_address, true); 
            $addresses = array_combine(range(1, count($addresses)), array_values($addresses));   
            $records = [];

            for ($i = 1; $i <= 100; $i++) {
    
                $province_random=$faker->randomElement($addresses);
                $province = (int)$province_random['id'];
                
                $district_random = $faker->randomElement($addresses[$province]['district']);
                $district=$district_random['id'];
                $commune=$faker->randomElement($district_random['communes'])['id'] ?? 0;

                $first_name=$faker->randomElement($list_name);
                $last_name=$faker->randomElement($list_name);
                $records[] = [
                    'book_id'                     => $faker->uuid,
                    'madeAt'                      => $faker->dateTimeThisDecade,
                    'formula'                     => $faker->word,
                    'last_name'                   => $last_name,
                    'first_name'                  => $first_name,
                    'nickname'                    => $faker->randomElement($list_name),
                    'dob'                         => $faker->date,
                    'pob_province'                => $province,
                    'pob_district'                => $district,
                    'pob_commune'                 => $commune,
                    'ethnicity'                   => $faker->randomElement(['Ethnicity X', 'Ethnicity Y', 'Ethnicity Z']),
                    'nationality'                 => $faker->country,
                    'religion'                    => $faker->randomElement(['Religion A', 'Religion B', 'Religion C']),
                    'previous_occupation'         => $faker->randomElement($list_occupation),
                    'occupation'                  => $faker->randomElement($list_occupation),
                    'current_address'             => $faker->address,
                    'province'                    => $province,
                    'district'                    => $district,
                    'commune'                     => $commune,
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
                    'identity_photo'              => "",
                    'right_thumb_print'           => "",
                    'right_index_print'           => "",
                    'right_middle_print'          => "",
                    'right_ring_print'            => "",
                    'right_pinky_print'           => "",
                    'left_thumb_print'            => "",
                    'left_index_print'            => "",
                    'left_middle_print'           => "",
                    'left_ring_print'             => "",
                    'left_pinky_print'            => "",
                    'front_body_photo'            => "",
                    'right_profile_photo'         => "",
                    'left_profile_photo'          => "",
                    'four_left_fingers_print'     => "",
                    'left_thumb_print01'          => "",
                    'right_thumb_print01'         => "",
                    'four_right_fingers_print'    => "",
                    'left_palm_print'             => "",
                    'right_palm_print'            => "",
                    'special_mark1'               => "",
                    'special_mark2'               => "",
                    'special_mark3'               => "",
                    'form_template'               => "",
                    'number'                      => $faker->randomNumber(9, true),
                    'upload_by'                   => $faker->numberBetween(1, 3),
                    'uuid'                        => $faker->uuid(),
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                    'dir_name'                    => $i."_".$first_name . '_' . $last_name,
                    'dir_name_updated'            => $i."_".$first_name . '_' . $last_name,
                ];
            }
    
            DB::table('documents')->insert($records);
        } catch (\Throwable $th) {
            log_error($th->getMessage());
        }
    }
}
