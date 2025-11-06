/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ArchitectsOfTheWestKingdom implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * architectsofthewestkingdom.js
 *
 * ArchitectsOfTheWestKingdom user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", "./modules/archelper"], function (dojo, declare) {
  return declare("bgagame.architectsofthewestkingdom", ebg.core.gamegui, {
    constructor: function () {
      this.helper = new bgagame.archelper();
      this.height = dojo.marginBox("gboard").h + 580;
      this.zoomScales = [1, 0.8, 0.6, 0.4];
      this.zoomIndex = 0;
      dojo.connect(window, "onresize", this, dojo.hitch(this, "adaptViewportSize"));
    },

    adaptViewportSize: function () {
      var bodycoords = dojo.marginBox("arc-overall");
      var contentWidth = bodycoords.w;
      var rowWidth = 2000;
      var zoom = this.zoomScales[this.zoomIndex] ?? 1;

      rowWidth /= zoom;
      const zoomWrapper = $("zoom-wrapper");
      if (contentWidth >= rowWidth) {
        zoomWrapper.style.transform = undefined;
        zoomWrapper.style.height = this.height + "px";
        return;
      }

      var percentageOn1 = contentWidth / rowWidth;

      zoomWrapper.style.transform = "scale(" + percentageOn1 + ")";
      zoomWrapper.style.height = this.height * percentageOn1 + "px";
      this.writeLocalProp("zoomIndex", String(this.zoomIndex));
    },

    /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

    setup: function (gamedatas) {
      this.players = gamedatas.players;
      this.zoomIndex = parseInt(this.readLocalProp("zoomIndex", 0));
      var nbp = 0;
      // Setting up player boards
      for (var player_id in gamedatas.players) {
        nbp++;
        var player = gamedatas.players[player_id];
        const type = Math.floor((player["type"] % 10) / 2);

        player["posx"] = 25 * type;
        dojo.place(this.format_block("jstpl_res", player), $("player_board_" + player["id"]));
        for (let i = 1; i <= 8; i++) {
          dojo.query(".res_" + player["id"] + "_" + i).forEach(function (selectTag) {
            selectTag.innerHTML = player["res" + i];
          });
        }
        dojo.query(".res_" + player["id"] + "_" + 7).forEach(function (selectTag) {
          selectTag.innerHTML += "/6";
        });

        for (let i = 12; i <= 14; i++) {
          dojo.query(".res_" + player["id"] + "_" + i).forEach(function (selectTag) {
            selectTag.innerHTML = player["res" + i];
          });
        }

        dojo.place(
          this.format_block("jstpl_marker", {
            id: "cathedral_" + player["id"],
            color: player["color"],
          }),
          $("cathedral" + player["cathedral"])
        );
        dojo.place(
          this.format_block("jstpl_marker", {
            id: "virtue_" + player["id"],
            color: player["color"],
          }),
          $("virtue" + player["virtue"])
        );

        dojo.query("#player" + player["id"]).addClass("type" + player["type"]);
        const playerBoard = "playerboard" + player["id"];
        dojo.style(playerBoard, {
          "background-position": 100 * (player["type"] % 2) + "% " + 11.111 * Math.floor(player["type"] / 2) + "%",
        });
        $(playerBoard).dataset["playertype"] = player["type"];
        dojo.style("res8_" + player["id"], {
          "background-position": 25 * type + "% 0%",
        });
        var name = _(this.helper.bosses[player["type"]]["name"]);
        dojo.query("#name" + player["id"]).forEach(function (selectTag) {
          selectTag.innerHTML = name;
        });
        if (this.helper.bosses[player["type"]]["desc"] != null) {
          var html = "<b>" + name + " :</b><br/><div>" + _(this.helper.bosses[player["type"]]["desc"]) + "</div>";
          this.addTooltipHtml(playerBoard, html, 1000);
        }

        if (this.player_id != player["id"]) {
          dojo.query("#lowerlane" + player["id"]).forEach(dojo.destroy);
          dojo.query("#lowerlaneselect" + player["id"]).forEach(dojo.destroy);
        } else {
          dojo.query("#gboard").addClass("meepletype" + type);
        }

        if ($("eye_" + player["id"]) == null) {
          dojo.place(this.format_block("jstpl_view", player), dojo.query("#player_board_" + player["id"] + " .player_score")[0]);
        }
        if (this.player_id != player_id) {
          const wrapper = document.querySelector(`#player${player_id} #hand_area`);
          if (wrapper) wrapper.remove();
        }
      }

      for (var worker_id in gamedatas.workers) {
        var worker = gamedatas.workers[worker_id];
        var player = this.players[worker.player_id];
        var type = Math.floor((player["type"] % 10) / 2);
        worker["posx"] = 25 * type;
        worker["type"] = type;
        dojo.place(this.format_block("jstpl_worker", worker), $(worker.location));
      }

      for (var card_id in gamedatas.apprentices) {
        var card = gamedatas.apprentices[card_id];
        this.addApprentice(card);
      }

      for (var card_id in gamedatas.buildings) {
        var card = gamedatas.buildings[card_id];
        this.addBuilding(card);
      }

      for (var card_id in gamedatas.hand) {
        var card = gamedatas.hand[card_id];
        this.addBuilding(card);
      }

      if (gamedatas.blackmarket1 != null) {
        dojo.place(
          this.format_block("jstpl_bmcard", {
            id: gamedatas.blackmarket1.id,
            posx: (gamedatas.blackmarket1.type - 1) * 11.11,
            posy: 50,
          }),
          $("phblackmarket1")
        );
      }
      if (gamedatas.blackmarket2 != null) {
        dojo.place(
          this.format_block("jstpl_bmcard", {
            id: gamedatas.blackmarket2.id,
            posx: (gamedatas.blackmarket2.type - 1) * 11.11,
            posy: 0,
          }),
          $("phblackmarket2")
        );
      }

      if (gamedatas.rewardnb > 0) {
        dojo.query("#rewardcpt").forEach(function (selectTag) {
          selectTag.innerHTML = gamedatas.rewardnb;
        });
      } else {
        dojo.query("#rewardcpt").forEach(dojo.destroy);
      }

      dojo.query("#taxcpt").forEach(function (selectTag) {
        selectTag.innerHTML = gamedatas.tax;
      });
      dojo.query("#guildhall").addClass("gh" + nbp + "p");

      var boards = this.helper.boards;
      var translates = this.helper.translates;
      for (var trad_id in this.helper.boards) {
        dojo.query("#txt" + trad_id).forEach(function (selectTag) {
          selectTag.innerHTML = _(boards[trad_id]);
        });
      }
      for (var trad_id in this.helper.tooltips) {
        this.addTooltipHtml(trad_id, _(this.helper.tooltips[trad_id]), 1000);
      }
      dojo.query(".or").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["or"]);
      });
      dojo.query(".per").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["per"]);
      });
      dojo.query(".actionper").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["actionper"]);
      });
      dojo.query(".most").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["most"]);
      });
      dojo.query(".yourhand").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["yourhand"]);
      });
      dojo.query(".selectCard").forEach(function (selectTag) {
        selectTag.innerHTML = _(translates["selectCard"]);
      });

      if (gamedatas.score.length > 0) {
        dojo.query("#score").removeClass("hidden");
        var score = gamedatas.score;
        for (let i = 0; i < score.length; i++) {
          console.warn(score[i]);
          for (let j = 0; j < 9; j++) {
            dojo.place(
              this.format_block("jstpl_score", {
                top: 40 + 85 * j,
                left: 172 + 84 * i,
                text: score[i][j],
              }),
              "scoreinside2"
            );
          }
        }
      }

      this.addTooltipHtmlToClass(
        "remove2app",
        _(
          "When someone places a worker here, the 2 left-most Apprentices (and any coins on them) get removed from the game. Then, all of the Apprentices slide along and 2 more Apprentices get drawn from the deck."
        ),
        1000
      );
      this.addTooltipHtmlToClass(
        "virtue",
        _(
          "<b>Virtue Track</b><br/>Depending on how much Virtue they have at the game’s end, they may gain or lose Victory Points. This is equal to the numbers shown in the yellow flags next to the upper and lower levels of the Virtue Track."
        ),
        1000
      );

      // Setup game notifications to handle (see "setupNotifications" method below)
      this.setupNotifications();

      dojo.query(".action").connect("onclick", this, "onSelect");
      dojo.query(".meeple").connect("onclick", this, "onSelect");
      dojo.query("#zoom").connect("onclick", this, "onZoom");
      dojo.query("#closescore").connect("onclick", this, "onScore");

      //floating hand stuff
      if (!this.isSpectator) {
        const handArea = document.querySelector("#hand_area");
        const handOpen = this.readLocalProp("handopen", "1"); // this local setting has no ui in settings control
        handArea.dataset.open = handOpen;

        let value = this.readLocalProp("handplace", "static");
        if (value != "static") value = "floating";
        this.setLocalProperty("handplace", value);
        this.reflowHandPosition();

        handArea.querySelectorAll("#button_hand_open").forEach((node) => {
          this.connect(node, "onclick", () => {
            handArea.dataset.open = !(handArea.dataset.open == "1") ? "1" : "0";
            this.writeLocalProp("handopen", handArea.dataset.open);
          });
          this.addTooltip(node.id, "Click to open/close hand", "");
        });

        handArea.querySelectorAll(".icon_settle").forEach((node) => {
          this.connect(node, "onclick", () => {
            let value = this.readLocalProp("handplace", "static");
            if (value == "static") value = "floating";
            else value = "static";
            this.setLocalProperty("handplace", value);
            this.reflowHandPosition();
          });
          this.addTooltip(node.id, "Click to make hand float/static", "");
        });
      }
    },

    reflowHandPosition: function () {
      let value = this.readLocalProp("handplace", "static");
      const v = value == "floating";
      if (!v && $(`player${this.player_id}`)) {
        // not floating
        $(`player${this.player_id}`).append($("hand_area"));
      } else {
        $("hand_wrapper").append($("hand_area"));
      }
    },

    setLocalProperty: function (key, value) {
      $("ebd-body").dataset["localsetting_" + key] = value;
      $("ebd-body").style.setProperty("--localsetting_" + key, value);
      this.writeLocalProp(key, value);
    },

    getLocalStorageItemId: function (key) {
      return this.gameName + "." + key;
    },

    readLocalProp: function (key, def = undefined) {
      const value = localStorage.getItem(this.getLocalStorageItemId(key));
      if (value === undefined || value === null) return def;
      return value;
    },

    writeLocalProp: function (key, val) {
      try {
        localStorage.setItem(this.getLocalStorageItemId(key), val);
        return true;
      } catch (e) {
        console.error(e);
        return false;
      }
    },

    ///////////////////////////////////////////////////
    //// Game & client states

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    onEnteringState: function (stateName, args) {
      console.log("onEnteringState", stateName, args);
      dojo.query(".selectable").removeClass("selectable");
      dojo.query(".selectable_later").removeClass("selectable_later");

      switch (stateName) {
        case "playerDraft":
          if (this.isCurrentPlayerActive() && args.args.selectCards != null) {
            this.args = args.args;
            this.selected = null;
            for (var card_id in args.args.selectCards) {
              var card = args.args.selectCards[card_id];
              if (card["card_location"] == "selectCards" + this.player_id) {
                this.addBuilding(card);
              }
            }
            dojo.query(".lowerlaneselect").removeClass("hidden");
            if (args.args.selectable != null) {
              for (var sid in args.args.selectable) {
                dojo.query("#" + sid).addClass("selectable");
              }
            }
          } else {
            dojo.query(".lowerlaneselect").addClass("hidden");
          }
          break;

        case "playerTurn":
          if (this.isCurrentPlayerActive()) {
            if (args.args.titleyou != null) {
              $("pagemaintitletext").innerHTML = this.format_string_recursive(
                _(args.args.titleyou).replace("${you}", this.divYou()).replace("#nb#", args.args.nb).replace("#nb2#", args.args.nb2),
                args.args
              );
            }

            if (args.args.selectCards != null) {
              for (var card_id in args.args.selectCards) {
                var card = args.args.selectCards[card_id];
                this.addBuilding(card);
              }
              dojo.query(".lowerlaneselect").removeClass("hidden");
            } else {
              dojo.query(".lowerlaneselect").addClass("hidden");
            }
            if (args.args.selectable != null) {
              for (var sid in args.args.selectable) {
                dojo.query("#" + sid).addClass("selectable");
                const info = args.args.selectable[sid];
                if (info.selectable)
                  for (let subitem in info.selectable) {
                    const div = $(subitem);
                    if (div) div.classList.add("selectable_later");
                  }
              }
            }
            this.args = args.args;
            this.selected = null;

            if (args.args.selectCards != null) {
              dojo.query(".lowerlaneselect").removeClass("hidden");
            } else {
              dojo.empty("selectCards" + this.player_id);
            }
          } else {
            if (!this.isSpectator) {
              dojo.empty("selectCards" + this.player_id);
            }
            dojo.query(".lowerlaneselect").addClass("hidden");
            if (args.args.title != null) {
              $("pagemaintitletext").innerHTML = this.format_string_recursive(
                _(args.args.title)
                  .replace("${actplayer}", this.divActPlayer())
                  .replace("#nb#", args.args.nb)
                  .replace("#nb2#", args.args.nb2),
                args.args
              );
            }
          }
          break;
      }
    },

    // onLeavingState: this method is called each time we are leaving a game state.
    //                 You can use this method to perform some user interface changes at this moment.
    //
    onLeavingState: function (stateName) {
      switch (stateName) {
        case "playerDraft":
          dojo.empty("selectCards" + this.player_id);
          dojo.query(".lowerlaneselect").addClass("hidden");
          break;

        case "dummmy":
          break;
      }
    },

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    onUpdateActionButtons: function (stateName, args) {
      if (this.isCurrentPlayerActive()) {
        switch (stateName) {
          case "playerTurn":
            for (var nb in args.buttons) {
              if (args.buttons[nb].startsWith("res")) {
                if (args.buttons[nb] == "resgh3") {
                  var lbl = '<div id="0" class="arcicon res10"></div><div id="0" class="arcicon res11"></div>';
                  this.addActionButton(args.buttons[nb], lbl, "onButton", null, null, col);
                } else if (args.storehouse != null) {
                  var full = parseInt(args.buttons[nb].split("to")[0].replace(/[^0-9]/g, ""));
                  var lbl = "";
                  for (var i = 1; i <= 10; i++) {
                    var isolated = Math.floor(full / Math.pow(10, i)) % 10;
                    if (isolated > 0) {
                      if (isolated > 1) {
                        lbl += isolated + "&nbsp;";
                      }
                      lbl += this.format_block("jstpl_reslog", {
                        id: 0,
                        type: i,
                      });
                    }
                  }

                  lbl += ' <i class="fa fa-arrow-right"></i> ';
                  var full = parseInt(args.buttons[nb].split("to")[1].replace(/[^0-9]/g, ""));
                  for (var i = 1; i <= 10; i++) {
                    var isolated = Math.floor(full / Math.pow(10, i)) % 10;
                    if (isolated > 0) {
                      if (isolated > 1) {
                        lbl += isolated + "&nbsp;";
                      }
                      lbl += this.format_block("jstpl_reslog", {
                        id: 0,
                        type: i,
                      });
                    }
                  }

                  this.addActionButton(args.buttons[nb], lbl, "onButton", null, null, col);
                } else {
                  var full = parseInt(args.buttons[nb].replace(/[^0-9]/g, ""));
                  var lbl = "";
                  for (var i = 1; i <= 10; i++) {
                    var isolated = Math.floor(full / Math.pow(10, i)) % 10;
                    if (isolated > 0) {
                      if (isolated > 1) {
                        lbl += isolated + "&nbsp;";
                      }
                      lbl += this.format_block("jstpl_reslog", {
                        id: 0,
                        type: i,
                      });
                    }
                  }
                  this.addActionButton(args.buttons[nb], lbl, "onButton", null, null, col);
                }
              } else {
                var col = "gray";
                if (args.buttons[nb] != "Pass" && args.buttons[nb] != "Undo" && args.buttons[nb] != "Skip") {
                  col = "blue";
                }
                this.addActionButton(args.buttons[nb], _(this.helper.buttons[args.buttons[nb]]), "onButton", null, null, col);
              }
            }
            break;

          case "client_selectTarget":
            for (var t_id in this.args.selectable[this.selected]["target"]) {
              var id = this.args.selectable[this.selected]["target"][t_id];
              if (id.startsWith("workertype")) {
                var typ = parseInt(id.replace(/[^0-9]/g, ""));
                var lbl = this.format_block("jstpl_res", {
                  id: 0,
                  type: typ + 6,
                });
                this.addActionButton(id, lbl, "onButton");
              }
            }

            this.addActionButton("cancel", _("Cancel"), "onOpCancel", null, null, "gray");
            break;
        }
      }
    },

    ///////////////////////////////////////////////////
    //// Utility methods

    divYou: function () {
      var color = this.players[this.player_id].color;
      var color_bg = "";
      var you = '<span style="font-weight:bold;color:#' + color + ";" + color_bg + '">' + _("You") + "</span>";
      return you;
    },

    replacePlayerName: function (log) {
      for (var key in this.players) {
        var player = this.players[key];
        var color = player.color;
        var name = player.name;
        var color_bg = "";
        log = log.replace(name, '<span style="font-weight:bold;color:#' + color + ";" + color_bg + '">' + name + "</span>");
      }

      return log;
    },
    divActPlayer: function () {
      var color = this.players[this.getActivePlayerId()].color;
      var name = this.players[this.getActivePlayerId()].name;
      var color_bg = "";
      var you = '<span style="font-weight:bold;color:#' + color + ";" + color_bg + '">' + name + "</span>";
      return you;
    },

    format_string_recursive: function (log, args) {
      try {
        if (log && args && !args.processed) {
          args.processed = true;

          if (args["cost"] != null) {
            var full = parseInt(args["cost"]);
            var lbl = "";
            for (var i = 1; i <= 10; i++) {
              var isolated = Math.floor(full / Math.pow(10, i)) % 10;
              if (isolated > 0) {
                lbl += isolated + this.format_block("jstpl_reslog", { id: 0, type: i });
              }
            }
            args["cost"] = lbl;
          }

          if (args["location"]) {
            args["location"] = _(this.helper.boards[args["location"]]);
          }

          if (args["apprentice"]) {
            args["apprentice"] = _(this.helper.apprentices[args["apprentice"]]["name"]);
          }

          if (args["building"]) {
            args["building"] = _(this.helper.buildings[args["building"]]["name"]);
          }
          if (args["board"]) {
            args["board"] = _(this.helper.boards[args["board"]]);
          }

          if (args["other_player_name"] != null) {
            args["other_player_name"] = this.replacePlayerName(args["other_player_name"]);
          }
        }
      } catch (e) {
        console.error(log, args, "Exception thrown", e.stack);
      }
      return this.inherited(arguments);
    },

    attachToNewParentNoDestroy: function (mobile_in, new_parent_in, relation, place_position) {
      const mobile = $(mobile_in);
      const new_parent = $(new_parent_in);

      var src = dojo.position(mobile);
      if (place_position) mobile.style.position = place_position;
      dojo.place(mobile, new_parent, relation);
      mobile.offsetTop; //force re-flow
      var tgt = dojo.position(mobile);
      var box = dojo.marginBox(mobile);
      var cbox = dojo.contentBox(mobile);
      var left = box.l + src.x - tgt.x;
      var top = box.t + src.y - tgt.y;

      mobile.style.position = "absolute";
      mobile.style.left = left + "px";
      mobile.style.top = top + "px";
      box.l += box.w - cbox.w;
      box.t += box.h - cbox.h;
      mobile.offsetTop; //force re-flow
      return box;
    },

    addDebt: function (debt) {
      dojo.place(this.format_block("jstpl_debt", debt), $("cards" + debt.player_id));
      debt.paid = 0;
      var html = '<div class="anatooltip"><div>' + this.format_block("jstpl_debt", debt);
      debt.paid = 1;
      html +=
        this.format_block("jstpl_debt", debt) +
        "</div><div>" +
        _("<b>Debt :</b>Unpaid debt make you loose 2 VP or pay it to gain 1 Virtue") +
        "</div></div>";
      this.addTooltipHtml("debt_" + debt["id"], html, 1000);
    },

    returnDebt: function (debt) {
      dojo.place(this.format_block("jstpl_debt", debt), $("cards" + debt.player_id));
    },

    addBuilding: function (card) {
      card["posx"] = (card["card_type"] % 6) * 20;
      card["posy"] = Math.floor(card["card_type"] / 6) * 14.286;
      card["name"] = _(this.helper.buildings[card["card_type"]]["name"]);
      card["most"] = "";
      card["per"] = "";
      if (this.helper.buildings[card["card_type"]]["most"]) {
        card["most"] = _(this.helper.translates["most"]);
      }
      if (this.helper.buildings[card["card_type"]]["per"]) {
        card["per"] = _(this.helper.translates["per"]);
      }
      dojo.place(this.format_block("jstpl_building", card), $(card.card_location));
      dojo.query("#building" + card["card_id"]).connect("onclick", this, "onSelect");

      var html =
        '<div class="anatooltip"><div class="anattbuilding">' +
        this.format_block("jstpl_building", card) +
        '</div><div class="ttbuilding"><b>' +
        _(this.helper.buildings[card["card_type"]]["name"]) +
        " :</b><br/><div>" +
        _(this.helper.buildings[card["card_type"]]["tooltip"]) +
        "</div></div></div>";
      this.addTooltipHtml("building" + card["card_id"], html, 1000);
    },

    addApprentice: function (card) {
      card["posx"] = (card["card_type"] % 6) * 20;
      card["posy"] = Math.floor(card["card_type"] / 6) * 14.286;
      card["name"] = _(this.helper.apprentices[card["card_type"]]["name"]);
      card["target"] = _(this.helper.apprentices[card["card_type"]]["target"]);
      dojo.place(this.format_block("jstpl_apprentice", card), $(card.card_location));
      dojo.query("#apprentice" + card["card_id"]).connect("onclick", this, "onSelect");

      if (card["bonus"] > 0) {
        dojo.query("#silverbonushead" + card["card_id"]).removeClass("hidden");
      }

      var html =
        '<div class="anatooltip"><div class="anattbuilding">' +
        this.format_block("jstpl_apprentice", card) +
        '</div><div class="ttbuilding"><b>' +
        _(this.helper.apprentices[card["card_type"]]["name"]) +
        " :</b><br/><div>" +
        _(this.helper.apprentices[card["card_type"]]["tooltip"]) +
        "</div></div></div>";
      this.addTooltipHtml("apprentice" + card["card_id"], html, 1000);
    },

    ///////////////////////////////////////////////////
    //// Player's action

    onZoom: function (evt) {
      evt.preventDefault();
      this.zoomIndex = (this.zoomIndex + 1) % this.zoomScales.length;
      this.adaptViewportSize();
    },

    onScore: function (evt) {
      evt.preventDefault();
      if (dojo.hasClass("score", "mini")) {
        dojo.query("#score").removeClass("mini");
        document.getElementById("closescore").innerHTML = '<i class="fa fa-compress" aria-hidden="true"></i>';
      } else {
        dojo.query("#score").addClass("mini");
        document.getElementById("closescore").innerHTML = '<i class="fa fa-expand" aria-hidden="true"></i>';
      }
    },

    onSelect: function (evt) {
      // Preventing default browser reaction
      dojo.stopEvent(evt);

      if (!this.isCurrentPlayerActive() || !evt.currentTarget.classList.contains("selectable")) {
        return;
      }

      if (this.selected != null) {
        dojo.query(".selectable").removeClass("selectable");
        this.ajaxcall(
          "/architectsofthewestkingdom/architectsofthewestkingdom/actSelect.html",
          {
            lock: true,
            arg1: this.selected,
            arg2: event.currentTarget.id,
          },
          this,
          function (result) {},
          function (is_error) {}
        );
      } else {
        this.selected = event.currentTarget.id;

        if (this.args.selectable[this.selected]["target"] == null) {
          dojo.query(".selectable").removeClass("selectable");
          this.ajaxcall(
            "/architectsofthewestkingdom/architectsofthewestkingdom/actSelect.html",
            {
              lock: true,
              arg1: this.selected,
            },
            this,
            function (result) {},
            function (is_error) {}
          );
        } else {
          dojo.query(".selectable").removeClass("selectable");
          this.setClientState("client_selectTarget", {
            descriptionmyturn: _(this.args.selectable[this.selected]["titleyou"]),
            args: {},
          });
        }
      }
    },

    onButton: function (event) {
      dojo.stopEvent(event);
      if (this.isCurrentPlayerActive() && this.checkAction("select")) {
        if (this.selected != null) {
          dojo.query(".selectable").removeClass("selectable");
          this.ajaxcall(
            "/architectsofthewestkingdom/architectsofthewestkingdom/actSelect.html",
            {
              lock: true,
              arg1: this.selected,
              arg2: event.currentTarget.id,
            },
            this,
            function (result) {},
            function (is_error) {}
          );
        } else {
          dojo.query(".selectable").removeClass("selectable");
          this.ajaxcall(
            "/architectsofthewestkingdom/architectsofthewestkingdom/actSelect.html",
            {
              lock: true,
              arg1: event.currentTarget.id,
            },
            this,
            function (result) {},
            function (is_error) {}
          );
        }
      }
    },

    onOpCancel: function (evt) {
      this.restoreServerGameState();
    },

    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications

    /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notify->all" and "notify->player" calls in
                  your architectsofthewestkingdom.game.php file.
        
        */
    setupNotifications: function () {
      dojo.subscribe("counter", this, "notif_counter");
      dojo.subscribe("counterid", this, "notif_counterid");
      dojo.subscribe("countermask", this, "notif_countermask");
      dojo.subscribe("move", this, "notif_move");
      dojo.subscribe("remove", this, "notif_remove");
      dojo.subscribe("gain", this, "notif_gain");
      dojo.subscribe("newapprentice", this, "notif_newapprentice");
      dojo.subscribe("newbuilding", this, "notif_newbuilding");
      dojo.subscribe("newdebt", this, "notif_newdebt");
      dojo.subscribe("returndebt", this, "notif_returndebt");
      dojo.subscribe("reward", this, "notif_reward");
      dojo.subscribe("blackmarket", this, "notif_blackmarket");
      dojo.subscribe("popup", this, "notif_popup");
      dojo.subscribe("finalscore", this, "notif_finalscore");
      this.notifqueue.setSynchronous("finalscore", 1000);
    },

    notif_finalscore: function (notif) {
      dojo.query("#score").removeClass("hidden");
      dojo.place(
        this.format_block("jstpl_score", {
          top: 40 + 85 * notif.args.j,
          left: 172 + 84 * notif.args.i,
          text: notif.args.score,
        }),
        $("scoreinside2")
      );
    },

    notif_popup: function (notif) {
      var element = document.getElementById("newRound");
      element.innerHTML = _(notif.args.msg);
      element.classList.remove("popit");
      element.classList.remove("hidden");
      void element.offsetWidth;
      element.classList.add("popit");
    },

    notif_blackmarket: function (notif) {
      dojo.empty("phblackmarket1");
      dojo.empty("phblackmarket2");

      if (notif.args.blackmarket1 != null) {
        dojo.place(
          this.format_block("jstpl_bmcard", {
            id: notif.args.blackmarket1.id,
            posx: (notif.args.blackmarket1.type - 1) * 11.11,
            posy: 50,
          }),
          $("phblackmarket1")
        );
      }
      if (notif.args.blackmarket2 != null) {
        dojo.place(
          this.format_block("jstpl_bmcard", {
            id: notif.args.blackmarket2.id,
            posx: (notif.args.blackmarket2.type - 1) * 11.11,
            posy: 0,
          }),
          $("phblackmarket2")
        );
      }
    },

    notif_reward: function (notif) {
      var reward = notif.args.reward;
      if (reward != null) {
        dojo.place(
          this.format_block("jstpl_reward", {
            id: reward.id,
            posx: ((reward.type - 1) % 6) * 20,
            posy: 100 * Math.floor((reward.type - 1) / 6),
          }),
          $("phreward")
        );
        this.fadeOutAndDestroy("reward" + reward.id, 1000, 5000);
      }
      if (notif.args.rewardnb > 0) {
        dojo.query("#rewardcpt").forEach(function (selectTag) {
          selectTag.innerHTML = notif.args.rewardnb;
        });
      } else {
        dojo.query("#rewardcpt").forEach(dojo.destroy);
      }
    },

    notif_newapprentice: function (notif) {
      var card = notif.args.card;
      this.addApprentice(card);
    },

    notif_newbuilding: function (notif) {
      var card = notif.args.card;
      this.addBuilding(card);
    },

    notif_newdebt: function (notif) {
      this.addDebt(notif.args.debt);
    },

    notif_returndebt: function (notif) {
      dojo.query("#debt_" + notif.args.id).addClass("paid1");
      dojo.query("#debt_" + notif.args.id).removeClass("paid0");
    },

    notif_gain: function (notif) {
      if (notif.args.source != null && notif.args.target != null) {
        var full = parseInt(notif.args.costinv);
        var j = 0;
        for (var i = 1; i <= 8; i++) {
          var isolated = Math.floor(full / Math.pow(10, i)) % 10;
          for (var z = 0; z < isolated; z++) {
            var id = Math.floor(Math.random() * 100000);

            dojo.place(this.format_block("jstpl_resmove", { id: id, type: i }), $(notif.args.source));
            this.slideToObjectAndDestroy("resmove" + id, notif.args.target, 500, j * 300);
            j++;
          }
        }
      }
    },

    notif_remove: function (notif) {
      this.fadeOutAndDestroy(notif.args.id);
    },

    sortChildrenDivsById: function (parentId) {
      var parent = document.getElementById(parentId);
      var children = parent.children;
      var ids = [],
        i,
        len;
      for (i = 0, len = children.length; i < len; i++) {
        ids.push(children[i].id);
      }
      ids.sort(function (str1, str2) {
        return str1 == str2 ? 0 : str1 > str2 ? 1 : -1;
      });
      for (i = 0, len = ids.length; i < len; i++) {
        parent.appendChild(document.getElementById(ids[i]));
      }
    },

    notif_move: function (notif) {
      this.attachToNewParentNoDestroy(notif.args.mobile, notif.args.parent, notif.args.position);

      if (dojo.hasClass(notif.args.parent, "meeplelocation") || notif.args.parent.startsWith("cards")) {
        this.sortChildrenDivsById(notif.args.parent);
      }

      element = document.getElementById(notif.args.mobile);
      void element.offsetWidth;
      dojo.style(notif.args.mobile, {
        left: "0px",
        top: "0px",
        transition: "0.5s",
      });

      setTimeout(() => {
        dojo.style(notif.args.mobile, {
          position: "",
          left: "",
          top: "",
          bottom: "",
          right: "",
        });
      }, "600");
    },

    notif_counter: function (notif) {
      dojo.query("." + notif.args.id).forEach(function (selectTag) {
        selectTag.innerHTML = notif.args.nb;
      });

      if (notif.args.id.endsWith("7")) {
        dojo.query("." + notif.args.id).forEach(function (selectTag) {
          selectTag.innerHTML += "/6";
        });
      }
    },

    notif_counterid: function (notif) {
      dojo.query("#" + notif.args.id).forEach(function (selectTag) {
        selectTag.innerHTML = notif.args.nb;
      });
    },

    notif_countermask: function (notif) {
      dojo.query("#silverbonus" + notif.args.id).forEach(function (selectTag) {
        selectTag.innerHTML = notif.args.nb;
      });
      if (notif.args.nb > 0) {
        dojo.query("#silverbonushead" + notif.args.id).removeClass("hidden");
      } else {
        dojo.query("#silverbonushead" + notif.args.id).addClass("hidden");
      }
    },
  });
});
