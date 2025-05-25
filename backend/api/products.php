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
        // Support optional filtering by category_id
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
        if ($category_id) {
            $stmt = $conn->prepare("SELECT p.*, c.name_fr as category_name_fr, c.name_ar as category_name_ar FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ?");
            $stmt->bind_param("i", $category_id);
        } else {
            $stmt = $conn->prepare("SELECT p.*, c.name_fr as category_name_fr, c.name_ar as category_name_ar FROM products p JOIN categories c ON p.category_id = c.id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            // Decode JSON fields
            $row['images'] = json_decode($row['images'], true) ?: [];
            $row['sizes'] = json_decode($row['sizes'], true) ?: [];
            $row['colors'] = json_decode($row['colors'], true) ?: [];
            $products[] = $row;
        }
        echo json_encode($products);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name_fr'], $data['name_ar'], $data['price'], $data['category_id'], $data['stock'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }
        $name_fr = $conn->real_escape_string($data['name_fr']);
        $name_ar = $conn->real_escape_string($data['name_ar']);
        $description_fr = isset($data['description_fr']) ? $conn->real_escape_string($data['description_fr']) : '';
        $description_ar = isset($data['description_ar']) ? $conn->real_escape_string($data['description_ar']) : '';
        $price = floatval($data['price']);
        $category_id = intval($data['category_id']);
        $stock = intval($data['stock']);
        $promotion = !empty($data['promotion']) ? 1 : 0;
        $new = !empty($data['new']) ? 1 : 0;
        $images = isset($data['images']) ? json_encode($data['images']) : json_encode([]);
        $sizes = isset($data['sizes']) ? json_encode($data['sizes']) : json_encode([]);
        $colors = isset($data['colors']) ? json_encode($data['colors']) : json_encode([]);

        $stmt = $conn->prepare("INSERT INTO products (name_fr, name_ar, description_fr, description_ar, price, category_id, stock, promotion, new, images, sizes, colors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdiiiisss", $name_fr, $name_ar, $description_fr, $description_ar, $price, $category_id, $stock, $promotion, $new, $images, $sizes, $colors);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add product: " . $stmt->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing product id"]);
            exit();
        }
        $id = intval($data['id']);
        $name_fr = isset($data['name_fr']) ? $conn->real_escape_string($data['name_fr']) : null;
        $name_ar = isset($data['name_ar']) ? $conn->real_escape_string($data['name_ar']) : null;
        $description_fr = isset($data['description_fr']) ? $conn->real_escape_string($data['description_fr']) : null;
        $description_ar = isset($data['description_ar']) ? $conn->real_escape_string($data['description_ar']) : null;
        $price = isset($data['price']) ? floatval($data['price']) : null;
        $category_id = isset($data['category_id']) ? intval($data['category_id']) : null;
        $stock = isset($data['stock']) ? intval($data['stock']) : null;
        $promotion = isset($data['promotion']) ? ($data['promotion'] ? 1 : 0) : null;
        $new = isset($data['new']) ? ($data['new'] ? 1 : 0) : null;
        $images = isset($data['images']) ? json_encode($data['images']) : null;
        $sizes = isset($data['sizes']) ? json_encode($data['sizes']) : null;
        $colors = isset($data['colors']) ? json_encode($data['colors']) : null;

        // Build dynamic update query
        $fields = [];
        $params = [];
        $types = '';

        if ($name_fr !== null) { $fields[] = "name_fr=?"; $params[] = $name_fr; $types .= 's'; }
        if ($name_ar !== null) { $fields[] = "name_ar=?"; $params[] = $name_ar; $types .= 's'; }
        if ($description_fr !== null) { $fields[] = "description_fr=?"; $params[] = $description_fr; $types .= 's'; }
        if ($description_ar !== null) { $fields[] = "description_ar=?"; $params[] = $description_ar; $types .= 's'; }
        if ($price !== null) { $fields[] = "price=?"; $params[] = $price; $types .= 'd'; }
        if ($category_id !== null) { $fields[] = "category_id=?"; $params[] = $category_id; $types .= 'i'; }
        if ($stock !== null) { $fields[] = "stock=?"; $params[] = $stock; $types .= 'i'; }
        if ($promotion !== null) { $fields[] = "promotion=?"; $params[] = $promotion; $types .= 'i'; }
        if ($new !== null) { $fields[] = "new=?"; $params[] = $new; $types .= 'i'; }
        if ($images !== null) { $fields[] = "images=?"; $params[] = $images; $types .= 's'; }
        if ($sizes !== null) { $fields[] = "sizes=?"; $params[] = $sizes; $types .= 's'; }
        if ($colors !== null) { $fields[] = "colors=?"; $params[] = $colors; $types .= 's'; }

        if (count($fields) === 0) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit();
        }

        $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id=?";
        $params[] = $id;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update product: " . $stmt->error]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing product id"]);
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
