<?php
// Di setiap halaman sistem
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}
include 'koneksi.php';

// Ambil data alternatif dan kriteria
$alternatif = [];
$res = $conn->query("SELECT * FROM alternatif ORDER BY id");
while ($row = $res->fetch_assoc()) {
    $alternatif[$row['id']] = $row['nama'];
}

$kriteria = [];
$res = $conn->query("SELECT * FROM kriteria ORDER BY id");
while ($row = $res->fetch_assoc()) {
    $kriteria[$row['id']] = $row['nama'];
}

// Ambil nilai sebelumnya jika ada
$nilai = [];
$res = $conn->query("SELECT * FROM nilai");
while ($row = $res->fetch_assoc()) {
    $nilai[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

// Simpan data nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hapus data lama
    $conn->query("DELETE FROM nilai");

    foreach ($_POST['nilai'] as $id_alt => $nilai_kriteria) {
        foreach ($nilai_kriteria as $id_krit => $n) {
            $n = floatval($n);
            if ($n < 0 || $n > 10) {
                echo "<script>alert('Nilai harus antara 0 dan 10');window.location='nilai.php';</script>";
                exit;
            }
            $conn->query("INSERT INTO nilai (id_alternatif, id_kriteria, nilai) VALUES ($id_alt, $id_krit, $n)");
        }
    }

    header("Location: nilai.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Input Nilai Alternatif - Sistem SPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <style>
    body {
        background: #121212;
        color: #f0e9db;
        font-family: 'Montserrat', sans-serif;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 2.8rem;
        color: #c9b037;
        letter-spacing: 3px;
        text-align: center;
        margin-bottom: 2.5rem;
        text-shadow: 0 0 8px rgba(201, 176, 55, 0.75);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: transparent;
        border: 2px solid #c9b037;
        color: #c9b037;
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 3rem;
        user-select: none;
    }

    .btn-back:hover,
    .btn-back:focus {
        background-color: #c9b037;
        color: #121212;
        text-decoration: none;
        outline: none;
        box-shadow: 0 0 8px #c9b037;
    }

    form {
        max-width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        max-width: 900px;
        margin: 0 auto 2rem auto;
        border-collapse: separate;
        border-spacing: 0 14px;
        font-size: 1rem;
        color: #f0e9db;
        box-shadow: 0 6px 18px rgba(201, 176, 55, 0.2);
        border-radius: 14px;
        overflow: hidden;
        background-color: #1f1f1f;
    }

    thead tr {
        background-color: #2c2c2c;
        color: #c9b037;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1.2px;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.7);
    }

    thead th {
        padding: 1rem 1.5rem;
        white-space: nowrap;
    }

    tbody tr {
        background-color: #2a2a2a;
        transition: background-color 0.25s ease;
        border-radius: 14px;
    }

    tbody tr:hover {
        background-color: #3a3a3a;
    }

    tbody td {
        padding: 1rem 1.2rem;
        vertical-align: middle;
        font-weight: 600;
        white-space: nowrap;
    }

    tbody td:first-child {
        width: 15%;
        text-align: left;
    }

    /* Input number styling */
    input[type="number"].form-control {
        background-color: #121212;
        border: 2px solid #c9b037;
        color: #f0e9db;
        font-weight: 600;
        border-radius: 12px;
        padding: 0.4rem 0.9rem;
        font-size: 1rem;
        width: 80px;
        margin: auto;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    input[type="number"].form-control:focus {
        outline: none;
        border-color: #fff;
        background-color: #2c2c2c;
        color: #fff;
        box-shadow: 0 0 10px #c9b037;
    }

    /* Submit button */
    button[type="submit"] {
        display: block;
        margin: 0 auto;
        background-color: #c9b037;
        border: none;
        color: #121212;
        font-weight: 700;
        font-size: 1.2rem;
        padding: 0.65rem 2rem;
        border-radius: 14px;
        cursor: pointer;
        box-shadow: 0 0 15px rgba(201, 176, 55, 0.7);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    button[type="submit"]:hover,
    button[type="submit"]:focus {
        background-color: #a89529;
        box-shadow: 0 0 20px #a89529;
        outline: none;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {

        thead th,
        tbody td {
            padding: 0.6rem 0.5rem;
            font-size: 0.85rem;
        }

        input[type="number"].form-control {
            width: 60px;
            font-size: 0.9rem;
        }
    }
    </style>
</head>

<body>
    <h2>Input Nilai Alternatif</h2>
    <a href="index.php" class="btn-back" aria-label="Kembali ke halaman utama">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <form method="post" novalidate>
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($kriteria as $k): ?>
                    <th><?= htmlspecialchars($k) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alternatif as $id_alt => $nama_alt): ?>
                <tr>
                    <td><?= htmlspecialchars($nama_alt) ?></td>
                    <?php foreach ($kriteria as $id_krit => $nama_krit):
                        $val = isset($nilai[$id_alt][$id_krit]) ? $nilai[$id_alt][$id_krit] : '';
                    ?>
                    <td>
                        <input type="number" class="form-control" name="nilai[<?= $id_alt ?>][<?= $id_krit ?>]"
                            value="<?= htmlspecialchars($val) ?>" min="0" max="10" step="0.1" required
                            aria-label="Nilai <?= htmlspecialchars($nama_alt) ?> untuk <?= htmlspecialchars($nama_krit) ?>" />
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" aria-label="Simpan nilai alternatif">
            <i class="bi bi-save2"></i> Simpan Nilai
        </button>
    </form>
</body>

</html>