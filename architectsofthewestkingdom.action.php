<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * architectsofthewestkingdom.action.php
 *
 * ArchitectsOfTheWestKingdom main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/architectsofthewestkingdom/architectsofthewestkingdom/myAction.html", ...)
 *
 */
  
  
  class action_architectsofthewestkingdom extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "architectsofthewestkingdom_architectsofthewestkingdom";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
  	
  	public function actSelect()
  	{
  	    self::setAjaxMode();
  	    
  	    $arg1 = self::getArg( "arg1", AT_alphanum );
  	    $arg2 = self::getArg( "arg2", AT_alphanum );
  	    
  	    $this->game->actSelect( $arg1, $arg2 );
  	    
  	    self::ajaxResponse( );
  	}

  }
  

