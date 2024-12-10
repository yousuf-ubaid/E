<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_signature_setup');
echo head_page($title  , false);


?>
<div class="row">
    <div class="col-md-9">&nbsp;</div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="open_signature_modal()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?>
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="signature_tbl" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_designation');?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="signature_set_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <?=$this->lang->line('hrms_others_master_assign_new_employee')?></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="signature_set_frm" autocomplete="off"'); ?>
            <input type="hidden" id="autoID" name="autoID" value="0">
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_employee');?></label>
                        <div class="col-sm-6">
                            <select name="emp_drop" id="emp_drop"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="sav-btn" class="btn btn-primary">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var signature_tbl = null;
    var signature_set_frm = $('#signature_set_frm');


    $('#emp_drop').select2({
        ajax: {
            url: "<?php echo site_url('Employee/dropDown_search'); ?>",
            dataType: 'json',
            processResults: function (data) {
                return {
                    results: data.items
                };
            }
        }
    });

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/signature-setup', 'Test', 'Signature setup');
        });

        load_signature_tbl();

        $('#signature_set_frm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
            }
        })
            .on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                var requestUrl = signature_set_frm.attr('action');
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: requestUrl,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        $('#sav-btn').prop('disabled', false);
                        if (data[0] == 's') {
                            signature_tbl.ajax.reload();
                            $("#signature_set_model").modal('hide');
                        }
                    }, error: function () {
                        myAlert('e' ,'<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            });
    });

    function load_signature_tbl(selectedID=null) {
        signature_tbl = $('#signature_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_signaturelist'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                { "targets": [0], "searchable": false, "orderable": true },
                { "targets": [3], "orderable": false }
            ],
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
                    if (parseInt(oSettings.aoData[x]._aData['signatureID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "signatureID"},
                {"mData": "empName"},
                {"mData": "designation"},
                {"mData": "action"}
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

    function open_signature_modal() {
        signature_set_frm.bootstrapValidator('resetForm', true);
        signature_set_frm[0].reset();
        signature_set_frm.attr('action', "<?php echo site_url('Employee/add_to_signature_list'); ?>");
        $("#signature_set_model").modal({backdrop: "static"});
    }

    function delete_signature(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id},
                    url: "<?php echo site_url('Employee/delete_signature_list'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            signature_tbl.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e' ,'<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            }
        );
    }
</script>
