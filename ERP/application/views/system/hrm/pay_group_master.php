

<!--Translation added by Naseek-->
<?php
$companyID = current_companyID();
$expenseGL = expenseGL_drop();
$liabilityGL = liabilityGL_drop();

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
$title = $this->lang->line('hrms_payroll_paysheet_grouping');
echo head_page($title, false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openSocialInsurance_modal()">
            <i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="load_payGroupMaster" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 80px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="new_social_insurance" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_add_pay_group');?><!--Add Pay Group--></h4>
            </div>
            <form class="form-horizontal" id="add-social_insurance_form">
                <div class="modal-body">
                    <table class="table table-bordered" id="socialinsurance-add-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <th class="hide"><?php echo $this->lang->line('hrms_payroll_is_group_total');?><!--Is Group Total--></th>
                            <th class="hide"><?php echo $this->lang->line('hrms_payroll_employee_contribution');?><!--Employee Contribution--></th>
                            <th class="hide"><?php echo $this->lang->line('hrms_payroll_employer_contribution');?><!--Employer Contribution--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="vertical-align: middle;">
                                <input type="text" name="description[]" class="form-control saveInputs new-items"/>
                            </td>
                            <td class="hide" style="text-align: center;vertical-align: middle">
                                <input type="checkbox" name="isGroupTotal[]" onclick="enableGL(this)">
                                <input type="hidden" name="isGroupTotalHidden[]">
                            </td>
                            <td class="hide" style="vertical-align: middle;">
                                <select class="form-control saveInputs expenseGlAutoID" onchange="changeGL(this)"
                                        id="expenseGlAutoID_1" name="expenseGlAutoID[]" disabled>
                                    <option></option>
                                    <?php
                                    foreach ($expenseGL as $key => $row) {
                                        echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="expenseGlAutoIDHidden[]">
                            </td>
                            <td class="hide" style="vertical-align: middle;">
                                <select
                                    class="form-control saveInputs liabilityGlAutoID" id="liabilityGlAutoID_1"
                                    onchange="changeGL(this)"
                                    name="liabilityGlAutoID[]" disabled>
                                    <option></option> <?php
                                    foreach ($liabilityGL as $key => $row) {
                                        echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="liabilityGlAutoIDHidden[]">
                            </td>
                            <td style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_paygroup()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_edit_pay_group');?><!--Edit Pay Group--></h4>
            </div>

            <form role="form" id="editPayGroup_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="socialinsurance-edit-tb">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('hrms_payroll_edit_pay_group');?><!--Description--></th>
                                    <th class="hide"><?php echo $this->lang->line('hrms_payroll_is_group_total');?><!--Is Group Total--></th>
                                    <th class="hide"><?php echo $this->lang->line('hrms_payroll_employee_contribution');?><!--Employee Contribution--></th>
                                    <th class="hide"><?php echo $this->lang->line('hrms_payroll_employer_contribution');?><!--Employer Contribution--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <input type="text" name="pgDes" id="pgDes" class="form-control saveInputs"/>
                                        <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                    </td>
                                    <td class="hide" style="text-align: center;vertical-align: middle">
                                        <input type="checkbox" name="pgIsGroupTotal" id="pgIsGroupTotal">
                                    </td>
                                    <td class="hide" style="vertical-align: middle;">
                                        <select class="form-control saveInputs" id="pgexpenseGlAutoID"
                                                name="pgexpenseGlAutoID">
                                            <option></option>
                                            <?php
                                            foreach ($expenseGL as $key => $row) {
                                                echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="hide" style="vertical-align: middle;">
                                        <select class="form-control saveInputs" id="pgLiabilityGlAutoID"
                                                name="pgLiabilityGlAutoID">
                                            <option></option>
                                            <?php
                                            foreach ($liabilityGL as $key => $row) {
                                                echo '<option value="' . $row['GLAutoID'] . '" > ' . $row['GLSecondaryCode'] . ' | ' . $row['GLDescription'] . '</option>';
                                            }
                                            ?>
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
                <button type="button" class="btn btn-primary btn-sm" onclick="updatePayGroup()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php
$items = [
    'MA_MD' => true,
    'balancePay' => true,
    'SSO' => true,
    'payGroup' => true,
    'only_salCat_payGroup' => false
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var social_insurance_tb = $('#socialinsurance-add-tb');
    var pgId = null;
    var urlSave = '<?php echo site_url('Employee/saveFormula_new') ?>';
    var isPaySheetGroup = 1;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/pay_group_master', 'Test', 'HRMS');
        });
        load_payGroupMaster();

    });

    function load_payGroupMaster(selectedRowID=null) {
        $('#load_payGroupMaster').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_paygroupmaster'); ?>",
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

            },
            "aoColumns": [
                {"mData": "payGroupID"},
                {"mData": "description"},
                {"mData": "actions"}
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

    function openSocialInsurance_modal() {
        $('#socialinsurance-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_social_insurance').modal({backdrop: "static"});
    }

    function save_paygroup() {
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
                url: '<?php echo site_url('Employee/savePayGroup'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#new_social_insurance').modal('hide');
                        load_payGroupMaster();
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

    function edit_paygroup(id, des, isGroupTotal, expenseGlAutoID, liabilityGlAutoID) {
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val($.trim(id));
        $('#pgDes').val($.trim(des));
        $('#pgexpenseGlAutoID').val($.trim(expenseGlAutoID));
        $('#pgexpenseGlAutoID').val($.trim(liabilityGlAutoID));

        if (isGroupTotal == 1) {
            $('#pgIsGroupTotal').prop('checked', true)
        } else {
            $('#pgIsGroupTotal').prop('checked', false)
        }
    }

    function delete_paygroup(id) {
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
                    url: "<?php echo site_url('Employee/deletePayGroup'); ?>",
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
                            load_payGroupMaster()
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
        var appendData = $('#socialinsurance-add-tb tbody tr:first').clone();
        appendData.find('input').val('')
        var expneceGL = $('#expenseGlAutoID_1 option').clone();
        var liabilityGL = $('#liabilityGlAutoID_1 option').clone();

        appendData.find('#expenseGlAutoID_1').attr('id', '')
        appendData.find('#liabilityGlAutoID_1').attr('id', '')

        appendData.find(':last-child').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        social_insurance_tb.append(appendData);
        var lenght = $('#socialinsurance-add-tb tbody tr').length - 1;

        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.expenseGlAutoID').html(expneceGL);
        $('#socialinsurance-add-tb tbody tr:eq(' + lenght + ')').find('.liabilityGlAutoID').html(liabilityGL);
    }

    function updatePayGroup() {
        var postData = $('#editPayGroup_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/updatePayGroup'); ?>',
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
                    load_payGroupMaster($('#hidden-id').val());
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

    function enableGL(item) {
        if ($(item).is(':checked')) {
            $(item).closest('tr').find('.expenseGlAutoID').attr('disabled', false)
            $(item).closest('tr').find('.liabilityGlAutoID').attr('disabled', false)
            $(item).next('input[name="isGroupTotalHidden[]"]').val(1)
        } else {
            $(item).closest('tr').find('.expenseGlAutoID').attr('disabled', true)
            $(item).closest('tr').find('.liabilityGlAutoID').attr('disabled', true)
            $(item).next('input[name="isGroupTotalHidden[]"]').val(1)
        }
    }

    function changeGL(item) {
        $(item).next('input[type="hidden"]').val($(item).val())
    }

</script>