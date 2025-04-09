<?php
session_start();
include("server/connection.php");

// Verifica login
if (!isset($_SESSION['logged_in'])) {
    $_SESSION['checkout_error'] = "Você precisa estar logado para finalizar o pedido. <a href='login.php'>Clique aqui para fazer login</a>";
    header("Location: checkout.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $uf = $_POST['uf'];
    $cidade = $_POST['cidade'];
    $endereco = $_POST['endereco'];
    $total = $_SESSION['total'];

    // Inserir pedido na tabela orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, uf, cidade, endereco, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pendente', NOW())");
    $stmt->bind_param("idsss", $user_id, $total, $uf, $cidade, $endereco);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Inserir itens na tabela order_items
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['product_quantity'];
        $price = $item['product_price'];

        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
    }

    // Salvar o ID do pedido na sessão
    $_SESSION['order_id'] = $order_id;

    // Limpar o carrinho
    unset($_SESSION['cart']);
    unset($_SESSION['quantity']);
    unset($_SESSION['total']);

    header("Location: order_success.php");
    exit;
}
?>
