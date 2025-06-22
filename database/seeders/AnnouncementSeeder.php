<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Announcement::create([
            'title' => 'مواعيد  المقابلات العملية لمساق تطوير الويب',
            'content' => 'ستعقد المقابلات العملية يوم الأربعاء الموافق 15\5\2025 من الساعة 10 صباحاً حتى 2 ظهراً في القاعة 302',
            'published_at' => Carbon::now(),
        ]);
       Announcement::create([
            'title' => 'امتحان نهاية الفصل',
            'content' => 'سيقام امتحان نهاية الفصل يوم الاثنين القادم الساعة 10 صباحاً.',
            'published_at' => Carbon::now(),
        ]);

        Announcement::create([
            'title' => 'تنبيه بخصوص المحاضرات',
            'content' => 'تم تغيير وقت المحاضرات يوم الخميس إلى الساعة 2 ظهراً.',
            'published_at' => Carbon::now()->subDay(),
        ]);
}
}