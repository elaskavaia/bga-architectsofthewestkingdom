<?php

for ($i = 1; $i <= 43; $i++) {
    include("apprentice/apprentice{$i}.php");
}

class apprentice
{
    public $type = 0;
    public $skill = 0;
    public $virtue = 0;
    public $player_id = 0;
    public $card_id = 0;

    public function __construct()
    {
        $this->type = (int) filter_var(get_class($this), FILTER_SANITIZE_NUMBER_INT);
    }

    public static function instantiate($apprentice)
    {
        $class = "apprentice" . $apprentice['card_type'];
        $obj = new $class();
        $obj->card_id = $apprentice['card_id'];
        $obj->player_id = (int) filter_var($apprentice['card_location'], FILTER_SANITIZE_NUMBER_INT);

        return $obj;
    }

    public function getAdditionalBonus($location)
    {
        return 0;
    }

    public function getCostReduction($location)
    {
        return 0;
    }

    public function addPending($location) {}

    public function getAdditionalStorehouse()
    {
        return [];
    }
}
