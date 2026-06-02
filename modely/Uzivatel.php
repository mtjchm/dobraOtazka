<?php
require_once("Db.php");
class Uzivatel {
    public int $uid;
    public string $username;
    private string $password_hash;

    public function __construct($uid, $username, $password_hash) {
        $this->uid = $uid;
        $this->username = $username;
        $this->password_hash = $password_hash;
    }

    public static function newFromAssocArray($assocArray): Uzivatel {
        return new Uzivatel(
            uid: $assocArray['uid'],
            username: $assocArray['username'],
            password_hash: $assocArray['password_hash']
        );
    }

    public function noveHeslo($password): void {
        $this->password_hash = password_hash($password, PASSWORD_BCRYPT);
        Db::dotaz("UPDATE uzivatel SET password_hash = ? WHERE uid = ?", [$this->password_hash, $this->uid]);
    }

    public function porovnejHeslo($password): bool {
        return password_verify($password, $this->password_hash);
    }

    public static function getById(int $id): Uzivatel {
        return self::newFromAssocArray(Db::dotazJeden("SELECT * FROM uzivatel WHERE uid = ?", [$id]));
    }
}
