<?php
session_start();
require_once("../server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET['id'];

$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Produto excluído com sucesso.";
} else {
    $_SESSION['message'] = "Erro ao excluir o produto.";
}

$stmt->close();

header("Location: products.php");
exit();
