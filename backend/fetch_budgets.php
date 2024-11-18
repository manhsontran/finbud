<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn ngân sách của người dùng
$sql = "SELECT b.budget_id, b.amount, c.category_name, b.start_date, b.end_date 
        FROM Budgets b
        JOIN Categories c ON b.category_id = c.category_id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $budgets = [];

    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row; // Thêm từng ngân sách vào mảng
    }
    echo json_encode($budgets); // Trả về JSON
} else {
    echo json_encode([]); // Nếu lỗi, trả về mảng rỗng
}

$stmt->close();
$conn->close();
?>
