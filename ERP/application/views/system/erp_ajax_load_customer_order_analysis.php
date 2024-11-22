<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_5<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('dashboard_quotation');?><!--Quotation-->
                </a>
        </li>
        <li class=""><a href="#tab_6<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('dashboard_sales_order');?><!--Sales Order--></a>
        </li>
        <li class=""><a href="#tab_7<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('dashboard_contract');?><!--Contract-->
                </a>
        </li>
        <li class="pull-right">
            <div style="margin-top: 10px"><strong class="btn-box-tool">Currency : (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong></div>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_5<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="quotation<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('dashboard_quote_value');?><!--Quote Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_invoiced_delivered_value');?><!--Invoiced / Delivered Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_balance');?><!--Balance--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_6<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="sales_order<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('dashboard_order_value');?><!--Order Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_invoiced_delivered_value');?><!--Invoiced / Delivered Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_balance');?><!--Balance--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="tab_7<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="contract<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('dashboard_contract_value');?><!--Contract Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_invoiced_delivered_value');?><!--Invoiced / Delivered Value--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_balance');?><!--Balance--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>
<script>

    quotation<?php echo $userDashboardID ?>();
    salesOrder<?php echo $userDashboardID ?>();
    contract<?php echo $userDashboardID ?>();

    function quotation<?php echo $userDashboardID ?>() {
        var Otable5 = $('#quotation'+<?php echo $userDashboardID ?>).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_quotation'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "customerName"},
                {"mData": "conractCompanyReportingAmount"},
                {"mData": "invoiceCompanyReportingAmount"},
                {"mData": "balanceAmount"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": 2}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function salesOrder<?php echo $userDashboardID ?>() {
        var Otable6 = $('#sales_order'+<?php echo $userDashboardID ?>).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_sales_order'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "customerName"},
                {"mData": "conractCompanyReportingAmount"},
                {"mData": "invoiceCompanyReportingAmount"},
                {"mData": "balanceAmount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function contract<?php echo $userDashboardID ?>() {
        var Otable7 = $('#contract'+<?php echo $userDashboardID ?>).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_contract'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "customerName"},
                {"mData": "conractCompanyReportingAmount"},
                {"mData": "invoiceCompanyReportingAmount"},
                {"mData": "balanceAmount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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