<?php
// =========================================================================
// 1. INITIALIZATION & OOP ENGINE LOADING (ADMINLTE 4 CONTROLLER)
// =========================================================================
require_once 'config/Database.php';
require_once 'controllers/ManajemenShowroom.php';
require_once 'models/Kendaraan.php';
require_once 'models/MobilKonvensional.php';
require_once 'models/MobilListrik.php';
require_once 'models/MobilHybrid.php';
require_once 'models/MotorBesar.php';

$db = new Database();
$manajemenShowroom = new ManajemenShowroom();
$manajemenShowroom->loadDataFromDatabase($db->conn);

// Routing State berdasarkan parameter URL (AdminLTE 4 Pattern)
$currentMenu     = isset($_GET['menu']) ? $_GET['menu'] : 'dashboard';       
$currentCategory = isset($_GET['kategori']) ? $_GET['kategori'] : 'all';     
$statusPajak     = isset($_GET['status_pajak']) ? $_GET['status_pajak'] : 'all'; 
$searchKeyword   = isset($_GET['search']) ? $_GET['search'] : '';

// LOGIKA TOGGLE & RESET RESET MENU (Jika diklik lagi, menu & kategori kembali ke default 'dashboard' & 'all')
$nextMenuPajakAktif    = ($currentMenu === 'pajak' && $statusPajak === 'Aktif') ? 'dashboard' : 'pajak';
$nextStatusAktif       = ($currentMenu === 'pajak' && $statusPajak === 'Aktif') ? 'all' : 'Aktif';

$nextMenuPajakTidak    = ($currentMenu === 'pajak' && $statusPajak === 'Tidak Aktif') ? 'dashboard' : 'pajak';
$nextStatusTidakAktif  = ($currentMenu === 'pajak' && $statusPajak === 'Tidak Aktif') ? 'all' : 'Tidak Aktif';

$nextCategoryKonvensional = ($currentCategory === 'konvensional') ? 'all' : 'konvensional';
$nextCategoryHybrid       = ($currentCategory === 'hybrid') ? 'all' : 'hybrid';
$nextCategoryListrik      = ($currentCategory === 'listrik') ? 'all' : 'listrik';
$nextCategoryMotor        = ($currentCategory === 'motor') ? 'all' : 'motor';

// 1. Ambil seluruh data mentah dari koleksi objek (Polymorphic Array)
$allData = $manajemenShowroom->getListKendaraan();

// 2. OOP Filtering Data untuk View Table berdasarkan State Navigasi AdminLTE 4
$daftarKendaraan = [];
$searchKeywordLower = strtolower(trim($searchKeyword));

foreach ($allData as $item) {
    $className = get_class($item);
    $matchCategory = false;
    
    // Filter Subclass Polymorphism
    if ($currentCategory === 'all') $matchCategory = true;
    elseif ($currentCategory === 'konvensional' && $className === 'MobilKonvensional') $matchCategory = true;
    elseif ($currentCategory === 'hybrid' && $className === 'MobilHybrid') $matchCategory = true;
    elseif ($currentCategory === 'listrik' && $className === 'MobilListrik') $matchCategory = true;
    elseif ($currentCategory === 'motor' && $className === 'MotorBesar') $matchCategory = true;

    // Filter Status Kepatuhan Pajak (Submenu Treeview)
    $matchStatus = true;
    if ($statusPajak !== 'all' && $item->getStatusPajak() !== $statusPajak) {
        $matchStatus = false;
    }

    // Filter Search Bar Engine
    $matchSearch = true;
    if ($searchKeywordLower !== '') {
        $haystack = strtolower($item->getBrand() . ' ' . $item->getModel() . ' ' . $item->getTahun() . ' ' . $item->getTransmisi());
        if (strpos($haystack, $searchKeywordLower) === false) {
            $matchSearch = false;
        }
    }

    if ($matchCategory && $matchStatus && $matchSearch) {
        $daftarKendaraan[] = $item;
    }
}

// =========================================================================
// 2. AKUMULASI METRIK DINAMIS UNTUK 4 TOP CARDS (ADMINLTE 4 WIDGETS)
// =========================================================================
$totalKendaraan = 0;
$totalPajakTahunan = 0;
$totalNilaiStok = 0;
$pajakAktifCount = 0;
$pajakMatiCount = 0;

