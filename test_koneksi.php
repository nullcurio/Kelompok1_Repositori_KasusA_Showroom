<?php
require_once 'config/Database.php';

$db = new Database();
if ($db->conn) {
    echo "<div style='color: green; font-weight: bold; padding: 10px; background-color: #e6f4ea; border-radius: 5px; display: inline-block;'>
            ✔ Sukses Terhubung ke Database 'pbo_dbshowroom'!
          </div>";
} else {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background-color: #fce8e6; border-radius: 5px; display: inline-block;'>
            ❌ Gagal Terhubung ke Database.
          </div>";
}