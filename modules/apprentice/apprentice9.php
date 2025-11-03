<?php 

class apprentice9 extends apprentice
{
    public $skill = MAS;
    public $virtue = -1;
    
    public function addPending($location)
    {
        if($location == "blackmarketreset")
        {
            $sql = "SELECT * from worker where location = 'prison' and player_id = {$this->player_id} LIMIT 2";
            $prisonners = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb( $sql );
            
            $target = "reserve_".$this->player_id;
            
            foreach($prisonners as $worker)
            {
                ArchitectsOfTheWestKingdom::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => "worker_".$worker['id'],
                    'parent' => "{$target}",
                    'position' => 'last'
                        ) );
                self::DbQuery("update worker set location = '{$target}' where id = {$worker['id']}");
            }
            
            $this->player_name = "";
            if($this->player_id != null)
            {
                $this->player_name = self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id = {$this->player_id}");;
            }
            
            $nbmeeplesLeft = self::getUniqueValueFromDB( "select count(*) from worker where player_id = {$this->player_id} and  location like 'reserve%'");
            ArchitectsOfTheWestKingdom::$instance->notifyAllPlayers( "counter", clienttranslate('${player_name} releases up to 2 workers from prison'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'id' => "res_".$this->player_id."_8",
                'nb' => $nbmeeplesLeft
            ) );
        }
    }
}