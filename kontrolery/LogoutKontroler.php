<?php
class LogoutKontroler extends Kontroler {

    public function zpracuj($parametry)
    {
        // TODO: Implement zpracuj() method.
        session_destroy();
        $this->presmeruj("login");
    }
}