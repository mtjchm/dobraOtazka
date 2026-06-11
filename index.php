<?php
require "init.php";
session_start();
$smerovac = new SmerovacKontroler();
$smerovac->zpracuj([$_SERVER["REQUEST_URI"]]);
$smerovac->vypisPohled();
