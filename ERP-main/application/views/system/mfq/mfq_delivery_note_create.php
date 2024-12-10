<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$data_set = array(0 => array('estimateMasterID' => '', 'estimateDetailID' => '', 'bomMasterID' => '', 'mfqCustomerAutoID' => '', 'description' => '', 'mfqItemID' => '', 'unitDes' => '', 'type' => 1, 'itemDescription' => '', 'expectedQty' => 0, 'mfqSegmentID' => '', 'mfqWarehouseAutoID' => ''));
if ($data_arr) {
    $data_set = $data_arr;
}
$mainJobFilter = getPolicyValues('DNJF', 'All');
if(!isset($mainJobFilter)) {
    $mainJobFilter = 1;
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<!--<script src="<?php /*echo base_url('plugins/html5sortable/jquery.sortable.js'); */ ?>"></script>-->
<!--<link rel="stylesheet"
      href="<?php /*echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); */ ?>"/>-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .affix-content .container .page-header {
        margin-top: 0;
    }

    .affix-sidebar {
        padding-right: 0;
        font-size: small;
        padding-left: 0;
    }

    .affix-row, .affix-container, .affix-content {
        height: 100%;
        overflow: scroll;
        margin-left: 0;
        margin-right: 0;
    }

    .affix-content {
        background-color: white;
    }

    .sidebar-nav .navbar .navbar-collapse {
        padding: 0;
        max-height: none;
    }

    .sidebar-nav .navbar {
        border-radius: 0;
        margin-bottom: 0;
        border: 0;
    }

    .sidebar-nav .navbar ul {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li a {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    }

    @media (min-width: 769px) {
        .affix-content .container {
            width: 600px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 992px) {
        .affix-content .container {
            width: 900px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 1220px) {
        .affix-row {
            overflow: hidden;
        }

        .affix-content {
            overflow: auto;
        }

        .affix-content .container {
            width: 1000px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }

        .affix-content {
            padding-right: 30px;
            padding-left: 10px;
        }

        .affix-title {
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .navbar-nav {
            margin: 0;
        }

        .navbar-collapse {
            padding: 0;
        }

        .sidebar-nav .navbar li a:hover {
            background-color: #428bca;
            color: white;
        }

        .sidebar-nav .navbar li a > .caret {
            margin-top: 8px;
        }
    }

    .sidebar {
        padding-bottom: 0px;
    }

    div.bhoechie-tab-container {
        background-color: #ffffff;
        padding: 0 !important;
        border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ddd;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        -moz-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        background-clip: padding-box;
        opacity: 0.97;
        filter: alpha(opacity=97);
    }

    div.bhoechie-tab-menu {
        padding-right: 0;
        padding-left: 0;
        padding-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon,
    div.bhoechie-tab-menu div.list-group > a .fa {
        color: #E78800;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #555;
        border: 2px solid #555;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a:first-child {
        border-top-right-radius: 0;
        -moz-border-top-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a:last-child {
        border-bottom-right-radius: 0;
        -moz-border-bottom-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a.active,
    div.bhoechie-tab-menu div.list-group > a.active .glyphicon,
    div.bhoechie-tab-menu div.list-group > a.active .fa {
        background-color: #E78800;
        color: #ffffff;
    }

    div.bhoechie-tab-menu div.list-group > a.active .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #ffffff;
        border: 2px solid #ffffff;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a.active:after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        margin-top: -13px;
        border-left: 0;
        border-bottom: 13px solid transparent;
        border-top: 13px solid transparent;
        border-left: 10px solid #E78800;
    }

    div.bhoechie-tab-content {
        background-color: #ffffff;
        /* border: 1px solid #eeeeee; */
        padding-left: 20px;
        padding-top: 10px;
    }

    div.bhoechie-tab div.bhoechie-tab-content:not(.active) {
        display: none;
    }

    .list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
        border: 1px solid #ddd;
    }

    .bhoechie-tab {
        border: solid 2px #E78800;
        margin-left: -2px;
        margin-top: 1px;
        margin-bottom: 1px;
        min-height: 300px;
    }

    .disabledbutton {
        pointer-events: none;
    }

    .table-responsive {
        overflow: visible !important
    }

</style>
<style>
    .chkboxlabl {
        border: 1px solid #ccc;
        padding: 10px;
        margin: 0 0 10px;
        display: block;
        font-weight: normal;
    }

    .chkboxlabl:hover {
        background: #eee;
        cursor: pointer;
    }
    .increasedzindexclass {
        z-index: 999999;
    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('manufacturing_step_one_delivery_note_header') ?><!--Step 1 - Delivery Note Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_dn_confirmation()" data-toggle="tab">
        <?php echo $this->lang->line('manufacturing_step_two_delivery_note_confirmation') ?><!--Step 2 - Delivery Note Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="delivery_note_frm"'); ?>
        <input type="hidden" name="deliverNoteID" id="edit_deliverNoteID">

        <div class="row">
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                        <label class="title">
                            <?php echo $this->lang->line('manufacturing_customer_name') ?><!--Customer Name--></label>
                    </div>
                    <div class="form-group col-sm-7" style="margin-top: 10px;">
                        <span class="input-req"
                              title="Required Field"><?php echo form_dropdown('mfqCustomerAutoID', all_mfq_customer_drop(), $data_set[0]['mfqCustomerAutoID'], 'class="form-control select2" id="mfqCustomerAutoID"');
                            ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('manufacturing_delivery_date') ?><!--Delivery Date--></label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field"><div class="input-group datepic" id="dateStartDate">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="deliveryDate" id="deliveryDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control startDate" required>
                    </div>
                    <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <?php if ($mainJobFilter == 1) { ?>
                    <div class="row mainJob" style="margin-top: 10px;">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('manufacturing_main') ?>
                                <?php echo $this->lang->line('manufacturing_job') ?><!--Job--></label>
                        </div>
                        <div class="form-group col-sm-7">
                            <div class="input-req" title="Required Field">
                                <div id="div_loadCustomerMainJobs">
                                    <?php echo form_dropdown('mainJobID', 'Select Main Job', "", 'class="form-control select2" id="mainJobID"'); ?>
                                </div>
                                <span class="input-req-inner"></span>
                            </div>
                            <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                            <?php // echo form_dropdown('jobID', array("" => "Select"), "", 'class="form-control select2" id="jobID"'); ?>

                        </div>
                    </div>
                <?php } ?>

                
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('manufacturing_driver_name') ?><!--Driver Name--></label>
                    </div>
                    <div class="form-group col-sm-7">
                    <!-- <span class="input-req" title="Required Field"> -->
                        <input type="text" name="driverName" id="driverName"
                                                                      class="form-control"
                                                                      value=""
                    >
                    <!-- <span
                            class="input-req-inner"></span></span> -->
                    </div>
                </div>

            </div>
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                        <label class="title">Segment</label>
                    </div>
                    <div class="form-group col-sm-7" style="margin-top: 10px;">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('mfqsegmentID', fetch_mfq_segment(true), '', 'class="form-control select2" id="mfqsegmentID"');
                            ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <!--                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Delivery Note Code</label>
                                    </div>
                                    <div class="form-group col-sm-7">
                                <span class="input-req" title="Required Field"><input type="text" name="deliveryNoteCode"
                                                                                      id="deliveryNoteCode"
                                                                                      class="form-control"
                                                                                      value=""
                                    ><span
                                        class="input-req-inner"></span></span>
                                    </div>
                                </div>-->
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title"><?php echo $this->lang->line('manufacturing_job') ?><!--Job--></label>
                    </div>
                    <div class="form-group col-sm-7">
                        <div class="input-req" title="Required Field">
                            <div id="div_loadCustomerJobs">
                                <?php echo form_dropdown('jobID', 'Select All', "", 'class="form-control" id="jobID"'); ?>
                            </div>
                            <span class="input-req-inner"></span>
                        </div>
                        <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                        <?php // echo form_dropdown('jobID', array("" => "Select"), "", 'class="form-control select2" id="jobID"'); ?>

                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('manufacturing_vehicle_no') ?><!--Vehicle No--></label>
                    </div>
                    <div class="form-group col-sm-7">
                <!-- <span class="input-req" title="Required Field"> -->
                    <input type="text" name="vehicleNo" id="vehicleNo"
                                                                      class="form-control"
                                                                      value=""
                    >
                    <!-- <span
                            class="input-req-inner"></span></span> -->
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('manufacturing_mobile_no') ?><!--Mobile No--></label>
                    </div>
                    <div class="form-group col-sm-7">
                <!-- <span class="input-req" title="Required Field"> -->
                    <input type="text" name="mobileNo" id="mobileNo"
                                                                      class="form-control"
                                                                      value=""
                    >
                    <!-- <span
                            class="input-req-inner"></span></span> -->
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">
                            <?php echo $this->lang->line('common_comments') ?><!--comments--></label>
                    </div>
                    <div class="form-group col-sm-7">
                        <input type="text" name="comment" id="comment" class="form-control" value="">
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_notes'); ?> </label><!--Notes-->
            </div>
            <div class="form-group col-sm-10" style="z-index: 0;">
                <textarea class="form-control notes_delivery" rows="30" name="invoiceNote" id="invoiceNote"><span style="color: white">.</span></textarea>
                <button class="btn btn-primary" type="button" onclick="open_all_notes('MDN')" style="margin-top: 5px;">
                    <i class="fa fa-bookmark" aria-hidden="true"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit" id="saveJob">
                        <?php echo $this->lang->line('common_save') ?><!--Save--></button>
                </div>
            </div>
        </div>
        </form>
        <br>
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>Delivery Item Details</h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="col-sm-12">
                    <div id="deliveryNote_details"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">
                <?php echo $this->lang->line('common_previous') ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft') ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm') ?><!--Confirm--></button>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('manufacturing_stock_insufficient'); ?><!--Stock Insufficient--></h4>
            </div>
            <div class="modal-body">
                <div>
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('manufacturing_item_code'); ?><!--Item Code--></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th><?php echo $this->lang->line('manufacturing_current_stock'); ?><!--Current Stock--></th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="all_notes_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Notes</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="all_notes_form" class="form-group">
                    <div id="allnotebody">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="save_notes()">
                    <?php echo $this->lang->line('common_add_note'); ?><!--Add Note--></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); ?>"></script>

<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var deliverNoteID;
    var jobID = '';
    var mainJobFilter_policy = <?php echo $mainJobFilter; ?>;
    var CustomerAutoID = '';
    var segmentID = '';
    var mainjobID = '';

    $(document).ready(function () {
        //init tynimce
        tinyMCE.init({
            selector: ".notes_delivery",
            height: 400,
            browser_spellcheck: true,
            cleanup: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",
            menubar: false,
            toolbar_items_size: 'small'
        });

        //tinyMCE.get("invoiceNote").setContent('');
        $(".select2").select2();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('#jobID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '100%',
            maxHeight: '30px'
        });

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_delivery_note', '', 'Delivery Note');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        

        deliverNoteID = null;
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            deliverNoteID = p_id;
            load_delivery_note_header();
        } else {
            load_default_note('MDN');
            $('.btn-wizard').addClass('disabled');
        }

        $('#delivery_note_frm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                mfqCustomerAutoID: {validators: {notEmpty: {message: 'Customer is required.'}}},
                // jobID: {validators: {notEmpty: {message: 'Job is required.'}}},
                deliveryDate: {validators: {notEmpty: {message: 'Delivery Date is required.'}}},
                // driverName: {validators: {notEmpty: {message: 'Driver Name is required.'}}},
                // mobileNo: {validators: {notEmpty: {message: 'Mobile No is required.'}}},
                // vehicleNo: {validators: {notEmpty: {message: 'Vehicle No is required.'}}},
                // invoiceNote: {validators: {notEmpty: {message: 'Note is required.'}}}
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_DeliveryNote/save_delivery_note_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        deliverNoteID = data[2];
                        load_delivery_note_details();
                        // $('.btn-wizard').removeClass('disabled');
                        // $('[href=#step2]').tab('show');
                        // load_dn_confirmation();
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

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

        $("#mfqCustomerAutoID").change(function () {
            CustomerAutoID = $(this).val();
            segmentID = $('#mfqsegmentID').val();
            if (mainJobFilter_policy == 1) {
                get_customer_main_jobs($(this).val(), segmentID)
            } else {
                get_customer_jobs($(this).val(), segmentID)
            }
        });
        $("#mfqsegmentID").change(function () {
            CustomerAutoID = $('#mfqCustomerAutoID').val();
            segmentID = $(this).val();
            if (mainJobFilter_policy == 1) {
                get_customer_main_jobs(CustomerAutoID, $(this).val())
            } else {
                get_customer_jobs(CustomerAutoID, $(this).val())
            }
        });


    });

    function load_delivery_note_header() {
        if (deliverNoteID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {deliverNoteID: deliverNoteID},
                url: "<?php echo site_url('MFQ_DeliveryNote/load_delivery_note_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        deliverNoteID = data['header']['deliverNoteID'];

                        $('#edit_deliverNoteID').val(deliverNoteID);
                        $('#mfqCustomerAutoID').val(data['header']['mfqCustomerAutoID']).change();
                        $('#deliveryDate').val(data['header']['deliveryDate']).change();
                        $('#mfqsegmentID').val(data['header']['mfqSegmentID']).change();
                        mainjobID = data['main_job_id'];
                        load_delivery_note_details();
                        jobID = data['jobs'];
                        $('#driverName').val(data['header']["driverName"]);
                        $('#mobileNo').val(data['header']["mobileNo"]);
                        $('#deliveryNoteCode').val(data['header']["deliveryNoteCode"]);
                        $('#vehicleNo').val(data['header']["vehicleNo"]);
                        tinyMCE.get("invoiceNote").setContent(data['header']["note"]);
                        $('#comment').val(data['header']["comment"]);

                        load_dn_confirmation();

                        // setTimeout(function () {
                        //     load_default_note('MDN');
                        // }, 300);



                        // $('[href=#step2]').tab('show');

                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function load_dn_confirmation() {
        if (deliverNoteID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'deliverNoteID': deliverNoteID, 'html': true},
                url: "<?php echo site_url('MFQ_DeliveryNote/load_deliveryNote_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (deliverNoteID) {
            swal({
                    title: "Are you sure?",
                    text: "You want confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'deliverNoteID': deliverNoteID},
                        url: "<?php echo site_url('MFQ_DeliveryNote/delivery_note_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            stopLoad();
                            if (data[0] == 's') {
                                fetchPage('system/mfq/mfq_delivery_note', '', 'Delivery Note');
                            }
                            if (data[2]) {
                                $("#jv_modal").modal('hide');
                                $('#errormsg').empty();
                                $.each(data[2], function (key, value) {
                                    $('#errormsg').append('<tr><td>' + value['itemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['currentStock'] + '</td></tr>');
                                });
                                $('#invalidinvoicemodal').modal('show');
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (deliverNoteID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/mfq/mfq_delivery_note', deliverNoteID, 'Delivery Note');
                });
        }
    }

    function get_customer_main_jobs(mfqCustomerID, segmentAutoID) {
        if (mfqCustomerID && segmentAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    mfqCustomerAutoID: mfqCustomerID,
                    mfqSegmentID: segmentAutoID
                },
                url: "<?php echo site_url('MFQ_DeliveryNote/fetch_customer_main_jobs'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loadCustomerMainJobs').html('');
                    $('#div_loadCustomerMainJobs').html(data);
                    $(".select2").select2();

                    if(mainjobID){
                      $('#mainjobID').val(mainjobID).change();
                    }

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function get_customer_jobs(mfqCustomerID = '', segmentAutoID = '', mainjobID = '') {
        if (jQuery.isEmptyObject(mfqCustomerID)) {
            mfqCustomerID = CustomerAutoID;
        }
        if (jQuery.isEmptyObject(segmentAutoID)) {
            segmentAutoID = segmentID;
        }
        if (mfqCustomerID && segmentAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    mfqCustomerAutoID: mfqCustomerID,
                    mfqSegmentID: segmentAutoID,
                    mainjobID: mainjobID
                },
                url: "<?php echo site_url('MFQ_DeliveryNote/fetch_customer_jobs'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loadCustomerJobs').html('');
                    $('#div_loadCustomerJobs').html(data);
                    $('#jobID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '100%',
                        maxHeight: '30px'
                    });

                    $('#jobID').multiselect2("deselectAll", false).multiselect2("refresh");
                    if (!$.isEmptyObject(data)) {
                        if (jobID) {
                            $.each(jobID, function (k, text) {
                                $('#jobID').multiselect2('select', text['jobID']);
                            });
                        }
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function load_delivery_note_details() {

        if (deliverNoteID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'deliverNoteID': deliverNoteID},
                url: "<?php echo site_url('MFQ_DeliveryNote/load_deliveryNote_detail_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#deliveryNote_details').empty();
                    $('#deliveryNote_details').html(data);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function add_delivery_note_details() {
        var data = $('#delivery_note_details_frm').serializeArray();
        data.push({'name': 'deliverNoteID', 'value': deliverNoteID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_DeliveryNote/save_delivery_note_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.btn-wizard').removeClass('disabled');
                    $('[href=#step2]').tab('show');
                    load_dn_confirmation();
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_Delivery_order_Detail(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'deliveryNoteDetailID': id},
                    url: "<?php echo site_url('MFQ_DeliveryNote/delete_delivery_note_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_delivery_note_details();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function open_all_notes(docid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': docid},
            url: "<?php echo site_url('Invoices/open_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#allnotebody').empty();
                    var x = 1;
                    $.each(data, function (key, value) {
                        $('#allnotebody').append('<label class="chkboxlabl" ><input type="radio" name="allnotedesc" value="' + value['autoID'] + '" id="chkboxlabl_' + value['autoID'] + '">' + value['description'] + '</label>')
                        x++;
                    });
                    $("#all_notes_modal").modal({backdrop: "static"});
                } else {
                    myAlert('w', 'No Notes assigned')
                }
            }
        });
    }

    function save_notes() {
        var data = $("#all_notes_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/load_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                tinyMCE.get("invoiceNote").setContent('');
                tinyMCE.get("invoiceNote").setContent(data['description']);
                $("#all_notes_modal").modal('hide');
            }, error: function () {
                stopLoad();
            }
        });
    }

    function load_default_note(docid) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': docid},
            url: "<?php echo site_url('Invoices/load_default_note'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    setTimeout(function () {
                        tinyMCE.get("invoiceNote").setContent(data['description']);
                    }, 300);
                }else{
                    tinyMCE.get("invoiceNote").setContent('');
                }
            }, error: function () {
                stopLoad();
            }
            });
    }
</script>