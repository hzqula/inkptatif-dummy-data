<?php
include '../koneksi.php';

// Mengambil data seminar dan detail terkait berdasarkan parameter yang diberikan
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $nama = isset($_GET['nama']) ? $_GET['nama'] : null;
  $id = isset($_GET['id']) ? $_GET['id'] : null;

  // Memeriksa apakah ada parameter selain nama atau id yang diberikan
  $validParams = array('nama', 'id');
  $params = array_keys($_GET);
  $invalidParams = array_diff($params, $validParams);

  if (!empty($invalidParams)) {
    // Jika ada parameter selain nama atau id, kembalikan pesan kesalahan
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
    exit;
  }

  // Tentukan query SQL berdasarkan parameter yang ada
  if ($nama !== null || $id !== null) {
    $stmt = $connect->prepare("
      SELECT 
        s.id AS id,
        s.judul AS judul,
        k.jenis AS seminar,
        s.tempat AS tempat,
        s.tanggal AS pada,
        m.nim AS nim,
        m.nama AS mahasiswa,
        (
          SELECT JSON_ARRAYAGG(JSON_OBJECT('nip', d.nip, 'nama', d.nama))
          FROM DOSEN d
          JOIN GRUP_DOSEN_DETAIL gdd ON d.nip = gdd.nip
          WHERE gdd.id_grup_dosen = g.id AND gdd.id_keterangan = 1
        ) AS dosen_pembimbing,
        (
          SELECT JSON_ARRAYAGG(JSON_OBJECT('nip', d.nip, 'nama', d.nama))
          FROM DOSEN d
          JOIN GRUP_DOSEN_DETAIL gdd ON d.nip = gdd.nip
          WHERE gdd.id_grup_dosen = g.id AND gdd.id_keterangan = 2
        ) AS dosen_penguji
      FROM SEMINAR s
      JOIN DETAIL dt ON s.id = dt.id_seminar
      JOIN MAHASISWA m ON dt.nim = m.nim
      JOIN GRUP_DOSEN g ON dt.id_grup_dosen = g.id
      JOIN KATEGORI k ON dt.id_kategori = k.id
      WHERE m.nama LIKE ? OR s.id = ?
      ORDER BY s.id
    ");
    $namaPattern = "%$nama%";
    $stmt->execute([$namaPattern, $id]);
  } else {
    $stmt = $connect->query("
      SELECT 
        s.id AS id,
        s.judul AS judul,
        k.jenis AS seminar,
        s.tempat AS tempat,
        s.tanggal AS pada,
        m.nim AS nim,
        m.nama AS mahasiswa,
        (
          SELECT JSON_ARRAYAGG(JSON_OBJECT('nip', d.nip, 'nama', d.nama))
          FROM DOSEN d
          JOIN GRUP_DOSEN_DETAIL gdd ON d.nip = gdd.nip
          WHERE gdd.id_grup_dosen = g.id AND gdd.id_keterangan = 1
        ) AS dosen_pembimbing,
        (
          SELECT JSON_ARRAYAGG(JSON_OBJECT('nip', d.nip, 'nama', d.nama))
          FROM DOSEN d
          JOIN GRUP_DOSEN_DETAIL gdd ON d.nip = gdd.nip
          WHERE gdd.id_grup_dosen = g.id AND gdd.id_keterangan = 2
        ) AS dosen_penguji
      FROM SEMINAR s
      JOIN DETAIL dt ON s.id = dt.id_seminar
      JOIN MAHASISWA m ON dt.nim = m.nim
      JOIN GRUP_DOSEN g ON dt.id_grup_dosen = g.id
      JOIN KATEGORI k ON dt.id_kategori = k.id
      ORDER BY s.id
    ");
  }

  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Parse JSON strings to arrays
  if ($data) {
    foreach ($data as &$seminar) {
      $seminar['dosen_pembimbing'] = json_decode($seminar['dosen_pembimbing'], true);
      $seminar['dosen_penguji'] = json_decode($seminar['dosen_penguji'], true);
    }
  }

  header('Content-Type: application/json');
  echo json_encode($data);
} else {
  // Jika request method bukan GET, kembalikan error
  header('Content-Type: application/json');
  echo json_encode(["error" => "Invalid request method"]);
}
