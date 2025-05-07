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
            'name' => 'Dr. Yasser',
            'email' => 'yasser@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $instructor2 = DB::table('users')->insertGetId([
            'name' => 'Dr. Ahmad',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create learning paths
        $path1 = DB::table('learning_paths')->insertGetId([
            'name' => 'Web Development',
            'description' => 'Learn full-stack web development.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $path2 = DB::table('learning_paths')->insertGetId([
            'name' => 'Mobile Development',
            'description' => 'Learn mobile apps development.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create courses
        $course1 = DB::table('courses')->insertGetId([
            'title' => 'Laravel Basics',
            'description' => 'Introduction to Laravel framework.',
            'category' => 'Backend Development',
            'price' => 99.99,
            'instructor_id' => $instructor1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course2 = DB::table('courses')->insertGetId([
            'title' => 'Flutter Development',
            'description' => 'Building mobile apps with Flutter.',
            'category' => 'Mobile Development',
            'price' => 149.99,
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
                'title' => 'Introduction to Laravel',
                'content' => 'Laravel basics, installation, setup.',
                'course_id' => $course1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Flutter Widgets',
                'content' => 'Learn about different Flutter widgets.',
                'course_id' => $course2,
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
            'questions' => json_encode(['What is Laravel?', 'Explain MVC.']),
            'course_id' => $course1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
