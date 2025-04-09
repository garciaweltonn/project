<?php
$cart_quantity = isset($_SESSION['quantity']) ? $_SESSION['quantity'] : 0;
$cart_total = isset($_SESSION['total']) ? number_format($_SESSION['total'], 2, ',', '.') : '0,00';
?>

<nav class="navbar navbar-expand-lg bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="assets/imgs/logo.png" width="100px" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Fale Conosco</a></li>
                <li class="nav-item position-relative">
                    <a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i>
                        <?php if ($cart_quantity > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cart_quantity ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item d-none d-lg-block align-self-center ms-2">
                    <span class="nav-link disabled text-muted">R$ <?= $cart_total ?></span>
                </li>
                <li class="nav-item"><a class="nav-link" href="login.php"><i class="fa fa-user"></i></a></li>
            </ul>
        </div>
    </div>
</nav>