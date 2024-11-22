<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('srm_order_review');
echo head_page($title, false);


/*echo head_page('Order Review', false);*/
$this->load->helper('srm_helper');
$order_inquiry_arr = all_order_inquiries();
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
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('srm_order_review_header');?><!-- ORDER REVIEW HEADER--> </h2>
        </header>
    </div>
</div>
<input type="hidden" name="orderreviewID" id="orderreviewID">
<!-- <div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="float-right"> <?php echo $this->lang->line('srm_inquiry_id');?> </label>
    </div>
    <div class="form-group col-sm-4">
        <?php echo form_dropdown('inquiryID', $order_inquiry_arr, '', 'class="form-control select2" id="inquiryID" onchange="load_customerInquiry_header()"');
        ?>
    </div>
    <div class="form-group col-sm-2">
        <label class="float-right"> <?php echo $this->lang->line('common_narration');?></label>
    </div>
    <div class="form-group col-sm-4">
        <input type="text" name="narration" id="narration" class="form-control">
    </div>
</div> -->
<!-- <div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2 custyp">
        <label class="float-right"> <?php echo $this->lang->line('common_customer_name');?></label>
    </div>
    <div class="form-group col-sm-4 custyp">
        <input type="text" name="customerName" id="customerName" class="form-control" readonly>
        <input type="hidden" name="CustomerAutoID" id="CustomerAutoID" class="form-control">
    </div>

    <div class="form-group col-sm-2 prqtyp hidden">
        <label class="float-right"> Segment</label>
    </div>
    <div class="form-group col-sm-4 prqtyp hidden">
        <input type="text" name="segment" id="segment" class="form-control" readonly>
    </div>

    <div class="form-group col-sm-2">
        <label class="float-right"> <?php echo $this->lang->line('srm_reference_number');?> </label>
    </div>
    <div class="form-group col-sm-4">
        <input type="text" name="referanceNumber" id="referanceNumber" class="form-control">
    </div>
</div> -->

<!-- <div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2 ">
        <label class="float-right"> Template</label>
    </div>
    <div class="form-group col-sm-4 ">
        <select name="template" class="form-control " id="template">
            <option value="1">Item Base</option>
            <option value="2">Supplier Base</option>
        </select>
    </div>


</div> -->

<!-- <div class="row Analysebtn" style="margin-top: 10px;">
    <div class="form-group col-sm-12">
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg " type="button" onclick="orderReview_analyse()">Generate</button>
        </div>
    </div>
</div> -->

<!-----------------order review new section-------------------------->
<div class="customer_master_style">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="btn-default-new size-sm tab-style-one mr-1 active" id="pending-li"><a href="#or_ongoing" data-toggle="tab" onclick="Otable2.draw()">Open/Ongoing</a></li>
        <li class="btn-default-new size-sm tab-style-one mr-1 "><a href="#or_completed" data-toggle="tab" onclick="Otable1.draw()">Completed</a></li>
       
        <!-- <li class="btn-default-new size-sm tab-style-one" id="statement-li"><a href="#statement" data-toggle="tab">COMPARATIVE STATEMENT</a></li> -->
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane " id="or_completed">
        <div class="table-responsive">
            <table id="or_completed_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>

                        <th style="width: 15%">Doc Number</th>

                        <th style="width: 20%">Date</th>
                        
                        <th style="width: 12%">Narration</th>
                        
                        <th style="width: 20%">RFQ</th>                
                        
                      
                        
                        <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                        <!--Action-->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane active" id="or_ongoing">
        <div class="table-responsive">
            <table id="or_ongoing_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>

                        <th style="width: 15%">Doc Number</th>

                        <th style="width: 20%">Date</th>
                        
                        <th style="width: 12%">Narration</th>
                        
                        <th style="width: 20%">RFQ</th>                
                        
                        <th style="width: 5%">Status</th>
                        
                        <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                        <!--Action-->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- <div class="tab-pane" id="statement">
        <div id="inquiryDetailView">NO COMPARATIVE STATEMENT GENERATED</div>
    </div> -->
</div>

<!-----------------order review new section-------------------------->
<br>
<div id="inquiryDetailView"></div>



