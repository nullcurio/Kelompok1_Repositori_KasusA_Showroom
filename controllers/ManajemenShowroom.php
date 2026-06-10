<?php
require_once __DIR__ . '/../models/MobilKonvensional.php';
require_once __DIR__ . '/../models/MobilListrik.php';
require_once __DIR__ . '/../models/MobilHybrid.php';
require_once __DIR__ . '/../models/MotorBesar.php';

class ManajemenShowroom {
    protected $listKendaraan = [];

    public function loadDataFromDatabase($dbConn) {
        $this->listKendaraan = []; // Clear collection[cite: 1]

        $query = "SELECT k.*, 
                         mkonv.kapasitas_mesin AS cc_konv, mkonv.jenis_bahan_bakar AS bbm_konv, mkonv.kapasitas_tangki AS tangki_konv, mkonv.konsumsi_bbm AS irit_konv,
                         mlist.kapasitas_baterai AS bat_list, mlist.daya_motor_listrik AS daya_list, mlist.waktu_pengisian AS cas_list, mlist.jarak_tempuh AS jarak_list, mlist.kecepatan_maksimum AS top_list,
                         mhyb.kapasitas_mesin AS cc_hyb, mhyb.jenis_bahan_bakar AS bbm_hyb, mhyb.kapasitas_tangki AS tangki_hyb, mhyb.kapasitas_baterai AS bat_hyb, mhyb.daya_motor_listrik AS daya_hyb, mhyb.tipe_hybrid AS tipe_hyb, mhyb.mode_berkendara AS mode_hyb, mhyb.konsumsi_bbm AS irit_hyb,
                         mobe.jenis_bahan_bakar AS bbm_mobe, mobe.kapasitas_mesin AS cc_mobe, mobe.kapasitas_tangki AS tangki_mobe, mobe.konsumsi_bbm AS irit_mobe, mobe.tipe_motor AS tipe_mobe
                  FROM tb_kendaraan k
                  LEFT JOIN tb_mobil_konvensional mkonv ON k.id_kendaraan = mkonv.id_kendaraan
                  LEFT JOIN tb_mobil_listrik mlist ON k.id_kendaraan = mlist.id_kendaraan
                  LEFT JOIN tb_mobil_hybrid mhyb ON k.id_kendaraan = mhyb.id_kendaraan
                  LEFT JOIN tb_motor_besar mobe ON k.id_kendaraan = mobe.id_kendaraan
                  ORDER BY k.id_kendaraan ASC"; //[cite: 1]

        $stmt = $dbConn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { //[cite: 1]
            if ($row['cc_konv'] !== null) { //[cite: 1]
                $this->listKendaraan[] = new MobilKonvensional(
                    $row['id_kendaraan'], $row['brand'], $row['model'], $row['tahun'], $row['stok'], $row['harga_dasar'], $row['transmisi'], $row['jumlah_kursi'], $row['status_pajak'],
                    $row['cc_konv'], $row['bbm_konv'], $row['tangki_konv'], $row['irit_konv']
                ); //[cite: 1]
            } elseif ($row['bat_list'] !== null) { //[cite: 1]
                $this->listKendaraan[] = new MobilListrik(
                    $row['id_kendaraan'], $row['brand'], $row['model'], $row['tahun'], $row['stok'], $row['harga_dasar'], $row['transmisi'], $row['jumlah_kursi'], $row['status_pajak'],
                    $row['bat_list'], $row['daya_list'], $row['cas_list'], $row['jarak_list'], $row['top_list']
                ); //[cite: 1]
            } elseif ($row['cc_hyb'] !== null) { //[cite: 1]
                $this->listKendaraan[] = new MobilHybrid(
                    $row['id_kendaraan'], $row['brand'], $row['model'], $row['tahun'], $row['stok'], $row['harga_dasar'], $row['transmisi'], $row['jumlah_kursi'], $row['status_pajak'],
                    $row['cc_hyb'], $row['bbm_hyb'], $row['tangki_hyb'], $row['bat_hyb'], $row['daya_hyb'], $row['tipe_hyb'], $row['mode_hyb'], $row['irit_hyb']
                ); //[cite: 1]
            } elseif ($row['cc_mobe'] !== null) { //[cite: 1]
                $this->listKendaraan[] = new MotorBesar(
                    $row['id_kendaraan'], $row['brand'], $row['model'], $row['tahun'], $row['stok'], $row['harga_dasar'], $row['transmisi'], $row['jumlah_kursi'], $row['status_pajak'],
                    $row['bbm_mobe'], $row['cc_mobe'], $row['tangki_mobe'], $row['irit_mobe'], $row['tipe_mobe']
                ); //[cite: 1]
            }
        }
    }

    public function getListKendaraan() {
        return $this->listKendaraan;
    }

    public function getFilteredData($category = 'all', $search = '') {
        $filtered = [];
        $search = strtolower(trim($search));

        foreach ($this->listKendaraan as $item) {
            $matchCategory = false;
            $className = get_class($item);

            if ($category === 'all') $matchCategory = true;
            elseif ($category === 'konvensional' && $className === 'MobilKonvensional') $matchCategory = true;
            elseif ($category === 'listrik' && $className === 'MobilListrik') $matchCategory = true;
            elseif ($category === 'hybrid' && $className === 'MobilHybrid') $matchCategory = true;
            elseif ($category === 'motor' && $className === 'MotorBesar') $matchCategory = true;

            $matchSearch = true;
            if ($search !== '') {
                $haystack = strtolower($item->getBrand() . ' ' . $item->getModel() . ' ' . $item->getTahun() . ' ' . $item->getTransmisi());
                if (strpos($haystack, $search) === false) {
                    $matchSearch = false;
                }
            }

            if ($matchCategory && $matchSearch) {
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
}