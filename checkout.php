<?php
session_start();
include("layouts/header.php");

// Verifica se há itens no carrinho
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: index.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>Finalizar Pedido</h2>
    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['checkout_error']; unset($_SESSION['checkout_error']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="place_order.php">
        <div class="mb-3">
            <label for="uf" class="form-label">UF</label>
            <input type="text" name="uf" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cidade" class="form-label">Cidade</label>
            <input type="text" name="cidade" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" required>
        </div>

        <h4>Total: R$ <?= number_format($_SESSION['total'], 2, ',', '.') ?></h4>

        <button type="submit" class="btn btn-success">Finalizar Pedido</button>
    </form>
</div>

<?php include("layouts/footer.php"); ?>
