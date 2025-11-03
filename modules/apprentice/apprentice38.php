<?php 

class apprentice38 extends apprentice
{
    public $skill = MAS;
    public $virtue = -1;
    
    public function getCostReduction($location)
    {
        if($location == "blackmarketa"
            || $location == "blackmarketb"
            || $location == "blackmarketc")
        {
            return SI;
        }
        return 0;
    }
}