<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollRequest;
use App\Http\Resources\Collection\EnrollmentCollection;
use App\Http\Resources\Collection\StudentCollection;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{

    private $enrollmentService;
    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }
    public function addStudents(EnrollRequest $request, string $courseId)
    {
        $studentIds = $request->input('student_ids');

        if (!is_array($studentIds)) {
            return response()->json(['message' => 'Invalid student IDs format.'], 400);
        }

        $success = $this->enrollmentService->addStudents($courseId, $studentIds);

        if (!$success) {
            return response()->json(['message' => 'Failed to add students.'], 500);
        }

        return response()->json(['message' => 'Students added successfully.']);
    }

    public function removeStudents(EnrollRequest $request, string $courseId)
    {
        $studentIds = $request->input('student_ids');

        if (!is_array($studentIds)) {
            return response()->json(['message' => 'Invalid student IDs format.'], 400);
        }

        $success = $this->enrollmentService->removeStudents($courseId, $studentIds);

        if (!$success) {
            return response()->json(['message' => 'Failed to remove students.'], 500);
        }

        return response()->json(['message' => 'Students removed successfully.']);
    }
  
     public function index(Request $request){
        $enrollments = $this->enrollmentService->getAll($request);
        if (!$enrollments) {
            return response()->json(['message' => 'No enrollments found.'], 404);
        }
        return response()->json([
            'message' => 'Enrollments retrieved successfully.',
            'enrollments' => new EnrollmentCollection($enrollments)
        ]);
     }

    // public function addGrades(Request $request, string $id)
    // {
    //     $grades = $this->enrollmentService->addGrades($id, $request->grades);
    //     if (!$grades) {
    //         return response()->json(['message' => 'Failed to add grades.'], 500);
    //     }
    //     return response()->json(['message' => 'Grades added successfully.']);
    // }
}
