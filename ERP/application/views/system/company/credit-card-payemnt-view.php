<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$csrf = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

$merchant_url = $this->config->item('merchant_base_url');
$merchant_verion = $this->config->item('mastercard_version');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Credit card payments</title>
    <link rel="stylesheet" href="<?=base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
    
    <script src="<?=base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?=base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>


    <script src="<?=$merchant_url?>/checkout/version/<?=$merchant_verion?>/checkout.js" 
                data-error="errorCallback" 
                data-cancel="cancelCallback" 
                data-timeout="timeoutCallback" 
                data-complete="completeCallback"
    ></script>

    <style>
        tbody td {
            font-size: 11px !important;
        }

        #loading-contents{
            margin: auto;
            margin-top: 3%;
            width: 50%;            
            padding: 10px;
            text-align: center;
            //border: 1px solid #ccc;
        }
    </style>
</head>

<body class="sidebar-mini fixed hold-transition">
    <div id="loading-contents" >         
        <img src="<?=base_url('images/spinner-1.gif')?>" id="loading-img" style=""/>

        <h3 style="margin-top: -135px">Loading ...</h3>
    </div>
</body>


<div class="modal fade" id="view_creditcard_receipt" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 38%">
        <div class="modal-content">
            <div class="modal-body" id="credit_card_receiptview">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default btn-sm" type="button" onclick="redirect_back()"><?=$this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    let invID = '<?=$inv_id?>';
    let storage_key = '<?=$storage_key?>';
    let pay_status = 1;

    function errorCallback(error) {        
        pay_status = 2;
        addTo_error_log(2, error);
    }

    function cancelCallback() { 
        pay_status = 3;
        addTo_error_log(3);        
    }

    function timeoutCallback() {
        pay_status = 4;
        addTo_error_log(4); 
    }  
    
    if (localStorage[storage_key]) { 
        setTimeout(() => {        
            if( pay_status == 1 ) {
                validate_payment();
            }
                       
        });
    }
    else{        
        proceed_payment();
    }    


    function proceed_payment(){     
        $.ajax({
            async: true,
            type: 'post',
            data: {'invoiceID': invID, '<?=$csrf['name']?>': '<?=$csrf['hash']?>'},
            dataType: 'json',
            url: "<?=site_url('Company/get_mastercard_sessionID'); ?>",
            beforeSend: function () {                
            },
            success: function (data) { 
                if(data['session_id'] == '0') {
                    redirect_confirm( data['error_msg'] );                    
                    return false;
                }
                 
                window.localStorage.setItem(storage_key, invID);

                Checkout.configure({
                    merchant: data['merchant'],
                    order: {
                        amount: function() {                            
                            return data['invoice_amount']
                        },
                        currency: data['mastercard_currency'],
                        description: 'Subscription Payments',
                        id: data['invID'],
                        reference : 'ORDREF'+data['invID']
                    },
                    session : {
                        id : data['session_id']
                        //id : ''
                    },
                    transaction :{
                        reference : 'TRANSREF'+data['invID']
                    },
                    interaction: {
                        operation : 'PURCHASE',
                        displayControl: {
                            billingAddress : 'HIDE',
                            customerEmail  : 'HIDE',
                            orderSummary   : 'SHOW',
                            shipping       : 'HIDE'
                        },
                        merchant: {
                            name: data['invoice_name'],
                            address: {
                                line1: data['companyPrintAddress'],
                            }
                        },                        
                    }
                });

                Checkout.showPaymentPage();
            }, 
            error: function () {
                swal("Error", "Some thing went wrong,Please contact system support.", "error"); 
            }
        });
    }
    

    function validate_payment(){
        var verify_invID = localStorage[storage_key];
        
        $.ajax({
            async: true,
            type: 'post',
            data: {
                'invoiceID': verify_invID, '<?=$csrf['name']?>': '<?=$csrf['hash']?>'
            },
            dataType: 'json',
            url: "<?= site_url('Company/save_mastercard_details'); ?>",
            beforeSend: function () {
                
            },
            success: function (data) {
                if(data[0] == 's') {                    
                    fetch_credit_receipt_view(verify_invID, 'Success', data['payID']); 
                }
                else { 
                    fetch_credit_receipt_view(verify_invID, 'Not captured');
                }
            }, error: function () {  
                swal("Error", "Some thing went wrong,Please contact system support.", "error");                
            }
        });            
    }

    function fetch_credit_receipt_view(invoiceID, results, payID=0)
    { 
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
               'invoiceID':invoiceID, 
               'results':results, 
               'payID':payID, 
               '<?=$csrf['name']?>': '<?=$csrf['hash']?>'
            },
            url: "<?=site_url('Company/credit_card_receipt_view'); ?>",
            beforeSend: function () {
                
            },
            success: function (data) {
                $('#loading-contents').hide();
                $('#credit_card_receiptview').html(data);
                $('#view_creditcard_receipt').modal('show');
                window.localStorage.removeItem(storage_key);                
            },
            error: function (jqXHR, textStatus, errorThrown) {                
                swal("Error", errorThrown, "error");
            }
        });
    }

    function redirect_back(){
        window.location = "<?=site_url('subscription'); ?>";
    }

    function addTo_error_log(pay_type, log=null){
        if(log != null){        
            log = JSON.stringify(log);
        }

        window.localStorage.removeItem(storage_key);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
               'invID': invID, 
               'pay_type': pay_type, 
               'log': log, 
               '<?=$csrf['name']?>': '<?=$csrf['hash']?>'
            },
            url: "<?=site_url('Company/addTo_error_log'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loading-contents').hide();
                
                if(data['msg'] !== ''){
                    redirect_confirm(data['msg']);
                }
                else{
                    redirect_back();                    
                }                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loading-contents').hide();
                redirect_confirm('An unexpected error occured.');              
            }
        });
    }

    function redirect_confirm(msg){
        swal(
            {
                title: "",
                text: msg,
                type: "warning",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?=$this->lang->line('common_ok');?>",                

            },
            function () {
                redirect_back();
            }
        );
    }
</script>

</html>