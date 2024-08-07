<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeItemController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\StudentController;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:student')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('restore_task/{id}', [TaskController::class, 'restore']);
    // Route::get('/tasks/{id}', [TaskController::class, 'show']);
    //Route::Resource('tasks', TaskController::class);


    //Route::put('/update_status/{id}',  [TaskController::class, 'updateStatus']);
});
//Route::get('get_by_Category/{id}', [TaskController::class, 'getByCategory']);

Route::Resource('students', StudentController::class);
Route::Resource('levels', LevelController::class);
Route::Resource('courses', CourseController::class);
Route::Resource('grade-items', GradeItemController::class);

Route::post('enroll/{courseId}/students', [EnrollmentController::class, 'addStudents']);
Route::post('remove/{courseId}/students', [EnrollmentController::class, 'removeStudents']);
Route::get('all_enrollments', [EnrollmentController::class, 'index']);

Route::get('students_with_grades', [GradeController::class, 'getStudentsWithGrades']);
Route::get('student_grades', [GradeController::class, 'getStudentGrades']);
Route::post('/add_grades', [GradeController::class, 'store']);


