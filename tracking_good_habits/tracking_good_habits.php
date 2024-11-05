<?php
session_start();

require '../functions.php';

// Check if the user is logged in
if (!isset($_SESSION['id_akun'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

$id_akun = $_SESSION['id_akun'];

$id_habit_get = isset($_SESSION['id_habit']) ? $_SESSION['id_habit'] : 0;

// Validate if the habit belongs to the logged-in user
$stmt_habit = $connect->prepare("
    SELECT habit_name, category, id_habit, frekuensi_target
    FROM habit
    WHERE id_akun = ? 
");

$stmt_habit->bind_param("i", $id_akun);
$stmt_habit->execute();
$habit_result = $stmt_habit->get_result();

$habits_by_category = [];

// Check if a habit was found for this user
if ($habit_result->num_rows === 0) {
    // Habit does not belong to this user; restrict access
    echo "<script>alert('Access denied: This habit does not belong to you.');</script>";
    echo "<script>window.location.href='../goodhabits/goodhabits.php'</script>";
    exit();
}

// Organize habits by category
while ($row = $habit_result->fetch_assoc()) {
    $habits_by_category[$row['category']][] = $row;
}

$stmt_habit->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Good Habits Tracker | AtomicApp.</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-r from-gray-100 to-gray-200 rounded">
    <?php include('../layout/header.html') ?>
    <div class="container mx-auto m-5 flex flex-col items-center">
        <h2 class="text-2xl font-semibold text-center text-gray-800 m-6 font-bold">Good Habits Progress Tracker</h2>

        <?php if (!empty($habits_by_category)): ?>
            <div class="space-y-6 w-full">
                <?php foreach ($habits_by_category as $category => $habits): ?>
                    <div class="bg-gray-200 p-4 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3"><?= htmlspecialchars($category) ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($habits as $habit): ?>

                                <div class="bg-white shadow-lg rounded-md p-4 text-center flex flex-col justify-between hover:shadow-lg transition-shadow duration-300 p-6 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold" >

                                    <div class="flex justify-between items-center my-4 mb-5">
                                        <div class="flex flex-col">
                                            <p class="text-gray-500 text-lg font-bold">Habit name</p>
                                            <span class="font-medium text-md font-semibold"><?= htmlspecialchars($habit['habit_name']) ?></span>
                                        </div>
                                        <div class="flex flex-col">
                                            <p class="text-gray-500 text-lg font-bold">Target of frequency</p>
                                            <span class="font-medium text-md font-bold"><?= htmlspecialchars($habit['frekuensi_target']) ?></span>
                                        </div>
                                        
                                    </div>
    
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Habit</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Frequency</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">

                                            <?php
                                                
                                                $stmt_habit_progress = $connect->prepare("SELECT h.habit_name, hp.date, hp.daily_frequency, hp.notes, hp.id_habit_progress FROM habit_progress hp JOIN habit h ON hp.id_habit = h.id_habit WHERE h.id_habit = ?");
                                                $stmt_habit_progress->bind_param("i", $habit['id_habit']);
                                                $stmt_habit_progress->execute();
                                                $habit_result_progress = $stmt_habit_progress->get_result();
                                            ?>

                                                <?php
                                                if (isset($habit_result_progress) && $habit_result_progress->num_rows > 0) { 
                                                    while ($row = $habit_result_progress->fetch_assoc()) { ?>
                                                <tr>
                                                    <td class='px-6 py-4 whitespace-nowrap'>
                                                    <form action="../goodhabits_progress/detail_habit_progress.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="id_habit_progress" value="<?= $row['id_habit_progress']; ?>">
                                                    <button type="submit" class="hover:underline"><?= $row["habit_name"] ?></button>
                                                    </form>
                                                    </td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["date"]?></td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["daily_frequency"]?></td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["notes"]?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <a href="../goodhabits_progress/delete_gp.php?id=<?= $row['id_habit_progress']; ?>" >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </a> 
                                                    </td>
                                                </tr>
                                                    <?php }
                                                } else {
                                                    echo "<option disabled>No habits found</option>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                </div>

                                <div class="overflow-x-auto mt-4">
                                    <div class="grid grid-cols-10  gap-1">
                                        <?php
                                            $stmt_habit_progress = $connect->prepare("SELECT h.habit_name, hp.date, hp.daily_frequency, hp.notes, hp.id_habit_progress FROM habit_progress hp JOIN habit h ON hp.id_habit = h.id_habit WHERE h.id_habit = ?");
                                            $stmt_habit_progress->bind_param("i", $habit['id_habit']);
                                            $stmt_habit_progress->execute();
                                            $habit_result_progress = $stmt_habit_progress->get_result();
                                        ?>

                                        <?php
                                        // Membuat array untuk menyimpan progress
                                        $progressDays = [];
                                        if (isset($habit_result_progress) && $habit_result_progress->num_rows > 0) {
                                            while ($row = $habit_result_progress->fetch_assoc()) {
                                                $date = date('Y-m-d', strtotime($row['date']));
                                                $progressDays[$date] = $row['daily_frequency'];
                                            }
                                        }

                                        // Menghitung jumlah hari dalam sebulan
                                        $startDate = new DateTime('first day of this week');
                                        $endDate = new DateTime('last day of this week');
                                        $interval = new DateInterval('P1D');
                                        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

                                        foreach ($dateRange as $date) {
                                            $currentDate = $date->format('Y-m-d');
                                            $dailyFrequency = isset($progressDays[$currentDate]) ? $progressDays[$currentDate] : 0;
                                            $color = $dailyFrequency > 0 ? 'bg-green-500' : 'bg-gray-200'; // Hijau jika ada progress, abu-abu jika tidak
                                        ?>
                                            <div class="<?= $color ?> w-6 h-6 rounded-md hover:scale-105 transition-transform duration-200" title="<?= $currentDate . ': ' . $dailyFrequency ?> <?= $dailyFrequency > 0 ? 'times' : '' ?>">

                                                
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">Not habits found in this account.</p>
        <?php endif; ?>
    </div>
</body>
</html>
