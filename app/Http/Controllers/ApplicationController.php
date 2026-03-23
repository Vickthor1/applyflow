<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function apply(Request $request, $jobId)
    {
        $user = auth()->user();
        $job = Job::findOrFail($jobId);

        // Verificar se já aplicou
        if ($job->user_id == $user->id && $job->applied) {
            return response()->json(['message' => 'Already applied to this job'], 400);
        }

        // Marcar como aplicada
        $job->update([
            'applied' => true,
            'applied_at' => now(),
            'application_status' => 'applied',
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Application marked successfully']);
    }

    public function updateStatus(Request $request, $jobId)
    {
        $request->validate([
            'status' => 'required|in:not_applied,applied,viewed,interview,rejected,hired'
        ]);

        $user = auth()->user();
        $job = Job::where('id', $jobId)->where('user_id', $user->id)->firstOrFail();

        $job->update([
            'application_status' => $request->status,
            'applied' => in_array($request->status, ['applied', 'viewed', 'interview', 'hired']),
        ]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function getApplications()
    {
        $user = auth()->user();

        $applications = Job::where('user_id', $user->id)
            ->where('applied', true)
            ->orderBy('applied_at', 'desc')
            ->get();

        return response()->json($applications);
    }

    public function getStats()
    {
        $user = auth()->user();

        $stats = [
            'total_applications' => Job::where('user_id', $user->id)->where('applied', true)->count(),
            'pending' => Job::where('user_id', $user->id)->where('application_status', 'applied')->count(),
            'viewed' => Job::where('user_id', $user->id)->where('application_status', 'viewed')->count(),
            'interviews' => Job::where('user_id', $user->id)->where('application_status', 'interview')->count(),
            'rejected' => Job::where('user_id', $user->id)->where('application_status', 'rejected')->count(),
            'hired' => Job::where('user_id', $user->id)->where('application_status', 'hired')->count(),
        ];

        return response()->json($stats);
    }
}
