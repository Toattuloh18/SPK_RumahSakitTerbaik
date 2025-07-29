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
    $alternatif[$row['id']] = ['nama' => $row['nama']];
}

$kriteria = [];
$res = $conn->query("SELECT * FROM kriteria ORDER BY id");
while ($row = $res->fetch_assoc()) {
    $kriteria[$row['id']] = ['nama' => $row['nama'], 'bobot' => $row['bobot'], 'tipe' => $row['tipe']];
}

// Ambil nilai alternatif per kriteria
$nilai = [];
foreach ($alternatif as $id_alt => $a) {
    foreach ($kriteria as $id_krit => $k) {
        $res2 = $conn->query("SELECT nilai FROM nilai WHERE id_alternatif=$id_alt AND id_kriteria=$id_krit");
        $n = $res2->fetch_assoc();
        $nilai[$id_alt][$id_krit] = $n ? floatval($n['nilai']) : 0;
    }
}

// Fungsi preferensi linear: P(d) = max(0, d)
function preferensi($d) {
    return max(0, $d);
}

// Hitung matriks preferensi antara alternatif i dan j per kriteria
$prefMatrix = [];
$alt_ids = array_keys($alternatif);
foreach ($alt_ids as $i) {
    foreach ($alt_ids as $j) {
        if ($i == $j) {
            $prefMatrix[$i][$j] = 0;
            continue;
        }
        $p_total = 0;
        foreach ($kriteria as $id_krit => $k) {
            if ($k['tipe'] == 'minimize') {
                $d = $nilai[$j][$id_krit] - $nilai[$i][$id_krit];  // d = x_j - x_i
            } else { // maximize
                $d = $nilai[$i][$id_krit] - $nilai[$j][$id_krit];  // d = x_i - x_j
            }
            $p = preferensi($d);
            $p_total += $k['bobot'] * $p;
        }
        $prefMatrix[$i][$j] = $p_total;
    }
}

// Hitung Leaving Flow (phi+)
$leaving = [];
foreach ($alt_ids as $i) {
    $sum = 0;
    foreach ($alt_ids as $j) {
        if ($i != $j) {
            $sum += $prefMatrix[$i][$j];
        }
    }
    $leaving[$i] = $sum / (count($alt_ids) - 1);
}

// Hitung Entering Flow (phi-)
$entering = [];
foreach ($alt_ids as $i) {
    $sum = 0;
    foreach ($alt_ids as $j) {
        if ($i != $j) {
            $sum += $prefMatrix[$j][$i];
        }
    }
    $entering[$i] = $sum / (count($alt_ids) - 1);
}

// Hitung Net Flow (phi)
$netflow = [];
foreach ($alt_ids as $i) {
    $netflow[$i] = $leaving[$i] - $entering[$i];
}

// Urutkan berdasarkan net flow descending
arsort($netflow);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Hasil Ranking PROMETHEE II</title>
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

    table {
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 14px;
        font-size: 1rem;
        color: #f0e9db;
        box-shadow: 0 6px 18px rgba(201, 176, 55, 0.2);
        border-radius: 14px;
        overflow: hidden;
    }

    thead tr {
        background-color: #1f1f1f;
        color: #c9b037;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1.2px;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.7);
    }

    thead th {
        padding: 1rem 1.5rem;
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
        padding: 1rem 1.4rem;
        vertical-align: middle;
        font-weight: 600;
    }

    tbody td:nth-child(1) {
        text-align: center;
        width: 8%;
    }

    tbody td:nth-child(3) {
        width: 15%;
    }

    /* Rank colors */
    .rank-1 {
        background-color: #ffd700;
        color: #121212;
        font-weight: 700;
    }

    .rank-2 {
        background-color: #c0c0c0;
        color: #121212;
        font-weight: 700;
    }

    .rank-3 {
        background-color: #cd7f32;
        color: #121212;
        font-weight: 700;
    }
    </style>
</head>

<body>
    <h2>Hasil Ranking Rumah Sakit Terbaik (PROMETHEE II)</h2>
    <a href="index.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Alternatif</th>
                <th>Net Flow</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rank = 1;
            foreach ($netflow as $id => $val) {
                $class = '';
                if ($rank == 1) $class = 'rank-1';
                else if ($rank == 2) $class = 'rank-2';
                else if ($rank == 3) $class = 'rank-3';

                echo "<tr class='$class'>
                    <td>$rank</td>
                    <td>" . htmlspecialchars($alternatif[$id]['nama']) . "</td>
                    <td>" . number_format($val, 4) . "</td>
                </tr>";
                $rank++;
            }
            ?>
        </tbody>
    </table>
</body>

</html>