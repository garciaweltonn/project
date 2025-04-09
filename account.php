<?php
session_start();
include("server/connection.php");

// Verifica se está logado
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Verifica se o parâmetro user_id foi passado
if (!isset($_GET['user_id']) || $_GET['user_id'] != $_SESSION['user_id']) {
    echo "Acesso não autorizado.";
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$pass_msg = '';

// Busca dados do usuário
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Atualiza senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['current_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Busca senha atual
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $data = $stmt->fetch();

    if (!password_verify($current, $data['password'])) {
        $pass_msg = "Senha atual incorreta.";
    } elseif ($new !== $confirm) {
        $pass_msg = "A nova senha e a confirmação não coincidem.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed, $user_id]);
        $pass_msg = "Senha atualizada com sucesso.";
    }
}

// Busca pedidos do usuário
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$order_list = $orders->fetchAll();
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

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Minha Conta</h2>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5>Informações do Usuário</h5>
                <p><strong>Nome:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3">
                <h5>Alterar Senha</h5>
                <?php if ($pass_msg): ?>
                    <div class="alert alert-info"><?= $pass_msg ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-2">
                        <label>Senha atual</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Nova senha</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Confirmar nova senha</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Atualizar Senha</button>
                </form>
            </div>
        </div>
    </div>

    <h4 class="mt-4">Meus Pedidos</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID do Pedido</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Valor</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($order_list) > 0): ?>
                    <?php foreach ($order_list as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= ucfirst($order['status']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>R$ <?= number_format($order['total'], 2, ',', '.') ?></td>
                            <td><a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Nenhum pedido encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('layouts/footer.php'); ?>
