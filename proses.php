<?php
// Di setiap halaman sistem
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}
include 'koneksi.php';

// Ambil data alternatif
$alternatif = [];
$res = $conn->query("SELECT * FROM alternatif ORDER BY id");
while ($row = $res->fetch_assoc()) {
    $alternatif[$row['id']] = $row['nama'];
}
$alt_ids = array_keys($alternatif);

// Ambil data kriteria
$kriteria = [];
$res = $conn->query("SELECT * FROM kriteria ORDER BY id");
while ($row = $res->fetch_assoc()) {
    $kriteria[$row['id']] = [
        'nama' => $row['nama'],
        'bobot' => floatval($row['bobot']),
        'tipe' => $row['tipe']
    ];
}

// Ambil nilai alternatif
$nilai = [];
$res = $conn->query("SELECT * FROM nilai");
while ($row = $res->fetch_assoc()) {
    $nilai[$row['id_alternatif']][$row['id_kriteria']] = floatval($row['nilai']);
}

// Hitung matriks preferensi dengan detail rumus
$pref = [];
$detail_pref = [];

foreach ($alt_ids as $i) {
    foreach ($alt_ids as $j) {
        if ($i == $j) {
            $pref[$i][$j] = 0;
            $detail_pref[$i][$j] = "-";
        } else {
            $pref_sum = 0;
            $detail_kriteria = [];

            foreach ($kriteria as $id_krit => $k) {
                $nilai_i = $nilai[$i][$id_krit] ?? 0;
                $nilai_j = $nilai[$j][$id_krit] ?? 0;

                if ($k['tipe'] === 'minimize') {
                    $diff = $nilai_j - $nilai_i;
                    $rumus_diff = "d = x_j - x_i = {$nilai_j} - {$nilai_i} = " . round($diff, 4);
                } else {
                    $diff = $nilai_i - $nilai_j;
                    $rumus_diff = "d = x_i - x_j = {$nilai_i} - {$nilai_j} = " . round($diff, 4);
                }

                $pref_ij = max(0, $diff);
                $weighted = $pref_ij * $k['bobot'];

                $detail_kriteria[] = "<b>" . htmlspecialchars($k['nama']) . "</b>:<br>"
                    . "$rumus_diff<br>"
                    . "P = max(0, d) = $pref_ij<br>"
                    . "P × bobot = $pref_ij × {$k['bobot']} = " . round($weighted, 4);

                $pref_sum += $weighted;
            }

            $pref[$i][$j] = $pref_sum;
            $detail_pref[$i][$j] = implode("<hr>", $detail_kriteria);
        }
    }
}

// Hitung leaving, entering, net flow
$leaving = [];
$entering = [];
$net_flow = [];
$n = count($alt_ids);

foreach ($alt_ids as $i) {
    $sum_leaving = 0;
    $sum_entering = 0;
    foreach ($alt_ids as $j) {
        if ($i != $j) {
            $sum_leaving += $pref[$i][$j];
            $sum_entering += $pref[$j][$i];
        }
    }
    $leaving[$i] = $sum_leaving / ($n - 1);
    $entering[$i] = $sum_entering / ($n - 1);
    $net_flow[$i] = $leaving[$i] - $entering[$i];
}

