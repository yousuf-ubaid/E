
<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = 'Salary Variable Declaration';//$this->lang->line('hrms_payroll_salary_declaration');
echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>


<style>
    .right-align{ text-align: right; }

    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
    }
</style>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span><?php echo $this->lang->line('common_not_confirmed');?><!-- Not Confirmed-->
                    / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right btn-sm" onclick="openNewDeclaration_modal()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?><!--New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="declaration_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_code_declaration');?><!--Declaration Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="newDeclaration_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="" role="document">
        <div class="modal-content">
            <form class="" id="declaration_form" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type='text' class="form-control" id="documentDate" name="documentDate" value="<?php echo $current_date; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                            </div>
                        </div>

                        <div class="form-group col-sm-2">&nbsp;</div>

                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <?php echo form_dropdown('MasterCurrency', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="MasterCurrency" required');?>
                        </div>

                    </div>

                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <select name="isPayrollCategory" id="isPayrollCategory" class="form-control">
                                <option value="1"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                <option value="2"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non payroll--></option>
                            </select>
                        </div>

                        <div class="form-group col-sm-2">&nbsp;</div>

                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('hrms_payroll_initial_declaration');?><!--Initial Declaration--></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <select name="isInitialDeclaration" id="isInitialDeclaration" class="form-control">
                                <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
                                <option value="1"><?php echo $this->lang->line('common_yes');?><!--Yes--></option>
                            </select>
                        </div>

                    </div>
                    <div class="row" style="margin-left: 2px">

                    </div>
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('common_template');?></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <select name="declaration_template" id="declaration_template" class="form-control">
                                <!-- <option value="1"><?php //echo $this->lang->line('common_standard');?></option> -->
                                <option value="2"><?php echo $this->lang->line('common_increment');?></option>
                            </select>
                        </div>

                        <div class="form-group col-sm-2">&nbsp;</div>

                        <div class="form-group col-sm-2">
                            <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        </div>
                        <div class="form-group col-sm-3">
                            <textarea class="form-control" id="salary_description" name="salary_description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.select2').select2();
    let dec_tbl = null;

    Inputmask().mask(document.querySelectorAll("input"));
    let date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/salery_declaration','','HRMS');
        });

        salary_declaration_table();

        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {
            //$('#declaration_form').bootstrapValidator('revalidateField', 'documentDate');
        });

        $('#declaration_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                MasterCurrency: {validators: {notEmpty: {message: 'Currency is required.'}}},
                salary_description: {validators: {notEmpty: {message: 'Description is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#MasterCurrency option:selected').text()});
            data.push({'name': 'isVariable', 'value': '1'});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_employee_declaration_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#newDeclaration_modal').modal('hide');


                        setTimeout(function(){
                            load_details(data['id'], data['declaration_template']);
                        },300);

                    }


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

    });

    function openNewDeclaration_modal(){
        $('#newDeclaration_modal').modal('show');
    }

    function LoadDeclarationModel() {
        $("#declarationModal").modal("show");
        $("#outputSalaryDeclaration_detail").hide();
        $('#frm_salaryDeclarationMaster').show();
    }

    function salary_declaration_table(selectedID=null) {
        dec_tbl = $('#declaration_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_declaration_variable_employees_master'); ?>",
            "aaSorting": [[2, 'desc']],
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
                    if (parseInt(oSettings.aoData[x]._aData['purchaseOrderID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')

                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')

            },
            "aoColumns": [
                {"mData": "salarydeclarationMasterID"},
                {"mData": "newDocumentDate"},
                {"mData": "documentSystemCode"},
                {"mData": "transactionCurrency"},
                {"mData": "Description"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0,5,6]},
                {"targets": [0,5,6,7], "orderable": false}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
                aoData.push({"name": "isVariable", "value": "1"});
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

    function delete_declaration(id, value) {
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
                    data: {'masterID': id,'isVariable': '1'},
                    url: "<?php echo site_url('Employee/delete_salary_declaration_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            dec_tbl.ajax.reload();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referBackDeclaration(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id,'isVariable':1},
                    url: "<?php echo site_url('Employee/referback_salary_declaration'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            dec_tbl.ajax.reload();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_details(id, template){
        let page = 'system/hrm/salery_variable_declaration_new.php';
        if(template == 2){
            page = 'system/hrm/ajax/variable_salary_declaration_multiple_insertion_ajax'
        }
        fetchPage(page, id,'HRMS', 'standard');
    }

    function view_modal( docID ){
        documentPageView_modal('SVD-2', docID)
    }

    function print_SD(docID, docCode, template){
        let url = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
        if(template == 2){
            url = "<?php echo site_url('Employee/load_salary_approval_confirmation_view'); ?>";
        }
        window.open(url+'/'+docID+'/'+docCode+'/1', "blank");
    }

    
</script>
