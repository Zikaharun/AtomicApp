<?php
require '../functions.php';

if (isset($_GET['id'])) {
    $id_habit_progress = $_GET['id'];

    $stmt = $connect -> prepare('SELECT id_habit FROM habit_progress WHERE id_habit_progress = ?');
    $stmt->bind_param('i', $id_habit_progress);
    $stmt->execute();
    $stmt->bind_result($id_habit);
    $stmt->fetch();
    $stmt->close();

    $stmt_delete = $connect -> prepare('DELETE FROM habit_progress WHERE id_habit_progress = ?');
    $stmt_delete->bind_param('i', $id_habit_progress);

    if( $stmt_delete->execute() ) {

        header('Location: goodhabits_progress_tracker.php?id_habit=' .$id_habit);
        exit();
       
        

    } else {

        echo "error deleted row:" .  $stmt->error ;

       
    }
    $stmt->close();

}


?>