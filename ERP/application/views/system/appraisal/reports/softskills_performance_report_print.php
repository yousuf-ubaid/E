<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_activity_softskills_performance_title');

//var_dump()

?>

<?php  
   // echo '<pre>';print_r($manager['markingType']);exit;
 ?>
<div id="report_view">
    <div class="table-responsive">
        <table style="width: 100%; border: none !important;">
            <tbody style="border: none !important;">
                <tr style="border: none !important;">
                    <td style="width:40% !important; border: none !important;">
                        <table style="border: none !important;">
                            <tr style="border: none !important;">
                                <td>
                                    <img alt="Logo" style="width: 50% !important; height: 130px;" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="width:60% !important; border: none !important;">
                        <table style="border: none !important;">
                            <tr style="border: none !important;">
                                <td style="text-align: center; border: none !important;">
                                    <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                                    <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <h4 id="report_header" style="text-align: center;">Performance Appraisal</h4>

    <hr>
    <?php if($manager['markingType'] == 2){ ?>  <!-- .................................................mpo base -->

        <div class="table-responsive">
            <table style="width: 100%;border:none !important;">
                <tbody style="border:none !important;">
                    <tr style="border:none !important;">
                        <td style="width:50% !important;border:none !important;">
                            <table style="">
                                <tr style="">
                                    <td style="text-align: left;">Employee No/ Name</td>
                                    <td style="text-align: left;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Emp-Position</td>
                                    <td style="text-align: left;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Company Name</td>
                                    <td style="text-align: left;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Reporting To</td>
                                    <td style="text-align: left;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Manager Position</td>
                                    <td style="text-align: left;"></td>
                                </tr>
                            </table>
                        </td>

                        <td style="width:50% !important;border:none !important;">
                            <table style="">
                                <tr style="">
                                    <td style="text-align: left;">Location </td>
                                    <td style="text-align: center;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">DOJ</td>
                                    <td style="text-align: center;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Assessment Period</td>
                                    <td style="text-align: center;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">JD Number</td>
                                    <td style="text-align: center;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Date</td>
                                    <td style="text-align: center;"></td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: left;">Assessment Type</td>
                                    <td style="text-align: center;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr>
        <br>&nbsp;</br>

        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        A. Job Purpose/ Main Functions/ Targets
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>

        <br>&nbsp;</br>

        <h5 class="col-sm-12 mpo_based" style="width:100%;margin-left:15px;"><u><b>B. LIST OF MEASURED DUTIES TO BE PERFORMED (MAJOR TASK)</b></u></h5>
    
        <!-- table div-->
        <div class="col-md-12">
            <div class="col-md-12">
                <div style="margin-top: 15px;">

                    <?php 
                            $grand_total_measure = 0;
                            $grand_total_employee = 0;
                            $grand_total_manager = 0;

                        foreach($manager['performance_areas'] as $hd_item) {
                            //echo '<pre>';print_r($hd_item);exit;
                            $performance_area = $hd_item['description'];
                            $order = $hd_item['order'];
                           
                            // Check if hd_item.sub is defined and is an array
                            if (!empty($hd_item['sub'])) {
                                echo ' <div class="table-responsive">';
                                    echo' <table id="soft_skill_MPO_table_mngr" class="'.table_class() .'">';
                                            echo' <thead>';
                                                echo' <tr>';
                                                    echo' <th style="min-width: 5%"><b>'. $order .'</b></th>';
                                                    echo' <th class="text-left" style="min-width: 50%"><u><b>'. $performance_area .'</b></u></th>';
                                                    echo' <th style="min-width: 15%"><b>Measured Points</b></th>';
                                                    echo' <th style="min-width: 15%"><b>Employee Points</b></th>';
                                                    echo' <th style="min-width: 15%"><b>Manager Pointss</b></th>';
                                                echo' </tr>';
                                            echo' </thead>';
                                            echo' <tbody>';

                                            $mpoint = 0;
                                            $emp_point = 0;
                                            $mngr_point = 0;
                                            $total_measure = 0;
                                            $total_employee = 0;
                                            $total_manager = 0;

                                            foreach($hd_item['sub'] as $sub_item){
                                                //echo '<pre>';print_r($sub_item);exit;
                                                $mpoint = $sub_item['measuredPoints'] ? floatval($sub_item['measuredPoints']) : 0; // Convert to number
                                                $emp_point = $sub_item['employeePoints'] ? floatval($sub_item['employeePoints']) : 0; // Convert to number
                                                $mngr_point = $sub_item['managerPoints'] ? floatval($sub_item['managerPoints']) : 0; // Convert to number

                                                echo' <tr>';
                                                    echo' <td class="text-center">&nbsp;</td>';
                                                    echo' <td>* '. $sub_item['description'] .'</td>';
                                                    echo' <td>'. $mpoint .'</td> ';

                                                    echo' <td>'. $emp_point .'</td> ';
                                                    echo' <td>'. $mngr_point .'</td> ';
                                                echo' </tr>';

                                                $total_measure += $mpoint;
                                                $total_employee += $emp_point;
                                                $total_manager += $mngr_point;
                                            };

                                                echo' <tr style="background-color:rgb(221,210,0,0.6);">';
                                                    echo' <td class="text-center">-</td>';
                                                    echo' <td class="text-center">Total</td>';
                                                    echo' <td>'. $total_measure .'</td>';
                                                    echo' <td>'. $total_employee .'</td>';
                                                    echo' <td>'. $total_manager .'</td>';
                                                echo' </tr>';

                                            echo' </tbody>';
                                    echo' </table>';
                                echo' </div>';

                                $grand_total_measure += $total_measure;
                                $grand_total_employee += $total_employee;
                                $grand_total_manager += $total_manager;
                            }
                        };

                        $tottbl = ""; //for grand total & percentages
                        $percentage_measure = 100 . '%';
                        $percentage_employee = ( (100/$grand_total_measure) * $grand_total_employee ). '%';
                        $percentage_manager = ( (100/$grand_total_measure) * $grand_total_manager ) . '%';

                        echo ' <div class="table-responsive">';
                           echo' <table id="soft_skill_MPO_grandTotal_table" class="<?php echo table_class(); ?>">';
                                echo' <tbody>';
                                    echo' <tr style="background-color:rgba(255,163,34,0.67);">';
                                        echo' <td class="text-center" style="min-width: 5%">--</td>';
                                        echo' <td class="text-center" style="min-width: 50%">Grand Total</td>';
                                        echo' <td style="min-width: 15%">'. $grand_total_measure .'</td>';
                                        echo' <td style="min-width: 15%">'. $grand_total_employee .'</td>';
                                        echo' <td style="min-width: 15%">'. $grand_total_manager .'</td>';
                                    echo' </tr>';
                                    echo' <tr style="background-color:rgba(255,163,34,0.67);">';
                                        echo' <td class="text-center" style="min-width: 5%">%</td>';
                                        echo' <td class="text-center" style="min-width: 50%">Percentage</td>';
                                        echo' <td style="min-width: 15%">'. $percentage_measure .'</td>';
                                        echo' <td style="min-width: 15%">'. $percentage_employee .'</td>';
                                        echo' <td style="min-width: 15%">'. $percentage_manager .'</td>';
                                    echo' </tr>';
                                echo' </tbody>';
                            echo' </table>';
                        echo' </div>';

                    ?>
                </div>
            </div>
            <div id="rating_error" class="error-message"></div>
        </div>
        

    <?php }else{ ?>     <!-- ....................................................................grade base -->

        <div class="col-md-12">
            <div class="col-md-12">
                <div style="margin-top: 15px;<?php
                $isHideMarkedByEmpShow = hide_marks_marked_by_employee();
                if ($isHideMarkedByEmpShow) {
                    echo 'visibility: hidden;';
                }
                ?>"><span class="lbl1" style="font-weight: 600;"><?php echo $this->lang->line('appraisal_marked_by_you'); ?><!--Marked By You--></span>
                </div>
                <div id="softskills_template">
                <?php 
                    echo '<table class="table table-striped table-bordered">
                            <thead>
                                <tr>';
                            echo '<th>'.$this->lang->line('appraisal_performance_area').'</th>';
                            echo '<th>'.$this->lang->line('sub_performance_area').'</th>';
                        foreach ($manager['skills_grades_list'] as $item){
                            if ($item['grade'] == "Not Applicable") {
                            echo '<th>'.$item['grade'].' </th>';
                            } else {
                            echo '<th>'.$item['grade'].'( '.$item['marks'].' Marks)</th>';
                            }
                        }
                
                        echo '</tr>
                            </thead>
                            <tbody id="table_body_read_only">';

                                $total = 0;
                                foreach ($manager['performance_areas'] as $item){
                                    if(isset($item['sub']) && $item !=null){ // if there are any sub performance area
                                        $rowspan = sizeof($item['sub']) + 1;
                                        echo '<tr> <td rowspan="'.$rowspan.'">' . $item['description'] . '</td>';
                
                                        foreach ($item['sub'] as $itemSub){
                                            echo '<tr><td>'.$itemSub['description'].'</td>';
                                            $radio_group_name = "performance" . $itemSub['performance_area_id'];
                                            $currently_selected_grade_id =$itemSub['grade_id'];
                                            $performance_area_id =$itemSub['performance_area_id'];
                                            foreach ($manager['skills_grades_list'] as $skillItem){
                                                $is_checked = '';
                                                if ($currently_selected_grade_id == $skillItem['id']) {
                                                    $is_checked = 'checked="checked"';
                                                    $total += $skillItem['marks'];
                                                }
                                                echo '<td style="text-align: center;"><input ' .$is_checked. ' type="radio" name="' .$radio_group_name. '"/></td>';
                
                                            }
                                            echo '</tr>';
                                        }
                
                                    }
                                    else{
                                        $radio_group_name = "performance" . $item['performance_area_id'];
                                        $currently_selected_grade_id =$item['grade_id'];
                                        $performance_area_id =$item['performance_area_id'];
                                        echo '<tr>
                                                <td>'.$item['description'].'</td>
                                                <td></td>';
                                                foreach ($manager['skills_grades_list'] as $skillItem){
                                                    $is_checked = '';
                                                    if ($currently_selected_grade_id == $skillItem['id']) {
                                                        $is_checked = 'checked="checked"';
                                                        $total += $skillItem['marks'];
                                                    }
                                                    echo '<td style="text-align: center;"><input ' .$is_checked. ' type="radio" name="' .$radio_group_name. '"/></td>';
                    
                                                }
                                        echo'<tr>';
                                    }
                                }
                        echo'</tbody>
                        </table>';
                ?>
                </div>
            </div>
            <div id="rating_error" class="error-message"></div>
        </div>
        <div class="col-md-12">
            <div class="col-md-2 total_div" style="margin-top: 7px;">
                                            <span style="background-color: #02cf32;color: white;">&nbsp;<?php echo $this->lang->line('appraisal_total_marks'); ?>: </span>
                &nbsp;<span id="total"><?php echo $total; ?></span>
            </div>
            <div class="col-md-4 last_update_mgr_div" style="margin-top: 7px;">
                <?php echo $this->lang->line('common_last_updated'); ?><!--Last updated-->:
                <span id="last_update_mgr"><?php echo $manager['last_update_time']; ?></span></div>
        </div>

        <div class="col-md-12" style="<?php
        $isHideMarkedByEmpShow = hide_marks_marked_by_employee();
        if ($isHideMarkedByEmpShow) {
            echo 'display:none';
        }
        ?>">
            <div class="col-md-12">
                <div style="margin-top: 15px;"><span class="lbl1"
                                                    style="font-weight: 600;"><?php echo $this->lang->line('appraisal_marked_by_employee'); ?><!--Marked By Employee--></span>
                </div>
                <div id="softskills_template_emp_self">
                <?php
                    echo '<table class="table table-striped table-bordered">
                                <thead>
                                    <tr>';
                                    echo '<th>'.$this->lang->line('appraisal_performance_area').'</th>';
                                    echo '<th>'.$this->lang->line('sub_performance_area').'</th>';
                                foreach ($employee['skills_grades_list'] as $item){
                                if ($item['grade'] == "Not Applicable") {
                                        echo '<th>'.$item['grade'].' </th>';
                                } else {
                                        echo '<th>'.$item['grade'].'( '.$item['marks'].' Marks)</th>';
                                }
                                }
                            echo '</tr>
                                </thead>
                            <tbody id="table_body_read_only">';
                            $total_e = 0;
                            foreach ($employee['performance_areas'] as $item){

                                if(isset($item['sub']) && $item['sub']!=null){

                                    $rowspan = sizeof($item['sub']) + 1;
                                    echo '<tr> <td rowspan="'.$rowspan.'">' . $item['description'] . '</td>';
            
                                    foreach ($item['sub'] as $itemSub){
                                        echo '<tr><td>'.$itemSub['description'].'</td>';
                                        $radio_group_name = "performance_self" . $itemSub['performance_area_id'];
                                        $currently_selected_grade_id =$itemSub['grade_id'];
                                        $performance_area_id =$itemSub['performance_area_id'];
                                        foreach ($employee['skills_grades_list'] as $skillItem){
                                            $is_checked = '';
                                            if ($currently_selected_grade_id == $skillItem['id']) {
                                                $is_checked = 'checked="checked"';
                                                $total_e += $skillItem['marks'];
                                            }
            
                                            echo '<td style="text-align: center;"><input ' .$is_checked. ' type="radio" name="' .$radio_group_name. '"/></td>';
            
                                        }
                                        echo '</tr>';
                                    }
            
                                }else{
                                    $radio_group_name = "performance" . $item['performance_area_id'];
                                        $currently_selected_grade_id =$item['grade_id'];
                                        $performance_area_id =$item['performance_area_id'];
                                        echo '<tr>
                                                <td>'.$item['description'].'</td>
                                                <td></td>';
                                                foreach ($employee['skills_grades_list'] as $skillItem){
                                                    $is_checked = '';
                                                    if ($currently_selected_grade_id == $skillItem['id']) {
                                                        $is_checked = 'checked="checked"';
                                                        $total_e += $skillItem['marks'];
                                                    }
                                                    echo '<td style="text-align: center;"><input ' .$is_checked. ' type="radio" name="' .$radio_group_name. '"/></td>';
                    
                                                }
                                        echo'<tr>';
                                }
                            }
                    
                    echo '</tbody></table>';
                    ?>
                </div>
            </div>
            <div id="rating_error" class="error-message"></div>
        </div>
        <div class="col-md-12" style="<?php
        if ($isHideMarkedByEmpShow) {
            echo 'display:none';
        }
        ?>">
            <div class="col-md-2 total_div" style="margin-top: 7px;">
                <span style="background-color: #02cf32;color: white;">&nbsp;<?php echo $this->lang->line('appraisal_total_marks'); ?>: </span>
                &nbsp;<span id="total_emp"><?php echo $total_e; ?></span>
            </div>
            <div class="col-md-4 last_update_emp_div" style="margin-top: 7px; margin-bottom: 7px;">
                <?php echo $this->lang->line('common_last_updated'); ?><!--Last updated-->:
                <span id="last_update_emp"><?php echo $employee['last_update_time']; ?></span></div>
        </div>

    <?php } ?>
    <!-- end of tables -->

    <br>&nbsp;</br>

    <?php if($manager['markingType'] == 2){ ?>
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        C.“Begin with the end in mind”
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        D.Miscellaneous worth Mentioning
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        E.Benchmark Objective assessment
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        F. Job Purpose/ Main Functions/ Targets
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="manager_comment_label">
                        G. Job Purpose/ Main Functions/ Targets
                    </label>
                    : <?php echo $manager['manager_comment']; ?>
                </div>
            </div>
        </div>
    <?php } ?> 

    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_activity_department_manager_comment'); ?><!--Manager comment-->
                </label>
                : <?php echo $manager['manager_comment']; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_suggested_reward'); ?><!--Suggested reward-->
                </label>
                : <?php echo $manager['suggested_reward']; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_identified_training_needs'); ?><!--Identified training needs-->
                </label>
                : <?php echo $manager['identified_training_needs']; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?><!--Special remarks from HOD-->
                </label>
                : <?php echo $manager['special_remarks_from_hod']; ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_signature_of_hod'); ?>:
                </label>
                <br/><br/>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_signature_of_hr'); ?>:
                </label>
                <br/><br/>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_signature_of_employee'); ?>:
                </label>
                <br/><br/>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_employee_comment'); ?><!--Employee comment-->
                </label>
                : <?php echo $manager['special_remarks_from_emp']; ?>
                <br/><br/>
            </div>
        </div>
    </div>    
    <!-- <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group">
                <label class="manager_comment_label">
                    <?php echo $this->lang->line('appraisal_signature_of_coo'); ?>:
                </label>
                <br/><br/>
            </div>
        </div>
    </div> -->
</div>
<?php //$this->load->view('include/js_resource_footer'); ?>