<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil, silakan login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Email sudah digunakan atau terjadi kesalahan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi - SPK RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@500&display=swap');

    body {
        background: linear-gradient(135deg, #0d0d0d, #1a1a1a);
        color: #f8f5f0;
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .card {
        background: rgba(212, 175, 55, 0.1);
        border: 2px solid #d4af37;
        border-radius: 16px;
        padding: 2rem;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 8px 24px rgba(212, 175, 55, 0.3);
    }

    .card h2 {
        font-family: 'Playfair Display', serif;
        text-align: center;
        color: #d4af37;
        margin-bottom: 1.5rem;
        font-size: 2rem;
    }

    .form-control {
        background-color: #1f1f1f;
        border: 1.5px solid #d4af37;
        color: #fff;
    }

    .form-control:focus {
        background-color: #2a2a2a;
        border-color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.4);
    }

    .btn-primary {
        background-color: #d4af37;
        border: none;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #b89e30;
    }

    .btn-link {
        color: #d4af37;
        text-decoration: none;
        font-weight: 500;
    }

    .btn-link:hover {
        color: #fff;
        text-decoration: underline;
    }

    .alert {
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
    }
    </style>
</head>

<body>
    <div class="card">
        <h2>Registrasi Pengguna</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label class="form-label text-white">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-white">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-white">Kata Sandi</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 mt-2">Daftar</button>
            <div class="text-center mt-3">
                <a href="login.php" class="btn-link">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</body>

</html>