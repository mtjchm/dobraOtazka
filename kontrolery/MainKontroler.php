<?php
class MainKontroler extends Kontroler {
    public function zpracuj($parametry) {
        $this->pohled = "main";

        $radky = Db::dotazVsechny("
            SELECT balicek_id, nazev, popis, autor_balicek
            FROM balicek
        ");

        $this->data['decks'] = array_map(
            fn($radek) => Balik::newFromAssocArray($radek),
            $radky
        );

        $this->data['stats'] = [
            'pocet_balicku'  => Db::dotazSamotny("SELECT COUNT(*) FROM balicek"),
            'pocet_karet'    => Db::dotazSamotny("SELECT COUNT(*) FROM karty"),
            'pocet_uzivatelu'=> Db::dotazSamotny("SELECT COUNT(*) FROM uzivatel"),
        ];
    }
}