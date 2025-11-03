<?php 

class apprentice4 extends apprentice
{
    public $skill = CAR;
    
    
    public function getCostReduction($location)
    {
        if($location == "towncenter")
        {
            return SI;
        }
        return 0;
    }
}