<?php
session_start();
include 'database/koneksi.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, nama, username, password, role, status FROM user WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user) {
            if (strtolower($user['status']) !== 'aktif') {
                $error = "Akun Anda berstatus nonaktif. Hubungi Admin!";
            } else {
                // Mendukung password yang di-hash (password_hash) maupun plain text lama
                $password_valid = password_verify($password, $user['password']) || ($password === $user['password']);

                if ($password_valid) {
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['nama']     = $user['nama'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Username atau password salah!";
                }
            }
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            padding: 16px;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 28px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .login-card h2 {
            font-size: 20px;
            color: #111827;
            margin-bottom: 6px;
        }
        .login-card p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }
        .form-group input {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .form-group input:focus {
            border-color: #111827;
        }
        .btn-login {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            background-color: #111827;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
        }
        .btn-login:hover { opacity: 0.92; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login Aplikasi Kasir</h2>
        <p>Masuk menggunakan akun Admin atau Pegawai</p>

        <?php if ($error !== ""): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>