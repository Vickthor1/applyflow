<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Exports\JobsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportExcel()
    {
        $user = auth()->user();
        return Excel::download(new JobsExport($user->id), 'jobs_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPDF()
    {
        $user = auth()->user();

        $jobs = Job::where('user_id', $user->id)->get();
        $stats = $this->getStats($user);

        $pdf = Pdf::loadView('reports.jobs_pdf', compact('jobs', 'stats', 'user'));

        return $pdf->download('jobs_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function analyticsReport()
    {
        $user = auth()->user();
        $stats = $this->getStats($user);

        $pdf = Pdf::loadView('reports.analytics_pdf', compact('stats', 'user'));

        return $pdf->download('analytics_report_' . now()->format('Y-m-d') . '.pdf');
    }

    private function getStats($user)
    {
        return [
            'total_jobs' => Job::where('user_id', $user->id)->count(),
            'applied_jobs' => Job::where('user_id', $user->id)->where('applied', true)->count(),
            'avg_match_score' => Job::where('user_id', $user->id)->avg('match_score') ?? 0,
            'jobs_by_source' => Job::where('user_id', $user->id)->selectRaw('source, COUNT(*) as count')->groupBy('source')->get(),
            'jobs_by_type' => Job::where('user_id', $user->id)->selectRaw('job_type, COUNT(*) as count')->groupBy('job_type')->get(),
            'application_stats' => [
                'applied' => Job::where('user_id', $user->id)->where('application_status', 'applied')->count(),
                'viewed' => Job::where('user_id', $user->id)->where('application_status', 'viewed')->count(),
                'interview' => Job::where('user_id', $user->id)->where('application_status', 'interview')->count(),
                'rejected' => Job::where('user_id', $user->id)->where('application_status', 'rejected')->count(),
                'hired' => Job::where('user_id', $user->id)->where('application_status', 'hired')->count(),
            ],
            'recent_applications' => Job::where('user_id', $user->id)->where('applied', true)->orderBy('applied_at', 'desc')->take(5)->get(),
        ];
    }
}
