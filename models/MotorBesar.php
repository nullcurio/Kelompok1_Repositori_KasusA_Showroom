<?php
require_once __DIR__ . '/Kendaraan.php';

class MotorBesar extends Kendaraan {
    private $jenisBahanBakar;
    private $kapasitasMesin;
    private $kapasitasTangki;
    private $konsumsiBbm;
    private $tipeMotor;

    public function __construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak, $jenisBahanBakar, $kapasitasMesin, $kapasitasTangki, $konsumsiBbm, $tipeMotor) {
        parent::__construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak);
        $this->jenisBahanBakar = $jenisBahanBakar;
        $this->kapasitasMesin = $kapasitasMesin;
        $this->kapasitasTangki = $kapasitasTangki;
        $this->konsumsiBbm = $konsumsiBbm;
        $this->tipeMotor = $tipeMotor;
    }

    public function getKapasitasMesin() { return $this->kapasitasMesin; }
    public function getTipeMotor() { return $this->tipeMotor; }
    public function getKapasitasTangki() { return $this->kapasitasTangki; }

    public function hitungPajakTahunan() {
        // 1.5% * Harga Dasar
        return 0.015 * $this->hargaDasar;
    }

    public function tampilkanSpesifikasi() {
        return $this->kapasitasMesin . " cc";
    }
}