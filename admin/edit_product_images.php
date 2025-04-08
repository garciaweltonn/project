<?php
session_start();
require_once("../server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int) $_GET['id'];

// Diretório onde as imagens serão salvas
$imageDir = "../uploads/products/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    for ($i = 1; $i <= 4; $i++) {
        $fieldName = $i === 1 ? "product_image" : "product_image$i";

        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES[$fieldName]["tmp_name"];
            $extension = pathinfo($_FILES[$fieldName]["name"], PATHINFO_EXTENSION);
            $newName = "product_{$product_id}_img{$i}." . $extension;
            move_uploaded_file($tmp_name, $imageDir . $newName);

            // Atualiza a imagem no banco
            $stmt = $conn->prepare("UPDATE products SET $fieldName = ? WHERE product_id = ?");
            $stmt->bind_param("si", $newName, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $success = "Images updated successfully!";
}

// Buscar imagens atuais
$stmt = $conn->prepare("SELECT product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-3">
        <h2>Edit Images Product #<?= $product_id ?></h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): 
                    $fieldName = $i === 1 ? "product_image" : "product_image$i";
                    $imagePath = $product[$fieldName] ?? '';
                ?>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Image <?= $i ?></label><br>
                    <?php if (!empty($imagePath)): ?>
                        <img src="<?= $imageDir . $imagePath ?>" class="img-fluid mb-2" alt="Image <?= $i ?>">
                    <?php else: ?>
                        <div class="text-muted mb-2">No image</div>
                    <?php endif; ?>
                    <input type="file" name="<?= $fieldName ?>" class="form-control">
                </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn btn-primary">Save Images</button>
            <a href="products.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
