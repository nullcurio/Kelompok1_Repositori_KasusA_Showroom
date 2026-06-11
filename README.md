# Kelompok1_Repositori_KasusA_Showroom

# ShowroomPro: Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan

ShowroomPro adalah aplikasi manajemen inventaris dan kalkulasi fiskal showroom kendaraan terintegrasi. Aplikasi ini dibangun menggunakan arsitektur _Pure-OOP (Object-Oriented Programming)_ PHP dengan pola desain kontroler modern berbasis komponen template AdminLTE 4.

Aplikasi ini dirancang untuk mengelola tata kelola inventaris, klasifikasi aset, serta perhitungan beban fiskal (pajak tahunan) otomatis pada berbagai tipe kendaraan secara dinamis.

---

## Struktur Tim & Pembagian Tugas (Job Desk Kelompok)

Sistem dibangun secara kolaboratif dengan membagi arsitektur aplikasi menjadi 5 peran spesifik:

### Job 5: System Analyst & Project Manager (PM)

**Tanggung Jawab Utama:** Penanggung jawab perencanaan logika sistem secara makro, memantau ritme pengerjaan proyek, serta mentransformasikan arsitektur kode pemrograman ke dalam diagram visual standar industri.

- **Output Hasil Kerja:**
  - `UML_Class_Diagram.png` (Skema Hubungan Antar Kelas / Class Blueprint)
  - `UML_UseCase_Diagram.png` (Skema Interaksi Aktor Terhadap Sistem)
- **Penjelasan Penting:**
  - **Use Case Diagram**: Menggambarkan batasan sistem (_system boundary_) dan interaksi aktor (seperti Admin Showroom atau Pemilik) dalam melakukan operasi pada aplikasi, seperti melihat dasbor aset, memfilter kategori kendaraan, serta memantau akumulasi total beban fiskal (pajak).
  - **Class Diagram**: Bagian paling krusial yang memetakan seluruh kelas di dalam proyek secara visual. Diagram ini menunjukkan hubungan pewarisan (_Inheritance_) dengan tanda panah dari sub-class ke `Kendaraan` (Abstract Class), memperlihatkan visibilitas properti (`-` untuk private, `#` untuk protected, `+` untuk public), serta menggambarkan hubungan asosiasi antara kelas `Database`, `ManajemenShowroom`, dan komponen kelas model.

### Job 1: Database Engineer & Data Access Layer (DAL)

**Tanggung Jawab Utama:** Penanggung jawab penuh atas ketersediaan data, perancangan skema relasional, serta penyediaan gerbang koneksi basis data yang aman ke aplikasi.

- **File yang Dikerjakan:** \* `pbo_dbshowroom.sql` (Skema Basis Data MySQL)
  - `config/Database.php` (Kelas Koneksi PDO)
  - `test_koneksi.php` (Skrip Validasi Koneksi)
- **Penjelasan Penting & Logika Kode:**
  - **`pbo_dbshowroom.sql`**: Menggunakan teknik _Class Table Inheritance_ pada MySQL. Tabel utama `tb_kendaraan` menyimpan atribut umum, sedangkan tabel anak (`tb_mobil_konvensional`, `tb_mobil_hybrid`, `tb_mobil_listrik`, `tb_motor_besar`) terikat melalui hubungan relasional `FOREIGN KEY ... ON DELETE CASCADE`. Hal ini menjamin jika data kendaraan dihapus di tabel induk, detail spesifik di tabel anak otomatis ikut terhapus secara bersih.
  - **`config/Database.php`**: Mengimplementasikan koneksi menggunakan driver **PDO (PHP Data Objects)**. Koneksi diletakkan di dalam fungsi magis `__construct()` sehingga setiap kali objek `Database` diinstansiasi, jabat tangan (_handshake_) dengan server MySQL langsung berjalan secara otomatis dengan atribut `PDO::ERRMODE_EXCEPTION` untuk penanganan error (_error handling_) yang aman.
  - **`test_koneksi.php`**: Berfungsi sebagai modul uji cepat (_sanity check_) mandiri. Jika koneksi berhasil, akan merender komponen visual hijau tanda sistem siap digunakan.

### Job 2: Software Architect & Core Abstraction

**Tanggung Jawab Utama:** Perancang arsitektur awal sistem, serta cetak biru (_blueprint_) utama dari pilar enkapsulasi dan abstraksi objek.

- **File yang Dikerjakan:**
  - `models/Kendaraan.php` (Master Abstract Class Induk)
- **Penjelasan Penting & Logika Kode:**
  - **Struktur Folder**: Memisahkan komponen data (Models), pemrosesan logika data (Controllers), dan konfigurasi infrastruktur (Config) agar memenuhi prinsip kebersihan kode (_clean code architecture_).
  - **`models/Kendaraan.php`**: Kelas ini dideklarasikan sebagai `abstract class`, yang berarti kelas induk ini bersifat konseptual dan tidak dapat diinstansiasi langsung menggunakan kata kunci `new`. Properti di dalamnya (seperti `brand`, `model`, `hargaDasar`, dll) dibungkus menggunakan _Access Modifier_ `protected` agar aman dari akses luar langsung (**Encapsulation**), namun tetap bisa diwariskan ke kelas anak. Kelas ini menetapkan dua standar metode abstrak wajib (_Contractual Methods_):
    - `abstract public function hitungPajakTahunan();`
    - `abstract public function tampilkanSpesifikasi();`

### Job 3: Subclass Developer & Business Logic Specialist

