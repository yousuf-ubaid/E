<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
echo head_page('Check List', false);
?>

<div class="row">

    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="open_checklist();" ><i class="fa fa-plus"></i> Add Check List</button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="checklist_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 1%">#</th>
            <th style="width: 10%">Description</th><!--Code-->
            <th style="width: 5%">Status</th><!--Reference-->
            <th style="width: 1%">Action</th><!--Details-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="add_checklistmaster">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Check List</h4>
            </div>
            <?php echo form_open('', 'role="form" id="checklist_master_form"'); ?>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_description') ?> <?php echo required_mark()?></label>
                    </div>
                    <div class="form-group col-sm-6">

                    <input type="text" class="form-control " id="description" name="description" required>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 ">
                        <label class="title">Number of Column <?php echo required_mark()?></label>
                    </div>
                    <div class="form-group col-sm-6">
                      <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="numberofcolumn" name="numberofcolumn" required>
                    <span class="input-req-inner"></span>
                     </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submit_checklist()" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?><!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    var salesPersonID = null;
    var Otable;
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/pm/checklist_master','','Check List');
        });
        checklist_table();
    });

    function checklist_table(){
        Otable = $('#checklist_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_checklistmaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "checklistID"},
                {"mData": "checklistDescription"},
                {"mData": "status"},
                {"mData": "edit"},

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

    function open_checklist()
    {
        $('#description').val('');
        $('#numberofcolumn').val('');
        $('#checklist_master_form')[0].reset();
        $('#add_checklistmaster').modal('show');
    }
    function submit_checklist()
    {
      var data = $('#checklist_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_checkList'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    checklist_table();
                    $('#add_checklistmaster').modal('hide');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>