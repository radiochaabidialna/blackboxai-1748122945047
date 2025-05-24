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
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.category_id, p.stock, p.promotion, p.new, p.images, p.sizes, p.colors, p.date_added, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode(["error" => "Product not found"]);
                exit();
            }
            $product = $result->fetch_assoc();
            $product['images'] = json_decode($product['images']);
            $product['sizes'] = json_decode($product['sizes']);
            $product['colors'] = json_decode($product['colors']);
            echo json_encode($product);
        } else {
            $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.stock, p.promotion, p.new, p.images, p.sizes, p.colors, p.date_added, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
            $result = $conn->query($sql);
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $row['images'] = json_decode($row['images']);
                $row['sizes'] = json_decode($row['sizes']);
                $row['colors'] = json_decode($row['colors']);
                $products[] = $row;
            }
            echo json_encode($products);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid JSON"]);
            exit();
        }
        $name = $conn->real_escape_string($data['name']);
        $description = $conn->real_escape_string($data['description'] ?? '');
        $price = floatval($data['price']);
        $category_id = intval($data['category_id']);
        $stock = intval($data['stock']);
        $promotion = !empty($data['promotion']) ? 1 : 0;
        $new = !empty($data['new']) ? 1 : 0;
        $images = json_encode($data['images'] ?? []);
        $sizes = json_encode($data['sizes'] ?? []);
        $colors = json_encode($data['colors'] ?? []);

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, stock, promotion, new, images, sizes, colors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiiiisss", $name, $description, $price, $category_id, $stock, $promotion, $new, $images, $sizes, $colors);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add product"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid JSON or missing id"]);
            exit();
        }
        $id = intval($data['id']);
        $name = $conn->real_escape_string($data['name']);
        $description = $conn->real_escape_string($data['description'] ?? '');
        $price = floatval($data['price']);
        $category_id = intval($data['category_id']);
        $stock = intval($data['stock']);
        $promotion = !empty($data['promotion']) ? 1 : 0;
        $new = !empty($data['new']) ? 1 : 0;
        $images = json_encode($data['images'] ?? []);
        $sizes = json_encode($data['sizes'] ?? []);
        $colors = json_encode($data['colors'] ?? []);

        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, stock=?, promotion=?, new=?, images=?, sizes=?, colors=? WHERE id=?");
        $stmt->bind_param("ssdiiiisssis", $name, $description, $price, $category_id, $stock, $promotion, $new, $images, $sizes, $colors, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update product"]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing id"]);
            exit();
        }
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete product"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>
