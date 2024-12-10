
<!--Translation added by Naseek-->

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_type', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('emp_type_master');
$employeeConType = fetch_sysEmpContractType();

echo head_page($title, false);
?>

<style type="text/css">
    .saveInputs {
        /*height: 25px;
        font-size: 11px*/
    }

    #employeeType-add-tb td {
        padding: 2px;
    }

    .custom-field{ display: none; }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="openEmployeeType_modal()">
            <i class="fa fa-plus-square"></i>&nbsp;<?php echo $this->lang->line('common_add'); ?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="load_employee_types" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"> <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
            <th style="width: auto"> <?php echo $this->lang->line('emp_type'); ?><!--Employee Type--></th>
            <th style="width: 100px"> <?php echo $this->lang->line('common_probation_period'); ?></th>
            <th style="width: 100px"> <?php echo $this->lang->line('common_contract_period'); ?><!--Period--></th>
            <th style="width: 100px"> <?php echo $this->lang->line('common_is_open_contract'); ?></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="new_employeeType" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="employeeType-title"></h4>
            </div>
            <form class="form-horizontal" id="add-employeeType-form" onsubmit="return save_employeeType()" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="description" class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?></label>
                        <div class="col-sm-5">
                            <input type="text" name="description" id="description" class="form-control saveInputs new-items"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="conType" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_type');?></label>
                        <div class="col-sm-5">
                            <?php echo form_dropdown('conType', $employeeConType, null, 'class="form-control saveInputs new-items"
                                id="conType" onchange="periodStatus()"'); ?>
                            <input type="hidden" name="typeID_hidden" id="typeID_hidden" value="">
                        </div>
                    </div>
                    <div class="form-group custom-field contract-ty">
                        <label for="is_open_contract" class="col-sm-4 control-label"><?php echo $this->lang->line('common_is_open_contract');?></label>
                        <div class="col-sm-5">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="is_open_contract" id="is_open_contract_yes" value="1" onchange="contract_change(1)">
                                    </span>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $this->lang->line('common_yes');?>">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="radio" name="is_open_contract" id="is_open_contract_no" value="0" checked onchange="contract_change(0)">
                                    </span>
                                        <input type="text" class="form-control" disabled="" value="<?php echo $this->lang->line('common_no');?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group custom-field pro-period">
                        <label for="probation_period" class="col-sm-4 control-label"><?php echo $this->lang->line('common_probation_period');?></label>
                        <div class="col-sm-3">
                            <input type="text" name="probation_period" class="form-control number" id="probation_period"/>
                        </div>
                    </div>
                    <div class="form-group div-period">
                        <label for="period" class="col-sm-4 control-label"><?php echo $this->lang->line('common_contract_period');?></label>
                        <div class="col-sm-3">
                            <input type="text" name="period" class="form-control saveInputs number" id="period"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span style="color: red; float: left"><?php echo $this->lang->line('emp_period_notice'); ?></span>
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_employeeType()"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var employeeType_tb = $('#load_employee_types');

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/contract_type_master', 'Test', 'HRMS');
        });
        load_employee_types();
    });

    function load_employee_types(selectedRowID=null) {
        employeeType_tb = $('#load_employee_types').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_employee_types'); ?>",
            "aaSorting": [[1, 'desc']],
            "columnDefs": [ {
                "targets": [0,5,6],
                "orderable": false
            }, {"searchable": false, "targets": [0]} ],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['EmpContractTypeID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EmpContractTypeID"},
                {"mData": "Description"},
                {"mData": "employeeType"},
                {"mData": "probationStr"},
                {"mData": "periodStr"},
                {"mData": "isOpen"},
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

    function openEmployeeType_modal() {
        $('#add-employeeType-form')[0].reset();
        $('.saveInputs').val('');
        $('#employeeType-title').text('<?php echo $this->lang->line('emp_type_add_new'); ?>'); <!--Add Employee Type-->
        $('#add-employeeType-form').attr('action', '<?php echo site_url('Employee/saveEmployeeType'); ?>');
        $('#conType').attr('disabled', false);
        $('.custom-field, .div-period').hide();
        $('#new_employeeType').modal({backdrop: "static"});
    }

    function save_employeeType() {
        var employeeType_form = $('#add-employeeType-form');
        var postData = employeeType_form.serialize();
        var url = employeeType_form.attr('action');

        $.ajax({
            type: 'post',
            url: url,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#new_employeeType').modal('hide');
                    load_employee_types(data['autoID'])
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function editEmployeeDetail(id, obj) {
        $('#add-employeeType-form')[0].reset();
        contract_change(0);
        var table = $('#load_employee_types').DataTable();
        var thisRow = $(obj);
        var details = table.row(  thisRow.parents('tr') ).data();

        $('.saveInputs').val('');
        $('#employeeType-title').text('<?php echo $this->lang->line('common_edit_employment_type'); ?>');
        $('#add-employeeType-form').attr('action', '<?php echo site_url('Employee/editEmployeeTypeMaster'); ?>');
        $('#new_employeeType').modal({backdrop: "static"});

        $('#hidden-id').val($.trim(id));
        $('#description').val($.trim(details.Description));


        var contType = $.trim(details.typeID);
        $('#conType').val(contType).attr('disabled', true);
        $('#typeID_hidden').val(contType);
        periodStatus();
        $('#period').val($.trim(details.period));


        if(contType == 2){ /*If contract type*/
            if(parseInt(details.is_open_contract) == 1){
                contract_change(1);
                $('#is_open_contract_yes').prop('checked', true);
            }else{
                $('#is_open_contract_no').prop('checked', true);
            }
        }

        if(contType != 4){ /*If not Intern*/
            $('#probation_period').val($.trim(details.probation_period));
        }
    }

    function deleteEmployeeTypeMaster(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/deleteEmployeeTypeMaster'); ?>",
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
                            employeeType_tb.ajax.reload();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function periodStatus(){
        $('.custom-field, .div-period').hide();

        var contType = parseInt( $('#conType').val() );
        if(contType == 2){
            $('.div-period').show();
        }
        $('#period').val('');

        if(contType == 1 || contType == 3){
            $('.pro-period').show();
        }

        if(contType == 2){
            $('.custom-field').show();
        }
    }

    function contract_change(va){
        $('#period').val('');
        $('.div-period').show();
        if(va == 1){
            $('.div-period').hide();
        }
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    $('.number').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });
</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-03
 * Time: 11:25 AM
 */