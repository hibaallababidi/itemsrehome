<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')
            ->insert([
                ['city_name' => 'حلب'],
                ['city_name' => 'دمشق'],
                ['city_name' => 'درعا'],
                ['city_name' => 'دير الزور'],
                ['city_name' => 'حماه'],
                ['city_name' => 'الحسكة'],
                ['city_name' => 'حمص'],
                ['city_name' => 'إدلب'],
                ['city_name' => 'اللاذقية'],
                ['city_name' => 'القنيطرة'],
                ['city_name' => 'الرقة'],
                ['city_name' => 'السويداء'],
                ['city_name' => 'طرطوس'],
            ]);
    }
}
