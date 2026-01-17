<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pl_PL');

        // 1. Tworzenie Klas
        $classes = [];
        foreach ([1, 2, 3] as $level) {
            foreach (['A', 'B', 'C'] as $letter) {
                $classes[] = ClassGroup::create(['name' => $level . $letter]);
            }
        }

        // 2. Tworzenie Przedmiotów
        $subjectNames = ['Matematyka', 'Język Polski', 'Angielski', 'Historia', 'Biologia'];
        $subjects = [];
        foreach ($subjectNames as $name) {
            $subjects[] = Subject::create(['name' => $name]);
        }

        // 3. Tworzenie Nauczycieli (z unikalnymi mailami)
        $teachers = [];
        for ($i = 1; $i <= 20; $i++) {
            $name = $faker->firstName . ' ' . $faker->lastName;
            $email = Str::slug($name) . $i . "@szkola.pl";
            
            $teachers[] = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('nauczyciel123'),
                'role' => 'teacher'
            ]);
        }

        // 4. Tworzenie Admina
        User::firstOrCreate(
            ['email' => 'admin@admin.pl'],
            ['name' => 'Administrator Systemu', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        // 5. Przypisanie Nauczycieli do Klas
        foreach ($classes as $class) {
            foreach ($subjects as $subject) {
                $randomTeacher = $teachers[array_rand($teachers)];
                
                DB::table('teacher_assignments')->insert([
                    'teacher_id' => $randomTeacher->id,
                    'subject_id' => $subject->id,
                    'class_group_id' => $class->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 6. Tworzenie Uczniów i Rodziców
        $gradeDescriptions = ['Kartkówka', 'Odpowiedź ustna', 'Sprawdzian', 'Praca domowa', 'Praca na lekcji', 'Projekt'];

        foreach ($classes as $class) {
            for ($i = 1; $i <= 10; $i++) {
                $lastName = $faker->lastName;
                $parentName = $faker->firstName('male') . ' ' . $lastName;
                $studentName = $faker->firstName . ' ' . $lastName;

                
                $parentEmail = Str::slug($parentName) . "." . Str::slug($class->name) . $i . "@rodzic.pl";
                $studentEmail = Str::slug($studentName) . "." . Str::slug($class->name) . $i . "@uczen.pl";

                $parent = User::create([
                    'name' => $parentName,
                    'email' => $parentEmail,
                    'password' => Hash::make('start123'),
                    'role' => 'parent'
                ]);

                $student = User::create([
                    'name' => $studentName,
                    'email' => $studentEmail,
                    'password' => Hash::make('start123'),
                    'role' => 'student',
                    'class_group_id' => $class->id,
                    'parent_id' => $parent->id
                ]);

                // 7. Wstawianie ocen
                foreach ($subjects as $subject) {
                    $teacherId = DB::table('teacher_assignments')
                        ->where('class_group_id', $class->id)
                        ->where('subject_id', $subject->id)
                        ->value('teacher_id');

                    if ($teacherId) {
                        for ($g = 0; $g < rand(2, 5); $g++) {
                            Grade::create([
                                'student_id' => $student->id,
                                'subject_id' => $subject->id,
                                'teacher_id' => $teacherId,
                                'value' => rand(1, 6),
                                'comment' => $gradeDescriptions[array_rand($gradeDescriptions)],
                                'created_at' => now()->subDays(rand(1, 30)),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
