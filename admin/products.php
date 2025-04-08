<?php
session_start();
require_once("../server/connection.php");

// Verifica autenticação
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

// Paginação
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total de produtos
$total_query = "SELECT COUNT(*) AS total FROM products";
$total_result = $conn->query($total_query);
$total_products = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Produtos com paginação
$query = "SELECT * FROM products ORDER BY product_id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-3">
        <h2 class="mb-4">List Products</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Offer</th>
                        <th>Color</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $product['product_id'] ?></td>
                        <td>
                            <?php if (!empty($product['product_image'])): ?>
                            <img src="../uploads/products/<?= htmlspecialchars($product['product_image']) ?>" alt="Image Product" width="80">
                            <?php else: ?>
                            <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td class="text-end">R$ <?= number_format($product['product_price'], 2, ',', '.') ?></td>
                        <td class="text-end"><?= $product['product_special_offer'] ?>%</td>
                        <td><?= htmlspecialchars($product['product_color']) ?></td>
                        <td class="text-center">
                            <a href="edit_product_images.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-secondary mb-1">
                                <i class="fas fa-image"></i> Edit Images
                            </a>
                            <a href="edit_product.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-warning mb-1">
                                <i class="fas fa-edit"></i> Edit Products
                            </a>
                            <a href="delete_product.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-danger mb-1"
                                onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Próximo</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
