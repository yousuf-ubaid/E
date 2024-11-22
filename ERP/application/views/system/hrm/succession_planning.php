
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_succession_planing_title');
echo head_page($title, false);


?>
<style>
    .error-message {
        color: red;
    }

    td {
        text-align: center;
    }

    .succession_plan_link{
        cursor: pointer;
        text-decoration: underline;
        color: #0000EE;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-10">
        <input type="button" class="btn btn-primary" value="Generate Report" onclick="goto_succession_plan_report()">
    </div>
    <div class="col-md-2 text-center">
        <input type="button" class="btn btn-primary" value="Add Header" onclick="load_add_header_dialog()"/>

    </div>

</div>
<hr>
<div class="table-responsive">
    <table id="quotation_contract_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="width: 15%">#</th>
            <th style="width: 60%">Segment Description</th>
            <th style="width: 15%"> </th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="add_header_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:75%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="houseEnr_title">Succession Plan Header</h4>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-1">
                            <label>Description</label>
                        </div>
                        <div class="col-md-3">
<!--                            <input type="input" class="form-control" id="input_header_description"/>-->
                            <textarea id="input_header_description" class="form-control" rows="2"></textarea>
                            <div id="header-description-error" class="error-message"></div>
                        </div>
                        <div class="col-md-1 text-center">
                            <input type="button" class="btn btn-primary" value="Save" onclick="save_header();"/>
                        </div>

                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="header_table" class="<?php echo table_class() ?>">
                            <thead>
                            <tr>
                                <th style="width: 15%">#</th>
                                <th style="width: 75%">Description</th>
                                <th style="width: 75%">Is Active</th>
                                <th style="width: 75%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var app = {};
    app.header_id = '';

    $(document).ready(function () {



        quotation_contract_table();
    });

    function goto_succession_plans(segment_id,description){
        localStorage.setItem('segment_id',segment_id);
        localStorage.setItem('segment_description',description);
        fetchPage('system/hrm/segmented_succession_plans','','HRMS');
    }

    function quotation_contract_table() {
        var Otable = $('#quotation_contract_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_segments_table'); ?>",
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
                {"mData": "segmentID"},
                {"mData": "description"},
                {"mData": "succession_plan_link"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function header_table() {
        var Otable = $('#header_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/load_succession_headers_table'); ?>",
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
                {"mData": "headerID"},
                {"mData": "description"},
                {"mData": "isActiveColumn"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function load_add_header_dialog() {
        $("#add_header_dialog").modal('show');
        header_table();
    }

    function edit_header() {
        const description=$(this).data('description');
        app.header_id = $(this).data('header_id');
        $("#input_header_description").val(description);
    }

    function save_header() {
        if (validate_header()) {
            const description = $("#input_header_description").val();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'description': description, 'id': app.header_id},
                url: '<?php echo site_url('Employee/save_succession_header') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert('s', data.message);
                    header_table();
                    app.header_id="";
                    $("#input_header_description").val("");
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }

    }

    function validate_header() {
        let is_valid = true;
        const description = $("#input_header_description").val();
        if (description == "") {
            is_valid = false;
            show_error('header-description-error', 'Required');
        } else {
            hide_error('header-description-error');
        }
        return is_valid;
    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function header_active_check(cb,header_id){
        let status = cb.checked;
        status = status?1:0;
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'status': status, 'header_id':header_id},
            url: '<?php echo site_url('Employee/update_succession_header_active_status') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert('s', data.message);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function goto_succession_plan_report() {
        fetchPage('system/hrm/succession_plan_report', '', 'HRMS');
    }
</script>
