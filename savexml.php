<?php
//savexml.php
//saves xml file from wikipedia api query, to prevent js error
$title = htmlspecialchars($_POST["title"]);
$url = "http://en.wikipedia.org/w/api.php?action=query&titles=" . urlencode($title) . "&format=xml&prop=info&inprop=url";
file_put_contents("pageinfo.txt", $url);
file_put_contents("pageinfo.xml", file_get_contents($url));
?>