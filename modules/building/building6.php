<?php 

class building6 extends building
{
    public $vp = 5;
    public $cost = 2*C+W+2*S;
    public $requirement = 0;
    
    public function getExtraVP()
    {
        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb( $sql );
        $ret = 0;
        foreach($apprentices as $apprentice)
        {
            $appobj = apprentice::instantiate($apprentice);            
            if(($appobj->skill & TIL) == TIL)
            {
                $ret++;
            }
        }  
        
        return $ret;
    }
}