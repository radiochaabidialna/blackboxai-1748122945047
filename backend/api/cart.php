<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

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
        $sql = "SELECT * FROM cart";
        $result = $conn->query($sql);
        $cart = [];
        while ($row = $result->fetch_assoc()) {
            $cart[] = $row;
        }
        echo json_encode($cart);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id']) || !isset($data['size']) || !isset($data['color']) || !isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }
        $id = intval($data['id']);
        $size = $conn->real_escape_string($data['size']);
        $color = $conn->real_escape_string($data['color']);
        $quantity = intval($data['quantity']);

        // Check if item already in cart
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE product_id=? AND size=? AND color=?");
        $stmt->bind_param("iss", $id, $size, $color);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($existing_quantity);
            $stmt->fetch();
            $new_quantity = $existing_quantity + $quantity;
            $update_stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE product_id=? AND size=? AND color=?");
            $update_stmt->bind_param("iiss", $new_quantity, $id, $size, $color);
            if ($update_stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update cart"]);
            }
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO cart (product_id, size, color, quantity) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("issi", $id, $size, $color, $quantity);
            if ($insert_stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to add to cart"]);
            }
        }
        break;

    case 'DELETE':
        // Require id, size, and color to delete a specific cart item
        if (!isset($_GET['id']) || !isset($_GET['size']) || !isset($_GET['color'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id, size or color"]);
            exit();
        }
        $id = intval($_GET['id']);
        $size = $conn->real_escape_string($_GET['size']);
        $color = $conn->real_escape_string($_GET['color']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE product_id=? AND size=? AND color=?");
        $stmt->bind_param("iss", $id, $size, $color);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete from cart"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>
