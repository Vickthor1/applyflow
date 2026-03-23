<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\LocationService;

class JobScraperService
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function search($keywords)
    {
        // Se for string, converter para array
        if (is_string($keywords)) {
            $keywords = array_map('trim', explode(',', $keywords));
        }

        $allJobs = [];

        // Para cada keyword, buscar em múltiplas fontes
        foreach ($keywords as $keyword) {
            $jobs = $this->searchInAllSources($keyword);
            $allJobs = array_merge($allJobs, $jobs);
        }

        // Remover duplicatas baseadas em título e empresa
        $uniqueJobs = [];
        $seen = [];
        foreach ($allJobs as $job) {
            $key = $job['title'] . '|' . $job['company_name'];
            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $uniqueJobs[] = $job;
            }
        }

        return $uniqueJobs;
    }

    private function searchInAllSources($keyword)
    {
        $jobs = [];

        // 1. Remotive API
        try {
            $url = "https://remotive.com/api/remote-jobs?search=" . urlencode($keyword);
            $response = Http::withOptions(['verify' => false])->timeout(10)->get($url);
            if ($response->successful()) {
                $remotiveJobs = $response->json()["jobs"] ?? [];
                foreach ($remotiveJobs as $job) {
                    $locationData = $this->processLocation($job["candidate_required_location"] ?? "Remote");
                    $language = $this->detectJobLanguage($job, $locationData);

                    $jobs[] = [
                        "title" => $job["title"] ?? "N/A",
                        "company_name" => $job["company_name"] ?? "N/A",
                        "description" => $job["description"] ?? "",
                        "candidate_required_location" => $job["candidate_required_location"] ?? "Remote",
                        "url" => $job["url"] ?? "#",
                        "salary" => $job["salary"] ?? null,
                        "job_type" => $job["job_type"] ?? "full-time",
                        "source" => "Remotive",
                        "latitude" => $locationData['latitude'],
                        "longitude" => $locationData['longitude'],
                        "city" => $locationData['city'],
                        "country" => $locationData['country'],
                        "language" => $language
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error silently
        }

        // 2. The Muse API
        try {
            $url = "https://www.themuse.com/api/public/jobs?page=1&category=" . urlencode($keyword);
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $museJobs = $response->json()["results"] ?? [];
                foreach ($museJobs as $job) {
                    $locationString = implode(", ", array_column($job["locations"] ?? [], "name")) ?: "N/A";
                    $locationData = $this->processLocation($locationString);
                    $language = $this->detectJobLanguage($job, $locationData);

                    $jobs[] = [
                        "title" => $job["name"] ?? "N/A",
                        "company_name" => $job["company"]["name"] ?? "N/A",
                        "description" => $job["contents"] ?? "",
                        "candidate_required_location" => $locationString,
                        "url" => $job["refs"]["landing_page"] ?? "#",
                        "salary" => null,
                        "job_type" => "full-time",
                        "source" => "The Muse",
                        "latitude" => $locationData['latitude'],
                        "longitude" => $locationData['longitude'],
                        "city" => $locationData['city'],
                        "country" => $locationData['country'],
                        "language" => $language
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log error silently
        }

        // 3. Adzuna API (mock - em produção, precisaria de chave API)
        try {
            // Simulando busca no Adzuna (gratuito para desenvolvimento limitado)
            $url = "https://api.adzuna.com/v1/api/jobs/br/search/1?app_id=demo&app_key=demo&what=" . urlencode($keyword);
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $adzunaJobs = $response->json()["results"] ?? [];
                foreach ($adzunaJobs as $job) {
                    $jobs[] = [
                        "title" => $job["title"] ?? "N/A",
                        "company_name" => $job["company"]["display_name"] ?? "N/A",
                        "description" => $job["description"] ?? "",
                        "candidate_required_location" => $job["location"]["display_name"] ?? "N/A",
                        "url" => $job["redirect_url"] ?? "#",
                        "salary" => $job["salary_min"] ? "$" . $job["salary_min"] . " - $" . $job["salary_max"] : null,
                        "job_type" => "full-time",
                        "source" => "Adzuna"
                    ];
                }
            }
        } catch (\Exception $e) {
            // Adzuna pode falhar sem chave válida
        }

        // 4. JSearch API (mock - RapidAPI)
        try {
            // Em produção: usar RapidAPI key
            $mockJobs = [
                [
                    "title" => "Senior " . $keyword . " Developer",
                    "company_name" => "Tech Corp",
                    "description" => "Looking for experienced " . $keyword . " developer",
                    "candidate_required_location" => "Remote",
                    "url" => "https://example.com/job1",
                    "salary" => "$80,000 - $120,000",
                    "job_type" => "full-time",
                    "source" => "JSearch"
                ],
                [
                    "title" => $keyword . " Engineer",
                    "company_name" => "Startup Inc",
                    "description" => "Join our team as " . $keyword . " engineer",
                    "candidate_required_location" => "São Paulo, Brazil",
                    "url" => "https://example.com/job2",
                    "salary" => "$60,000 - $90,000",
                    "job_type" => "full-time",
                    "source" => "JSearch"
                ]
            ];
            $jobs = array_merge($jobs, $mockJobs);
        } catch (\Exception $e) {
            // Fallback
        }

        // 5. Indeed API (mock - em produção, usar Indeed Publisher API)
        try {
            // Simulando busca no Indeed (gratuito limitado)
            $mockIndeedJobs = [
                [
                    "title" => $keyword . " Developer",
                    "company_name" => "Indeed Corp",
                    "description" => "Full-time " . $keyword . " developer position with competitive salary",
                    "candidate_required_location" => "New York, NY",
                    "url" => "https://indeed.com/viewjob?jk=123456",
                    "salary" => "$70,000 - $100,000",
                    "job_type" => "full-time",
                    "source" => "Indeed"
                ],
                [
                    "title" => "Junior " . $keyword . " Specialist",
                    "company_name" => "Tech Solutions Inc",
                    "description" => "Entry-level position for " . $keyword . " enthusiasts",
                    "candidate_required_location" => "San Francisco, CA",
                    "url" => "https://indeed.com/viewjob?jk=789012",
                    "salary" => "$50,000 - $70,000",
                    "job_type" => "full-time",
                    "source" => "Indeed"
                ]
            ];
            $jobs = array_merge($jobs, $mockIndeedJobs);
        } catch (\Exception $e) {
            // Indeed fallback
        }

        // 6. Glassdoor API (mock - em produção, usar Glassdoor API)
        try {
            // Simulando busca no Glassdoor
            $mockGlassdoorJobs = [
                [
                    "title" => "Senior " . $keyword . " Engineer",
                    "company_name" => "Glassdoor Tech",
                    "description" => "Senior level position requiring 5+ years of " . $keyword . " experience",
                    "candidate_required_location" => "Austin, TX",
                    "url" => "https://glassdoor.com/job-123456",
                    "salary" => "$90,000 - $130,000",
                    "job_type" => "full-time",
                    "source" => "Glassdoor"
                ],
                [
                    "title" => $keyword . " Analyst",
                    "company_name" => "Data Insights Ltd",
                    "description" => "Business analyst role with " . $keyword . " focus",
                    "candidate_required_location" => "Chicago, IL",
                    "url" => "https://glassdoor.com/job-789012",
                    "salary" => "$65,000 - $85,000",
                    "job_type" => "full-time",
                    "source" => "Glassdoor"
                ]
            ];
            $jobs = array_merge($jobs, $mockGlassdoorJobs);
        } catch (\Exception $e) {
            // Glassdoor fallback
        }

        // 7. LinkedIn Jobs (mock - em produção, usar LinkedIn API ou scraping ético)
        try {
            // Simulando busca no LinkedIn Jobs
            $mockLinkedInJobs = [
                [
                    "title" => "Lead " . $keyword . " Developer",
                    "company_name" => "LinkedIn Corp",
                    "description" => "Lead developer position at LinkedIn with equity package",
                    "candidate_required_location" => "Sunnyvale, CA",
                    "url" => "https://linkedin.com/jobs/view/lead-developer-123",
                    "salary" => "$120,000 - $180,000",
                    "job_type" => "full-time",
                    "source" => "LinkedIn"
                ],
                [
                    "title" => $keyword . " Consultant",
                    "company_name" => "Professional Services LLC",
                    "description" => "Consulting role helping clients with " . $keyword . " solutions",
                    "candidate_required_location" => "Boston, MA",
                    "url" => "https://linkedin.com/jobs/view/consultant-456",
                    "salary" => "$85,000 - $115,000",
                    "job_type" => "full-time",
                    "source" => "LinkedIn"
                ]
            ];
            $jobs = array_merge($jobs, $mockLinkedInJobs);
        } catch (\Exception $e) {
            // LinkedIn fallback
        }

        return $jobs;
    }

    /**
     * Processar localização e obter coordenadas geográficas
     */
    private function processLocation($locationString)
    {
        // Valores padrão para remote/unknown
        if (empty($locationString) || $locationString === 'Remote' || $locationString === 'N/A') {
            return [
                'latitude' => null,
                'longitude' => null,
                'city' => null,
                'country' => null
            ];
        }

        // Tentar geocodificar a localização
        $locationData = $this->locationService->geocodeLocation($locationString);

        if ($locationData) {
            return $locationData;
        }

        // Fallback: tentar extrair cidade e país da string
        return $this->parseLocationString($locationString);
    }

    /**
     * Detectar idioma da vaga baseado na localização e conteúdo
     */
    private function detectJobLanguage($job, $locationData)
    {
        // Primeiro tentar detectar pelo país
        if ($locationData['country']) {
            $detectedLanguage = $this->locationService->detectLanguageFromLocation($locationData['country'], $locationData['city']);
            if ($detectedLanguage !== 'en') {
                return $detectedLanguage;
            }
        }

        // Verificar se a descrição contém palavras em português ou espanhol
        $description = strtolower($job['description'] ?? '');

        $portugueseKeywords = ['desenvolvedor', 'programador', 'engenheiro', 'sênior', 'júnior', 'experiência', 'conhecimento'];
        $spanishKeywords = ['desarrollador', 'programador', 'ingeniero', 'senior', 'junior', 'experiencia', 'conocimiento'];

        foreach ($portugueseKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'pt';
            }
        }

        foreach ($spanishKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'es';
            }
        }

        // Default para inglês
        return 'en';
    }

    /**
     * Parse manual de string de localização quando geocoding falha
     */
    private function parseLocationString($locationString)
    {
        $parts = array_map('trim', explode(',', $locationString));

        $city = null;
        $country = null;

        if (count($parts) >= 2) {
            $city = $parts[0];
            $country = $parts[1];
        } elseif (count($parts) === 1) {
            // Tentar identificar se é cidade ou país
            $singlePart = $parts[0];

            // Lista de países conhecidos
            $countries = ['Brazil', 'United States', 'Spain', 'Portugal', 'Mexico', 'Argentina', 'Colombia', 'Chile', 'Peru'];

            if (in_array($singlePart, $countries)) {
                $country = $singlePart;
            } else {
                $city = $singlePart;
            }
        }

        return [
            'latitude' => null,
            'longitude' => null,
            'city' => $city,
            'country' => $country
        ];
    }

}