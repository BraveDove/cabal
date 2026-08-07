<?php
session_start();
require 'config.php';
$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$welcome = "Welcome " . $userID . "!";
// Function to query Cash value for a given user ID
function getCashValue($userID) {
    try {
        // Establish MSSQL connection (assumes it's defined in ShopConfig.php)
        global $connCabalCash; // Assuming the connection is stored in the $conn variable
        
        // Query the CashAccount table
        $query = "SELECT Cash FROM dbo.CashAccount WHERE ID = ?";
        $params = array($userID);
        $stmt = sqlsrv_query($connCabalCash, $query, $params);
        
        if ($stmt === false) {
            throw new Exception("Error while querying the CashAccount table: " . sqlsrv_errors());
        }
        
        // Fetch the Cash value
        if (sqlsrv_fetch($stmt) === false) {
            throw new Exception("No matching record found for the user ID '$userID'");
        }
        
        $cashValue = sqlsrv_get_field($stmt, 0);
        
        // Close the statement
        sqlsrv_free_stmt($stmt);
        
        // Return the Cash value
        return $cashValue;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Usage example

$cashValue = getCashValue($userID);
$_SESSION['cash_value'] = $cashValue;
$myCash = "Cash: " . $cashValue;

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
    <script>
        var user = '<?php echo $userID; ?>';
        var userID = '<?php echo $userID; ?>';
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg  bg-transparent">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">ORIGIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link" aria-current="page" href="dashboard.php">Home</a>
                <a class="nav-link" href="downloads.php">DOWNLOADS</a>
                <a class="nav-link" href="webshop.php">SHOP</a>
                <a class="nav-link" href="convert.php">WEXP CONVERTER</a>
            </div>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">LogOut</a>
            </div>
        </div>
    </div>
</nav>
<div class="userDetails">
    <?php echo $welcome; ?><br>
    <a class = "cashBal"><?php echo $myCash; ?></a>
</div>
<section class="wrapper">
  <div class="top"  id=qCard>WELCOME</div>
  <div class="bottom" id=wCard aria-hidden="true">WELCOME</div>
</section>
<script src="script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>

