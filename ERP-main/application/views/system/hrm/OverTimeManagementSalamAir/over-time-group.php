<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_over_time_overtime_gsroup');
echo head_page($title, false);

$currency_arr = all_currency_new_drop();


?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">


    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="opensystemInputModal()"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="overtime_Group_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 10px">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="width: 50px">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="systemInputModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="salary-cat-title"><?php echo $this->lang->line('hrms_over_time_new_ot_group_master');?><!--New OT Group Master--></h4>
            </div>
          <?php echo form_open('', 'role="form" class="form-horizontal" id="overtimeHeader" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label col-sm-4 col-xs-4"
                                   for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8"><input type="text" name="description" id="description"
                                                                  class="form-control"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group ">
                            <label class="control-label col-sm-4 col-xs-4" for="salesPersonCurrencyID"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                            <div class="col-sm-8 col-xs-8"><?php echo form_dropdown('CurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2"  id="CurrencyID" required'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm modalBtn" id="saveBtn"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
          <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/OverTimeManagementSalamAir/over-time-group.php', '', 'HRMS')
        });
        overtime_Group_table();
    });

    $('#overtimeHeader').bootstrapValidator({

        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
            CurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}/*Currency is required*/

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
            url: "<?php echo site_url('Salary_category/create_overTimeGroup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    overtime_Group_table();
                    $('#systemInputModal').modal('hide');
                }
            }, error: function () {
                myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    });

    function overtime_Group_table() {
        var Otable = $('#overtime_Group_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Salary_category/table_overtime_group'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

            },
            "aoColumns": [
                {"mData": "otGroupID"},
                {"mData": "otGroupDescription"},
                {"mData": "CurrencyCode"},

                {"mData": "edit"}

            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function delete_ot_group(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'otGroupID': id},
                    url: "<?php echo site_url('Salary_category/delete_ot_group'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        myAlert('s', 'Successfully deleted');
                        overtime_Group_table();

                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function edit_overtimegroup(otGroupID){
        fetchPage('system/hrm/OverTimeManagementSalamAir/new_overtime_group', otGroupID, 'HRMS')
    }

    function opensystemInputModal(){
       /* $('#overtimeHeader')[0].reset();*/
        $('#description').val('');
        //$('#overtimeHeader').bootstrapValidator('resetForm', true);
        $('#systemInputModal').modal('show');
    }



</script>