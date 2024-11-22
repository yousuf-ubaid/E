<?php
$primaryLanguage = getPrimaryLanguage();
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];

?>
<div class="row" style="margin-top: 5px;margin-right: 0%;">
    <div class="col-md-12">
        <button type="button" class="btn btn-danger btn-xs pull-right" style="margin-right: 10px"
                onclick="print_btn();">
            <i class="fa fa-print"></i> Print
        </button>
        <button type="button" class="btn btn-success btn-xs pull-right" style="margin-right: 10px"
                onclick="download_in_excel();">
            <i class="fa fa-file-excel-o"></i> Excel
        </button>
    </div>
</div>
<br>
<form role="form" id="frm_rpt" class="form-horizontal" autocomplete="off">
    <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
    <input type="hidden" id="projectid" name="projectid" value="<?php echo $ProjectID ?>">
    <input type="hidden" id="startdate" name="startdate" value="<?php echo $start_date ?>">
    <input type="hidden" id="enddate" name="enddate" value="<?php echo $end_date ?>">
</form>

<div class="table-responsive">
    <table id="audit_log" class="<?php echo table_class(); ?>">

        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 10%">Project</th>
            <th style="min-width: 10%">Column Name</th>
            <th style="min-width: 7%">Old Value</th>
            <th style="min-width: 10%">New Value</th>
            <th style="min-width: 5%">Updated Time</th>
            <th style="min-width: 5%">Updated By</th>
        </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        auditlog_table();
    });

    function auditlog_table() {

        Otable = $('#audit_log').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_audit_report'); ?>",
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
                {"mData": "id"},
                {"mData": "projectName"},
                {"mData": "display_name"},
                {"mData": "display_old_val"},
                {"mData": "display_new_val"},
                {"mData": "timestamp"},
                {"mData": "updateemployee"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({"name": "projectid", "value":  $("#projectid").val()});
                aoData.push({"name": "startdate", "value": $("#startdate").val()});
                aoData.push({"name": "enddate", "value": $("#enddate").val()});
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
    function print_btn() {
        var form = document.getElementById('frm_rpt');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Boq/get_pm_auditrpt'); ?>';
        form.submit();
    }
    function download_in_excel() {
        var form = document.getElementById('frm_rpt');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('Boq/get_projectmanagement_auditrptexcel'); ?>';
        form.submit();
    }
</script>