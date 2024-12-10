<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->input->post('page_name'), false);
$main_grp = fetch_main_group();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <button style="margin-right: 15px" type="button" class="btn btn-primary btn-xs pull-right "
            onclick="modal_sub_group_add()"> <?php echo $this->lang->line('config_common_create_user_group');?><!--Create UserGroup-->
    </button>
</div>
<hr style="margin-top: 10px">
<div class="row">
    <div class="col-sm-12" id="div_reload">
        <table id="table_nav_access" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
               <th style="min-width: 5%;">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('config_common_main_group');?></th>
                <th style="min-width: 70%;"><?php echo $this->lang->line('config_common_sub_group');?><!--Sub Group--></th>
                <th style="min-width: 95%;">&nbsp;</th>

            </tr>
            </thead>
        </table>

    </div>
</div>

<div class="modal fade" id="SubGroupModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'class="form-horizontal" role="form" id="sub_group_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_common_user_group');?><!--User Group--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row ">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"> <?php echo $this->lang->line('config_common_main_group');?><!--Main Group--></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('companyGroupID', $main_grp, '', 'class="form-control select2" id="companyGroupID"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="description"
                                   name="description">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="SubGroupModalEdit" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'class="form-horizontal" role="form" id="sub_groupedit_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_company_sub_group_edit');?><!--Sub Group Edit--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-6">
                            <input type="hidden" id="companySubGroupID" name="companySubGroupID">
                            <input type="text" class="form-control" id="descriptionedit"
                                   name="descriptionedit">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/companyConfiguration/company_sub_group_management','','Company Sub Groups');
        });
        var Otable;
        table_sub_group();


        $('#sub_group_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                companyGroupID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_main_group_is_required');?>.'}}}/*Main Group is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            //data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_management/save_sub_group'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $("#SubGroupModal").modal('hide');
                        table_sub_group();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                   // myAlert(data[0], data[1]);
                }
            });
        });

        $('#sub_groupedit_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                descriptionedit: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            //data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_management/edit_sub_group'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#btnSave').prop('disabled', false);
                    if (data[0] == 's') {
                        $("#SubGroupModalEdit").modal('hide');
                        table_sub_group();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    // myAlert(data[0], data[1]);
                }
            });
        });


    });

    function table_sub_group() {
        window.Otable = $('#table_nav_access').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "recordsFiltered": 10,
            "sAjaxSource": "<?php echo site_url('Group_management/load_companysubgroup'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var
                    tmp_i = oSettings._iDisplayStart;
                var
                    iLen = oSettings.aiDisplay.length;
                var
                    x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "companySubGroupID"},
                {"mData": "groupdescription"},
                {"mData": "subdescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,3]}],
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

    function modal_sub_group_add() {
        $('#sub_group_form')[0].reset();
        $("#SubGroupModal").modal({backdrop: "static"});
    }

    function opensubgroupmodel(id){
        $("#companySubGroupID").val(id);
        $("#SubGroupModalEdit").modal({backdrop: "static"});
        load_company_sub_group(id);
    }

    function load_company_sub_group(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companySubGroupID': id},
            url: "<?php echo site_url('Group_management/load_company_sub_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#descriptionedit").val(data['description']);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                // myAlert(data[0], data[1]);
            }
        });
    }



</script>