<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = "Commision Report";
echo head_page($title, false);

$date_format_policy = date_format_policy();
$supplierArr = all_supplier_drop();
$current_date = current_format_date();

$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

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
    /* .custom-label {
    background-color: #FFFF00;
    padding: 5px 10px;
    border-radius: 5px;
    } */

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <div class="col-md-12">
            <div class="pull-right">
                <div id="exportButton"><!-- data-export-url="<?php //echo site_url('Procurement/export_excel_commision_report'); ?>"-->
                    <a href="#" class="btn btn-excel btn-xs" id="btn-excel" onclick="export_excel_commision_report()">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i><?php echo $this->lang->line('common_excel'); ?>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body">
            <form role="form" id="searchForm" autocomplete="off">
                <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
                <div class="row" style="margin: 6px 0px;">
                    <!-- <div class="col-sm-3">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchOrder" type="text" class="form-control input-sm"
                                       placeholder="Search Customer Order"
                                       id="searchOrder" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div> -->
                    <div class="form-group col-sm-4">
                        <div class="">
                            <label for="supplierID" class="custom-label">Supplier</label>
                        </div>
                        <div class="">
                                <?php echo form_dropdown('supplierID', $supplierArr, '', 'class="form-control" id="supplierID" onchange="startMasterSearch()"'); ?>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <div class="">
                            <label for="from_date" class="custom-label">From Date</label>
                        </div>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="from_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="from_date" class="form-control" onchange="startMasterSearch()">
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <div class="">
                            <label for="to_date" class="custom-label">To Date</label>
                        </div>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="to_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="to_date" class="form-control" onchange="startMasterSearch()">
                        </div>
                    </div>
                    <div class="col-sm-1" id="search_cancel">
                        <span class="tipped-top" rel="tooltip" title="Clear all filters">
                            <a id="cancelSearch" href="#" onclick="clearSearchFilter()"><i class="fa fa-lg fa-times-circle-o"></i></a>    <!--<img src="<?php //echo base_url("images/crm/cancel-search.gif") ?>">-->
                        </span>
                    </div>
                </div>
            </form>
            <div id="commision_Report_tableView" class="table-responsive mailbox-messages"></div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/procurement/report/erp_commision_report', '', 'Commision Report');
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //
        });
        $('#search_cancel').addClass('hide');
        get_CommisionReport_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });

    function get_CommisionReport_tableView() {
        var postData = $('#searchForm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: postData,
            url: "<?php echo site_url('Procurement/load_commision_report_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#commision_Report_tableView').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#commision_Report_tableView').html('<br>Message: ' + errorThrown);
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        get_CommisionReport_tableView();
    }

    function clearSearchFilter() {
        //$('#filter_confirmedYN').val('-1');
        //$('#searchOrder').val('');
        $('#supplierID').val('').change();
        $('#from_date').val('');
        $('#to_date').val('');
        $('#search_cancel').addClass('hide');
        get_CommisionReport_tableView();
    }

    function delete_record(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderDetailsID' : id},
                    url: "<?php echo site_url('Procurement/delete_commision_record'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        get_CommisionReport_tableView();
                        myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function export_excel_commision_report() {
        var form = document.getElementById('searchForm');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Procurement/export_excel_commision_report'); ?>';
        form.submit();
    }

</script>