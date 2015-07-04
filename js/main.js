$(window).load(main);

var boxes = [];

// *************************
// main function called on load

function main() {

  // Init interface
  $.ajaxSetup ({ cache: false });  
  $("#maincontainer").selectable({selected: selectedBox, unselected: unSelectBox});
  unSelectBox();

  // Fetch all boxes
  $.getJSON( 'ajax.php' , { "verb" : "allBoxes" }, function(data) {
    $.each(data, function (key, value) {
      createBox(key, value.xpos, value.ypos, value.width, value.height, value.color);
    });
  });

  // Register event handlers
  $("div#toolbar button.new").click(bt_new);
  $("div#boxinfo button.delete").click(bt_delete);
  $('#colorpicker').farbtastic(palette_change);
  
  $.getJSON( 'comet.php' , comethandler );
  
}

// ### EVENTS ###

// *************************
// Event Comet Reply

function comethandler(data) {

  // No data, server probably just handshaking
  if (data == undefined) {
    $.getJSON( 'comet.php' , comethandler );
    return;
  }

  if (data.action == 'new') {
    if (boxes[data.box] == undefined) { // Box does not exist
      $.getJSON( 'ajax.php' , { "verb" : "get", "box" : data.box }, function(data) {
        createBox(data.id, data.xpos, data.ypos, data.width, data.height, data.color);
      });
    } else { // Box exists
      $.getJSON( 'ajax.php' , { "verb" : "get", "box" : data.box }, function(data) {
        setBox(data.id, data.xpos, data.ypos, data.width, data.height, data.color);
      });
    }
  } else if (data.action == 'delete') {
    boxes[data.box].remove();
  }
  // Start new request
  $.getJSON( 'comet.php' , comethandler );
}

// *************************
// Event Button Click New
function bt_new () {
  $.getJSON( 'ajax.php' , { "verb" : "new" });
}

// *************************
// Event Button Click Delete
function bt_delete () {
    index = $('div#boxinfo').data('boxindex');
    boxes[index].remove();
    $.getJSON( 'ajax.php' , { "verb" : "delete", "box" : index });
    unSelectBox();
}

// *************************
// Event Colorpicker Change

function palette_change(color) {
    index = $('div#boxinfo').data('boxindex');
    boxes[index].data('color', color);
    boxes[index].css('background-color', color);
    commitBox(index);
}

// *************************
// Event Boxes Change (drag and resize)

function updateBox(event, ui) {
  commitBox($(this).data("boxindex"));
}

// *************************
// Event Boxes Select

function selectedBox(event, ui) {
  $('div#boxinfo input').removeAttr('disabled');
  $('div#boxinfo button').removeAttr('disabled');
  boxindex = $(ui.selected).data('boxindex')
  $('div#boxinfo').data('boxindex',boxindex);
  $('div#boxinfo legend').html("Box " + boxindex);
  $.farbtastic('#colorpicker').setColor(boxes[boxindex].data('color'));
}

// ### OTHERS ###

// *************************
// create a box element from specifications

function createBox(id, xpos, ypos, width, height, color) {
  boxes[id] = $('<div class="floatbox"></div>');
  boxes[id].data("boxindex",id);
  boxes[id].append('<div class="ui-icon ui-icon-arrow-4 handle"></div>')
  setBox(id, xpos, ypos, width, height, color);
  boxes[id].appendTo('div#maincontainer');
  boxes[id].draggable({ containment: 'parent', stop: updateBox, handle: 'div.handle' });
  boxes[id].resizable({ containment: 'parent', stop: updateBox });
}

// *************************
// set attributes for a box based on input

function setBox(id, xpos, ypos, width, height, color) {
  boxes[id].css("left", xpos);
  boxes[id].css("top", ypos);
  boxes[id].width(width);
  boxes[id].height(height);
  boxes[id].css("background-color", color);
  boxes[id].data("color",color);
}

// *************************
// send box changes to server

function commitBox(index) {
  var position = boxes[index].position();
  $.getJSON( 'ajax.php' , {
    "verb" : "put",
    "box" : index,
    "xpos" : position.left,
    "ypos" : position.top,
    "width" : boxes[index].width(),
    "height" : boxes[index].height(),
    "color" : boxes[index].data("color")
  });

}

// *************************
// updates property form to reflect no selection

function unSelectBox() {
  $('div#boxinfo legend').html("Ingen boks valgt");
  $('div#boxinfo input').attr('disabled', true);
  $('div#boxinfo button').attr('disabled', true);
}



