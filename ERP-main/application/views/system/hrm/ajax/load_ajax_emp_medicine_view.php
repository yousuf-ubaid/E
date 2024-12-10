<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//echo fetch_account_review(false, true, $approval);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$condition = array(
    '' => 'select',
    1 => 'Low',
    2 => 'Normal',
    3 => 'High'
);

?>

<div style="background-color:#ecf0f4;width:100%;padding:10px;">
    <!-- <form class="form-horizontal" id="add_empMedical_form"> -->
    <?php echo form_open('', 'role="form" id="add_empMedical_form"'); ?>
    <input type="hidden" name="medicalInformationID" id="medicalInformationID" value="<?php echo empty($information['id']) ? '': $information['id'];  ?>">
    <input type="hidden" name="medicalcategoryID" id="medicalcategoryID"  value="<?php echo empty($categories['id']) ? '': $categories['id']; ?>">
    <div class="row" style="width:100%;padding:10px;">
            <div class="col-sm-6" style="">
                <div class="row form-group text-right" style="margin-top:10px;">
                    <div class="col-sm-4">
                        <label for="empCode">Employee Code&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" style="width:100%;" name="empCode" id="empCode" value="<?php echo isset($employee['ECode']) ? $employee['ECode']: ''; ?>">
                    </div>
                </div>
                <div class="row form-group text-right" style="margin-top:10px;">
                    <div class="col-sm-4">
                        <label for="eeGroup">EE Group&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                    </div>
                    <div class="col-sm-8 text-left">
                        <input type="text" name="eeGroup" id="eeGroup" value="<?php echo isset($information['eeGroup'])? $information['eeGroup']: ''; ?>">
                        <span><?php echo isset($employee['employmentType']) ? $employee['employmentType']: ''; ?></span>
                    </div>
                </div>
                <div class="row form-group text-right" style="margin-top:10px;">
                    <div class="col-sm-4">
                        <label for="eeSubGroup">EE Sub-Groupe&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                    </div>
                    <div class="col-sm-8 text-left">
                        <input type="text" name="eeSubGroup" id="eeSubGroup" value="<?php echo isset($information['eeSubGroup']) ? $information['eeSubGroup']: ''; ?>">
                        <span><?php echo isset($employee['DepartmentDes']) ? $employee['DepartmentDes']: ''; ?></span>
                    </div>
                </div>
                <div class="row form-group text-right" style="margin-top:10px;">
                    <div class="col-sm-4">
                        <label for="startDate">Start&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                    </div>
                    <div class="col-sm-8">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startDate"
                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                value="<?php echo empty($information['fromDate']) ? $current_date : $information['fromDate']; ?>" id="startDate" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row form-group text-right" style="margin-top:10px;">
                    <div class="col-sm-4">
                        <label  for="chngDate">Chngd&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                    </div>
                    <div class="col-sm-4">
                    <div class="input-group datepic ">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="chngDate"
                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                value="<?php echo  empty($information['chngDate']) ? $current_date : $information['chngDate']; ?>" id="chngDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-sm-4 text-left">
                        <input type="text" name="" id="" placeholder="HEALTH_MSE" readonly>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6" style="">
                    <div class="row form-group text-right" style="margin-top:10px;">
                        <label class="col-sm-3" for="empName">Name&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                        <input class="col-sm-9" type="text" name="empName" id="empName" value="<?php echo isset($employee['Ename2']) ? $employee['Ename2']:''; ?>">
                    </div>
                    <div class="row form-group text-right" style="margin-top:10px;">
                        <label class="col-sm-3" for="perse_area">Perse.area&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                        <input class="col-sm-2" type="text" name="perse_area" id="perse_area" value="<?php echo isset($information['perse_area']) ? $information['perse_area']: ''; ?>">
                        <div class="col-sm-7 text-left"><span><?php echo isset($company['employcompany']) ? $company['employcompany']:'';?></span></div>
                    </div>
                    <div class="row form-group text-right" style="margin-top:10px;">
                        <label class="col-sm-3" for="costCenter">Cost Center&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                        <input class="col-sm-3" type="text" name="costCenter" id="costCenter" value="<?php echo isset($information['costCenter']) ? $information['costCenter']: ''; ?>">
                        <div class="col-sm-6 text-left"><span><?php echo isset($segment['segmentDescription']) ? $segment['segmentDescription']:''; ?></span></div>
                    </div>
                    <div class="row form-group text-right" style="margin-top:10px;">
                        <label class="col-sm-3" for="toDate">To&nbsp;&nbsp;:&nbsp;&nbsp;</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="toDate"
                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                value="<?php echo  empty($information['toDate']) ? $current_date : $information['toDate']; ?>" id="toDate" class="form-control" required>
                        </div>
                    </div>
                    
            </div>
    </div>
    <br>
    <!-- <br> -->
    <div class="row" style="width:100%;padding:10px;">
        <div class="row col-sm-12 text-center"><strong>MEDICAL FITNESS CERTIFICATE</strong></div>
    </div>
    <!-- <br> -->
    <!-- <br> -->
    <div class="row" style="width:100%;padding:10px;">
        <div class="row col-sm-12 text-left" style="margin-left:0px;">MEDICAL FITNESS CERTIFICATE</div>
    </div>
    <hr>
    <div class="row" style="width:100%;padding:10px;">
        <div class="row form-group col-sm-6">
            <label class="col-sm-4" for="hospital">Hospital Name</label>
            <input class="col-sm-8" type="text" name="hospital" id="hospital" value="<?php echo isset($information['hospital']) ? $information['hospital']: ''; ?>">
        </div>
        
        <div class="row form-group col-sm-6">
            <div class="col-sm-12">
                <input type="checkbox" name="foodHandlerYN" id="foodHandlerYN" value="1" <?php echo isset($information['foodHandlerYN']) ? (($information['foodHandlerYN'] == 1) ? 'checked': '') : ''; ?> >
                <label for="foodHandlerYN">Food Handler</label>
            </div>
        </div>
    </div>
    <hr>
    <div class="row" style="width:100%;padding:10px;">
        <!-- <br> -->
        <div class="row col-sm-6">
            <hr>
            <div class="col-sm-6 text-left"><label for="">MEDICAL EXAMINATION</label></div>
            <div class="col-sm-6 text-center"><label for="">RESULT</label></div>
            <br><br>
            <div class="col-sm-5"><label for="audiogram">Audiogram</label></div>
            <div class="form-group col-sm-7" style="padding-left:0px;">
                    <?php echo form_dropdown('audiogram', $condition, empty($information['audiogram']) ? '': $information['audiogram'], 'class="form-control select2" id="audiogram"'); ?>
            </div>
            <div class="col-sm-5"><label style="margin-top:15px;" for="visualAcuity_colorVision">Visual Acuity & Color Vision</label></div>
            <div class="form-group col-sm-7" style="margin-top:15px;padding-left:0px;">
                    <?php echo form_dropdown('visualAcuity_colorVision', $condition, empty($information['visualAcuity_colorVision']) ? '': $information['visualAcuity_colorVision'], 'class="form-control select2" id="visualAcuity_colorVision"'); ?>
            </div>
            <br>
            <hr class="col-sm-12" style="margin-top:15px;">
            <label class="col-sm-6 text-left" for="" >PHYSICAL EXAMINATION</label>
            <label class="col-sm-6 text-center" for="">RESULT</label>
            <br>
            <br>
            <label class="col-sm-5" for="height" style="margin-top:15px;">Height(cm)</label>
            <input class="col-sm-7" type="text" name="height" id="height" style="margin-top:15px;" value="<?php echo isset($information['height']) ? $information['height']:''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="weight">Weight(Kg)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="weight" id="weight" value="<?php echo isset($information['weight']) ? $information['weight']:''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="bmi">BMI</label>
            <input class="col-sm-7" style="margin-top:15px;background-color:#ecf0f4;" type="text" name="bmi" id="bmi" value="<?php echo isset($information['bmi']) ? $information['bmi']:''; ?>" readonly>
            <label class="col-sm-5" style="margin-top:15px;" for="waistLine">Waist line(cm) (at the belly button)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="waistLine" id="waistLine" value="<?php echo isset($information['waistLine']) ? $information['waistLine']:''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="smoker">Smoker</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="smoker" id="smoker" value="<?php echo isset($information['smoker']) ? $information['smoker']: ''; ?>">

            <label class="col-sm-5" style="margin-top:15px;" for="bp_pr">Blood Pressure & Pulse Rate</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="bp_pr" id="bp_pr" value="<?php echo isset($information['bp_pr']) ? $information['bp_pr']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="respiratoryRate_temperature">Respiratory Rate & Temperature</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="respiratoryRate_temperature" id="respiratoryRate_temperature" value="<?php echo isset($information['respiratoryRate_Temperature']) ? $information['respiratoryRate_Temperature']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="neurological">Neurological</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="neurological" id="neurological" value="<?php echo isset($information['neurological']) ? $information['neurological']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="lymphNode">Lymph Node</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="lymphNode" id="lymphNode" value="<?php echo isset($information['lymphNode']) ? $information['lymphNode']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="chest_lungs">Chest/ Lungs</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="chest_lungs" id="chest_lungs" value="<?php echo isset($information['chest_lungs']) ? $information['chest_lungs']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="abdomen">Abdomen</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="abdomen" id="abdomen" value="<?php echo isset($information['abdomen']) ? $information['abdomen']:''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="extremities">Extremities</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="extremities" id="extremities" value="<?php echo isset($information['extremities']) ? $information['extremities']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="allergies">Allergies</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="allergies" id="allergies" value="<?php echo isset($information['allergies']) ? $information['allergies']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="currentMedication">Current Medications</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="currentMedication" id="currentMedication" value="<?php echo isset($information['currentMedication']) ? $information['currentMedication']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="medicalCondition">Medical Conditions</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="medicalCondition" id="medicalCondition" value="<?php echo isset($information['medicalCondition']) ? $information['medicalCondition']: ''; ?>">
        </div>
        <div class="row col-sm-6">
            <label class="col-sm-6 text-left" for="">LABORATORY EXAMINATION</label>
            <label class="col-sm-6 text-center" for="">RESULT</label>
            <br>
            <br>
            <label class="col-sm-5" for="ecg_ekg">ECG/ EKG</label>
            <div class="col-sm-7 text-left" style="padding:0px;">
                    <?php echo form_dropdown('ecg_ekg', $condition, empty($information['ecg_akg'])? '': $information['ecg_akg'], 'class="form-control select2" id="ecg_ekg"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="bloodGroup">Blood Group (Blood Type)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="bloodGroup" id="bloodGroup" value="<?php echo isset($information['bloodGroup']) ? $information['bloodGroup']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="cbc">CBC (Complete Blood Count)</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('cbc', $condition, empty($information['cbc'])? '': $information['cbc'], 'class="form-control select2" id="cbc"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="fbs">FBS (mg/dl)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="fbs" id="fbs" value="<?php echo isset($information['fbs']) ? $information['fbs']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="hba1c">HBA1c (%)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="hba1c" id="hba1c" value="<?php echo isset($information['hba1c']) ? $information['hba1c']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="totCholestterol">Total Cholestterol (mg/dl)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="totCholestterol" id="totCholestterol" value="<?php echo isset($information['totCholestterol']) ? $information['totCholestterol']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="hdl">HDL (mg/dl)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="hdl" id="hdl" value="<?php echo isset($information['hdl']) ? $information['hdl']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="ldl">LDL (mg/dl)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="ldl" id="ldl" value="<?php echo isset($information['ldl']) ? $information['ldl'] : ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="triglycerides">Triglycerides (mg/dl)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="triglycerides" id="triglycerides" value="<?php echo isset($information['triglycerides']) ? $information['triglycerides']: ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="serumCreatinine">Serum Creatinine</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="serumCreatinine" id="serumCreatinine" value="<?php echo isset($information['serumCreatinine']) ? $information['serumCreatinine'] : ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="ast">AST(SGOT)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="ast" id="ast" value="<?php echo isset($information['ast']) ? $information['ast'] : ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="alt">ALT(SGPT)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="alt" id="alt" value="<?php echo isset($information['alt']) ? $information['alt'] : ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="hepatitis_B">Hepatitis B s-Ag</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('hepatitis_B', $condition, empty($information['hepatitis_B'])? '': $information['hepatitis_B'], 'class="form-control select2" id="hepatitis_B"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="hepatitis_C">Hepatitis C Virus (ABS)</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('hepatitis_C', $condition, empty($information['hepatitis_C'])? '': $information['hepatitis_C'], 'class="form-control select2" id="hepatitis_C"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="hiv">HIV</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('hiv', $condition, empty($information['hiv'])? '': $information['hiv'], 'class="form-control select2" id="hiv"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="urineAnalysis">Urine Analysis</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('urineAnalysis', $condition, empty($information['urineAnalysis'])? '': $information['urineAnalysis'], 'class="form-control select2" id="urineAnalysis"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="stoolAnalysis">Stool Analysis</label>
            <div class="col-sm-7 text-left" style="margin-top:15px;padding:0px;">
                    <?php echo form_dropdown('stoolAnalysis', $condition, empty($information['stoolAnalysis'])? '': $information['stoolAnalysis'], 'class="form-control select2" id="stoolAnalysis"'); ?>
            </div>
            <label class="col-sm-5" style="margin-top:15px;" for="throatSwab">Throat Swab (for food handler)</label>
            <input class="col-sm-7" style="margin-top:15px;" type="text" name="throatSwab" id="throatSwab" value="<?php echo isset($information['throatSwab']) ? $information['throatSwab'] : ''; ?>">
            <label class="col-sm-5" style="margin-top:15px;" for="weightCategory">Weight Category</label>
            <input class="col-sm-7" style="margin-top:15px;background-color:#ecf0f4;" type="text" name="weightCategory" id="weightCategory" value="Overweight" readonly>
            <label class="col-sm-5" style="margin-top:15px;" for="diabetes">Diabetes</label>
            <input class="col-sm-7" style="margin-top:15px;background-color:#ecf0f4;" type="text" name="diabetes" id="diabetes" value="Diabetes" readonly>
        </div>
    </div>
    <br>
    <hr>
    <div class="row" style="width:100%;padding:10px;">
        <div class="row col-sm-12 text-center" style="margin-top:0px;"><strong>CATEGORY</strong></div>
    </div>
    <div class="row" style="width:100%;padding:10px;">
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category1" id="category1" value="1" <?php echo isset($categories['category1']) ? (($categories['category1'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category1">1. Has all the test result as Normal & FIT to work</label>
        </div>
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category2" id="category2" value="1" <?php echo isset($categories['category2']) ? (($categories['category2'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category2">2. Has some problem but only need diet control & excercise,medicine is not needed for treatment. (i.e. Overweight, High Cholestrol or Blood Sugar)</label>
        </div>
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category3" id="category3" value="1" <?php echo isset($categories['category3']) ? (($categories['category3'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category3">3. Has problems that need regular treatment with medicine. Ability to work may have restrictions. (i.e. Diabetes, High BP, High Cholestrol)</label>
        </div>
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category4" id="category4" value="1" <?php echo isset($categories['category4']) ? (($categories['category4'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category4">4. Has the test results as ABNORMAL & UNFIT for work. (Contagious and Special Diseases)</label>
        </div>
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category5" id="category5" value="1" <?php echo isset($categories['category5']) ? (($categories['category5'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category5">5. Overdue medicals / expired medicals, without medical record, Pending</label>
        </div>
        <div class="col-sm-12">
            <input class="" type="checkbox" name="category6" id="category6" value="1" <?php echo isset($categories['category6']) ? (($categories['category6'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="category6">6. High risk</label>
        </div>
    </div>
    <div class="row" style="width:100%;padding:10px;margin-left:5px;">
        <input class="col-sm-12" style="margin-top:10px;" type="text" name="des" id="des">
        <input class="col-sm-12" style="margin-top:10px;" type="text" name="des" id="des">
        <input class="col-sm-12" style="margin-top:10px;" type="text" name="des" id="des">
    </div>
    <br>
    <hr>
    <div class="row" style="width:100%;padding:10px;">
        <div class="row col-sm-12 text-center" style="margin-top:0px;"><strong>RECOMMENDATION</strong></div>
    </div>
    <br>
    <div class="row" style="width:100%;padding:10px;">
        <div class="col-sm-3">
            <input class="" type="checkbox" name="dietControlYN" id="dietControlYN" value="1" <?php echo isset($categories['dietControlYN']) ? (($categories['dietControlYN'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="dietControlYN">Diet Control</label>
        </div>
        <div class="col-sm-3">
            <input class="" type="checkbox" name="excerciseYN" id="excerciseYN" value="1" <?php echo isset($categories['exerciseYN']) ? (($categories['exerciseYN'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="excerciseYN">Excercise</label>
        </div>
        <div class="col-sm-3">
            <input class="" type="checkbox" name="increaseFluidIntakeYN" id="increaseFluidIntakeYN" value="1" <?php echo isset($categories['increaseFluidIntakeYN']) ? (($categories['increaseFluidIntakeYN'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="increaseFluidIntakeYN">Increase Fluid Intake</label>
        </div>
    </div>
    <br>
    <div class="row" style="width:100%;padding:10px;">
        <div class="col-sm-3">
            <input class="" type="checkbox" name="furtherTestingYN" id="furtherTestingYN" value="1" <?php echo isset($categories['furtherTestingYN']) ? (($categories['furtherTestingYN'] == 1) ? 'checked': '') : ''; ?> >&nbsp;&nbsp;
            <label class=" text-left" for="furtherTestingYN">Further testing/ Follow up</label>
        </div>
        <div class="col-sm-9">
            <input class="col-sm-12" type="text" name="furtherTestingText" id="furtherTestingText" value="<?php echo isset($categories['furtherTestingText']) ? $categories['furtherTestingText']: ''; ?>">
        </div>
    </div>
    <br>
    <div class="row" style="width:100%;padding:10px;">
        <div class="row" style="width:100%;padding:10px;">
            <label class="col-sm-3 text-left" for="nextCheckupDate">Next Checkup Date</label>
            <div class="input-group datepic col-sm-2">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="nextCheckupDate"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                    value="<?php echo  empty($categories['nextCheckupDate']) ? $current_date : $categories['nextCheckupDate']; ?>" id="nextCheckupDate" class="form-control" required>
            </div>
            <span class="col-sm-7">&nbsp;</span>
        </div>
        <div class="row" style="width:100%;padding:10px;">
            <label class="col-sm-3 text-left" for="healthyLifeStyle">Healthy Life Style</label>
            <input class="col-sm-2" type="text" name="healthyLifeStyle" id="healthyLifeStyle" value="<?php echo isset($categories['healthyLifeStyle']) ? $categories['healthyLifeStyle'] : ''; ?>">
            <span class="col-sm-7">&nbsp;</span>
        </div>
        <div class="row" style="width:100%;padding:10px;">
            <label class="col-sm-3 text-left" for="knowYourBody">Know Your Body</label>
            <input class="col-sm-2" type="text" name="knowYourBody" id="knowYourBody" value="<?php echo isset($categories['knowYourBody']) ? $categories['knowYourBody'] : ''; ?>">
            <span class="col-sm-7">&nbsp;</span>
        </div>
    </div>
    <br>
    <hr>
    <div class="row" style="width:100%;padding:10px;">
        <label class="col-sm-4 text-left" for="nameAndSignatureOfAttendingPhysician">Name and Signature of Attending Physician:</label>
        <input class="col-sm-4" type="text" name="nameAndSignatureOfAttendingPhysician" id="nameAndSignatureOfAttendingPhysician" value="<?php echo isset($information['nameAndSignatureOfAttendingPhysician']) ? $information['nameAndSignatureOfAttendingPhysician'] : ''; ?>">
        <label class="col-sm-2 text-right" for="documentDate">Date</label>
        <div class="input-group datepic">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="documentDate"
                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                value="<?php echo empty($information['documentDate']) ? $current_date : $information['documentDate']; ?>" id="documentDate" class="form-control" required>
        </div>
    </div>
    </form>
</div>
<br>
<div class="row" style="">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <?php if((isset($setOnlyReadable) ? true : false ) == true){?>
                <button class="btn btn-default btn-sm" id="back_to_medical_TB" onclick="fetch_medical_details()" type="button">Go Back </button>&nbsp;&nbsp;
                <button class="btn btn-primary btn-sm pull-right hide" id="save_medical" onclick="save_medical()" type="button">Save<!--Save Changes--></button>
            <?php }else{ ?>
                <button class="btn btn-default btn-sm" id="back_to_medical_TB" onclick="fetch_medical_details()" type="button">Cancel</button>&nbsp;&nbsp;
                <button class="btn btn-primary btn-sm pull-right" id="save_medical" onclick="save_medical()" type="button">Save<!--Save Changes--></button>
            <?php }?>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        <?php if((isset($setOnlyReadable) ? true : false ) == true){?>
            $('input').prop('disabled', true);
            $('.select2').prop('disabled', true);
        <?php } ?>

        $('.select2').select2();
        $('#dischargeUpdate').hide();

        $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
        }).on('dp.change', function (ev) {
              //  $('#').bootstrapValidator('revalidateField', 'actionDate');
        });

    });


    function save_medical() 
    {
            var postData = $('#add_empMedical_form').serializeArray();
            postData.push({name:'empID', value: empID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_medical'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () { startLoad(); },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        setTimeout(function(){ fetch_medical_details(); }, 400);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
            })
        
    }

</script>