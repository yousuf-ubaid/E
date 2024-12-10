<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('srm_customer_order');
echo head_page($title, false);

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
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm "
                onclick="fetchPage('system/srm/customer-order/create_new_customer_order',null,'New Customer Order Inquiry','SRM');">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('srm_order');?><!--Order-->
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
                                <input name="searchOrder" type="text" class="form-control input-sm"
                                       placeholder="Search Customer Order"
                                       id="searchOrder" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="col-sm-3">
                            <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="filter_statusID" onchange="startMasterSearch()"'); ?>
                        </div>
                        <div class="col-sm-3">
                            <?php echo form_dropdown('customerID', $customer_arr_filter, '', 'class="form-control" id="filter_customerID" onchange="startMasterSearch()"'); ?>
                        </div>
                    </div>
                    <div class="col-sm-1 hide" id="search_cancel">
                        <span class="tipped-top">
                        <a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </form>
            <div id="customerOrderMaster_view" class="table-responsive mailbox-messages"></div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customer_order', '', 'Customer Order Master');
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
            url: "<?php echo site_url('Srm_master/load_customer_order_master'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#customerOrderMaster_view').html(data);
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
        $('#searchOrder').val('');
        $('#filter_statusID').val('');
        $('#filter_customerID').val('');
        getCustomerOrderManagement_tableView();
    }

    function delete_customer_order_master(id) {
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
                    data: {'customerOrderID': id},
                    url: "<?php echo site_url('Srm_master/delete_customer_order_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getCustomerOrderManagement_tableView();
                        myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

</script>