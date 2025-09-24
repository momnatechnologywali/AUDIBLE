<?php
// save_progress.php - AJAX endpoint to save progress (called by JS)
include 'db.php';
 
if ($_POST && isset($_SESSION['user_id'])) {
    $book_id = $_POST['book_id'];
    $progress = (int)$_POST['progress'];
    $stmt = $pdo->prepare("UPDATE user_progress SET progress_seconds = ?, last_listened = CURRENT_TIMESTAMP WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$progress, $_SESSION['user_id'], $book_id]);
    echo 'OK';
} else {
    http_response_code(403);
}
?>
