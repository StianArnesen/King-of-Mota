<?php

class WarInfo
{
    public $MAX_DIFFERENCE_IN_SCORE;

    public $RANK_BRONZE_SCORE;
    public $RANK_SILVER_SCORE;
    public $RANK_GOLD_SCORE;

    public $WAR_PREP_TIME;

    public function __construct()
    {
        $this->MAX_DIFFERENCE_IN_SCORE = 15;
        $this->RANK_BRONZE_SCORE = 5;
        $this->RANK_SILVER_SCORE = 25;
        $this->RANK_GOLD_SCORE = 55;

        $this->WAR_PREP_TIME = 100;
    }
    public function verifyRanks($SCORE_CREW_A, $SCORE_CREW_B)
    {
        if($this->getLeague($SCORE_CREW_A) == $this->getLeague($SCORE_CREW_B))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getLeague($SCORE)
    {
        $RANK = 0;

        if($SCORE >= $this->RANK_BRONZE_SCORE)
        {
            $RANK = 1;
        }
        if($SCORE >= $this->RANK_SILVER_SCORE)
        {
            $RANK = 2;
        }
        if($SCORE >= $this->RANK_GOLD_SCORE)
        {
            $RANK = 3;
        }

        return $RANK;
    }
}