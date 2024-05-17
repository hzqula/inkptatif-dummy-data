<?php

$servername = "localhost";
$username = "root";
$password = "@IlooqstrasiHZ0113";
$dbname = "inkptatif_v4";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br>";

// Menampilkan data penguji
$showPenguji = $conn->query("SELECT dsn.nama AS nama_dosen, mhs.nama AS nama_mahasiswa, kti.jenis AS jenis_kategori, ktn.jenis AS status_dosen FROM DOSEN dsn JOIN DETAIL dtl ON dsn.nip = dtl.nip JOIN MAHASISWA mhs ON mhs.nim = dtl.nim JOIN KATEGORI kti ON kti.id = dtl.id_kategori JOIN KETERANGAN ktn on ktn.id = kti.id_status WHERE kti.id = 123 AND mhs.nim = '223848' AND kti.id_status = dtl.id_status");

if ($showPenguji->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Nama Dosen</th>
                <th>Nama Mahasiswa</th>
                <th>Seminar</th>
                <th>Status Dosen</th>
            </tr>";
    while ($row = $showPenguji->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["nama_dosen"] . "</td>
                <td>" . $row["nama_mahasiswa"] . "</td>
                <td>" . $row["jenis_kategori"] . "</td>
                <td>" . $row["status_dosen"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Tidak ada data yang ditemukan.";
}

// Menutup koneksi
$conn->close();
?>
