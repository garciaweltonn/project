<?php
session_start();
include("connection.php");

if (isset($_GET['transaction_id']) && isset($_GET['order_id'])) {
    $transaction_id = $_GET['transaction_id'];
    $order_id = $_GET['order_id'];

    // Atualiza status do pedido
    $update = $conn->prepare("UPDATE orders SET order_status = 'paid' WHERE order_id = ?");
    $update->bind_param("i", $order_id);
    $update->execute();

    // Registra pagamento
    $insert = $conn->prepare("INSERT INTO payments (order_id, transaction_id, payment_date) VALUES (?, ?, NOW())");
    $insert->bind_param("is", $order_id, $transaction_id);
    $insert->execute();

    $_SESSION['cart'] = [];
    $_SESSION['quantity'] = 0;
    $_SESSION['total'] = 0;

    echo "<div class='container mt-5'><div class='alert alert-success'>Pagamento confirmado com sucesso!</div></div>";
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Dados inválidos para confirmação de pagamento.</div></div>";
}
?>
