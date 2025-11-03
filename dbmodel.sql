
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

ALTER TABLE `player` ADD `res1` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res2` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res3` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res4` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res5` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `res6` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `cathedral` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `virtue` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `player` ADD `type` INT UNSIGNED NOT NULL DEFAULT '7';

 CREATE TABLE IF NOT EXISTS `worker` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `location` varchar(50) NOT NULL DEFAULT 'reserve',
   `location_arg` int(2) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
  
 CREATE TABLE IF NOT EXISTS `debt` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   `paid` INT UNSIGNED NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
 
 CREATE TABLE IF NOT EXISTS `apprentice` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  `bonus` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 
 CREATE TABLE IF NOT EXISTS `building` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(32) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 

CREATE TABLE IF NOT EXISTS `blackmarket` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `reward` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pending` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NULL,  
  `function` varchar(50) NULL,
  `arg` varchar(50) NULL,  
  `arg2` varchar(50) NULL,
  `arg3` varchar(50) NULL,
  `arg4` varchar(50) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;