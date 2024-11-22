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
                                        Occidental Mukhaizna
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 15px">
                                        Daily Job Report
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important;text-align:right">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
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
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">Report#</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:16%">Date</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:12%">Well#  </th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:10%">Rig #</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:12%">Well Type</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width:40%">Objective</th>
                        </tr>
                    
                        <tr>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;">1</td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo date('F d, Y', strtotime($report_header['dateFrom']) ); ?>	</td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $job_master['well_name'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $job_master['rig_hoist_name'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $job_master['well_type_op'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $job_master['job_obj_summary'] ?></td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 33%; height:40px" />
                            <col style="width: 33%; height:40px" />
                            <col style="width: 33%; height:40px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="40">PTW Number</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="40">Isolation Certification Number	</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="40">Hot Work Permit Number</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="40"><?php echo $job_master['ptw_number'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="40"><?php echo $job_master['iso_certificate'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="40"><?php echo $job_master['hot_permit_number'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">Today's Hrs</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">Total Hours</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">Time to Complete	</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">Current Operation</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">Muster Area</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;font-weight:bold">NPT last 24 hrs	</th>
                        </tr>
                    
                        <tr>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $total_hours ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $total_hours_job ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $report_header['current_operation'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $job_master['muster_area'] ?>	</td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $total_npt_activity_hours ?> </td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <colgroup>
                            <col style="width: 10%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 70%; height:40px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;width: 15%;">From</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;width: 15%;">To</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;width: 15%;">Time  </th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:12px;width: 55%;">Details</th>
                        </tr>

                        <?php foreach($report_detail as $detail) {
                            $total_activity_hours = $total_activity_hours + $detail['time'];
                            ?>
                            
                            <tr>
                                <td style="text-align:center;font-size:12px"><?php echo $detail['dateFrom'] ?></td>
                                <td style="text-align:center;font-size:12px"><?php echo $detail['dateTo'] ?></td>
                                <td style="text-align:center;font-size:12px"><?php echo $detail['time'] ?></td>
                                <td style="text-align:center;font-size:12px"><?php echo $detail['supervisor_text'] ?></td>
                                <!-- <td style="text-align:left;font-size:12px"><input type="text" class="input_style_1" name="supervisor_text" id="supervisor_text" <?php echo $readonly ?> onchange="change_header_value(this, 'supervisor_text','<?php echo $detail['id'] ?>')" value="<?php echo $detail['supervisor_text'] ?>" /></td> -->
                            </tr>
                        
                        <?php } ?>
                       
                        <tr>
                            <td colspan="3" style="text-align:center;font-size:12px;border: 1px solid #dee2e6;">24 hours summary:</td>
                            <td style="text-align:left;font-size:12px;border: 1px solid #dee2e6;"> <?php echo $report_header['hour_summary'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:center;font-size:12px;border: 1px solid #dee2e6;">Next 24 hours summary:</td>
                            <td style="text-align:left;font-size:12px;border: 1px solid #dee2e6;"><?php echo $report_header['next_hour_summary'] ?>	</td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list">
                        <colgroup>
                            <col style="width: 37%; height:40px" />
                            <col style="width: 13%; height:40px" />
                            <col style="width: 37%; height:40px" />
                            <col style="width: 13%; height:40px" />
                        </colgroup>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6" valign="middle" height="40">Number of What ifs:</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0" valign="middle" height="40"><?php echo $report_header['num_ifs'] ?></td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-left:0" valign="middle" height="40">Last H2S Drill: </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['last_h2s_drill']) ? $report_header['last_h2s_drill'] : $current_date )) ?>	</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Negative:	</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo $report_header['negative'] ?> </td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Last Fire Drill: </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['last_free_drill']) ? $report_header['last_free_drill'] : $current_date )) ?>	</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Positive:	</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo $report_header['positive'] ?></td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Last BOP Drill:  </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['bop_drill']) ? $report_header['bop_drill']  : $current_date)) ?>	</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Total days without LTI: Rig Start 19-07-12	</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"> 344</td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Last Man Down Drill: </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['man_down_drill']) ? $report_header['man_down_drill']: $current_date )) ?>	</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Last weekly safety meeting:	</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['safety_meeting']) ? $report_header['safety_meeting']: $current_date )) ?> </td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Last slip & cut Drill line:  </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo date('F d, Y',strtotime(($report_header['slip_cut_drill'])  ? $report_header['slip_cut_drill'] : $current_date )) ?>	</td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">Lifting color code: </td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo $report_header['lifting_color_code'] ?> </td>
                            <td style="text-align:left;font-weight:600;font-size:12px;border: 1px solid #dee2e6;border-top:0" valign="middle" height="40">PRV set (PSI) @: 26/10/2021</td>
                            <td style="text-align:center;font-size:12px;border: 1px solid #dee2e6;border-left:0;border-top:0" valign="middle" height="40"><?php echo $report_header['prv_number'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:15px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 80%; height:40px" />
                            <col style="width: 20%; height:40px" />
                        </colgroup>
                        <tr>
                            <td style="width: 80%;font-size:12px !important;border: 1px solid #dee2e6;">Reported to Rig Equipment Lead and/or Superintendent : Yes / No</td>
                            <td style="width: 20%;font-size:12px !important;border: 1px solid #dee2e6;">
                                <?php echo ($report_header['report_yes_no'] == 'yes') ? 'Yes' : 'No' ?>
                            </td>
                        </tr>                        
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:15px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <tbody>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">OXY WSM: Day</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['oxy_day'] ?>   </td>                        
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Rig Manager:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['rig_manager'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Night Tool pusher:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['n_tool_pusher'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Day Driller:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['day_driller'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Night Driller:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['night_driller'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Crew (A,B,C or D):</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['crew'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Shortage of Day crew:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['shortage_day_crew'] ?></td>                            
                        </tr>
                        <tr>
                            <td width="40%" style="font-size:12px;border: 1px solid #dee2e6;">Shortage of Night crew:</td>
                            <td width="60%" style="font-size:12px;border: 1px solid #dee2e6;border-left:0"><?php echo $report_header['shortage_night_crew'] ?></td>                            
                        </tr>
                        </tbody>
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