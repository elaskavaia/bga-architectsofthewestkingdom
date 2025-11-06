define(["dojo", "dojo/_base/declare"], function (dojo, declare) {
  return declare("bgagame.archelper", null, {
    constructor: function () {
      this.buttons = {
        Confirm: _("Confirm"),
        Skip: _("Skip"),
        Pass: _("Pass"),
        Finish: _("Finish"),
        Discard: _("Discard"),
        Undo: _("Undo"),
      };

      this.boards = {
        guildhall: _("Guildhall"),
        cathedral: _("Cathedral"),
        kingsstorehouse: _("King's Storehouse"),
        storehouse: _("King's Storehouse"),
        mines: _("Mines"),
        taxstand: _("Tax Stand"),
        blackmarket: _("Black Market"),
        blackmarketa: _("Black Market"),
        blackmarketb: _("Black Market"),
        blackmarketc: _("Black Market"),
        silversmith: _("Silversmith"),
        towncenter: _("Town Centre"),
        workshop: _("Workshop"),
        forest: _("Forest"),
        quarry: _("Quarry"),
        guardhouse: _("Guardhouse"),
        peradditional: _("Per additional"),
      };

      this.skills = {
        0: _("No skills"),
        1: _("Carpentry"),
        2: _("Tiling"),
        3: _("Carpentry and Tiling"),
        4: _("Masonry"),
        5: _("Carpentry and Masonry"),
        6: _("Tiling and Masonry"),
        7: _("All three skills"),
      };

      this.tooltips = {
        actmines1: _("Gain 1 Clay per Worker, plus 1 additional Clay for each Worker"),
        actmines2: _("Gain 1 Gold for every 2 Workers"),
        actquarry: _("Gain 1 Stone per Worker"),
        actforest: _("Gain 1 Wood per Worker"),
        actsilversmith: _("Gain 1 Silver, plus 1 additional Silver for each Worker"),
        actstorehouse: _(
          "This location allows players to take up to 1 action for each Worker they have there. The available actions are to trade any 2 pictured resources (Clay, Wood or Stone) for 1 Virtue, or any 3 pictured resources (Wood or Stone) for 1 Marble. There are also several Apprentices who will allow their owner to take further actions here. Players may take the same action multiple times if they choose."
        ),
        actworkshop1: _(
          "When placing their first Worker in the Workshop, players can only hire an Apprentice from the left-most column. If placing their second Worker, they could hire from the first or second column. Therefore, if they place their fourth Worker, they can hire any of the available Apprentices. There is way to hire Apprentices with fewer Workers than required. Players may place 1 Silver on an Apprentice Card to skip it. Any Silver placed must always be placed on the left-most card of a row, followed by the next card in the row if skipping more than 1 card."
        ),
        actworkshop2: _(
          "When choosing this action, the current player gains Building Cards from the top of the Building Card Pile. The number of Building Cards they gain is always 1, plus 1 additional card for every 2 Workers they have at the Workshop."
        ),
        acttowncenter: _(
          "The Town Centre allows players to recruit locals to capture Workers from any of the 'large open circle' locations on the Main Board. For each Worker a player has at the Town Centre, they may spend 1 Silver to capture 1 group of Workers (of a single colour) from 1 location (up to 2 locations for 2-3 player game)"
        ),
        acttaxstand: _(
          "This location allows players to take all the Silver from above the Tax Stand and place it into their own supply. However, this will cause them to lose 2 Virtue. Players are not allowed to place a Worker here if there is no Tax to steal."
        ),
        actblackmarketa: _("Pay 1 Silver and lose 1 Virtue to gain resources below"),
        actblackmarketb1: _("Pay 2 Silver and lose 1 Virtue to Hire any faceup Apprentice"),
        actblackmarketb2: _(
          "Pay 2 Silver and lose 1 Virtue to Draw 5 Building Cards, keeping 1 and discarding the other 4 to the bottom of the Building Card Pile."
        ),
        actblackmarketc: _("Pay 3 Silver and lose 1 Virtue to gain resources below"),
        actguardhouse1: _("Send all captured Workers from their Player Board to the Prison, gaining 1 Silver for each"),
        actguardhouse2: _("Release all their own Workers from Prison, placing them back onto their Player Board"),
        actguardhouse3: _(
          "Pay 5 Silver (2 Tax), or take 1 Debt and lose 1 Virtue to release all their own Workers from other players' Boards, placing them back onto their Player Board."
        ),
        actguardhouse4: _("Pay 6 Silver (3 Tax) to pay off 1 Debt."),
        actguildhall: _(
          "The Guildhall is where players place Workers to construct either a Building from their hand, or advance work on the Cathedral"
        ),
        actcathedral: _(
          "1. Pay the indicated resources to the left of the Cathedral, 1 level above where their Player Marker currently sits (all players start below the lowest level).<br/> 2. Discard any 1 Building Card from their hand, facedown to the bottom of the Building Card Pile.<br/> 3. Move their Player Marker 1 level up the Cathedral.<br/> 4. Reveal the top card from the Reward Card Pile and gain its benefits. Once resolved, Reward Cards are removed from play."
        ),
        rewardcpt: _(
          "While Advancing work on the cathedral, Reveal the top card from this Reward Card Pile and gain its benefits. Digit is the number of Reward Card left"
        ),
        buildingstack: _("Building cards deck"),
        bmreset: _(
          "<b>Black Market Reset :</b><br/>This is triggered when a third Worker is placed in the Black Market, or when a Worker is placed on either of the left-most spaces on the bottom 2 rows of the Guildhall. Remember to send all Workers from the Black Market to Prison and refresh the Black Market Cards. Players with 3 or more Workers in Prison lose 1 Virtue. The player, or players with the most Workers in Prison each gain 1 Debt Card."
        ),
      };

      this.apprentices = {
        1: {
          name: _("Acolyte"),
          target: _(""),
          tooltip: _("When advancing work on the Cathedral, also gain the reward shown on the right."),
          skill: 1,
        },
        2: {
          name: _("Acolyte"),
          target: _(""),
          tooltip: _("When advancing work on the Cathedral, also gain the reward shown on the right."),
          skill: 2,
        },
        3: {
          name: _("Acolyte"),
          target: _(""),
          tooltip: _("When advancing work on the Cathedral, also gain the reward shown on the right."),
          skill: 1,
        },
        4: {
          name: _("Conspirator"),
          target: _("TOWN CENTRE"),
          tooltip: _("Spend 1 less Silver (not Tax) than required when making captures at the Town Centre."),
          skill: 1,
        },
        5: {
          name: _("Conspirator"),
          target: _("TOWN CENTRE"),
          tooltip: _("Spend 1 less Silver (not Tax) than required when making captures at the Town Centre."),
          skill: 1,
        },
        6: {
          name: _("Debt Collector"),
          target: _(""),
          tooltip: _("When paying off a Debt, also gain the additional resources shown on the right."),
          skill: 2,
        },
        7: {
          name: _("Debt Collector"),
          target: _(""),
          tooltip: _("When paying off a Debt, also gain the additional resources shown on the right."),
          skill: 4,
        },
        8: {
          name: _("Debt Collector"),
          target: _(""),
          tooltip: _("When paying off a Debt, also gain the additional resources shown on the right."),
          skill: 1,
        },
        9: {
          name: _("Gatekeeper"),
          target: _(""),
          tooltip: _("This player may release 2 of their Workers from Prison everytime there is a Black Market Reset."),
          skill: 4,
        },
        10: {
          name: _("Illusionist"),
          target: _("BLACK MARKET"),
          tooltip: _("Do not lose Virtue when taking actions at the Black Market."),
          skill: 1,
        },
        11: {
          name: _("Jeweller"),
          target: _("SILVERSMITH"),
          tooltip: _("When taking an action to collect Silver at the Silversmith, also gain 1 additional Silver."),
          skill: 2,
        },
        12: {
          name: _("Jeweller"),
          target: _("SILVERSMITH"),
          tooltip: _("When taking an action to collect Silver at the Silversmith, also gain 1 additional Silver."),
          skill: 2,
        },
        13: { name: _("Labourer"), target: _(""), tooltip: _("This Apprentice has all 3 skill types."), skill: 7 },
        14: {
          name: _("Merchant"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the Silver on the left to gain the resources shown on the right."
          ),
          skill: 2,
        },
        15: {
          name: _("Merchant"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the Silver on the left to gain the resources shown on the right."
          ),
          skill: 4,
        },
        16: {
          name: _("Merchant"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the Silver on the left to gain the resources shown on the right."
          ),
          skill: 2,
        },
        17: {
          name: _("Merchant"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the Silver on the left to gain the resources shown on the right."
          ),
          skill: 4,
        },
        18: {
          name: _("Merchant"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the Silver on the left to gain the resources shown on the right."
          ),
          skill: 1,
        },
        19: {
          name: _("Miner"),
          target: _("MINES"),
          tooltip: _("When taking an action to collect either Clay or Gold at the Mines, also gain 1 additional Clay."),
          skill: 2,
        },
        20: {
          name: _("Miner"),
          target: _("MINES"),
          tooltip: _("When taking an action to collect either Clay or Gold at the Mines, also gain 1 additional Clay."),
          skill: 2,
        },
        21: {
          name: _("Patron"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain the Virtue shown on the right."
          ),
          skill: 2,
        },
        22: {
          name: _("Patron"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain the Virtue shown on the right."
          ),
          skill: 4,
        },
        23: {
          name: _("Patron"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain the Virtue shown on the right."
          ),
          skill: 4,
        },
        24: {
          name: _("Patron"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain the Virtue shown on the right."
          ),
          skill: 1,
        },
        25: { name: _("Pickpocket"), target: _("TAX STAND"), tooltip: _("Gain 1 Gold when stealing from the Tax Stand."), skill: 2 },
        26: {
          name: _("Squire"),
          target: _(""),
          tooltip: _("They gain the reward shown on the right, if they have no Workers in Prison during each Black Market Reset."),
          skill: 2,
        },
        27: {
          name: _("Squire"),
          target: _(""),
          tooltip: _("They gain the reward shown on the right, if they have no Workers in Prison during each Black Market Reset."),
          skill: 4,
        },
        28: {
          name: _("Squire"),
          target: _(""),
          tooltip: _("They gain the reward shown on the right, if they have no Workers in Prison during each Black Market Reset."),
          skill: 1,
        },
        29: {
          name: _("Stonecutter"),
          target: _("QUARRY"),
          tooltip: _("When taking an action to collect Stone at the Quarry, also gain 1 additional Stone."),
          skill: 4,
        },
        30: {
          name: _("Stonecutter"),
          target: _("QUARRY"),
          tooltip: _("When taking an action to collect Stone at the Quarry, also gain 1 additional Stone."),
          skill: 4,
        },
        31: {
          name: _("Swindler"),
          target: _("BLACK MARKET"),
          tooltip: _("When taking an action at the Black Market, also gain the additional resources shown."),
          skill: 2,
        },
        32: {
          name: _("Swindler"),
          target: _("BLACK MARKET"),
          tooltip: _("When taking an action at the Black Market, also gain the additional resources shown."),
          skill: 4,
        },
        33: {
          name: _("Swindler"),
          target: _("BLACK MARKET"),
          tooltip: _("When taking an action at the Black Market, also gain the additional resources shown."),
          skill: 1,
        },
        34: { name: _("Thief"), target: _("TAX STAND"), tooltip: _("Lose 1 less Virtue when stealing from the Tax Stand."), skill: 1 },
        35: {
          name: _("Trader"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain those shown on the right."
          ),
          skill: 2,
        },
        36: {
          name: _("Trader"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain those shown on the right."
          ),
          skill: 4,
        },
        37: {
          name: _("Trader"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _(
            "As an action at the King's Storehouse, players may spend the resources on the left to gain those shown on the right."
          ),
          skill: 1,
        },
        38: {
          name: _("Trickster"),
          target: _("BLACK MARKET"),
          tooltip: _("Pay 1 less Silver than required when taking actions at the Black Market."),
          skill: 4,
        },
        39: {
          name: _("Woodcutter"),
          target: _("FOREST"),
          tooltip: _("When taking an action to collect Wood at the Forest, also gain 1 additional Wood."),
          skill: 1,
        },
        40: {
          name: _("Woodcutter"),
          target: _("FOREST"),
          tooltip: _("When taking an action to collect Wood at the Forest, also gain 1 additional Wood."),
          skill: 1,
        },
        41: { name: _("Enforcer"), target: _(""), tooltip: _("Gain 1 Gold when sending all captured Workers to Prison."), skill: 2 },
        42: {
          name: _("Overseer"),
          target: _("KING'S STOREHOUSE"),
          tooltip: _("Always treat the King's Storehouse as if you had 1 additional Worker there."),
          skill: 4,
        },
        43: { name: _("Peddler"), target: _(""), tooltip: _("Gain 2 Silver on each Black Market Reset."), skill: 1 },
      };

      this.buildings = {
        1: {
          name: _("Smithy"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 0,
        },
        2: {
          name: _("Steeple"),
          most: true,
          per: false,
          tooltip: _(
            "If the controlling player has advanced work on the Cathedral more than any other player (ties don't count), they gain the reward shown on the right."
          ),
          requirement: 0,
        },
        3: {
          name: _("Stone Market"),
          most: true,
          per: false,
          tooltip: _(
            "The controlling player gains 2 additional VP if they have more of the resource shown on the left than any other player (ties don't count)."
          ),
          requirement: 4,
        },
        4: {
          name: _("Tavern"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately hires any 1 faceup Apprentice for free."),
          requirement: 1,
        },
        5: {
          name: _("Thieves' Den"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 4,
        },
        6: {
          name: _("Tiler's Hut"),
          most: false,
          per: true,
          tooltip: _("The controlling player scores 1 additional VP for each hired Apprentice they have with the indicated skill."),
          requirement: 0,
        },
        7: {
          name: _("Trading Post"),
          most: false,
          per: true,
          tooltip: _(
            "The controlling player gains 1 Virtue (before scoring) for each item or resource shown on the right, that they hold."
          ),
          requirement: 4,
        },
        8: {
          name: _("Treasury"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 2,
        },
        9: {
          name: _("University"),
          most: false,
          per: false,
          tooltip: _("The controlling player gains 2 additional VP if they have no unpaid Debts."),
          requirement: 7,
        },
        10: {
          name: _("Watchtower"),
          most: false,
          per: false,
          tooltip: _("All players with 3 or more Workers in Prison immediately lose 1 Virtue (including the controlling player)."),
          requirement: 7,
        },
        11: {
          name: _("Well"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 0,
        },
        12: {
          name: _("Wood Market"),
          most: true,
          per: false,
          tooltip: _(
            "The controlling player gains 2 additional VP if they have more of the resource shown on the left than any other player (ties don't count)."
          ),
          requirement: 1,
        },
        13: {
          name: _("Aqueduct"),
          most: false,
          per: true,
          tooltip: _("The controlling player gains 1 VP for every constructed Buildings."),
          requirement: 0,
        },
        14: {
          name: _("Armoury"),
          most: false,
          per: false,
          tooltip: _(
            "The controlling player immediately takes the action shown, in the same manner as it would be taken at the Town Centre or Guardhouse."
          ),
          requirement: 5,
        },
        15: {
          name: _("Barracks"),
          most: false,
          per: false,
          tooltip: _(
            "The controlling player immediately takes the action shown, in the same manner as it would be taken at the Town Centre or Guardhouse."
          ),
          requirement: 7,
        },
        16: {
          name: _("Betting House"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 0,
        },
        17: {
          name: _("Carpenter's Hut"),
          most: false,
          per: true,
          tooltip: _("The controlling player scores 1 additional VP for each hired Apprentice they have with the indicated skill."),
          requirement: 0,
        },
        18: {
          name: _("Castle"),
          most: false,
          per: true,
          tooltip: _(
            "The controlling player gains 1 Virtue (before scoring) for each item or resource shown on the right, that they hold."
          ),
          requirement: 7,
        },
        19: {
          name: _("Chapel"),
          most: true,
          per: false,
          tooltip: _(
            "If the controlling player has advanced work on the Cathedral more than any other player (ties don't count), they gain the reward shown on the right."
          ),
          requirement: 1,
        },
        20: {
          name: _("Church"),
          most: false,
          per: false,
          tooltip: _("The controlling player may immediately destroy 1 unpaid Debt they hold."),
          requirement: 0,
        },
        21: {
          name: _("Clay Market"),
          most: true,
          per: false,
          tooltip: _(
            "The controlling player gains 2 additional VP if they have more of the resource shown on the left than any other player (ties don't count)."
          ),
          requirement: 2,
        },
        22: {
          name: _("Clay Pit"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 0,
        },
        23: {
          name: _("Drafting Room"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 3,
        },
        24: { name: _("Dungeon"), most: false, per: false, tooltip: _("The controlling player loses 2 Virtue"), requirement: 7 },
        25: {
          name: _("Factory"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 4,
        },
        26: {
          name: _("Fortress"),
          most: false,
          per: false,
          tooltip: _(
            "The controlling player immediately takes the action shown, in the same manner as it would be taken at the Town Centre or Guardhouse."
          ),
          requirement: 7,
        },
        27: {
          name: _("Fountain"),
          most: false,
          per: false,
          tooltip: _("The controlling player loses 1 less VP for each unpaid Debt."),
          requirement: 0,
        },
        28: {
          name: _("Gambler's Den"),
          most: false,
          per: false,
          tooltip: _("The controlling player loses 1 additional VP for each unpaid Debt (3 total per unpaid Debt)."),
          requirement: 3,
        },
        29: {
          name: _("Hideout"),
          most: false,
          per: false,
          tooltip: _(
            "The controlling player immediately takes the action shown, in the same manner as it would be taken at the Town Centre or Guardhouse."
          ),
          requirement: 5,
        },
        30: {
          name: _("Keep"),
          most: false,
          per: true,
          tooltip: _("The controlling player gains 1 VP for every 3 Captured Workers."),
          requirement: 7,
        },
        31: {
          name: _("Library"),
          most: false,
          per: false,
          tooltip: _("The controlling player may immediately discard 1 chosen Building Card from their hand to gain 2 Gold."),
          requirement: 3,
        },
        32: {
          name: _("Lighthouse"),
          most: false,
          per: true,
          tooltip: _("The controlling player gains 1 VP for every 4 Virtues."),
          requirement: 2,
        },
        33: {
          name: _("Lumber Camp"),
          most: false,
          per: false,
          tooltip: _("The controlling player immediately gains the resources or cards shown."),
          requirement: 0,
        },
        34: {
          name: _("Mason's Hut"),
          most: false,
          per: true,
          tooltip: _("The controlling player scores 1 additional VP for each hired Apprentice they have with the indicated skill."),
          requirement: 0,
        },
        35: {
          name: _("Moneylender"),
          most: false,
          per: true,
          tooltip: _("The controlling player gains 1 VP for every 1 paid Debts."),
          requirement: 4,
        },
        36: {
          name: _("Monument"),
          most: false,
          per: false,
          tooltip: _("All players immediately gain 1 Virtue. (Note that the controlling player will gain a total of 2 Virtue)."),
          requirement: 6,
        },
        37: {
          name: _("Observatory"),
          most: false,
          per: true,
          tooltip: _(
            "The controlling player gains 1 Virtue (before scoring) for each item or resource shown on the right, that they hold."
          ),
          requirement: 1,
        },
        38: {
          name: _("Palace"),
          most: false,
          per: true,
          tooltip: _("The controlling player immediately gains 1 Gold for every 4 Virtue they currently have."),
          requirement: 6,
        },
        39: {
          name: _("Reservoir"),
          most: false,
          per: true,
          tooltip: _(
            "The controlling player gains 1 Virtue (before scoring) for each item or resource shown on the right, that they hold."
          ),
          requirement: 0,
        },
        40: {
          name: _("Silver Market"),
          most: true,
          per: false,
          tooltip: _(
            "The controlling player gains 2 additional VP if they have more of the resource shown on the left than any other player (ties don't count)."
          ),
          requirement: 2,
        },
        41: {
          name: _("Crane"),
          most: false,
          per: false,
          tooltip: _(
            "Immediately place another Worker in the Guildhall to either construct another Building or advance work on the Cathedral."
          ),
          requirement: 0,
        },
        42: {
          name: _("Graveyard"),
          most: false,
          per: false,
          tooltip: _("Immediately discard 1 of your hired Apprentices."),
          requirement: 7,
        },
        43: {
          name: _("Museum"),
          most: false,
          per: true,
          tooltip: _("Gain 1 Virtue at the end of the game for every 2 Buildings you have constructed."),
          requirement: 0,
        },
      };

      this.translates = {
        or: _("OR"),
        per: _("PER"),
        actionper: _("1 ACTION PER"),
        most: _("MOST"),
        yourhand: _("YOUR HAND"),
        selectCard: _("SELECT A CARD"),
      };

      this.bosses = {
        0: { name: _("Lothaire"), desc: _("Pay 1 less Wood or Stone when advancing work on the Cathedral or constructing a Building.") },
        1: {
          name: _("Therese"),
          desc: _(
            "Begins the game with 4 Workers in Prison, 5 Silver, 5 Virtue and 1 additional Building Card (after the initial draft). For all intents and purposes, she treats Marble and Gold as the same resource"
          ),
        },
        2: { name: _("Hugo"), desc: _("You may spend 2 Silver at any time to avoid taking a Debt.") },
        3: {
          name: _("Caroline"),
          desc: _(
            "Begins the game with 8 Workers in Prison, 10 Silver, 3 Virtue, 1 unpaid Debt and 1 Marble. She also gains 1 Building Card each time she takes an action at the Black Market."
          ),
        },
        4: {
          name: _("Ada"),
          desc: _(
            "Begins the game with no Workers in Prison, no Silver, 11 Virtue, 1 Gold and 1 additional Building Card (after the initial draft). She may also ignore 1 Tax on all actions throughout the game."
          ),
        },
        5: { name: _("Clovis"), desc: _("Gain 1 Building card anytime you construct a Building.") },
        6: {
          name: _("Frederick"),
          desc: _(
            "Begins the game with no Workers in Prison, 4 Silver, 7 Virtue and 1 Stone. He also does not need to discard a Building Card when advancing work on the Cathedral"
          ),
        },
        7: { name: _("Fara"), desc: _("You may release 1 of your imprisoned Worker during each Black Market Reset.") },
        8: {
          name: _("Rudolf"),
          desc: _(
            "Begins the game with no Workers in Prison, 3 Silver, 9 Virtue and 1 Wood. He also pays 1 less Silver (not tax) when hiring Apprentices in the Workshop and may hire up to 6 Apprentices."
          ),
        },
        9: { name: _("Bertha"), desc: _("Gain 2 additional Silver when sending all captured Workers to Prison.") },
        10: { name: _("Lothaire") },
        11: { name: _("Therese") },
        12: { name: _("Hugo") },
        13: { name: _("Caroline") },
        14: { name: _("Ada") },
        15: { name: _("Clovis") },
        16: { name: _("Frederick") },
        17: { name: _("Fara") },
        18: { name: _("Rudolf") },
        19: { name: _("Bertha") },
      };
    },
  });
});
