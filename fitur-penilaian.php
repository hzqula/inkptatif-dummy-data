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

// Menampilkan data penilaian
$showPenilaian = $conn->query(
    "SELECT dsn.nama AS nama_dosen, 
    mhs.nama AS nama_mahasiswa, 
    kti.jenis AS jenis_kategori, 
    ktn.jenis AS status_dosen ,nilai,keterangan 
    FROM DOSEN dsn JOIN DETAIL dtl ON dsn.nip = dtl.nip 
    JOIN MAHASISWA mhs ON mhs.nim = dtl.nim 
    JOIN KATEGORI kti ON kti.id = dtl.id_kategori 
    JOIN KETERANGAN ktn on ktn.id = kti.id_status 
    JOIN PENILAIAN pln on pln.nim = mhs.nim 
    JOIN KRITERIA kia on kia.id_kategori = kti.id
    WHERE kti.id = 123 AND mhs.nim = '223848' 
    AND kti.id_status = dtl.id_status 
    AND pln.nip = dsn.nip
    AND kia.id = pln.id_kriteria
    AND kti.id = pln.id_kategori
    AND dsn.nip = pln.nip
    AND kti.id_status = ktn.id");

if ($showPenilaian->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Nama Dosen</th>
                <th>Nama Mahasiswa</th>
                <th>Seminar</th>
                <th>Status Dosen</th>
                <th>Nilai</th>
                <th>Penilaian</th>
            </tr>";
    while ($row = $showPenilaian->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["nama_dosen"] . "</td>
                <td>" . $row["nama_mahasiswa"] . "</td>
                <td>" . $row["jenis_kategori"] . "</td>
                <td>" . $row["status_dosen"] . "</td>
                <td>" . $row["nilai"] . "</td>
                <td>" . $row["keterangan"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Tidak ada data yang ditemukan.";
}

// Menutup koneksi
$conn->close();
?>