**Tanggung Jawab Utama:** Pengenang logika bisnis konkret pada tingkat operasional unit kendaraan yang bertugas mengimplementasikan pewarisan sifat (_Inheritance_) dan rumus perhitungan spesifik (_Method Overriding_).

- **File yang Dikerjakan:**
  - `models/MobilKonvensional.php` (Unit Internal Combustion Engine / ICE)
  - `models/MobilHybrid.php` (Unit Kombinasi BBM-Baterai)
  - `models/MobilListrik.php` (Unit Battery Electric Vehicle / BEV)
  - `models/MotorBesar.php` (Unit Roda Dua Kapasitas Besar)
- **Penjelasan Penting & Logika Kode:**
  - Seluruh sub-class di atas wajib mengimplementasikan keyword `extends Kendaraan` untuk mewarisi sifat induknya. Konstruktor anak memanfaatkan `parent::__construct()` untuk mengirim data dasar ke kelas induk.
  - Menerapkan **Polymorphism** dengan merombak isi metode abstrak sesuai regulasi fiskal kendaraan:
    - **`MobilKonvensional.php`**: Memiliki atribut unik kapasitas mesin (cc) dan jenis bensin. Rumus pajak tahunan: `(2% * Harga Dasar) + (Kapasitas Mesin * 500)`.
    - **`MobilHybrid.php`**: Memiliki atribut campuran (cc mesin dan kWh baterai). Rumus pajak tahunan: `(1% * Harga Dasar) + (Kapasitas Mesin * 250)`.
    - **`MobilListrik.php`**: Memiliki atribut unik kWh baterai. Berkat insentif emisi nol (zero emission), rumus pajaknya sangat murah, yaitu hanya `0.5% * Harga Dasar`.
    - **`MotorBesar.php`**: Memiliki atribut tipe rantai dan mode berkendara. Rumus pajak tahunan: `1.5% * Harga Dasar`.

### Job 4: Controller & Polymorphic Driver Specialist

**Tanggung Jawab Utama:** Mengendalikan alur data (_data flow_) sistem yang mengeksekusi kumpulan objek, memetakan data relasional database ke bentuk objek, serta menyajikannya ke halaman antarmuka pengguna.

- **File yang Dikerjakan:**
  - `controllers/ManajemenShowroom.php` (Central Control Engine)
  - `index.php` (Main Dashboard Gateway UI)
- **Penjelasan Penting & Logika Kode:**
  - **`controllers/ManajemenShowroom.php`**: Di dalam file ini terdapat properti `$listKendaraan` berupa array kosong. Fungsi `loadDataFromDatabase()` mengeksekusi query SQL `LEFT JOIN` dari tabel `tb_kendaraan` ke empat tabel anak sekaligus. Melalui pengecekan kolom unik (misalnya: jika kolom `bat_list` tidak kosong maka buat `new MobilListrik`), kontroler melakukan **Dynamic Object Casting**. Semua tipe objek yang berbeda ini dimasukkan ke dalam satu wadah array tunggal (_Polymorphic Collection_).
  - **`index.php`**: Bertindak sebagai antarmuka utama pengguna (menggunakan Tailwind CSS). File ini menangkap parameter URL untuk melakukan filtering data (seperti kategori atau pencarian nama kendaraan), lalu melakukan perulangan (_looping_) data untuk disajikan ke dalam tabel. Berkat mekanisme **Dynamic Binding**, file ini cukup memanggil `$item->hitungPajakTahunan()` dan `$item->tampilkanSpesifikasi()`, dan PHP secara otomatis akan mengeksekusi rumus yang tepat sesuai blueprint objek aslinya.

## Implementasi Pilar Utama OOP (Object-Oriented Programming)

Aplikasi ini mengimplementasikan 4 pilar utama OOP secara ketat sesuai instruksi penugasan:
1.  **Abstraction**: Penerapan `abstract class Kendaraan` beserta fungsi abstraknya sebagai cetak biru wajib tanpa bisa diinstansiasi langsung.
2.  **Inheritance**: Pewarisan atribut dasar kendaraan dari kelas induk ke kelas `MobilKonvensional`, `MobilListrik`, `MobilHybrid`, dan `MotorBesar`.
3.  **Encapsulation**: Penggunaan akses kontrol `private` dan `protected` serta pemanfaatan metode **Getter** (seperti `getBrand()`, `getModel()`, `getHargaDasar()`) untuk menjaga keamanan integritas data dari manipulasi luar.
4.  **Polymorphism**: Teknik *Method Overriding* pada fungsi `hitungPajakTahunan()` dan `tampilkanSpesifikasi()` yang menghasilkan output berbeda secara cerdas tergantung pada tipe objek yang sedang diproses.

---

## Panduan Menjalankan Aplikasi

1.  **Persiapan Database:**
    * Buka phpMyAdmin atau MySQL Client Anda.
    * Buat database baru bernama `pbo_dbshowroom`.
    * Impor file `sql/pbo_dbshowroom.sql` ke dalam database tersebut.
2.  **Konfigurasi Koneksi:**
    * Buka file `config/Database.php`, sesuaikan isi `$username` dan `$password` dengan konfigurasi server MySQL lokal Anda (default XAMPP: username `root` dan password dikosongkan `""`).
3.  **Pengujian Jalur Data:**
    * Buka browser dan akses `http://localhost/nama_folder_proyek/test_koneksi.php` untuk memastikan status koneksi sukses.
4.  **Akses Aplikasi:**
    * Buka dashboard utama di alamat: `http://localhost/nama_folder_proyek/index.php`.

![alt text](<UML/Class diagram.drawio.png>)

![alt text](<UML/use case.drawio.png>)