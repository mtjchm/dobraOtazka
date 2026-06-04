<?php
class Karta {
    public $name;
    public $id;
    public $deck;
    public $row;
    public $strength;
    public $ability;
    public $filename;
    public $count;
    //POUZE PRO DECK BUILDING
    private int $amount = 0;

    /**
     * @param $name
     * @param $id
     * @param $deck
     * @param $row
     * @param $strength
     * @param $ability
     * @param $filename
     * @param $count
     */
    public function __construct($name, $id, $deck, $row, $strength, $ability, $filename, $count)
    {
        $this->name = $name;
        $this->id = $id;
        $this->deck = $deck;
        $this->row = $row;
        $this->strength = $strength;
        $this->ability = $ability;
        $this->filename = $filename;
        $this->count = $count;
    }

    public static function newFromAssocArray($assocArray): Karta {
        return new Karta(
            name: $assocArray['name'],
            id: $assocArray['id'],
            deck: $assocArray['deck'],
            row: $assocArray['row'],
            strength: $assocArray['strength'],
            ability: $assocArray['ability'],
            filename: $assocArray['filename'],
            count: $assocArray['count']
        );
    }

    public static function getKartaByName(string $karta_name): Karta | null {
        return Db::dotazJeden("SELECT * FROM karty WHERE name LIKE ?", [$karta_name]);
    }

    public static function addKartaAmount(Karta $karta, int $amount): bool {
        if ($karta->amount + $amount < 0 || $karta->amount + $amount > $karta->count) {
            return false;
        }

        $karta->amount += $amount;
        return true;
    }
}