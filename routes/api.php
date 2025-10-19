<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');


// protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // teams
    Route::get('get-all-teams', [TeamController::class, 'getAllTeams']);
    Route::post('create-team', [TeamController::class, 'createTeam']);
    Route::get('show-team/{id}', [TeamController::class, 'showTeam']);
    Route::put('update-team/{id}', [TeamController::class, 'updateTeam']);
    Route::delete('delete-team/{id}', [TeamController::class, 'deleteTeam']);

    // users
    Route::get('get-all-users', [UserController::class, 'getAllUsers']);
    Route::post('create-user', [UserController::class, 'createUser']);
    Route::get('show-user/{id}', [UserController::class, 'showUser']);
    Route::put('update-user/{id}', [UserController::class, 'updateUser']);
    Route::delete('delete-user/{id}', [UserController::class, 'deleteUser']);

    // projects
    Route::get('get-all-projects', [ProjectController::class, 'getAllProjects']);
    Route::post('create-project', [ProjectController::class, 'createProject']);
    Route::get('show-project/{id}', [ProjectController::class, 'showProject']);
    Route::put('update-project/{id}', [ProjectController::class, 'updateProject']);
    Route::delete('delete-project/{id}', [ProjectController::class, 'deleteProject']);

    // project-usrs
    Route::get('get-all-project-users', [ProjectUserController::class, 'getAllProjectUsers']);
    Route::post('create-project-user', [ProjectUserController::class, 'createProjectUser']);
    Route::get('show-project-user/{id}', [ProjectUserController::class, 'showProjectUser']);
    Route::put('update-project-user/{id}', [ProjectUserController::class, 'updateProjectUser']);
    Route::delete('delete-project-user/{id}', [ProjectUserController::class, 'deleteProjectUser']);

    Route::get('/get-all-issues', [IssueController::class, 'getAllIssues']);
    Route::get('/show-issue/{id}', [IssueController::class, 'showIssue']);
    Route::post('/create-issue', [IssueController::class, 'createIssue']);
    Route::put('/update-issue/{id}', [IssueController::class, 'updateIssue']);
    Route::delete('/delete-issue/{id}', [IssueController::class, 'deleteIssue']);
    Route::put('/update-status/{id}', [IssueController::class, 'updateStatus']);
    Route::get('/my-issues', [IssueController::class, 'myIssues']);
    Route::get('/team-issues', [IssueController::class, 'teamIssues']);

    // sidebar counts
    Route::get('/sidebar-stats', [SidebarController::class, 'stats']);

    // for user role
    Route::get('/user-dashboard-stats', [UserDashboardController::class, 'stats']);
    Route::get('/my-recent-issues', [UserDashboardController::class, 'recentIssues']);

    // for admin role
    Route::get('/admin-dashboard-stats', [AdminController::class, 'stats']);
    Route::get('/recent-issues', [AdminController::class, 'recentIssues']);

    // for notification
    Route::get('/notifications/{userId}', [NotificationController::class, 'getAllForUser']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // for comment
    Route::get('/issues/{issue}/comments', [CommentController::class, 'index']);
    Route::post('/issues/{issue}/comments', [CommentController::class, 'store']);

    // for reporting
    Route::get('/issue-report', [ReportController::class, 'issueReport']);

});
