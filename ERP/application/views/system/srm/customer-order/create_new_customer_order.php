<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('srm_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customerArr = all_srm_customers();
$supplierArr = all_supplier_drop();
$currency_arr = all_currency_new_drop();
$status_arr = all_customer_order_status();
$umo_arr = array('' => 'Select UOM');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .posts-holder {
        padding: 0 0 10px 4px;
        margin-right: 10px;
    }

    #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        height: 20px;
    }

    .past-info {
        background: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 0 0 8px 10px;
        margin-left: 2px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .custome {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
    }

    .customestyle {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -46%
    }

    .customestyle2 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    .customestyle3 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;

        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }
</style>
<div class="set-poweredby">Powered by &nbsp;<a href=""><img src="https://ilooopssrm.rbdemo.live/images/logo-dark.png" width="75" alt="MaxSRM"></a></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('srm_step_one');?><!--Step 1--> - <?php echo $this->lang->line('srm_order_header');?><!--Order Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="order_multiple_attachemts()" data-toggle="tab"><?php echo $this->lang->line('srm_step_two');?><!--Step 2--> - <?php echo $this->lang->line('srm_order_attachments');?><!--Order Attachments--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="customer_order_form"'); ?>
        <div class="row">
            <div class="col-md-12">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('srm_customer_details');?><!--CUSTOMER DETAILS--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('srm_document_id');?><!--Document ID--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="documentAutoGeneratedID" id="documentAutoGeneratedID"
                               class="form-control"
                               readonly>
                        <input type="hidden" name="customerOrderID" id="customerOrderID_edit">
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Inquiry Date</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                    value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                            </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                        </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_reference_number');?><!--Reference Number--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="ref_number" id="ref_number" class="form-control">
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                        <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">BID Start Date</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="bid_start_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                    value="<?php echo $current_date; ?>" id="bid_start_date" class="form-control">
                            </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">BID End Date</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="bid_end_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                    value="<?php echo $current_date; ?>" id="bid_end_date" class="form-control">
                            </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;" >
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3" style="margin-top: 10px;">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('customerID', $customerArr, '', 'class="form-control select2" id="customerID" onchange="load_customer_BaseDetail()" required');?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Customer Ref.No.<!--Customer Reference Number--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="cus_ref_number" id="cus_ref_number" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('srm_customer_phone_no');?><!--Customer Phone No--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                            <input type="text" name="customerTelephone" id="customerTelephone" class="form-control">
                            
                        </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_address');?><!--Address--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                                <textarea class="form-control" rows="3"
                                    name="CustomerAddress1"
                                    id="CustomerAddress1"
                                    required></textarea>
                                <span>
                                
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('srm_expiry_date');?><!--Expiry Date--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="expiryDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="expiryDate" class="form-control" required>
                            </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                        </span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_narration');?><!--Narration--></label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <textarea class="form-control" rows="3"
                                      name="narration" id="narration"></textarea>
                    </div> 
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Supplier</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3" style="margin-top: 10px;">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('supplierID', $supplierArr, '', 'class="form-control select2" id="supplierID" required');?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Registered By</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="registered_by" id="registered_by" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label for="enable_back" class="title">Enable Back to Back</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="title">:</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input id="enable_back" type="checkbox" data-caption="" class="columnSelected" name="enable_back" value="1">
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row" id="customerItemDetail_div">
            <div class="col-md-12">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('srm_customer_item_details');?><!--CUSTOMER ITEM DETAILS--></h2>
                </header>
                <div class="row">
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                    <div class="col-sm-10 text-right">
                        <button type="button" class="btn btn-primary "
                                onclick="customer_order_detail_modal()">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_item');?><!--Item-->
                        </button>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                    <div class="col-sm-10">
                        <div id="taskMaster_view"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
        </form>
        <div class="row">
            <div class="col-sm-11">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary-new size-lg" onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
                    <button class="btn btn-success-new size-lg" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
                </div>
            </div>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('srm_order_attachments');?><!--Order Attachments--> </h4></div>
            <div class="col-md-4">
                <button type="button" onclick="show_order_button()" class="btn btn-primary-new size-sm pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('srm_add_attachment');?><!--Add Attachment-->
                </button>
            </div>
        </div>
        <div class="row hide" id="add_attachemnt_show">
            <?php echo form_open_multipart('', 'id="order_attachment_uplode_form" class="form-inline"'); ?>
            <div class="col-sm-10" style="margin-left: 3%">
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="taskattachmentDescription"
                               name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                        <input type="hidden" class="form-control" id="documentID" name="documentID" value="3">
                        <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                               value="customerOrder">
                        <input type="hidden" class="form-control" id="order_documentAutoID" name="documentAutoID">
                    </div>
                </div>
                <div class="col-sm-8" style="margin-top: -8px;">
                    <div class="form-group">
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                             style="margin-top: 8px;">
                            <div class="form-control" data-trigger="fileinput"><i
                                    class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                    class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                            <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                               data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                              aria-hidden="true"></span></a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                            class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <br>

        <div id="order_multiple_attachemts"></div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="customer_order_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('srm_add_item_detail');?><!--Add Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="customer_order_detail_form" class="form-horizontal">
                    <input type="hidden" class="" id="customerOrderID_orderDetail"
                           name="customerOrderID_orderDetail">
                    <input type="hidden" class="" id="customerOrderDetailsID_edit"
                           name="customerOrderDetailsID_edit">
                    <table class="table table-bordered table-condensed no-color" id="customer_order_detail_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('srm_item_code');?><!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom');?><!--UOM--> <?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('common_qty');?><!--Qty--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('srm_expected_price');?><!--Expected Price--> <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('srm_expected_delivery_date');?><!--Expected Delivery Date--><?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control f_search"
                                       name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id');?>,<?php echo $this->lang->line('common_item_description');?>..." id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <td><input type="text" name="quantityRequested[]" value="0" onkeyup="change_qty(this)"
                                       class="form-control number quantityRequested" onfocus="this.select();"
                                       required>
                            </td>
                            <td><input type="text" name="estimatedAmount[]" value="0" placeholder="0.00"
                                       onkeyup="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number estimatedAmount" onfocus="this.select();"></td>
                            <td style="width:140px">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <!--<input type="text" name="expectedDeliveryDate[]"
                                           data-inputmask="'alias': 'dd-mm-yyyy'" value=""
                                           class="form-control expectedDeliveryDate" required="">-->
                                    <input type="text" name="expectedDeliveryDate[]" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" class="form-control">
                                </div>
                            </td>
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('srm_item_comment');?>..."></textarea></td><!--Item Comment-->
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="saveCustomerOrderDetails()"><?php echo $this->lang->line('common_save_change');?><!--Save changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var search_id = 1;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customer_order', '', 'Customer Order')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            customerOrderID = p_id;
            load_customerOrder_header();

        } else {
            $('.btn-wizard').addClass('disabled');
            save_customer_order();
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

    });

    function getCustomerOrderItem_tableView(customerOrderID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerOrderID: customerOrderID},
            url: "<?php echo site_url('srm_master/load_customer_order_detail_item_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taskMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function customer_order_detail_modal() {

        $('.f_search').typeahead('destroy');
        customerOrderDetailsID = null;
        $('#customer_order_detail_form')[0].reset();
        $('#discount').val(0);
        $('#discount_amount').val(0);
        $('#customer_order_detail_table tbody tr').not(':first').remove();
        $('.net_amount,.net_unit_cost').text('0.00');
        $('.f_search').typeahead('val', '');
        $('.itemAutoID').val('');
        initializeitemTypeahead(1);
        $("#customer_order_detail_modal").modal({backdrop: "static"});

    }

    function initializeitemTypeahead(id) {

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
            }
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function add_more() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#customer_order_detail_table tbody tr:first').clone();
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#customer_order_detail_table').append(appendData);
        var lenght = $('#customer_order_detail_table tbody tr').length - 1;
        $(".select2").select2();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        number_validation();
        initializeitemTypeahead(search_id);
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function load_customer_order_autoGeneratedID(customerOrderID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {customerOrderID: customerOrderID},
            url: "<?php echo site_url('srm_master/load_customer_order_autoGeneratedID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#documentAutoGeneratedID').val(data[0]);
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_customer_order() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('srm_master/save_customer_ordermaster_add'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    $('#customerOrderID_edit').val(data[2]);
                    $('#customerOrderID_orderDetail').val(data[2]);
                    load_customer_order_autoGeneratedID(data[2]);
                    getCustomerOrderItem_tableView(data[2]);
                    $('#order_documentAutoID').val(data[2]);
                    order_multiple_attachemts();
                    //$('.btn-wizard').removeClass('disabled');
                    //$('[href=#step2]').tab('show');
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function saveCustomerOrderDetails() {
        var customerOrderID = $('#customerOrderID_orderDetail').val();
        var data = $('#customer_order_detail_form').serializeArray();
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('srm_master/save_customer_order_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getCustomerOrderItem_tableView(customerOrderID);
                        $('#customer_order_detail_modal').modal('hide');
                    }
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
            });

    }

    function load_customerOrder_header() {
        if (customerOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'customerOrderID': customerOrderID},
                url: "<?php echo site_url('srm_master/load_customerOrder_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#customerOrderID_edit').val(customerOrderID);
                        $('#customerOrderID_orderDetail').val(customerOrderID);
                        $('#order_documentAutoID').val(customerOrderID);
                        $('#documentAutoGeneratedID').val(data['header']['customerOrderCode']);
                        $('#customerID').val(data['header']['customerID']).change();
                        $('#customerTelephone').val(data['header']['contactPersonNumber']);
                        $('#CustomerAddress1').val(data['header']['CustomerAddress']);
                        $('#documentDate').val(data['header']['documentDate']);
                        $('#transactionCurrencyID').val(data['header']['transactionCurrencyID']).change();
                        $('#ref_number').val(data['header']['referenceNumber']);
                        $('#narration').val(data['header']['narration']);
                        $('#bid_start_date').val(data['header']['bidStartDate']);
                        $('#bid_end_date').val(data['header']['bidEndDate']);
                        $('#cus_ref_number').val(data['header']['customerReferenceNumber']);
                        $('#supplierID').val(data['header']['supplierID']).change();
                        $('#registered_by').val(data['header']['registeredBy']);
                        $('#expiryDate').val(data['header']['expiryDate']);

                        if(data['header']['isBackToBack'] == 1){
                            $('#enable_back').prop('checked',true);
                        }else{
                            $('#enable_back').prop('checked',false);
                        }

                        getCustomerOrderItem_tableView(customerOrderID);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var data = $('#customer_order_form').serializeArray();
                data.push({'name': 'confirmedYN', 'value': 1});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srm_master/save_customer_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/srm/srm_customer_order', '', 'Customer Order');
                        } else {

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_draft() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('srm_you_want_to_save_this_customer_order');?>",/*You want to save this Customer Order!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var data = $('#customer_order_form').serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srm_master/save_customer_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/srm/srm_customer_order', '', 'Customer Order');
                        } else {

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function load_customer_BaseDetail() {
        var customerID = $('#customerID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/load_customer_BaseDetail"); ?>',
            dataType: 'json',
            data: {'customerID': customerID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#transactionCurrencyID').val(data['customerCurrencyID']).change();
                    $('#customerTelephone').val(data['customerTelephone']);
                    $('#CustomerAddress1').val(data['CustomerAddress1']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function show_order_button() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function order_multiple_attachemts() {
        var customerOrderID = $('#customerOrderID_edit').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerOrderID: customerOrderID},
            url: "<?php echo site_url('srm_master/load_order_multiple_attachemts'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#order_multiple_attachemts').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function document_uplode() {
        var formData = new FormData($("#order_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('srm_master/attachement_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#taskattachmentDescription').val('');
                    order_multiple_attachemts();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function delete_srm_attachment(id, fileName) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('srm_you_want_to_delete');?>",/*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('srm_master/delete_srm_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            order_multiple_attachemts();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function delete_order_detail(customerOrderDetailsID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('srm_you_want_to_delete');?>",/*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'customerOrderDetailsID': customerOrderDetailsID},
                    url: "<?php echo site_url('srm_master/delete_customer_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            getCustomerOrderItem_tableView(customerOrderID);
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

</script>