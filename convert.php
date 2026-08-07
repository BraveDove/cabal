<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    include('war.php');
    $userID = $_SESSION['user_id'];
    $usernum = $_SESSION['usernum'];
    $cashValue = $_SESSION['cash_value'];
    $wexpValue = $_SESSION['WarExp'];
    $confirm = "";
    $outputs = "";
    $characterIdx = $usernum * 8;
    // Establish the database connections
    global $connServer01;
    global $connCabalCash;




    if (isset($_POST['convert'])) {
        // Convert PlayTime to Cash and deduct from PlayTime
        $cash = $wexpValue;
        $wexpValue = 0;

        // Begin a transaction to ensure data consistency across databases
        sqlsrv_begin_transaction($connServer01);
        sqlsrv_begin_transaction($connCabalCash);

        try {
            // Update the CashAccount table in the shop database
            $updateSql = "UPDATE [dbo].[CashAccount] SET Cash = Cash + ? WHERE ID = ?";
            $updateParams = array($cash, $userID);
            $updateStmt = sqlsrv_query($connCabalCash, $updateSql, $updateParams);
            
            if ($updateStmt === false) {
                sqlsrv_rollback($connCabalCash);
                throw new Exception("Error updating CashAccount table");
            }

            // Update the cabal_auth_table with the deducted PlayTime in the cabal database
            $deductSql = "UPDATE [dbo].[cabal_WarExp_Table] SET WarExp = ? WHERE characterIdx = ?";
            $deductParams = array($wexpValue, $characterIdx);
            $deductStmt = sqlsrv_query($connServer01, $deductSql, $deductParams);

            if ($deductStmt === false) {
                sqlsrv_rollback($connServer01);
                throw new Exception("Error updating");
            }

            // Update the cabal_WarExp_Table to 0
            for ($i = 0; $i < 8; $i++) {
                $currentCharacterIdx = $characterIdx + $i;
            
                $resetSql = "UPDATE [dbo].[cabal_WarExp_Table] SET WarExp = 0 WHERE characterIdx = ?";
                $resetParams = array($currentCharacterIdx);
                $resetStmt = sqlsrv_query($connServer01, $resetSql, $resetParams);
            
                if ($resetStmt === false) {
                    sqlsrv_rollback($connServer01);
                    throw new Exception("Error!");
                }

            }
        
            // Commit the changes if everything is successful
            sqlsrv_commit($connServer01);
            sqlsrv_commit($connCabalCash);

            $confirm = "<div>Conversion complete.</div>";
        } catch (Exception $e) {
            // Rollback the changes if an error occurred
            sqlsrv_rollback($connServer01);
            sqlsrv_rollback($connCabalCash);
            die($e->getMessage());
        }

        sqlsrv_free_stmt($updateStmt);
        sqlsrv_free_stmt($deductStmt);
        sqlsrv_free_stmt($resetStmt);
    }

    sqlsrv_close($connServer01);
    sqlsrv_close($connCabalCash);
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
                <a class="nav-link" aria-current="page" href="#">Home</a>
                <a class="nav-link" href="#">DOWNLOADS</a>
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
    <a class = "cashBal"><?php echo $myCash; ?></a><br>
    <a class = "cashBal">WEXP: <?php echo $wexpValue; ?></a>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card mt-5" id="xCard">
                <div class="card-body">
                    <h1 class="card-title">WEXP Converter</h1>
                        <form method="POST" action="">
                              <h6>(1 wexp = 1 Cash)</h6>
                               <button type="submit" class="btn" name="convert">Convert</button>    
                                <div style="font-family: 'Caesar Dressing', sans-serif; font-size: 18px; color: rgb(0, 255, 34);"><br><?php echo "$confirm"; ?></div>
                        </form>
                </div>
            </div>
       </div>
    </div>
 </div>
       
     
<script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>