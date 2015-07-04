<?php header('Content-Type: text/html; charset=utf-8'); ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv=Content-Type content="text/html; charset=utf-8">
  <title>Floating boxes test</title>
  <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.1.custom.css">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" type="text/css" href="farbtastic/farbtastic.css">
  <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
  <script type="text/javascript" src="js/main.js"></script>
  <script type="text/javascript" src="farbtastic/farbtastic.js"></script>
</head>
<body>

<div id="maincontainer">
</div>

<div id="boxinfo">
<fieldset>
<legend>Ingen boks valgt</legend>
<input type="text" id="color" name="color" value="#123456">
<div id="colorpicker"></div>
<button class="delete">Slett boks</button>
</fieldset>
</div>

<div id="toolbar"><button class="new">Lag boks</button></div>

</body>
</html>