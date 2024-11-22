<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('community_ngo_helper');

$degree = load_degree();
$university = load_university();
$language = load_language();
$sickness = fetch_permanentSicknes();
$vehiMaster = fetch_vehicleMaster();
$occupation = load_Jobcategories();
$occupationTypes = load_occupationTypes();
$grades = load_grades();
$schools = load_ngoSchools();
$helpCategory = load_help_category();
$schoolTypes = load_schoolTypes();
$date_format_policy = date_format_policy();
?>

    <style>
        .btn-circle {
            padding: 0px 6px;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            line-height: 25px;
        }

        #education-tab .nav-tabs-custom > .nav-tabs > li.active {
            border-top: 0px !important;
        }
    </style>

    <div id="details">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="education-tab"
                style="border: 1px solid rgba(150, 145, 145, 0.21); margin-bottom: 5px;">
                <li class="other_details_li active" data-value="languageTab">
                    <a href="#languageTab" data-toggle="tab" id="lanTab"
                       aria-expanded="true"> <i
                            class="fa fa-language"></i> <?php echo $this->lang->line('communityngo_Language'); ?></a>
                    <!--Language-->
                </li>
                <li class="other_details_li" data-value="occupationTab">
                    <a href="#occupationTab" data-toggle="tab" id="occTab"
                       aria-expanded="true"> <i
                            class="fa fa-user-md"></i> <?php echo $this->lang->line('communityngo_Job'); ?></a>
                    <!--Occupation-->
                </li>
                <li class="other_details_li" data-value="certificationTab">
                    <a href="#certificationTab" data-toggle="tab" id="quaTab"
                       aria-expanded="false"> <i
                            class="fa fa-graduation-cap"></i> <?php echo $this->lang->line('communityngo_Qualification'); ?>
                    </a>
                    <!--Qualification-->
                </li>
                <li class="other_details_li" data-value="healthStatus_Tab">
                    <a href="#healthStatus_Tab" data-toggle="tab" id="hsTab"
                       aria-expanded="true"> <i
                            class="fa fa-medkit"></i> <?php echo $this->lang->line('communityngo_CPermanent_sickness'); ?>
                    </a>
                    <!--Health Status-->
                </li>
                <li class="other_details_li" data-value="vehicleStatus_Tab">
                    <a href="#vehicleStatus_Tab" data-toggle="tab" id="vehTab"
                       aria-expanded="true"> <i
                            class="fa fa-car"></i> <?php echo $this->lang->line('communityngo_vehicle_details'); ?></a>
                    <!--Vehicle Status-->
                </li>
                <li class="other_details_li" data-value="memHeplRStatus_Tab">
                    <a href="#memHeplRStatus_Tab" data-toggle="tab" id="helpRTab"
                       aria-expanded="true"><i
                            class="fa fa-question-circle"></i> <?php echo $this->lang->line('communityngo_memHelp_require'); ?>
                    </a>
                    <!--help req Status-->
                </li>
                <li class="other_details_li" data-value="willingToHelp_Tab">
                    <a href="#willingToHelp_Tab" data-toggle="tab" id="willingToHeplTab"
                       aria-expanded="true"><i
                            class="far fa-hands-helping"></i> <?php echo $this->lang->line('communityNgo_willing_to_help'); ?>
                    </a>
                    <!--willing to help-->
                </li>
                <li class="pull-right" style="padding: 4px 0px;">
                    <div class="pull-right">
                        <a onclick="open_add_modal()" data-toggle="modal" class="btn btn-sm btn-primary"
                          >
                            <i class="fa fa-plus"> </i><?php echo $this->lang->line('common_add'); ?><!--Add-->
                        </a>
                    </div>
                </li>
            </ul>

            <div class="tab-content" style=" padding-top: 0px;">
                <div class="tab-pane disabled lanugage active" id="languageTab">
                    <div class="" style="" id="">
                        <?php
                        $a = 1;
                        foreach ($Language as $key => $det) {
                            echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $a . '</h5></td>
                                      <td ><h5>' . $det['description'] . '</h5></td>
                                      <td><span class="pull-right">
                                <button id="edit" title="" rel="tooltip" class="btn btn-circle btn-mini btn-danger" onclick="deleteLanguage(\'' . $det['MemLanguageID'] . '\')"
                                        data-original-title="Edit"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </span>
                            </td>
                                </tr>
                                  </table>';
                            $a++;
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled occupation" id="occupationTab">
                    <div class="tab-pane" id="" style="">
                        <?php
                        foreach ($Occupation as $key => $row) {
                            echo '
                        <table style="width: 100%;  margin-bottom: 1%">
                        <tbody>
                        <tr>
                        <td>';
                            if ($row['OccTypeID'] == 1) {
                                $school = $row['schoolComDes'];
                                $schType = $row['schoolTypeDes'];
                                $Grade = $row['gradeComDes'];
                                $Medium = $row['Medium'];
                                if ($row['Address'] == '' || $row['Address'] == null) {
                                    $Address = '';
                                } else {
                                    $Address = 'Address : ' . $row['Address'];
                                }
                                if (!empty($row['DateFrom']) && $row['DateFrom'] != '0000-00-00') {
                                    $DateFrom = 'Date : ' . format_date($row['DateFrom']) . ' - ' . format_date($row['DateTo']);
                                } else {
                                    $DateFrom = '';
                                }

                                if ($row['isActive'] == 0 || $row['isActive'] == null) {
                                    $isActiveYN = '';
                                } else {
                                    $isActiveYN = '- ' . $this->lang->line('communityngo_Active');
                                }


                                echo '  <div><h4>' . $row['Description'] . ' ' . $isActiveYN . '</h4></div>
                                <div>School : ' . $school . '</div>
                                <div>Type : ' . $schType . '</div>
                                        <div>Class : ' . $Grade . '</div>
                                        <div>Medium : ' . $Medium . '</div>
                                       
                                        <div><small>' . $Address . '</small></div>
                                        <div><small>' . $DateFrom . '</small></div>';

                            } else {
                                $JobCategory = $row['JobCatDescription'];
                                if ($row['jobDescription'] == '' || $row['jobDescription'] == null) {
                                    $jobDescription = '';
                                } else {
                                    $jobDescription = 'Job Description : ' . $row['jobDescription'];
                                }

                                if ($row['Specialization'] == '' || $row['Specialization'] == null) {
                                    $JobSpecialization = '';
                                } else {
                                    $JobSpecialization = 'Job Specialization : ' . $row['Specialization'];
                                }

                                if ($row['WorkingPlace'] == '' || $row['WorkingPlace'] == null) {
                                    $WorkingPlace = '';
                                } else {
                                    $WorkingPlace = 'Working Place : ' . $row['WorkingPlace'];
                                }

                                if ($row['Address'] == '' || $row['Address'] == null) {
                                    $Address = '';
                                } else {
                                    $Address = 'Address : ' . $row['Address'];
                                }

                                if (!empty($row['DateFrom']) && $row['DateFrom'] != '0000-00-00') {
                                    $DateFrom = 'Date : ' . format_date($row['DateFrom']) . ' - ' . format_date($row['DateTo']);
                                } else {
                                    $DateFrom = '';
                                }

                                if ($row['isPrimary'] == 0 || $row['isPrimary'] == null) {
                                    $isPrimaryYN = '';
                                } else {
                                    $isPrimaryYN = '- ' . $this->lang->line('communityngo_headPrimary');
                                }

                                echo '  <div><h4>' . $JobCategory . '</h4></div>
                                        <div>' . $row['Description'] . ' ' . $isPrimaryYN . '</div>
                                        <div>' . $JobSpecialization . '</div>
                                        <div>' . $jobDescription . '</div>
                                        <div>' . $WorkingPlace . '</div>
                                        <div><small>' . $Address . '</small></div>
                                        <div><small>' . $DateFrom . '</small></div>';
                            }

                            echo '</td>
                        <td>
                        
                        <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $row['MemJobID'] . ', \'Occupation Attachments\',10)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="editOccupation(\'' . $row['MemJobID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_Occupation(\'' . $row['MemJobID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                          
                        </td>
                    </tr>
                    </tbody>
                </table>
                <hr>';
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled certification" id="certificationTab">
                    <div class="tab-pane" id="three" style="">
                        <?php
                        foreach ($Qualification as $key => $row) {

                            if ($row['Year'] == 0 || $row['Year'] == null) {
                                $Year = '';
                            } else {
                                $Year = 'Year : ' . $row['Year'];
                            }

                            if ($row['UniversityID'] == 0 || $row['UniversityID'] == null) {
                                $Institute = '';
                            } else {
                                $Institute = $row['UniversityDescription'];
                            }

                            if ($row['gradeComID'] == 0 || $row['gradeComID'] == null) {
                                $grade = '';
                            } else {
                                $grade = $row['gradeComDes'];
                            }

                            if ($row['CurrentlyReading'] == 0 || $row['CurrentlyReading'] == null) {
                                $CurrentlyReadingYN = '';
                            } else {
                                $CurrentlyReadingYN = '- ' . $this->lang->line('communityngo_currently_reading');
                            }

                            if (empty($row['Remarks'])) {
                                $Remarks = '';
                            } else {
                                $Remarks = $this->lang->line('communityngo_Remarks') . ' : ' . $row['Remarks'];
                            }
                            echo '
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td>
                            <div><h4>' . $row['DegreeDescription'] . ' ' . $CurrentlyReadingYN . '</h4></div>
                            <div>' . $Institute . '</div>
                            <div>' . $Year . '</div>
                            <div>' . $grade . '</div>
                            
                            <small> ' . $Remarks . ' </small>
                        </td>
                        <td>
                        
                        <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $row['QualificationID'] . ', \'Qualification Attachments\',11)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="editQualification(\'' . $row['QualificationID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_Qualification(\'' . $row['QualificationID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                        
                         </td>
                    </tr>
                    </tbody>
                </table>
                <hr>';
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled healthStatus" id="healthStatus_Tab">
                    <div class="" style="" id="">
                        <?php
                        $a = 1;
                        foreach ($Sickness as $key => $det) {

                            if ($det['startedFrom'] == 0 || $det['startedFrom'] == null) {
                                $startedFrom = '';
                            } else {
                                $startedFrom = 'Started From :' . $det['startedFrom'];
                            }

                            if ($det['medicalCondition'] == 0 || $det['medicalCondition'] == null) {
                                $medicalCondition = '';
                            } else {
                                $medicalCondition = 'Medical Condition :' . $det['medicalCondition'];
                            }

                            if ($det['monthlyExpenses'] == 0 || $det['monthlyExpenses'] == null) {
                                $monthlyExpenses = '';
                            } else {
                                $monthlyExpenses = 'Monthly Expenses :' . $this->common_data['company_data']['company_default_currency'] . ' ' . $det['monthlyExpenses'];
                            }

                            if ($det['Remarks'] == 0 || $det['Remark'] == null) {
                                $sickRemarks = '';
                            } else {
                                $sickRemarks = 'Remark :' . $det['Remarks'];
                            }
                            echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><div style="color: #00a5e6;"><h5>' . $a . '</h5></div></td>
                                      <td><div style="color: #00a5e6;"><h5>' . $det['sickDescription'] . '</h5></div>
                                      <div><small>' . $startedFrom . '</small></div>
                                      <div><small>' . $medicalCondition . '</small></div>
                                      <div><small>' . $monthlyExpenses . '</small></div>
                                      <div><small>' . $sickRemarks . '</small></div></td>
                                      <td>
                                      
                                      <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $det['memPSicknessID'] . ', \'Sickness Attachments\',9)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_healthStatus(\'' . $det['memPSicknessID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_healthStatus(\'' . $det['memPSicknessID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                            $a++;
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled vehicleStatus" id="vehicleStatus_Tab">
                    <div class="" style="" id="">
                        <?php
                        $a = 1;
                        foreach ($VehicleConfig as $key => $det) {


                            if ($det['vehiDescription'] == null) {
                                $vehiDesc = '';
                            } else {
                                $vehiDesc = 'Vehicle Description :' . $det['vehiDescription'];
                            }
                            if ($det['vehiStatus'] == 0 || $det['vehiStatus'] == null) {
                                $vehiStatus = 'Vehicle Status : <span class="label label-success">Own</span>';
                            } else {
                                $vehiStatus = 'Vehicle Status : <span class="label label-warning">Lease</span>';
                            }

                            if ($det['vehiRemark'] == 0 || $det['vehiRemark'] == null) {
                                $vehiRemarks = '';
                            } else {
                                $vehiRemarks = 'Vehicle Remark :' . $det['vehiRemark'];
                            }

                            echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $a . '</h5></td>
                                      <td><div><h5>' . $det['vehicleDescription'] . '</h5></div>
                                      <div><small>' . $vehiDesc . '</small></div>
                                      <div><small>' . $vehiStatus . '</small></div>
                                      <div><small>' . $vehiRemarks . '</small></div></td>
                                      <td>
                                      
                                                                                                                             
                                      <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $det['memVehicleID'] . ', \'Vehicle Attachments\',9)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_vehicleStatus(\'' . $det['memVehicleID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_vehicleStatus(\'' . $det['memVehicleID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                            $a++;
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled memHeplRStatus" id="memHeplRStatus_Tab">
                    <div class="" style="" id="">
                        <?php

                        IF ($HelpReqConGv) {
                            echo '<br><div style="color: #00a5e6;">Requirement Type : Government Help</div>';

                            $g = 1;
                            foreach ($HelpReqConGv as $key => $det) {

                                if ($det['hlprDescription'] == null) {
                                    $helpDesc = '';
                                } else {
                                    $helpDesc = 'Help Description :' . $det['hlprDescription'];
                                }


                                echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $g . '</h5></td>
                                      <td><div><h5>' . $det['helpRequireDesc'] . '</h5></div>
                                      <div><small>' . $helpDesc . '</small></div>
                                      </td>
                                      <td>
                                      
                                                                                                                             
                                      <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $det['memHelprID'] . ', \'Help Attachments\',9)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                                $g++;
                            }
                        }

                        IF ($HelpReqConPv) {

                            echo '<div style="color: #00a5e6;">Requirement Type : Private Help</div>';

                            $p = 1;
                            foreach ($HelpReqConPv as $key => $det) {

                                if ($det['hlprDescription'] == null) {
                                    $helpDesc = '';
                                } else {
                                    $helpDesc = 'Help Description :' . $det['hlprDescription'];
                                }


                                echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $p . '</h5></td>
                                      <td><div><h5>' . $det['helpRequireDesc'] . '</h5></div>
                                      <div><small>' . $helpDesc . '</small></div>
                                      </td>
                                      <td>
                                      
                                                                                                                             
                                      <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $det['memHelprID'] . ', \'Help Attachments\',9)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                                $p++;
                            }
                        }

                        IF ($HelpReqConCs) {

                            echo '<div style="color: #00a5e6;">Requirement Type : Consultancy</div>';

                            $c = 1;
                            foreach ($HelpReqConCs as $key => $det) {


                                if ($det['hlprDescription'] == null) {
                                    $helpDesc = '';
                                } else {
                                    $helpDesc = 'Help Description :' . $det['hlprDescription'];
                                }


                                echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $c . '</h5></td>
                                      <td><div><h5>' . $det['helpRequireDesc'] . '</h5></div>
                                      <div><small>' . $helpDesc . '</small></div>
                                      </td>
                                      <td>
                                      
                                                                                                                             
                                      <span class="pull-right">
                               <span class="attachmentSpan" style="display: inline-block;"><a
                                                   onclick="add_other_attachment(' . $det['memHelprID'] . ', \'Help Attachments\',9)"><span
                                                        title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip "
                                                        style="color:green;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            
                             <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 44px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_memHeplRStatus(\'' . $det['memHelprID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                                $c++;
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="tab-pane disabled willingToHelp" id="willingToHelp_Tab">
                    <div class="" style="" id="">
                        <?php

                            echo '<br><div style="color: #00a5e6;"> Like to help Categories </div>';

                            $c = 1;
                            foreach ($willingToHelp as $key => $det) {


                                if ($det['helpComments'] == null) {
                                    $helpDesc = '';
                                } else {
                                    $helpDesc = 'Comments :' . $det['helpComments'];
                                }


                                echo '
                           <table class="table table-condensed">
                                    <tr>
                                      <td style="width: 10px"><h5>' . $c . '</h5></td>
                                      <td><div><h5>' . $det['helpCategoryDes'] . '</h5></div>
                                      <div><small>' . $helpDesc . '</small></div>
                                      </td>
                                      <td>
                                      
                                                                                                                             
                                      <span class="pull-right">
                           
                             <span class="pull-right" style="margin-right: -13px;">
                               <span class="editSpan" style="display: inline-block;"><a
                                                   onclick="edit_willingToHelp(\'' . $det['willingToHelpID'] . '\')"><span
                                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil "
                                                        style="color:blue;"
                                                        data-original-title="Edit"></span></a></span>
                            </span>
                            <span class="pull-right" style="margin-top: 22px; margin-right: -13px;">
                               <span class="deleteSpan" style="display: inline-block;"><a
                                                   onclick="delete_willingToHelp(\'' . $det['willingToHelpID'] . '\')"><span
                                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                            </span>
                                 
                            </td>
                                </tr>
                                  </table>';

                                $c++;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="language_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="language_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="language_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="LanguageID">
                                    <?php echo $this->lang->line('communityngo_Language'); ?><!--Language--> <?php required_mark(); ?></label>
                                <div class="col-sm-6">
                                    <select id="LanguageID" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_Language'); ?>"
                                            name="LanguageID">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($language)) {
                                            foreach ($language as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['languageID'] ?>"><?php echo $val['description'] ?></option>
                                                <?php

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-lan" name="hidden-id-lan" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-lg"
                            onclick="save_Language()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="healthStatus_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="healthStatus_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="healthStatus_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="sickAutoID">
                                    <?php echo $this->lang->line('communityngo_CPermanent_sickness'); ?><!--Sickness--> <?php required_mark(); ?></label>

                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <?php echo form_dropdown('sickAutoID', $sickness, '',
                                            'class="form-control select2" id="sickAutoID" '); ?>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('sickness')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 6%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_sickness_from'); ?><!--Started From--></label>
                                <div class="col-sm-6">
                                    <input type="text" name="startedFrom" value="" id="startedFrom"
                                           class="form-control"
                                           placeholder="<?php echo $this->lang->line('communityngo_sickness_from'); ?>">
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 12%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_medicalcondiation'); ?><!--Medical Condition--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="medicalCondition"
                                              placeholder="<?php echo $this->lang->line('communityngo_medicalcondiation'); ?>"
                                              name="medicalCondition" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 20%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('CommunityNgo_member_expenses'); ?><!--Monthly Expenses--></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <div
                                            class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                                        <input type="text" name="monthlyExpenses" value="" id="monthlyExpenses"
                                               class="form-control">
                                    </div>
                                    <span class="input-req-inner"></span>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 26%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_Remarks'); ?><!--Remarks--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="Sick_remark"
                                              placeholder="<?php echo $this->lang->line('communityngo_Remarks'); ?>"
                                              name="Sick_remark" rows="2"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-HS" name="hidden-id-HS" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-hs"
                            onclick="save_healthStatus()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="addModal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="qualificationModal-title"></h4>
                </div>
                <?php echo form_open('', 'role="form" id="qualification_form"'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-bottom: 6%;">
                                <label class="col-sm-4 control-label" for="DegreeID">
                                    <?php echo $this->lang->line('communityngo_QualificationType'); ?><!--Degree--> <?php required_mark(); ?></label>
                                <div class="col-sm-6">

                                    <div class="input-group">
                                        <select id="DegreeID" class="form-control select2" name="DegreeID"
                                                onchange="display_grade_for_school();"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_QualificationType'); ?>">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($degree)) {
                                                foreach ($degree as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['DegreeID'] ?>"><?php echo $val['DegreeDescription'] ?></option>
                                                    <?php

                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('qualification')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group university_div" style="margin-bottom: 12%; display: block;"
                                 id="university_div">
                                <label class="col-sm-4 control-label" for="UniversityID">
                                    <?php echo $this->lang->line('communityngo_University'); ?><!--Institution--></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <select id="UniversityID" class="form-control select2"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_University'); ?>"
                                                name="UniversityID">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($university)) {
                                                foreach ($university as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['UniversityID'] ?>"><?php echo $val['UniversityDescription'] ?></option>
                                                    <?php

                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="input-group-btn">
                                                   <button class="btn btn-default" type="button"
                                                           onclick="sendemail('institute')"
                                                           style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group grade_div" style="margin-bottom: 12%; display: block;"
                                 id="grade_div">
                                <label class="col-sm-4 control-label" for="gradeComID">
                                    <?php echo $this->lang->line('communityngo_SchoolGrade'); ?><!--Grade--></label>
                                <div class="col-sm-6">
                                    <select id="gradeComID" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_SchoolGrade'); ?>"
                                            name="gradeComID">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($grades)) {
                                            foreach ($grades as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['gradeComID'] ?>"><?php echo $val['gradeComDes'] ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="CurrentlyReading">
                                    <?php echo $this->lang->line('communityngo_currently_reading'); ?><!--Currently Reading--></label>
                                <div class="col-sm-6">
                                    <input type="checkbox" name="CurrentlyReading" id="CurrentlyReading" value="1"
                                           style="margin-top: 10px">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 23%;">
                                <label class="col-sm-4 control-label" for="Year">
                                    <?php echo $this->lang->line('communityngo_Year'); ?><!--Completed Year--></label>
                                <div class="col-sm-6">
                                    <input type="text" name="Year" value="" id="Year"
                                           class="form-control"
                                           placeholder="<?php echo $this->lang->line('communityngo_Year'); ?>">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 12%;">
                                <label class="col-sm-4 control-label" for="Remarks">
                                    <?php echo $this->lang->line('communityngo_Remarks'); ?><!--Remarks--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="Remarks" placeholder="Remarks"
                                              name="Remarks" rows="2"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn"
                            onclick="saveQualification()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="update-btn"
                            onclick="updateQualification()">
                        <?php echo $this->lang->line('common_update'); ?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="occupation_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="occupation_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="occupation_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="padding:15px">
                                <label class="col-sm-4 control-label" for="OccTypeID">
                                    <?php echo $this->lang->line('communityngo_occupationType'); ?><!--Occupation Types--> <?php required_mark(); ?></label>
                                <div class="col-sm-6">
                                    <select id="OccTypeID" class="form-control select2 occupationType"
                                            onchange="change_dropdown();"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_occupationType'); ?>"
                                            name="OccTypeID">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($occupationTypes)) {
                                            foreach ($occupationTypes as $val) {
                                                if ($val['OccTypeID'] == 1) {
                                                    ?>
                                                    <option selected="selected"
                                                            value="<?php echo $val['OccTypeID'] ?>"><?php echo $val['Description'] ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['OccTypeID'] ?>"><?php echo $val['Description'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group School" style="padding:15px">
                                <label class="col-sm-4 control-label" for="School">
                                    <?php echo $this->lang->line('communityngo_School'); ?><!--School--><?php required_mark(); ?></label>


                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <select id="schoolComID" class="form-control select2"
                                                onchange="get_sch_medium(this.value); get_sch_address(this.value);"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_School'); ?>"
                                                name="schoolComID">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($schools)) {
                                                foreach ($schools as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['schoolComID'] ?>"><?php echo $val['schoolComDes'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>

                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('school')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group schoolTypeID" style="padding:15px">
                                <label class="col-sm-4 control-label" for="schoolTypeID">
                                    <?php echo $this->lang->line('communityngo_SchoolType'); ?><!--School Type--><?php required_mark(); ?></label>

                                <div class="col-sm-6">
                                    <select id="schoolTypeID" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_SchoolType'); ?>"
                                            name="schoolTypeID">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($schoolTypes)) {
                                            foreach ($schoolTypes as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['schoolTypeID'] ?>"><?php echo $val['schoolTypeDes'] ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class="form-group gradeComID" style="padding:15px">
                                <label class="col-sm-4 control-label" for="gradeComID">
                                    <?php echo $this->lang->line('communityngo_SchoolGrade'); ?><!--Occupation Types--><?php required_mark(); ?></label>
                                <div class="col-sm-6">
                                    <select id="gradeComID" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_SchoolGrade'); ?>"
                                            name="gradeComID">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($grades)) {
                                            foreach ($grades as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['gradeComID'] ?>"><?php echo $val['gradeComDes'] ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group MediumID" style="padding:15px">
                                <label class="col-sm-4 control-label" for="gradeComID">
                                    <?php echo $this->lang->line('communityngo_medium'); ?><!--Medium--></label>
                                <div class="col-sm-6">
                                    <select id="MediumID" class="form-control select2"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_medium'); ?>"
                                            name="MediumID">
                                        <option value=""></option>
                                    </select>

                                </div>
                            </div>

                            <div class="form-group JobCategoryID hide" style="padding:15px">
                                <label class="col-sm-4 control-label" for="JobCategoryID">
                                    <?php echo $this->lang->line('communityngo_Job_Category'); ?><!--Job Category--> <?php required_mark(); ?></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <select id="JobCategoryID" class="form-control select2"
                                                onchange="get_job_specialization(this.value);"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_Job_Category'); ?>"
                                                name="JobCategoryID">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($occupation)) {
                                                foreach ($occupation as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['JobCategoryID'] ?>"><?php echo $val['JobCatDescription'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('job_category')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group specializationID hide" style="padding:15px">
                                <label class="col-sm-4 control-label" for="specializationID">
                                    <?php echo $this->lang->line('communityngo_Job_Specialization'); ?><!--Job Specialization--> </label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <select id="specializationID" class="form-control select2"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_Job_Specialization'); ?>"
                                                name="specializationID">
                                            <option value=""></option>
                                        </select>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('job_specialization')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group jobDescription hide" style="padding:15px">
                                <label class="col-sm-4 control-label" for="jobDescription">
                                    <?php echo $this->lang->line('communityngo_Job_Description'); ?><!--Job Description--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="jobDescription" name="jobDescription"
                                           placeholder="<?php echo $this->lang->line('communityngo_Job_Description'); ?>">
                                </div>
                            </div>

                            <div class="form-group WorkingPlace hide" style="padding:15px">
                                <label class="col-sm-4 control-label" for="WorkingPlace">
                                    <?php echo $this->lang->line('communityngo_Job_WorkingPlace'); ?><!--Working Place--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="WorkingPlace" name="WorkingPlace"
                                           placeholder="<?php echo $this->lang->line('communityngo_Job_WorkingPlace'); ?>">
                                </div>
                            </div>

                            <div class="form-group Address" style="padding:15px">
                                <label class="col-sm-4 control-label" for="Address">
                                    <?php echo $this->lang->line('communityngo_Job_Address'); ?><!--Address--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="Address" name="Address"
                                           placeholder="<?php echo $this->lang->line('communityngo_Job_Address'); ?>">
                                </div>
                            </div>

                            <div class="form-group DateFrom " style="padding:15px">
                                <label class="col-sm-4 control-label" for="DateFrom">
                                    <?php echo $this->lang->line('communityngo_DateFrom'); ?><!--Date From--></label>
                                <div class="col-sm-6">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="DateFrom"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="DateFrom" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group DateTo " style="padding:15px">
                                <label class="col-sm-4 control-label" for="DateTo">
                                    <?php echo $this->lang->line('communityngo_DateTo'); ?><!--Date To--></label>
                                <div class="col-sm-6">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="DateTo"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="DateTo" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group isPrimary" style="padding:15px">
                                <label class="col-sm-4 control-label" for="isPrimary">
                                    <?php echo $this->lang->line('communityngo_isPrimary'); ?><!--is primary--></label>
                                <div class="col-sm-6">
                                    <input type="checkbox" name="isPrimary" id="isPrimary" value="1"
                                           style="margin-top: 10px">
                                </div>
                            </div>

                            <div class="form-group isActive" style="padding:15px">
                                <label class="col-sm-4 control-label" for="isActive">
                                    <?php echo $this->lang->line('CommunityNgo_Is_Active'); ?><!--is active--></label>
                                <div class="col-sm-6">
                                    <input type="checkbox" name="isActive" id="isActive" value="1"
                                           style="margin-top: 10px">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-occ" name="hidden-id-occ" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-occ"
                            onclick="saveOccupation()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="update-btn-occ"
                            onclick="updateOccupation()">
                        <?php echo $this->lang->line('common_update'); ?><!--Update-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="vehicleStatus_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="vehicleStatus_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="vehicleStatus_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="vehicleAutoID">
                                    <?php echo $this->lang->line('communityngo_vehicle_details'); ?><!--vehicle--> <?php required_mark(); ?></label>

                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <?php echo form_dropdown('vehicleAutoID', $vehiMaster, '',
                                            'class="form-control select2" id="vehicleAutoID" '); ?>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button"
                                                            onclick="sendemail('mem_vehicle')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 6%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_vehicle_desc'); ?><!--Vehicle Description--></label>
                                <div class="col-sm-6">
                                    <input type="text" name="vehiDescription" value="" id="vehiDescription"
                                           class="form-control"
                                           placeholder="<?php echo $this->lang->line('communityngo_vehicle_desc'); ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 12%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_vehicle_status'); ?><!--Vehicle Status--></label>
                                <div class="form-group col-sm-6">
                                    <select id="vehiStatus" class="form-control select2"
                                            name="vehiStatus"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_vehicle_status'); ?>">
                                        <option value=""></option>
                                        <option value="0" selected>Own</option>
                                        <option value="1">Lease</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 18%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_Remarks'); ?><!--Remarks--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="vehiRemark"
                                              placeholder="<?php echo $this->lang->line('communityngo_Remarks'); ?>"
                                              name="vehiRemark" rows="2"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-vehi" name="hidden-id-vehi" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-vehi"
                            onclick="save_vehicleStatus()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="memHeplRStatus_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="memHeplRStatus_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="memHeplRStatus_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="memHelpType">
                                    <?php echo $this->lang->line('communityngo_memHelp_type'); ?><!--Help Requirement Type--> <?php required_mark(); ?></label>

                                <div class="form-group col-sm-6">
                                    <select id="memHelpType" class="form-control select2"
                                            name="memHelpType" onchange="get_helpType_del();"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_memHelp_type'); ?>">
                                        <option value=""></option>
                                        <option value="1">Government Help</option>
                                        <option value="2">Private Help</option>
                                        <option value="3">Consultancy</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="helpDelID">
                                    <?php echo $this->lang->line('communityngo_memHelp_details'); ?><!--Help Requirement del--> <?php required_mark(); ?></label>


                                <div class="form-group col-sm-6">
                                    <div class="input-group">
                                        <select name="helpDelID" class="form-control select2" id="helpDelID">
                                            <option value="" selected="selected">Select a Help Details</option>
                                        </select>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="email_btn"
                                                            disabled
                                                            onclick="sendemail('help_details')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group" style="margin-top: 6%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityngo_memHelp_desc'); ?><!--Help Description--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="hlprDescription"
                                              placeholder="<?php echo $this->lang->line('communityngo_memHelp_desc'); ?>"
                                              name="hlprDescription" rows="2"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-hlp" name="hidden-id-hlp" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-hlp"
                            onclick="save_memHeplRStatus()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="willingToHelp_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <?php echo form_open('', 'role="form" id="willingToHelp_form"'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="willingToHelp_modal-title"></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="helpCategoryID">
                                    <?php echo $this->lang->line('communityNgo_willing_to_help_category'); ?><!--Help category--> <?php required_mark(); ?></label>


                                <div class="form-group col-sm-6">
                                    <div class="input-group">
                                        <select name="helpCategoryID" class="form-control select2" id="helpCategoryID"  data-placeholder="Select a category">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($helpCategory)) {
                                                foreach ($helpCategory as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['helpCategoryID'] ?>"><?php echo $val['helpCategoryDes'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="email_btn_help"
                                                            onclick="sendemail('help_category')"
                                                            style="height: 27px; padding: 2px 10px;">
                                                        <i class="fa fa-envelope" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group" style="margin-top: 6%;">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('communityNgo_willing_to_help_comment'); ?><!--Comments--></label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="helpComments"
                                              placeholder="<?php echo $this->lang->line('communityNgo_willing_to_help_comment'); ?>"
                                              name="helpComments" rows="2"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="hidden-id-willingToHelp" name="hidden-id-willingToHelp" value="0">
                    <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-willingToHelp"
                            onclick="save_willingToHelp()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 35%">
            <form method="post" id="Send_Email_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="EmailHeader">Email</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="padding:5px">
                                    <label class="col-sm-12 control-label" for="sender_name">
                                        <?php echo $this->lang->line('communityngo_sender_name'); ?></label>
                                    <div class="col-sm-12">
                                        <input type="text" name="sender_name"
                                               value="" id="sender_name" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group" style="padding:5px">
                                    <label class="col-sm-12 control-label" for="email">
                                        Email From</label>
                                    <div class="col-sm-12">
                                        <input type="email" name="email" id="email" class="form-control"
                                               placeholder="example@example.com">
                                    </div>
                                </div>

                                <div class="form-group" style="padding:5px">
                                    <label class="col-sm-12 control-label" for="subject">
                                        <?php echo $this->lang->line('communityngo_subject'); ?></label>
                                    <div class="col-sm-12">
                                        <input type="text" name="subject" id="subject" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group" style="padding:5px">
                                    <label class="col-sm-12 control-label" for="message">
                                        <?php echo $this->lang->line('communityngo_sender_message'); ?></label>
                                    <div class="col-sm-12">
                                        <textarea type="text" name="message" id="message"
                                                  class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="send_request_email()">Send Email</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">

        var medium;
        var specialization;
        var helpDelIDs;

        $(document).ready(function () {
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            $('.select2').select2();

            Inputmask().mask(document.querySelectorAll("input"));
        });

        function open_add_modal() {
            var tabName = $('.other_details_li.active').attr('data-value');

            switch (tabName) {
                case 'languageTab':
                    add_language();
                    break;

                case 'certificationTab':
                    add_qualification();
                    break;

                case 'occupationTab':
                    add_occupation();
                    break;

                case 'healthStatus_Tab':
                    add_healthStatus();
                    break;

                case 'vehicleStatus_Tab':
                    add_vehicleStatus();
                    break;

                case 'memHeplRStatus_Tab':
                    add_memHeplRStatus();
                    break;

                case 'willingToHelp_Tab':
                    add_willingToHelp();
                    break;

                default:
                    add_language();
            }
        }

        function add_language() {
            $('#language_form').trigger("reset");
            $('#save-btn-lg').show();
            $('#language_modal-title').text('<?php echo $this->lang->line('communityngo_Language');?>');
            $('#language_modal').modal({backdrop: "static"});
        }

        function save_Language() {
            var postData = $('#language_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;

            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/save_Language'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('languageTab');
                    }

                    $('#language_modal').modal('hide');
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        function deleteLanguage(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/deleteLanguage'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_alldetails('languageTab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }


        function add_healthStatus() {

            $('#healthStatus_form').trigger("reset");
            $('#save-btn-hs').show();
            $('#healthStatus_modal-title').text('<?php echo $this->lang->line('communityngo_CPermanent_sickness');?>');
            $('#healthStatus_modal').modal({backdrop: "static"});
        }

        function save_healthStatus() {
            var postData = $('#healthStatus_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;

            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/save_healthStatus'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('healthStatus_Tab');
                    }

                    $('#healthStatus_modal').modal('hide');
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        function edit_healthStatus(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_healthStatus'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#healthStatus_form').trigger("reset");

                    $("#sickAutoID").val(data['sickAutoID']).change();
                    $('#startedFrom').val(data['startedFrom']);
                    $('#medicalCondition').val(data['medicalCondition']);
                    $('#monthlyExpenses').val(data['monthlyExpenses']);
                    $('#Sick_remark').val(data['Sick_remark']);

                    $('#hidden-id-HS').val(id);

                    $('.actionBtn').show();
                    $('#healthStatus_modal-title').text('<?php echo $this->lang->line('communityngo_health_edit');?>');
                    $('#healthStatus_modal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function delete_healthStatus(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/delete_healthStatus'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_alldetails('healthStatus_Tab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function add_vehicleStatus() {

            $('#vehicleStatus_form').trigger("reset");
            $('#save-btn-vehi').show();
            $('#vehicleStatus_modal-title').text('<?php echo $this->lang->line('communityngo_vehicle_details');?>');
            $('#vehicleStatus_modal').modal({backdrop: "static"});
        }

        function save_vehicleStatus() {
            var postData = $('#vehicleStatus_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;

            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/save_vehicleStatus'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('vehicleStatus_Tab');
                    }

                    $('#vehicleStatus_modal').modal('hide');
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        function edit_vehicleStatus(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_vehicleStatus'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#vehicleStatus_form').trigger("reset");

                    $("#vehicleAutoID").val(data['vehicleAutoID']).change();
                    $('#vehiDescription').val(data['vehiDescription']);
                    $("#vehiStatus").val(data['vehiStatus']).change();
                    $('#vehiRemark').val(data['vehiRemark']);

                    $('#hidden-id-vehi').val(id);

                    $('.actionBtn').show();
                    $('#vehicleStatus_modal-title').text('<?php echo $this->lang->line('communityngo_vehi_edit');?>');
                    $('#vehicleStatus_modal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function delete_vehicleStatus(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/delete_vehicleStatus'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_alldetails('vehicleStatus_Tab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function add_memHeplRStatus() {

            $('#memHeplRStatus_form').trigger("reset");
            $('#save-btn-hlp').show();
            $('#memHeplRStatus_modal-title').text('<?php echo $this->lang->line('communityngo_memHelp_require');?>');
            $('#memHeplRStatus_modal').modal({backdrop: "static"});
        }

        function get_helpType_del() {

            $('#email_btn').prop("disabled", false);

            var memHelpType = document.getElementById('memHelpType').value;


            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {memHelpType: memHelpType},
                url: "<?php echo site_url('CommunityNgo/fetch_helpType_delDropdown'); ?>",
                success: function (data) {
                    $('#helpDelID').html(data);
                    $('#helpDelID').val(helpDelIDs).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function save_memHeplRStatus() {
            var postData = $('#memHeplRStatus_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;

            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/save_memHeplRStatus'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('memHeplRStatus_Tab');
                    }

                    $('#memHeplRStatus_modal').modal('hide');

                    $("#memHelpType").val('').change();
                    helpDelIDs = null;
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        function edit_memHeplRStatus(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_memHeplRStatus'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#memHeplRStatus_form').trigger("reset");
                    if (data['helpRequireType'] == 'GOV') {
                        $("#memHelpType").val(1).change();
                    }
                    else if (data['helpRequireType'] == 'PVT') {
                        $("#memHelpType").val(2).change();
                    }

                    else if (data['helpRequireType'] == 'CONS') {
                        $("#memHelpType").val(3).change();
                    }

                    helpDelIDs = data['helpRequireID'];
                    //   $("#helpDelID").val(data['helpRequireID']).change();
                    $('#hlprDescription').val(data['hlprDescription']);

                    $('#hidden-id-hlp').val(id);

                    $('.actionBtn').show();
                    $('#memHeplRStatus_modal-title').text('<?php echo $this->lang->line('communityngo_helpr_edit');?>');
                    $('#memHeplRStatus_modal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function delete_memHeplRStatus(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/delete_memHeplRStatus'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_alldetails('memHeplRStatus_Tab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function add_occupation() {

            $('#schoolComID').val('').change();
            $('#schoolTypeID').val('').change();
            $("#gradeComID").val('').change();
            $("#MediumID").val('').change();
            $("#JobCategoryID").val('').change();
            $("#specializationID").val('').change();

            $('#occupation_form').trigger("reset");
            $('.actionBtn').hide();
            $('#save-btn-occ').show();
            $('#occupation_modal-title').text('<?php echo $this->lang->line('communityngo_Job');?>');
            $('#occupation_modal').modal({backdrop: "static"});

            $("#OccTypeID").val(1).change();
            $('.JobCategoryID').addClass('hide');
            $('.specializationID').addClass('hide');
            $('.jobDescription').addClass('hide');
            $('.WorkingPlace').addClass('hide');
            $('.isPrimary').addClass('hide');
            $('.School').removeClass('hide');
            $('.schoolTypeID').removeClass('hide');
            $('.gradeComID').removeClass('hide');
            $('.MediumID').removeClass('hide');
            $('.isActive').removeClass('hide');

        }

        function change_dropdown() {
            var dropdownvalue = $("select#OccTypeID option").filter(":selected").val();

            switch (dropdownvalue) {
                case '1':
                    $('.JobCategoryID').addClass('hide');
                    $('.specializationID').addClass('hide');
                    $('.jobDescription').addClass('hide');
                    $('.WorkingPlace').addClass('hide');
                    $('.isPrimary').addClass('hide');
                    $('.School').removeClass('hide');
                    $('.schoolTypeID').removeClass('hide');
                    $('.gradeComID').removeClass('hide');
                    $('.MediumID').removeClass('hide');
                    $('.isActive').removeClass('hide');
                    break;

                default:
                    $('.JobCategoryID').removeClass('hide');
                    $('.specializationID').removeClass('hide');
                    $('.jobDescription').removeClass('hide');
                    $('.WorkingPlace').removeClass('hide');
                    $('.isPrimary').removeClass('hide');
                    $('.School').addClass('hide');
                    $('.schoolTypeID').addClass('hide');
                    $('.gradeComID').addClass('hide');
                    $('.MediumID').addClass('hide');
                    $('.isActive').addClass('hide');
            }
        }

        function saveOccupation() {
            var postData = $('#occupation_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;
            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveOccupation'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('occupationTab');
                    }
                    $('#occupation_modal').modal('hide');

                    medium = null;
                    specialization = null;
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        $("#isPrimary").click(function () {
            if (this.checked) {

                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('CommunityNgo/check_primaryOcc_exist'); ?>',
                    data: {Com_MasterID: Com_MasterID},
                    dataType: 'JSON',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == 'Exist') {
                            myAlert('e', 'Another occupation has already been marked as primary. So can not mark this as primary.');
                            $('#isPrimary').prop('checked', false);
                        } else if (data == 'Not_exist') {
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    }
                });

            } else {
            }
        });

        function editOccupation(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/editOccupation'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#occupation_form').trigger("reset");

                    $("#OccTypeID").val(data['OccTypeID']).change();
                    $('#Address').val(data['Address']);

                    change_dropdown();

                    if (data['OccTypeID'] == 1) {

                        $('#schoolComID').val(data['schoolComID']).change();
                        $('#schoolTypeID').val(data['schoolTypeID']).change();
                        $("#gradeComID").val(data['gradeComID']).change();
                        //  $("#MediumID").val(data['LanguageID']).change();
                        medium = data['LanguageID'];

                        var isActive = data['isActive'];
                        $('#isActive').prop('checked', (isActive == 1));


                    } else {
                        $("#JobCategoryID").val(data['JobCategoryID']).change();
                        specialization = data['specializationID'];
                        $('#jobDescription').val(data['jobDescription']);
                        $('#WorkingPlace').val(data['WorkingPlace']);
                        $('#DateFrom').val(data['DateFrom']);
                        $('#DateTo').val(data['DateTo']);

                        var isPrimary = data['isPrimary'];
                        $('#isPrimary').prop('checked', (isPrimary == 1));

                    }

                    $('#hidden-id-occ').val(id);

                    $('.actionBtn').hide();
                    $('#update-btn-occ').show();
                    $('#occupation_modal-title').text('<?php echo $this->lang->line('communityngo_Job_edit');?>');
                    $('#occupation_modal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function get_sch_medium(schoolComID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {schoolComID: schoolComID},
                url: "<?php echo site_url('CommunityNgo/fetch_medium_based_school'); ?>",
                success: function (data) {
                    $('#MediumID').html(data);
                    $('#MediumID').val(medium).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function get_sch_address(schoolComID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {schoolComID: schoolComID},
                url: "<?php echo site_url('CommunityNgo/fetch_address_based_school'); ?>",
                success: function (data) {
                    $('#Address').val(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function get_job_specialization(JobCategoryID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {JobCategoryID: JobCategoryID},
                url: "<?php echo site_url('CommunityNgo/fetch_job_based_specialization'); ?>",
                success: function (data) {
                    $('#specializationID').html(data);
                    $('#specializationID').val(specialization).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_Occupation(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/deleteOccupation'); ?>",
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
                                fetch_alldetails('occupationTab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function updateOccupation() {

            var postData = $('#occupation_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;
            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/updateOccupation'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#occupation_modal').modal('hide');
                        fetch_alldetails('occupationTab');
                        medium = null;
                        specialization = null;
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }


        function add_qualification() {

            $("#DegreeID").val('').change();
            $("#UniversityID").val('').change();
            $("#gradeComID").val('').change();

            $('#qualification_form').trigger("reset");
            $('.actionBtn').hide();
            $('#save-btn').show();
            $('#addModal').modal({backdrop: "static"});
            $('#qualificationModal-title').text('<?php echo $this->lang->line('communityngo_Qualification');?>');
        }

        function display_grade_for_school() {

            var dropdownvalue = $("select#DegreeID option").filter(":selected").val();

            switch (dropdownvalue) {
                case '165':
                    $('.university_div').addClass('hide');
                    $('.grade_div').removeClass('hide');
                    break;

                default:
                    $('.grade_div').addClass('hide');
                    $('.university_div').removeClass('hide');
            }
        }

        function saveQualification() {
            var postData = $('#qualification_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;
            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveQualification'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {

                        fetch_alldetails('certificationTab');
                    }
                    $('#addModal').modal('hide');
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }

        function editQualification(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/editQualification'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#qualification_form').trigger("reset");

                    $("#DegreeID").val(data['DegreeID']).change();
                    $('#Remarks').val(data['Remarks']);
                    $("#UniversityID").val(data['UniversityID']).change();
                    $("#gradeComID").val(data['gradeComID']).change();
                    $('#Year').val(data['Year']);
                    var CurrentlyReading = data['CurrentlyReading'];
                    $('#CurrentlyReading').prop('checked', (CurrentlyReading == 1));

                    $('#hidden-id').val(id);

                    $('.actionBtn').hide();
                    $('#update-btn').show();
                    $('#qualificationModal-title').text('<?php echo $this->lang->line('communityngo_Qualification_edit');?>');
                    $('#addModal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function delete_Qualification(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/deleteQualification'); ?>",
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
                                fetch_alldetails('certificationTab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

        function updateQualification() {
            var postData = $('#qualification_form').serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/updateQualification'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $('#addModal').modal('hide');
                        fetch_alldetails('certificationTab');
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            })
        }


        function fetch_alldetails(tab) {

            if (Com_MasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'Com_MasterID': Com_MasterID},
                    url: '<?php echo site_url("CommunityNgo/load_memberOtherDetails_View"); ?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#details').html(data);

                        if (tab == 'certificationTab') {
                            $('#quaTab').tab('show');
                            $('[href=#certificationTab]').addClass('active');
                            $('.certification').addClass('active');
                        } else if (tab == 'occupationTab') {
                            $('#occTab').tab('show');
                            $('[href=#occupationTab]').addClass('active');
                            $('.occupation').addClass('active');
                        } else if (tab == 'healthStatus_Tab') {
                            $('#hsTab').tab('show');
                            $('[href=#healthStatus_Tab]').addClass('active');
                            $('.healthStatus').addClass('active');
                        }
                        else if (tab == 'vehicleStatus_Tab') {
                            $('#vehTab').tab('show');
                            $('[href=#vehicleStatus_Tab]').addClass('active');
                            $('.vehicleStatus').addClass('active');
                        }
                        else if (tab == 'memHeplRStatus_Tab') {
                            $('#helpRTab').tab('show');
                            $('[href=#memHeplRStatus_Tab]').addClass('active');
                            $('.memHeplRStatus').addClass('active');
                        }
                        else if (tab == 'willingToHelp_Tab') {
                            $('#willingToHeplTab').tab('show');
                            $('[href=#willingToHelp_Tab]').addClass('active');
                            $('.willingToHelp').addClass('active');
                        }
                        else {
                            $('#lanTab').tab('show');
                            $('[href=#languageTab]').addClass('active');
                            $('.language').addClass('active');
                        }

                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }

        function add_other_attachment(other_documentSystemCode, other_document_name, other_documentID) {
            $('#other_attachmentDescription').val('');
            $('#other_documentSystemCode').val(other_documentSystemCode);
            $('#other_document_name').val(other_document_name);
            $('#other_documentID').val(other_documentID);
            $('#other_remove_id').click();
            if (other_documentSystemCode) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("CommunityNgo/fetch_other_attachments"); ?>',
                    dataType: 'json',
                    data: {'other_documentSystemCode': other_documentSystemCode, 'other_documentID': other_documentID},
                    success: function (data) {
                        $('#other_attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + other_document_name + "");
                        $('#other_attachment_modal_body').empty();
                        $('#other_attachment_modal_body').append('' + data + '');
                        $("#other_attachment_modal").modal({backdrop: "static", keyboard: true});
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#ajax_nav_container').html(xhr.responseText);
                    }
                });
            }
        }

        function other_attachment_upload() {
            var formData = new FormData($("#other_attachment_upload_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('CommunityNgo/upload_other_attachments'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                        add_other_attachment($('#other_documentSystemCode').val(), $('#other_document_name').val(), $('#other_documentID').val());
                        $('#other_remove_id').click();
                        $('#other_attachmentDescription').val('');
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function delete_other_attachments(id, fileName) {

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': id, 'myFileName': fileName},
                        url: "<?php echo site_url('CommunityNgo/delete_member_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                                /*Deleted Successfully*/
                                $('#' + id).hide();
                            } else {
                                myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                                /*Deletion Failed*/
                            }
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        // send mail

        function sendemail(type) {

            switch (type) {
                case 'sickness':
                    var subject = 'Add new sickness';
                    break;

                case 'qualification':
                    subject = 'Add new qualification';
                    break;

                case 'institute':
                    subject = 'Add new institute';
                    break;

                case 'school':
                    subject = 'Add new school';
                    break;

                case 'job_category':
                    subject = 'Add job new category';
                    break;

                case 'job_specialization':
                    subject = 'Add job new specialization';
                    break;

                case 'mem_vehicle':
                    subject = 'Add new vehicle';
                    break;

                case 'help_category':
                    subject = 'Add new help category';
                    break;

                case 'help_details':

                    var dropdownvalue = $("select#memHelpType option").filter(":selected").val();

                    if (dropdownvalue == '1') {
                        var help_type = 'Government Help';
                    } else if (dropdownvalue == 2) {
                        help_type = 'Private Help';
                    } else if (dropdownvalue == 3) {
                        help_type = 'Consultancy';
                    }

                    subject = 'Add new help details under ' + help_type;
                    break;

                default:
                    subject = 'Add new sickness';
            }


            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {},
                url: "<?php echo site_url('CommunityNgo/loademail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#Email_modal").modal();
                    $('#Send_Email_form').trigger("reset");

                    if (!jQuery.isEmptyObject(data)) {
                        $('#sender_name').val(data['Ename1']);
                        $('#email').val(data['EEmail']);
                        $('#subject').val(subject);
                    }

                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }

        function send_request_email() {
            var form_data = $("#Send_Email_form").serialize();
            swal({
                    title: "Are You Sure?",
                    text: "You Want To Send This Mail",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: form_data,
                        url: "<?php echo site_url('CommunityNgo/send_request_email'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Email_modal").modal('hide');

                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function add_willingToHelp() {

            $('#willingToHelp_form').trigger("reset");
            $('#save-btn-willingToHelp').show();
            $('#willingToHelp_modal-title').text('<?php echo $this->lang->line('communityNgo_willing_to_help');?>');
            $('#willingToHelp_modal').modal({backdrop: "static"});
        }

        function save_willingToHelp() {

            var postData = $('#willingToHelp_form').serializeArray();
            var Com_MasterID = <?php echo json_encode(trim($this->input->post('Com_MasterID'))); ?>;

            postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/save_willingToHelp'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_alldetails('willingToHelp_Tab');
                    }

                    $('#willingToHelp_modal').modal('hide');

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
            })

        }

        function edit_willingToHelp(id) {

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_willingToHelp'); ?>',
                data: {id: id},
                dataType: 'JSON',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#willingToHelp_form').trigger("reset");

                    $("#helpCategoryID").val(data['helpCategoryID']).change();
                    $('#helpComments').val(data['helpComments']);

                    $('#hidden-id-willingToHelp').val(id);

                    $('.actionBtn').show();
                    $('#willingToHelp_modal-title').text('<?php echo $this->lang->line('communityNgo_willing_to_help_edit');?>');
                    $('#willingToHelp_modal').modal({backdrop: "static"});
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }

        function delete_willingToHelp(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('CommunityNgo/delete_willingToHelp'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_alldetails('willingToHelp_Tab');
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }
    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 1/25/2018
 * Time: 9:21 AM
 */