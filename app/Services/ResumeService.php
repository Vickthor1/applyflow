<?php

namespace App\Services;

class ResumeService
{

    public function generate($user,$job)
    {

        $resume = "
        {$user->name}

        Desenvolvedor Backend

        Bio:
        {$user->bio}

        Experiência:
        {$user->experience}

        Skills relevantes:
        " . implode(', ', $user->skills ?? []) . "

        Vaga: {$job->title} at {$job->company}
        ";

        $path = storage_path("resumes/{$user->id}_{$job->id}.txt");

        file_put_contents($path,$resume);

        return $path;
    }

    public function generateHTML($user, $job = null)
    {

        $skills = is_array($user->skills) ? $user->skills : [];

        $html = "
        <h1>{$user->name}</h1>

        <p><strong>Email:</strong> {$user->email}</p>

        <h2>Sobre Mim</h2>
        <p>{$user->bio}</p>

        <h2>Experiência</h2>
        <p>{$user->experience}</p>

        <h2>Skills</h2>
        <ul>";

        foreach($skills as $skill){
            $html .= "<li>{$skill}</li>";
        }

        $html .= "</ul>";

        if($job){
            $html .= "
            <h2>Vaga Aplicada</h2>
            <p>{$job->title} - {$job->company}</p>
            ";
        }

        return $html;
    }

    public function saveHTML($user, $job = null)
    {

        $html = $this->generateHTML($user,$job);

        $filename = $job
            ? "resume_{$user->id}_{$job->id}.html"
            : "resume_{$user->id}.html";

        $path = storage_path("resumes/".$filename);

        if(!is_dir(storage_path("resumes"))){
            mkdir(storage_path("resumes"),0755,true);
        }

        file_put_contents($path,$html);

        return $path;
    }

}