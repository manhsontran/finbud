<?php
session_start();
header('Content-Type: application/json'); // Đảm bảo phản hồi JSON
include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit();
}

// Lấy dữ liệu từ request body
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu
if (!isset($data['budget_id'], $data['amount'], $data['category_id'], $data['start_date'], $data['end_date'])) {
    echo json_encode(["status" => "error", "message" => "Missing required data"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$budget_id = intval($data['budget_id']);
$amount = floatval($data['amount']);
$category_id = intval($data['category_id']);
$start_date = $data['start_date'];
$end_date = $data['end_date'];

// Cập nhật ngân sách
$sql = "UPDATE Budgets SET amount = ?, category_id = ?, start_date = ?, end_date = ? WHERE budget_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dissii", $amount, $category_id, $start_date, $end_date, $budget_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Budget updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update budget"]);
}

$stmt->close();
$conn->close();
?>
