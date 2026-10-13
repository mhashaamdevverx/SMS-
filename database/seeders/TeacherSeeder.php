<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TeacherAssignment::truncate();
        Teacher::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $coursesList = [
            ['name' => 'Laravel Framework', 'code' => 'CRS-101'],
            ['name' => 'Web Development', 'code' => 'CRS-102'],
            ['name' => 'Graphic Design', 'code' => 'CRS-103'],
            ['name' => 'Python Programming', 'code' => 'CRS-104'],
            ['name' => 'Cyber Security', 'code' => 'CRS-105']
        ];
        
        foreach ($coursesList as $c) {
            Course::firstOrCreate(
                ['course_name' => $c['name']],
                ['course_code' => $c['code'], 'user_id' => 2, 'course_description' => $c['name'] . ' Certification']
            );
        }

        $classList = [
            ['name' => 'Pre-KG', 'section' => 'Red'],
            ['name' => 'KG', 'section' => 'Blue'],
            ['name' => 'ONE', 'section' => 'Red'],
            ['name' => 'TWO', 'section' => 'Green'],
            ['name' => 'THREE', 'section' => 'Yellow'],
            ['name' => 'FOUR', 'section' => 'Red'],
            ['name' => 'FIVE', 'section' => 'Blue'],
            ['name' => 'SIX', 'section' => 'Green'],
            ['name' => 'SEVEN', 'section' => 'Red'],
            ['name' => 'EIGHT', 'section' => 'Blue'],
            ['name' => 'NINE', 'section' => 'Green'],
            ['name' => 'TEN', 'section' => 'Red'],
        ];

        // Max existing class_no
        $maxClassNo = (int) Classes::max('class_no');

        foreach ($classList as $cls) {
            $existing = Classes::where('class_name', $cls['name'])->where('section_name', $cls['section'])->first();
            if (!$existing) {
                $maxClassNo++;
                Classes::create([
                    'class_name' => $cls['name'],
                    'section_name' => $cls['section'],
                    'class_no' => $maxClassNo,
                    'chairs' => 30,
                    'user_id' => 2
                ]);
            }
        }

        $allSubjects = ['English', 'Urdu', 'Mathematics', 'General Science', 'Islamiyat', 'Computer Science', 'Pakistan Studies', 'Physics', 'Chemistry', 'Biology', 'Social Studies', 'Drawing & Arts'];
        foreach ($classList as $cls) {
            foreach (array_slice($allSubjects, 0, 6) as $s) {
                Subject::firstOrCreate([
                    'class_name' => $cls['name'],
                    'section_name' => $cls['section'],
                    'subject_name' => $s
                ], [
                    'user_id' => 2,
                    'class_id' => 1,
                    'total_marks' => 100,
                    'obtained_marks' => 0
                ]);
            }
        }

        $names = [
            'Fatima Zahra', 'Ayesha Khan', 'Sana Ahmed', 'Zoya Shah', 'Hina Pervez',
            'Usman Tariq', 'Tariq Mahmood', 'Bilal Hassan', 'Hamza Malik', 'Maryam Nawaz',
            'Kamran Akmal', 'Shahzaib Khan', 'Asif Ali', 'Nida Yasir', 'Faisal Qureshi',
            'Imran Khan', 'Bushra Bibi', 'Wasim Akram', 'Waqar Younis', 'Mahira Khan',
            'Fawad Khan', 'Atif Aslam', 'Iqra Aziz', 'Yasir Hussain', 'Sheheryar Munawar',
            'Sajal Ali', 'Ahad Raza Mir', 'Sana Javed', 'Shoaib Malik', 'Sania Mirza',
            'Babar Azam', 'Shaheen Afridi', 'Rizwan Ahmed', 'Shadab Khan', 'Haris Rauf',
            'Fakhar Zaman', 'Imam-ul-Haq', 'Naseem Shah', 'Sarfaraz Ahmed', 'Kainat Imtiaz',
            'Bismah Maroof', 'Nida Dar', 'Rashid Khan', 'Shoaib Akhtar', 'Shahid Afridi',
            'Younis Khan', 'Misbah-ul-Haq', 'Saeed Anwar', 'Inzamam-ul-Haq', 'Abdul Razzaq',
            'Azhar Ali', 'Asad Shafiq', 'Shan Masood', 'Saud Shakeel', 'Agha Salman',
            'Mohammad Rizwan', 'Mohammad Amir', 'Umar Akmal', 'Ahmed Shehzad', 'Muhammad Ali'
        ];

        foreach ($names as $idx => $n) {
            $prefix = ($idx % 3 == 0 || $idx % 7 == 0) ? 'Ma\'am ' : 'Sir ';
            $fullName = $prefix . $n;
            $email = strtolower(str_replace([' ', '\''], ['', ''], $n)) . ($idx + 1) . '@gmail.com';
            $phone = '0300' . str_pad($idx + 1, 7, '0', STR_PAD_LEFT);
            
            $clsIndex = $idx % count($classList);
            $targetClass = $classList[$clsIndex];
            $subjIndex = $idx % count($allSubjects);
            $targetSubj = $allSubjects[$subjIndex];
            
            $isCourse = ($idx % 10 == 0);
            $courseVal = $isCourse ? $coursesList[$idx % count($coursesList)]['name'] : $targetSubj;

            $teacher = Teacher::create([
                'user_id' => 2,
                'teacher_name' => $fullName,
                'teacher_email' => $email,
                'teacher_phone' => $phone,
                'course' => $courseVal,
                'teacher_image' => null,
            ]);

            TeacherAssignment::create([
                'teacher_id' => $teacher->id,
                'class_name' => $isCourse ? 'N/A' : $targetClass['name'],
                'section_name' => $isCourse ? null : $targetClass['section'],
                'subject_or_course' => $courseVal,
            ]);

            if ($idx % 4 == 0 && !$isCourse) {
                $nextClass = $classList[($clsIndex + 1) % count($classList)];
                TeacherAssignment::create([
                    'teacher_id' => $teacher->id,
                    'class_name' => $nextClass['name'],
                    'section_name' => $nextClass['section'],
                    'subject_or_course' => $targetSubj,
                ]);

                $teacher->course = $courseVal . ' (' . $targetClass['name'] . ', ' . $nextClass['name'] . ')';
                $teacher->save();
            }
        }
    }
}
