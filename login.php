<?php
session_start();

$admin_user = "petrov";
$admin_pass = "viosbaba31";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Hatalı giriş!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center vh-100">
<div class="card p-4 bg-secondary shadow-lg" style="width: 350px;">
    <h3 class="text-center">Admin Giriş</h3>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Kullanıcı adı" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Şifre" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Giriş</button>
    </form>
</div>
</body>
</html>