// Urutkan ranking berdasarkan net flow descending
$ranking = $net_flow;
arsort($ranking);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Perhitungan PROMETHEE II - Sistem SPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <style>
    /* Reset & Base */
    body {
        margin: 0;
        background: #121212;
        color: #f0e9db;
        font-family: 'Montserrat', sans-serif;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    h2,
    h4 {
        font-family: 'Playfair Display', serif;
        color: #c9b037;
        font-weight: 700;
        letter-spacing: 3px;
        text-shadow: 0 0 8px rgba(201, 176, 55, 0.75);
        margin-bottom: 1.5rem;
    }

    h2 {
        font-size: 2.8rem;
        text-align: center;
        margin-bottom: 3rem;
    }

    a.btn-back,
    a.btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        border-radius: 14px;
        padding: 0.5rem 1.2rem;
        user-select: none;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
        cursor: pointer;
    }

    a.btn-back {
        background-color: transparent;
        border: 2px solid #c9b037;
        color: #c9b037;
    }

    a.btn-back:hover,
    a.btn-back:focus {
        background-color: #c9b037;
        color: #121212;
        outline: none;
        box-shadow: 0 0 8px #c9b037;
        text-decoration: none;
    }

    a.btn-primary {
        background-color: #c9b037;
        border: none;
        color: #121212;
    }

    a.btn-primary:hover,
    a.btn-primary:focus {
        background-color: #a89529;
        outline: none;
        box-shadow: 0 0 10px #a89529;
        text-decoration: none;
        color: #121212;
    }

    /* Table Styling */
    table {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto 3rem auto;
        border-collapse: separate;
        border-spacing: 0 14px;
        font-size: 1rem;
        color: #f0e9db;
        table-layout: fixed;
        word-wrap: break-word;
    }

    thead tr {
        color: #c9b037;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1.2px;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.7);
    }

    tbody tr {
        background-color: #1f1f1f;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(201, 176, 55, 0.2);
        transition: background-color 0.25s ease;
    }

    tbody tr:hover {
        background-color: #2e2e2e;
    }

    tbody td {
        padding: 1rem 1.2rem;
        vertical-align: middle;
        font-weight: 600;
        text-align: center;
    }

    tbody td:first-child {
        font-weight: 700;
    }

    tbody td[style*="text-align:left"] {
        text-align: left;
        font-weight: 400;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    /* Detail calculation style */
    hr {
        border: 0;
        border-top: 1px dashed #c9b037;
        margin: 4px 0;
        opacity: 0.5;
    }

    td>small {
        font-size: 0.85rem;
        color: #d7c86b;
        display: block;
    }

    /* Flow table colors */
    thead.table-success {
        background-color: #3d4d27 !important;
        color: #c9b037 !important;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.9);
    }

    thead.table-danger {
        background-color: #632626 !important;
        color: #c9b037 !important;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.9);
    }

    thead.table-info {
        background-color: #274c62 !important;
        color: #c9b037 !important;
        text-shadow: 0 0 7px rgba(201, 176, 55, 0.9);
    }

    /* Responsive */
    @media (max-width: 850px) {

        table,
        tbody tr,
        thead tr,
        tbody td,
        thead th {
            font-size: 0.85rem;
        }
    }
    </style>
</head>

<body>
    <h2>Perhitungan PROMETHEE II</h2>
    <div class="container">

        <a href="index.php" class="btn-back" tabindex="0" aria-label="Kembali ke halaman utama">← Kembali</a>

        <!-- Matriks Preferensi -->
        <h4>Matriks Preferensi (P(a_i, a_j))</h4>
        <table role="table" aria-describedby="matriksPrefDesc" tabindex="0">
            <caption id="matriksPrefDesc" class="visually-hidden">Matriks preferensi antar alternatif</caption>
            <thead>
                <tr>
                    <th scope="col">Alternatif</th>
                    <?php foreach ($alt_ids as $j) : ?>
                    <th scope="col"><?= htmlspecialchars($alternatif[$j]) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alt_ids as $i) : ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($alternatif[$i]) ?></th>
                    <?php foreach ($alt_ids as $j) : ?>
                    <td title="Detail perhitungan" tabindex="0">
                        <?= round($pref[$i][$j], 4) ?>
                        <br>
                        <small style="text-align:left; display:block; max-height:150px; overflow:auto;">
                            <?= $detail_pref[$i][$j] ?>
                        </small>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Leaving, Entering, Net Flow -->
        <h4>Leaving, Entering, dan Net Flow</h4>
        <table role="table" aria-describedby="flowDesc" tabindex="0">
            <caption id="flowDesc" class="visually-hidden">Tabel leaving, entering, dan net flow setiap alternatif
            </caption>
            <thead class="table-success">
                <tr>
                    <th>Alternatif</th>
                    <th>Leaving Flow (φ<sup>+</sup>)</th>
                    <th>Entering Flow (φ<sup>-</sup>)</th>
                    <th>Net Flow (φ)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alt_ids as $i) : ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($alternatif[$i]) ?></th>
                    <td><?= round($leaving[$i], 4) ?></td>
                    <td><?= round($entering[$i], 4) ?></td>
                    <td><?= round($net_flow[$i], 4) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="hasil.php" class="btn-primary" tabindex="0" aria-label="Lihat ranking alternatif">Lihat Ranking</a>

    </div>
</body>

</html>