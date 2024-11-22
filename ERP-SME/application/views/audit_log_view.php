<?php
$month_start = date('Y-m-01');
$current_date = date('Y-m-d');
$audit_column_arr = audit_column_arr();
$payActiveCompany_arr = payActiveCompany_arr();
?>

<style>
    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Audit Log</h3>

                <span class="pull-right">
                    <!--<button class="btn btn-success btn-xs" onclick="download_subscription()">
                        <i class="fa fa-file-excel-o"></i> Download
                    </button>-->
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"> Filter </legend>
                            <?php echo form_open('', ' name="frm-rpt" id="frm-rpt" class="form-horizontal" role="form"'); ?>
                            <div class="col-md-12">
                                <label for="column_drop[]" class="col-md-1 control-label">Company</label>
                                <div class="col-md-2">
                                    <select name="company_drop[]" id="company_drop" class="form-control" multiple="multiple">
                                        <?php
                                        $str = '';
                                        foreach ($payActiveCompany_arr as $item){
                                            $str .= '<option value="'.$item['company_id'].'">'.$item['company_name'].' ( '.$item['company_code'].')</option>';
                                        }
                                        echo $str;
                                        ?>
                                    </select>
                                </div>

                                <label for="column_drop[]" class="col-md-1 control-label">Column</label>
                                <div class="col-md-2">
                                    <select name="column_drop[]" id="column_drop" class="form-control" multiple="multiple">
                                        <?php
                                        $str = '';
                                        foreach ($audit_column_arr as $item){
                                            $str .= '<option value="'.$item['id'].'">'.$item['tbl_name'].'.'.$item['col_name'].'</option>';
                                        }
                                        echo $str;
                                        ?>
                                    </select>
                                </div>

                                <label for="from_date" class="col-md-1 control-label">From Date</label>
                                <div class="col-md-2">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control audit_log_date_pic" id="from_date" name="from_date" value="<?=$month_start; ?>" />
                                    </div>
                                </div>

                                <label for="to_date" class="col-md-1 control-label">To Date</label>
                                <div class="col-md-2">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" class="form-control audit_log_date_pic" id="to_date" name="to_date" value="<?=$current_date; ?>" />
                                    </div>
                                </div>

                                <button style="margin-top: 5px" type="button" onclick="audit_tb.ajax.reload()" class="btn btn-primary btn-sm pull-right">
                                    Load
                                </button>
                            </div>
                            <?php echo form_close(); ?>
                        </fieldset>
                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="audit_tb" class="<?=table_class()?>">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th style="width: auto">Company Name</th>
                                    <th style="width: auto">Column Name</th>
                                    <th style="width: auto">Auto ID</th>
                                    <th style="width: auto">Old Value</th>
                                    <th style="width: auto">New Value</th>
                                    <th style="width: auto">Updated By</th>
                                    <th style="width: auto">Updated Time</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>


<script type="text/javascript">
    let audit_tb = null;

    $('#column_drop, #company_drop').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#column_drop, #company_drop").multiselect2('selectAll', false);
    $("#column_drop, #company_drop").multiselect2('updateButtonText');

    $(document).ready(function () {
        load_audit_log_data();
    });

    function load_audit_log_data() {
        audit_tb = $('#audit_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_audit_report'); ?>",
            "aaSorting": [[7, 'desc']],
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
            "columnDefs": [
               { "targets": [0], "orderable": false },
               { "targets": [3], "visible": false }
            ],
            "aoColumns": [
                {"mData": "company_id"},
                {"mData": "company_name"},
                {"mData": "tableName"},
                {"mData": "rowID"},
                {"mData": "display_old_val"},
                {"mData": "display_new_val"},
                {"mData": "Fullname"},
                {"mData": "log_time"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'from_date', 'value': $('#from_date').val() });
                aoData.push({'name': 'to_date', 'value': $('#to_date').val() });
                aoData.push({'name': 'column_drop', 'value': $('#column_drop').val() });
                aoData.push({'name': 'company_drop', 'value': $('#company_drop').val() });

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
