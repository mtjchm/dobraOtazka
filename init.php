<?php
include_once("modely/Db.php");
$db = new Db();
$db->pripoj("localhost", "root", "", "DbOtazka");

function nactiTridu($nazevTridy) {
    if (preg_match("/Kontroler$/", $nazevTridy)) 
        require "kontrolery/$nazevTridy.php";
    else
        require "modely/$nazevTridy.php";
}

spl_autoload_register("nactiTridu");