<script type="text/javascript">
    var Otable1;
    var Otable2;
    orderreviewID = null;
    inquiryID = null;
    var supplierReviewSync = [];
    var supplierBaseReviewSync = [];

    load_order_review_srm_completed();
    load_order_review_srm_pending();

    $(document).ready(function () {
        $('.carousel').carousel({
            interval: false
        });

        $('.select2').select2();

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        polcy_id = <?php echo json_encode(trim($this->input->post('policy_id'))); ?>;

        if (p_id) {
            orderreviewID = p_id;
            inquiryID = polcy_id;
            $('#orderreviewID').val(orderreviewID);
            $('#inquiryID').val(inquiryID).change();
            $('.Analysebtn').addClass('hidden');
            view_supplier_AssignModel();
        } else {
            $('.Analysebtn').removeClass('hidden');
        }
    });

    function load_customerInquiry_header() {
        var inquiryID = $('#inquiryID').val();
        var orderreviewID = $('#orderreviewID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {inquiryID: inquiryID,orderreviewID: orderreviewID},
            url: "<?php echo site_url('srm_master/load_inquiry_reviewHeader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#narration').val(data['narration']);
                    $('#referanceNumber').val(data['referenceNumber']);

                    if(data['inquiryType']=='PRQ'){
                        $('.prqtyp').removeClass('hidden');
                        $('.custyp').addClass('hidden');
                        $('#segment').val(data['segmentCode']+' | '+data['segdescription']);
                    }else{
                        $('.prqtyp').addClass('hidden');
                        $('.custyp').removeClass('hidden');
                        $('#customerName').val(data['CustomerName']);
                        $('#CustomerAutoID').val(data['CustomerAutoID']);
                    }
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function orderReview_analyse(){
        create_order_review_header();
    }

    function create_order_review_header() {
        var inquiryID = $('#inquiryID').val();
        var narration = $('#narration').val();
        var customerName = $('#customerName').val();
        var CustomerAutoID = $('#CustomerAutoID').val();
        var referanceNumber = $('#referanceNumber').val();
       // var template = $('#template').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {inquiryID: inquiryID,narration: narration,referanceNumber: referanceNumber,customerName: customerName},
            url: "<?php echo site_url('srm_master/create_order_review_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if(data[0]=='s'){
                    $('#orderreviewID').val(data[2]);
                    orderreviewID = data[2];
                    inquiryID = inquiryID;
                    $('.Analysebtn').addClass('hidden');
                    view_supplier_AssignModel();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function view_supplier_AssignModel() {
        var inquiryID = $('#inquiryID').val();
        var narration = $('#narration').val();
        var customerName = $('#customerName').val();
        var CustomerAutoID = $('#CustomerAutoID').val();
        var referanceNumber = $('#referanceNumber').val();
        var template = $('#template').val();
        $('#inquiryDetailView').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryID,orderreviewID: orderreviewID,template:template},
            url: "<?php echo site_url('srm_master/load_order_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#inquiryDetailView').html(data);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function orderItem_selected_check(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            insert_review_detail(value,'checked');

            var inArray = $.inArray(value, supplierReviewSync);
            if (inArray == -1) {
                supplierReviewSync.push(value);
            }
        }
        else {
            insert_review_detail(value,'unchecked');

            var i = supplierReviewSync.indexOf(value);
            if (i != -1) {
                supplierReviewSync.splice(i, 1);
            }

        }
    }

    function orderItem_selected_check_supplier_base(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            insert_review_detail_supplier_base(value,'checked');

            var inArray = $.inArray(value, supplierBaseReviewSync);
            if (inArray == -1) {
                supplierBaseReviewSync.push(value);
            }
        }
        else {
            insert_review_detail_supplier_base(value,'unchecked');

            var i = supplierBaseReviewSync.indexOf(value);
            if (i != -1) {
                supplierBaseReviewSync.splice(i, 1);
            }

        }
    }

    function generate_review_supplier() {
        var inquiryID = $('#inquiryID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierReviewSync': supplierReviewSync,inquiryID: inquiryID},
            url: "<?php echo site_url('srm_master/generate_order_review_supplier'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    view_supplier_AssignModel();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function insert_review_detail_supplier_base(valu,actn) {
       var orderreviewID= $('#orderreviewID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'valu': valu,actn: actn,orderreviewID: orderreviewID},
            url: "<?php echo site_url('srm_master/insert_review_detail_supplier_base'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    //view_supplierAssignModel();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function insert_review_detail(valu,actn) {
       var orderreviewID= $('#orderreviewID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'valu': valu,actn: actn,orderreviewID: orderreviewID},
            url: "<?php echo site_url('srm_master/insert_review_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    //view_supplierAssignModel();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function confirm_order_review() {
        if (orderreviewID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'orderreviewID': orderreviewID},
                        url: "<?php echo site_url('srm_master/confirm_order_review'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0]=='s'){
                                fetchPage('system/srm/srm_order_review_management', '', 'Order Review Master');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function load_order_review_srm_completed(){

        Otable1 = $('#or_completed_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('srm_master/fetch_order_review_srm'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
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
            "aoColumns": [
                {"mData": "inquiryID"},
                {"mData": "documentCode"},
                {"mData": "documentDate"},
                {"mData": "narration"},
                {"mData": "rfq"},
               
                {"mData": "action"}
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            
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

    function load_order_review_srm_pending(){

        Otable2 = $('#or_ongoing_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('srm_master/fetch_selfservice_order_review_srm_pending'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
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
            "aoColumns": [
                {"mData": "inquiryID"},
                {"mData": "documentCode"},
                {"mData": "documentDate"},
                {"mData": "narration"},
                {"mData": "rfq"},
                {"mData": "status"},
                {"mData": "action"}
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
            
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

</script>
