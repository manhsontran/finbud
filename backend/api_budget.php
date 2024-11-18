<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // Kết nối cơ sở dữ liệu

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Fetch budgets or delete a budget
        if (isset($_GET['id'])) {
            // Xóa ngân sách
            $budget_id = intval($_GET['id']);
            $sql = "DELETE FROM Budgets WHERE budget_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $budget_id, $user_id);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Budget deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete budget"]);
            }
            $stmt->close();
        } else {
            // Lấy danh sách ngân sách
            $sql = "SELECT b.budget_id, b.amount, b.start_date, b.end_date, c.category_name, c.category_id 
                    FROM Budgets b 
                    JOIN Categories c ON b.category_id = c.category_id 
                    WHERE b.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $budgets = [];
                while ($row = $result->fetch_assoc()) {
                    $budgets[] = $row;
                }
                echo json_encode($budgets);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to fetch budgets"]);
            }
            $stmt->close();
        }
        break;

    case 'POST': // Add or update a budget
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['budget_id'])) {
            // Cập nhật ngân sách
            if (!isset($data['amount'], $data['category_id'], $data['start_date'], $data['end_date'])) {
                echo json_encode(["status" => "error", "message" => "Missing required data"]);
                exit();
            }
            $budget_id = intval($data['budget_id']);
            $amount = floatval($data['amount']);
            $category_id = intval($data['category_id']);
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];

            $sql = "UPDATE Budgets SET amount = ?, category_id = ?, start_date = ?, end_date = ? WHERE budget_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("dissii", $amount, $category_id, $start_date, $end_date, $budget_id, $user_id);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Budget updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update budget"]);
            }
            $stmt->close();
        } else {
            // Thêm ngân sách
            if (!isset($data['amount'], $data['category_id'], $data['start_date'], $data['end_date'])) {
                echo json_encode(["status" => "error", "message" => "Missing required data"]);
                exit();
            }
            $amount = floatval($data['amount']);
            $category_id = intval($data['category_id']);
            $start_date = $data['start_date'];
            $end_date = $data['end_date'];

            $sql = "INSERT INTO Budgets (user_id, category_id, amount, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsss", $user_id, $category_id, $amount, $start_date, $end_date);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Budget added successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add budget"]);
            }
            $stmt->close();
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}

$conn->close();
?>
