<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title= $this->lang->line('common_expense_claim');
echo head_page($title, false);
$this->load->helper('crm_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
//$status_arr_filter = all_quotation_status(false);
$status_arr_filter = array('' => 'Status', '1' => 'Confirmed','2' => 'Not Confirmed');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
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
<div class="row">
    <div class="col-md-8"></div>
    <div class="col-md-4">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/crm/create_new_expenseclaim',null,'<?php echo $this->lang->line('crm_new_expence_claim')?>','CRM');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('crm_new_expence_claim')?><!--New Expense Claim-->
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row" style="margin-top: 2%;">
                <div class="col-sm-4" style="margin-left: 2%;">
                    <div class="col-sm-2">
                        <div class="mailbox-controls">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">&nbsp;<label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Expense Claim"
                                       id="searchTask" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="col-sm-3">
                        <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="filter_statusID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </div>
            <div id="quotationMaster_view"></div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/expense_claim_management', '', 'Expense Claim');
        });
        getExpenseClaimManagement_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });

    function getExpenseClaimManagement_tableView() {
        var searchExpenseClaim = $('#searchExpenseClaim').val();
        var status = $('#filter_statusID').val();
        var type = $('#filter_typeID').val();
        var assignee = $('#filter_assigneesID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchExpenseClaim': searchExpenseClaim, status: status, type: type, assignee: assignee},
            url: "<?php echo site_url('Crm/load_expenseClaimManagement_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#quotationMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getExpenseClaimManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#filter_typeID').val('');
        $('#filter_statusID').val('');
        $('#filter_assigneesID').val('');
        $('#searchExpenseClaim').val('');
        getExpenseClaimManagement_tableView();
    }

    function delete_crm_quotation(id) {
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
                    data: {'quotationAutoID': id},
                    url: "<?php echo site_url('Crm/delete_crm_quotation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getExpenseClaimManagement_tableView();
                        myAlert('s', 'ExpenseClaim Deleted Successfully');
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function view_quotation_printModel(contractAutoID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {contractAutoID: contractAutoID, html: html},
            url: "<?php echo site_url('Crm/quotation_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


</script>