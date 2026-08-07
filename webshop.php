<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id'];
$usernum = $_SESSION['usernum'];
$purchase = "";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the selected item from the form
    $itemSelection = $_POST['itemSelection'];

    // Load the itemData from JSON file
    $itemDataFile = './CashShop/itemData.json';
    $itemData = json_decode(file_get_contents($itemDataFile), true);

    // Find the selected item in the itemData array
    $selectedItem = null;
    foreach ($itemData as $item) {
        if ($item['value'] === $itemSelection) {
            $selectedItem = $item;
            break;
        }
    }

    // Check if the selected item exists
    if ($selectedItem) {
        // Retrieve the necessary parameters from the selected item
        $itemIdx = $selectedItem['itemidx'];
        $itemOpt = $selectedItem['itemopt'];
        $tranNo = 1;
        $serverIdx = 1;
        $durationIdx = 31;
        $itemPrice = $selectedItem['price'];

        // Check if the user has enough cash to purchase the item
        if ($cashValue >= $itemPrice) {
            // Deduct the item price from the cash value
            $cashValue -= $itemPrice;

            // Update the cash value in the session
            $_SESSION['cash_value'] = $cashValue;

            global $connCabalCash;

            // Execute the stored procedure
            $tsql = " DECLARE @return_value INT; EXEC @return_value = [dbo].[up_AddMyCashItemByItem] @UserNum = ?, @TranNo = ?, @ServerIdx = ?, @ItemIdx = ?, @ItemOpt = ?, @DurationIdx = ?; SELECT @return_value AS ReturnValue; ";
            $params = array($usernum, $tranNo, $serverIdx, $itemIdx, $itemOpt, $durationIdx);
            $stmt = sqlsrv_query($connCabalCash, $tsql, $params);

            if ($stmt) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                if (isset($row['ReturnValue'])) {
                    $returnValue = $row['ReturnValue'];
                    // echo "Stored procedure executed successfully. Return value: " . $returnValue;
                } else {
                    // echo "Stored procedure executed successfully.";
                }

                // Update the cash value in the database
                $updateCashQuery = "UPDATE dbo.CashAccount SET Cash = ? WHERE UserNum = ?";
                $updateCashParams = array($cashValue, $usernum);
                $updateCashStmt = sqlsrv_query($connCabalCash, $updateCashQuery, $updateCashParams);

                if ($updateCashStmt) {
                    // echo "Cash value updated successfully in the database.";
                    $_SESSION['cash_value'] = $cashValue; // Update the cash value in the session after updating in the database
                } else {
                    // echo "Failed to update cash value in the database.";
                    $purchase = "Failed to update cash value in the database: " . print_r(sqlsrv_errors(), true);
                }

                sqlsrv_free_stmt($stmt);

                // Redirect to the confirmation page
                header("Location: success.php");
                exit;
            } else {
                $purchase = "Failed to execute stored procedure: " . print_r(sqlsrv_errors(), true);
            }

            sqlsrv_close($connCabalCash);
        } else {
            $purchase = "Insufficient cash to purchase the item.";
        }
    } else {
        echo "Selected item does not exist.";
    }
}
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
    <script src="webshop.js" type="module"> </script>
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
<?php if ($userID === "admin" || $userID === "killyou" || $userID === "option3"): ?>
        <div><?php include 'itemCSV.php' ?></div>
    <?php endif; ?>
    <a class = "cashBal"><?php echo $myCash; ?></a>
    <br>
 
<div class="container h-100 d-flex justify-content-center align-items-center">
    <div class="container">
        <h1 class="text-center">Cash Shop</h1>
        <a style="color: red; font-size: 20px;"><?php echo $purchase; ?></a>
        <form action="webshop.php" method="POST">
            <div class="output">
                <div class="select-container">
                    <select class="custom-select" name="itemSelection" id="itemSelection"></select>
                    <div class="select-grid" id="xCard"></div>
                </div>
            </div>
            </div>
            </div>
        </form>
    </div>
</div>
<script src="script.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>