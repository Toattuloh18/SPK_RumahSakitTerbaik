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
    <title>Registrasi - SPK Rumah Sakit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@600&display=swap"
        rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #0d0d0d, #1a1a1a);
        color: #f8f5f0;
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        padding: 2rem;
    }

    .register-box {
        background: rgba(212, 175, 55, 0.1);
        padding: 2.5rem;
        border-radius: 18px;
        backdrop-filter: blur(14px);
        box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        width: 100%;
        max-width: 420px;
        border: 2px solid rgba(212, 175, 55, 0.4);
    }

    .register-box h2 {
        font-family: 'Playfair Display', serif;
        color: #d4af37;
        text-align: center;
        margin-bottom: 1.8rem;
        font-size: 2rem;
    }

    .form-label {
        color: #f8f5f0;
        font-weight: 600;
    }

    .form-control {
        background-color: #1f1f1f;
        border: 2px solid #d4af37;
        color: #fff;
    }

    .form-control:focus {
        background-color: #2a2a2a;
        border-color: #fff;
        color: #fff;
    }

    .btn-primary {
        background-color: #d4af37;
        border: none;
        font-weight: 700;
        width: 100%;
    }

    .btn-primary:hover {
        background-color: #a89529;
        box-shadow: 0 0 10px #a89529;
    }

    .alert {
        margin-bottom: 1rem;
    }

    .text-center a {
        color: #d4af37;
        font-weight: 600;
    }

    .text-center a:hover {
        color: #fff;
    }
    </style>
</head>

<body>
    <div class="register-box">
        <h2>Registrasi Pengguna</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>

        <div class="text-center mt-3">
            <span>Sudah punya akun? <a href="login.php">Login</a></span>
        </div>
    </div>
</body>

</html>