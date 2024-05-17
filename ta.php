<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InKPTATIF</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="navbar">
    <div class="judul">
      <h1>InKPTATIF</h1>
    </div>
    <nav>
      <div class="nav-items">
        <a href="/dashboard" class="nav-item">Dashboard</a>
        <a href="/input-nilai-kp" class="nav-item">Input Nilai KP</a>
        <a href="/input-nilai-ta" class="nav-item">Input Nilai TA</a>
      </div>
    </nav>
    <div class="btn-logout">
      <button class="font">Keluar</button>
    </div>
  </header> 
  <main>
    <section class="titles">
      <h2 class="black-title">Daftar Mahasiswa</h2>
      <h2 class="blue-title">Mahasiswa Praktek</h2>
    </section>
  </main>   
  <section class="j1">
    <div class="title-container">
      <h3 class="j3">Yang Dibimbing</h3>
      <h3 class="j4">Yang Diuji</h3>
    </div>
  </section>
<div class="search-container">
  <input type="text" placeholder="Cari Mahasiswa..." class="search-bar">
</div>

<section class="cards-container">
<?php
$servername = "localhost";
$username = "root";
$password = "@IlooqstrasiHZ0113";
$dbname = "inkptatif";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Tugas Akhir<br>";

// Query untuk mendapatkan semua nama mahasiswa, dosen pembimbing, dan dosen penguji
$query = "
    SELECT 
        mhs.nama AS mhs_nama, 
        mhs.nim AS mhs_nim, 
        dsn_pembimbing.nama AS dosen_pembimbing_nama, 
        dsn_penguji.nama AS dosen_penguji_nama
    FROM 
        MAHASISWA mhs
    JOIN 
        DETAIL dtl_pembimbing ON mhs.nim = dtl_pembimbing.nim AND dtl_pembimbing.status = 'pembimbing'
    JOIN 
        DOSEN dsn_pembimbing ON dtl_pembimbing.nip = dsn_pembimbing.nip
    JOIN 
        DETAIL dtl_penguji ON mhs.nim = dtl_penguji.nim AND dtl_penguji.status = 'penguji'
    JOIN 
        DOSEN dsn_penguji ON dtl_penguji.nip = dsn_penguji.nip
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<div class='card-content'>";
        echo "<img src='path/to/image.jpg' alt='Foto Mahasiswa' class='student-photo'>";
        echo "<div>";
        echo "<p class='student-name'>Mahasiswa: " . $row['mhs_nama'] . "</p>";
        echo "<p class='student-nim'>" . $row['mhs_nim'] . "</p>";
        echo "<p class='student-name'>Dosen Pembimbing: " . $row['dosen_pembimbing_nama'] . "</p>";
        echo "<p class='student-name'>Dosen Penguji: " . $row['dosen_penguji_nama'] . "</p>";
        echo "</div>";
        echo "<div class='card-buttons'>";
        echo "<button class='btn-sudah'>Sudah</button>";
        echo "<button class='btn-detail'>Detail</button>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No data found.</p>";
}

$conn->close();
?>
</section>

</body>
</html>
