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
require_once('../includes/class/cls.FormValidator.php');

$error_msg = "";
$validator = new FormValidator();
if (isset($_POST['supplierQTY']) && !empty($_POST['supplierQTY'])) {
    foreach ($_POST['supplierQTY'] as $val) {
        if ($val == "") {
            $_POST['supplierQTY'] = "";
            $validator->addValidation("supplierQTY", "req", "Supplier QTY is required");
            continue;
        }
    }
}
if (isset($_POST['unitprice']) && !empty($_POST['unitprice'])) {
    foreach ($_POST['unitprice'] as $val) {
        if ($val == "") {
            $_POST['unitprice'] = "";
            $validator->addValidation("unitprice", "req", "Unit Price is required");
            continue;
        }
    }
}
if (isset($_POST['supplierDate']) && !empty($_POST['supplierDate'])) {
    foreach ($_POST['supplierDate'] as $val) {
        if ($val == "") {
            $_POST['supplierDate'] = "";
            $validator->addValidation("supplierExpectedDeliveryDate", "req", "Delivery date is required");
            continue;
        }
    }
}
/*if (isset($_POST['narration']) && !empty($_POST['narration'])) {
    foreach ($_POST['narration'] as $val) {
        if ($val == "") {
            $_POST['narration'] = "";
            $validator->addValidation("SupplierNarration", "req", "Narration is required");
            continue;
        }
    }
}*/
if ($validator->ValidateForm()) {
    if (isset($_POST['detailID']) && !empty($_POST['detailID'])) {
        $searches = $_POST['detailID'];
        foreach ($searches as $key => $inquiryDetailID) {
            date_default_timezone_set('UTC');
            $newdate = date('Y-m-d', strtotime($_POST['supplierDate'][$key]));
            $database_sup->update("srp_erp_srm_orderinquirydetails", array(
                "supplierQty" => $_POST['supplierQTY'][$key],
                "supplierPrice" => $_POST['unitprice'][$key],
                "supplierExpectedDeliveryDate" => $newdate,
                "SupplierNarration" => $_POST['narration'][$key],
                "isSupplierSubmited" => 1,
            ), array(
                "inquiryDetailID" => $inquiryDetailID
            ));
        }
        echo json_encode(array('error' => 0, 'message' => 'Records added successfully'));

    } else {

        echo json_encode(array('error' => 1, 'message' => "No Records to Submit"));
    }
} else {
    $error_hash = $validator->GetErrors();
    $error_msg = '<ul style="list-style:none;">';
    foreach ($error_hash as $inpname => $inp_err) {
        $error_msg .= "<li>$inp_err</li>";
    }
    $error_msg .= "</ul>";
    echo json_encode(array('error' => 1, 'message' => $error_msg));
}

