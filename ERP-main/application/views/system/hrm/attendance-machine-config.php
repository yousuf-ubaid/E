<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_machine_configuration');
echo head_page($title, false);

$attMappingMaster_drop = attMappingMaster_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">&nbsp;</div>
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="open_config_modal()">
            <i class="fa fa-plus-square"></i>&nbsp; <?=$this->lang->line('common_add');?>
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="config_tbl" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto">Machine Type</th>
            <th style="width: auto"><?php echo $this->lang->line('common_device_id');?></th>
            <th style="width: auto">Table Name</th>
            <th style="width: auto">Auto Id column</th>
            <th style="width: auto">Machine Id Column</th>
            <th style="width: auto">Time Column</th>
            <th style="width: 70px"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="config_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Machine Config</h4>
            </div>
            <?= form_open('','role="form" class="form-horizontal" id="config_form"'); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-6 control-label" for="deviceID"><?=$this->lang->line('common_device_id');?> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="deviceID"  id="deviceID" class="form-control number-field">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-6 control-label" for="machineType">Machine Type <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('machineType', $attMappingMaster_drop, '', 'class="form-control" id="machineType" required'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="mappingID" id="mappingID" value="" />
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            <?= form_close()?>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('.number-field').numeric({
        negative: false,
        decimal: false
    });

    $(document).ready(function() {
        load_config_tbl();
        $('.headerclose').click(function(){
            fetchPage('system/hrm/attendance-machine-config','Test','HRMS');
        });
    });

    function load_config_tbl(selectedRowID=null){
        $('#config_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_attendance_mapping'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['mapping_id']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "mapping_id"},
                {"mData": "machineTypeDes"},
                {"mData": "device_id"},
                {"mData": "table_name"},
                {"mData": "auto_id_column"},
                {"mData": "machine_id_column"},
                {"mData": "att_time_column"},
                {"mData": "action"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0]},
                {"orderable": false, "targets": [3,4,5,6,7]}
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

    function open_config_modal(){
        $('#config_form')[0].reset();
        $('#save-btn').attr('onclick', 'save_config()');
        $('#config_modal').modal({backdrop: "static"});
    }

    function save_config(is_update=0){
        let postData = $('#config_form').serializeArray();
        let url = '<?php echo site_url('Employee/save_attMachineConfig'); ?>';

        if(is_update == 1){
            url = '<?php echo site_url('Employee/update_attMachineConfig'); ?>';
        }

        $.ajax({
            type: 'post',
            url: url,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#config_form')[0].reset();
                    $('#config_modal').modal('hide');
                    load_config_tbl();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?=$this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function edit_config(id, deviceID, machineType){
        $('#config_form')[0].reset();
        $('#save-btn').attr('onclick', 'save_config(1)');
        $('#mappingID').val(id);
        $('#deviceID').val(deviceID);
        $('#machineType').val(machineType);

        $('#config_modal').modal({backdrop: "static"});
    }

    function delete_config(id, deviceID){
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/delete_attMachineConfig'); ?>',
            data: {'id': id, 'deviceID': deviceID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    load_config_tbl();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?=$this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>
