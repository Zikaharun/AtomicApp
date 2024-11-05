<?php

session_start();

require '../functions.php';

if (isset($_SESSION['id_akun'])) {

    $id_akun = $_SESSION['id_akun'];

    $stmt = $connect->prepare ('SELECT * FROM bad_habit WHERE id_akun = ?');
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Bad habits. | Atomic App.</title>
</head>
<style>
    .underline-link {
            text-decoration: underline;
        }

    .cursor {
        cursor: pointer;
    }
</style>
<body>
    <?php include("../layout/header.html"); ?>

    <div class="flex flex-col justify-center items-center space-y-5 p-5">
    <?php $row = query("SELECT username FROM account where id_akun = $id_akun")?>
    <?php foreach( $row as $index => $rows ): ?>
        <h1 class="text-3xl font-bold text-gray-800">Welcome, <?= htmlspecialchars($rows["username"]) ?>!</h1>
    <?php endforeach; ?>
    <p class="text-center text-lg text-gray-600 py-4">Make tiny changes in each day<br> and get big results.</p>
    <div class=" grid grid-cols-1 md:grid-cols-3 gap-6 py-4 my-4 w-full max-w-4xl">
    
    <div class="flex justify-center items-center">
    <div x-data="{ open: false }" class="bg-white border border-gray-300 p-4 rounded-lg shadow-md" onmouseover="this.style.boxShadow='2px 1px'" onmouseout="this.style.boxShadow='3px 2px'">
        
    <button class="bg-gray-200 px-4 py-2 hover:bg-gray-300 rounded" 
            @click="open = ! open">
        <h1 class="font-bold text-3xl text-center text-slate-400 hover:text-slate-100">+</h1>
    </button>
    
    

    <!-- Form muncul tanpa memengaruhi layout -->
    <div x-show="open" class="absolute flex-col justify-center items-center m-4 bg-white p-4 border-2 border-gray-300 rounded-lg shadow-lg">
        <form action="add_badhabit.php" method="post" class="space-y-2">

            <label for="bad_habit_name" class="font-semibold">bad habit name: </label>
            <input class="block w-full px-4 py-2 text-sm border-2 rounded" type="text" name="bad_habit_name" id="bad_habit_name" placeholder="Bad habit name" required />

            <label for="begin_frequency" class="font-bold">Begin frequency: </label>
            <input type="number" class="block w-full px-4 py-2 text-sm border-2 rounded" id="begin_frequency" name="begin_frequency" placeholder="Begin frequency" required />

            <label for="target_frequency" class="font-semibold">target frequency: </label>
            <input class="block w-full px-4 py-2 text-sm border-2 rounded" type="number" name="target_frequency" id="target_frequency" placeholder="Target frequency" />


            <button type="submit" name="add_bad_habit" class="bg-gray-200 border-2 mx-auto border-black rounded font-semibold px-4 py-1 hover:bg-gray-100 block w-full" 
                    style="box-shadow: 3px 2px;" 
                    onmouseover="this.style.boxShadow='2px 1px'" 
                    onmouseout="this.style.boxShadow='3px 2px'">add bad habit</button>
        </form>
    </div>
    </div>

    </div>
    

    <?php
    if (isset($result) && $result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) : ?>
            <div class="bg-white border border-gray-300 p-4 rounded-lg shadow-md" onmouseover="this.style.boxShadow='2px 1px'" onmouseout="this.style.boxShadow='3px 2px'">
                <div class="flex flex-col justify-center items-center">
                    <h3 class="text-center font-bold text-xl"><?= htmlspecialchars($row["bad_habit_name"]); ?></h3>
                    
                    <p class="text-center font-semibold text-gray-600"><?= htmlspecialchars($row["begin_frequency"]); ?> /day</p>

                    <p><?= htmlspecialchars($row['target_frequency'] ); ?> /week</p>
                    
                    
                    <a href="../badhabits_progress/badhabits_progress.php?id_bad_habit=<?= $row['id_bad_habit'] ?>" class="text-center self-center underline-link">See the progress</a>
                    
                </div>
                

                <div class="flex justify-center mt-4">
                
                <div x-data="{openEdit: false}">
                <div class="flex justify-center">
                    <a @click="openEdit = !openEdit"class="mr-2 cursor" >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 cursor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </a>
                    <a href="delete_badhabit.php?id=<?= $row['id_bad_habit']; ?>" class="hover:underline text-red-500" onclick="return confirm('Are you sure?');">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" >
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                </a>
                </div>
                <div x-show="openEdit" class=" absolute flex-col justify-center items-center m-4 bg-white p-4 border-2 border-gray-300 rounded-lg shadow-lg">
                    <form action="edit_bad_habit.php?id_bad_habit=<?= $row['id_bad_habit']; ?>" method="post" class="space-y-4">
                        <div>
                            <label for="edit_badhabit_name" class="font-semibold">Bad habit name: </label>
                            <input class="block w-full px-4 py-2 text-sm border-2 rounded" type="text" name="edit_badhabit_name" id="edit_badhabit_name" value="<?= htmlspecialchars($row['bad_habit_name'])?>" />
                        </div>

                        <div>
                            <label for="edit_begin_frequency" class="font-semibold">begin frequency: </label>
                            <input class="block w-full px-4 py-2 text-sm border-2 rounded" type="number" name="edit_begin_frequency" id="edit_begin_frequency" value="<?= htmlspecialchars($row['begin_frequency'])?>" />
                        </div>

                        <div>
                            <label for="edit_target_frequency" class="font-semibold">target frequency: </label>
                            <input class="block w-full px-4 py-2 text-sm border-2 rounded" type="text" name="edit_target_frequency" edit="edit_target_frequency" value="<?= htmlspecialchars($row['target_frequency'])?>" />
                        </div>

                        <button type="submit" name="btn-edit" class="bg-gray-200 border-2 mx-auto border-black rounded font-semibold px-4 py-1 hover:bg-gray-100 block w-full" 
                                onmouseover="this.style.boxShadow='2px 1px'" 
                                onmouseout="this.style.boxShadow='3px 2px'">Edit</button>
                    </form>
                </div>
                </div>

                </div>
                
            </div>
        <?php endwhile;
    } else {
        echo "<p class='text-center font-semibold text-red-500'>No habits found</p>";
    }
    ?>


<?php 

if (isset($stmt)) {
    $stmt->close(); 
}
?>
        
      
    </div>
   
</div>

</body>
</html>