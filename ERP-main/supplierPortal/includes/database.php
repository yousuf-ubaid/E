<?php
if(!isset($_SESSION))
{
    session_start();
}
require_once('medoo/medoo.php');
//error_reporting(-1); // on
error_reporting(E_ALL);
ini_set('display_errors', 1);

$str = $_SERVER['DOCUMENT_ROOT'];
define("DIR_PATH", $str);

// database file
include('db.php');


if(array_key_exists('sup_por_company_id',$_SESSION)){

    $com = $_SESSION['sup_por_company_id'];
    $result = $main_database_sup->query("SELECT * FROM srp_erp_company  WHERE company_id =$com ")->fetch();
    $fields = array(
        'database_name' => $result['db_name'],
        'server' => $result['host'],
        'username' => $result['db_username'],
        'password' => $result['db_password']
    );
    $fields = json_encode($fields);
    $hst=$_SERVER['SERVER_NAME'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$hst/index.php/login/connection/$com");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    $decod=base64_decode(base64_decode($response));
    $dbcon = explode("|", $decod);

    if($dbcon){

        $database_name=$dbcon[3];
        $dbserver=$dbcon[0];
        $dbusername=$dbcon[1];
        $dbpassword=$dbcon[2];

        $database_sup = new database([
            // required
            'database_type' => 'mysql',
            'database_name' => $database_name,
            'server' => $dbserver,
            'username' => $dbusername,
            'password' => $dbpassword,
            'charset' => 'utf8',
            // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ]);

    }
    
}


?>


