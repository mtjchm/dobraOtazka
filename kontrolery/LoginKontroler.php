<?php
class LoginKontroler extends Kontroler {
    public function zpracuj($parametry) {
        $this->data = array();
        if (isset($_GET)) {
            if (isset($_SESSION) && isset($_SESSION['uid'])) {
                //vykopne uživatele když je přihlášený
                $this->pohled = "main";
                return;
            };

            $this->pohled = "login";
        } else if (isset($_POST) && !empty($_POST['heslo']) && !empty($_POST['username'])) {
            if (isset($_SESSION) && isset($_SESSION['uid'])) {
                //odhlásí uživatele
                session_destroy();
            } else {
                $uzivatel = Uzivatel::getByUsername($_POST["username"]);
                if(is_null($uzivatel) || !$uzivatel->porovnejHeslo($_POST["password"])) {
                    $this->data["loginOK"] = false;
                    $this->pohled = "login";
                    return;
                }

                $_SESSION['uid'] = $uzivatel->uid;
                $_SESSION['username'] = $uzivatel->username;

                $this->data["loginOK"] = true;
                $this->pohled = "main";
            }
        }
    }
}