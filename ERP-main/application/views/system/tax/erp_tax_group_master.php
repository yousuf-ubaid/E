<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('tax_group_master');
echo head_page($title, false);


/*echo head_page('Tax Group Master', false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_tax_group_model()" class="btn btn-primary-new size-sm pull-right" ><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="warehousemaster_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('tax_group');?><!--Tax Group--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 15%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="taxgroupmaster_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="WarehouseHead"></h3>
            </div>
            <form role="form" id="taxgroupmaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="taxGroupID_Edit" name="taxGroupID_Edit">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('tax_group');?><!--Tax Group--></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('taxgroup', array('' => $this->lang->line('tax_select_group')/*'Select Group'*/, '1' => $this->lang->line('tax_outputs')/*'Outputs (Sales etc)'*/, '2' =>$this->lang->line('tax_inputs') /*'Inputs (Purchases, imports etc)'*/), '', 'class="form-control" id="taxgroup"') ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="taxdescription" name="taxdescription"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/warehousemaster_view','','Warehouse Master ');
        });

        taxGroupMasterview();

        $('#taxgroupmaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                taxgroup: {validators: {notEmpty: {message: '<?php echo $this->lang->line('tax_group_is_required');?>.'}}},/*Tax Group is required*/
                taxdescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Tax/save_tax_group_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if(data){
                        $("#taxgroupmaster_model").modal("hide");
                        taxGroupMasterview();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

    });


    function taxGroupMasterview() {
        var Otable = $('#warehousemaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Tax/load_tax_group_master'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "taxGroupID"},
                {"mData": "taxType"},
                {"mData": "Description"},
                {"mData": "action"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,1]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function open_tax_group_model() {
        $('#taxGroupID_Edit').val('');
        $('#taxgroupmaster_form')[0].reset();
        $('#taxgroupmaster_form').bootstrapValidator('resetForm', true);
        $('#WarehouseHead').html('<?php echo $this->lang->line('tax_add_new_group');?>');/*Add New Tax Group*/
        $("#taxgroupmaster_model").modal({backdrop: "static"});
    }

    function openTaxGgroupEdit(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('Tax/get_tax_group_edit'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                open_tax_group_model();
                $('#WarehouseHead').html('<?php echo $this->lang->line('tax_edit_group');?>');/*Edit Tax Group*/
                $('#taxGroupID_Edit').val(id);
                $('#taxgroup').val(data['taxType']);
                $('#taxdescription').val(data['Description']);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

            }
        });
    }

</script>