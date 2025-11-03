<?php 

class apprentice20 extends apprentice
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