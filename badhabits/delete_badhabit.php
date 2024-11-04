<?php
if (isset($_GET['id'])) {
    $id_habit = $_GET['id'];
    $stmt_ = $connect->prepare("DELETE FROM habit_progress WHERE id_habit = ?");
    $stmt_->bind_param('i', $id_habit);

    if ($stmt_->execute()) {
        header('Location: goodhabits.php');
    } else {
        echo 'Error deleting record: '. $stmt_->error;

    }

    $stmt_ ->close();

    $stmt = $connect->prepare("DELETE FROM habit WHERE id_habit = ?");
    $stmt->bind_param("i", $id_habit);

    if ($stmt->execute()) {
        header("Location: goodhabits.php");
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

?>