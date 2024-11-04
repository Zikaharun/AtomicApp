<?php 
session_start(); // Memulai session

require '../functions.php'; // Memanggil file koneksi database

// Periksa jika pengguna sudah login
if (isset($_SESSION['id_akun'])) {
    // Ambil id_akun dari session
    $id_akun = $_SESSION['id_akun'];

    // Periksa jika data habit dikirim melalui POST
    if (isset($_POST['add_bad_habit'])) {
        // Ambil data dari formulir
        $bad_habit_name = $_POST['bad_habit_name'];
        $begin_frequency = $_POST['begin_frequency'];
        $target_frequency = $_POST['target_frequency'];

        // Validasi input (jika diperlukan)
        if (!empty($bad_habit_name) ) {
            // Siapkan query untuk memasukkan data habit
            $stmt = $connect->prepare("INSERT INTO bad_habit (id_akun, bad_habit_name, begin_frequency, target_frequency) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $id_akun, $bad_habit_name, $begin_frequency, $target_frequency); // 'i' untuk integer, 's' untuk string

            // Eksekusi pernyataan
            if ($stmt->execute()) {
                // Habit berhasil ditambahkan
                echo "<script>
                        alert('bad habit to reduce has been added!');
                        document.location.href = 'badhabits.php'; // Arahkan ke halaman yang diinginkan
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