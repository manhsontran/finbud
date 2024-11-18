<?php
session_start();
header('Content-Type: application/json'); // Đảm bảo trả về JSON

include 'db.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit();
}

// Lấy dữ liệu JSON từ request body
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu đầu vào
if (!isset($data['amount'], $data['category_id'], $data['start_date'], $data['end_date'])) {
    echo json_encode(["status" => "error", "message" => "Missing input data"]);
    exit();
}

// Lấy các giá trị từ dữ liệu đầu vào
$user_id = intval($_SESSION['user_id']);
$amount = floatval($data['amount']);
$category_id = intval($data['category_id']);
$start_date = $data['start_date'];
$end_date = $data['end_date'];

// Kiểm tra tính hợp lệ của ngày tháng
if (strtotime($start_date) === false || strtotime($end_date) === false || strtotime($start_date) > strtotime($end_date)) {
    echo json_encode(["status" => "error", "message" => "Invalid date range"]);
    exit();
}

// Chuẩn bị câu lệnh SQL để thêm ngân sách
$sql = "INSERT INTO Budgets (user_id, category_id, amount, start_date, end_date) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Kiểm tra nếu chuẩn bị câu lệnh thất bại
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Failed to prepare statement", "error" => $conn->error]);
    exit();
}

// Gán tham số và thực thi câu lệnh
$stmt->bind_param("iisss", $user_id, $category_id, $amount, $start_date, $end_date);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Budget added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add budget", "error" => $stmt->error]);
}

// Đóng câu lệnh và kết nối
$stmt->close();
$conn->close();
?>
