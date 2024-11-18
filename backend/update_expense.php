<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

// Lấy dữ liệu từ request
$user_id = $_SESSION['user_id'];
$expense_id = $data['expense_transaction_id'] ?? null;
$amount = $data['amount'] ?? null;
$category_id = $data['category_id'] ?? null;
$sub_category_id = $data['sub_category_id'] ?? null;
$expense_date = $data['expense_date'] ?? null;
$description = $data['description'] ?? null;

// Kiểm tra dữ liệu đầu vào
if (!$expense_id || !$amount || !$category_id || !$expense_date) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

// Cập nhật expense trong cơ sở dữ liệu
$sql = "UPDATE expenses_transaction 
        SET amount = ?, category_id = ?, sub_category_id = ?, expense_date = ?, description = ?
        WHERE expense_transaction_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiissii", $amount, $category_id, $sub_category_id, $expense_date, $description, $expense_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Expense updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
