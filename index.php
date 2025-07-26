<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SPK PROMETHEE II - Rumah Sakit Terbaik Purwokerto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@600&display=swap');

        body {
            background: linear-gradient(135deg, #0d0d0d, #1a1a1a);
            color: #f8f5f0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 3rem 1rem;
        }

        header {
            width: 100%;
            max-width: 900px;
            margin-bottom: 3.5rem;
            text-align: center;
            font-family: 'Playfair Display', serif;
            color: #d4af37;
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: 4px;
            line-height: 1.2;
            text-transform: uppercase;
            text-shadow:
                0 0 6px rgba(212, 175, 55, 0.6),
                0 0 10px rgba(212, 175, 55, 0.5),
                0 3px 4px rgba(0, 0, 0, 0.25);
            user-select: none;
        }

        header span {
            font-size: 1.5rem;
            font-weight: 400;
            letter-spacing: 1.5px;
            color: #f0e9d2;
            display: block;
            margin-top: 0.3rem;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2.5rem;
            width: 100%;
            max-width: 900px;
        }

        .card-menu {
            background: rgba(212, 175, 55, 0.18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 18px;
            box-shadow:
                0 5px 18px rgba(212, 175, 55, 0.45),
                inset 0 0 18px rgba(212, 175, 55, 0.18);
            padding: 2.8rem 2rem;
            color: #d4af37;
            text-align: center;
            font-weight: 600;
            font-size: 1.25rem;
            text-decoration: none;
            transition: all 0.4s ease;
            user-select: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            border: 2.5px solid transparent;
        }

        .card-menu i {
            font-size: 4.5rem;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 5px #d4af37);
            line-height: 1;
        }

        .card-menu:hover,
        .card-menu:focus {
            color: #fff;
            background: rgba(212, 175, 55, 0.38);
            box-shadow:
                0 8px 30px rgba(212, 175, 55, 0.75),
                inset 0 0 22px rgba(212, 175, 55, 0.4);
            border-color: #d4af37;
            transform: translateY(-9px) scale(1.08);
            outline: none;
        }

        @media (max-width: 480px) {
            header {
                font-size: 2.3rem;
                letter-spacing: 3px;
            }

            header span {
                font-size: 1.2rem;
            }

            .card-menu {
                font-size: 1.1rem;
                padding: 2.5rem 1.6rem;
            }

            .card-menu i {
                font-size: 3.8rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <header>
        SISTEM PENDUKUNG KEPUTUSAN
        <span>Metode PROMETHEE II - Rumah Sakit Terbaik Purwokerto</span>
    </header>

    <main class="card-container" role="navigation" aria-label="Menu utama sistem pendukung keputusan">
        <a href="alternatif.php" class="card-menu" title="Data Alternatif" tabindex="0" role="link"
            aria-label="Data Alternatif">
            <i class="bi bi-person-badge-fill" aria-hidden="true"></i>
            Data Alternatif
        </a>
        <a href="kriteria.php" class="card-menu" title="Data Kriteria" tabindex="0" role="link"
            aria-label="Data Kriteria">
            <i class="bi bi-list-check" aria-hidden="true"></i>
            Data Kriteria
        </a>
        <a href="nilai.php" class="card-menu" title="Input Nilai Alternatif" tabindex="0" role="link"
            aria-label="Input Nilai Alternatif">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
            Input Nilai Alternatif
        </a>
        <a href="proses.php" class="card-menu" title="Proses Perhitungan" tabindex="0" role="link"
            aria-label="Proses Perhitungan">
            <i class="bi bi-calculator-fill" aria-hidden="true"></i>
            Proses Perhitungan
        </a>
        <a href="hasil.php" class="card-menu" title="Lihat Hasil" tabindex="0" role="link" aria-label="Lihat Hasil">
            <i class="bi bi-bar-chart-fill" aria-hidden="true"></i>
            Lihat Hasil
        </a>
    </main>
</body>

</html>