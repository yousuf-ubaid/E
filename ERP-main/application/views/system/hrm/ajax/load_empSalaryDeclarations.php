<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 15px;
        margin: auto;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .right-align{ text-align: right; }

    .more-info-btn{
        border-radius: 0px;
        font-size: 11px;
        line-height: 1.5;
        padding: 1px 7px;
    }
</style>

<div class="row">
    <?php
    if($access != 1){
        $this->lang->load('hrms_reports', $primaryLanguage);
        echo '<div class="col-sm-12">
                <div class="alert alert-warning">
                    <strong>'.$this->lang->line('hrms_reports_warning').'!</strong></br>
                    '.$this->lang->line('hrms_reports_no_rights').'
                </div>
              </div>';
        die();
    }
    ?>
    <div class="col-sm-6">
        <fieldset>
            <legend><?php echo $this->lang->line('emp_bank_payroll');?><!--Payroll--></legend>
            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_additions');?><!--Additions--> </h3>
                    <button type="button" class="btn btn-primary btn-xs pull-right navdisabl " onclick="fetchSalaryDeclarationHistory('N')">
                        <i class="fa fa-bars"></i> <?php echo $this->lang->line('emp_salary_detail_salary');?>
                    </button>
                </div>
                <div class="box-body declarationAddition" style="padding: 0px">
                    <table class="table table-bordered" id="add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--> <span class="pull-right empCurrencyDis"></span></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $totAdd = 0;
                        if( !empty($groupBYSalary) ){
                            foreach($groupBYSalary as $keyAdd=>$rowAdd){
                                if($rowAdd->salaryCategoryType == 'A'){
                                    echo '<tr>
                                    <td>'.$rowAdd->salaryDescription.'</td>
                                    <td>'.$rowAdd->transactionCurrency.'</td>
                                    <td align="right">'.number_format( $rowAdd->amount, $dPlaces ).'</td>
                                  </tr>';
                                    $totAdd += round( $rowAdd->amount, $dPlaces);
                                }
                            }
                        }
                        ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2" align="right"><?php echo $this->lang->line('emp_salary_total');?><!--Total--></td>
                            <td align="right"><?php echo number_format( $totAdd, $dPlaces ) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_deductions');?><!--Deductions--></h3>
                </div>

                <div class="box-body declarationDeduction" style="padding: 0px">
                    <table class="table table-bordered" id="deduct_declarationTB">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('emp_description');?><!-- Description--></th>
                            <th><?php echo $this->lang->line('common_currency');?> <!--Currency--></th>
                            <th><?php echo $this->lang->line('emp_salary_amount');?> <!--Amount--> <span class="pull-right empCurrencyDis"></span></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        $totDeductions = 0;
                        if( !empty($groupBYSalary) ){
                            foreach($groupBYSalary as $keyAdd=>$rowAdd){
                                if($rowAdd->salaryCategoryType == 'D'){
                                    echo '<tr>
                                <td>'.$rowAdd->salaryDescription.'</td>
                                <td>'.$rowAdd->transactionCurrency.'</td>
                                <td align="right">'.number_format( $rowAdd->amount, $dPlaces).'</td>
                              </tr>';

                                    $totDeductions += round( $rowAdd->amount, $dPlaces );
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" align="right"><?php echo $this->lang->line('emp_salary_total');?><!--Total--></td>
                            <td align="right"><?php echo number_format( $totDeductions, $dPlaces) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_net_salary');?><!--Net Salary--></h3>
                    <div class="box-tools pull-right">
                        <div class="box-title" id="netSalary" style="font-size: 18px; margin-top: 7px;">
                            <?php echo number_format( ($totAdd+$totDeductions) , $dPlaces) ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="col-sm-6">
        <fieldset>
            <legend><?php echo $this->lang->line('emp_bank_non_payroll');?><!--Non Payroll--></legend>

            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_additions');?><!--Additions--> </h3>
                    <button type="button" class="btn btn-primary btn-xs pull-right navdisabl " onclick="fetchSalaryDeclarationHistory('Y')">
                        <i class="fa fa-bars"></i> <?php echo $this->lang->line('emp_salary_detail_salary');?>
                    </button>
                </div>
                <div class="box-body declarationAddition" style="padding: 0px">
                    <table class="table table-bordered" id="add_declarationTB">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('emp_description');?> <!--Description--></th>
                            <th><?php echo $this->lang->line('common_currency');?> <!--Currency--></th>
                            <th><?php echo $this->lang->line('emp_salary_amount');?> <!--Amount--> <span class="pull-right empCurrencyDis"></span></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $grandTot = round( ($totAdd+$totDeductions) , $dPlaces);
                        $totAdd = 0;
                        if( !empty($salaryDetNon) ){
                            foreach($salaryDetNon as $keyAdd=>$rowAdd){
                                if($rowAdd->salaryCategoryType == 'A'){
                                    echo '<tr>
                                    <td>'.$rowAdd->salaryDescription.'</td>
                                    <td>'.$rowAdd->transactionCurrency.'</td>
                                    <td align="right">'.number_format( $rowAdd->amount, $dPlaces ).'</td>
                                  </tr>';
                                    $totAdd += round( $rowAdd->amount, $dPlaces);
                                }
                            }
                        }
                        ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="2" align="right"><?php echo $this->lang->line('emp_salary_total');?><!--Total--></td>
                            <td align="right"><?php echo number_format( $totAdd, $dPlaces ) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_deductions');?><!--Deductions--></h3>
                </div>

                <div class="box-body declarationDeduction" style="padding: 0px">
                    <table class="table table-bordered" id="deduct_declarationTB">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('emp_description');?> <!--Description--></th>
                            <th><?php echo $this->lang->line('common_currency');?> <!--Effective Date--></th>
                            <th><?php echo $this->lang->line('emp_salary_amount');?> <!--Amount--> <span class="pull-right empCurrencyDis"></span></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php

                        $totDeductions = 0;
                        if( !empty($salaryDetNon) ){
                            foreach($salaryDetNon as $keyAdd=>$rowAdd){
                                if($rowAdd->salaryCategoryType == 'D'){
                                    echo '<tr>
                                <td>'.$rowAdd->salaryDescription.'</td>
                                <td>'.$rowAdd->transactionCurrency.'</td>
                                <td align="right">'.number_format( $rowAdd->amount, $dPlaces).'</td>
                              </tr>';

                                    $totDeductions += round( $rowAdd->amount, $dPlaces );
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" align="right"><?php echo $this->lang->line('emp_salary_total');?><!--Total--></td>
                            <td align="right"><?php echo number_format( $totDeductions, $dPlaces) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                    <h3 class="box-title"><?php echo $this->lang->line('emp_salary_net_salary');?><!--Net Salary--></h3>
                    <div class="box-tools pull-right">
                        <div class="box-title" id="netSalary" style="font-size: 18px; margin-top: 7px;">
                            <?php echo number_format( ($totAdd+$totDeductions) , $dPlaces) ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>

<div style="height: 2%">&nbsp;</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid">
            <div class="box-header with-border" style="border-top: 1px solid #f4f4f4">
                <h3 class="box-title"><?php echo $this->lang->line('emp_salary_grand_total');?> </h3>
                <div class="box-tools pull-right">
                    <div class="box-title" id="netSalary" style="font-size: 18px; margin-top: 7px;">
                        <?php
                        $grandTot += round( ($totAdd+$totDeductions) , $dPlaces);
                        echo number_format( $grandTot, $dPlaces) ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <fieldset>
            <legend><?php echo $this->lang->line('common_variable_pay_declarations');?></legend>

            <table class="<?php echo table_class() ?> drill-table" >
                <thead>
                <tr>
                    <th style="width: 30px"> # </th>
                    <th style="width: 120px"><?php echo $this->lang->line('common_document_code');?></th>
                    <th style=""> <?php echo $this->lang->line('common_category');?> </th>
                    <th style="width: 105px; "> <?php echo $this->lang->line('common_effective_date');?> </th>
                    <th style="width: 110px"> <?php echo $this->lang->line('common_amount');?> </th>
                    <th style=""> <?php echo $this->lang->line('common_narration');?> </th>
                    <th style="width: 40px"> </th>
                </tr>
                </thead>

                <tbody>
                <?php
                if(!empty($vpDeclarations)){
                    $dPlace = $vpDeclarations[0]['trCurrencyDPlaces'];
                    $i = 1;
                    foreach ($vpDeclarations as $key=>$det){
                        echo '<tr>
                                <td class="right-align">'.$i.'</td>                                           
                                <td >'.$det['documentCode'].'</td>
                                <td >'.$det['salaryDescription'].'</td>
                                <td >'.$det['effectiveDate'].'</td>
                                <td class="right-align">'.number_format($det['amount'], $dPlace).'</td>       
                                <td class=""> '.$det['narration'].'</td>  
                                <td class="right-align">                                
                                    <button class="btn btn-default btn-xs more-info-btn" type="button" 
                                        onclick="load_vp_history(\''.$det['salaryCategoryID'].'\')" rel="tooltip" title="History">
                                        <i class="fa fa-info" aria-hidden="true" style="color: #1b1b1b"></i>
                                    </button>
                                </td>                                                                        
                              </tr>';
                        $i++;
                    }
                }
                else{
                    $no_record_found = $this->lang->line('common_no_records_found');
                    echo '<tr><td colspan="7" align="center">'.$no_record_found.'</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </fieldset>
    </div>
</div>

<!-- Accommodation  -->
<div style="height: 8%">&nbsp;</div>
<hr>
<div class="row">
    <div class="col-sm-12">
        <fieldset>
            <legend><?php echo $this->lang->line('common_accomodation');?></legend>

            <div style="display: flex; gap: 10px; margin-top: 10px;" class="pull-right">
                <button type="button" class="btn btn-primary" onclick="addAccommodation()">
                    <i class="fa fa-plus"></i><?php echo $this->lang->line('common_accomodation_add'); ?>
                </button>
                <button type="button" class="btn btn-primary"
                    onclick="assignAccommodation()"><i class="fa fa-plus"></i><?php echo $this->lang->line('common_add_accomodation_to_employee'); ?>
                </button>
            </div>
            <div style="height: 5%">&nbsp;</div>

            <table class="<?php echo table_class() ?> drill-table" >
                <thead>
                <tr>
                    <th style="width: 10px"> # </th>
                    <!-- <th style="width: 20px"><?php echo $this->lang->line('common_employee');?></th> -->
                    <th style="width: 20px"><?php echo $this->lang->line('common_accomodation_type');?></th>
                    <th style="width: 50px"><?php echo $this->lang->line('common_narration');?></th>
                    <th style="width: 40px">Action </th>
                </tr>
                </thead>

                <tbody id="emp_accommodation_tbody">
                    <!-- Table rows will be dynamically inserted here -->
                </tbody>
            </table>
        </fieldset>
    </div>
</div>

<div class="modal fade" id="salaryDeclarationHistory" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <?php echo $this->lang->line('emp_salary_salary_declaration_detail');?><!--Salary Declaration Detail-->
                </h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div style="margin-top: 3%">
                        <table class="<?php echo table_class(); ?>" id="salaryDeclarationHistoryTable">
                            <thead>
                            <tr>
                                <th> # </th>
                                <th><?php echo $this->lang->line('emp_description');?> <!--Description--></th>
                                <th><?php echo $this->lang->line('emp_salary_amount');?> <!--Amount--> </th>
                                <th> <?php echo $this->lang->line('emp_salary_effective_date');?><!--Effective Date--></th>
                                <th> <?php echo $this->lang->line('emp_pay_date');?><!--Pay Date--></th>
                                <th> <?php echo $this->lang->line('common_code');?><!--Code--></th>
                                <th> <?php echo $this->lang->line('emp_salary_comment');?><!--Comment--></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vpDeclarationHistory" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <?php echo $this->lang->line('common_variable_pay_declarations_history');?>
                </h3>
            </div>
            <div role="form" id="" class="form-horizontal">
                <div class="modal-body">
                    <div style="margin-top: 3%">
                        <table class="<?php echo table_class(); ?>" id="vpDeclarationHistoryTable">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th style="width: 120px"><?php echo $this->lang->line('common_document_code');?></th>
                                <th style=""> <?php echo $this->lang->line('common_category');?> </th>
                                <th style="width: 105px; "> <?php echo $this->lang->line('common_effective_date');?> </th>
                                <th style="width: 110px"> <?php echo $this->lang->line('common_amount');?> </th>
                                <th style=""> <?php echo $this->lang->line('common_narration');?> </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Accommodation Type -->
<div class="modal fade" id="add_accommodation" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="reset_accomadation()" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title add_accommodation_Title" id="myModalLabel"><?php echo $this->lang->line('common_accomodation_add');?><!--Add Accommodation--></h4>
            </div>
            <form class="form-horizontal" id="add_accommodation_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accommodation"><?php echo $this->lang->line('common_description');?><!--Accommodation Name--></label>
                        <div class="col-sm-6">
                            <input type="text" name="accommodation_type" id="accommodation_type" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="accommodationSaveBtn" onclick="saveAccommodation()">
                        <?php echo $this->lang->line('emp_save');?><!-- Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" onclick="reset_accomadation()"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Accommodations to employee -->
<div class="modal fade" id="add_accommodation_employee" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="reset_emp_accomadation()" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title add_accommodation_employee_Title" id="myModalLabel"><?php echo $this->lang->line('common_add_accomodation_to_employee');?><!--Add Accommodation to Employee--></h4>
            </div>
            <form class="form-horizontal" id="add_accommodation_employeeform">
                <div class="modal-body">
                    <input type="hidden" id="employeeAccomID" name="employeeAccomID">

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accommodation"><?php echo $this->lang->line('common_accomodation');?><!--Accommodation--></label>
                        <div class="col-sm-6">
                            <select id="accommodation_emp_type" name="accommodation_emp_type" class="form-control">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="accommodation"><?php echo $this->lang->line('common_narration');?><!--Narration--></label>
                        <div class="col-sm-6">
                            <input type="text" name="acc_narration" id="acc_narration" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="accommodationEmpSaveBtn" onclick="saveEmpAccommodation()">
                        <?php echo $this->lang->line('emp_save');?><!-- Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" onclick="reset_emp_accomadation()"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
      $(document).ready(function () {
        empAccommodationView();
      });
    function fetchSalaryDeclarationHistory(isNonPayroll) {
        $('#salaryDeclarationHistory').modal({backdrop: 'static'});

        $('#salaryDeclarationHistoryTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_empSalaryDeclaration'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0]}],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                if (oSettings.bSorted || oSettings.bFiltered) {

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                }
            },
            "aoColumns": [// salaryDescription, amount, effectiveDate, narration
                {"mData": "id"},
                {"mData": "salaryDescription"},
                {"mData": "amountTrAlign"},
                {"mData": "effectiveDateStr"},
                {"mData": "payDateStr"},
                {"mData": "documentSystemCode"},
                {"mData": "narration"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "empId",
                    "value": <?php echo json_encode(trim($this->input->post('empID'))); ?>
                });
                aoData.push({
                    "name": "isNonPayroll",
                    "value": isNonPayroll
                });
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

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
    }

    function load_vp_history(catID){
        $('#vpDeclarationHistory').modal({backdrop: 'static'});

        $('#vpDeclarationHistoryTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_empVariablePayDeclaration'); ?>",
            "aaSorting": [[1, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0]}],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                if (oSettings.bSorted || oSettings.bFiltered) {

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "documentSystemCode"},
                {"mData": "salDec"},
                {"mData": "effectiveDate"},
                {"mData": "amountTrAlign"},
                {"mData": "narration"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "empID", "value": <?php echo json_encode(trim($this->input->post('empID'))); ?> });
                aoData.push({ "name": "catID", "value": catID });

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

    function addAccommodation(){
        $('#add_accommodation').modal({backdrop: 'static'});
    }

    function saveAccommodation(){
        var add_accommodation_form = $('#add_accommodation_form'); 
        var postData = add_accommodation_form.serialize();
        var url = add_accommodation_form.attr('action');
        $.ajax({
            type: 'post',
            url:'<?php echo site_Url('Employee/saveAccommodation') ?>',
            data:postData,
            dataType:'json',
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                stopLoad();
                if (data.status === 'success') {
                    myAlert('s', data.message); 
                    reset_accomadation();
                } else {
                    myAlert('e', data.message); 
                }
            },
            error:function(){
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function reset_accomadation(){
        $('#accommodation_type').val('');
        $('#add_accommodation').modal('hide');
    }

    function reset_emp_accomadation(){
        $('#accommodation_emp_type').val('').change();
        $('#acc_narration').val('');
        $('#add_accommodation_employee').modal('hide');
    }
    
    function assignAccommodation(){
        
        $.ajax({
            type: 'post',
            url:'<?php echo site_Url('Employee/getaccomodation') ?>',
            dataType:'json',
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                var $select = $('#accommodation_emp_type');
                $select.empty(); 
                var defaultOption = $('<option></option>')
                    .attr('value', '')
                    .text('Select Accommodation');
                $select.append(defaultOption);

                $.each(data, function(index, item) {
                    var option = $('<option></option>')
                        .attr('value', item.id) 
                        .text(item.description); 
                    $select.append(option);
                });

                $('#add_accommodation_employee').modal({backdrop:'static'});
            },
            error:function(){
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }
    
    function assignAccommodationEdit(selectedValue){
        
        $.ajax({
            type: 'post',
            url:'<?php echo site_Url('Employee/getaccomodation') ?>',
            dataType:'json',
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                var $select = $('#accommodation_emp_type');
                $select.empty(); 
                var defaultOption = $('<option></option>')
                    .attr('value', '')
                    .text('Select Accommodation');
                $select.append(defaultOption);

                $.each(data, function(index, item) {
                    var option = $('<option></option>')
                        .attr('value', item.id) 
                        .text(item.description); 
                    $select.append(option);
                });

                if (selectedValue) {
                $select.val(selectedValue).change();
            }
            },
            error:function(){
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function saveEmpAccommodation(){
        var add_accommodation_employeeform = $('#add_accommodation_employeeform'); 
        var postData = add_accommodation_employeeform.serialize();
        var url = add_accommodation_employeeform.attr('action');
        $.ajax({
            type: 'post',
            url:'<?php echo site_Url('Employee/saveEmpAccommodation') ?>',
            data:postData,
            dataType:'json',
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                if (data.status === 'success') {
                    myAlert('s', data.message); 
                    reset_emp_accomadation();
                    empAccommodationView();
                    $('#employeeAccomID').val('');
                } else {
                    myAlert('e', data.message); 
                }
            },
            error:function(){
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function deleteEmpAcco(accID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    type: 'post',
                    url:'<?php echo site_Url('Employee/deleteEmpAccommodation') ?>',
                    data:{ accID: accID },
                    dataType:'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success:function(data){
                        stopLoad();
                        if (data[0] === 's') {
                            myAlert('s', data[1]); 
                            reset_emp_accomadation();
                            empAccommodationView();
                        } else if (data[0] === 'e') {
                            myAlert('e', data[1]); 
                        }
                    },
                    error:function(){
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    }
                });
            });
    }

    function empAccommodationView() {
        $.ajax({
            url: '<?php echo site_url('Employee/empAccommodationView') ?>',
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                var $tbody = $('#emp_accommodation_tbody');
                $tbody.empty(); 

                if (data.length > 0) {
                    var i = 1;
                    $.each(data, function(index, det) {
                        var row = '<tr>' +
                                '<td class="right-align">' + i + '</td>' +
                                // '<td>' + det.Ename2 + '</td>' +
                                '<td>' + det.description + '</td>' +
                                '<td>' + det.narration + '</td>' +
                                '<td class="right-align">' +
                                    '<button class="btn btn-default btn-xs more-info-btn" type="button" ' +
                                        'onclick="editEmpAcco(\'' + det.empAccID + '\')" rel="tooltip" title="History">' +
                                        '<i class="fa fa-pencil" aria-hidden="true" style="color: #1b1b1b"></i>' +
                                    '</button>' +
                                    '&nbsp| &nbsp;' +
                                    '<button class="btn btn-default btn-xs more-info-btn" type="button" ' +
                                        'onclick="deleteEmpAcco(\'' + det.empAccID + '\')" rel="tooltip" title="History">' +
                                        '<i class="fa fa-trash" aria-hidden="true" style="color: #1b1b1b"></i>' +
                                    '</button>' +
                                '</td>' +
                                '</tr>';
                        $tbody.append(row);
                        i++;
                    });
                } else {
                    var noRecordFound = '<?php echo $this->lang->line('common_no_records_found'); ?>';
                    $tbody.append('<tr><td colspan="5" align="center">' + noRecordFound + '</td></tr>');
                }
            },
            error: function() {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function editEmpAcco(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_edit'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    type: 'post',
                    url:'<?php echo site_Url('Employee/getEmpaccommodation') ?>',
                    data:{ id: id },
                    dataType:'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success:function(data){
                        stopLoad();

                        
                        $('#acc_narration').val(data['narration']);
                        $('#add_accommodation_employee').modal({backdrop: 'static'});
                        assignAccommodationEdit(data['accomadationID']);
                        $('#employeeAccomID').val(id);
                        $('#accommodation_emp_type').val(data['accomadationID']).change();
                    },
                    error:function(){
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    }
                });
            });
    }

</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2017-03-20
 * Time: 11:50 AM
 */