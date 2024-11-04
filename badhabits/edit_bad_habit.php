<?php
require '../functions.php';

if (isset($_POST["btn-edit"])) {
    
    $id_bad_habit = $_GET['id_bad_habit'];

    $bad_habit_name = $_POST['edit_badhabit_name'];
    $begin_frequency = $_POST['edit_begin_frequency'];
    $target_frequency = $_POST['edit_target_frequency'];

$stmt = $connect->prepare('UPDATE bad_habit SET bad_habit_name = ?, begin_frequency = ?, target_frequency = ? WHERE id_bad_habit = ?');
$stmt->bind_param('siii', $bad_habit_name, $begin_frequency, $target_frequency, $id_bad_habit);
if( $stmt->execute() ) {
    header('Location: badhabits.php');

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();

}

?>