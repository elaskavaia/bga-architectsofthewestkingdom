<?php 

class apprentice7 extends apprentice
{
    public $skill = MAS;
    public function getAdditionalBonus($location)
    {
        if($location == "debtrefund")
        {
            return 2*S;
        }
        return 0;
    }
}