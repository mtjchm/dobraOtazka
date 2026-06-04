<?php
class BalikyKontroler extends Kontroler {
    public function zpracuj($parametry) {
        $this->pohled = "baliky";
        $this->data = array();

        // 1. KROK: Pokud jde uživatel na /baliky/vytvor, rovnou mu dej pohled pro vytvoření
        if (isset($parametry[0]) && $parametry[0] === 'vytvor') {
            // Vytáhneme VŠECHNY karty z tabulky karty pro deck building
            $this->data['all_cards'] = Db::dotazVsechny("SELECT name, id, deck, row, strength, ability, filename FROM karty");
            
            $this->pohled = "balik_vytvor";
            return; // Ukončíme metodu, zbytek kódu se nespustí
        }

        // kontroluje jestli jsme na specificke karte nebo ne a podle toho zobrazuje informace
        if (isset($parametry[0])) {
            $this->data['isDetail'] = true;
            $balikId = $parametry[0];

            //? tohle by asi melo byt v nejake te classe 
            $radek = Db::dotazJeden("
                SELECT balicek_id, nazev, popis, autor_balicek
                FROM balicek
                WHERE balicek_id = ?
            ", array($balikId));

            if (!$radek) {
                $this->pohled = "chyba";
                return;
            }

            $balik = Balik::newFromAssocArray($radek);

            $this->data['deck']  = $balik;
            $this->data['cards'] = $balik->getKarty();
            $this->data['tags']  = $balik->getTagy();
            
        } else {
            $this->data['isDetail'] = false;

            //? stejne tak tohle 
            $radky = Db::dotazVsechny("
                SELECT balicek_id, nazev, popis, autor_balicek
                FROM balicek
            ");

            $this->data['decks'] = array_map(
                fn($radek) => Balik::newFromAssocArray($radek),
                $radky
            );
        }
    }
}