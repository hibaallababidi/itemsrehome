<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')
            ->insert([
                ['location_name' => 'الفردوس', 'city_id' => 1],
                ['location_name' => 'ابن يعقوب', 'city_id' => 1],
                ['location_name' => 'كرم الجبل', 'city_id' => 1],
                ['location_name' => 'الحيدرية', 'city_id' => 1],
                ['location_name' => 'الحلوانية', 'city_id' => 1],
                ['location_name' => 'مقر الأنبياء', 'city_id' => 1],

                ['location_name' => 'مركز درعا', 'city_id' => 3],
                ['location_name' => 'الصنمين', 'city_id' => 3],
                ['location_name' => 'إزرع', 'city_id' => 3],


                ['location_name' => 'حماه', 'city_id' => 5],
                ['location_name' => 'الغاب', 'city_id' => 5],
                ['location_name' => 'السلمية', 'city_id' => 5],
                ['location_name' => 'مصياف', 'city_id' => 5],

                ['location_name' => 'حمص', 'city_id' => 7],
                ['location_name' => 'القصير', 'city_id' => 7],
                ['location_name' => 'نلكلخ', 'city_id' => 7],
                ['location_name' => 'الرستن', 'city_id' => 7],
                ['location_name' => 'تدمر', 'city_id' => 7],

                ['location_name' => 'اللاذقية', 'city_id' => 9],
                ['location_name' => 'جبلة', 'city_id' => 9],
                ['location_name' => 'القرداحة', 'city_id' => 9],

                ['location_name' => 'الرقة', 'city_id' => 11],
                ['location_name' => 'تل أبيض', 'city_id' => 11],

                ['location_name' => 'طرطوس', 'city_id' => 13],
                ['location_name' => 'بانياس', 'city_id' => 13],
                ['location_name' => 'دريكيش', 'city_id' => 13],


                ['location_name' => 'أبو رمانة', 'city_id' => 2],
                ['location_name' => 'حي الأمين', 'city_id' => 2],
                ['location_name' => 'البرامكه', 'city_id' => 2],
                ['location_name' => 'الحجاز', 'city_id' => 2],
                ['location_name' => 'الحريقة', 'city_id' => 2],
                ['location_name' => 'الشعلان', 'city_id' => 2],
                ['location_name' => 'العسالي', 'city_id' => 2],
                ['location_name' => 'العمارة', 'city_id' => 2],
                ['location_name' => 'القابون', 'city_id' => 2],
                ['location_name' => 'القصاع', 'city_id' => 2],
                ['location_name' => 'الميدان', 'city_id' => 2],
                ['location_name' => 'باب البريد', 'city_id' => 2],
                ['location_name' => 'باب الشرقي', 'city_id' => 2],
                ['location_name' => 'برزة', 'city_id' => 2],
                ['location_name' => 'حي البزورية', 'city_id' => 2],
                ['location_name' => 'بستان الحجر', 'city_id' => 2],
                ['location_name' => 'جادة الدرويشية', 'city_id' => 2],
                ['location_name' => 'جادة السنجقدار', 'city_id' => 2],
                ['location_name' => 'جوبر', 'city_id' => 2],
                ['location_name' => 'حي التضامن', 'city_id' => 2],
                ['location_name' => 'حي الزهور', 'city_id' => 2],
                ['location_name' => 'حي الشيخ سعد', 'city_id' => 2],
                ['location_name' => 'حي المزرعة', 'city_id' => 2],
                ['location_name' => 'المهاجرين', 'city_id' => 2],
                ['location_name' => 'دمشق القديمة', 'city_id' => 2],
                ['location_name' => 'دويلعة', 'city_id' => 2],
                ['location_name' => 'ركن الدين', 'city_id' => 2],
                ['location_name' => 'زقاق الجن', 'city_id' => 2],
                ['location_name' => 'ساروجة', 'city_id' => 2],
                ['location_name' => 'سوق المناخلية', 'city_id' => 2],
                ['location_name' => 'نهرعيشة', 'city_id' => 2],
                ['location_name' => 'المزة', 'city_id' => 2],
                ['location_name' => 'كفر سوسة', 'city_id' => 2],
                ['location_name' => 'القيميرية', 'city_id' => 2],
                ['location_name' => 'حي القنوات', 'city_id' => 2],

                ['location_name' => 'طبالة', 'city_id' => 2],
                ['location_name' => 'صالحية', 'city_id' => 2],
                ['location_name' => 'ديماس', 'city_id' => 2],
                ['location_name' => 'يعفور', 'city_id' => 2],
                ['location_name' => 'قرى الأسد', 'city_id' => 2],
                ['location_name' => 'صحنايا', 'city_id' => 2],
                ['location_name' => 'صيدنايا', 'city_id' => 2],
                ['location_name' => 'زبداني', 'city_id' => 2],
// دير الزور

                ['location_name' => 'البوكمال', 'city_id' => 4],
                ['location_name' => 'الميادين', 'city_id' => 4],
                ['location_name' => 'الصالحية', 'city_id' => 4],
                ['location_name' => 'الشاطىء', 'city_id' => 4],
                ['location_name' => 'حطين', 'city_id' => 4],

                ['location_name' => 'حسرات', 'city_id' => 4],
                ['location_name' => 'غريبه', 'city_id' => 4],
                ['location_name' => 'تشرين', 'city_id' => 4],
                ['location_name' => 'بقرص', 'city_id' => 4],
                ['location_name' => 'بسيتين', 'city_id' => 4],
                ['location_name' => 'الدوير', 'city_id' => 4],
                ['location_name' => 'الرغيب', 'city_id' => 4],


                //قامشلي
                ['location_name' => 'قامشلي', 'city_id' => 6],
                ['location_name' => 'مالكية', 'city_id' => 6],
                ['location_name' => 'رميلان', 'city_id' => 6],
                ['location_name' => 'معبدة', 'city_id' => 6],
                ['location_name' => 'قحطانية', 'city_id' => 6],
                ['location_name' => 'جوادية', 'city_id' => 6],
                ['location_name' => 'شرم الشيخ', 'city_id' => 6],
                ['location_name' => 'كربلاء', 'city_id' => 6],
                ['location_name' => 'رأس العين', 'city_id' => 6],
                ['location_name' => 'خان الجبل', 'city_id' => 6],
                ['location_name' => 'مزرعة الشاطئ', 'city_id' => 6],

                ['location_name' => 'يافا', 'city_id' => 6],
                ['location_name' => 'دير الغصن', 'city_id' => 6],
                ['location_name' => 'الغسانية', 'city_id' => 6],
                ['location_name' => 'تل حميس', 'city_id' => 6],
                ['location_name' => 'تل حلف', 'city_id' => 6],


//أدلب

                ['location_name' => 'سرمدا', 'city_id' => 8],
                ['location_name' => 'باعودا', 'city_id' => 8],
                ['location_name' => 'باقرحا', 'city_id' => 8],
                ['location_name' => 'حارم', 'city_id' => 8],
                ['location_name' => 'الغسانية', 'city_id' => 8],
                ['location_name' => 'اليعقوبية', 'city_id' => 8],
                ['location_name' => 'القنية', 'city_id' => 8],
                ['location_name' => 'معرة النعمان', 'city_id' => 8],
                ['location_name' => 'تفتناز', 'city_id' => 8],
                ['location_name' => 'سرجيلا', 'city_id' => 8],
                ['location_name' => 'المغارة', 'city_id' => 8],
                ['location_name' => 'دللوزة', 'city_id' => 8],
                ['location_name' => 'البارة', 'city_id' => 8],
                ['location_name' => 'أريحا', 'city_id' => 8],
                ['location_name' => 'عرشين', 'city_id' => 8],
                ['location_name' => 'تل افس', 'city_id' => 8],

                //القنيطرة

                ['location_name' => 'ناحية مسعدة ', 'city_id' => 10],
                ['location_name' => 'ناحية خشنية ', 'city_id' => 10],
                ['location_name' => 'ناحية خان أرنبة ', 'city_id' => 10],
                ['location_name' => 'شكوم ', 'city_id' => 10],
                ['location_name' => 'عامودية ', 'city_id' => 10],
                ['location_name' => 'رمتا ', 'city_id' => 10],
                ['location_name' => 'القصبية ', 'city_id' => 10],
                ['location_name' => 'الرفيد ', 'city_id' => 10],
                ['location_name' => 'الصبح ', 'city_id' => 10],
                ['location_name' => 'الكوم ', 'city_id' => 10],
                ['location_name' => 'المعلقة ', 'city_id' => 10],
                ['location_name' => 'قرقس ', 'city_id' => 10],

//السويدا
                ['location_name' => 'المزرعة ', 'city_id' => 12],
                ['location_name' => 'شقا ', 'city_id' => 12],
                ['location_name' => 'العريقة ', 'city_id' => 12],
                ['location_name' => 'صلخد ', 'city_id' => 12],
                ['location_name' => 'الغارية ', 'city_id' => 12],
                ['location_name' => 'القريا ', 'city_id' => 12],
                ['location_name' => 'ملح ', 'city_id' => 12],
                ['location_name' => 'ظهر الجبل ', 'city_id' => 12],
                ['location_name' => 'سد العين ', 'city_id' => 12],
            ]);
    }
}
