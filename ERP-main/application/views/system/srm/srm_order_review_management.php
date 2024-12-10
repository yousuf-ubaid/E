<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page('Order Review', false);

/*echo head_page('Customer Order', false);*/
$this->load->helper('srm_helper');
$date_format_policy = date_format_policy();
$status_arr_filter = all_customer_order_status(false);
$customer_arr_filter = all_srm_customers(true);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
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
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
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
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="set-poweredby">Powered by &nbsp;<a href=""><img src="https://ilooopssrm.rbdemo.live/images/logo-dark.png" width="75" alt="MaxSRM"></a></div>
<div class="row hide">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary "
                onclick="fetchPage('system/srm/srm_order_review',null,'Add Order Review','SRM');">
            <i class="fa fa-plus"></i> Order Review
        </button>
    </div>
</div>
<div class="row hide">
    <div class="col-md-12">
        <div class="box-body">
            <form id="searchForm">
                <div class="row" style="margin: 6px 0px;">
                    <div class="col-sm-3">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchOrder" type="text" class="form-control input-sm"
                                       placeholder="Search Customer Order"
                                       id="searchOrder" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div id="OrderReviewMaster_table" class="table-responsive mailbox-messages"></div>
        </div>
    </div>
</div>

<!-----------------order review new section-------------------------->
<div class="customer_master_style">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#or_completed" data-toggle="tab" onclick="Otable.draw()">Completed</a></li>
        <li class="btn-default-new size-sm tab-style-one mr-5" id="pending-li"><a href="#or_ongoing" data-toggle="tab" onclick="Otable.draw()">Open/Ongoing</a></li>
        <li class="btn-default-new size-sm tab-style-one" id="statement-li"><a href="#statement" data-toggle="tab">COMPARATIVE STATEMENT</a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="or_completed">
        <div class="table-responsive">
            <table id="or_completed_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>

                        <th style="width: 15%">Doc Number</th>

                        <th style="width: 20%">Date</th>
                        
                        <th style="width: 12%">Narration</th>
                        
                        <th style="width: 20%">Ref Number</th>                
                        
                        <th style="width: 5%">Status</th>
                        
                        <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                        <!--Action-->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="or_ongoing">
        <div class="table-responsive">
            <table id="or_ongoing_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>

                        <th style="width: 15%">Doc Number</th>

                        <th style="width: 20%">Date</th>
                        
                        <th style="width: 12%">Narration</th>
                        
                        <th style="width: 20%">Ref Number</th>                
                        
                        <th style="width: 5%">Status</th>
                        
                        <th style="width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                        <!--Action-->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="statement">
        <div id="inquiryDetailView">NO COMPARATIVE STATEMENT GENERATED</div>
    </div>
</div>

<!-----------------order review new section-------------------------->


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    orderreviewID = null;
    inquiryID = null;
    var supplierReviewSync = [];
    var supplierBaseReviewSync = [];

    var Otable;
    load_order_review_srm_completed();
    load_order_review_srm_pending();

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_order_review', '', 'Order Review Master');
        });

        getOrderReviewManagement_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });

    function getOrderReviewManagement_tableView() {
        var postData = $('#searchForm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: postData,
            url: "<?php echo site_url('Srm_master/getOrderReviewManagement_tableView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#OrderReviewMaster_table').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#OrderReviewMaster_table').html('<br>Message: ' + errorThrown);
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getCustomerOrderManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#filter_confirmedYN').val('-1');
        $('#searchOrder').val('');
        $('#filter_statusID').val('');
        $('#filter_customerID').val('');
        getCustomerOrderManagement_tableView();
    }

    function delete_customer_order_review(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'orderreviewID': id},
                    url: "<?php echo site_url('Srm_master/delete_customer_order_review'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getOrderReviewManagement_tableView();
                        myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referbacksrmordrew(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>", /*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'orderreviewID': id},
                    url: "<?php echo site_url('srm_master/referback_order_review'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getOrderReviewManagement_tableView();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    
    function load_order_review_srm_completed(){

        Otable = $('#or_completed_table').DataTable({
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
                {"mData": "referenceNumber"},
                {"mData": "isRfqSubmitted"},
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

        Otable = $('#or_ongoing_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('srm_master/fetch_order_review_srm_pending'); ?>",
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
                {"mData": "referenceNumber"},
                {"mData": "isRfqSubmitted"},
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

    function orderReview_analyse(inquiryID,narration,referanceNumber){
        create_order_review_header(inquiryID,narration,referanceNumber);
    }

    function create_order_review_header(inquiryID,narration,referanceNumber) {
        var inquiryID = inquiryID;
        var narration = narration;
        var customerName = '';
        var CustomerAutoID = '';
        var referanceNumber = referanceNumber;
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
                    view_supplierAssignModel(inquiryID,orderreviewID);                    
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function view_supplierAssignModel(inquiryID,orderreviewID) {
        
        var template = '';
        $('#inquiryDetailView').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryID,orderreviewID: orderreviewID,template:template},
            url: "<?php echo site_url('srm_master/order_review_detail_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#inquiryDetailView').html(data);
                stopLoad();
                $('#pending-li').removeClass('active');
                $('#or_ongoing').removeClass('active');
                $('#statement-li').addClass('active');
                $('#statement').addClass('active');
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
       //alert("test");
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


</script>