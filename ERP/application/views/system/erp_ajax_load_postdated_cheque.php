<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
        if ($documentId == 'AP' || $documentId == null) {
        ?>
        <li class="<?php echo $documentId == null || $documentId == 'AP' ? 'active' : '' ?>"><a href="#tab_3<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('dashboard_post_dated_cheques_given');?><!--Post Dated Cheques Given--></a>
        </li>
        <?php }
        if ($documentId == 'AR' || $documentId == null) {
        ?>
        <li class="<?php echo $documentId == 'AR' ? 'active' : '' ?>"><a href="#tab_4<?php echo $userDashboardID ?>" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('dashboard_post_dated_cheques_received');?><!--Post Dated Cheques Received--></a>
        </li>
        <?php } ?>
        <li class="pull-right">
            <div style="margin-top: 7px"><label><?php echo $this->lang->line('dashboard_due_days');?><!--Due Days-->:</label> <select id="dueDays<?php echo $userDashboardID ?>">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="all"><?php echo $this->lang->line('common_all');?><!--All--></option>
                </select></div>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane <?php echo $documentId == 'AP' || $documentId == null ? 'active' : '' ?>" id="tab_3<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="pdcgiven_table<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('dashboard_party_name');?><!--Party Name--></th>
                        <th style="min-width: 15%">Cheque No</th>
                        <th style="min-width: 15%">Cheque Date</th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_days');?><!--Days--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('dashboard_remainin');?><!--RemainIn--></th>
                    </tr>
                    </thead>
                </table>
            </div>    
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane <?php echo $documentId == 'AR' ? 'active' : '' ?>" id="tab_4<?php echo $userDashboardID ?>">
            <div class="table-responsive">
                <table id="pdcreceived_table<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 48%"><?php echo $this->lang->line('dashboard_party_name');?><!--Party Name--></th>
                        <th style="min-width: 15%">Cheque No</th>
                        <th style="min-width: 15%">Cheque Date</th>
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_days');?><!--Days--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('dashboard_remainin');?><!--RemainIn--></th>
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
    let documentId = "<?php echo $documentId; ?>";

    $(document).ready(function () {
        $('#dueDays'+<?php echo $userDashboardID ?>).change(function () {
            if(documentId === 'AP' || documentId === '') {
                postdatedChequeGiven<?php echo $userDashboardID ?>();
            }
            if(documentId === 'AR' || documentId === '') {
                postdatedChequeReceived<?php echo $userDashboardID ?>();
            }
        });
        if(documentId === 'AP' || documentId === '') {
            postdatedChequeGiven<?php echo $userDashboardID ?>();
        }
        if(documentId === 'AR' || documentId === '') {
            postdatedChequeReceived<?php echo $userDashboardID ?>();
        }
    });

    function postdatedChequeGiven<?php echo $userDashboardID ?>() {
        $('#pdcgiven_table'+<?php echo $userDashboardID ?>).DataTable({
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
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_postdated_cheque_given'); ?>",
            "aaSorting": [[4, 'asc']],
            "columnDefs": [
                {
                    "targets": [5],
                    "visible": false
                }
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "vendor"},
                {"mData": "chequeNo"},
                {"mData": "dueDate"},
                {"mData": "bankCurrencyAmount"},
                {"mData": "dueDays"},
                {"mData": "remainIn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "dueDays", "value": $("#dueDays"+<?php echo $userDashboardID ?>).val()});
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

    function postdatedChequeReceived<?php echo $userDashboardID ?>() {
        $('#pdcreceived_table'+<?php echo $userDashboardID ?>).DataTable({
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
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_postdated_cheque_received'); ?>",
            "aaSorting": [[4, 'asc']],
            "columnDefs": [
                {
                    "targets": [5],
                    "visible": false
                }
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "vendor"},
                {"mData": "chequeNo"},
                {"mData": "dueDate"},
                {"mData": "bankCurrencyAmount"},
                {"mData": "dueDays"},
                {"mData": "remainIn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "dueDays", "value": $("#dueDays"+<?php echo $userDashboardID ?>).val()});
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