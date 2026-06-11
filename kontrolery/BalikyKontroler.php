<?php
class BalikyKontroler extends Kontroler {
    public function zpracuj($parametry) {
        
        // --- CHYTÁNÍ POST POŽADAVKŮ (Mazání a Ukládání) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);

            // Ověření přihlášení uživatele
            if (!isset($_SESSION['uid'])) {
                echo json_encode(['success' => false, 'message' => 'Nejsi přihlášen.']);
                exit;
            }
            $autorId = (int) $_SESSION['uid'];

            // A: Pokud v URL parametru je 'smazat' (fetch z main.phtml)
            if (isset($parametry[0]) && $parametry[0] === 'smazat') {
                if (!isset($input['balicek_id'])) {
                    echo json_encode(['success' => false, 'message' => 'Chybí ID balíčku.']);
                    exit;
                }

                $balicekId = (int) $input['balicek_id'];
                $smazano = Balik::smazatPodleIdAAutora($balicekId, $autorId);

                if ($smazano) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Balíček nebyl nalezen nebo nemáte oprávnění ho smazat.']);
                }
                exit; 
            }

            // B: Pokud v URL parametru je 'vytvor' (Ukládání nového balíčku)
            if (isset($parametry[0]) && $parametry[0] === 'vytvor') {
                if (!$input || empty($input['nazev'])) {
                    echo json_encode(['success' => false, 'message' => 'Neplatná data balíčku.']);
                    exit;
                }

                try {
                    Db::dotaz(
                        "INSERT INTO balicek (nazev, popis, autor_balicek) VALUES (?, ?, ?)",
                        [$input['nazev'], $input['popis'] ?? '', $autorId]
                    );

                    $novyBalicekId = Db::dotazSamotny("SELECT LAST_INSERT_ID()");
                    //echo json_encode(['success' => false, 'message' => var_export($input["karty"])]);

                    if (!empty($input['karty']) && is_array($input['karty'])) {
                        foreach ($input['karty'] as $karta) {
                            if (!empty($karta['name']) && isset($karta['count'])) {
                                Db::dotaz(
                                    "INSERT INTO balik_karta (balicek_id, karta_name, pocet) VALUES (?, ?, ?)",
                                    [$novyBalicekId, $karta['name'], (int)$karta['count']]
                                );
                            }
                        }
                    }

                    echo json_encode(['success' => true]);
                    exit;

                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Chyba databáze: ' . $e->getMessage()]);
                    exit;
                }
            }
            
            echo json_encode(['success' => false, 'message' => 'Neznámá akce.']);
            exit;
        }

        // --- --- --- ZOBRAZOVÁNÍ STRÁNEK (GET) --- --- ---
        $this->data = array();
        // Definujeme isDetail jako false ve výchozím stavu, aby baliky.phtml nehlásil chybu
        $this->data['isDetail'] = false; 

        // 1. Pokud jde uživatel na /baliky/vytvor
        if (isset($parametry[0]) && $parametry[0] === 'vytvor') {
            $this->data['all_cards'] = Db::dotazVsechny(
                "SELECT name, id, deck, `row`, strength, ability, filename, count FROM karty"
            );
            $this->pohled = "balik_vytvor";
            return; 
        }

        // 2. Pokud jde uživatel na detail konkrétního balíčku (např. /baliky/5)
        if (isset($parametry[0]) && is_numeric($parametry[0])) {
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
            $this->pohled = "baliky";
            return;
        } 
        
        // 3. Výchozí stav: Uživatel je na přehledu /baliky (chce vidět VŠECHNY balíčky)
        // OPRAVENO: Tady načteme všechny balíčky do proměnné $decks, aby baliky.phtml mohl běžet!
        $radky = Db::dotazVsechny("SELECT balicek_id, nazev, popis, autor_balicek FROM balicek");
        $this->data['decks'] = array_map(
            fn($radek) => Balik::newFromAssocArray($radek),
            $radky
        );
        $this->pohled = "baliky";
    }
}