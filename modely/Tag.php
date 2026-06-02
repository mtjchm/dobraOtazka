<?php
class Tag {
    public int $tag_id;
    public string $nazev;
    public string $popis;
    public Uzivatel $autor;

    public function __construct(int $tag_id, string $nazev, string $popis, int $autor_id)
    {
        $this->tag_id = $tag_id;
        $this->nazev = $nazev;
        $this->popis = $popis;
        $this->autor = Uzivatel::getById($autor_id);
    }
}