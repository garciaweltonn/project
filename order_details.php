<?php
session_start();
include("layouts/header.php");
include("server/connection.php");

// Verifica se o ID do pedido foi enviado via POST
if (!isset($_POST['order_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Pedido não encontrado.</div></div>";
    include("layouts/footer.php");
    exit;
}

$order_id = $_POST['order_id'];

// Consulta para buscar os itens do pedido com os dados dos produtos
$query = $conn->prepare("
    SELECT p.product_name, p.product_price, oi.product_quantity 
    FROM order_itens oi
    INNER JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");
$query->bind_param("i", $order_id);
$query->execute();
$result = $query->get_result();

// Consulta para obter o status do pedido
$status_query = $conn->prepare("SELECT order_status, order_total FROM orders WHERE order_id = ?");
$status_query->bind_param("i", $order_id);
$status_query->execute();
$status_result = $status_query->get_result();
$order_data = $status_result->fetch_assoc();

?>

<div class="container mt-5">
    <h2 class="mb-4">Detalhes do Pedido #<?= $order_id ?></h2>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td>R$ <?= number_format($row['product_price'], 2, ',', '.') ?></td>
                    <td><?= $row['product_quantity'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <strong>Total do Pedido:</strong> R$ <?= number_format($order_data['order_total'], 2, ',', '.') ?><br>
        <strong>Status:</strong> <?= $order_data['order_status'] == 'not paid' ? '<span class="text-danger">Não Pago</span>' : '<span class="text-success">Pago</span>' ?>
    </div>

    <?php if ($order_data['order_status'] === 'not paid'): ?>
        <form action="payment.php" method="POST" class="mt-3">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <input type="hidden" name="amount" value="<?= $order_data['order_total'] ?>">
            <button type="submit" class="btn btn-success">Pagar agora</button>
        </form>
    <?php endif; ?>
</div>

<?php include("layouts/footer.php"); ?>
