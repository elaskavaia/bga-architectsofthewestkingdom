<?php 

class apprentice32 extends apprentice
{
    public $skill = MAS;
    public $virtue = -1;
    
    public function getAdditionalBonus($location)
    {
        if($location == "blackmarketa"
            || $location == "blackmarketb"
            || $location == "blackmarketc")
        {
            return S;
        }
        return 0;
    }
}