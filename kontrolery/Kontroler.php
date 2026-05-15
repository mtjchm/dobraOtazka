<?php
abstract class Kontroler {
    protected $pohled = ""; // název souboru s pohledem (bez přípony .phtml)

    abstract public function zpracuj($parametry);

    public function vypisPohled() {
        require "pohledy/{$this->pohled}.phtml";
    }
    
    public function presmeruj($url) {
        header("Location: /$url");
        exit();
    }
}