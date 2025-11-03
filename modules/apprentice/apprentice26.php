<?php 

class apprentice26 extends apprentice
{
    public $skill = TIL;
    public $virtue = 1;
    
    public function addPending($location)
    {
        if($location == "blackmarketreset")
        {
            $nbprisonners = ArchitectsOfTheWestKingdom::$instance->getUniqueValueFromDB( "SELECT count(*) from worker where location = 'prison' and player_id = {$this->player_id}" );
            if($nbprisonners == 0)
            {
                $player = new ARCPlayer($this->player_id); 
                $player->gain("apprentice".$this->card_id, null, G);
            }
        }
    }
}