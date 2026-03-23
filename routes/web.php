<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

Route::get('/idioma/{locale}', function ($locale) {
    if (in_array($locale, ['pt', 'en', 'es'])) {
        session()->put('locale', $locale);
        session()->save(); // <-- Essa linha força o salvamento imediato
    }
    return redirect()->back();
});

Route::get('/meu-curriculo', function () {
    // Pega o usuário logado para mostrarmos os dados dele
    $user = auth()->user(); 
    return view('resume', compact('user'));
})->name('resume.view');
Route::middleware(['auth'])->group(function () {
    
    // Rota da IA
    Route::get('/api/analyze-job/{id}', [JobController::class, 'analyzeWithAI']);
});

Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'apiLogout']);
        Route::get('/user', [AuthController::class, 'getUser']);
    });
});

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get("/dashboard",[DashboardController::class,"index"]);
    Route::get("/profile",[ProfileController::class,"show"]);
    Route::post("/profile",[ProfileController::class,"update"]);
    Route::post("/profile/update-location",[ProfileController::class,"updateLocation"]);

    // Application tracking routes
    Route::post("/jobs/{jobId}/apply", [ApplicationController::class, 'apply']);
    Route::put("/jobs/{jobId}/status", [ApplicationController::class, 'updateStatus']);
    Route::get("/applications", [ApplicationController::class, 'getApplications']);
    Route::get("/applications/stats", [ApplicationController::class, 'getStats']);
    Route::post("/search-jobs", function() {
        $keywords = request('keywords', 'PHP,Laravel,Python');

        // Executar o comando
        $exitCode = \Artisan::call('jobs:search', [
            'keywords' => $keywords
        ]);

        $output = \Artisan::output();

        return response()->json([
            'message' => 'Job search completed',
            'keywords' => $keywords,
            'output' => $output,
            'exit_code' => $exitCode
        ]);
    });

    Route::post("/apply-jobs", function() {
        // ℹ️  Credenciais podem ser enviadas pelo formulário ou carregadas do .env
        
        $email = request('email');
        $password = request('password');
        $keyword = request('keyword', env('BOT_KEYWORD', 'php'));

        // Se credenciais vazias, usar .env
        $useConfig = empty($email) || empty($password);

        try {
            $params = [
                'email' => $useConfig ? 'config' : $email,
                'password' => $useConfig ? 'config' : $password,
                '--keyword' => $keyword
            ];
            
            // Executar o comando com keyword do formulário
            $exitCode = \Artisan::call('jobs:apply', $params);

            $output = \Artisan::output();

            return response()->json([
                'message' => '✓ Bot iniciado. Verifique os logs do bot.',
                'success' => true,
                'keyword' => $keyword,
                'using_config' => $useConfig,
                'output' => $output,
                'exit_code' => $exitCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '✗ Erro ao iniciar bot',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get("/resume", function() {
        $user = auth()->user();
        $resumeService = app(\App\Services\ResumeService::class);

        $html = $resumeService->generateHTML($user);

        return response($html)->header('Content-Type', 'text/html');
    });

    Route::get("/resume/download", function() {
        $user = auth()->user();
        $resumeService = app(\App\Services\ResumeService::class);

        $path = $resumeService->saveHTML($user);

        return response()->download($path, "curriculo_{$user->name}.html");
    });

    Route::get("/resume/{jobId}", function($jobId) {
        $user = auth()->user();
        $job = \App\Models\Job::find($jobId);
        $resumeService = app(\App\Services\ResumeService::class);

        if (!$job) {
            abort(404);
        }

        $html = $resumeService->generateHTML($user, $job);

        return response($html)->header('Content-Type', 'text/html');
    });
});
