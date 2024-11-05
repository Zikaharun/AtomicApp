<?php

session_start();

require 'functions.php';


if(!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['id_akun'])) {
    $id_akun = $_SESSION['id_akun'];
    $stmt = $connect->prepare ('SELECT username FROM account WHERE id_akun = ?');
    $stmt->bind_param('i', $id_akun);

    $stmt->execute();

    if ($stmt->execute()) {

        // Simpan hasil query ke variabel $result
        $result = $stmt->get_result();
    } else {
        echo "Error executing query: " . $stmt->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Atomic app.</title>
</head>
<body class="bg-gray-100">
<header class="flex items-center justify-between p-5 border-2 shadow">
        <h1 class="text-3xl text-slate-500 font-bold"><a href="index.php">AtomicApp.</a></h1>
        <div class="space-x-4">
            <a href="logout.php" class="text-slate-500 font-semibold hover:text-slate-400 ">logout</a>
        </div>
</header>

   <div class="flex flex-col justify-center items-center space-y-5 p-5">
   <?php if (isset($result) && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <h1 class="text-3xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($row["username"]) ?>!</h1>
    <?php endwhile; ?>
<?php else: ?>
    <p class='text-center font-semibold text-red-500'>No habits found</p>
<?php endif; ?>

    <p class="text-center text-lg text-gray-600 py-4">Here's your dashboard progress to build good habits and<br> break bad ones.</p>

    <div class=" grid grid-cols-1 md:grid-cols-2 gap-6  py-4 my-4 w-full max-w-4xl">
        <button class="bg-white border-2 border-gray-300 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"  onclick="window.location.href='goodhabits/goodhabits.php'">
            Build tiny good habits.
        </button>
        <button class="bg-white border-2 border-gray-300 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"  onclick="window.location.href='badhabits/badhabits.php'">
            Reduce bad habits.
        </button>
        <button class="bg-white border-2 border-gray-300 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"  onclick="window.location.href='tracking_good_habits/tracking_good_habits.php'">
            <h3 class="text-center font-bold">Tracking your habits.</h3>
        </button>
        <button class="bg-white border-2 border-gray-300 shadow-md hover:shadow-lg transition-shadow duration-300 p-6 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"  onclick="window.location.href='tracking_badhabits_progress/tracking_badhabits.php'">
            <h3 class="text-center font-bold">Tracking your bad habits.</h3>
        </button>
    </div>
</div>
</body>
</html>