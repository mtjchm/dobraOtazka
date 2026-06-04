<?php
require_once("Db.php");
class Balik {
    public int $balicek_id;
    public string $nazev;
    public string $popis;
    private Uzivatel $autor_balicek;
    //POUZE PRO DECK BUILDING
    private array $karty = array();

    /**
     * @param int $balicek_id
     * @param string $nazev
     * @param string $popis
     * @param Uzivatel $autor_balicek
     */
    public function __construct(int $balicek_id, string $nazev, string $popis, Uzivatel $autor_balicek)
    {
        $this->balicek_id = $balicek_id;
        $this->nazev = $nazev;
        $this->popis = $popis;
        $this->autor_balicek = $autor_balicek;
        if (is_null($this->autor_balicek)) {
            throw new Exception("Autor nenalezen!");
        }
    }

    public static function newFromAssocArray(array $assocArray): Balik {
        return new Balik(
            balicek_id: $assocArray['balicek_id'],
            nazev: $assocArray['nazev'],
            popis: $assocArray['popis'],
            //autor_balicek: $assocArray['autor_balicek']
            //schanim autora přes Uzivatel classu
            autor_balicek: Uzivatel::getById($assocArray['autor_balicek'])
        );
    }

    public function getAutor(): Uzivatel {
        return $this->autor_balicek;
    }

    public function getKarty(): array {
        //změnil jsem  "karty INNER JOIN balik_karta USING (karta_name)"  na  
        // "karty k INNER JOIN balik_karta b ON (k.name = b.karta_name)"
        //kvůli tomu že v bd.sql bylo použito v karty tabulce jenom name místo karta_name
        return Db::dotazVsechny("SELECT name, id, deck, row, strength, ability, filename, count, pocet FROM karty k INNER JOIN balik_karta b ON (k.name = b.karta_name) WHERE balicek_id = ?", [$this->balicek_id]);
    }

    public function getTagy(): array {
        return Db::dotazVsechny("SELECT tag_id, nazev, popis FROM tag INNER JOIN tag_balicek USING (tag_id) WHERE balicek_id = ?", [$this->balicek_id]);
    }

    public function existsBalik(): bool {
        return !is_null(Db::dotazJeden("SELECT * FROM balicek WHERE balicek_id = ?", [$this->balicek_id]));
    }

    //FUNKCE POUZE PRO DECK BULDING
    public function addKartaByName(string $karta_name): bool {
        $karta = Karta::getKartaByName($karta_name);
        if (is_null($karta) || array_search($karta, $this->karty)) {
            return false;
        }
        array_push($this->karty, $karta);
        return true;
    }

    public function getObsahBalicek(): array {
        if (empty($this->karty)) {
            $karty = $this->getKarty();
            if (!empty($karty)) {
                $this->karty = $karty;
            }

        }
        return $this->karty;
    }

    public function addKarta(Karta $karta): bool {
        if (array_search($karta, $this->karty)) {
            return false;
        }
        array_push($this->karty, $karta);
        return true;
    }

    public function saveBalikObsah(int $uzivatel_uid): bool {
        if (!$this->existsBalik() || $this->autor_balicek->uid != $uzivatel_uid) {
            return false;
        }
        $celk_pocet = 0;
        for ($i = 0; $i < count($this->karty); $i++) {
            $celk_pocet += $this->karty[$i]->amount;
        }
        if ($celk_pocet < 22) {
            return false;
        }
        for ($i = 0; $i < count($this->karty); $i++) {
            Db::dotaz("INSERT INTO balik_karta VALUES (
                                $this->karty[$i]->name,
                                $this->balicek_id,
                                $this->karty[$i]->amount,
            )");
        }
        return true;
    }
}