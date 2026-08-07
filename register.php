<?php
include('config.php');
$ip = $_SERVER['REMOTE_ADDR'];
$message ="";
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $reppassword = $_POST['reppassword'];
    $email = $_POST['email'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $error = '';
    // Check for already existing username
    $check_username_query = "SELECT * FROM cabal_auth_table WHERE ID = ?";
    $params = array($username);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($connAccount, $check_username_query, $params, $options);
    $check_username = sqlsrv_num_rows($stmt);

    if ($check_username != 0) {
        $message = "<font color='red'><b>This username is used by another player</b></font> <br>";
        $error = 'Yes';
    }

    // Check repeat password
    if ($password != $reppassword) {
        $message =   "<font color='red'><b>Your Passwords must be the same</b></font> <br>";
        $error = 'Yes';
    }

    // Check for empty fields
    if (empty($username) || empty($password) || empty($reppassword) || empty($email) || empty($question) || empty($answer)) {
        $message =  "<font color='red'><b>You cannot leave empty fields</b></font> <br>";
        $error = 'Yes';
    }

    // Register if all is OK
    if ($error != 'Yes') {
        $register_query = "EXECUTE Account.dbo.cabal_tool_registerAccount_web ?, ?, ?, ?, ?, ?";
        $params = array($username, $password, $email, $question, $answer, $ip);
        $stmt = sqlsrv_query($connAccount, $register_query, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $message = "<font color='green'><b>Account $username is registered successfully.</b></font><br>";
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card mt-5" id="xCard">
                <div class="card-body">
                    <h1 class="card-title">Register</h1>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" id="username" name="username" required placeholder="username">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" required placeholder="password">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="reppassword" name="reppassword" required placeholder="repeat password">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" required placeholder="email">
                </div>
                <div class="mb-3">
                <select class="form-control" name="question" required>
                    <option value="" disabled selected>Select a Secret Question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What is the name of your first pet?">What is the name of your first pet?</option>
                    <option value="What is your favorite book?">What is your favorite book?</option>
                    <option value="What city were you born in?">What city were you born in?</option>
                </select>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="answer" name="answer" required placeholder="secret answer">
                </div>
                <button type="submit" class="btn btn-primary" name="register">Register</button>
                <?php
                if (!empty($message)) {
                    echo '<div class="alert">' . $message . '</div>';
                }
                ?>
            </form>
        </div>
    </div>
</div>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>

