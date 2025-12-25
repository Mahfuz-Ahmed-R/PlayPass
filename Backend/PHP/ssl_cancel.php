<?php
include __DIR__ . "/connection.php";
$tran_id = $_GET['tran_id'] ?? $_POST['tran_id'] ?? null;
if ($tran_id) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE tran_id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $tran_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payment Cancelled</title></head><body>
<h2>Payment Cancelled</h2>
<p>Transaction id: <?php echo htmlspecialchars($tran_id); ?></p>
<p>You cancelled the transaction.</p>
</body></html>
