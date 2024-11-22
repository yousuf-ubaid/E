<?php
$subType_arr = [
        0 => 'Active', 1 => 'Inactive', 2 => 'On Hold', 3 => 'Expire'
];

$paymentType_arr = [ 0=> 'No', 1=> 'Yes'];
$com_type = company_type();
?>
<style>
    .label-invoice{
        color: #3c8dbc;
        font-weight: bold;
        font-size: 11px;
    }

    .label-invoice:hover{
        cursor: pointer;
    }

    .create-invoice{
        color: #0d6aad;
        font-weight: bold;
    }

    .create-invoice:hover{
        cursor: pointer;
    }

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

    .user_type_drop {
        padding: 2px;
        font-size: 12px;
        height: 20px;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">
            <?php
            /*** URL hard coded because of hosting problem ***/
            $url = 'https://cloud.spur-int.com/sme_company/index.php/Dashboard/company_subscription_excel';
            //$url = base_url('Dashboard/company_subscription_excel');
            ?>
            <?=form_open($url, 'id="subscription_filter_form" name="subscription_filter_form" autocomplete="off" target="_blank"'); ?>
            <div class="box-header with-border">
                <h3 class="box-title">Subscription</h3>
                <span class="">
                    <button type="button" class="btn btn-success btn-xs pull-right" onclick="download_subscription()">
                        <i class="fa fa-file-excel-o"></i> Download
                    </button>

                    <div class="col-sm-3 pull-right sub-container">
                        <label class="frm-filtter-label" for="com_type" class="">Type</label>
                        <?=form_dropdown('com_type[]', $com_type, null, 'class="form-control" onchange="subscription_tb.ajax.reload()" multiple id="com_type"')?>
                    </div>                    

                    <div class="col-sm-3 pull-right sub-container">
                        <label class="frm-filtter-label" for="subType" class="">Sub. Status</label>
                        <?=form_dropdown('subType[]', $subType_arr, '', 'class="form-control" onchange="subscription_tb.ajax.reload()" multiple id="subType"'); ?>
                    </div>                    

                    <div class="col-sm-3 pull-right sub-container">
                        <label class="frm-filtter-label" for="paymentType" class="">Payment</label>
                        <?=form_dropdown('paymentType[]', $paymentType_arr, '', 'class="form-control" onchange="subscription_tb.ajax.reload()" multiple id="paymentType"'); ?>
                    </div>
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="subscription_tb" class="<?=table_class()?>">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th style="min-width: 10%">Company Name</th>
                                    <th style="min-width: 10%">Business Name</th>
                                    <th style="min-width: 10%">Contact Details</th>
                                    <th style="min-width: 8%">Country</th>
                                    <th style="min-width: 6%">Payment Enabled</th>
                                    <th style="min-width: 6%">Type</th>
                                    <th style="min-width: 10%">Subscription</th>
                                    <th style=""><abbr title="Days left to expire">Days</abbr></th>
                                    <th style="min-width: 10%">Subscription ID</th>
                                    <th style="min-width: 10%">Registered Date</th>
                                    <th style="min-width: 10%">Subscription Start Date</th>
                                    <th style="min-width: 10%">Subscription Amount</th>
                                    <th style="min-width: 10%">Implementation Amount</th>
                                    <th style="min-width: 10%">Next Renewal Date</th>
                                    <th style="min-width: 10%">Last Renewed Date</th>
                                    <th style="width: 50px">Currency</th>
                                    <th style="min-width: 10%">Last Access Date</th>
                                    <th style="width: 55px">&nbsp;</th>
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

<div class="modal fade" id="subscription_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Company Setup </h3>
            </div>
            <form role="form" id="frm_subscription" class="form-horizontal" autocomplete="off" action="#">
                <input type="hidden" name="sub_company_id" id="sub_company_id" value="" />
                <div class="modal-body" >
                    <div class="form-group">
                        <label class="col-sm-10 ">
                            <span style="font-weight: normal">Company Name : </span>
                            <span id="sub_company_name"> </span>
                        </label>
                    </div>

                    <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
                        <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                            <li class="active">
                                <a href="#subscription-tab" data-toggle="tab" aria-expanded="true">Subscription</a>
                            </li>
                            <li class="">
                                <a href="#product-tab" data-toggle="tab" aria-expanded="false">Products</a>
                            </li>
                        </ul>
                        <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                            <div class="tab-pane active" id="subscription-tab">
                                <div class="form-group">
                                    <label for="payCurrencyID" class="col-sm-4 control-label">Subscription Currency</label>
                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('currencyID', all_currency_drop(), '', 'class="form-control" id="currencyID" disabled'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Subscription Amount</label>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                                            <input type="text" name="sub_amount"  id="sub_amount" class="form-control number" required value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <button type="button" class="btn btn-primary btn-sm submitBtn pull-right" onclick="update_subscription_amount()">Save</button>
                                </div>
                            </div>

                            <div class="tab-pane" id="product-tab">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border"> Assign products </legend>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Products</label>
                                        <div class="col-sm-5">
                                            <?=form_dropdown('products_drop[]', null, '', 'class="form-control select2" 
                                                            id="products_drop" multiple="multiple"');?>
                                        </div>
                                    </div>

                                    <div class="box-footer">
                                        <div class="pull-right">
                                            <button class="btn btn-primary btn-sm " type="button" id="product-btn" onclick="assign_product()">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </fieldset>

                                <hr/>

                                <div class="table-responsive">
                                    <table id="com_product_tb" class="<?=table_class()?>">
                                        <thead>
                                        <tr>
                                            <th style="width: 15px">#</th>
                                            <th style="width: auto">Description</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="invoice_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 999999;">
    <div class="modal-dialog modal-lg" id="invoice_modal_dialog" style="width: 80%">
        <?php echo form_open('', 'role="form" id="subscription_inv_form" autocomplete="off"'); ?>
        <div class="modal-content">
            <div class="modal-body" id="invoice_body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" id="inv_generate_btn">Generate</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="subscription_history_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    Subscription History <span id="subscription_history_title" style="font-size: 16px;"></span>
                </h3>
            </div>
            <form role="form" id="" class="form-horizontal" autocomplete="off" action="#">
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-md-5">
                            <div class="table-responsive" style="margin-bottom: 15px;">
                                <table class="table table-bordered table-striped table-condensed">
                                    <tbody>
                                        <tr>
                                            <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Paid </td>
                                            <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Unpaid</td>
                                            <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Pending For Verification </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <button type="button" class="btn btn-primary btn-sm pull-right" id="ad-hoc-btn" style="margin-left: 10px;">
                                Ad-hoc
                            </button>
                            &nbsp;
                            <button type="button" class="btn btn-primary btn-sm pull-right" id="new-subscription-btn">
                                New Subscription
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div style="height: 420px">
                                <table id="sub_history_tb" class="<?=table_class()?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th style="width: 120px">Subscription Start Date</th>
                                        <th style="width: 50px">Type</th>
                                        <th style="min-width: 100px">Amount</th>
                                        <th style="min-width: 100px">Next Renewal Date</th>
                                        <th style="min-width: 100px">Due Date</th>
                                        <th style="min-width: 100px">Invoice</th>
                                        <th style="width: 80px">Status</th>
                                        <th style="width: 80px">Mark As Paid</th>
                                        <th style="width: 60px"></th>
                                    </tr>
                                    </thead>

                                    <tbody id="sub-table-body"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12" id="sub-history-error" style="padding: 20px 50px 10px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="warehouse_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><span class="outlet_title" style="font-size: 16px;"></span></h3>
            </div>

            <div class="modal-body" >
                <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
                    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                        <li class="active">
                            <a href="#outlet-tab" data-toggle="tab" aria-expanded="true">Outlets</a>
                        </li>
                        <li class="">
                            <a href="#user-tab" data-toggle="tab" aria-expanded="false">Users</a>
                        </li>
                    </ul>
                    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                        <div class="tab-pane active" id="outlet-tab">
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <button class="btn btn-primary btn-xs pull-right" onclick="new_outlet()">
                                        New Outlet
                                    </button>
                                </div>
                                                            
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="warehouse_tb" class="<?=table_class()?>">
                                            <thead>
                                            <tr>
                                                <th style="width: 15px">#</th>
                                                <th style="width: auto">Code</th>
                                                <th style="width: auto">Description</th>
                                                <th style="width: auto">Location</th>
                                                <th style="width: 55px">Status</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="user-tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="<?=table_class()?>" id="com_user_tb">
                                            <thead>
                                            <tr>
                                                <th style="min-width: 5%">#</th>
                                                <th style="min-width: 10%">Code</th>
                                                <th style="min-width: 40%">Employee Name</th>
                                                <th style="min-width: 15%">UserName</th>
                                                <th style="min-width: 10%">Gender</th>
                                                <th style="min-width: 10%">Date Joined</th>
                                                <th style="">Status</th>
                                                <th style="">Login</th>
                                                <th style="">User type</th>
                                                <th>Last Login</th>
                                                <th style="">Action</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>

            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newOutlet_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <?=form_open('', 'id="new_outlet_frm" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Create Outlet : <span class="outlet_title" style="font-size: 16px;"></span></h3>
            </div>

            <div class="modal-body" >
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">POS Type <?required_mark()?></label>
                        <div class="col-sm-6">
                            <select name="pos_type" id="pos_type" class="form-control" onchange="change_form_content(this)">
                                <option value="1">General</option>
                                <option value="0">Restaurant</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Segment <?required_mark()?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('pos_segment', null, '', 'id="pos_segment" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group" id="pos-related-div">
                        <label class="col-sm-4 control-label">POS Template <?required_mark()?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('posTemplateID', null, '', 'id="posTemplateID" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Code <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_code" name="outlet_code" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Name <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_name" name="outlet_name" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Location <?required_mark()?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_location" name="outlet_location" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Address</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="outlet_address" name="outlet_address" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Telephone</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="outlet_tel" name="outlet_tel" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label">Foot Note</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="foot_note" name="foot_note" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="create_outlet()">Create</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
            <?=form_close()?>
        </div>
    </div>
</div>

<div class="modal fade" id="user_setup_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="">
    <div class="modal-dialog">
        <?php echo form_open('', 'role="form" id="user_setup_form" class="form-horizontal" autocomplete="off"'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"> User Password Reset </h3>
            </div>
            <div class="modal-body" id="">
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">Employee Name</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="user_setup_name" readonly>
                        <input type="hidden" id="user_setup_id" name="user_id">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">User Name</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="user_setup_userName" name="userName" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-4 control-label">Password</label>
                    <div class="col-sm-7">
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" onclick="reset_password()">Reset password</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>

<link rel="stylesheet" href="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.css');?>">
<script type="text/javascript" src="<?=base_url('plugins/bootstrap-switch/bootstrap-switch.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>

<script type="text/javascript">
    let subscription_tb = null;
    let sub_history_tb = null;
    let hidden_companyID = null;

    let subType = $('#subType');
    let paymentType = $('#paymentType');
    let com_type = $('#com_type');
    
    subType.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    subType.multiselect2('selectAll', false);
    subType.multiselect2('updateButtonText');

    paymentType.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    paymentType.multiselect2('selectAll', false);
    paymentType.multiselect2('updateButtonText');

    com_type.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    com_type.multiselect2('selectAll', false);
    com_type.multiselect2('updateButtonText');

    $('#products_drop').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:300,
        maxHeight: 200,
        numberDisplayed: 1
    });

    $(document).ready(function () {
        load_subscription_data();

        $('#sub_amount').numeric({decimalPlaces:3, negative:false});
    });

    function load_subscription_data() {
        refresh_tbl = subscription_tb = $('#subscription_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_subscription_data'); ?>",
            "aaSorting": [[1, 'ASC']],
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
                $('#subscription_tb_filter').find('input[type="search"]').attr('name', 'text-search');
            },
            "columnDefs": [
                { "targets": [0,3,6,7,8,16,18], "orderable": false }
             ],
            "aoColumns": [
                {"mData": "company_id"},
                {"mData": "com_name"},              
                {"mData": "company_business_name"},              
                {"mData": "company_det"},
                {"mData": "company_country"},
                {"mData": "paymentEnabled"},
                {"mData": "tyDes"},
                {"mData": "subscription_status"},
                {"mData": "daysLeftForExpire"},
                {"mData": "subscriptionNo"},
                {"mData": "registeredDate"},
                {"mData": "subscriptionStartDate"},
                {"mData": "subscriptionAmount"},
                {"mData": "implementationAmount"},
                {"mData": "nextRenewalDate"},
                {"mData": "lastRenewedDate"},
                {"mData": "currencyCode"},
                {"mData": "last_access_date"},
                {"mData": "edit"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'subType', 'value': subType.val()});
                aoData.push({'name': 'paymentType', 'value': paymentType.val()});
                aoData.push({'name': 'com_type', 'value':  $('#com_type').val()});
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

    function subscription_action(companyID, obj) {
        let det = get_dataTable_det('subscription_tb', obj);

        $("#sub_company_id").val(companyID);
        $("#sub_company_name").text(det.company_name);
        $("#currencyID").val(det.curr_id).change();
        $("#sub_amount").val(det.subAmount);

        load_products();
        fetch_company_product();

        $('#subscription_modal').modal('show');
    }

    function fetch_company_product() {
        $('#com_product_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_product'); ?>",
            "aaSorting": [[1, 'desc']],
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
            "columnDefs": [
                { "targets": [0,2], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "action"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                let com_id = $("#sub_company_id").val();
                aoData.push({'name': 'company_id', 'value': com_id});
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

    function load_products(){
        let company_id = $('#sub_company_id').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/get_product_for_company');?>",
            data: {'company_id': company_id},
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#products_drop').empty();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){

                    let str = '';
                    $.each(data['products'], function (i, val) {
                        str += '<option value="'+val['id']+'">'+val['description']+'</option>'
                    });

                    $('#products_drop').html(str);
                    $("#products_drop").multiselect2('destroy');
                    $('#products_drop').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        buttonWidth:300,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function assign_product(){
        let products = $('#products_drop').val();
        let company_id = $('#sub_company_id').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/assign_product_to_company'); ?>",
            data: {'products': products, 'company_id': company_id },
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    load_products();
                    fetch_company_product();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function remove_product_from_company(id){
        swal({
                title: "Are you sure?",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/remove_company_from_product');?>",
                    data: {'id': id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            fetch_company_product();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            },
        );
    }

    function update_subscription_amount() {
        var post_data = $('#frm_subscription').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/update_subscription_amount'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    subscription_tb.ajax.reload();
                    $('#subscription_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_history(company_id, obj){
        $('#subscription_history_modal').modal('show');
        
        let det = get_dataTable_det('subscription_tb', obj);

        $('#subscription_history_title').text(' : '+det.company_name);
        $('#new-subscription-btn').attr('onclick', 'generate_subscription('+company_id+')');
        $('#ad-hoc-btn').attr('onclick', 'generate_ad_hoc('+company_id+')');
        fetch_subscription_history(company_id);
    }

    function fetch_subscription_history(company_id){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/subscription_history_data'); ?>",
            data: {'company_id': company_id},
            cache: false,
            beforeSend: function () {
                startLoad();
                let str = '<tr><td colspan="6">No data available in table</td></tr>';
                $('#sub-history-error').html('');
                $('#sub-table-body').html(str);
                $('#new-subscription-btn').hide();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    if(data['isInitialSubscriptionConfirmed'] == 1){
                        $('#new-subscription-btn').show().attr('onclick', 'generate_subscription('+company_id+')');
                    }
                    if(! $.isEmptyObject(data['att_data'])){
                        let str = ''; let lastRenewedDate = ''; let isAmountPaid = 0;
                        $.each(data['att_data'], function(i, val){
                            let invoice_am = val['invoice_am'];
                            if(val['invNo'] == null && data['is_paymentEnabled'] == 1) {
                                invoice_am = '<a href="#" data-type="text" class="xeditable" data-placement="bottom" data-pk="'+val['subscriptionID']+'"';
                                invoice_am += 'data-name="name" data-title="Subscription Amount" data-value="'+val['invoice_am']+'" id="sub-his-'+i+'" ';
                                invoice_am += 'data-id="sub-his-'+i+'" data-inv-type="'+val['inv_type']+'" ';
                                invoice_am += 'data-url="<?=site_url('Dashboard/update_single_subscription_amount') ?>">'+ val['invoice_am'] +'</a>'
                            }

                            let inv_type = '';
                            switch(Number(val['inv_type'])){
                                case 1: inv_type = 'Subscription'; break;
                                case 2: inv_type = 'Implementation'; break;
                                case 3: inv_type = 'Ad hoc'; break;
                            }

                            str += '<tr>';
                            str += '<td>'+(i+1)+'</td>';
                            str += '<td style="text-align: center">'+val['subscriptionStartDate']+'</td>';
                            str += '<td>'+inv_type+'</td>';
                            str += '<td style="text-align: right">'+invoice_am+'</td>';
                            str += '<td style="text-align: center">'+val['nextRenewalDate']+'</td>';
                            str += '<td style="text-align: center">'+val['dueDate']+'</td>';
                            str += '<td style="text-align: center">';

                            if(val['invNo'] == null){
                                if(data['is_paymentEnabled'] == 1) {
                                    str += '<span class="create-invoice" onclick="invoice_build_view(' + val['subscriptionID'] + ', ' + val['inv_type'] + ')">Create Invoice</span>'
                                }
                            }
                            else{
                                str += '<span class="label-invoice" onclick="open_invoice('+val['invID']+')">'+val['invNo']+'</span>';
                            }

                            str += '</td><td style="text-align: center">';

                            isAmountPaid = parseInt(val['isAmountPaid']);
                            if(isAmountPaid == -1){
                                str += '<span class="label label-warning verify_'+val['invID']+'" onclick="verify_payment('+val['invID']+')">';
                                str += ' <i class="fa fa-plus"></i> </span>';
                            }
                            else if(isAmountPaid == 1){
                                str += '<span class="label label-success"> &nbsp; &nbsp;</span>';
                            }else {
                                str += '<span class="label label-danger"> &nbsp; &nbsp;</span>';
                            }

                            let markAsPaid = val['paymentType'];
                            str += '<td style="text-align: center">';
                            if(val['invNo'] != null && val['online_pay'] == null ){
                                let checked_str = (markAsPaid ==3)? 'checked': '';
                                str += '<input type="checkbox" id="markAsPaid_'+val['invID']+'" onclick="update_paid_status('+val['invID']+','+company_id+')"'; 
                                str += 'name="markAsPaid" '+checked_str+'>';                            
                            }
                            str += '</td>';

                            let autoID = (val['invID'] != null)? val['invID']: val['subscriptionID'];
                            let deleteType = (val['invID'] != null)? 'invoice': 'subscription';

                            str += '</td><td align="right"><span rel="tooltip" class="glyphicon glyphicon-trash delete-icon" ';
                            str += 'onclick="delete_subscription_invoice(\''+deleteType+'\','+ autoID + ',' + company_id + ')" title="Delete '+deleteType+'"></span>';
                            str += '</td></tr>';

                        });

                        $('#sub-table-body').html(str);

                        $('.xeditable').editable({
                            ajaxOptions: { dataType: 'json'},
                            params: function(params) {
                                // add additional params from data-attributes of trigger element
                                params.tb_id = $(this).editable().data('id');
                                params.inv_type = $(this).editable().data('inv-type');
                                return params;
                            },
                            success: function(response, newValue) {
                                setTimeout(function(){
                                    $('#'+response['id']).editable('setValue', response['amount']);
                                }, 500);
                            }
                        });
                    }

                    $('#sub_history_tb').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                }
                else{
                    $('#sub-history-error').html('<div class="alert alert-danger">'+data[1]+'</div>');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function open_invoice(inv_id){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/load_invoice'); ?>",
            data: {'inv_id': inv_id},
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#inv_generate_btn').hide();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $('#invoice_modal_dialog').addClass('modal-lg').css('width', '80%');
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

    function invoice_build_view(sub_id, itemID){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/build_initial_invoice'); ?>",
            data: {'sub_id': sub_id, 'itemID': itemID},
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#inv_generate_btn').show().attr('onclick', 'subscription_invoice_generation()');
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    //$('#invoice_modal_dialog').removeClass('modal-lg').removeAttr('style');
                    $('#invoice_body').html(data['built_view']);
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

    function subscription_invoice_generation(){
        let post_data = $('#subscription_inv_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: post_data ,
            url: "<?php echo site_url('Dashboard/initial_invoice_generate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    fetch_subscription_history(data['company_id']);
                    $('#invoice_modal').modal('hide');
                    subscription_tb.ajax.reload();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function verify_payment(inv_id) {
        swal({
                title: "Are you sure?",
                text: "You want to mark this invoice as verified",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/verify_subscription_payment');?>",
                    data: {'inv_id': inv_id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                        $('#inv_generate_btn').hide();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            $('.verify_'+inv_id).removeClass('label-warning').addClass('label-success').html('&nbsp; &nbsp;').removeAttr('onclick')
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });
            }
        );
    }

    function update_paid_status(invid,company_id) {
        var checkBox = document.getElementById("markAsPaid_"+invid);
        var ismanualYn, txt = '';
        if (checkBox.checked == true){
            ismanualYn = 3;
        }
        else {
            txt = 'un';
            ismanualYn = 0;
        }

       swal({
                title: "Are you sure?",
                text: "You want to mark this invoice as "+txt+" paid",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/mark_as_paid_amt');?>",
                    data: {'inv_id': invid,'ismarkaspaid' : ismanualYn},
                    cache: false,
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            subscription_tb.ajax.reload();
                            fetch_subscription_history(company_id);
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            },
        );
    }

    function delete_subscription_invoice(deleteType, autoID, company_id) {
       swal({
                title: "Are you sure?",
                text: "You want to delete this "+deleteType,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                let reqUrl = (deleteType == 'invoice')? 'delete_invoice': 'delete_subscription';
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/');?>"+reqUrl,
                    data: {'autoID': autoID},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            subscription_tb.ajax.reload();
                            fetch_subscription_history(company_id);
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            },
       );
    }

    function generate_subscription(company_id) {
       swal({
                title: "Are you sure?",
                text: "You want to generate next subscription",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/generate_subscription');?>",
                    data: {'company_id': company_id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            invoice_build_view(data['sub_id'], 1);
                            fetch_subscription_history(company_id);
                            subscription_tb.ajax.reload();
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            },
        );
    }

    function download_subscription() {
        document.subscription_filter_form.submit();
    }

    function generate_ad_hoc(company_id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/build_ad_hoc_invoice'); ?>",
            data: {'company_id': company_id},
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#inv_generate_btn').show().attr('onclick', 'generate_ad_hoc_invoice()');
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $('#invoice_body').html(data['built_view']);
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

    function generate_ad_hoc_invoice(){
        let post_data = $('#subscription_inv_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: post_data ,
            url: "<?php echo site_url('Dashboard/generate_ad_hoc_invoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    fetch_subscription_history(data['company_id']);
                    $('#invoice_modal').modal('hide');
                    subscription_tb.ajax.reload();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }
</script>

<script type="text/javascript">
    let warehouse_tb = null;
    function load_warehouse(company_id, obj) {
        hidden_companyID = company_id;
        let det = get_dataTable_det('subscription_tb', obj);
        $('.outlet_title').text( det.company_name );
        $('#warehouse_modal').modal('show');

        warehouse_tb = $('#warehouse_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_company_warehouse'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                $(".switch-chk").bootstrapSwitch();
            },
            "columnDefs": [
                { "targets": [0,4], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"},
                {"mData": "wr_status"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value': hidden_companyID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        fetch_users_data();
    }

    var com_user_tb = $('#com_user_tb');
    function fetch_users_data() {
        com_user_tb = $('#com_user_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_users'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                $(".switch-chk").bootstrapSwitch();
            },
            "columnDefs": [
                {"targets": [ 0,6,7,8,10 ], "orderable": false },
            ],
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "Ename2"},
                {"mData": "UserName"},
                {"mData": "gender_str"},
                {"mData": "date_join"},
                {"mData": "discharge_str"},
                {"mData": "login_act"},
                {"mData": "user_type_str"},
                {"mData": "last_login_str"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value': hidden_companyID});
                aoData.push({'name': 'user_status', 'value': $('#user_status').val()});
                aoData.push({'name': 'login_status', 'value': $('#login_status_drop').val()});
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

    function update_userType(type, EIdNo) {
        var initialVal = (parseInt(type.value) === 0)? 1: 0;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID':EIdNo, 'type': type.value, 'company_id': hidden_companyID},
            url: "<?= site_url('Dashboard/update_userType'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] != 's') {
                    $(type).val( initialVal );
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();                
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown); 
                $(type).val( initialVal );
            }
        });
    }

    function change_login_status(obj, userID, empName, user){
        let msg, log_status;
        if ($(obj).prop('checked')) {
            msg = 'activate';
            log_status = 0;
        } else {
            msg = 'inactivate';
            log_status = 4;
        }

        swal({
                title: "Are you sure?",
                text: "You want to "+msg+" the login of "+empName+" ("+user+")",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'userID': userID, 'company_id': hidden_companyID, 'type': log_status},
                        url: "<?php echo site_url('Dashboard/reset_login_attempts'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_users_data();
                            }
                        },
                        error: function () {
                            stopLoad();
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            let changeStatus = ( !$(obj).prop('checked') );
                            $('#login_status' + userID).prop('checked', changeStatus).change();
                        }
                    });
                }
                else {
                    let changeStatus = ( !$(obj).prop('checked') );
                    $('#login_status' + userID).prop('checked', changeStatus).change();
                }
            }
        );
    }

    function user_setup(userID, empName, userName){
        $('#user_setup_id').val(userID);
        $('#user_setup_name').val(empName);
        $('#user_setup_userName').val(userName);
        $('#password').val('');

        $('#user_setup_modal').modal('show');
    }

    function reset_password(){
        let post_data = $('#user_setup_form').serializeArray();
        post_data.push({'name': 'company_id', 'value': hidden_companyID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: post_data,
            url: "<?php echo site_url('Dashboard/user_password_rest'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#user_setup_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function new_outlet() {
        $('#new_outlet_frm')[0].reset();
        $('#newOutlet_modal').modal('show');
        $('#pos-related-div').hide();
        load_pos_dropDown_data();
    }

    function create_outlet() {
        let postDate = $('#new_outlet_frm').serializeArray();
        postDate.push({'name': 'company_id', 'value': hidden_companyID});

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/create_outlet'); ?>",
            data: postDate,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    warehouse_tb.ajax.reload();
                    $('#newOutlet_modal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_pos_dropDown_data() {
        let segment_drop = $('#pos_segment');
        let temp_drop = $('#posTemplateID');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/get_pos_template_master_drop'); ?>",
            data: {'company_id': hidden_companyID},
            cache: false,
            beforeSend: function () {
                startLoad();
                segment_drop.empty();
                temp_drop.empty();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    segment_drop.append( '<option value="">Select a segment</option>' );
                    temp_drop.append( '<option value="">Select a template</option>' );

                    if(data['drop_segment'].length > 0){
                        $.each(data['drop_segment'], function(i, val){
                            segment_drop.append( '<option value="'+val['segmentID']+'">'+val['description']+'</option>' );
                        });
                    }

                    if(data['drop_template'].length > 0){
                        $.each(data['drop_template'], function(i, val){
                            temp_drop.append( '<option value="'+val['posTemplateID']+'">'+val['posTemplateDescription']+'</option>' );
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function change_form_content(obj){
        if($(obj).val() == 1){
            $('#pos-related-div').fadeOut('slow');
        }
        else{
            $('#pos-related-div').fadeIn('slow');
        }
    }

    function change_warehouse_status(obj, id, wareHouse){
        let msg, status;
        if ($(obj).prop('checked')) {
            msg = 'activate';
            status = 1;
        } else {
            msg = 'inactivate';
            status = 0;
        }

        swal({
                title: "Are you sure?",
                text: "You want to "+msg+" the outlet "+wareHouse,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'warehouseID': id, 'company_id': hidden_companyID, 'status': status},
                        url: "<?php echo site_url('Dashboard/change_warehouse_status'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                        },
                        error: function () {
                            stopLoad();
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            let changeStatus = ( !$(obj).prop('checked') );
                            $('#warehouseStatus' + id).prop('checked', changeStatus).change();
                        }
                    });
                }
                else {
                    let changeStatus = ( !$(obj).prop('checked') );
                    $('#warehouseStatus' + id).prop('checked', changeStatus).change();
                }
            }
        );
    }
</script>
<?php
/**
 * Created by PhpStorm.
 * User: Nasik
 * Date: 5/15/2019
 * Time: 3:23 PM
 */
