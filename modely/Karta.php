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


}