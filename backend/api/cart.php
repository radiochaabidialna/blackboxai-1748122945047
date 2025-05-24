<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$method = $_SERVER['REQUEST_METHOD'];

function findCartItemIndex($cart, $id, $size = null, $color = null) {
    foreach ($cart as $index => $item) {
        if (isset($item['id']) && $item['id'] == $id) {
            // Check size and color if provided
            if (($size === null || (isset($item['size']) && $item['size'] === $size)) &&
                ($color === null || (isset($item['color']) && $item['color'] === $color))) {
                return $index;
            }
        }
    }
    return -1;
}

if ($method === 'GET') {
    echo json_encode($_SESSION['cart']);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id']) || !isset($data['quantity'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit();
    }
    $id = $data['id'];
    $quantity = max(1, intval($data['quantity']));
    $size = isset($data['size']) ? $data['size'] : null;
    $color = isset($data['color']) ? $data['color'] : null;

    $index = findCartItemIndex($_SESSION['cart'], $id, $size, $color);
    if ($index >= 0) {
        // Update existing item quantity and attributes
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        if ($size !== null) {
            $_SESSION['cart'][$index]['size'] = $size;
        }
        if ($color !== null) {
            $_SESSION['cart'][$index]['color'] = $color;
        }
    } else {
        // Add new item
        $item = [
            'id' => $id,
            'quantity' => $quantity,
        ];
        if ($size !== null) {
            $item['size'] = $size;
        }
        if ($color !== null) {
            $item['color'] = $color;
        }
        $_SESSION['cart'][] = $item;
    }
    echo json_encode(["success" => true]);
} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete_vars);
    if (!isset($delete_vars['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit();
    }
    $id = $delete_vars['id'];
    $size = isset($delete_vars['size']) ? $delete_vars['size'] : null;
    $color = isset($delete_vars['color']) ? $delete_vars['color'] : null;

    $index = findCartItemIndex($_SESSION['cart'], $id, $size, $color);
    if ($index >= 0) {
        array_splice($_SESSION['cart'], $index, 1);
        echo json_encode(["success" => true]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Item not found in cart"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>
