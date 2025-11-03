<?php 

class building34 extends building
{
    public $vp = 5;
    public $cost = 2*W+4*S;
    public $requirement = 0;
    
    public function getExtraVP()
    {
        $sql = "SELECT * from apprentice where card_location = 'cards{$this->player_id}'";
        $apprentices = ArchitectsOfTheWestKingdom::$instance->getCollectionFromDb( $sql );
        $ret = 0;
        foreach($apprentices as $apprentice)
        {
            $appobj = apprentice::instantiate($apprentice);
            if(($appobj->skill & MAS) == MAS)
            {
                $ret++;
            }
        }
        
        return $ret;
    }
}