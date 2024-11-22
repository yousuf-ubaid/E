<?php
$expenseGL = expenseGL_drop();
$liabilityGL = liabilityGL_drop();
$slabsmaster = slabsmaster();

?>
<style type="text/css">
    .saveInputs {
        height: 25px;
        font-size: 11px
    }

    #socialinsurance-add-tb td, #socialinsurance-edit-tb td {
        padding: 2px;
    }
</style>

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_payee_master');
echo head_page($title, false);

?>


<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openPayee_modal()"><i
                class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="load_socialInsurance" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_sort_code');?><!--Sort Code--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_liability_gl_code');?><!--Liability GL Code--></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></th>
            <th style="width: 75px"></th>
        </tr>
        </thead>
    </table>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="new_payee_insurance" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_add_payee_master');?><!--Add Payee Master--></h4>
            </div>
            <form class="form-horizontal" id="add-social_insurance_form">
                <div class="modal-body">
                    <table class="table table-bordered" id="socialinsurance-add-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('hrms_payroll_sort_code');?><!--Sort Code--></th>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <th><?php echo $this->lang->line('hrms_payroll_liability_gl_code');?><!--Liability GL Code--></th>
                            <th style="width:120px"><?php echo $this->lang->line('hrms_payroll_slab');?><!--Slab--></th>
                            <th><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="vertical-align: middle;">
                                <input type="text" name="sortCode[]" class="form-control saveInputs new-items"/>
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" name="description[]" class="form-control saveInputs new-items"/>
                            </td>
                            <td style="vertical-align: middle;">
                                <select class="form-control saveInputs liabilityGlAutoID select2" id="liabilityGlAutoID_1" name="liabilityGlAutoID[]">
                                    <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                    <?php
                                    foreach ($liabilityGL as $key => $row) {
                                        echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>

                            <td>
                                <select name="ifSlab[]" id="ifSlab_1" onchange="changeSlab(this)" class="form-control saveInputs ifSlab select2">
                                    <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                    <?php
                                    foreach ($slabsmaster as $key => $value) {
                                        echo '<option value="' . $value->slabsMasterID . '" > ' . $value->Description . ' | ' . $value->documentSystemCode . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="ifSlabHidden[]">
                            </td>
                            <td>
                                <select name="payrollType[]" id="payrollType_1" class="form-control saveInputs payrollType select2">
                                    <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                    <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                    <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                                </select>
                            </td>
                            <td style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_social_insurance()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_edit_payee_master');?><!--Edit Payee Master--></h4>
            </div>

            <form role="form" id="editSocialInsurance_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="socialinsurance-edit-tb">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('hrms_payroll_sort_code');?><!--Sort Code--></th>
                                    <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                    <th><?php echo $this->lang->line('hrms_payroll_liability_gl_code');?><!--Liability GL Code--></th>
                                    <th style="width:120px"><?php echo $this->lang->line('hrms_payroll_slab');?><!--Slab--></th>
                                    <th><?php echo $this->lang->line('hrms_payroll_payroll_type');?><!--Payroll Type--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <input type="text" name="siSortCode" id="siSortCode" class="form-control saveInputs"/>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <input type="text" name="siDes" id="siDes" class="form-control saveInputs"/>
                                        <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                    </td>

                                    <td>
                                        <select class="form-control saveInputs select2" id="si_liabilityGlAutoID" name="si_liabilityGlAutoID">
                                            <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                            <?php
                                            foreach ($liabilityGL as $key => $row) {
                                                echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="siSlab" id="siSlab" class="form-control saveInputs select2">
                                            <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                            <?php
                                            foreach ($slabsmaster as $key => $value) {
                                                echo '<option value="' . $value->slabsMasterID . '" > ' . $value->Description . ' | ' . $value->documentSystemCode . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="payrollTypeEdit" id="payrollTypeEdit" class="form-control saveInputs payrollType select2" disabled>
                                            <option value=""><?php echo $this->lang->line('common_select_a_option');?><!--Select a option--></option>
                                            <option value="N"><?php echo $this->lang->line('hrms_payroll_payroll');?><!--Payroll--></option>
                                            <option value="Y"><?php echo $this->lang->line('hrms_payroll_non_payroll');?><!--Non Payroll--></option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateSocialInsurance()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php
$items = [
    'MA_MD' => false,
    'balancePay' => false,
    'SSO' => false,
    'payGroup' => false,
    'only_salCat_payGroup' => true
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var social_insurance_tb = $('#socialinsurance-add-tb tbody');
    var pgId = null;
    var oTable = null;
    var urlSave = '<?php echo site_url('Employee/saveFormula_new') ?>';
    var isPaySheetGroup = 0;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/payee_master', 'Test', 'HRMS');
        });

        load_socialInsurance();

        $('.number').keypress(function (event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });

       $('.select2').select2();
    });

    function load_socialInsurance(selectedRowID=null) {
        oTable = $('#load_socialInsurance').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_payeeMaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if (parseInt(oSettings.aoData[x]._aData['RId']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }


                //makeTdAlign('load_socialInsurance', 'right', [3, 4])
            },
            "aoColumns": [
                {"mData": "payeeMasterID"},
                {"mData": "sortCode"},
                {"mData": "Description"},
                {"mData": "liablityGlCOde"},
                {"mData": "isPayrollYN"},
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

    function openPayee_modal() {
        $('#socialinsurance-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('').change();
        $('#liabilityGlAutoID_1 option:first-child').attr("selected", "selected").change();
        $('#ifSlab_1 option:first-child').attr("selected", "selected").change();
        $('#payrollType_1 option:first-child').attr("selected", "selected").change();
        $('#new_payee_insurance').modal({backdrop: "static"});
    }

    function save_social_insurance() {
        var errorCount = 0;
        $('.new-items').each(function () {
            if ($.trim($(this).val()) == '') {
                errorCount++;
                return false;
            }
        });

        if (errorCount == 0) {
            var postData = $('#add-social_insurance_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/savePayeeMaster'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#new_payee_insurance').modal('hide');
                        load_socialInsurance();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            })
        }
        else {
            myAlert('e', '<?php echo $this->lang->line('hrms_payroll_please_fill_all_fields');?>');/*Please fill all fields*/
        }
    }

    function edit_social_insurance(id, des, employee, employer, sortCode, glCode, isSlabApplicable, SlabID, payrollType) {

        var splitGl = glCode.split('_');

        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val($.trim(id));
        $('#siDes').val($.trim(des));
        $('#siEmployee').val($.trim(employee));
        $('#siEmployer').val($.trim(employer));
        $('#siSortCode').val($.trim(sortCode));
        $('#si_expenseGlAutoID').val($.trim(splitGl[0])).change();
        $('#si_liabilityGlAutoID').val($.trim(splitGl[1])).change();
        $('#payrollTypeEdit').val($.trim(payrollType)).change();

        if (isSlabApplicable == 1) {
            $('#siIsSlab').prop('checked', true);
        } else {
            $('#siIsSlab').prop('checked', false);
        }
        $('#siSlab').val($.trim(SlabID)).change();
    }

    function delete_social_insurance(id, description) {
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
                    url: "<?php echo site_url('Employee/deletePayeeMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'hidden-id': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_socialInsurance()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#socialinsurance-add-tb tbody tr:first').clone() //html();
        //social_insurance_tb.append('<tr>'+appendData+'</tr>');
        //$('.select2').select2();

        appendData.find('input').val('')
        var expneceGL = $('#expenseGlAutoID_1 option').clone();
        var liabilityGL = $('#liabilityGlAutoID_1 option').clone();
        var ifSlab = $('#ifSlab_1 option').clone();
        var payrollType = $('#payrollType_1 option').clone();

        appendData.find('#expenseGlAutoID_1').attr('id', '')
        appendData.find('#liabilityGlAutoID_1').attr('id', '')
        appendData.find('#ifSlab_1').attr('id', '')
        appendData.find('#payrollType_1').attr('id', '')

        appendData.find(':last-child').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');

        social_insurance_tb.append(appendData);
        var lenght = $('#socialinsurance-add-tb tbody tr').length - 1;

        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.expenseGlAutoID').html(expneceGL);
        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.liabilityGlAutoID').html(liabilityGL);
        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.ifSlab').html(ifSlab);
        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.payrollType').html(payrollType);
        $('select.select2').select2();
    }

    function updateSocialInsurance() {
        var postData = $('#editSocialInsurance_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/editPayeeMaster'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#editModal').modal('hide');
                    load_socialInsurance($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function updatePayGroupDetails() {
        var formData = $('#updatePayGroupDetails_form').serializeArray();
        formData.push({'name': 'payGroupId', 'value': pgId})
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/updatePayGroupDetails'); ?>',
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })
    }

    function enableSlab(item) {
        if ($(item).is(':checked')) {
            $(item).closest('tr').find('.ifSlab').attr('disabled', false)
            $(item).next('input[name="isSlabHidden[]"]').val(1)
        } else {
            $(item).closest('tr').find('.ifSlab').attr('disabled', true)
            $(item).next('input[name="isSlabHidden[]"]').val(0)
        }
    }

    function changeSlab(item) {
        $(item).closest('tr').find('input[name="ifSlabHidden[]"]').val($(item).val())
    }
</script>