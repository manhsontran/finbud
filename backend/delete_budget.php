<?php
session_start();
header('Content-Type: application/json'); // Đảm bảo phản hồi JSON

include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit();
}

// Kiểm tra nếu `id` được gửi qua GET
if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Budget ID is missing"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$budget_id = intval($_GET['id']);

// Xóa ngân sách dựa trên `budget_id` và `user_id`
$sql = "DELETE FROM Budgets WHERE budget_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $budget_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Budget deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete budget"]);
}

$stmt->close();
$conn->close();
?>
