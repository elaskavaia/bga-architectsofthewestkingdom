<?php 

class apprentice31 extends apprentice
{
    public $skill = TIL;
    public $virtue = -1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "blackmarketa"
            || $location == "blackmarketb"
            || $location == "blackmarketc")
        {
            return 2*C;
        }
        return 0;
    }
}