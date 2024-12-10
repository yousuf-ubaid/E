<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$transfer_type_arr = transferType();
$transfer_term_arr = transferTerm();

$employee_arr = fetch_all_employees();
// $groups = all_group_drop();
$designations_arr = all_designation_drop();
$department_arr = all_departments_drom();

$manager_arr = all_managers_drom();
$grades_arr = employee_grade_drop();
$locations_arr = all_location_drom();
$group_arr = all_group_drop_PAA();
$company_arr = all_company_drom();
$segment_arr = all_segment_arr_PAA();
$sub_segment_arr = all_sub_segment_arr_PAA();
$division_arr = all_division_drop_PAA();

$activityCode_arr = get_activity_codes();

$company_reporting_currency=$this->common_data['company_data']['company_reporting_currency'];
$company_reporting_DecimalPlaces=$this->common_data['company_data']['company_reporting_decimal'];
?>

<style>
    fieldset {
        /*border: 1px solid silver;*/
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

    /* Add this CSS to your stylesheet */
.shadow-box {
    border: 2px solid #ccc;
    padding: 10px;
    background-color: #fff; /* Change background color to white */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Set shadow underneath */
}
</style>

<input type="hidden" value="<?php echo $id ?>" id="pa_action_ID">
    <div class="row">
        <div class="row col-sm-12" style="text-align:center;">
            <div class="col-sm-4"></div>
            <div class="col-sm-4"><h2>EMPLOYEE TRANSFER FORM</h2></div>
                
                <div class="col-sm-4">
                    <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                </div>
            </div>
        </div>
        
        <hr>

        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <div class="panel-body" id="transfer">
                    <div class="col-md-12 animated pulse">
                        <fieldset style="border: none; padding: 0; margin-bottom: 10px;">
                            <legend></legend>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">COMPANY</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Company']['currentText']) ? $template_data['transfer_details']['Company']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">DIVISION</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Division']['currentText']) ? $template_data['transfer_details']['Division']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">SEGMENT</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Segment']['currentText']) ? $template_data['transfer_details']['Segment']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">SUB SEGMENT</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Sub Segment']['currentText']) ? $template_data['transfer_details']['Sub Segment']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">NAME</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Name']['currentText']) ? $template_data['transfer_details']['Name']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title">DESIGNATION</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Designation']['currentText']) ? $template_data['transfer_details']['Designation']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">EMPLOYEE ID</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EmpCODE']['currentText']) ? $template_data['transfer_details']['EmpCODE']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">GRADE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Grade']['currentText']) ? $template_data['transfer_details']['Grade']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title">DATE OF JOINING</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EDOJ']['currentText']) ? $template_data['transfer_details']['EDOJ']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">LAST REVIEW DATE</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Last Review Date']['currentText']) ? $template_data['transfer_details']['Last Review Date']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">LAST INCREMENT AMOUNT&nbsp;&nbsp;&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency']; ?>)</label>&nbsp;&nbsp;:&nbsp;&nbsp;
                                        <span><?php echo number_format(!empty($template_data['transfer_details']['Last Increment Amount']['currentText']) ? floatval($template_data['transfer_details']['Last Increment Amount']['currentText']) : 0, $company_reporting_DecimalPlaces); ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">

                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-12">
                                    <div class="shadow-box">
                                        <label class="title ">REMARK</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span>
                                            <p style="font-size:14px; word-wrap: break-word; overflow-wrap: break-word;">
                                                <?php echo empty($headerDetails['Remarks']) ? '': $headerDetails['Remarks'] ; ?>
                                            </p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="col-sm-1"></div>
        </div>

        <br>
        <br>

        <div class="row"><h4 class="text-center"><b>CURRENT AND NEW DIVISION & DEPARTMENT</h4></b></div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8 animated pulse">
                <!-- <div class="panel-body" id="transfer" >
                <div class="col-md-12 animated zoomIn"> -->
                <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                <legend></legend>
                        <table class="table table-bordered table-striped table-condensed mx-auto" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="min-width: 10%">DESCRIPTION</th>
                                    <th style="min-width: 10%">CURRENT</th>
                                    <th style="min-width: 10%">NEW</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr><td>COMPANY</td><td><?php echo isset($template_data['transfer_details']['Company']['currentText']) ? $template_data['transfer_details']['Company']['currentText']: '';?></td><td>
                                <?php echo form_dropdown('group', $company_arr, isset($template_data['transfer_details']['Company']['NewValue']) ? $template_data['transfer_details']['Company']['NewValue']: '' , 'class="form-control select2" id="group" onchange="changeValue(\'Company\', this)" required'); ?>
                                </td></tr>
                                
                                <tr><td>REPORTING MANAGER</td><td><?php echo isset($template_data['transfer_details']['Reporting Manager']['currentText']) ? $template_data['transfer_details']['Reporting Manager']['currentText']: '';?></td><td>
                                    <?php echo form_dropdown('managerID', $manager_arr, isset($template_data['transfer_details']['Reporting Manager']['NewValue']) ? $template_data['transfer_details']['Reporting Manager']['NewValue']: '', 'class="form-control select2" id="managerID" onchange="changeValue(\'Reporting Manager\', this)" required'); ?>
                                </td></tr>
                                <tr><td>DESIGNATION</td><td><?php echo isset($template_data['transfer_details']['Designation']['currentText']) ? $template_data['transfer_details']['Designation']['currentText']: ''; ?></td><td>
                                    <?php echo form_dropdown('designationID', $designations_arr, isset($template_data['transfer_details']['Designation']['NewValue']) ? $template_data['transfer_details']['Designation']['NewValue']:'', 'class="form-control select2" id="designationID" onchange="changeValue(\'Designation\', this)" required'); ?>
                                </td></tr>
                                <tr><td>GRADE</td><td><?php echo isset($template_data['transfer_details']['Grade']['currentText']) ? $template_data['transfer_details']['Grade']['currentText']: '';?></td><td>
                                    <?php echo form_dropdown('grade', $grades_arr, isset($template_data['transfer_details']['Grade']['NewValue']) ? $template_data['transfer_details']['Grade']['NewValue']: '', 'class="form-control select2" id="grade" onchange="changeValue(\'Grade\', this)" required'); ?>
                                </td></tr>
                                <tr><td>LOCATION</td><td><?php echo isset($template_data['transfer_details']['Location']['currentText']) ? $template_data['transfer_details']['Location']['currentText']: '';?></td><td>
                                    <?php echo form_dropdown('location', $locations_arr, isset($template_data['transfer_details']['Location']['NewValue']) ? $template_data['transfer_details']['Location']['NewValue']: '', 'class="form-control select2" id="location" onchange="changeValue(\'Location\', this)" required'); ?>
                                </td></tr>
                                <tr><td>ACTIVITY CODE</td><td><?php echo isset($template_data['transfer_details']['Activity Code']['currentText']) ? $template_data['transfer_details']['Activity Code']['currentText']: '';?></td><td>
                                    <?php echo form_dropdown('location', $activityCode_arr, isset($template_data['transfer_details']['Activity Code']['NewValue']) ? $template_data['transfer_details']['Activity Code']['NewValue']: '', 'class="form-control select2" id="location" onchange="changeValue_activecodeType(\'Activity Code\', this)" required'); ?></td>
                                </tr>

                                <!-- <tr class="danger">
                                </tr> -->
                            </tbody>
                            <!--<tfoot id="table_tfoot">
                            </tfoot>-->
                        </table> 
                </fieldset>
                <!-- </div>
                </div> -->
            </div>
            <div class="col-md-2"></div>
        </div>

        <hr>
    
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
            <div class="pull-start">
                <!-- <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;"> -->
                        <table class="table table-bordered table-striped table-condensed mx-auto" style="width:800px;">
                                <thead>
                                    <tr>
                                        <th colspan="3">Reporting Structure</th>
                                    </tr>
                                    <tr>
                                        <th style="min-width: 10%">OLD</th>
                                        <th style="min-width: 10%">NEW</th>   
                                    </tr>
                                </thead>
                                <tbody id="activityCode_based_table_body">
                                    <?php if(isset($reportingData)) { ?>
                                        <td>
                                            <table class="table">
                                                <tbody>
                                                    <?php foreach($reportingData as $rep){ ?>
                                                        <tr>
                                                            <?php  if($rep['ActivityCodeType'] == 1){ ?>
                                                                <td><?php echo isset($rep['currentText']) ? $rep['fieldType'] .' | '. $rep['currentText']: '-'; ?></td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <table class="table">
                                                <tbody>
                                                    <?php foreach($reportingData as $rep){ ?>
                                                        <tr>
                                                            <?php  if($rep['ActivityCodeType'] == 2){ ?>
                                                                <td><?php echo isset($rep['NewValueText']) ? $rep['fieldType'] .' | '. $rep['NewValueText']: '-'; ?></td>
                                                            <?php }?>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    <?php }else{
                                        $norec=$this->lang->line('common_no_records_found');
                                        echo '<tr class="danger"><td colspan="2" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
                                    }?>
                                </tbody>
                        </table>
                <!-- </fieldset> -->
            </div>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>
        <hr>

        <div class="row" style="margin-left: 10px;margin-right: 10px;">
            <div class="form-group col-sm-2"></div>
            <div class="row col-sm-8">
                <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                <div class="col-sm-1"></div>
                <div class="form-group col-sm-5">
                    <div class="row col-sm-12"><h4><label for="tType">TRANSFER TYPE<?php required_mark(); ?></label></h4></div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Type" value="1" data-text="Inter Department Transfer" <?php echo ($template_data['transfer_details']['Transfer Type']['NewValueText']) == 'Inter Department Transfer' ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">INTER DEPARTMENT TRANSFER</label>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Type" value="1" data-text="Transfer to other group companies within OMAN" <?php echo ($template_data['transfer_details']['Transfer Type']['NewValueText']) == 'Transfer to other group companies within OMAN' ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">TRANSFER TO OTHER GROUP COMPANIES WITHIN OMAN</label>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Type" value="1" data-text="International transfer to other RAY international Subsidiaries" <?php echo ($template_data['transfer_details']['Transfer Type']['NewValueText']) == 'International transfer to other RAY international Subsidiaries' ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">INTERNATIONAL TRANSFER TO OTHER RAY INTERNATIONAL SUBSIDIARIES</label>
                    </div>
                </div>
                <div class="col-sm-2"></div>
                <div class="form-group col-sm-3 jd" style="margin-top: 0px;">
                    <div class="row col-sm-12"><h4><label for="tType">TRANSFER TERM<?php required_mark(); ?></label></h4></div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Term" value="1" data-text="Permanent" <?php echo ($template_data['transfer_details']['Transfer Term']['NewValueText'] == 'Permanent' && $template_data['transfer_details']['Transfer Term']['NewValue'] == 'true') ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">PERMANENT</label>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Term" value="1" data-text="Temporary" <?php echo ($template_data['transfer_details']['Transfer Term']['NewValueText'] == 'Temporary' && $template_data['transfer_details']['Transfer Term']['NewValue'] == 'true') ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">TEMPORARY</label>
                    </div>

                    <div class="row col-sm-12" style="margin-top: 10px;">
                        <input type="checkbox" class="myCheckbox col-sm-2" id="<?php echo $id ?>" name="Transfer Term" value="1" data-text="Project Specified" <?php echo ($template_data['transfer_details']['Transfer Term']['NewValueText'] == 'Project Specified' && $template_data['transfer_details']['Transfer Term']['NewValue'] == 'true') ? 'checked' : ''; ?>>
                        <label for="myCheckbox" class="col-sm-10">PROJECT SPECIFIC</label>
                    </div>
                </div>
                </fieldset>
            </div>
            <div class="form-group col-sm-2"></div>
        </div>
        <br>
        <br>
        <hr>


        <div class="row d-flex justify-content-between">
            
        </div>

        <!-- <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
            <button class="btn btn-primary next" onclick="load_conformation();">Next</button>
        </div> -->
    </div>
    


    
<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({ 
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                var formatedValue = ev.date.format(ev.date._f);
                changeValue('transferDate', formatedValue, 1);
            });

            //chechboxes
            $('.myCheckbox').change(function() {
                var id = $(this).attr('id');
                var fieldText = $(this).attr('data-text');
                var isChecked = $(this).prop('checked');
                var type = $(this).attr('name');
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id, 'fieldValue': isChecked, 'fieldType': type, 'fieldText': fieldText, 'type' : 1},
                    url: "<?php echo site_url('Employee/update_persional_action_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                        success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    });
    
    function changeValue(fieldType, ev, date = null){
        if(date){
            var date = new Date(ev);
            var year = date.getFullYear();
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var day = ("0" + date.getDate()).slice(-2);
            var formattedDate = year + "-" + month + "-" + day;

            var fieldText = formattedDate;
        }else{
            var fieldText = $(ev).find('option:selected').text();
        }

        var fieldValue = $(ev).val();
        var pa_action_ID = $('#pa_action_ID').val();

        $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType, 'fieldText': fieldText, 'type' : 1},
                    url: "<?php echo site_url('Employee/update_persional_action_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        fetch_persional_view();                        
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
    }

    

    function changeValue_activecodeType(fieldType, ev){
        var newActiveCodeName = $(ev).find('option:selected').text();
        var newActiveCodeID = $(ev).val();
        var paID = $('#pa_action_ID').val();
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': paID, 'fieldType': fieldType, 'fieldValue': newActiveCodeID,  'fieldText': newActiveCodeName},
                    url: "<?php echo site_url('Employee/update_persional_action_details_actionCodeType'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if(data[0] == "s"){
                            fetch_persional_view();  
                        }
                        
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            );
    }
</script>