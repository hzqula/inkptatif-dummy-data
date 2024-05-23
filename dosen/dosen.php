<?php
include '../koneksi.php';

// Mengambil data dosen dan mahasiswa terkait
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Memeriksa apakah ada parameter selain nip yang diberikan
  $validParams = array('nip');
  $params = array_keys($_GET);
  $invalidParams = array_diff($params, $validParams);

  if (!empty($invalidParams)) {
    // Jika ada parameter selain nip, kembalikan pesan kesalahan
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
  } else {
    $nip = isset($_GET['nip']) ? $_GET['nip'] : null;

    if ($nip !== null) {
      // Hanya tampilkan detail jika parameter nip diberikan
      $stmt = $connect->prepare("SELECT d.nip, d.nama AS nama_dosen,
                                      (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nim', m.nim, 'nama', m.nama))
                                          FROM MAHASISWA m
                                          JOIN DETAIL dt ON m.nim = dt.nim
                                          JOIN GRUP_DOSEN_DETAIL gdd ON dt.id_grup_dosen = gdd.id_grup_dosen
                                          WHERE gdd.nip = d.nip AND gdd.id_keterangan = 1
                                      ) AS dibimbing,
                                      (
                                          SELECT JSON_ARRAYAGG(JSON_OBJECT('nim', m.nim, 'nama', m.nama))
                                          FROM MAHASISWA m
                                          JOIN DETAIL dt ON m.nim = dt.nim
                                          JOIN GRUP_DOSEN_DETAIL gdd ON dt.id_grup_dosen = gdd.id_grup_dosen
                                          WHERE gdd.nip = d.nip AND gdd.id_keterangan = 2
                                      ) AS diuji
                                  FROM DOSEN d
                                  WHERE d.nip LIKE ?");
      $nipPattern = "%$nip%";
      $stmt->execute([$nipPattern]);
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Parse JSON strings to arrays
      if ($data) {
        foreach ($data as &$dosen) {
          $dosen['dibimbing'] = json_decode($dosen['dibimbing'], true);
          $dosen['diuji'] = json_decode($dosen['diuji'], true);
        }
      }

      header('Content-Type: application/json');
      echo json_encode($data);
    } else {
      // Tampilkan semua data dosen jika parameter nip tidak diberikan
      $stmt = $connect->query('SELECT nip, nama FROM DOSEN');
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
