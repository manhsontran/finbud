<?php
include 'db.php';
header('Content-Type: application/json');

// Kiểm tra xem category_id có tồn tại hay không
if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    echo json_encode(['error' => 'Invalid category_id']);
    exit();
}

$category_id = intval($_GET['category_id']);

// Chuẩn bị truy vấn SQL
$sql = "SELECT sub_category_id, sub_category_name FROM sub_category WHERE category_id = ?";
$stmt = $conn->prepare($sql);

// Kiểm tra nếu chuẩn bị câu truy vấn thất bại
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit();
}

$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

$sub_categories = [];

// Lấy dữ liệu
while ($row = $result->fetch_assoc()) {
    $sub_categories[] = $row;
}

// Đóng statement và kết nối
$stmt->close();
$conn->close();

// Trả về JSON danh sách sub-categories
echo json_encode($sub_categories);
?>
