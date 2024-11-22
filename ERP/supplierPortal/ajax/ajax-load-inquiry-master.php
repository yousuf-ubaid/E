<?php
if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    $masterID = $_SERVER['HTTP_REFERER'];
    $newID = explode("_", $masterID);
    $compID = $newID[2];
    $supID = $newID[1];
    $_SESSION['sup_por_company_id']=$compID;
}
include('../includes/medoo/medoo.php');
include('../includes/database.php');
if(isset($_POST['inquiryID']) && !empty($_POST['inquiryID'])){
    $result = $database_sup->query("SELECT CurrencyCode,narration FROM srp_erp_srm_orderinquirymaster INNER JOIN srp_erp_currencymaster ON srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID WHERE inquiryID = ".$_POST['inquiryID']."")->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result);
    //return $result;
}

?>