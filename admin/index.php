<?php
session_start();
include("../server/connection.php"); 

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

$limit = 5; 
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_query = "SELECT COUNT(*) AS total FROM orders";
$total_result = $conn->query($total_query);
$total_orders = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);

$query = "SELECT orders.*, users.user_name FROM orders 
          JOIN users ON orders.user_id = users.user_id 
          ORDER BY order_date DESC 
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

?>


<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-3">
        <h2 class="mb-4">Order List</h2>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Id</th>
                        <th>Customer</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>User Id</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= $row['user_name'] ?></td>
                        <td>R$ <?= number_format($row['order_cost'], 2, ',', '.') ?></td>
                        <td><?= $row['order_status'] ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($row['order_date'])) ?></td>
                        <td><?= $row['user_id'] ?></td>
                        <td>
                            <a href="edit_order.php?id=<?= $row['order_id'] ?>"
                                class="btn btn-warning btn-sm">Editar</a>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['order_id'] ?>"> Excluir</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
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
<script>
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', async () => {
        const orderId = button.dataset.id;

        if (confirm(`Tem certeza que deseja excluir o pedido #${orderId}?`)) {
            try {
                const response = await fetch('delete_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${orderId}`
                });

                const result = await response.json();

                if (result.success) {
                    // Remove a linha da tabela
                    button.closest('tr').remove();
                    alert(result.message);
                } else {
                    alert(result.message);
                }

            } catch (error) {
                alert("Erro na requisição.");
                console.error(error);
            }
        }
    });
});
</script>

</body>

</html>