namespace App\Jobs;

use App\Services\JobScraperService;
use App\Models\Job;

class SearchJobs
{
    public function handle(JobScraperService $scraper)
    {

        $jobs = $scraper->search("php");

        foreach ($jobs as $job) {

            Job::updateOrCreate(

                ["link" => $job["url"]], // evita duplicar vagas

                [
                    "title" => $job["title"] ?? "Sem título",

                    "company" => $job["company_name"] ?? "Empresa desconhecida",

                    "description" => $job["description"] ?? "",

                    "location" => $job["candidate_required_location"] ?? null,

                    "job_type" => $job["job_type"] ?? "remote",

                    "salary" => $job["salary"] ?? null,

                    "source" => "remotive",

                    "match_score" => rand(60,95),

                    "link" => $job["url"]
                ]
            );

        }

    }
}