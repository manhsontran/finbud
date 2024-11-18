<?php
header('Content-Type: application/json');
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$amount = $data['amount'] ?? null;
$category_id = $data['category_id'] ?? null;
$expense_date = $data['expense_date'] ?? null;
$description = $data['description'] ?? null;
$sub_category_id = $data['sub_category_id'] ?? null;

// Kiểm tra dữ liệu
if (!$amount || !$category_id || !$expense_date) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Chèn vào cơ sở dữ liệu
$sql = "INSERT INTO expenses_transaction (user_id, category_id, sub_category_id, amount, expense_date, description) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiidss", $user_id, $category_id, $sub_category_id, $amount, $expense_date, $description);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Expense added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
