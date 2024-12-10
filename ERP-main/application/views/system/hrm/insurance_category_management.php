<!--Translation added by Naseek-->


<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_insurance_category');
echo head_page($title  , false);
/*$title = 'Insurance Category';
echo head_page($title  , false);*/

?>
<div class="row">
    <div class="col-md-7">

    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="open_insurance_claim_category_modal()"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_others_master_create_insurance_category');?><!--Create Insurance Category-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="insurance_category_table" class="<?php echo table_class() ?>">
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
<div class="modal fade" id="claim_category_add_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_insurance_category');?><!--Insurance Category--></h4>
            </div>
            <form class="form-horizontal" id="claim_category_form">
                <input type="hidden" id="insurancecategoryID" name="insurancecategoryID">
                <div class="modal-body">
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
            fetchPage('system/expenseClaim/expense_claim_category', 'Test', 'Expense Claim Category');
        });
        $('.select2').select2();
        insurance_category_table();

        $('#claim_category_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
                //glAutoID: {validators: {notEmpty: {message: 'GL Code is required.'}}}
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
                url: "<?php echo site_url('InsuranceCategory/save_insurance_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#savbtn').prop('disabled', false);
                    if (data[0] == 's') {
                        $("#claim_category_add_model").modal('hide');
                        insurance_category_table();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });
    function insurance_category_table(selectedID=null) {
        Otable = $('#insurance_category_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('InsuranceCategory/fetch_insurance_category'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [
                {"searchable": false, "targets": [0,2]}
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
                    if (parseInt(oSettings.aoData[x]._aData['expenseClaimCategoriesAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "insurancecategoryID"},
                {"mData": "description"},
                {"mData": "edit"}
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

    function open_insurance_claim_category_modal() {
        $('#claim_category_form')[0].reset();
        $('#insurancecategoryID').val('');
        $("#claim_category_add_model").modal({backdrop: "static"});
    }

    function edit_insurance_category(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'insurancecategoryID': id},
            url: "<?php echo site_url('InsuranceCategory/edit_insurance_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#description').val(data['description']);
                $('#insurancecategoryID').val(id);
                $("#claim_category_add_model").modal({backdrop: "static"});
            }, error: function () {
                stopLoad();
            }
        });
    }
</script>