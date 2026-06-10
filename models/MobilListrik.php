<?php
require_once __DIR__ . '/Kendaraan.php';

class MobilListrik extends Kendaraan {
    private $kapasitasBaterai;
    private $dayaMotorListrik;
    private $waktuPengisian;
    private $jarakTempuh;
    private $kecepatanMaksimum;

    public function __construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak, $kapasitasBaterai, $dayaMotorListrik, $waktuPengisian, $jarakTempuh, $kecepatanMaksimum) {
        parent::__construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak);
        $this->kapasitasBaterai = $kapasitasBaterai;
        $this->dayaMotorListrik = $dayaMotorListrik;
        $this->waktuPengisian = $waktuPengisian;
        $this->jarakTempuh = $jarakTempuh;
        $this->kecepatanMaksimum = $kecepatanMaksimum;
    }

    public function hitungPajakTahunan() {
        // 0.5% * Harga Dasar
        return 0.005 * $this->hargaDasar;
    }

    public function tampilkanSpesifikasi() {
        return $this->kapasitasBaterai . " kWh";
    }
}