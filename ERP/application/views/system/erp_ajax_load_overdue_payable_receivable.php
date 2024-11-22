<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
            if ($documentId == 'AP') {
        ?>
            <li class="active"><a href="#tab_1<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('dashboard_overdue_payables');?><!--Overdue Payables--></a>
            </li>
        <?php }
            if ($documentId == 'AR') {
        ?>
            <li class="active"><a href="#tab_2<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('dashboard_overdue_receivables');?><!--Overdue Receivables--></a>
            </li>
        <?php } ?>
        <li class="pull-right bb-none">
            <div class="set-inline"><label><?php echo $this->lang->line('common_currency');?><!--Currency-->:</label> <select id="currency<?php echo $userDashboardID ?>">
                    <option value="transactionAmount" selected><?php echo $this->lang->line('common_transaction');?><!--Transaction--></option>
                    <option value="companyReportingAmount"><?php echo $this->lang->line('dashboard_reporting');?><!--Reporting--></option>
                </select></div>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane <?php echo $documentId == 'AP' ? 'active' : '' ?>" id="tab_1<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="overdue_payable<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('common_name');?><!--Name--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane <?php echo $documentId == 'AR' ? 'active' : '' ?>" id="tab_2<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="overdue_receivable<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('common_name');?><!--Name--></th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>

<!--modal report-->
<div class="modal fade" id="vendor_statement_report_modal<?php echo $userDashboardID ?>" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('dashboard_vendor_statement');?><!--Vendor Statement--></h4>
            </div>
            <div class="modal-body">
                <div id="vendorReportContent<?php echo $userDashboardID ?>"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customer_statement_report_modal<?php echo $userDashboardID ?>" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 100%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('dashboard_customer_statement');?><!--Customer Statement--></h4>
            </div>
            <div class="modal-body">
                <div id="CustomerReportContent<?php echo $userDashboardID ?>"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script>
    let documentId = "<?php echo $documentId; ?>";

    $('#currency'+<?php echo $userDashboardID ?>).change(function () {
        if(documentId === 'AP'){
            overduePayables<?php echo $userDashboardID ?>();
        }
        if(documentId === 'AR'){
            overdueReceivable<?php echo $userDashboardID ?>();
        }
    });
    if(documentId === 'AP'){
        overduePayables<?php echo $userDashboardID ?>();
    }
    if(documentId === 'AR'){
        overdueReceivable<?php echo $userDashboardID ?>();
    }

    function overduePayables<?php echo $userDashboardID ?>() {
        $('#overdue_payable'+<?php echo $userDashboardID ?>).DataTable({
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
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_overdue_payables_frmgl'); ?>",

            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    if (oSettings['aoData'][x]._aData.srchamount == 0) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).hide();
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "supplierName"},
                {"mData": "currency"},
                {"mData": "amount"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": 2}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "currency", "value": $("#currency"+<?php echo $userDashboardID ?>).val()});
                aoData.push({"name": "userDashboardID", "value":<?php echo $userDashboardID ?>});
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

    function overdueReceivable<?php echo $userDashboardID ?>() {
       $('#overdue_receivable'+<?php echo $userDashboardID ?>).DataTable({
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
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_overdue_receivable_frmgl'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    if (oSettings['aoData'][x]._aData.srchamount == 0) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).hide();
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "customerName"},
                {"mData": "currency"},
                {"mData": "amount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "currency", "value": $("#currency"+<?php echo $userDashboardID ?>).val()});
                aoData.push({"name": "userDashboardID", "value":<?php echo $userDashboardID ?>});
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

    function dashboardOverduePayables<?php echo $userDashboardID ?>(supplierID,currencyType){
        var captionChk = '';
        var currency = $("#currency"+<?php echo $userDashboardID ?>).val();
        var RptID = 'AP_VS';
        var fieldNameChk = [currency];
        var vendorTo = [supplierID];
        if(currency == 'transactionAmount'){
            captionChk = ['Transaction Currency'];
        }else{
            captionChk = ['Reporting Currency'];
        }
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/dashboardReportView') ?>",
            data: {RptID : RptID,fieldNameChk : fieldNameChk,captionChk : captionChk, vendorTo : vendorTo,currency:currencyType},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#vendorReportContent"+<?php echo $userDashboardID ?>).html(data);
                $('#vendor_statement_report_modal'+<?php echo $userDashboardID ?>).modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

    function dashboardOverdueReceivables<?php echo $userDashboardID ?>(customerID,currencyType){
        var captionChk = '';
        var currency = $("#currency"+<?php echo $userDashboardID ?>).val();
        var RptID = 'AR_CS';
        var fieldNameChk = [currency];
        var customerTo = [customerID];
        if(currency == 'transactionAmount'){
            captionChk = ['Transaction Currency'];
        }else{
            captionChk = ['Reporting Currency'];
        }
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/dashboardReportView') ?>",
            data: {RptID : RptID,fieldNameChk : fieldNameChk,captionChk : captionChk, customerTo : customerTo,currency:currencyType},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#CustomerReportContent"+<?php echo $userDashboardID ?>).html(data);
                $('#customer_statement_report_modal'+<?php echo $userDashboardID ?>).modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

</script>