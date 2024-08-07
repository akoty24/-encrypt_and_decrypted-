<?php
namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeService
{
    public function getStudentsWithGrades(int $courseId)
    {
        $course = Course::find($courseId);
        if (!$course) {
            return null;
        }

        return $course->enrollments()->with(['student', 'grades'])->get();
    }
    public function getGradesByStudent(int $studentId)
    {
        return Grade::whereHas('enrollment', function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->with('enrollment.course', 'gradeItem') 
          ->get();
    }
    public function addGrades(int $studentId, array $coursesData): bool
    {
        DB::transaction(function () use ($studentId, $coursesData) {
            foreach ($coursesData as $courseData) {
                $courseId = $courseData['course_id'];
                $gradesData = $courseData['grades'];

                // Check if the student is enrolled in the course
                $isEnrolled = Enrollment::where('student_id', $studentId)
                                        ->where('course_id', $courseId)
                                        ->exists();

                if (!$isEnrolled) {
                    // Handle error, student is not enrolled in the course
                    throw new \Exception("Student is not enrolled in course ID $courseId.");
                }

                foreach ($gradesData as $gradeData) {
                    Grade::updateOrCreate(
                        [
                            'enrollment_id' => Enrollment::where('student_id', $studentId)
                                                         ->where('course_id', $courseId)
                                                         ->value('id'),
                            'grade_item_id' => $gradeData['grade_item_id']
                        ],
                        ['score' => $gradeData['score']]
                    );
                }
            }
        });

        return true;
    }
}


