<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_commission_scheme');
echo head_page($title  , false);


?>
<div class="row">
    <div class="col-md-9">&nbsp;</div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="open_commission_modal()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="commission_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="commission_add_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span id="comm-title-modal"></span> <?php echo $title;?></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="commission_form" autocomplete="off"'); ?>
                <input type="hidden" id="autoID" name="autoID" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="description" name="description">
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
    var commTbl = null;
    var commission_form = $('#commission_form');
    //$('#sub-container').addClass('col-md-6').removeClass('col-md-12');

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/commission-scheme/commission-scheme-master', 'Test', 'Expense Claim Category');
        });

        commission_table();

        $('#commission_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            }
        })
         .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            var requestUrl = commission_form.attr('action');
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
                        commTbl.ajax.reload();
                        $("#commission_add_model").modal('hide');
                    }
                }, error: function () {
                    myAlert('e' ,'<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        });
    });

    function commission_table(selectedID=null) {
        commTbl = $('#commission_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('CommissionScheme/fetch_commission_schemes'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                { "targets": [0], "orderable": true, "searchable": false },
                { "targets": [2], "orderable": false, "searchable": false }
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
                    if (parseInt(oSettings.aoData[x]._aData['id']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
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

    function open_commission_modal() {
        $('#comm-title-modal').text('<?php echo $this->lang->line('common_add'); ?>');
        commission_form.bootstrapValidator('resetForm', true);
        commission_form[0].reset();
        commission_form.attr('action', "<?php echo site_url('CommissionScheme/save_scheme'); ?>");
        $("#commission_add_model").modal({backdrop: "static"});
    }

    function edit_commission(obj) {
        $('#comm-title-modal').text('<?php echo $this->lang->line('common_update'); ?>');
        commission_form.attr('action', "<?php echo site_url('CommissionScheme/edit_scheme'); ?>");
        var thisRow = $(obj);
        var details = commTbl.row(  thisRow.parents('tr') ).data()  ;

        $('#autoID').val(details['id']);
        $('#description').val(details['description']);
        $("#commission_add_model").modal({backdrop: "static"});
    }

    function delete_commission(id) {
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
                    url: "<?php echo site_url('CommissionScheme/delete_scheme'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            commTbl.ajax.reload();
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

<?php
