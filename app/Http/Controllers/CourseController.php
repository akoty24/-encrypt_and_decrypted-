<?php
namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Http\Resources\Collection\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $courses = $this->courseService->getAll($request);

        if (!$courses) {
            return response()->json(['message' => 'No courses found.'], 404);
        }
        return response()->json([
            'message' => 'Courses retrieved successfully.',
            'courses' => new CourseCollection($courses)
        ]);
    }

    public function store(CourseRequest $request)
    {
        $course = $this->courseService->create($request->all());
        if (!$course) {
            return response()->json(['message' => 'Failed to create Course.'], 500);
        }
        return response()->json([
            'message' => 'Course created successfully.',
            'course' => new CourseResource($course)
        ]);
    }

    public function update(CourseRequest $request, string $id)
    {
        $course = $this->courseService->update($request->all(), $id);
        if (!$course) {
            return response()->json(['message' => 'Failed to update Course.'], 500);
        }
        return response()->json([
            'message' => 'Course updated successfully.',
            'course' => new CourseResource($course)
        ]);
    }

    public function destroy(string $id)
    {
        $course = $this->courseService->delete($id);
        if (!$course) {
            return response()->json(['message' => 'Failed to delete Course.'], 500);
        }
        return response()->json(['message' => 'Course deleted successfully.']);
    }

    public function enroll(Request $request, string $id)
    {
        $enrollment = $this->courseService->enroll($id, $request->student_ids);
        if (!$enrollment) {
            return response()->json(['message' => 'Failed to enroll students.'], 500);
        }
        return response()->json(['message' => 'Students enrolled successfully.']);
    }

    public function addGrades(Request $request, string $id)
    {
        $grades = $this->courseService->addGrades($id, $request->grades);
        if (!$grades) {
            return response()->json(['message' => 'Failed to add grades.'], 500);
        }
        return response()->json(['message' => 'Grades added successfully.']);
    }
}
