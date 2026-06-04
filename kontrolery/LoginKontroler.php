<?php
class LoginKontroler extends Kontroler {
    public function zpracuj($parametry) {
        $this->data = array();
        if (isset($GET)) {
            if (isset($SESSION) && isset($SESSION['uid'])) {
                //vykopne uživatele když je přihlášený
                $this->pohled = "main";
                return;
            };

            $this->pohled = "login";
        } else if (isset($POST) && !empty($POST['heslo']) && !empty($POST['username'])) {
            if (isset($SESSION) && isset($SESSION['uid'])) {
                //odhlásí uživatele
                session_destroy();
            } else {
                $uzivatel = Uzivatel::getByUsername($POST["username"]);
                if(is_null($uzivatel) || !$uzivatel->porovnejHeslo($POST["password"])) {
                    $this->data["loginOK"] = false;
                    $this->pohled = "login";
                    return;
                }

                $SESSION['uid'] = $uzivatel->uid;
                $SESSION['username'] = $uzivatel->username;

                $this->data["loginOK"] = true;
                $this->pohled = "main";
            }
        }
    }
}