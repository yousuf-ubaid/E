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
$designations_arr = all_designation_drop();
$manager_arr = all_managers_drom();
$grades_arr = employee_grade_drop();
$locations_arr = all_location_drom();
$leaveGroup_arr = all_leaveGroup_drop();

$group_arr = all_group_drop_PAA();
$company_arr = all_company_drom();
$segment_arr = all_segment_arr_PAA();
$sub_segment_arr = all_sub_segment_arr_PAA();
$division_arr = all_division_drop_PAA();

$status_arr = array(
    '' => 'Select Status',
    '0' => 'Inactive Employee',
    '1' => 'Active Employee',
);

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
                <div class="col-sm-4"><h2>EMPLOYEE CHANGE OF STATUS </h2></div>
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
                                        <label class="title ">COMPANY</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Company']['currentText']) ? $template_data['transfer_details']['Company']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">DIVISION</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Division']['currentText']) ? $template_data['transfer_details']['Division']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">SEGMENT</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Segment']['currentText']) ? $template_data['transfer_details']['Segment']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">SUB SEGMENT</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Sub Segment']['currentText']) ? $template_data['transfer_details']['Sub Segment']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title ">NAME</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Name']['currentText']) ? $template_data['transfer_details']['Name']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="shadow-box">
                                        <label class="title">DESIGNATION</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Designation']['currentText']) ? $template_data['transfer_details']['Designation']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">EMPLOYEE ID</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EmpCODE']['currentText']) ? $template_data['transfer_details']['EmpCODE']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">GRADE</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Grade']['currentText']) ? $template_data['transfer_details']['Grade']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title">DATE OF JOINING</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['EDOJ']['currentText']) ? $template_data['transfer_details']['EDOJ']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-4">
                                    <div class="shadow-box">
                                        <label class="title ">LAST REVIEW DATE</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                        <span><?php echo isset($template_data['transfer_details']['Last Review Date']['currentText']) ? $template_data['transfer_details']['Last Review Date']['currentText'] : ''; ?></span>
                                    </div>
                                </div>
                                <?php if($actionType != 3){ ?>
                                    <div class="form-group col-sm-4">
                                        <div class="shadow-box">
                                            <label class="title ">LAST INCREMENT AMOUNT&nbsp;&nbsp;&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency'] ?? ''; ?>)</label>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span><?php echo number_format(!empty($template_data['transfer_details']['Last Increment Amount']['currentText']) ? floatval($template_data['transfer_details']['Last Increment Amount']['currentText']) : 0, $company_reporting_DecimalPlaces) ; ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
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


            <div class="row">
                <?php if($actionType != 3){ ?>
                    <h4 class="text-center">CURRENT POSITION AND PROPOSED (ALONG WITH SALARY DETAILS)</h4>
                <?php }else{ ?>
                    <h4 class="text-center">Bonus Details</h4>
                <?php } ?>
            </div><br>
            <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="panel-body" id="transfer" >
                <div class="col-md-12 animated pulse">
                <fieldset style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; background-color: white;">
                <legend></legend>
                    <table class="table table-bordered table-striped table-condensed mx-auto" style="width:100%;">
                        <thead>
                            <tr>
                                <!-- <td class="text-center" style="min-width: 10%">#</td> -->
                                <th class="text-center" style="min-width: 10%">DESCRIPTION</th>
                                <?php if($actionType != 3){ ?>
                                    <th class="text-center" style="min-width: 10%">CURRENT</th>
                                <?php } ?>
                                <?php if($actionType != 3){ ?>
                                    <th class="text-center" style="min-width: 10%">NEW</th>
                                <?php }else{ ?>
                                    <th class="text-center" style="min-width: 10%">NEW&nbsp;&nbsp;:&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency'] ?? ''; ?>)</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody id="table_body">
                                <?php
                                $x=1;
                                foreach ($details as $val) {

                                    ?>
                                    <tr>
                                        <?php if($val['fieldType'] != 'JD ATTACHED' && $val['fieldType'] != 'Last Increment Amount' && $val['fieldType'] != 'Last Review Date' && $val['fieldType'] != 'DEPARTMENT' && $val['fieldType'] != 'Sub Segment' && $val['fieldType'] != 'Segment' && $val['fieldType'] != 'Location' && $val['fieldType'] != 'Name' && $val['fieldType'] != 'EmpCODE' && $val['fieldType'] != 'EDOJ' && $val['fieldType'] != 'Division' && $val['fieldType'] != 'Reporting Manager' && $val['fieldType'] != 'Company' && $val['fieldType'] != 'Justification' && $val['fieldType'] != 'New job description' && $val['fieldType'] != 'Reporting Structure' && $val['fieldType'] != 'KPI' && $val['fieldType'] != 'Performance Appraisal form'){ ?>
                                         <!--1st column -->
                                        <?php if($actionType != 3){ ?>
                                            <?php if(!empty($val['salaryCategoryID'])){ ?>
                                                <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                    <td class="text-left"><?php echo $val['fieldType']; ?>&nbsp;&nbsp;:&nbsp;&nbsp;(<?php echo $empCurrency['transactionCurrency'] ?? ''; ?>)</td>
                                                <?php } ?>
                                            <?php }else{ ?>
                                                <td class="text-left"><?php echo $val['fieldType']; ?></td>
                                            <?php } ?>
                                        <?php }else{
                                            if(!empty($val['monthlyDeclarationID'])){ ?>
                                                <td class="text-left"><?php echo $val['fieldType']; ?></td>
                                            <?php } ?>
                                        <?php } ?>


                                        <?php if($actionType !=3 ){
                                            if(!empty($val['salaryCategoryID'])){ ?> <!--2nd column -->
                                                <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                    <td><input type="text" class="text-right" style="width:100%;" placeholder="Enter Value Here" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['currentText']) ? 0 : floatval($val['currentText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)"></td>
                                                <?php } ?>
                                            <?php }else{ ?>
                                                <td class="text-left"><?php echo !empty($val['currentText']) ? $val['currentText'] : '-'; ?></td> <!--2nd column -->
                                            <?php }
                                        }?>

                                        <!-- 3rd column -->
                                        <?php if($actionType != 3){
                                            if(!empty($val['salaryCategoryID'])){ ?>
                                                <?php if(empty($val['monthlyDeclarationID'])) { ?>
                                                    <td><input type="text" class="text-right" style="width:100%;" placeholder="Enter Value Here" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['NewValueText']) ? floatval($val['currentText']) : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)"></td>
                                                <?php } ?>
                                            <?php }
                                            else{
                                                if($val['fieldType'] == 'Grade'){ ?>
                                                    <td><?php echo form_dropdown('grade', $grades_arr, empty($template_data['transfer_details']['Grade']['NewValue']) ? $template_data['transfer_details']['Grade']['currentValue'] : $template_data['transfer_details']['Grade']['NewValue'], 'class="form-control select2" id="grade" onchange="changeValue(\'Grade\', this)" required'); ?></td>
                                                <?php }else if($val['fieldType'] == 'Status'){ ?>
                                                    <td><?php echo form_dropdown('status', $status_arr, empty($template_data['transfer_details']['Status']['NewValue']) ? $template_data['transfer_details']['Status']['currentValue'] : $template_data['transfer_details']['Status']['NewValue'] , 'class="form-control select2" id="status" onchange="changeValue(\'Status\', this)" required'); ?></td>
                                                <?php }
                                                else if($val['fieldType'] == 'Designation'){?>
                                                    <td><?php echo form_dropdown('designationID', $designations_arr, empty($template_data['transfer_details']['Designation']['NewValue']) ? $template_data['transfer_details']['Designation']['currentValue'] : $template_data['transfer_details']['Designation']['NewValue'], 'class="form-control select2" id="designationID" onchange="changeValue(\'Designation\', this)" required'); ?></td>
                                                <?php }
                                                else if($val['fieldType'] == 'Leave Group'){ ?>
                                                    <td><?php echo form_dropdown('leaveGroup', $leaveGroup_arr, empty($template_data['transfer_details']['Leave GroupP']['NewValue']) ? $template_data['transfer_details']['Leave Group']['currentValue'] : $template_data['transfer_details']['Leave Group']['NewValue'] , 'class="form-control select2" id="leaveGroup" onchange="changeValue(\'Leave Group\', this)" required'); ?></td>
                                                <?php }
                                            }
                                        }else{?>
                                            <?php if(!empty($val['monthlyDeclarationID'])){ ?>
                                                <td><input type="text" class="text-right" style="width:100%;" placeholder="<?php echo  number_format(0, $company_reporting_DecimalPlaces) ;?>" name="field" id="<?php echo $val['paID'];?>" value="<?php echo  number_format(empty($val['NewValueText']) ? 0 : floatval($val['NewValueText']), $company_reporting_DecimalPlaces) ;?>" data-current-value="<?php echo $val['fieldType'];?>" onchange="onkeyupchangeValue(this)"></td>
                                            <?php } ?>
                                        <?php } ?>
                                    </tr>
                                    <?php $x++;
                                    }
                                } ?>
                        </tbody>
                    </table>
                </fieldset>
                </div>
                </div>
                </div>
            </div>
            <div class="col-md-2"></div>

            <br>
            <br>

            <?php if($actionType != 3){ ?>
            <div class="row"><h4 class="text-center">FOR DESIGNATION CHANGE (PLEASE PROVIDE THE BELOW)</h4></div>
            <br>
            <div class="row" style="margin-left: 10px;margin-right: 10px;">
                <div class="col-sm-2"></div>
                    <div class="col-sm-2 text-center">
                    <?php
                        $a = '';
                        if(isset($template_data['transfer_details']['New job description']['NewValue'])){
                            $a = $template_data['transfer_details']['New job description']['NewValue'] == 1 ? 'checked' : '';
                        } ?>
                        <input type="checkbox" class="myCheckbox" id="A_<?php echo $id ?>" name="AmyCheckbox" value="" data-text="New job description" <?php echo $a; ?>><br>
                        <label for="myCheckbox">NEW JOB DESCRIPTION</label>
                    </div>

                    <div class="col-sm-2 text-center">
                    <?php
                        $b = '';
                        if(isset($template_data['transfer_details']['Reporting Structure']['NewValue'])){
                            $b = $template_data['transfer_details']['Reporting Structure']['NewValue'] == 1 ? 'checked' : '';
                        } ?>
                        <input type="checkbox" class="myCheckbox" id="B_<?php echo $id ?>" name="BmyCheckbox" value="" data-text="Reporting Structure" <?php echo $b; ?>><br>
                        <label for="myCheckbox">REPORTING STRUCTURE</label>
                    </div>

                    <div class="col-sm-2 text-center">
                    <?php
                        $c = '';
                        if(isset($template_data['transfer_details']['KPI']['NewValue'])){
                            $c = $template_data['transfer_details']['KPI']['NewValue'] == 1 ? 'checked' : '';
                        } ?>
                        <input type="checkbox" class="myCheckbox" id="C_<?php echo $id ?>" name="CmyCheckbox" value="" data-text="KPI" <?php echo $c; ?>><br>
                        <label for="myCheckbox">KPI</label>
                    </div>

                    <div class="col-sm-2 text-center">
                    <?php
                        $d = '';
                        if(isset($template_data['transfer_details']['Performance Appraisal form']['NewValue'])){
                            $d = $template_data['transfer_details']['Performance Appraisal form']['NewValue'] == 1 ? 'checked' : '';
                        } ?>
                        <input type="checkbox" class="myCheckbox" id="D_<?php echo $id ?>" name="DmyCheckbox" value="" data-text="Performance Appraisal form" <?php echo $d; ?>><br>
                        <label for="myCheckbox">PERFORMANCE APPRAISAL FORM</label>
                    </div>
                    <div class="col-sm-2"></div>
            </div>

            <br>
            <br>
            <br>
            <?php } ?>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                <fieldset style="border: 0px #ffffff; padding: 10px; margin-bottom: 10px;">
                <legend></legend>
                    <div class="form-group ">
                        <label for="justification">JUSTIFICATION FOR CHANGE IN STATUS:</label>
                        <?php if($actionType != 3){ ?>
                            <textarea class="form-control justification" name="justification" id="<?php echo $id ?>" data-current-value="Justification" onchange="onkeyupchangeValue(this)" rows="3" style="border: 1px solid #ccc;" ><?php echo empty($template_data['transfer_details']['Justification']['NewValueText']) ? '': trim($template_data['transfer_details']['Justification']['NewValueText']); ?></textarea>
                        <?php }
                        else{ ?>
                            <textarea class="form-control justification" name="Justification_bonus" id="<?php echo $id ?>" data-current-value="Justification_bonus" onchange="onkeyupchangeValue(this)" rows="3" style="border: 1px solid #ccc;" ><?php echo empty($template_data['transfer_details']['Justification_bonus']['NewValueText']) ? '': trim($template_data['transfer_details']['Justification_bonus']['NewValueText']); ?></textarea>
                        <?php } ?>
                    </div>
                    </fieldset>
                </div>
                <div class="col-sm-2"></div>
            </div>
        <br>
        <br>
        <hr>

        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
            <!-- <button class="btn btn-primary next" onclick="move_confirm_tab();"><?php // echo $this->lang->line('common_save_and_next'); ?></button> -->
        </div>
    <br><br><br><br>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
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
            var prefix_id = id.split('_');
            var type = $(this).attr('data-text');
            // var split_text = type.split('_');
            // var text = split_text[0]+' '+split_text[1]+' '+split_text[2] ;
            var isChecked = $(this).prop('checked') ? 1 : 0;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id': prefix_id[1], 'fieldValue': isChecked, 'fieldType': type, 'fieldText': type, 'type' : 2},
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

    //common fields
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
            data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType, 'fieldText': fieldText, 'type' : 2},
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
    }

    //textarea
    function onkeyupchangeValue(inputElement, checkAttr = null, pid = null){
        var fieldValue = inputElement.value;

        if(checkAttr){
            var fieldType = checkAttr;
            var pa_action_ID = pid;
        }else{
            var pa_action_ID = inputElement.getAttribute('id');
            var fieldType = inputElement.getAttribute('data-current-value');
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': pa_action_ID, 'fieldValue': fieldValue, 'fieldType': fieldType, 'type' : 2},
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
    }
</script>