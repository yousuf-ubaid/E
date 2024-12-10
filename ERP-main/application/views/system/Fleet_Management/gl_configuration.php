<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_expanse_claim', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_GL_Config');
echo head_page($title  , false);



$date_format_policy = date_format_policy();
$current_date = current_format_date();
$supplier_arr = all_supplier_drop(false);
$gl_arr = fetch_glcode_claim_category();
?>
<div class="row">
    <div class="col-md-7">

    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="open_claim_category_modal()"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Create Claim Category-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="calim_category_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="approvel_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_approval');?><!--Approval--></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="approval_table" class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_approval_level');?><!--Approval Level--></th>
                            <th><?php echo $this->lang->line('common_document_confirmed_by');?><!--Document Confirmed By--></th>
                            <th><?php echo $this->lang->line('common_company_id');?><!--Company ID--></th>
                            <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gl_new_add_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('fleet_GL_Config_NEW');?><!--Expense Claim Category--></h4>
            </div>
            <form class="form-horizontal" id="new_gl_category_form">
                <input type="hidden" id="glConfigAutoID" name="glConfigAutoID">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="glConfigDescription"
                                       name="glConfigDescription">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_gl_code');?><!--GL Code--></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('glAutoID', $gl_arr, '', 'class="form-control select2" id="glAutoID"'); ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" id="savbtn" class="btn btn-primary"><i class="fa fa-floppy-o"
                                                                                 aria-hidden="true"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/gl_configuration', 'Test', 'GL Configuration');
        });
        $('.select2').select2();
        calim_category_table();

        $('#new_gl_category_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                glConfigDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
                //glAutoID: {validators: {notEmpty: {message: 'GL Code is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'GLCode', 'value': $('#glAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Fleet/save_new_GL_config'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                  //  $('#savbtn').prop('disabled', false);
                    if (data[0] == 's') {
                        $("#gl_new_add_model").modal('hide');
                        Otable.draw();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });
    function calim_category_table(selectedID=null) {
        Otable = $('#calim_category_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Fleet/fetch_GL_config_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['glConfigAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "glConfigAutoID"},
                {"mData": "glConfigDescription"},
                {"mData": "Ec_detail"},
                {"mData": "edit"},
                {"mData": "glCode"},
                {"mData": "glCodeDescription"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0,2]},
                {"visible": false,"orderable": false, "searchable": true, "targets": [4,5]}
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

    function open_claim_category_modal() {
        $('#new_gl_category_form')[0].reset();
        $('#glAutoID').val('').change();
        $('#glConfigAutoID').val('');
        $("#gl_new_add_model").modal({backdrop: "static"});
    }

    function editGLconfig(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'glConfigAutoID': id},
            url: "<?php echo site_url('Fleet/editGLconfig'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#glAutoID').val(data['glAutoID']).change();
                $('#glConfigDescription').val(data['glConfigDescription']);
                $('#glConfigAutoID').val(id);
                $("#gl_new_add_model").modal({backdrop: "static"});
            }, error: function () {
                stopLoad();
            }
        });
    }

    function deleteGLconfig(id) {
        swal({



                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'glConfigAutoID': id},
                    url: "<?php echo site_url('Fleet/deleteGLconfig'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        Otable.draw();
                        myAlert(data[0], data[1]);
                    }, error: function () {
                        stopLoad();
                    }
                });
            });
    }


</script>
