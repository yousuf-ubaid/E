<?php
$frm_date = date('Y-01-01');
$to_date = date('Y-m-d'); 

$paymentType_arr = payment_type();
$inv_type = [ 1=> 'Subscription', 2=> 'Implementation', 0=>'Ad hoc' ];
?>
<style>
    .pay-input{
        float: left;
        margin-right: 10px;
    }

    .label-warning:hover{
        cursor: pointer;
    }

    .sub-container button.multiselect2.dropdown-toggle{
        padding: 0px;
    }

    .form-inline.editableform{
        padding-left: 10px;
        padding-right: 10px;
    }

    .frm-filtter-label{
        padding-right: 10px;
    }

    .input-group {        
        display: inline-flex;
    }

    .addon-date{
        padding: 3px 12px;
    }

    .fa-date-fitter {
        font-size: 12px;
        padding: 2px 3px;
        margin-left: -10px;
    }

    .date-input{
        width: 75px !important;
        font-size: 12px;
        padding: 4px 4px;
        height: 24px;
        border: 1px solid #d2d6de;
    }  

    .date-input:focus {
        border-color: #3c8dbc;
        box-shadow: none;
        outline: 0;
    }  

    .btn-group .input-group-addon{
        padding: 8px 4px;
        font-size: 12px;
        width: 35px;
    }

    .label-invoice {
        color: #3c8dbc;
        font-weight: bold;
        font-size: 11px;
    }

    .label-invoice:hover {
        cursor: pointer;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">            
            <?=form_open('', 'id="det_filter_form" name="det_filter_form" autocomplete="off" target="_blank"'); ?>
            <div class="box-header with-border">
                <h3 class="box-title">Payment Details</h3>
                <span class=""> 
                    <div class="col-sm-3 pull-right sub-container">
                        <label class="frm-filtter-label" for="paymentType" class="">Payment Type</label>
                        <?=form_dropdown('paymentType[]', $paymentType_arr, '', 'class="form-control" onchange="payDet_tb.ajax.reload()" multiple id="paymentType"'); ?>
                    </div>

                    <div class="col-sm-3 pull-right sub-container">
                        <label class="frm-filtter-label" for="inv_type" class="">Type</label>
                        <?=form_dropdown('inv_type[]', $inv_type, null, 'class="form-control" onchange="payDet_tb.ajax.reload()" multiple id="inv_type"')?>
                    </div>                                
                                  
                    <div class="col-sm-4 pull-right sub-container">  
                        <label class="frm-filtter-label" for="frm_date">From </label>                        
                        <div class="input-group" id="">
                            <div class="input-group-addon addon-date"><i class="fa fa-calendar fa-date-fitter" aria-hidden="true"></i></div>
                            <input type="text" name="frm_date" class="date-input input-small" value="<?=$frm_date?>" id="frm_date" >
                        </div>

                        <label class="frm-filtter-label" for="to_date"> To</label>
                        <div class="input-group" id="">
                            <div class="input-group-addon addon-date"><i class="fa fa-calendar fa-date-fitter" aria-hidden="true"></i></div>
                            <input type="text" name="to_date" class="date-input input-small" value="<?=$to_date?>" id="to_date">    
                        </div>        
                    </div>                
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="payDet_tb" class="<?=table_class()?>">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th style="min-width: 10%">Company Name</th>
                                    <th style="min-width: 10%">Type</th>
                                    <th style="min-width: 8%">Invoice No</th>                                     
                                    <th style="min-width: 10%">Narration</th>
                                    <th style="min-width: 10%">Invoice Date</th>
                                    <th style="min-width: 10%">Due Date</th>
                                    <th style="min-width: 10%">Invoice Amount</th>
                                    <th style="min-width: 10%">Paid Amount</th>
                                    <th style="min-width: 10%">Paid Date</th>
                                    <th style="min-width: 10%">Payment Type</th>
                                    <th style="min-width: 10%">Ref No</th>                                    
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?=form_close(); ?>
        </div>
    </div>
</section>

<div class="modal fade" id="invoice_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 999999;">
    <div class="modal-dialog modal-lg" id="invoice_modal_dialog" style="width: 80%">
        <?php echo form_open('', 'role="form" id="subscription_inv_form" autocomplete="off"'); ?>
        <div class="modal-content">
            <div class="modal-body" id="invoice_body">
            </div>
            <div class="modal-footer">                
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>

<script type="text/javascript">    
    let frm_date = $('#frm_date');
    let to_date = $('#to_date'); 
    let paymentType = $('#paymentType');
    let inv_type = $('#inv_type');
 
    $('#paymentType, #inv_type').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $('#paymentType, #inv_type').multiselect2('selectAll', false);
    $('#paymentType, #inv_type').multiselect2('updateButtonText');
 
    $(document).ready(function () {
        load_payment_data();

        setTimeout( () => {
            $('.date-input').datepicker({
                format: "yyyy-mm-dd",
                viewMode: "months",
                minViewMode: "days"
            }).on('changeDate', function (ev) {
                $(this).datepicker('hide');

                if( frm_date.val() > to_date.val() ){
                    swal("Error", "To date should be grater than from date", "error");                    
                    return false;
                }

                load_payment_data();   
            });
        }, 300);
    });

    function load_payment_data() {
        payDet_tb = $('#payDet_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_payment_data'); ?>",
            "aaSorting": [[0, 'DESC']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "initComplete": function() {
                //add a name to search box for excel download purpose (with out the input name we cannot get the value in POST)
                $('#payDet_tb_filter').find('input[type="search"]').attr('name', 'text-search');
            },
            "columnDefs": [
                
             ],
            "aoColumns": [                              
                {"mData": "invID"},
                {"mData": "com_name"},
                {"mData": "invoiceType"},
                {"mData": "invNo"},
                {"mData": "itemDescription"},
                {"mData": "invDate"},
                {"mData": "dueDate"},
                {"mData": "invTotal"},
                {"mData": "paidAmount"},
                {"mData": "pay_date"},
                {"mData": "paymentType"},
                {"mData": "TransRefNo"}                
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {                
                aoData.push({'name': 'frm_date', 'value': frm_date.val()});
                aoData.push({'name': 'to_date', 'value': to_date.val()});
                aoData.push({'name': 'paymentType', 'value': paymentType.val()});
                aoData.push({'name': 'inv_type', 'value':  inv_type.val()});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function open_invoice(inv_id){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/load_invoice'); ?>",
            data: {'inv_id': inv_id, 'is_view_only': true},
            cache: false,
            beforeSend: function () {
                startLoad(); 
                $('#invoice_body').html('');
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){                    
                    $('#invoice_body').html(data['view']);
                    $('#invoice_modal').modal('show');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }
</script>    