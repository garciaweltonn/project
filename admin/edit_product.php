<?php
session_start();
require_once("../server/connection.php");

// Verifica autenticação
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

// Verifica se foi passado o ID do produto
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET['id'];

// Atualização de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = floatval(str_replace(',', '.', $_POST['product_price']));
    $offer = isset($_POST['on_offer']) ? 1 : 0;
    $color = $_POST['color'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE products SET product_name=?, description=?, product_price=?, on_offer=?, product_color=?, product_category=? WHERE product_id=?");
    $stmt->bind_param("ssdisii", $name, $description, $price, $offer, $color, $category, $product_id);

    if ($stmt->execute()) {
        $success = "Produto atualizado com sucesso.";
    } else {
        $error = "Erro ao atualizar o produto.";
    }

    $stmt->close();
}

// Buscar dados do produto
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produto não encontrado.";
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-3">
        <h2>Edit Product</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required
                       value="<?= htmlspecialchars($product['product_name']) ?>">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Product Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                          required><?= htmlspecialchars($product['product_description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="product_price" class="form-label">Price</label>
                <input type="text" class="form-control" id="product_price" name="product_price" required
                       value="<?= number_format($product['product_price'], 2, ',', '.') ?>">
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="on_offer" name="on_offer"
                       <?= $product['product_special_offer'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="on_offer">Product on Offer</label>
            </div>

            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color"
                       value="<?= htmlspecialchars($product['product_color']) ?>">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" class="form-control" id="category" name="category"
                       value="<?= htmlspecialchars($product['product_category']) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
