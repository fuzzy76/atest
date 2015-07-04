<?php

$dbhost = "127.0.0.1";
$dbuser = "atest";
$dbpassword = "atest2010";
$dbdatabase = "atest";

if (!($db = mysql_connect($dbhost, $dbuser, $dbpassword))) {
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($dbdatabase, $db)) {
    die('Could not select database: ' . mysql_error());
}

// Ensure UTF-8 all the way
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $db);
mysql_set_charset('utf-8',$db);

// Ensure tables exist

$dbtablequery = "
CREATE TABLE IF NOT EXISTS boxes (
  id INT NOT NULL auto_increment,
  xpos INT NOT NULL,
  ypos INT NOT NULL,
  width INT NOT NULL,
  height INT NOT NULL,
  color VARCHAR(30) NOT NULL,
  PRIMARY KEY (id)
) CHARACTER SET utf8;
";
mysql_query($dbtablequery);

$dbtablequery2 = "
CREATE TABLE IF NOT EXISTS boxop (
  id INT NOT NULL auto_increment,
  action VARCHAR(10) NOT NULL,
  box INT NOT NULL,
  PRIMARY KEY (id)
) CHARACTER SET utf8;
";
mysql_query($dbtablequery2);

// Initialises the comet "last operation received" pointer
function initComet() {
  $result = mysql_query("SELECT MAX(id) FROM boxop");
  if ($result)
    $row = mysql_fetch_row($result);
  if ($row) {
    setcookie("lastop", $row[0]);
  } else {
    setcookie("lastop", 0);
  }
}


?>