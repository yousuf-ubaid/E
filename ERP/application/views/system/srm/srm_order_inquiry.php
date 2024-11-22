<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('srm_order_inquiry');
echo head_page('Request for Quotation', false);

/*echo head_page('Order Inquiry', false);*/
$this->load->helper('crm_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$types_arr_filter = all_campaign_types(false);
$status_arr_filter = all_campaign_status(false);
$assignees_arr_filter = load_all_employees_campaignFilter(false);
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
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm"
                onclick="fetchPage('system/srm/customer-order/create_new_order_inquiry',null,'New Request for Quotation'/*New Order Inquiry*/,'SRM');">
            <i class="fa fa-plus"></i> Request for Quotation
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body">
            <form id="searchForm">
                <div class="row" style="margin: 6px 0px;">
                    <div class="col-sm-3">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchInquiry" type="text" class="form-control input-sm"
                                       placeholder="<?php echo $this->lang->line('srm_search_order_requiry');?>"
                                       id="searchInquiry" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span><!--Search Order Inquiry-->
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="col-sm-3">
                            <?php echo form_dropdown('confirmedYN', ['-1' => $this->lang->line('srm_select_confirm')/*'Select Confirm'*/,'0' => $this->lang->line('common_not_confirmed')/*'Not Confirmed'*/, '1' =>$this->lang->line('common_confirmed') /*'Confirmed'*/], '', 'class="form-control" id="filter_confirmedYN" onchange="startMasterSearch()"'); ?>
                        </div>
                        <div class="col-md-3 hide">
                            <div class="input-group date" data-provide="datepicker">
                                <input type="text" class="form-control" id="startDate" name="startDate"
                                       placeholder="Document Date">

                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-th"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1 hide" id="search_cancel">
                        <span class="tipped-top">
                        <a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </form>
            <div id="customerOrder_inquiry_Master_view" class="table-responsive mailbox-messages"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="rfq_email_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_generated_rfq');?><!--Generated RFQ--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="generated_inquiry_rfq_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!--Supplier Portal link show modal-->
<div class="modal fade" id="getLinkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                                        
                <h4 class="modal-title" id="myModalLabel2"> Supplier Portal Link </h4>
            </div>
            <div class="modal-body"  style="color: #696CFF;">
               
                <div class="row" style="word-wrap: break-word;">
                    <div class="col-sm-12">
                        <div id="getLink"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeGetLinkModal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_order_inquiry', '', 'Request for Quotation');
        });

        getCustomerOrderManagement_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });

    function getCustomerOrderManagement_tableView() {
        var postData = $('#searchForm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: postData,
            url: "<?php echo site_url('Srm_master/load_customer_order_inquiry_master'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#customerOrder_inquiry_Master_view').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#customerOrderMaster_view').html('<br>Message: ' + errorThrown);
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
        $('#searchInquiry').val('');
        $('#filter_assigneesID').val('');
        $('#searchCampaign').val('');
        getCustomerOrderManagement_tableView();
    }

    function delete_customer_inquiry_master(id) {
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
                    data: {'inquiryID': id},
                    url: "<?php echo site_url('Srm_master/delete_customer_inquiry_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getCustomerOrderManagement_tableView();
                        myAlert('s', 'Deleted Successfully');

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function generated_supplier_RFQ_View(inquiryID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryID},
            url: "<?php echo site_url('srm_master/load_orderbase_generated_rfq_view_with_po'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#generated_inquiry_rfq_view').html(data);
                $("#rfq_email_modelView").modal({backdrop: "static"});
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function send_rfq_supplier(inquiryMasterID, supplierID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'inquiryMasterID': inquiryMasterID, supplierID: supplierID},
            url: "<?php echo site_url('srm_master/send_rfq_email_suppliers'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    generated_supplier_RFQ_View(inquiryMasterID);
                    getCustomerOrderManagement_tableView();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_rfq_supplier_link(inquiryMasterID, supplierID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'inquiryMasterID': inquiryMasterID, supplierID: supplierID},
            url: "<?php echo site_url('srm_master/get_rfq_supplier_link'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                // if(data[0]=='s'){
                //     // setTimeout(function () {
                //     //     navigator.clipboard.writeText(data[1]);
                //     // }, 1000);
                    
                //     // myAlert('s','link copied to clipboard');
                    
                // }else{
                //     //myAlert(data[0], data[1]);
                // }
                
                $("#getLink").empty();
                $("#getLink").html(data[1]);
                $("#getLinkModal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_rfq_printModel(inquiryMasterID, supplierID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID, supplierID: supplierID, html: html},
            url: "<?php echo site_url('srm_master/supplier_rfq_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

</script>