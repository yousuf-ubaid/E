<?php
// $result1= array_slice($details,0,6);
// $result2= array_slice($details,6,6);


    $total_hours = getDayWiseDifference($report_header['dateFrom'],$report_header['dateTo'],'hours_minute_num');

    $current_date = date('Y-m-d');
    $confirmedYN = $report_header['confirmedYN'];

    $readonly = '';
    if($confirmedYN == 1){
        $readonly = 'readonly';
    }

    $total_npt_activity_hours = 0;

    foreach($report_detail as $detail) {
        if($detail['isNpt'] == 1){
            $total_npt_activity_hours += $detail['hours'];
        }
    }

    // $total_activity_hours = get_add_times($total_activity_hours_arr);
?>

<input type="hidden" name="job_id" id="job_id" value="<?php echo $job_id ?>" />
<input type="hidden" name="report_id" id="report_id" value="<?php echo $report_id ?>" />

<div class="row">
    <div class="col-md-12" style="background: #fff;padding: 25px;">
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                <table>
                        <colgroup>
                            <col style="width: 25%; height:40px" />
                            <col style="width: 50%; height:40px" />
                            <col style="width: 25%; height:40px" />
                        </colgroup>
                        <tr>
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="50%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 18px">
                                        Work Over Rig Daily Report			
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important;text-align:right">
                                
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-6">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                            <col style="width: 12%; height:25px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Well No.</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Rig-up Date</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Rig Down Date</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Rig Days</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Job Type</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Contractor</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Rig</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Date</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $job_master['well_number']; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">14-Apr-24</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">-45396.23</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $job_master['job_type']; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">AWS</td>                            
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">255</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">15-Apr-24</td>
                        </tr>
                        
                    </table>
                </div>
            </div>
            
        </div>

        <table>
            <tr><td height="5px" style="height:5px; padding:0">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 40%; height:25px" />
                            <col style="width: 60%; height:25px" />
                        </colgroup>
                        <tr>
                            <th colspan="2" style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Ticket Approvals	</th>
                        </tr>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">BAPCO upstream</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Contractor</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Lead Supervisor- Ali janahi</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Superintendent- Manish</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Supervisor- Abdulla</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Supervisor:  Pintu /  Magdy</td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> Supervisor:- S.faisal  </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">HSE Advisor- gerwer / Khalid</td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="5px" style="height:5px; padding:0">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 40%; height:25px" />
                            <col style="width: 60%; height:25px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25">Safety Incident</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 12%;" valign="middle" height="25"></th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Time of Incident</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Time BAPCO Notified</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Name of BAPCO Notified </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Days from last Incident</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="5px" style="height:5px; padding:0">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 33%;" />
                            <col style="width: 33%;" />
                            <col style="width: 33%;" />
                        </colgroup>                       
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Code</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Rate Type</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Hours</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Rate </th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Total </th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">01</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Operating with crew</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">02</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Operating without crew</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">S1</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Standby with crew</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">S2</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Standby without crew</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">M</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Moving</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Z</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">Zero</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">0:00 </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="20">Total</td>
                            <td style="text-align:center;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="20">0:00</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="20"></td>
                            <td style="text-align:center;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="20">0:00 </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="5px" style="height:5px; padding:0">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 33%;" />
                            <col style="width: 33%;" />
                            <col style="width: 33%;" />
                        </colgroup>                       
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Fluid</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Vol</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;" valign="middle" height="25">Total Cost</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">8.7 ppg</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25">350 bbl</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"> </td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="5px" style="height:5px; padding:0">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        
                        <tr>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">from<br><?php echo $dateFrom; ?></th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">to<br><?php echo $dateTo; ?></th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Hrs (hr)</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Op Code</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Op Sub</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Op Type<br>(P or PT)</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Operation Details</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">01</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">02</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">S1</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">S2</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">M</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">Z</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:9px;font-weight:bold">From (HIDE)</th>
                        </tr>
                    <?php 
                    foreach($operation_details as $val){ 
                        $t1 = strtotime($val['dateFrom']);
                        $t2 = strtotime($val['dateTo']);
                        $diff = $t2 - $t1;
                        $hours = $diff / ( 60 * 60 );    
                        
                        $new_date_From = date('Y-m-d', strtotime($val['dateFrom']));
                        $new_date_To = date('Y-m-d', strtotime($val['dateTo']));
                    ?>
                        <tr>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $new_date_From; ?></td>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $new_date_To; ?></td>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $hours; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">P</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['description']; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0:00</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0:00</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"></td>
                        </tr>
                        
                    <?php  } ?>    
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <!-- <div class="row">
            <div class="col-12 col-md-12">  
                <button type="button" class="btn btn-primary-new size-sm float-right" onclick="marked_as_confirm()"><i class="fa fa-save"></i> Confirm</button>
            </div>
        </div> -->
    </div>
</div>


<script>

   

    function change_header_value(ev,field_name,detail_id = null){

        var changed_val = $(ev).val();
        var job_id = $('#job_id').val();
        var report_id = $('#report_id').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:  {'value': changed_val,'job_id':job_id,'report_id':report_id,'field_name':field_name,'detail_id':detail_id},
            url: "<?php echo site_url('Jobs/update_daily_report_values'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function marked_as_confirm(){

        var job_id = $('#job_id').val();
        var report_id = $('#report_id').val();

        swal({
            title: "Are you sure?",
            text: "You want to confirm this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'job_id': job_id, 'report_id': report_id,'value':1,'field_name':'confirmedYN'},
                url: "<?php echo site_url('Jobs/update_daily_report_values'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                  
                },
                error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });

    }

</script>