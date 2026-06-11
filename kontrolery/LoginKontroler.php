<?php
class LoginKontroler extends Kontroler {
    public function zpracuj($parametry) {
        //session_start();
        $this->data = array();
        if (empty($_POST["username"]) && empty($_POST["password"])) {


            if (isset($_SESSION) && isset($_SESSION['uid'])) {
                //vykopne uživatele když je přihlášený
                $this->presmeruj('main');
            };

            $this->pohled = "login";
        } else if (isset($_POST) && !empty($_POST['password']) && !empty($_POST['username'])) {

            try {
                $uzivatel = Uzivatel::getByUsername($_POST["username"]);
            } catch (Exception $e) {
                $this->data["loginOK"] = false;
                $this->pohled = "login";
                return;
            }
            if(is_null($uzivatel) || !$uzivatel->porovnejHeslo($_POST["password"])) {
                $this->data["loginOK"] = false;
                $this->pohled = "login";
                return;
            }

            $_SESSION['uid'] = $uzivatel->uid;
            $_SESSION['username'] = $uzivatel->username;

            $this->data["loginOK"] = true;
            $this->presmeruj('main');

        }
    }
}