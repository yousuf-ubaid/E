

<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('bank_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('bank_employees_bank_master');
$bankID = $this->input->post('page_id');
echo head_page($title, false);
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="newBank()"><i
                    class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add'); ?><!-- Add -->
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="empBankTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('bank_code'); ?><!--Bank Code--></th>
                <th style="min-width: 25%"><?php echo $this->lang->line('bank_name'); ?><!--Bank Name--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_swift_code');?> <!--Swift Code--></th>
                <th style="min-width: 7%"></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


    <div class="modal fade" id="bankModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title bankMaster-title" id="myModalLabel"><?php echo $this->lang->line('new_bank_master'); ?><!--New Bank--></h4>
                </div>
                <?php echo form_open('', 'role="form" class="form-horizontal" id="bankMaster_form" autocomplete="off"'); ?>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="bankCode"> <?php echo $this->lang->line('bank_code'); ?><!--Bank Code--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="bankCode" id="bankCode" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="bankName"><?php echo $this->lang->line('bank_name'); ?><!--Bank Name--> <?php required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="bankName" id="bankName" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="swiftCode"><?php echo $this->lang->line('common_swift_code');?><!--Swift Code--></label>
                        <div class="col-sm-6">
                            <input type="text" name="swiftCode"  id="swiftCode" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hiddenID" id="hiddenID"/>
                    <button type="submit" class="btn btn-primary btn-sm" id="saveBtn"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        var bankMaster_form = $('#bankMaster_form');
        var hiddenID = $('#hiddenID');
        var empBankTbl = $('#empBankTB');

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/hrm/emp_bank_master', 'Test', 'HRMS');
            });

            bankMaster_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    bankCode: {validators: {notEmpty: {message: 'Bank code is required.'}}},
                    bankName: {validators: {notEmpty: {message: 'Bank name is required.'}}}
                },
            })
             .on('success.form.bv', function (e) {
                $('.submitBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');

                var requestUrl = $form.attr('action');
                var postData = $form.serialize();
                $.ajax({
                    type: 'post',
                    url: requestUrl,
                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            $('#bankModal').modal('hide');
                            empBankTbl.ajax.reload();
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });


            });

            empBankTB('<?php echo $bankID; ?>');
        });

        function empBankTB(selectedRowID=null) {
            empBankTbl = $('#empBankTB').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Employee/fetch_empBank'); ?>",
                "aaSorting": [[2, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();

                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                        if (parseInt(oSettings.aoData[x]._aData['bankID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }

                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "bankID"},
                    {"mData": "bankCode"},
                    {"mData": "bankName"},
                    {"mData": "bankSwiftCode"},
                    {"mData": "edit"}
                ],
                "columnDefs": [ {
                    "targets": [0,4],
                    "orderable": false
                }, {"searchable": false, "targets": [0]} ],
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

        function newBank() {
            $('.bankMaster-title').text('<?php echo $this->lang->line('new_bank_master_cap');?>');
            bankMaster_form[0].reset();
            bankMaster_form.attr('action', '<?php echo site_url('Employee/save_empBank'); ?>');

            bankMaster_form.bootstrapValidator('resetForm', true);
            hiddenID.val('');
            $('#bankModal').modal({backdrop: "static"});
        }

        function edit_empBank(obj) {
            $('.bankMaster-title').text('<?php echo $this->lang->line('new_bank_master_cap_update'); ?>');
            hiddenID.val('');
            bankMaster_form[0].reset();
            bankMaster_form.attr('action', '<?php echo site_url('Employee/update_empBank'); ?>');
            bankMaster_form.bootstrapValidator('resetForm', true);

            var details = getTableRowData(obj);

            $('#bankCode').val($.trim(details.bankCode));
            $('#bankName').val($.trim(details.bankName));
            $('#swiftCode').val($.trim(details.bankSwiftCode));
            $('#hiddenID').val($.trim(details.bankID));

            $('#bankModal').modal({backdrop: "static"});
        }

        function delete_empBank(obj) {
            var details = getTableRowData(obj);

            swal(
                {
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
                        url: "<?php echo site_url('Employee/delete_empBank'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'hiddenID': details.bankID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                empBankTbl.ajax.reload();
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');

                        }
                    });
                }
            );
        }

        function branches(obj) {
            var details = getTableRowData(obj);

            fetchPage('system/hrm/emp_bankBranches_master', details.bankID, 'HRMS');

        }

        function getTableRowData(obj) {
            var table = $('#empBankTB').DataTable();
            var thisRow = $(obj);
            return table.row(thisRow.parents('tr')).data();
        }

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });
    </script>


<?php
