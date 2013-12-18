<?php
//logout.php
//this file ends the php session
session_start();
session_destroy();

include 'index.php';
?>