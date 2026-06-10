<?php
require_once __DIR__ . '/Kendaraan.php';

class MobilKonvensional extends Kendaraan {
    private $kapasitasMesin;
    private $jenisBahanBakar;
    private $kapasitasTangki;
    private $konsumsiBbm;

    public function __construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak, $kapasitasMesin, $jenisBahanBakar, $kapasitasTangki, $konsumsiBbm) {
        parent::__construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak);
        $this->kapasitasMesin = $kapasitasMesin;
        $this->jenisBahanBakar = $jenisBahanBakar;
        $this->kapasitasTangki = $kapasitasTangki;
        $this->konsumsiBbm = $konsumsiBbm;
    }

    public function getKapasitasMesin() { return $this->kapasitasMesin; }
    public function getKapasitasTangki() { return $this->kapasitasTangki; }

    public function hitungPajakTahunan() {
        // (2% * Harga) + (Mesin * 500)
        return (0.02 * $this->hargaDasar) + ($this->kapasitasMesin * 500);
    }

    public function tampilkanSpesifikasi() {
        return $this->kapasitasMesin . " cc";
    }
}