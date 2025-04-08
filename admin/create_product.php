<?php
session_start();
include("../server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = floatval($_POST['product_price']);
    $on_offer = isset($_POST['product_offer']) ? 1 : 0;
    $color = trim($_POST['product_color']);
    $category = trim($_POST['product_category']);
    $image_name = "";

    if (!empty($_FILES['product_image']['name'])) {
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_name = basename($_FILES['product_image']['name']);
        $target_dir = "../assets/imgs/";
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($image_tmp, $target_file)) {
            $msg = "Erro ao fazer upload da imagem.";
        }
    }

    if ($name && $description && $price && $category && !$msg) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_price, product_offer, product_color, product_category, product_image) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsiss", $name, $description, $price, $on_offer, $color, $category, $image_name);
        
        if ($stmt->execute()) {
            $msg = "Produto cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar produto.";
        }

        $stmt->close();
    } elseif (!$msg) {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-4">
    <h2 class="mb-4">Add New Product</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Product Name *</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description *</label>
            <textarea name="product_description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price (R$) *</label>
            <input type="number" step="0.01" name="product_price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category *</label>
            <input type="text" name="product_category" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Color</label>
            <input type="text" name="product_color" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="product_offer" id="product_offer">
            <label class="form-check-label" for="product_offer">On Sale?</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" name="product_image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Register Product</button>
    </form>
</div>

