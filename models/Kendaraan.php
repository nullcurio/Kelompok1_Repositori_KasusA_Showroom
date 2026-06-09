<?php
/**
 * Abstract Class Kendaraan
 * Induk Class penampung data modular[cite: 1].
 */
abstract class Kendaraan {
    protected $idKendaraan;
    protected $brand;
    protected $model;
    protected $tahun;
    protected $stok;
    protected $hargaDasar;
    protected $transmisi;
    protected $jumlahKursi;
    protected $statusPajak;

    public function __construct($idKendaraan, $brand, $model, $tahun, $stok, $hargaDasar, $transmisi, $jumlahKursi, $statusPajak) {
        $this->idKendaraan = $idKendaraan;
        $this->brand = $brand;
        $this->model = $model;
        $this->tahun = $tahun;
        $this->stok = $stok;
        $this->hargaDasar = $hargaDasar;
        $this->transmisi = $transmisi;
        $this->jumlahKursi = $jumlahKursi;
        $this->statusPajak = $statusPajak;
    }

    public function getIdKendaraan() { return $this->idKendaraan; }
    public function getBrand() { return $this->brand; }
    public function getModel() { return $this->model; }
    public function getTahun() { return $this->tahun; }
    public function getStok() { return $this->stok; }
    public function getHargaDasar() { return $this->hargaDasar; }
    public function getTransmisi() { return $this->transmisi; }
    public function getJumlahKursi() { return $this->jumlahKursi; }
    public function getStatusPajak() { return $this->statusPajak; }

    abstract public function hitungPajakTahunan();
    abstract public function tampilkanSpesifikasi();
}