foreach ($daftarKendaraan as $knd) {
    $stokUnit = $knd->getStok();
    $pajakUnit = $knd->hitungPajakTahunan();
    
    $totalKendaraan += $stokUnit;
    $totalPajakTahunan += ($pajakUnit * $stokUnit);
    $totalNilaiStok += ($knd->getHargaDasar() * $stokUnit);
    
    if ($knd->getStatusPajak() === 'Aktif') {
        $pajakAktifCount++;
    } else {
        $pajakMatiCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShowroomPro - AdminLTE 4 Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;600;700;800&display=fallback">
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
    </style>
</head>
<body class="bg-[#f8f9fa] flex h-screen text-[#212529] text-sm antialiased overflow-hidden">

    <aside class="w-64 bg-[#1a2229] text-[#adb5bd] flex flex-col justify-between shrink-0 shadow-lg z-20">
        <div>
            <div class="px-4 py-4 flex items-center gap-3 bg-[#13191e] border-b border-[#2d373f]">
                <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white text-sm shadow-sm">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white text-sm tracking-tight leading-none">
                        Showroom<span class="text-blue-400">Pro</span>
                    </h1>
                    <span class="text-[10px] text-gray-500 font-medium tracking-wider uppercase block mt-0.5">ADMIN PANEL</span>
                </div>
            </div>

            <div class="px-4 py-3 flex items-center gap-3 border-b border-[#2d373f]/60 bg-[#161d23]/40">
                <div class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center text-white text-xs border border-slate-500">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-bold text-white">Administrator</p>
                    <span class="text-[10px] text-emerald-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Kelompok PBO</span>
                </div>
            </div>

            <nav class="p-2 space-y-1">
                <div class="text-[10px] font-bold text-gray-500 uppercase px-3 py-1.5 tracking-wider">HEADER</div>
                
                <a href="index.php?menu=dashboard&kategori=all&status_pajak=all" class="flex items-center gap-2.5 px-3 py-2 rounded text-xs transition-all <?= ($currentMenu === 'dashboard' && $currentCategory === 'all' && $statusPajak === 'all') ? 'bg-blue-500 text-white font-semibold shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>" onclick="sessionStorage.clear();">
                    <i class="fa-solid fa-chart-pie w-4 text-center text-blue-400 <?= ($currentMenu === 'dashboard' && $currentCategory === 'all' && $statusPajak === 'all') ? 'text-white' : '' ?>"></i> 
                    <span>Dashboard (Semua)</span>
                </a>

                <div>
                    <a href="#" id="btn-dropdown-pajak" class="flex items-center justify-between px-3 py-2 rounded text-xs transition-all hover:bg-slate-800 hover:text-white">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-coins w-4 text-center text-red-400"></i>
                            <span>Kalkulasi Pajak</span>
                        </div>
                        <i id="arrow-pajak" class="fa-solid fa-angle-left text-[10px] transform transition-transform text-gray-500"></i>
                    </a>
                    
                    <div id="dropdown-pajak" class="pl-4 mt-0.5 space-y-0.5 hidden bg-[#13191e]/50 py-1 rounded">
                        <a href="index.php?menu=<?= $nextMenuPajakAktif ?>&kategori=<?= $currentCategory ?>&status_pajak=<?= $nextStatusAktif ?>" 
                           id="link-pajak-aktif"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= ($currentMenu === 'pajak' && $statusPajak === 'Aktif') ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-dot-circle text-[8px] text-emerald-400"></i> Pajak Aktif
                            </div>
                            <?= ($currentMenu === 'pajak' && $statusPajak === 'Aktif') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                        
                        <a href="index.php?menu=<?= $nextMenuPajakTidak ?>&kategori=<?= $currentCategory ?>&status_pajak=<?= $nextStatusTidakAktif ?>" 
                           id="link-pajak-tidakaktif"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= ($currentMenu === 'pajak' && $statusPajak === 'Tidak Aktif') ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-dot-circle text-[8px] text-red-400"></i> Pajak Tidak Aktif
                            </div>
                            <?= ($currentMenu === 'pajak' && $statusPajak === 'Tidak Aktif') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                    </div>
                </div>

                <div>
                    <a href="#" id="btn-dropdown-kendaraan" class="flex items-center justify-between px-3 py-2 rounded text-xs transition-all hover:bg-slate-800 hover:text-white">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-car w-4 text-center text-emerald-400"></i>
                            <span>Kendaraan</span>
                        </div>
                        <i id="arrow-kendaraan" class="fa-solid fa-angle-left text-[10px] transform transition-transform text-gray-500"></i>
                    </a>
                    
                    <div id="dropdown-kendaraan" class="pl-4 mt-0.5 space-y-0.5 hidden bg-[#13191e]/50 py-1 rounded">
                        <a href="index.php?menu=<?= $currentMenu ?>&kategori=<?= $nextCategoryKonvensional ?>&status_pajak=<?= $statusPajak ?>" 
                           id="link-kat-konvensional"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= $currentCategory === 'konvensional' ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle text-[6px] text-orange-400 w-3 text-center"></i> Mobil Konvensional
                            </div>
                            <?= ($currentCategory === 'konvensional') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                        
                        <a href="index.php?menu=<?= $currentMenu ?>&kategori=<?= $nextCategoryHybrid ?>&status_pajak=<?= $statusPajak ?>" 
                           id="link-kat-hybrid"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= $currentCategory === 'hybrid' ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle text-[6px] text-amber-400 w-3 text-center"></i> Mobil Hybrid
                            </div>
                            <?= ($currentCategory === 'hybrid') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                        
                        <a href="index.php?menu=<?= $currentMenu ?>&kategori=<?= $nextCategoryListrik ?>&status_pajak=<?= $statusPajak ?>" 
                           id="link-kat-listrik"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= $currentCategory === 'listrik' ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle text-[6px] text-blue-400 w-3 text-center"></i> Mobil Listrik
                            </div>
                            <?= ($currentCategory === 'listrik') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                        
                        <a href="index.php?menu=<?= $currentMenu ?>&kategori=<?= $nextCategoryMotor ?>&status_pajak=<?= $statusPajak ?>" 
                           id="link-kat-motor"
                           class="flex items-center justify-between px-3 py-1.5 rounded text-[11px] <?= $currentCategory === 'motor' ? 'text-blue-400 font-bold bg-slate-800 border-r-2 border-blue-400' : 'text-gray-400 hover:text-white' ?>">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle text-[6px] text-purple-400 w-3 text-center"></i> Motor Besar
                            </div>
                            <?= ($currentCategory === 'motor') ? '<i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i>' : '' ?>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="p-3 bg-[#13191e] text-[10px] border-t border-[#2d373f] text-gray-500 font-mono flex justify-between px-4">
            <span>SHOWROOM ENGINE</span>
            <span>PURE-OOP</span>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white border-b border-gray-200 px-4 py-2.5 flex items-center justify-between shrink-0 shadow-2xs">
            <div class="flex items-center gap-2 text-xs">
                <span class="text-gray-400 font-normal"><i class="fa-solid fa-bars mr-2 text-gray-500"></i> Home</span>
                <i class="fa-solid fa-angle-right text-[8px] text-gray-300"></i>
                <span class="text-gray-500 font-normal capitalize"><?= $currentMenu ?></span>
                <i class="fa-solid fa-angle-right text-[8px] text-gray-300"></i>
                <span class="text-gray-900 font-bold uppercase text-[11px]"><?= $currentCategory ?></span>
            </div>
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span class="text-[11px] font-semibold bg-gray-100 border border-gray-200 px-2 py-0.5 rounded text-gray-600">
                    <i class="fa-solid fa-database text-blue-500 mr-1"></i> Database: pbo_dbshowroom
                </span>
                <span class="text-gray-400"><i class="fa-regular fa-clock mr-1"></i> 09 Jun 2026</span>
            </div>
        </header>

        <div class="p-4 space-y-4 max-w-[1680px] w-full mx-auto">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded border border-gray-200 shadow-2xs flex items-stretch overflow-hidden">
                    <div class="w-3 bg-blue-500"></div>
                    <div class="p-3 flex-1 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">TOTAL KENDARAAN</span>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-0.5"><?= $totalKendaraan ?> <span class="text-xs font-normal text-gray-400">unit</span></h3>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded flex items-center justify-center text-lg"><i class="fa-solid fa-car-side"></i></div>
                    </div>
                </div>
                <div class="bg-white rounded border border-gray-200 shadow-2xs flex items-stretch overflow-hidden">
                    <div class="w-3 bg-red-500"></div>
                    <div class="p-3 flex-1 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">TOTAL PAJAK / THN</span>
                            <h3 class="text-2xl font-extrabold text-red-600 mt-0.5">Rp <?= number_format($totalPajakTahunan, 0, ',', '.') ?></h3>
                        </div>
                        <div class="w-10 h-10 bg-red-50 text-red-500 rounded flex items-center justify-center text-lg"><i class="fa-solid fa-scale-balanced"></i></div>
                    </div>
                </div>
                <div class="bg-white rounded border border-gray-200 shadow-2xs flex items-stretch overflow-hidden">
                    <div class="w-3 bg-emerald-500"></div>
                    <div class="p-3 flex-1 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">TOTAL NILAI STOK</span>
                            <h3 class="text-2xl font-extrabold text-emerald-600 mt-0.5">Rp <?= number_format($totalNilaiStok, 0, ',', '.') ?></h3>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded flex items-center justify-center text-lg"><i class="fa-solid fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="bg-white rounded border border-gray-200 shadow-2xs flex items-stretch overflow-hidden">
                    <div class="w-3 bg-purple-500"></div>
                    <div class="p-3 flex-1 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">KEPATUHAN PAJAK</span>
                            <h3 class="text-2xl font-extrabold text-purple-700 mt-0.5"><?= $pajakAktifCount ?> / <?= count($daftarKendaraan) ?></h3>
                            <span class="text-[10px] text-red-500 font-bold block mt-0.5">🔴 <?= $pajakMatiCount ?> OFF</span>
                        </div>
                        <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded flex items-center justify-center text-lg"><i class="fa-solid fa-shield-halved"></i></div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 rounded border border-gray-200 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <form action="index.php" method="GET" class="w-full sm:w-[450px] flex items-center gap-2">
                    <input type="hidden" name="menu" value="<?= htmlspecialchars($currentMenu) ?>">
                    <input type="hidden" name="kategori" value="<?= htmlspecialchars($currentCategory) ?>">
                    <input type="hidden" name="status_pajak" value="<?= htmlspecialchars($statusPajak) ?>">
                    <div class="relative w-full">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-xs"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($searchKeyword) ?>" 
                               placeholder="Cari brand, model, transmisi di halaman ini..." 
                               class="w-full bg-[#f8f9fa] text-xs pl-8 pr-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition-all font-medium">
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded shadow-2xs cursor-pointer">Filter</button>
                    <?php if($searchKeyword !== ''): ?>
                        <a href="index.php?menu=<?= $currentMenu ?>&kategori=<?= $currentCategory ?>&status_pajak=<?= $statusPajak ?>" class="text-xs text-red-500 hover:underline px-1">Reset</a>
                    <?php endif; ?>
                </form>
                <div class="text-[11px] font-semibold text-gray-400">
                    Sistem Penyaringan Polimorfik Aktif
                </div>
            </div>

            <div class="bg-white rounded border border-gray-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-[#f8f9fa] flex justify-between items-center">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-gray-400"></i> 
                        <?= $currentMenu === 'pajak' ? 'MODUL ANALISIS BEBAN FISKAL & KEPATUHAN OBJEK PAJAK' : 'RINGKASAN REKAPITULASI INVENTARIS UNIT KENDARAAN' ?>
                    </h3>
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded">
                        <?= count($daftarKendaraan) ?> Baris Ditemukan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#f8f9fa] border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                                <th class="p-3 text-center w-12 border-r border-gray-100">NO</th>
                                <th class="p-3 border-r border-gray-100">BRAND & MODEL</th>
                                <th class="p-3 text-center border-r border-gray-100">TAHUN</th>
                                <th class="p-3 border-r border-gray-100">CONCRETE OBJECT CLASS</th>
                                <th class="p-3 text-center border-r border-gray-100">STOK</th>
                                <th class="p-3 text-center border-r border-gray-100">TRANSMISI</th>
                                <th class="p-3 border-r border-gray-100">SPESIFIKASI ENGINE</th>
                                <?php if ($currentMenu === 'dashboard'): ?>
                                    <th class="p-3 text-right border-r border-gray-100">HARGA DASAR</th>
                                    <th class="p-3 text-right border-r border-gray-100 bg-gray-50/50">NILAI ASSET</th>
                                <?php endif; ?>
                                <th class="p-3 text-right border-r border-gray-100 text-red-700 bg-red-50/10">PAJAK / UNIT</th>
                                <th class="p-3 text-right border-r border-gray-100 text-red-800 bg-red-50/20">TOTAL BEBAN FISKAL</th>
                                <th class="p-3 text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 font-medium text-gray-700">
                            <?php 
                            if (empty($daftarKendaraan)): 
                            ?>
                                <tr>
                                    <td colspan="12" class="p-12 text-center text-gray-400">
                                        <i class="fa-solid fa-inbox text-2xl block mb-2 text-gray-300"></i>
                                        Tidak ditemukan data model unit showroom pada kriteria filter pencarian ini.
                                    </td>
                                </tr>
                            <?php 
                            else:
                                $no = 1;
                                $subtotalStok = 0;
                                $subtotalNilaiAset = 0;
                                $subtotalBebanPajak = 0;

                                foreach ($daftarKendaraan as $item):
                                    $className = get_class($item);
                                    $isPajakMati = ($item->getStatusPajak() === 'Tidak Aktif');
                                    
                                    $pajakMasingMasing = $item->hitungPajakTahunan();
                                    $totalBebanPajakModel = $pajakMasingMasing * $item->getStok();
                                    $nilaiAsetPerModel = $item->getHargaDasar() * $item->getStok();

                                    // Akumulasi Subtotal Halaman Terbuka
                                    $subtotalStok       += $item->getStok();
                                    $subtotalNilaiAset  += $nilaiAsetPerModel;
                                    $subtotalBebanPajak += $totalBebanPajakModel;

                                    // Mapping Badge AdminLTE 4 Style Flat Colors
                                    $badgeStyle = "bg-gray-100 text-gray-700 border border-gray-200";
                                    if ($className === 'MobilKonvensional') $badgeStyle = "bg-orange-100 text-orange-800";
                                    elseif ($className === 'MobilHybrid') $badgeStyle = "bg-amber-100 text-amber-800";
                                    elseif ($className === 'MobilListrik') $badgeStyle = "bg-blue-100 text-blue-800";
                                    elseif ($className === 'MotorBesar') $badgeStyle = "bg-purple-100 text-purple-800";
                            ?>
                                <tr class="<?= $isPajakMati ? 'bg-red-50/40 hover:bg-red-50/70' : 'hover:bg-gray-50/60' ?> transition-colors">
                                    <td class="p-2.5 text-center text-gray-400 border-r border-gray-100 font-normal"><?= $no++ ?></td>
                                    <td class="p-2.5 border-r border-gray-100">
                                        <div class="font-bold text-gray-900"><?= htmlspecialchars($item->getBrand()) ?></div>
                                        <div class="text-[11px] text-gray-400 font-normal"><?= htmlspecialchars($item->getModel()) ?></div>
                                    </td>
                                    <td class="p-2.5 text-center border-r border-gray-100 font-semibold text-gray-600"><?= $item->getTahun() ?></td>
                                    <td class="p-2.5 border-r border-gray-100">
                                        <span class="px-2 py-0.5 rounded font-bold text-[10px] tracking-wide <?= $badgeStyle ?>">
                                            <?= $className ?>
                                        </span>
                                    </td>
                                    <td class="p-2.5 text-center border-r border-gray-100">
                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded font-mono font-bold text-gray-700">
                                            <?= $item->getStok() ?>
                                        </span>
                                    </td>
                                    <td class="p-2.5 text-center border-r border-gray-100 text-gray-500 font-semibold"><?= $item->getTransmisi() ?></td>
                                    <td class="p-2.5 border-r border-gray-100 font-mono text-gray-600 font-bold"><?= $item->tampilkanSpesifikasi() ?></td>
                                    
                                    <?php if ($currentMenu === 'dashboard'): ?>
                                        <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-gray-900">Rp <?= number_format($item->getHargaDasar(), 0, ',', '.') ?></td>
                                        <td class="p-2.5 text-right border-r border-gray-100 font-bold text-gray-900 bg-gray-50/30">Rp <?= number_format($nilaiAsetPerModel, 0, ',', '.') ?></td>
                                    <?php endif; ?>

                                    <td class="p-2.5 text-right border-r border-gray-100 font-semibold text-red-600 bg-red-50/5">Rp <?= number_format($pajakMasingMasing, 0, ',', '.') ?></td>
                                    <td class="p-2.5 text-right border-r border-gray-100 font-bold text-red-700 bg-red-50/15">Rp <?= number_format($totalBebanPajakModel, 0, ',', '.') ?></td>
                                    
                                    <td class="p-2.5 text-center">
                                        <?php if (!$isPajakMati): ?>
                                            <span class="bg-emerald-500 text-white font-bold text-[9px] px-1.5 py-0.5 rounded-xs uppercase">Active</span>
                                        <?php else: ?>
                                            <span class="bg-red-500 text-white font-bold text-[9px] px-1.5 py-0.5 rounded-xs uppercase">Expired</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr class="bg-[#212529] text-white font-bold border-t border-gray-700 text-xs">
                                <td colspan="4" class="p-3 text-right font-bold text-gray-400 uppercase text-[9px] tracking-wider">SUBTOTAL HALAMAN INI:</td>
                                <td class="p-3 text-center bg-[#2c3034] text-yellow-400 font-extrabold border-r border-gray-700"><?= $subtotalStok ?> Unit</td>
                                <td colspan="2" class="border-r border-gray-700"></td>
                                
                                <?php if ($currentMenu === 'dashboard'): ?>
                                    <td class="border-r border-gray-700"></td>
                                    <td class="p-3 text-right text-emerald-400 border-r border-gray-700 font-extrabold">Rp <?= number_format($subtotalNilaiAset, 0, ',', '.') ?></td>
                                <?php endif; ?>

                                <td class="border-r border-gray-700"></td>
                                <td class="p-3 text-right text-red-400 bg-red-950/40 font-extrabold border-r border-gray-700">Rp <?= number_format($subtotalBebanPajak, 0, ',', '.') ?></td>
                                <td></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-2.5 bg-[#f8f9fa] border-t border-gray-200 text-[11px] text-gray-500 flex items-center justify-between">
                    <span><i class="fa-solid fa-code-branch text-blue-500 mr-1"></i> Data diikat dinamis menggunakan Dynamic Binding Runtime Environment.</span>
                    <span class="font-mono text-gray-400 text-[10px]">AdminLTE v4.0.0-Beta Core</span>
                </div>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil elemen DOM untuk Dropdown Pajak
            const btnPajak = document.getElementById('btn-dropdown-pajak');
            const dropdownPajak = document.getElementById('dropdown-pajak');
            const arrowPajak = document.getElementById('arrow-pajak');

            // Ambil elemen DOM untuk Dropdown Kendaraan
            const btnKendaraan = document.getElementById('btn-dropdown-kendaraan');
            const dropdownKendaraan = document.getElementById('dropdown-kendaraan');
            const arrowKendaraan = document.getElementById('arrow-kendaraan');

            // Ambil Elemen Anchor Link Submenu
            const linkPajakAktif = document.getElementById('link-pajak-aktif');
            const linkPajakTidak = document.getElementById('link-pajak-tidakaktif');
            const linkKonvensional = document.getElementById('link-kat-konvensional');
            const linkHybrid = document.getElementById('link-kat-hybrid');
            const linkListrik = document.getElementById('link-kat-listrik');
            const linkMotor = document.getElementById('link-kat-motor');

            // Ambil Parameter URL saat ini
            const urlParams = new URLSearchParams(window.location.search);
            const currentCat = urlParams.get('kategori') || 'all';
            const currentMen = urlParams.get('menu') || 'dashboard';
            const currentTax = urlParams.get('status_pajak') || 'all';

            // ==========================================
            // 1. LOGIKA UTAMA: PERTAHANKAN MENU UTAMA YANG SEDANG AKTIF
            // ==========================================
            
            // Atur Kondisi Awal Dropdown Pajak
            if (currentMen === 'pajak') {
                // Periksa apakah ini aksi klik ulang (reset kembali ke dashboard)
                if (sessionStorage.getItem('pajak_reset_action') === 'true') {
                    dropdownPajak.classList.add('hidden');
                    arrowPajak.classList.remove('-rotate-90');
                    sessionStorage.setItem('pajak_open', 'false');
                    sessionStorage.removeItem('pajak_reset_action');
                } else {
                    // Jaga agar menu pajak TETAP TERBUKA saat sub-menunya aktif!
                    dropdownPajak.classList.remove('hidden');
                    arrowPajak.classList.add('-rotate-90');
                    btnPajak.classList.add('bg-slate-800', 'text-white', 'font-semibold', 'border-l-2', 'border-blue-500');
                    sessionStorage.setItem('pajak_open', 'true');
                }
            } else if (sessionStorage.getItem('pajak_open') === 'true') {
                dropdownPajak.classList.remove('hidden');
                arrowPajak.classList.add('-rotate-90');
            }
            
            // Atur Kondisi Awal Dropdown Kendaraan
            if (['konvensional', 'hybrid', 'listrik', 'motor'].includes(currentCat)) {
                dropdownKendaraan.classList.remove('hidden');
                arrowKendaraan.classList.add('-rotate-90');
                btnKendaraan.classList.add('bg-slate-800', 'text-white', 'font-semibold', 'border-l-2', 'border-blue-500');
                sessionStorage.setItem('kendaraan_open', 'true');
            } else if (sessionStorage.getItem('kendaraan_open') === 'true') {
                dropdownKendaraan.classList.remove('hidden');
                arrowKendaraan.classList.add('-rotate-90');
            }

            // ==========================================
            // 2. DETEKSI KLIK SAMA UNTUK MANAGEMENT STORAGE
            // ==========================================
            linkPajakAktif.addEventListener('click', function() {
                if (currentMen === 'pajak' && currentTax === 'Aktif') {
                    sessionStorage.setItem('pajak_open', 'false');
                    sessionStorage.setItem('pajak_reset_action', 'true');
                } else {
                    sessionStorage.setItem('pajak_open', 'true');
                    sessionStorage.removeItem('pajak_reset_action');
                }
            });
            
            linkPajakTidak.addEventListener('click', function() {
                if (currentMen === 'pajak' && currentTax === 'Tidak Aktif') {
                    sessionStorage.setItem('pajak_open', 'false');
                    sessionStorage.setItem('pajak_reset_action', 'true');
                } else {
                    sessionStorage.setItem('pajak_open', 'true');
                    sessionStorage.removeItem('pajak_reset_action');
                }
            });
            
            linkKonvensional.addEventListener('click', function() {
                if (currentCat === 'konvensional') sessionStorage.setItem('kendaraan_open', 'false');
            });
            linkHybrid.addEventListener('click', function() {
                if (currentCat === 'hybrid') sessionStorage.setItem('kendaraan_open', 'false');
            });
            linkListrik.addEventListener('click', function() {
                if (currentCat === 'listrik') sessionStorage.setItem('kendaraan_open', 'false');
            });
            linkMotor.addEventListener('click', function() {
                if (currentCat === 'motor') sessionStorage.setItem('kendaraan_open', 'false');
            });

            // ==========================================
            // 3. LOGIKA TOGGLE MANUAL (SIDEBAR UTAMA)
            // ==========================================
            btnPajak.addEventListener('click', function(e) {
                e.preventDefault();
                const isHidden = dropdownPajak.classList.toggle('hidden');
                arrowPajak.classList.toggle('-rotate-90');
                btnPajak.classList.toggle('bg-slate-800');
                btnPajak.classList.toggle('text-white');
                sessionStorage.setItem('pajak_open', !isHidden);
            });

            btnKendaraan.addEventListener('click', function(e) {
                e.preventDefault();
                const isHidden = dropdownKendaraan.classList.toggle('hidden');
                arrowKendaraan.classList.toggle('-rotate-90');
                btnKendaraan.classList.toggle('bg-slate-800');
                btnKendaraan.classList.toggle('text-white');
                sessionStorage.setItem('kendaraan_open', !isHidden);
            });
        });
    </script>
</body>
</html>