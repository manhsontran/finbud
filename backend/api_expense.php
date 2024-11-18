<?php
session_start();
include 'db.php'; // Kết nối cơ sở dữ liệu

header('Content-Type: application/json');

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true); // Lấy dữ liệu từ JSON
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'add':
        // Thêm Expense
        $amount = $data['amount'] ?? null;
        $category_id = $data['category_id'] ?? null;
        $sub_category_id = $data['sub_category_id'] ?? null;
        $expense_date = $data['expense_date'] ?? null;
        $description = $data['description'] ?? null;

        if (!$amount || !$category_id || !$expense_date) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit();
        }

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
        break;

    case 'delete':
        // Xóa Expense
        $expense_id = $data['expense_transaction_id'] ?? null;

        if (!$expense_id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing expense ID']);
            exit();
        }

        $sql = "DELETE FROM expenses_transaction WHERE expense_transaction_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $expense_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Expense deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }

        $stmt->close();
        break;

    case 'update':
        // Cập nhật Expense
        $expense_id = $data['expense_transaction_id'] ?? null;
        $amount = $data['amount'] ?? null;
        $category_id = $data['category_id'] ?? null;
        $sub_category_id = $data['sub_category_id'] ?? null;
        $expense_date = $data['expense_date'] ?? null;
        $description = $data['description'] ?? null;

        if (!$expense_id || !$amount || !$category_id || !$expense_date) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit();
        }

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
        break;

    case 'fetch':
        // Lấy danh sách Expense
        $sql = "SELECT et.expense_transaction_id, et.amount, et.expense_date, et.description, 
                       c.category_name, sc.sub_category_name 
                FROM expenses_transaction et
                JOIN Categories c ON et.category_id = c.category_id
                LEFT JOIN sub_category sc ON et.sub_category_id = sc.sub_category_id
                WHERE et.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $expenses = [];
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }

        echo json_encode(['status' => 'success', 'expenses' => $expenses]);

        $stmt->close();
        break;

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>
