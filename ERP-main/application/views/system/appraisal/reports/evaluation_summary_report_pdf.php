<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_reports_performance_evaluation_summary_title');

//department_id: 31
//goal_id: 64
//employee_id: 1223

?>
<style>
    .td-text-alignment{
        text-align: left;
        padding-left: 100px;
    }
</style>

<div class="container">
    <div id="report_view" style="margin-top: 10px;">
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                    <tr>
                        <td style="width:50%;">
                            <table>
                                <tr>
                                    <td>
                                        <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: center; padding-left: 10px;">
                                        <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                                        <p style="font-size: 11px;"><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="report_title_for_print" style="margin-bottom: 15px;"><h4
                    style="text-align: center"><?php echo $this->lang->line('appraisal_employee_performance_evaluation_summary'); ?></h4>
        </div>
        <table class="table table-striped">

            <tbody>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_epf_number'); ?>
                        :</label></td>
                <td class="td-text-alignment">
                    <span id="epf_number"><?php echo $employee_details[0]['ssoNo'] != null ? $employee_details[0]['ssoNo'] : ''; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;margin-right: 200px;">
                    <label for="">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_emp_name'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="name_of_the_employee"><?php echo $employee_details[0]['Ename1']; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_designation'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="designation"><?php echo $employee_details[0]['DesDescription'] != null ? $employee_details[0]['DesDescription'] : ''; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_department'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="department"><?php echo $department_name; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_period_of_review'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                <span id="period_of_review">
                    <?php
                    //var_dump($appraisal_start_date);
                    $start_date = date_create($appraisal_start_date);
                    $end_date = date_create($appraisal_end_date);
                    echo date_format($start_date, "Y-m-d") . ' to ' . date_format($end_date, "Y-m-d");
                    ?>
                </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_name_of_hod'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                <span id="name_of_department_head">
                    <?php echo $hod_details[0]['Ename1'] != null ? $hod_details[0]['Ename1'] : ''; ?>
                </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_today'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="date_today"><?php echo date('Y-m-d') ?></span>
                </td>
            </tr>


            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_softskills_evaluation_result'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="soft_skills_evaluation_result"><?php echo $softskills_based_percentage_of_employee; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_soft_skills_evaluation_final_score'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                <span id="soft_skills_evaluation_final_score">
                    <?php

                    $soft_skills_evaluation_result = $softskills_based_percentage_of_employee;
                    if ($soft_skills_evaluation_result != 0) {
                        $soft_skills_evaluation_final_score = ($soft_skills_evaluation_result * 40) / 100;
                    } else {
                        $soft_skills_evaluation_final_score = 0;
                    }
                    echo $soft_skills_evaluation_final_score;

                    ?>
                </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_kpis_evaluation_result'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span id="kpi_evaluation_result"><?php echo $objective_based_percentage_of_employee; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_kpis_evaluation_final_score'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                <span id="kpi_evaluation_final_score">
                     <?php

                     $kpi_evaluation_result = $objective_based_percentage_of_employee;
                     if ($kpi_evaluation_result != 0) {
                         $kpi_evaluation_final_score = ($kpi_evaluation_result * 60) / 100;
                     } else {
                         $kpi_evaluation_final_score = 0;
                     }
                     echo $kpi_evaluation_final_score;

                     ?>
                </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="">
                        <?php echo $this->lang->line('appraisal_final_score'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                <span id="final_score">
                    <?php echo $soft_skills_evaluation_final_score + $kpi_evaluation_final_score; ?>
                </span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="">
                        <?php echo $this->lang->line('manager_comment'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $manager_comment; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="">
                        <?php echo $this->lang->line('appraisal_suggested_rewards'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $suggested_reward; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="">
                        <?php echo $this->lang->line('appraisal_identified_training_needs'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $identified_training_needs; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_special_remarks_from_employee'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $special_remarks_from_emp; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $special_remarks_from_hod; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_signature_of_hod'); ?>
                        :</label>
                </td>
                <td>
                    <span id=""></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_signature_of_hr'); ?>
                        :</label>
                </td>
                <td>
                    <span id=""></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_signature_of_employee'); ?>
                        :</label>
                </td>
                <td>
                    <span id=""></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_special_remarks_from_employee_skill'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $special_remarks_from_emp_skill; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('manager_comment_skill'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $manager_comment_skill; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_suggested_rewards_skill'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $suggested_reward_skill; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_identified_training_needs_skill'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $identified_training_needs_skill; ?></span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php echo $this->lang->line('appraisal_special_remarks_from_hod_skill'); ?>
                        :</label>
                </td>
                <td class="td-text-alignment">
                    <span><?php echo $special_remarks_from_hod_skill; ?></span>
                </td>
            </tr> 
            <!-- <tr>
                <td style="padding: 10px;width: 50%;">
                    <label for="to_date">
                        <?php /*echo $this->lang->line('appraisal_signature_of_coo');*/ ?>
                        :</label>
                </td>
                <td>
                    <span id=""></span>
                </td>
            </tr> -->
            </tbody>
        </table>
    </div>
</div>