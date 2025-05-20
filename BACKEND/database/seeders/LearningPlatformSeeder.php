<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LearningPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // Create instructors
        $instructor1 = DB::table('users')->insertGetId([
            'name' => 'د.سامي علي',
            'email' => 'yasser@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $instructor2 = DB::table('users')->insertGetId([
            'name' => 'د.أحمد علي',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create learning paths
        $path1 = DB::table('learning_paths')->insertGetId([
            'name' => 'برمجة تطبيقات الويب',
            'description' => 'تعلم كل شيء تحتاجه لتطوير تطبيقات الويب',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $path2 = DB::table('learning_paths')->insertGetId([
            'name' => 'برمجة تطبيقات الجوال',
            'description' => 'ابن تطبيق الموبايل الخاص بك',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create courses
        $course1 = DB::table('courses')->insertGetId([
            'title' => 'أساسيات لارفل',
            'description' => 'مقدمة عن لارفل',
            'category' => 'برمجيات',
            'price' => 100000,
            'is_paid' => true,
            'instructor_id' => $instructor1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course2 = DB::table('courses')->insertGetId([
            'title' => 'تطوير تطبيقات الجوال',
            'description' => 'بناء تطبيقات الجوال عن طريق فلاتر',
            'category' => 'برمجيات',
            'price' => 200000,
            'is_paid' => true,
            'instructor_id' => $instructor2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attach courses to learning paths
        DB::table('path_courses')->insert([
            ['path_id' => $path1, 'course_id' => $course1, 'created_at' => now(), 'updated_at' => now()],
            ['path_id' => $path2, 'course_id' => $course2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create lectures
        DB::table('lectures')->insert([
            [
                'title' => 'المحاضرة الأولى',
                'content' => 'Laravel basics, installation, setup.',
                'course_id' => $course1,
                'type' => 'video',
                'url' => 'https://www.youtube.com/watch?v=RbKEYDtkAJI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'المحاضرة الثانية',
                'content' => 'Learn about different Flutter widgets.',
                'course_id' => $course1,
                'type' => 'video',
                'url' => 'https://www.youtube.com/watch?v=fWmkhW8Y-cM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'المحاضرة الثالثة',
                'content' => 'Learn about different Flutter widgets.',
                'course_id' => $course1,
                'type' => 'video',
                'url' => 'https://www.youtube.com/watch?v=CikyLcP31Kw',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'المحاضرة الرابعة',
                'content' => 'Learn about different Flutter widgets.',
                'course_id' => $course1,
                'type' => 'pdf',
                'url' => env('APP_URL').'/laravel_tutoril.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'المحاضرة الخامسة',
                'content' => 'Learn about different Flutter widgets.',
                'course_id' => $course1,
                'type' => 'pdf',
                'url' => env('APP_URL').'/The-Clean-Coders-Guide-to-Laravel.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create a student to comment
        $student = DB::table('users')->insertGetId([
            'name' => 'Student Ali',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'department' => 'Computer Science',
            'academic_year' => '3rd',
            'specialization' => 'Software Engineering',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a blog post
        $post = DB::table('blogs')->insertGetId([
            'title' => 'My first blog',
            'content' => 'This is a sample student post.',
            'user_id' => $student,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create an exam for course1
        DB::table('exams')->insert([
            'title' => 'Laravel Exam',
            'course_id' => $course1,
            'url' => env('APP_URL').'/laravel_exam.pdf',
            'duration' => 'ساعة واحدة',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
