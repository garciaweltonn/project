<?php
session_start();
include("server/connection.php");
include("layouts/header.php");

// Verificação se há um pedido em andamento
if (!isset($_SESSION['order_id']) || empty($_SESSION['order_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Nenhum pedido em andamento.</div></div>";
    include("layouts/footer.php");
    exit;
}

$order_id = $_SESSION['order_id'];

// Verifica se o pedido está como "not paid"
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND order_status = 'not paid'");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Este pedido já foi pago ou não existe.</div></div>";
    include("layouts/footer.php");
    exit;
}

$order = $result->fetch_assoc();
$amount = $order['order_total'];
?>

<div class="container mt-5">
    <h2 class="text-center">Pagamento</h2>
    <p class="text-center">Total do pedido: <strong>R$ <?= number_format($amount, 2, ',', '.') ?></strong></p>

    <div id="paypal-button-container" class="d-flex justify-content-center mt-4"></div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=YOUR_SANDBOX_CLIENT_ID&currency=BRL"></script>
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?= number_format($amount, 2, '.', '') ?>'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(orderData) {
            var transaction = orderData.purchase_units[0].payments.captures[0];
            alert('Pagamento ' + transaction.status + ': ' + transaction.id);
            window.location.href = "server/complete_payment.php?transaction_id=" + transaction.id + "&order_id=<?= $order_id ?>";
        });
    }
}).render('#paypal-button-container');
</script>

<?php include("layouts/footer.php"); ?>
