<?php
include '../koneksi.php';

// Mengambil data mahasiswa dan dosen terkait
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Memeriksa apakah ada parameter selain nim yang diberikan
  $validParams = array('nim');
  $params = array_keys($_GET);
  $invalidParams = array_diff($params, $validParams);

  if (!empty($invalidParams)) {
    // Jika ada parameter selain nim, kembalikan pesan kesalahan
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
  } else {
    $nim = isset($_GET['nim']) ? $_GET['nim'] : null;

    if ($nim !== null) {
      // Hanya tampilkan detail jika parameter nim diberikan
      $stmt = $connect->prepare("SELECT m.nim, m.nama, d.nip, d.nama AS nama_dosen, k.jenis AS 'status'
                                  FROM MAHASISWA m
                                  JOIN DETAIL dt ON m.nim = dt.nim
                                  JOIN GRUP_DOSEN_DETAIL gdd ON dt.id_grup_dosen = gdd.id_grup_dosen
                                  JOIN DOSEN d ON gdd.nip = d.nip
                                  JOIN KETERANGAN k ON gdd.id_keterangan = k.id
                                  WHERE m.nim LIKE ?
                                  ORDER BY m.nim");
      $nimPattern = "%$nim%";
      $stmt->execute([$nimPattern]);
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      header('Content-Type: application/json');
      echo json_encode($data);
    } else {
      // Tampilkan semua data mahasiswa jika parameter nim tidak diberikan
      $stmt = $connect->query('SELECT nim, nama FROM MAHASISWA');
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      header('Content-Type: application/json');
      echo json_encode($data);
    }
  }
} else {
  // Jika request method bukan GET, kembalikan error
  header('Content-Type: application/json');
  echo json_encode(["error" => "Invalid request method"]);
}
