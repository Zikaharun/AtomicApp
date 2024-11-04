<?php
require '../functions.php';

if (isset($_GET['id_bad_habit_progress'])) {
    $id_bad_habit_progress = $_GET['id_bad_habit_progress'];

    $stmt = $connect -> prepare('SELECT id_bad_habit FROM bad_habit_progress WHERE id_bad_habit_progress = ?');
    $stmt->bind_param('i', $id_bad_habit_progress);
    $stmt->execute();
    $stmt->bind_result($id_bad_habit);
    $stmt->fetch();
    $stmt->close();

    $stmt_delete = $connect -> prepare('DELETE FROM bad_habit_progress WHERE id_bad_habit_progress = ?');
    $stmt_delete->bind_param('i', $id_bad_habit_progress);

    if( $stmt_delete->execute() ) {

        header('Location: badhabits_progress.php?id_bad_habit=' .$id_bad_habit);
        exit();
       
        

    } else {

        echo "error deleted row:" .  $stmt->error ;

       
    }
    $stmt->close();
}


?>