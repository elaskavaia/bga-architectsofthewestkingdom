<?php 

for($i = 1; $i<=43;$i++)
{
    include("building/building{$i}.php");    
}

class building extends APP_GameClass
{
    public $type = 0;
    public $vp = 0;
    public $cost = 0;
    public $requirement = 0;
    public $virtue = 0;
    public $player_id = 0;
        
    public function __construct()
    {
        $this->type = (int) filter_var(get_class($this), FILTER_SANITIZE_NUMBER_INT);
    }
    
    public static function instantiate($building)
    {
        $class = "building".$building['card_type'];
        $obj = new $class();
        $obj->card_id = $building['card_id'];
        $obj->player_id = (int) filter_var($building['card_location'], FILTER_SANITIZE_NUMBER_INT);
        return $obj;
    }
    
    public function instant(ARCPlayer $player)
    {
        
    }
    
    public function instantFinal($player)
    {
        
    }
    
    public function getExtraVP()
    {
        return 0;
    }
}