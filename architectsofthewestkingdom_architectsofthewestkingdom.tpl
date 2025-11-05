{OVERALL_GAME_HEADER}

<div id="hand_wrapper">
  <!-- hand area has to be outside of zoom-wrapper to float properly -->
</div>
<div id="zoom-wrapper">
<div id="gboard">
	<div id="newRound" class="popit hidden" >Black Market Reset</div>
	<div class="board" id="board">	

		<div id="zoom" class=""><i class="fa fa-search-plus" aria-hidden="true"></i></div>
		
		<div class="officialtext btext" id="txtguildhall">MAISON DES CORPORATIONS</div>
		<div class="officialtext btext" id="txtcathedral">CATHEDRALE</div>
		<div class="officialtext btext" id="txtkingsstorehouse">ENTREPOT ROYAL</div>
		<div class="officialtext btext" id="txtmines">MINES</div>
		<div class="officialtext btext" id="txttaxstand">BATIMENT DES IMPOTS</div>
		<div class="officialtext btext" id="txtblackmarket">MARCHE NOIR</div>
		<div class="officialtext btext" id="txtsilversmith">ATELIER D'ORFEVRE</div>
		<div class="officialtext btext" id="txttowncenter">HOTEL DE VILLE</div>
		<div class="officialtext btext" id="txtworkshop">ATELIER</div>
		<div class="officialtext btext" id="txtforest">FORET</div>
		<div class="officialtext btext" id="txtquarry">CARRIERE</div>
		<div class="officialtext btext" id="txtguardhouse">CORPS DE GARDE</div>
		<div class="officialtext small or" id="txtor1">OU</div>
		<div class="officialtext small or" id="txtor2">OU</div>
		<div class="officialtext small or" id="txtor3">OU</div>
		<div class="officialtext small or" id="txtor4">OU</div>
		<div class="officialtext small or" id="txtor5">OU</div>
		<div class="officialtext small or" id="txtor6">OU</div>
		<div class="officialtext small or" id="txtor7">OU</div>
		<div class="officialtext small actionper" id="txtactionper1">1 ACTION PAR</div>
		<div class="officialtext small actionper" id="txtactionper2">1 ACTION PAR</div>
		<div class="officialtext small per" id="txtper1">PAR</div>
		<div class="officialtext small per" id="txtper2">PAR</div>
		<div class="officialtext small per" id="txtper3">PAR</div>
		<div class="officialtext small per" id="txtper4">PAR</div>
		<div class="officialtext small per" id="txtper5">PAR</div>
		<div class="officialtext small per" id="txtper6">PAR</div>
		<div class="officialtext small per" id="txtper7">PAR</div>
		<div class="officialtext small" id="txtperadditional">PAR ADDITIONNEL</div>
		<div class="officialtext small most" id="txtmost">LE PLUS</div>
		
		<div class="phcard" id="phapprentice1"></div>
		<div class="phcard" id="phapprentice2"></div>
		<div class="phcard" id="phapprentice3"></div>
		<div class="phcard" id="phapprentice4"></div>
		<div class="phcard" id="phapprentice5"></div>
		<div class="phcard" id="phapprentice6"></div>
		<div class="phcard" id="phapprentice7"></div>
		<div class="phcard" id="phapprentice8"></div>
		
		<div class="phsmallcard" id="phblackmarket1"></div>
		<div class="phsmallcard" id="phblackmarket2"></div>
		
		<div id="phreward">
			<div id="rewardcpt" class="RewardCard officialtext">
				0
			</div>
		</div>
		
		<div class="building" id="buildingstack"></div>
		<div class="meeplelocation" id="prison"></div>
		<div id="guildhall"></div>
		<div class="meeplelocation" id="guardhouse"></div>
		<div class="meeplelocation" id="quarry"></div>
		<div class="meeplelocation" id="mines"></div>
		<div class="meeplelocation" id="forest"></div>
		<div class="meeplelocation" id="silversmith"></div>
		<div class="meeplelocation" id="towncenter"></div>
		<div class="meeplelocation" id="taxstand"></div>
		<div class="meeplelocation" id="storehouse"></div>
		<div class="meeplelocation" id="workshop"></div>
		<div class="meeplelocation" id="blackmarketa"></div>
		<div class="meeplelocation" id="blackmarketb"></div>	
		<div class="meeplelocation" id="blackmarketc"></div>	
		<div class="cathedralline" id="cathedral0"></div>
		<div class="cathedralline"  id="cathedral1"></div>
		<div class="cathedralline"  id="cathedral2"></div>
		<div class="cathedralline"  id="cathedral3"></div>
		<div class="cathedralline"  id="cathedral4"></div>
		<div  class="cathedralline" id="cathedral5"></div>	
		
		<div  class="remove2app" id="remove2app1"></div>
		<div  class="remove2app" id="remove2app2"></div>
		<div  class="remove2app" id="remove2app3"></div>	
		
		<div id="bmreset"></div>
		
		<div  class="virtue" id="virtue0"></div>
		<div  class="virtue" id="virtue1"></div>
		<div  class="virtue" id="virtue2"></div>
		<div  class="virtue" id="virtue3"></div>
		<div  class="virtue" id="virtue4"></div>
		<div  class="virtue" id="virtue5"></div>
		<div  class="virtue" id="virtue6"></div>
		<div  class="virtue" id="virtue7"></div>
		<div  class="virtue" id="virtue8"></div>
		<div  class="virtue" id="virtue9"></div>
		<div  class="virtue" id="virtue10"></div>
		<div  class="virtue" id="virtue11"></div>
		<div  class="virtue" id="virtue12"></div>
		<div  class="virtue" id="virtue13"></div>
		<div  class="virtue" id="virtue14"></div>
		
		<div id="taxcoins">
			<div class="officialtext taxfont" id="taxcpt">1</div>
			<div class="coin" id="taxcoin"></div>
		</div>
		
		<div  class="action" id="actguildhall"></div>
		<div  class="action" id="actcathedral"></div>
		<div  class="action" id="actmines1"></div>
		<div  class="action" id="actmines2"></div>
		<div  class="action" id="actguardhouse1"></div>
		<div  class="action" id="actguardhouse2"></div>
		<div  class="action" id="actguardhouse3"></div>
		<div  class="action" id="actguardhouse4"></div>
		<div  class="action" id="actquarry"></div>
		<div  class="action" id="actforest"></div>
		<div  class="action" id="actsilversmith"></div>
		<div  class="action" id="actstorehouse"></div>
		<div  class="action" id="acttowncenter"></div>
		<div  class="action" id="actworkshop1"></div>
		<div  class="action" id="actworkshop2"></div>
		<div  class="action" id="acttaxstand"></div>
		<div  class="action" id="actblackmarketa"></div>
		<div  class="action" id="actblackmarketb1"></div>
		<div  class="action" id="actblackmarketb2"></div>
		<div  class="action" id="actblackmarketc"></div>
		
		<div id="score" class="hidden">
			<div id="closescore" class=""><i class="fa fa-compress" aria-hidden="true"></i></div>
			<div id="scoreinside2"></div>
		</div>
	</div>


