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
                                        Al Mansoori Workover Services W.L.L.	
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
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 33%; height:25px" />
                            <col style="width: 33%; height:25px" />
                            <col style="width: 33%; height:25px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px !important;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Customer Name/Number:</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">County/Parish</th>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">State</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $report_header_wll['customerName'] ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $report_header_wll['customerCountry'] ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php //echo $job_master['hot_permit_number'] ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Contact:</th>
                            <th colspan="2" style="text-align:center;background:#eee;font-size:10px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Service Location</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $report_header_wll['customerTelephone'] ?></td>
                            <td colspan="2" style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $job_master['well_number'] ?></td>
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
                            <col style="width: 50%; height:25px" />
                            <col style="width: 25%; height:25px" />
                            <col style="width: 25%; height:25px" />
                        </colgroup>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 50%;" valign="middle" height="25">Preventive Maintenance</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 25%;" valign="middle" height="25">Yard Number: </th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 25%;" valign="middle" height="25">Unit/Asset Number:	</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php echo $job_master['job_obj_summary'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php //echo $job_master['iso_certificate'] ?></td>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"><?php //echo $job_master['hot_permit_number'] ?></td>
                        </tr>
                        <tr>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Job Class:</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Hub Meter Reading:	</th>
                            <th style="text-align:center;background:#eee;font-size:12px;font-weight:bold;border: 1px solid #dee2e6;width: 33%;" valign="middle" height="25">Ending Hour Meter Reading:	</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
                            <td colspan="2" style="text-align:center;font-size:12px;font-weight:400;border: 1px solid #dee2e6;" valign="middle" height="25"></td>
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
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Work Ticket Description</th>
                        </tr>
                    
                        <tr>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;min-height:35px">&nbsp; <?php echo "Not available" ?></td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr><th colspan="10" style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">PAYROLL</th></tr>
                        <tr>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Class</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Employee ID No.</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Employee Name/Signature	</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Start</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">End</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Billable<br>Hours</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Billable<br>Travel</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Down<br>Hours</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Other</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Total Hours</th>
                        </tr>
                    <?php 
                    foreach($credetails_list as $val){ ?>
                        <tr>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['designation']; ?></td>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['ECode']; ?></td>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['name']; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                        </tr>
                    <?php } ?>    
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">                       
                        <tr>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Asset Number</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Service Code and Description</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">QTY</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">UOM</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Price</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Disc%</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Rate</th>
                            <th style="text-align:center;background:#eee;border-top: 0 !important;font-size:10px;font-weight:bold">Total</th>
                        </tr>
                    <?php 
                    foreach($asset_list as $val){ ?>
                        <tr>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['assetCode']; ?></td>
                            <td style="text-align:left;font-size:10px;font-weight:400;border: 1px solid #dee2e6;"><?php echo $val['assetName']; ?></td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                            <td style="text-align:center;font-size:10px;font-weight:400;border: 1px solid #dee2e6;">0</td>
                        </tr>
                    <?php } ?>    
                    </table>
                </div>
            </div>
        </div>

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