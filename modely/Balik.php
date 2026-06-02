<?php
require_once("Db.php");
class Balik {
    public int $balicek_id;
    public string $nazev;
    public string $popis;
    private Uzivatel $autor_balicek;

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
    }

    public static function newFromAssocArray(array $assocArray): Balik {
        return new Balik(
            balicek_id: $assocArray['balicek_id'],
            nazev: $assocArray['nazev'],
            popis: $assocArray['popis'],
            autor_balicek: $assocArray['autor_balicek']
        );
    }

    public function getAutor(): Uzivatel {
        return $this->autor_balicek;
    }

    public function getKarty(): array {
        return Db::dotazVsechny("SELECT name, id, deck, row, strength, ability, filename, count, pocet FROM karty INNER JOIN balik_karta USING (karta_name) WHERE balicek_id = ?", [$this->balicek_id]);
    }

    public function getTagy(): array {
        return Db::dotazVsechny("SELECT tag_id, nazev, popis FROM tag INNER JOIN tag_balicek USING (tag_id) WHERE balicek_id = ?", [$this->balicek_id]);
    }
}