<!-- BEGIN player -->		
	<div class="playeroveroverall" id="player{PLAYER_ID}">
		<div class="playeroverall">
		<div class="overplayerboard">
			<div class="playerboard " id="playerboard{PLAYER_ID}">
				<div class="board_name officialtext" id="name{PLAYER_ID}">FREDERICK</div>
				<div class="ressourcesboard">
					<div class="resnb res_{PLAYER_ID}_1" id="res_{PLAYER_ID}_1">20</div>
					<div class="arcicon res1"></div>
					<div class="resnb res_{PLAYER_ID}_2" id="res_{PLAYER_ID}_2">20</div>
					<div class="arcicon res2"></div>
					<div class="resnb res_{PLAYER_ID}_3" id="res_{PLAYER_ID}_3">20</div>
					<div class="arcicon res3"></div>
				</div>
				<div class="ressourcesboard line2">
					<div class="resnb res_{PLAYER_ID}_4" id="res_{PLAYER_ID}_4">20</div>
					<div class="arcicon res4"></div>
					<div class="resnb res_{PLAYER_ID}_5" id="res_{PLAYER_ID}_5">20</div>
					<div class="arcicon res5"></div>
				</div>
				<div class="ressourcesboard line3">
					<div class="resnb res_{PLAYER_ID}_6" id="res_{PLAYER_ID}_6">20</div>
					<div class="arcicon res6"></div>
					<div class="resnb res_{PLAYER_ID}_7" id="res_{PLAYER_ID}_7">20</div>
					<div class="arcicon res9"></div>
					<div class="resnb res_{PLAYER_ID}_8" id="res_{PLAYER_ID}_8">20</div>
					<div class="arcicon resmeeple" id="res8_{PLAYER_ID}"></div>
					<div class="reserve" id="reserve_{PLAYER_ID}">20</div>
				</div>				
				<div class="ressourcesboard line4">
					<div class="resnb res_{PLAYER_ID}_12" id="res_{PLAYER_ID}_12">20</div>
					<div class="arcicon res12"></div>
					<div class="resnb res_{PLAYER_ID}_13" id="res_{PLAYER_ID}_13">20</div>
					<div class="arcicon res13"></div>
					<div class="resnb res_{PLAYER_ID}_14" id="res_{PLAYER_ID}_14">20</div>
					<div class="arcicon res14"></div>
				</div>
				<div class="meeplelocation pboard" id="prison_{PLAYER_ID}"></div>
			</div>
		</div>
		<div class="middle">
			<div class="pname" style="color:#{PLAYER_COLOR}">{PLAYER_NAME}</div>
			<div class="cards" id="cards{PLAYER_ID}"></div>
		</div>
		<div class="playerleft">
			<a href="#topbar"><i class="fa fa-arrow-up"></i></a>
			<a href="#player{PLAYER_BEFORE}" id="up_{PLAYER_ID}"><i class="fa fa-chevron-up"></i></a>
			<a href="#player{PLAYER_AFTER}" id="down_{PLAYER_ID}"><i class="fa fa-chevron-down"></i></a>
		</div>
	</div>
	<div id="hand_area" class="hand_area" data-open="1">
	  <div id="hand_area_buttons">
    <div id="button_hand_open" class="icon_hand hand_area_button">
      <i class="fa fa-arrow-circle-o-down icon_down" aria-hidden="true"></i>
      <i class="fa fa-arrow-circle-o-up icon_up" aria-hidden="true"></i>
    </div>
    <div id="button_hand_layout" class="icon_settle hand_area_button">
      <i class="fa fa-hand-paper-o icon_float" aria-hidden="true"></i>
      <i class="fa fa-window-maximize icon_park" aria-hidden="true"></i>
    </div>
  </div>
	<div class="lowerlane" id="lowerlane{PLAYER_ID}">
        <div class="playertablename yourhand" style="color:#{PLAYER_COLOR}">

            YOUR HAND2
        </div>
		<div class="cards" id="hand{PLAYER_ID}"></div>
	</div>
	<div class="lowerlane lowerlaneselect hidden" id="lowerlaneselect{PLAYER_ID}">
        <div class="playertablename selectCard" style="color:#{PLAYER_COLOR}">
            SELECT A CARD
        </div>
		<div class="cards" id="selectCards{PLAYER_ID}"></div>
	</div>
	</div>
