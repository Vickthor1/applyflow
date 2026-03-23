<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        $query = Job::query();

        // 🔎 Filtros básicos
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('min_salary')) {
            $query->whereRaw(
                "CAST(REPLACE(REPLACE(salary, '$', ''), ',', '') AS INTEGER) >= ?",
                [$request->min_salary]
            );
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // 🌎 Idioma
        $currentLocale = session('locale', 'pt');

        $query->where(function($q) use ($currentLocale) {
            $q->whereIn('language', [$currentLocale, 'en'])
              ->orWhereNull('language')
              ->orWhere('language', '');
        });

        // 📦 Executa query SEM distância (SQLite-safe)
        $jobs = $query->orderBy("match_score", "desc")->get();

        // 📍 Calcular distância MANUAL (PHP)
        if ($user->latitude && $user->longitude) {

            foreach ($jobs as $job) {

                if ($job->latitude && $job->longitude) {

                    $job->distance_km = $this->calculateDistance(
                        $user->latitude,
                        $user->longitude,
                        $job->latitude,
                        $job->longitude
                    );

                } else {
                    $job->distance_km = null;
                }
            }

            // 🔃 Ordenar por distância
            $jobs = $jobs->sortBy('distance_km');
        }

        // 📊 Stats
        $stats = [
            'total_jobs' => Job::count(),
            'avg_match_score' => Job::avg('match_score') ?? 0,
            'jobs_by_source' => Job::selectRaw('source, COUNT(*) as count')->groupBy('source')->get(),
            'jobs_by_type' => Job::selectRaw('job_type, COUNT(*) as count')->groupBy('job_type')->get(),
            'user_location' => [
                'city' => $user->city ?? null,
                'country' => $user->country ?? null,
                'has_coordinates' => !empty($user->latitude) && !empty($user->longitude)
            ]
        ];

        return view("dashboard", compact("jobs", "stats"));
    }

    // 📍 Função correta (fora do index)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}