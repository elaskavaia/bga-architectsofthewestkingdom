<?php 

class apprentice34 extends apprentice
{
    public $skill = CAR;
    public function getCostReduction($location)
    {
        if($location == "taxstand")
        {
            return V;
        }
        return 0;
    }
}