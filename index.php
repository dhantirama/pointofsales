<?php
session_start();
session_regenerate_id();
require_once "config/koneksi.php";
if (isset($_POST['login'])) {
    $email  = $_POST['email'];
    $password = $_POST['password'];

    $selectLogin = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");
    if (mysqli_num_rows(($selectLogin)) > 0) {
        $row = mysqli_fetch_assoc($selectLogin);

        if ($row['email'] == $email && $row['password'] == $password) {
            $_SESSION['EMAILNYABRO'] = $row['email'];
            $_SESSION['NAMALENGKAPNYA'] = $row['nama_lengkap'];
            header("location: kasir.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/login.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="form-container" id="login-form">
            <h1>Login</h1>
            <form method="post">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login"><i class="bi bi-box-arrow-in-right"></i>Login</button>
            </form>
            <p>Don't have an account? <a href="#" id="signup-link">Sign up</a></p>
        </div>

        <div class="form-container" id="signup-form" style="display: none;">
            <h1>Sign Up</h1>
            <form>
                <label for="new-username">Username</label>
                <input type="text" id="new-username" name="new-username" required>
                <label for="new-email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="new-password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Sign Up</button>
            </form>
            <p>Already have an account? <a href="#" id="login-link">Login</a></p>
        </div>
    </div>
</body>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>