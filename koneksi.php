<?php

$servername = "localhost";
$username = "root";
$password = "@IlooqstrasiHZ0113";
$dbname = "inkptatif_v8";

try {
    // Membuat koneksi ke database menggunakan PDO
    $connect = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Mengatur PDO error mode ke exception
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
