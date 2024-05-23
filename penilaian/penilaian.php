<?php
include '../koneksi.php';

// Mengambil data penilaian berdasarkan parameter yang diberikan
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $nim = isset($_GET['nim']) ? $_GET['nim'] : null;

  // Memeriksa apakah ada parameter selain nim yang diberikan
  $validParams = array('nim');
  $params = array_keys($_GET);
  $invalidParams = array_diff($params, $validParams);

  if (!empty($invalidParams)) {
    // Jika ada parameter selain nim, kembalikan pesan kesalahan
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid parameter(s): " . implode(', ', $invalidParams)]);
    exit;
  }

  // Tentukan query SQL berdasarkan parameter yang ada
  if ($nim !== null) {
    $stmt = $connect->prepare("
      SELECT 
        m.nama AS nama_mahasiswa,
        m.nim AS nim,
        k.jenis AS jenis_seminar,
        d.nama AS nama_dosen,
        ktr.jenis AS status,
        kri.kriteria AS kriteria,
        p.nilai AS nilai
      FROM PENILAIAN p
      JOIN MAHASISWA m ON p.nim = m.nim
      JOIN DOSEN d ON p.nip = d.nip
      JOIN KETERANGAN ktr ON p.id_keterangan = ktr.id
      JOIN KRITERIA kri ON p.id_kriteria = kri.id
      JOIN KATEGORI k ON p.id_kategori = k.id
      WHERE m.nim LIKE ?
      ORDER BY m.nim, d.nip, kri.id
    ");
    $nimPattern = "%$nim%";
    $stmt->execute([$nimPattern]);
  } else {
    $stmt = $connect->query("
      SELECT 
        m.nama AS nama_mahasiswa,
        m.nim AS nim,
        k.jenis AS jenis_seminar,
        d.nama AS nama_dosen,
        ktr.jenis AS status,
        kri.kriteria AS kriteria,
        p.nilai AS nilai
      FROM PENILAIAN p
      JOIN MAHASISWA m ON p.nim = m.nim
      JOIN DOSEN d ON p.nip = d.nip
      JOIN KETERANGAN ktr ON p.id_keterangan = ktr.id
      JOIN KRITERIA kri ON p.id_kriteria = kri.id
      JOIN KATEGORI k ON p.id_kategori = k.id
      ORDER BY m.nim, d.nip, kri.id
    ");
  }

  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Organize data into the desired structure
  $result = [];
  foreach ($data as $row) {
    $nim = $row['nim'];
    $namaMahasiswa = $row['nama_mahasiswa'];
    $jenisSeminar = $row['jenis_seminar'];
    $namaDosen = $row['nama_dosen'];
    $status = $row['status'];
    $kriteria = $row['kriteria'];
    $nilai = $row['nilai'];

    if (!isset($result[$nim])) {
      $result[$nim] = [
        'nama_mahasiswa' => $namaMahasiswa,
        'nim' => $nim,
        'jenis_seminar' => $jenisSeminar,
        'dosen' => []
      ];
    }

    $found = false;
    foreach ($result[$nim]['dosen'] as &$dosen) {
      if ($dosen['nama_dosen'] === $namaDosen && $dosen['status'] === $status) {
        $dosen['penilaian'][] = ['kriteria' => $kriteria, 'nilai' => $nilai];
        $found = true;
        break;
      }
    }

    if (!$found) {
      $result[$nim]['dosen'][] = [
        'nama_dosen' => $namaDosen,
        'status' => $status,
        'penilaian' => [
          ['kriteria' => $kriteria, 'nilai' => $nilai]
        ]
      ];
    }
  }

  // Reset keys to make it a valid JSON array
  $result = array_values($result);

  header('Content-Type: application/json');
  echo json_encode($result);
}
