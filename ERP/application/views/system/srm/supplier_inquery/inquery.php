<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log in | ERP</title>
    <link rel="icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/css/supplier_portal.css'); ?>"/>
    
 
    <!-- //custom css file -->

    <!-- google fonts -->
    <!-- <link
        href='//fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic,800,800italic'
        rel='stylesheet' type='text/css'> -->
    <!-- //google fonts -->

</head>
<style>
    .hide {
        display: none;
    }

    .alert.alert-danger {
        border-top: 1px solid rgba(140, 0, 0, 0.4);
        border-bottom: 1px solid rgba(140, 0, 0, 0.4);
    }

    .alert.alert-success {
        border-top: 1px solid limegreen;
        border-bottom: 1px solid limegreen;
    }

    .alert {
        padding-left: 30px;
        margin-left: 15px;
        position: relative;
        font-size: 12px;
    }

    .alert {
        background-position: 2% 7px;
        background-repeat: no-repeat;
        background-size: auto 35px;
        background-color: rgba(0, 0, 0, 0);
        border: 0;
        min-width: auto !important;
        text-align: left;
        padding-left: 68px;
    }

    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-danger, .alert-error {
        color: #b94a48;
        background-color: #f2dede;
        border-color: #eed3d7;
    }

    .alert, .alert h4 {
        color: #c09853;
    }

    .alert {
        padding: 8px 35px 8px 14px;
        margin-bottom: 20px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    @media print {
        .printPageButton {
            display: none;
        }
    }
</style>

    <?php

        $supDetails = $this->db->query("SELECT * FROM srp_erp_srm_suppliermaster WHERE companyID = " . $compID . " AND supplierAutoID = " . $supID . "")->row_array();

    ?>

<body>
<h1 style="color: black">Supplier Portal</h1>

<div class="agile-its" style="">
    <div class="w3layouts">
        <div class="photos-upload-view">
            <div class="row">
                <div class="table-responsive">
                    <div class="wthreesubmitaits">
                        <input type="button" class="printPageButton hidden" style="background-color: #0b659e; width: 50%;" name="print" value="Print" onclick="print()">
                    </div>
                    <div style="text-align: center"> <label>Supplier Details</label> </div>
                    <br>
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td class="headrowtitle" style="font-size: 13px; color: black;"><strong> Supplier Name</strong></td><!--Customer Name-->
                            <td class="headrowtitle" ><strong>:</strong></td>
                            <td class="headrowtitle" style="font-size: 11px;"> <?php echo $supDetails['supplierName']; ?></td>

                            <td class="headrowtitle" style="font-size: 13px; color: black;"><strong>Supplier Address</strong></td>
                            <td class="headrowtitle" ><strong>:</strong></td>
                            <td class="headrowtitle" style="font-size: 11px;"><?php echo $supDetails['supplierAddress1']; ?> , <?php echo $supDetails['supplierAddress2']; ?> </td>

                            <td class="headrowtitle" style="font-size: 13px; color: black;"><strong>Credit Period</strong></td>
                            <td class="headrowtitle" ><strong>:</strong></td>
                            <td class="headrowtitle" style="font-size: 11px;"><?php echo $supDetails['supplierCreditPeriod']; ?> Months</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="agileinfo">
            </div>
            <div class="agileinfo-row">
                <label>rfq header</label>

                <div class="form_box">
                    <div class="select-block1 middle">
                        <?php
                        $result = $this->db->query("SELECT inquiryID,documentCode FROM srp_erp_srm_orderinquirymaster WHERE confirmedYN = 1")->result_array();
                        ?>
                        <select name="inquiryID" id="inquiryID" onchange="load_inquiryMaster()">
                            <option value="">Inquiry ID</option>
                            <?php
                            if (!empty($result)) {
                                foreach ($result as $row) { ?>
                                    <option
                                        value="<?php echo $row['inquiryID'] ?>"><?php echo $row['documentCode'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" id="supplierID" name="supplierID">
            <input type="hidden" id="compID" name="companyID">
            <input type="text" id="currencyID" name="Name" placeholder="Currency" required="" style="width: 30%;">

            <input type="text" id="narration" name="narration" placeholder="Narration" required=""
                   style="width: 30%;">

            <br>

        </div>
        <br>

        <div id="supplier_detail_rfq_div" class="">
            <label>rfq detail</label>

            <form action="#" method="POST" name="supplier_rfq_frm" id="supplier_rfq_frm">
                <div id="assignSupplier_item_Content"></div>
                <div class="wthree-text">
                    <div class="wthreesubmitaits">
                        <input type="button" style="background-color: #0d9564;" name="submit" class="printPageButton" value="Submit" onclick="submitSupplierRFQ()">
                    </div>
                </div>
            </form>

            <span id="output_save">&nbsp;</span>
        </div>
    </div>
    <div class="clear"></div>
</div>
</div>
<?php //include('footer.php'); ?>


<script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>

<script>
    load_inquiryMaster();
    function load_supportal_db() {
        var companyID = $("#compID").val();
        $.ajax({
            type: "POST",
            url: "ajax/ajax-load-supplier-portal-db.php",
            data: {companyID: companyID},
            dataType: "JSON",
            cache: false,
            beforeSend: function () {
                $("#pageViwContent").html('<i class="icon-refresh icon-spin"></i> Loading contents.');
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    load_inquiryMaster();
                }
            }
        });
    }

    function load_inquiryMaster() {

        var inquiryID = 1; //$("#inquiryID").val();
        var companyID = 505;
        var supplierID = 2;

        $.ajax({
            type: "POST",
            url: "SupplierPortal/retrive_order_inquery",
            data: {inquiryID: inquiryID,companyID: companyID,supplierID: supplierID},
            dataType: "JSON",
            cache: false,
            beforeSend: function () {
                $("#pageViwContent").html('<i class="icon-refresh icon-spin"></i> Loading contents.');
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#currencyID').val(data['CurrencyCode']);
                    $('#narration').val(data['narration']);
                    load_supplierItemMaster();
                }
            }
        });
        return false;
    }
    function load_supplierItemMaster() {
        $('#assignSupplier_item_Content').html('');
        var inquiryID = $("#inquiryID").val();
        var supplierID = $("#supplierID").val();
        $.ajax({
            type: "POST",
            url: "ajax/ajax-load-supplier-items.php",
            data: {inquiryID: inquiryID, supplierID: supplierID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#pageViwContent").html('<i class="icon-refresh icon-spin"></i> Loading contents.');
            },
            success: function (data) {
                $('#supplier_detail_rfq_div').removeClass('hide');
                $('#assignSupplier_item_Content').html(data);
            }
        });
        return false;
    }

    function submitSupplierRFQ() {
        var customerOrderID = $('#customerOrderID_orderDetail').val();
        var data = $('#supplier_rfq_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "ajax/ajax-save-supplier-feedback.php",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                if (data['error'] == 0) {
                    $('#TempReportMainID').val(data['code']);
                    $("#output_save").html('<span class="alert alert-success"><strong>Records Updated Successfully</strong></span>');
                    setTimeout(function () {
                        $("#output_save").html('')
                    }, 3000);
                    load_supplierItemMaster();
                }
                else if (data.error == 1) {
                    $("#output_save").html('<div style="margin:5px" class="alert alert-danger"><strong>Please correct the following</strong><br>' + data['message'] + '</div>');
                    setTimeout(function () {
                        $("#output_save").html('')
                    }, 10000);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
</script>

<?php
if (isset($_GET['link']) && !empty($_GET['link'])) {
    $masterID = $_GET['link'];
    $newID = explode("_", $masterID);
    $inquiryID = $newID[0];
    $supplierID = $newID[1];
    $compID = $newID[2];

    ?>
    <script>
        $("#inquiryID").val(<?php echo $inquiryID ?>);
        $("#supplierID").val(<?php echo $supplierID ?>);
        $("#compID").val(<?php echo $compID ?>);

        load_inquiryMaster();
    </script>
    <?php
}

?>

</body>
</html>