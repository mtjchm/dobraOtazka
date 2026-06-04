<?php
class BalikyKontroler extends Kontroler {
    public function zpracuj($parametry) {
        
        // --- CHYTÁNÍ UKLÁDÁNÍ BALÍČKU PŘES AJAX ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);

            if (!$input || empty($input['nazev'])) {
                echo json_encode(['success' => false, 'message' => 'Neplatná data balíčku.']);
                exit;
            }

            // Ověř přihlášení
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Nejsi přihlášen.']);
                exit;
            }
            $autorId = (int) $_SESSION['user_id'];

            try {
                // 1. Vložení balíčku — použijeme dotaz() pro raw SQL (ne zmen(), to je pro UPDATE)
                Db::dotaz(
                    "INSERT INTO balicek (nazev, popis, autor_balicek) VALUES (?, ?, ?)",
                    [$input['nazev'], $input['popis'] ?? '', $autorId]
                );

                // 2. Získání ID nově vloženého balíčku přes metodu z Db třídy
                $novyBalicekId = (int) Db::idPoslednihoVlozeneho();

                if ($novyBalicekId === 0) {
                    throw new Exception("Nepodařilo se získat ID nového balíčku.");
                }

                // 3. Vložení karet do vazební tabulky balik_karta
                if (!empty($input['karty']) && is_array($input['karty'])) {
                    foreach ($input['karty'] as $karta) {
                        if (empty($karta['name']) || !isset($karta['count'])) continue;
                        Db::dotaz(
                            "INSERT INTO balik_karta (karta_name, balicek_id, pocet) VALUES (?, ?, ?)",
                            [$karta['name'], $novyBalicekId, (int) $karta['count']]
                        );
                    }
                }

                echo json_encode(['success' => true, 'id' => $novyBalicekId]);
                exit;

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        // --- KLASICKÉ ZOBRAZENÍ STRÁNEK ---
        $this->pohled = "baliky";
        $this->data = array();

        // Pokud jde uživatel na /baliky/vytvor, dáme mu data karet a pohled
        if (isset($parametry[0]) && $parametry[0] === 'vytvor') {
            $this->data['all_cards'] = Db::dotazVsechny(
                "SELECT name, id, deck, row, strength, ability, filename, count FROM karty"
            );
            $this->pohled = "balik_vytvor";
            return; 
        }

        // Kontrola specifického detailu balíčku
        if (isset($parametry[0])) {
            $this->data['isDetail'] = true;
            $balikId = (int) $parametry[0];

            $radek = Db::dotazJeden(
                "SELECT balicek_id, nazev, popis, autor_balicek FROM balicek WHERE balicek_id = ?",
                [$balikId]
            );

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

            $radky = Db::dotazVsechny(
                "SELECT balicek_id, nazev, popis, autor_balicek FROM balicek"
            );

            $this->data['decks'] = array_map(
                fn($radek) => Balik::newFromAssocArray($radek),
                $radky
            );
        }
    }
}