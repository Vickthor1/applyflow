<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SearchJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:search {keywords?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for jobs using multiple keywords across various job sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keywords = $this->argument('keywords') ?? 'PHP,Laravel,Python';

        $this->info("Searching for jobs with keywords: {$keywords}");

        $scraper = app(\App\Services\JobScraperService::class);
        $matchService = app(\App\Services\MatchService::class);

        $jobs = $scraper->search($keywords);

        $this->info('Found ' . count($jobs) . ' jobs from multiple sources');

        // Usar usuário autenticado ou primeiro usuário como fallback
        $user = \App\Models\User::first(); // Para MVP, ainda usa primeiro usuário
        $skills = $user ? $user->skills : ['PHP', 'Laravel', 'MySQL'];

        foreach($jobs as $job){

            $score = $matchService->calculate($job["description"], $skills);

            \App\Models\Job::create([
                "title"=>substr($job["title"], 0, 250),
                "company"=>substr($job["company_name"], 0, 250),
                "description"=>substr($job["description"], 0, 1000),
                "location"=>$job["candidate_required_location"],
                "link"=>$job["url"],
                "match_score" => $score,
                "salary" => $job["salary"] ?? null,
                "job_type" => $job["job_type"] ?? "full-time",
                "source" => $job["source"] ?? "Unknown"
            ]);

        }

        $this->info('Jobs searched and saved.');
    }
}
