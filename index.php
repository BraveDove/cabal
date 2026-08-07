<?php
session_start();
require 'config.php';
$loginError = '';
$loginSuccess = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $tsql = "SELECT ID, UserNum, Password FROM Account.dbo.cabal_auth_table WHERE ID = ? AND PWDCOMPARE(?, Password) = 1";

    $params = array($username, $password);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $stmt = sqlsrv_query($connAccount, $tsql, $params, $options);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row_count = sqlsrv_num_rows($stmt);

    if (empty($username) || empty($password)) {
        $loginError = 'Login Failed! Please enter username and password.';
    } elseif ($row_count == 0) {
        $loginError = 'Invalid username or password. Please try again.';
    }
    // If all is OK
    else {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['usernum'] = $row['UserNum'];
        $loginSuccess = 'Login successful!';
        header("Location: dashboard.php");
        exit;
    }

    sqlsrv_free_stmt($stmt);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ωrigin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg  bg-transparent">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ORIGIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" aria-current="page" href="index.php">Home</a>
                <a class="nav-link" href="downloads.php">DOWNLOADS</a>
                <a class="nav-link" href="register.php">REGISTER</a>
                
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" style="margin-right: 20px; "href="login.php">Login</a>
                </div>
        </div>
    </div>
</nav>
<section class="wrapper">
  <div class="top" id=yCard>Ωrigin</div>
  <div class="bottom" id=xCard aria-hidden="true">Ωrigin</div>
</section>
<footer style="margin-left: 90px;">EST 2023</footer>
<script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>

