# Kelompok1_Repositori_KasusA_Showroom

# ShowroomPro: Sistem Manajemen Inventaris & Fiskal Showroom Kendaraan

ShowroomPro adalah aplikasi manajemen inventaris dan kalkulasi fiskal showroom kendaraan terintegrasi. Aplikasi ini dibangun menggunakan arsitektur *Pure-OOP (Object-Oriented Programming)* PHP dengan pola desain kontroler modern berbasis komponen template AdminLTE 4.

Aplikasi ini dirancang untuk mengelola tata kelola inventaris, klasifikasi aset, serta perhitungan beban fiskal (pajak tahunan) otomatis pada berbagai tipe kendaraan secara dinamis.

---

## Struktur Tim & Pembagian Tugas (Job Desk Kelompok)

Sistem dibangun secara kolaboratif dengan membagi arsitektur aplikasi menjadi 5 peran spesifik:


### Job 5: System Analyst & Project Manager (PM)
**Tanggung Jawab Utama:** Penanggung jawab perencanaan logika sistem secara makro, memantau ritme pengerjaan proyek, serta mentransformasikan arsitektur kode pemrograman ke dalam diagram visual standar industri.
* **Output Hasil Kerja:**
    * `UML_Class_Diagram.png` (Skema Hubungan Antar Kelas / Class Blueprint)
    * `UML_UseCase_Diagram.png` (Skema Interaksi Aktor Terhadap Sistem)
* **Penjelasan Penting:**
    * **Use Case Diagram**: Menggambarkan batasan sistem (*system boundary*) dan interaksi aktor (seperti Admin Showroom atau Pemilik) dalam melakukan operasi pada aplikasi, seperti melihat dasbor aset, memfilter kategori kendaraan, serta memantau akumulasi total beban fiskal (pajak).
    * **Class Diagram**: Bagian paling krusial yang memetakan seluruh kelas di dalam proyek secara visual. Diagram ini menunjukkan hubungan pewarisan (*Inheritance*) dengan tanda panah dari sub-class ke `Kendaraan` (Abstract Class), memperlihatkan visibilitas properti (`-` untuk private, `#` untuk protected, `+` untuk public), serta menggambarkan hubungan asosiasi antara kelas `Database`, `ManajemenShowroom`, dan komponen kelas model.
