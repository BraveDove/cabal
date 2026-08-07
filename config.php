<?php
$config = [
    'server' => '100.125.130.81',
    'username' => 'sa',
    'password' => 'Jbgj_Jbgj',
    // Databases
    'AccountDB' => 'Account',
    'CabalCashDB' => 'CabalCash',
    'Server01DB' => 'Server01'
];

$serverName = $config['server'];
$connectionOptions = [
    'Uid' => $config['username'],
    'PWD' => $config['password']
];

$databaseConnections = [];
$connAccount = null;
$connCabalCash = null;
$connServer01 = null;

foreach ($config as $key => $value) {
    if (strpos($key, 'DB') !== false) {
        $databaseName = $value;
        $connectionOptions['Database'] = $databaseName;
        $connection = sqlsrv_connect($serverName, $connectionOptions);

        if ($connection === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $databaseConnections[$key] = $connection;

        // Assign connections to individual variables
        if ($key === 'AccountDB') {
            $connAccount = $connection;
        } elseif ($key === 'CabalCashDB') {
            $connCabalCash = $connection;
        } elseif ($key === 'Server01DB') {
            $connServer01 = $connection;
        }
    }
}
$connAccount = $databaseConnections['AccountDB'];
$connCabalCash = $databaseConnections['CabalCashDB'];
$connServer01 = $databaseConnections['Server01DB'];
?>