<?php
include("server/connection.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Produto não encontrado.</div></div>";
    exit;
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Produto não encontrado.</div></div>";
    exit;
}

$product = $result->fetch_assoc();
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
<?php include('layouts/header.php'); ?>

<section class="container mt-5">
    <div class="row">
        <!-- Imagem Principal -->
        <div class="col-md-6">
            <img id="mainImage" src="assets/imgs/<?= htmlspecialchars($product['product_image']) ?>" class="img-fluid mb-3" alt="<?= htmlspecialchars($product['product_name']) ?>">

            <!-- Miniaturas -->
            <div class="d-flex gap-2">
                <?php for ($i = 1; $i <= 4; $i++):
                    $image = $product["product_image" . ($i > 1 ? $i : '')];
                    if (!empty($image)): ?>
                        <img src="assets/imgs/<?= htmlspecialchars($image) ?>" class="img-thumbnail thumb-img" style="width: 80px; cursor: pointer;" onclick="changeImage(this)">
                    <?php endif;
                endfor; ?>
            </div>
        </div>

        <!-- Detalhes do Produto -->
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['product_name']) ?></h2>
            <p><strong>Categoria:</strong> <?= htmlspecialchars($product['product_category']) ?></p>
            <p><strong>Cor:</strong> <?= htmlspecialchars($product['product_color']) ?></p>
            <h4 class="text-success">R$ <?= number_format($product['product_price'], 2, ',', '.') ?></h4>
            <?php if ($product['product_special_offer']): ?>
                <span class="badge bg-warning text-dark">Oferta</span>
            <?php endif; ?>
            <p class="mt-3"><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>

            <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Quantidade:</label>
                    <input type="number" name="quantity" class="form-control w-25" min="1" value="1" required>
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-success">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>
</section>

<?php include('layouts/footer.php'); ?>

<script>
function changeImage(element) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = element.src;
}
</script>
