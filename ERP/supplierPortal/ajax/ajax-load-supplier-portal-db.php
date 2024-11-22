<?php
include('../includes/medoo/medoo.php');
include('../includes/database.php');

if(isset($_POST['companyID']) && !empty($_POST['companyID'])){
    $result = $main_database_sup->query("SELECT * FROM srp_erp_company  WHERE company_id = ".$_POST['companyID']."")->fetch();


    $database_sup = new database([
        // required
        'database_type' => 'mysql',
        'database_name' => 'central_db',
        'server' => 'localhost',
        'username' => 'root',
        'password' => 'gearsdev',
        'charset' => 'utf8',
        // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);
    //echo json_encode($result);
    //return $result;
}

?>