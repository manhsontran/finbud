<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT rb.remaining_budget, c.category_name 
        FROM remaining_budget rb 
        JOIN Budgets b ON rb.budget_id = b.budget_id 
        JOIN Categories c ON b.category_id = c.category_id 
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$budgets = [];
while ($row = $result->fetch_assoc()) {
    $row['remaining_budget'] = $row['remaining_budget'] ?? 0;
    $budgets[] = $row;
}
echo json_encode($budgets);
?>
