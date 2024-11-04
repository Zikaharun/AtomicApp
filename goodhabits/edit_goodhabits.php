<?php
require '../functions.php';

if (isset($_POST["btn-edit"])) {
    
    $id_habit = $_GET['id'];

    $habit_name = $_POST['edit_habit_name'];
    $frekuensi_target = $_POST['edit_frequency_target'];
    $category = $_POST['edit_category'];

$stmt = $connect->prepare('UPDATE habit SET habit_name = ?, frekuensi_target = ?, category = ? WHERE id_habit = ?');
$stmt->bind_param('sisi', $habit_name, $frekuensi_target, $category, $id_habit);
if( $stmt->execute() ) {
    header('Location: goodhabits.php');

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();

}




?>