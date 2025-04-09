<?php
session_start();
include("layouts/header.php");

// Inicializa o carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Atualiza item
if (isset($_POST['update_cart'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = intval($_POST['product_quantity']);

    if ($new_quantity > 0) {
        $_SESSION['cart'][$product_id]['product_quantity'] = $new_quantity;
    }
}

// Remove item
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
}

// Função para calcular total
function calcularTotal()
{
    $total = 0;
    $quantity = 0;

    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['product_price'] * $item['product_quantity'];
        $total += $subtotal;
        $quantity += $item['product_quantity'];
    }

    $_SESSION['total'] = $total;
    $_SESSION['quantity'] = $quantity;
    return $total;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
</head>

<body>

<div class="container mt-5">
    <h2 class="mb-4">Carrinho de Compras</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning">Seu carrinho está vazio.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td>
                            <img src="assets/imgs/<?= htmlspecialchars($item['product_image']) ?>" width="50" height="50">
                            <?= htmlspecialchars($item['product_name']) ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="number" name="product_quantity" value="<?= $item['product_quantity'] ?>" min="1" class="form-control me-2" style="width: 80px;">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" name="update_cart" class="btn btn-sm btn-primary">Atualizar</button>
                            </form>
                        </td>
                        <td>R$ <?= number_format($item['product_price'] * $item['product_quantity'], 2, ',', '.') ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" name="remove_item" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
            <h4>Total: R$ <?= number_format(calcularTotal(), 2, ',', '.') ?></h4>
        </div>
    <?php endif; ?>
</div>

<?php include("layouts/footer.php"); ?>
