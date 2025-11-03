<?php 

class apprentice6 extends apprentice
{
    public $skill = TIL;
    
    public function getAdditionalBonus($location)
    {
        if($location == "debtrefund")
        {
            return 2*C;
        }
        return 0;
    }
}