<?php

// Cache would ruin everything
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include "db.php";

if (!isset($_COOKIE['lastop'])) { // We need the cookie set. If it isn't, wait a second and try again. This is set by the allBoxes() AJAX call.
  sleep(1);
  return;
}

set_time_limit(0); // This script should not time out ever

// Real work begins here:

for ($waitloop = 5*60 ; $waitloop ; $waitloop--) { // We do a small handshake every 5 minutes, to make sure the server doesn't end up with hanged threads

  $newop = getOp();
  
  if ($newop) {
    setcookie("lastop", $newop['id']);
    echo json_encode($newop);
    return;
  }
  
  sleep(1);
}

return;

// -----

function getOp() { // Get the first operation since last time.
  $result = mysql_query("SELECT * FROM boxop WHERE id > ".$_COOKIE['lastop']." ORDER BY id ASC LIMIT 1");
  if ($result)
    $result = mysql_fetch_assoc($result);
  return $result;
}



?>