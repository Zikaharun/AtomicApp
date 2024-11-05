<?php
session_start();
require '../functions.php';

// Periksa apakah ada id_habit_progress yang dikirim melalui POST
if (isset($_POST['id_bad_habit_progress'])) {
    $_SESSION['id_bad_habit_progress'] = $_POST['id_bad_habit_progress'];
    // Redirect ke halaman yang sama untuk memuat data yang benar
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil id_habit_progress dari session atau GET
$id_bad_habit_progress = isset($_SESSION['id_bad_habit_progress']) ? $_SESSION['id_bad_habit_progress'] : (isset($_GET['id_bad_habit_progress']) ? intval($_GET['id_bad_habit_progress']) : null);

// Jika id_habit_progress tidak tersedia, redirect ke halaman lain atau tampilkan pesan error
if ($id_bad_habit_progress === null) {
    header("Location: ../badhabits/badhabits_progress.php"); // Ganti dengan halaman yang sesuai
    exit;
}

// Query untuk mendapatkan detail progress habit
$stmt_progress = $connect->prepare("SELECT bhp.*, bh.bad_habit_name FROM bad_habit_progress bhp JOIN bad_habit bh ON bhp.id_bad_habit = bh.id_bad_habit WHERE bhp.id_bad_habit_progress = ?");
$stmt_progress->bind_param("i", $id_bad_habit_progress);
$stmt_progress->execute();
$progress_result = $stmt_progress->get_result()->fetch_assoc();

// Cek apakah progress_result ada
if (!$progress_result) {
    // Tindakan jika tidak ada hasil
    echo "Progress tidak ditemukan.";
    exit;
}

// Query untuk mendapatkan data mingguan untuk chart
$stmt_weekly = $connect->prepare("
    SELECT date, SUM(daily_frequency) AS total_daily_frequency 
    FROM bad_habit_progress 
    WHERE id_bad_habit = ? AND date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
    GROUP BY date
    ORDER BY date ASC
");
$stmt_weekly->bind_param("i", $progress_result['id_bad_habit']);
$stmt_weekly->execute();
$weekly_data = $stmt_weekly->get_result()->fetch_all(MYSQLI_ASSOC);

$total_weekly_progress = array_sum(array_column($weekly_data, 'total_daily_frequency'));


// Query untuk mendapatkan data harian selama 30 hari terakhir
$stmt_daily = $connect->prepare("
    SELECT date, SUM(daily_frequency) AS total_daily_frequency 
    FROM bad_habit_progress 
    WHERE id_bad_habit = ? AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY date
    ORDER BY date ASC;
");

$stmt_daily->bind_param("i", $progress_result['id_bad_habit']);
$stmt_daily->execute();
$daily_data = $stmt_daily->get_result()->fetch_all(MYSQLI_ASSOC);

// Query untuk mendapatkan total progres harian
$stmt_total_daily = $connect->prepare('SELECT SUM(daily_frequency) AS total_progress_daily FROM bad_habit_progress WHERE id_bad_habit = ?');
$stmt_total_daily->bind_param('i', $progress_result['id_bad_habit']);
$stmt_total_daily->execute();
$total_daily_data = $stmt_total_daily->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Habit Progress Detail | Atomic App.</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include("../layout/header.html"); ?>
    <div class="container mx-auto p-5 bg-gray-100">
        <h1 class="text-4xl font-bold text-center mb-8 text-blue-600">Habit Progress Detail</h1>

        <?php if ($progress_result): ?>
            <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                <h2 class="font-bold text-2xl"><?= htmlspecialchars($progress_result['bad_habit_name']); ?></h2>
                <p class="text-slate-500 font-semibold"><strong>Date:</strong> <?= htmlspecialchars($progress_result['date']); ?></p>
                <p class="text-slate-500 font-semibold"><strong>Daily Frequency:</strong> <?= htmlspecialchars($progress_result['daily_frequency']); ?></p>
                <p class="text-slate-500 font-semibold"><strong>Notes:</strong> <?= htmlspecialchars($progress_result['notes']); ?></p>
            </div>

            <!-- Weekly Progress Chart -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Weekly Progress</h2>
                <h3 class="text-xl font-semibold text-gray-500">Total Weekly Progress: <?= $total_weekly_progress ?> times</h3>
                <canvas id="weeklyProgressChart"></canvas>
            </div>

            <!-- Daily Progress Chart -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Daily Progress</h2>
                <?php if ($total_daily_data): ?>
                    <h3 class="text-xl text-gray-500 font-semibold mb-4"><?= $total_daily_data['total_progress_daily']?> times.</h3>
                <?php endif; ?>

                <canvas id="dailyProgressChart"></canvas>
            </div>

            <script>
                const ctxWeekly = document.getElementById('weeklyProgressChart').getContext('2d');
                const ctxDaily = document.getElementById('dailyProgressChart').getContext('2d');

                const weeklyData = <?= json_encode($weekly_data); ?>;
                const dailyData = <?= json_encode($daily_data); ?>;

                // Extract dates and frequency values for the weekly chart
                const weeklyDates = weeklyData.map(data => data.date);
                const weeklyFrequencies = weeklyData.map(data => data.total_daily_frequency);

                // Generate the weekly chart
                new Chart(ctxWeekly, {
                    type: 'line',
                    data: {
                        labels: weeklyDates,
                        datasets: [{
                            label: 'Daily Frequency',
                            data: weeklyFrequencies,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Weekly Progress'
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Date' } },
                            y: { title: { display: true, text: 'Frequency' } }
                        }
                    }
                });

                // Extract dates and frequency values for the daily chart
                const dailyDates = dailyData.map(data => data.date);
                const dailyFrequencies = dailyData.map(data => data.total_daily_frequency);

                // Generate the daily chart
                new Chart(ctxDaily, {
                    type: 'bar',
                    data: {
                        labels: dailyDates,
                        datasets: [{
                            label: 'Daily Frequency',
                            data: dailyFrequencies,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Daily Progress (Last 30 days).'
                            }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Date' } },
                            y: { title: { display: true, text: 'Frequency' },
                                beginAtZero: true }
                           
                        }
                    }
                });
            </script>
        <?php else: ?>
            <p>No habit progress data found.</p>
        <?php endif; ?>
    </div>
</body>
</html>