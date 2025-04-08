<?php
session_start();
require_once("../server/connection.php");

header('Content-Type: application/json');

if (!isset($_SESSION["admin_logged_in"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Não autorizado."]);
    exit();
}

$orderId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$orderId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID inválido."]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $orderId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pedido excluído com sucesso."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao excluir pedido."]);
}
