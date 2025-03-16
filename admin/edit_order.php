<?php
include('../server/connection.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

$query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['order_status'];

    $update_query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        header("Location: index.php?success=Pedido atualizado");
    } else {
        $error = "Erro ao atualizar pedido.";
    }
}
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>


    <div class="container mt-5">
        <h2>Editar Pedido #<?= $order_id ?></h2>

        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Status do Pedido</label>
                <select name="order_status" class="form-control" required>
                    <option value="on_hold" <?= ($order['order_status'] == 'on_hold') ? 'selected' : '' ?>>Em análise
                    </option>
                    <option value="paid" <?= ($order['order_status'] == 'paid') ? 'selected' : '' ?>>Pago</option>
                    <option value="shipped" <?= ($order['order_status'] == 'shipped') ? 'selected' : '' ?>>Enviado
                    </option>
                    <option value="delivered" <?= ($order['order_status'] == 'delivered') ? 'selected' : '' ?>>Entregue
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
</body>

</html>