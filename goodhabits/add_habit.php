<?php
session_start(); // Memulai session

require '../functions.php'; // Memanggil file koneksi database

// Periksa jika pengguna sudah login
if (isset($_SESSION['id_akun'])) {
    // Ambil id_akun dari session
    $id_akun = $_SESSION['id_akun'];

    // Periksa jika data habit dikirim melalui POST
    if (isset($_POST['add_habit'])) {
        // Ambil data dari formulir
        $habit_name = $_POST['habit_name'];
        $frequency_target = $_POST['frequency_target'];
        $category = $_POST['category'];

        // Validasi input (jika diperlukan)
        if (!empty($habit_name) && !empty($frequency_target) && !empty($category)) {
            // Siapkan query untuk memasukkan data habit
            $stmt = $connect->prepare("INSERT INTO habit (id_akun, habit_name, frekuensi_target, category) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $id_akun, $habit_name, $frequency_target, $category); // 'i' untuk integer, 's' untuk string

            // Eksekusi pernyataan
            if ($stmt->execute()) {
                // Habit berhasil ditambahkan
                echo "<script>
                        alert('Habit berhasil ditambahkan!');
                        document.location.href = 'goodhabits.php'; // Arahkan ke halaman yang diinginkan
                      </script>";
            } else {
                // Jika terjadi kesalahan
                echo "Error: " . $stmt->error; // Tampilkan error
            }

            // Tutup prepared statement
            $stmt->close();
        } else {
            // Jika input tidak valid
            echo "<script>alert('Silakan isi semua bidang!');</script>";
        }
    }
} else {
    // Jika pengguna belum login
    echo "<script>alert('Anda harus login terlebih dahulu!');</script>";
    echo "<script>document.location.href = 'login.php';</script>"; // Arahkan ke halaman login
    exit;
}
?>
