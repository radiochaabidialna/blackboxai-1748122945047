<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$servername = "localhost";
$username = "root";
$password = ""; // Set your MySQL password
$dbname = "aminashopdz";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode($categories);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing name"]);
            exit();
        }
        $name = $conn->real_escape_string($data['name']);
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add category"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id']) || !isset($data['name'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id or name"]);
            exit();
        }
        $id = intval($data['id']);
        $name = $conn->real_escape_string($data['name']);
        $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update category"]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit();
        }
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete category"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>
