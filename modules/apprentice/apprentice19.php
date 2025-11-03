<?php 

class apprentice19 extends apprentice
{
    public $skill = TIL;
    
    public function getAdditionalBonus($location)
    {
        if($location == "mines")
        {
            return C;
        }
        return 0;
    }
}