</div>
<!-- END player -->
</div>

<!-- END zoom-wrapper -->
</div>

<div id="arc-overall">
</div>



<script type="text/javascript">

// Javascript HTML templates


var jstpl_view='<span id="eye_${id}">&nbsp;<a href="#player${id}"><i class="fa fa-eye"></i></a></span>';
var jstpl_res='<div class="score">\
					<div class="resnb res_${id}_1">20</div>\
					<div class="arcicon res1"></div>\
					<div class="resnb res_${id}_2">20</div>\
					<div class="arcicon res2"></div>\
					<div class="resnb res_${id}_3">20</div>\
					<div class="arcicon res3"></div>\
				</div>\
				<div class="score">\
					<div class="resnb res_${id}_4">20</div>\
					<div class="arcicon res4"></div>\
					<div class="resnb res_${id}_5">20</div>\
					<div class="arcicon res5"></div>\
				</div>\
				<div class="score">\
					<div class="resnb res_${id}_6">20</div>\
					<div class="arcicon res6"></div>\
					<div class="resnb res_${id}_7">20</div>\
					<div class="arcicon res9"></div>\
					<div class="resnb res_${id}_8">20</div>\
					<div class="arcicon resmeeple" id="res8log_${id}" style="background-position: ${posx}% 0%"></div>\
				</div>\
				<div class="score score4">\
					<div class="resnb res_${id}_12" id="res_${id}_12">20</div>\
					<div class="arcicon res12"></div>\
					<div class="resnb res_${id}_13" id="res_${id}}_13">20</div>\
					<div class="arcicon res13"></div>\
					<div class="resnb res_${id}_14" id="res_${id}_14">20</div>\
					<div class="arcicon res14"></div>\
				</div>';
				
var jstpl_debt='<div class="BMCard paid${paid}" id="debt_${id}"></div>';
var jstpl_marker='<div class="marker color${color}" id="${id}"></div>';
var jstpl_apprentice='<div class="apprentice apptype${card_type}" id="apprentice${card_id}" style="background-position: ${posx}% ${posy}%;">\
						<div class="apprentice_name officialtext">${name}</div>\
						<div class="apprentice_target officialtext">${target}</div>\
						<div class="bonus hidden" id="silverbonushead${card_id}">\
							<div class="officialtext" id="silverbonus${card_id}">${bonus}</div>\
							<div class="coin" class="taxcoin"></div>\
						</div>\
					</div>';
					
var jstpl_building='<div class="building" id="building${card_id}" style="background-position: ${posx}% ${posy}%;">\
					<div class="building_name officialtext">${name}</div>\
					<div class="building_most officialtext">${most}</div>\
					<div class="building_per officialtext">${per}</div>\
				</div>';
var jstpl_worker='<div class="meeple meeple${type}" id="worker_${id}" style="background-position: ${posx}% 0%"></div>';
var jstpl_reslog='<div id="${id}" class="arcicon res${type}"></div>';
var jstpl_resmove='<div id="resmove${id}" class="arcicon res${type} resmove"></div>';
var jstpl_bmcard='<div id="bmcard${id}" class="BMCard" style="background-position: ${posx}% ${posy}%;"></div>';
var jstpl_reward='<div id="reward${id}" class="RewardCard" style="background-position: ${posx}% ${posy}%;"></div>';
var jstpl_score ='<div class="anascore" style="left: ${left}px; top: ${top}px;">${text}</div>';
					
</script>  

{OVERALL_GAME_FOOTER}
