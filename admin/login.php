<?php
session_start();
include("../server/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Verifica se o admin existe
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // Verifica a senha (ajuste para password_hash() caso use hash seguro)
    if ($admin && md5($password) === $admin["admin_password"]) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_name"] = $admin["admin_name"];
        header("Location: index.php");
        exit();
    } else {
        $error = "E-mail ou senha incorretos!";
    }
}
?>

<?php include "header.php"; ?>
<div class="d-flex justify-content-center align-items-center" style="height: calc(100vh - 70px);">
    <form method="POST" class="p-4 border rounded shadow w-25">
        <h3 class="mb-3 text-center">Login</h3>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>

</html>