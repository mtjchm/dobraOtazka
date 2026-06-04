<?php
class RegisterKontroler extends Kontroler {
    public function zpracuj($parametry) {
        session_start();
        $this->data = array();
        if (empty($_POST["username"]) && empty($_POST["password"])) {


            if (isset($_SESSION) && isset($_SESSION['uid'])) {
                //vykopne uživatele když je přihlášený
                $this->pohled = "main";
                return;
            };

            $this->pohled = "register";
        } else if (isset($_SESSION) && isset($_SESSION['uid'])) {
            //odhlásí uživatele
            session_destroy();
        } else if (isset($_POST) && !empty($_POST['password']) && !empty($_POST['username'])) {
            $uzivatel = Uzivatel::getByUsername($_POST["username"]);
            if(!is_null($uzivatel)) {
                $this->data["userExists"] = true;
                $this->pohled = "register";
                return;
            }

            if (Uzivatel::dbCreateUzivatel($_POST["username"], $_POST["password"])) {
                $uzivatel = Uzivatel::getByUsername($_POST["username"]);
                $_SESSION['uid'] = $uzivatel->uid;
                $_SESSION['username'] = $uzivatel->username;
                $this->data["loginOK"] = true;
                $this->pohled = "main";
            } else {
                $this->data["otherError"] = true;
            }


        }
    }
}