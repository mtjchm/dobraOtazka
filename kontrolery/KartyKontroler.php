<?php
class KartyKontroler extends Kontroler {
    public function zpracuj($parametry) {
        $this->pohled = "karty";
        $kartyAssArr = Db::dotazVsechny("SELECT * FROM karty ORDER BY deck");
        $this->data["echo"] = var_export($parametry, true);
        $this->data["cards"] = $kartyAssArr;
    }
}