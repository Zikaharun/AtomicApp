<?php
session_start();
require 'functions.php';

// Cek apakah ada cookie
if (isset($_COOKIE['account']) && isset($_COOKIE['key'])) {
    $account = $_COOKIE['account'];
    $key = $_COOKIE['key'];

    // Gunakan prepared statements untuk keamanan
    $stmt = $connect->prepare("SELECT username FROM account WHERE id_akun = ?");
    $stmt->bind_param("i", $account);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Verifikasi cookie
    if ($key === hash('sha256', $row['username'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_akun'] = $account;
    }
}

// Jika sudah login, redirect ke index.php
if (isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

// Cek apakah tombol login ditekan
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Gunakan prepared statements
    $stmt = $connect->prepare("SELECT * FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah username ditemukan
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION['id_akun'] = $row['id_akun'];  // Simpan id_akun ke session

            // Cek jika remember me diaktifkan
            if (isset($_POST['remember'])) {
                // Set cookie untuk 2 menit
                setcookie('account', $row['id_akun'], time() + 4320);
                setcookie('key', hash('sha256', $row['username']), time() + 4320);
            }

            // Redirect ke halaman index setelah login berhasil
            header("Location: index.php");
            exit;
        }
    }

    // Jika login gagal, tampilkan error
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login | Atomic App</title>
</head>
<body class="bg-gray-50">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 w-full max-w-md border border-gray-300 rounded-lg shadow-md space-y-6">
            <h1 class="text-center text-3xl font-bold text-gray-800">Atomic App</h1>
            <h2 class="text-center text-xl font-semibold text-gray-600">Login</h2>

            <?php if (isset($error)): ?>
                <p class="text-red-600 font-bold text-center">Username/password incorrect!</p>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username:</label>
                    <input class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" type="text" name="username" id="username" placeholder="Username" required />
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password:</label>
                    <input class="mt-1 px-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" type="password" name="password" id="password" placeholder="Password" required />
                </div>

                <button type="submit" name="login" class="w-full bg-blue-500 text-white rounded-lg font-semibold py-2 hover:bg-blue-600 transition duration-200">Login</button>
            </form>

            <p class="text-center text-sm text-gray-600">Forgot account? <a href="register.php" class="text-blue-500 font-semibold underline">Register</a></p>
        </div>
    </div>
</body>
</html>