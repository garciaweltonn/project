<?php
session_start();
include("../server/connection.php");

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit();
}

// Buscar todos os usuários
$query = "SELECT * FROM users ORDER BY user_name ASC";
$result = $conn->query($query);
?>

<?php include "header.php"; ?>

<div class="d-flex">
    <?php include "sidemenu.php"; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Usuários Cadastrados</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['user_id'] ?></td>
                            <td><?= htmlspecialchars($user['user_name']) ?></td>
                            <td><?= htmlspecialchars($user['user_email']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
