<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include "db.php";

// Ensure valid box id, or none at all
$box = $_GET['box'];
if (!is_numeric($box))
  unset($box);

// Execute correct function
switch ($_GET['verb']) {
  case "get":
    echo get($box);
    break;
  case "new":
    echo newBox($box);
    break;
  case "put":
    put($box, mysql_real_escape_string($_GET['xpos']), mysql_real_escape_string($_GET['ypos']), mysql_real_escape_string($_GET['width']), mysql_real_escape_string($_GET['height']), mysql_real_escape_string(urldecode($_GET['color'])));
    break;
  case "allBoxes":
    echo allBoxes();
    break;
  case "delete":
    delete($box);
    break;
}

// Get data for a specific box

function get($box) {
  $result = mysql_query("SELECT * FROM boxes WHERE id=$box");
  if ($row = mysql_fetch_assoc($result)) {
    return json_encode(array(
      'id' => $box,
      'xpos' => (int)$row['xpos'],
      'ypos' => (int)$row['ypos'],
      'width' => (int)$row['width'],
      'height' => (int)$row['height'],
      'color' => $row['color']
    ));
  }
}

// Store data for a specific box

function put($box, $xpos, $ypos, $width, $height, $color) {
  if (is_null($box)) {
    $query = "INSERT INTO boxes VALUES (null, $xpos, $ypos, $width, $height, '$color')";
  } else {
    $query = "REPLACE INTO boxes VALUES ($box, $xpos, $ypos, $width, $height, '$color')";
  }
  $result = mysql_query($query);
  if (!$result)
    return false;
  if (is_null($box))
    $box = mysql_insert_id();
  _logop("new", $box); // We deal with changes and additions as the same in the frontend
  return $box;
}

// Get all boxes

function allBoxes() {
  initComet(); // Set a marker for newest received state
  $result = mysql_query("SELECT * FROM boxes");
  $boxes = array();
  while ($row = mysql_fetch_assoc($result)) {
    $boxes[$row['id']] = array(
      'id' => $box,
      'xpos' => (int)$row['xpos'],
      'ypos' => (int)$row['ypos'],
      'width' => (int)$row['width'],
      'height' => (int)$row['height'],
      'color' => $row['color']
    );
  }
  return json_encode($boxes);
}

// Create a new box

function newBox() {
    $xpos = rand(1,400);
    $width = rand(50, 500 - $xpos);
    $ypos = rand(1,300);
    $height = rand(50, 400 - $ypos);
    $newbox = array();
    $newbox['tmp'] = array('xpos' => $xpos, 'ypos' => $ypos, 'width' => $width, 'height' => $height, 'color' => sprintf("#%02X%02X%02X", mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
    $id = put(null, $xpos, $ypos, $width, $height, $newbox['tmp']['color']); // Store the box before returning it.
    $newbox[$id] = $newbox['tmp'];
    unset($newbox['tmp']);
    return json_encode($newbox);
}

// Delete box

function delete($box) {
  $result = mysql_query("DELETE FROM boxes WHERE id=$box");
  _logop("delete", $box);
}

// Log operation for comet notification

function _logop($action, $box) {
  $query = "INSERT INTO boxop VALUES (null, '$action', '$box')";
  $result = mysql_query($query);
}

?>