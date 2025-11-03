<?php 

class apprentice12 extends apprentice
{
    public $skill = TIL;
    public function getAdditionalBonus($location)
    {
        if($location == "silversmith")
        {
            return 1;
        }
        return 0;
    }
}