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

// Store the id_habit in session if it's provided in GET or POST
if (isset($_GET['id_bad_habit'])) {
    $_SESSION['id_bad_habit'] = intval($_GET['id_bad_habit']);
} elseif (isset($_POST['id_bad_habit'])) {
    $_SESSION['id_bad_habit'] = intval($_POST['id_bad_habit']);
}

$id_bad_habit_get = isset($_SESSION['id_bad_habit']) ? $_SESSION['id_bad_habit'] : 0;

// Validate if the habit belongs to the logged-in user
$stmt_habit = $connect->prepare("SELECT * FROM bad_habit WHERE id_bad_habit = ? AND id_akun = ?");
$stmt_habit->bind_param("ii", $id_bad_habit_get, $id_akun);
$stmt_habit->execute();
$bad_habit_result = $stmt_habit->get_result();

// Check if a habit was found for this user
if ($bad_habit_result->num_rows === 0) {
    // Habit does not belong to this user; restrict access
    echo "<script>alert('Access denied: This habit does not belong to you.');</script>";
    echo "<script>window.location.href='../badhabits/badhabits.php'</script>";
    exit();
}

// Continue with your code here, such as displaying the habit details, etc.
if (isset($_POST['submit'])) {
    $id_bad_habit = $_POST["id_bad_habit"];
    $date = $_POST["date"];
    $daily_frequency = $_POST["daily_frequency"];
    $notes = $_POST["notes"];

    // Input validation
    if (!empty($id_bad_habit) && !empty($date) && !empty($daily_frequency) && !empty($notes)) {
        // Prepare the query to insert habit progress
        $stmt = $connect->prepare("INSERT INTO bad_habit_progress (id_bad_habit, date, daily_frequency, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $id_bad_habit, $date, $daily_frequency, $notes);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect upon successful addition
            header("Location: badhabits_progress.php?id_bad_habit=" . $id_bad_habit_get);
            exit();
        } else {
            // Show error if any issue occurs
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill out all fields!');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bad Habit Progress Tracker | Atomic App.</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php include("../layout/header.html"); ?>

    <div class="container mx-auto p-5 bg-gray-100">
        <h1 class="text-4xl font-bold text-center mb-8 text-blue-600">Bad habits Progress Tracker</h1>

        <div class="bg-white shadow-lg rounded-md p-6 mb-8">

        
        <?php 

            $stmt_target = $connect->prepare("SELECT bh.bad_habit_name, bh.begin_frequency, bh.target_frequency, SUM(bhp.daily_frequency) AS total_bad_frequency FROM bad_habit AS bh JOIN bad_habit_progress AS bhp ON bh.id_bad_habit = bhp.id_bad_habit WHERE bh.id_bad_habit = ?  GROUP BY bh.id_bad_habit ");
            $stmt_target->bind_param("i", $id_bad_habit_get);
            $stmt_target->execute();
            $target_result = $stmt_target->get_result();

        ?>
        <?php if (isset($target_result) && $target_result->num_rows > 0) : ?>
        <?php while($row = $target_result-> fetch_assoc()) : ?>
            <h2 class="text-center text-xl text-red-500 font-semibold">Your target for the week <?= $row['bad_habit_name'] ?> <?= $row['begin_frequency']?> times!</h2>
            <p class="text-center text-md text-red-500 font-semibold">You just  <?= $row['total_bad_frequency'] ?> times done in this week!</p>
        <?php endwhile; ?>
        <?php  else : ?>
            <?php 

                $stmt_target = $connect->prepare("SELECT bad_habit_name, begin_frequency FROM bad_habit WHERE id_bad_habit = ?");
                $stmt_target->bind_param("i", $id_bad_habit_get);
                $stmt_target->execute();
                $target_result = $stmt_target->get_result();

            ?>

            <?php while($row = $target_result-> fetch_assoc()) : ?>

            <h2 class="text-center text-xl text-red-500 font-semibold">Your target for a week <?= $row['bad_habit_name'] ?> <?= $row['begin_frequency']?> times!</h2>
            <p class="text-center text-md text-red-500 font-semibold">You haven't done anything yet this week!</p>

        <?php endwhile; ?>
        <?php endif; ?>


        </div>


        <!-- Form to add new habit progress -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Add Bad Habit Progress</h2>

            <form class="space-y-4" action="badhabits_progress.php" method="post">
            

                    <div>
                         <?php
                        if (isset($bad_habit_result) && $bad_habit_result->num_rows > 0) { 
                            while ($row = $bad_habit_result->fetch_assoc()) { ?>
                                <label for="id_bad_habit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 p-2"><?= htmlspecialchars($row["bad_habit_name"]) ?></opton>
                                <input type="hidden" name="id_bad_habit" id="id_bad_habit" value="<?= htmlspecialchars($row['id_bad_habit'])?>">
                            <?php }
                        } else {
                            echo "<option disabled>No habits found</option>";
                        }
                        ?>
                        
                    
                    </div>
        
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="date" name="date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 p-2">
                </div>

                <div>
              <label for="daily_frequency" class="block text-sm font-medium text-gray-700">Daily Frequency</label>
                    <input type="number" id="daily_frequency" name="daily_frequency" required class="mt-1 block  w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 p-2">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full p-4 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition duration-200" name="submit">Add Bad Habit Progress</button>
            </form>
        </div>


        <!-- Table to display existing habit progress -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Habit Progress Entries</h2>
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
                         
                         $stmt_habit_progress = $connect->prepare("SELECT bh.bad_habit_name, bhp.date, bhp.daily_frequency, bhp.notes, bhp.id_bad_habit_progress FROM bad_habit_progress bhp JOIN bad_habit bh ON bhp.id_bad_habit = bh.id_bad_habit WHERE bhp.id_bad_habit = ?");
                         $stmt_habit_progress->bind_param("i", $id_bad_habit_get);
                         $stmt_habit_progress->execute();
                         $habit_result_progress = $stmt_habit_progress->get_result();
                    ?>

                        <?php
                        if (isset($habit_result_progress) && $habit_result_progress->num_rows > 0) { 
                            while ($row = $habit_result_progress->fetch_assoc()) { ?>
                        <tr>
                            <td class='px-6 py-4 whitespace-nowrap'>
                            <form action="detail_bad_habit_progress.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_bad_habit_progress" value="<?= $row['id_bad_habit_progress']; ?>">
                            <button type="submit" class="hover:underline"><?= $row["bad_habit_name"] ?></button>
                            </form>
                            </td>
                            <td class='px-6 py-4 whitespace-nowrap'><?= $row["date"]?></td>
                            <td class='px-6 py-4 whitespace-nowrap'><?= $row["daily_frequency"]?></td>
                            <td class='px-6 py-4 whitespace-nowrap'><?= $row["notes"]?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="delete_bhp.php?id_bad_habit_progress=<?= $row['id_bad_habit_progress'];?>" >
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
    </div>

</body>
</html>


