<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('bank_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('bank_employees_bank_master');
echo head_page($this->lang->line('new_bank_employees_employeegrademaster'), false);

$allowance_arr = load_allowances();
?>
<style>
    #setup-title{
        font-size: 12px;
        font-weight: bold;
    }

    .hide-row{
        display: none;
    }

    #allowance .multiselect-container {
        width: 100% !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">

        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="newGrade()">
            <i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add'); ?><!-- Add -->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="empGradeTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_grade') ?></th>
            <th style="min-width: 5%"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="gradeModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title gradeMaster-title" id="myModalLabel">New Grade</h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="gradeMaster_form"'); ?>
            <input type="hidden" name="gradeID" id="gradeID"/>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="gradeDescription"><?php echo $this->lang->line('common_grade') ?> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="gradeDescription" id="gradeDescription" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                    <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeSalarySetup_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Salary setup : <span id="setup-title"> </span> </h4>
            </div>

            <div class="m-b-md" id="wizardControl">
                <div class="steps">
                    <a class="step-wiz step--incomplete step--active sal" href="#step1" onclick="steps1()" data-toggle="tab">
                        <span class="step__icon"></span>
                        <span class="step__label">Salary<!--Step 1 - Salary--></span>
                    </a>
                    <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="steps2()" data-toggle="tab">
                        <span class="step__icon"></span>
                        <span class="step__label">Allowances <!--Step 2 - Allowances--></span>
                    </a>
                </div>
            </div>

            <div class="tab-content">
                <div id="step1" class="tab-pane active">
                    <?php echo form_open('', 'role="form" class="form-horizontal" id="gradeSetup_form"'); ?>
                    <input type="hidden" name="gradeID" id="setup_gradeID"/>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-5 col-xs-6">
                                <div class="dataTables_filter">
                                    <label>
                                        <?=$this->lang->line('common_air_ticket_enhancemnet');?> :
                                        <input type="text" class="form-control numeric" name="air_ticket" id="air_ticket"  placeholder="Enter Air Ticket Enhancement">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-7 col-xs-6">
                                <div class="dataTables_info" id="attendanceMasterTB_info" role="status" aria-live="polite">
                                    <b>Showing <span id="display-count"> </span> of  <span id="total-count"></span> entries</b>
                                </div>
                            </div>
                            <div class="col-sm-5 col-xs-6">
                                <div class="dataTables_filter">
                                    <label>
                                        <b><?=$this->lang->line('common_filter');?> :</b>
                                        <input type="search" class="form-control input-sm" id="filter" value="" placeholder="Type here...">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="fixHeader_Div" style="height: 435px">
                            <table class="table table-bordered" id="category-tbl">
                                <thead>
                                <tr>
                                    <th> # </th>
                                    <th> <?php echo $this->lang->line('common_salary_category');?> </th>
                                    <th> <?php echo $this->lang->line('common_start_range');?> </th>
                                    <th> <?php echo $this->lang->line('common_mid_range');?> </th>
                                    <th> <?php echo $this->lang->line('common_end_range');?> </th>
                                </tr>
                                </thead>

                                <tbody id="setup-data">
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_salarySetup()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save-->
                        </button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                <div id="step2" class="tab-pane">
                    <input type="hidden" name="gradeID" id="set_gradeID"/>
                    <div class="modal-body">
                        <div class="row" style="margin-top:20px;">
                            <div class="col-sm-1"></div>
                            <div class="col-sm-10">
                                    <table class="table table-bordered table-striped table-condensed mx-auto">
                                        <thead>
                                            <tr>
                                                <th>Allowance</th>
                                                <th><?php echo $this->lang->line('common_max_val'); ?></th>
                                                <th style="width:20%">Is Active</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activityCode_based_table_body">
                                            <?php if (isset($allowance_arr) && !empty($allowance_arr)) { ?>
                                                <?php foreach ($allowance_arr as $rep) { ?>
                                                    <tr>
                                                        <td><?php echo isset($rep['monthlyDeclaration']) ? $rep['monthlyDeclaration'] : '-'; ?></td>

                                                        <td>
                                                            <input type="text" class="numeric form-control maxAllowance" id="maxAllowance_<?= $rep['monthlyDeclarationID']; ?>" name="maxAllowance[]" placeholder="Not-assigned">
                                                        </td>


                                                        <td class="text-center"><input type="checkbox" class="allowance-checkbox" name="isActive[]" id="isActive_<?php echo $rep['monthlyDeclarationID']; ?>" value="<?php echo $rep['monthlyDeclarationID']; ?>"></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr class="danger">
                                                    <td colspan="2" class="text-center">
                                                        <b><?php echo $this->lang->line('common_no_records_found'); ?><!-- No Records Found --></b>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                            </div>
                            <div class="col-sm-1"></div>
                        </div>
                    </div> 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_allowance()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var gradeMaster_form = $('#gradeMaster_form');
    var oTable;
    var airTicketInput = document.getElementById('air_ticket');

    
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/emp_grade_master', 'Test', 'HRMS');
        });

        $('.maxAllowance').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.slice(0, -1); 
            }
        });

        $('#allowance').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '100%',
                maxHeight: '30px'
        });

        airTicketInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');

            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.substring(0, this.value.lastIndexOf('.'));
            }
        });

        gradeMaster_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                gradeDescription: {validators: {notEmpty: {message: 'Grade is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var postData = $form.serialize();
            $.ajax({
                type: 'post',
                url: "<?php echo site_url('Employee/saveGrade') ?>",
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    else if (data['error'] == 0) {
                        oTable.draw();
                        $('#gradeModal').modal('hide');
                        myAlert('s', data['message']);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        });

        empGradeTB();
    });

    function empGradeTB() {
        oTable = $('#empGradeTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_grade'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "gradeID"},
                {"mData": "gradeDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": [0]},
                {"orderable": false, "targets": [2]}
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

    function newGrade() {
        $('.gradeMaster-title').text('<?php echo $this->lang->line('new_bank_employees_newgrademaster') ?>');
        gradeMaster_form[0].reset();
        gradeMaster_form.bootstrapValidator('resetForm', true);
        $("#gradeID").val('');
        $('#gradeModal').modal({backdrop: "static"});
    }

    function editGrade(gradeID,element) {
        $('.gradeMaster-title').text('<?php echo $this->lang->line('new_bank_employees_editgrademaster') ?>');
        $('#gradeID').val(gradeID);
        $('#gradeDescription').val($(element).data('description'));
        $('#gradeModal').modal({backdrop: "static"});
    }

    function deleteGrade(gradeID) {
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
                    url: "<?php echo site_url('Employee/deleteGrade'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'gradeID': gradeID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            myAlert('e', data['message']);
                        }
                        else if (data['error'] == 0) {
                            oTable.draw();
                            myAlert('s', data['message']);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');

                    }
                });
            }
        );
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function salarySetup(id, gradeDec){
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/fetch_grade_salary'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'gradeID': id},
            beforeSend: function () {
                $('#setup_gradeID').val(id);
                $('#set_gradeID').val(id);
                $('#setup-title').text(gradeDec);
                $('#filter').val('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    $('#total-count, #display-count').text(data['dis_count']);
                    $('#air_ticket').val(data['air_ticket_enhancement']);
                    $('#setup-data').html(data['table_view']);

                    $('#category-tbl').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 10
                    });

                    $('.range-text').numeric({
                        negative: false,
                        decimalPlaces: data['dPlace']
                    });

                    setTimeout(function() {
                        //$('.allowance-checkbox').prop('checked', false);
                        $('#step1').addClass('active');
                        $('#step2').removeClass('active');
                        steps1();
                        $('#gradeSalarySetup_modal').modal('show');
                    }, 200);
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');

            }
        });
    }

    function save_salarySetup(){
        let postData = $('#gradeSetup_form').serializeArray();
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/save_gradeSalarySetup'); ?>",
            type: 'post',
            dataType: 'json',
            data:postData,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['error'], data['message']);

                if (data['error'] == 's') {
                    //$('#gradeSalarySetup_modal').modal('hide');
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');

            }
        });
    }

    $('#filter').keyup(function () {
        let searchText = new RegExp($(this).val(), 'i');
        var setup_data_tr = $('#setup-data tr');
        setup_data_tr.addClass('hide-row');

        setup_data_tr.filter(function () {
            return searchText.test($(this).text());
        }).removeClass('hide-row');

        $('#display-count').text( setup_data_tr.not('.hide-row').length );

        setup_data_tr.not('.hide-row').each(function(i, obj){
            $(obj).find('td:eq(0)').text( (i+1) );
        });
    });

    function save_allowance(){
        var gradeID = $('#set_gradeID').val();
        var allowances = {};
        $('.allowance-checkbox').each(function() {
            var id = $(this).val();
            var isChecked = $(this).is(':checked') ? 1 : 0;
            allowances[id] = isChecked;
        });

        var maxAllowance = [];
        $('.maxAllowance').each(function() {
            maxAllowance.push($(this).val());
        });

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/save_allowance'); ?>",
            type: 'post',
            dataType: 'json',
            data:{'allowances': allowances, 'gradeID': gradeID, 'maxAllowance':maxAllowance},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#gradeSalarySetup_modal').modal('hide');
                    empGradeTB();
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function steps1(){
        $('.sal').removeClass('step--inactive');
        $('.sal').addClass('step--active');
        $('.btn-wizard').addClass('step--inactive');
        $('.btn-wizard').removeClass('step--active');
    };

    function steps2()
    {
        $('.btn-wizard').removeClass('step--inactive');
        $('.btn-wizard').addClass('step--active');
        var gradeID = $('#set_gradeID').val();

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/fetch_grade_allowance'); ?>",
            type: 'post',
            dataType: 'json',
            data: {'gradeID': gradeID},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                data.declarationTypeIDs.forEach(function (declarationTypeID, index) {
                    if (data.isActive[index] == 1) {
                        $('#isActive_' + declarationTypeID).prop('checked', true);
                    } else {
                        $('#isActive_' + declarationTypeID).prop('checked', false);
                    }

                    if (data.maxAllowanceAmount[index] != '') {
                    $('#maxAllowance_' + declarationTypeID).val(data.maxAllowanceAmount[index]);
                } else {
                    $('#maxAllowance_' + declarationTypeID).val('');
                }
                });
                
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    };
</script>


<?php
