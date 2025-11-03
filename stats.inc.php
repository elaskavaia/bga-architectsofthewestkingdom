<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stats.inc.php
 *
 * ArchitectsOfTheWestKingdom game statistics description
 *
 */


$stats_type = array(

    // Statistics global to table
    "table" => array(

    ),
    
    // Statistics existing for each player
    "player" => array(
        
        "turns_number" => array("id"=> 10,
            "name" => totranslate("Number of turns"),
            "type" => "int" ),
        "buildings" => array("id"=> 11,
            "name" => totranslate("VP for all constructed Buildings"),
            "type" => "int" ),
        "cathedral" => array("id"=> 12,
            "name" => totranslate("VP for level of work done advancing construction on the Cathedral"),
            "type" => "int" ),
        "virtue" => array("id"=> 13,
            "name" => totranslate("VP for final position on the Virtue Track"),
            "type" => "int" ),
        "debt" => array("id"=> 14,
            "name" => totranslate("VP for unpaid Debt."),
            "type" => "int" ),
        "resource" => array("id"=> 15,
            "name" => totranslate("1 VP for each Gold and 1 VP for each Marble on their Player Board."),
            "type" => "int" ),
        "silver" => array("id"=> 16,
            "name" => totranslate("1 VP for each set of 10 Silver on their Player Board."),
            "type" => "int" ),
        "prison" => array("id"=> 17,
            "name" => totranslate("VP for every 2 Workers in Prison"),
            "type" => "int" ),
    

    )

);
