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

$id_bad_habit_get = isset($_SESSION['id_bad_habit']) ? $_SESSION['id_bad_habit'] : 0;

// Validate if the habit belongs to the logged-in user
$stmt_habit = $connect->prepare("
    SELECT id_bad_habit, bad_habit_name, begin_frequency, target_frequency
    FROM bad_habit
    WHERE id_akun = ? 
");

$stmt_habit->bind_param("i", $id_akun);
$stmt_habit->execute();
$habit_result = $stmt_habit->get_result();

$badhabits_by_name = [];

// Check if a habit was found for this user
if ($habit_result->num_rows === 0) {
    // Habit does not belong to this user; restrict access
    echo "<script>alert('Access denied: This habit does not belong to you.');</script>";
    echo "<script>window.location.href='../goodhabits/goodhabits.php'</script>";
    exit();
}

// Organize habits by category
while ($row = $habit_result->fetch_assoc()) {
    $badhabits_by_name[$row['bad_habit_name']][] = $row;
}

$stmt_habit->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bad Habits Tracker | AtomicApp.</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include('../layout/header.html') ?>
    <div class="container mx-auto m-5 flex-col items-center">
        <h2 class="text-2xl font-semibold text-center text-gray-800 m-6 font-bold">Bad Habits Progress Tracker</h2>

        <?php if (!empty($badhabits_by_name)): ?>
            <div class="space-y-6 w-full flex flex-col items-center">
                <?php foreach ($badhabits_by_name as $bad_habit_name => $habits): ?>
                    <div class="bg-gray-200 p-6  rounded-lg shadow-md w-full max-w-4xl">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4"><?= htmlspecialchars($bad_habit_name) ?></h3>
                        
                            <?php foreach ($habits as $habit): ?>

                                <div class="bg-white shadow-lg rounded-md p-4 text-center  flex flex-col hover:shadow-lg transition-shadow duration-300  text-gray-700 hover:bg-gray-50 font-semibold" >

                                    <div class="flex justify-between items-center mb-4">
                                        <div class="text-center">
                                            <p class="text-gray-500 text-lg font-bold">Habit name</p>
                                            <span class=" text-md font-semibold"><?= htmlspecialchars($habit['bad_habit_name']) ?></span>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-lg font-bold">Target of frequency</p>
                                            <span class=" text-md font-bold"><?= htmlspecialchars($habit['begin_frequency']) ?></span>
                                        </div>
                                        
                                    </div>
    
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bad Habit</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Frequency</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">

                                            <?php
                                                
                                                $stmt_habit_progress = $connect->prepare("SELECT bh.bad_habit_name, bhp.date, bhp.daily_frequency, bhp.notes, bhp.id_bad_habit_progress FROM bad_habit_progress bhp JOIN bad_habit bh ON bhp.id_bad_habit = bh.id_bad_habit WHERE bh.id_bad_habit = ?");
                                                $stmt_habit_progress->bind_param("i", $habit['id_bad_habit']);
                                                
                                                $stmt_habit_progress->execute();
                                                $habit_result_progress = $stmt_habit_progress->get_result();
                                            ?>

                                                <?php
                                                if (isset($habit_result_progress) && $habit_result_progress->num_rows > 0) { 
                                                    while ($row = $habit_result_progress->fetch_assoc()) { ?>
                                                <tr>
                                                    <td class='px-6 py-4 whitespace-nowrap'>
                                                    <form action="../badhabits_progress/detail_bad_habit_progress.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="id_bad_habit_progress" value="<?= $row['id_bad_habit_progress']; ?>">
                                                    <button type="submit" class="hover:underline"><?= $row["bad_habit_name"] ?></button>
                                                    </form>
                                                    </td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["date"]?></td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["daily_frequency"]?></td>
                                                    <td class='px-6 py-4 whitespace-nowrap'><?= $row["notes"]?></td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <a href="../badhabits_progress/delete_bhp.php?id=<?= $row['id_bad_habit_progress']; ?>" >
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

                                </div>
                            <?php endforeach; ?>

                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">Not habits found in this account.</p>
        <?php endif; ?>
    </div>
</body>
</html>