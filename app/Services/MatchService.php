<?php

namespace App\Services;

class MatchService
{

    public function calculate($jobDescription,$skills)
    {

        $score = 0;

        foreach($skills as $skill){

            if(str_contains(strtolower($jobDescription), strtolower($skill))){

                $score += 15;

            }

        }

        return $score;

    }

}