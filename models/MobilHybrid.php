<?php
require_once __DIR__ . '/Kendaraan.php';

class MobilHybrid extends Kendaraan {
    private $kapasitasMesin;
    private $jenisBahanBakar;
    private $kapasitasTangki;
    private $kapasitasBaterai;
    private $dayaMotorListrik;
    private $tipeHybrid;
    private $modeBerkendara;
    private $konsumsiBbm;

    public function __construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak, $kapasitasMesin, $jenisBahanBakar, $kapasitasTangki, $kapasitasBaterai, $dayaMotorListrik, $tipeHybrid, $modeBerkendara, $konsumsiBbm) {
        parent::__construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak);
        $this->kapasitasMesin = $kapasitasMesin;
        $this->jenisBahanBakar = $jenisBahanBakar;
        $this->kapasitasTangki = $kapasitasTangki;
        $this->kapasitasBaterai = $kapasitasBaterai;
        $this->dayaMotorListrik = $dayaMotorListrik;
        $this->tipeHybrid = $tipeHybrid;
        $this->modeBerkendara = $modeBerkendara;
        $this->konsumsiBbm = $konsumsiBbm;
    }

    public function getKapasitasMesin() { return $this->kapasitasMesin; }

    public function hitungPajakTahunan() {
        // Sesuai rumus gambar pendukung: (1% * Harga) + (Mesin * 250)
        return (0.01 * $this->hargaDasar) + ($this->kapasitasMesin * 250);
    }

    public function tampilkanSpesifikasi() {
        return $this->kapasitasMesin . " cc";
    }
}