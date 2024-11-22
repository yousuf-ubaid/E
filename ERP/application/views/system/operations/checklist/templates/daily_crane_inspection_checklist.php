<?php
$result1= array_slice($details,0,7);
$result2= array_slice($details,7,7);
$result3= array_slice($details,14,7);

?>
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
                                        RAY INTERNATIONAL OIL & GAS
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 15px">
                                        Daily Crane Inspection Checklist
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="25%" valign="middle" height="40">
                                <table>                                    
                                    <tr>
                                        <th style="text-align:right;font-size: 12px">
                                        RAY-OG-FRM-412(1.0)
                                        </th>
                                    </tr>
                                </table>        
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
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Date:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Crane Reg No #:</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Crane Operator Name</td>
                            <td valign="middle" height="40" colspan="3"></td>
                        </tr>   
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:35px;font-size:14px !important">Check all items as indicated. Inspect and indicate as Pass = P; Fail = F; or Not Applicable = N/A</td></tr>
        </table>

        <div class="row">
            <div class="col-md-4" style="padding-right: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                    <?php
                        foreach ($result1 as $val1) {
                            if(!empty($result1)){   ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val1['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="https://erp.rbdemo.live/images/icons/ico-2.png" width="50"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="finding1" id="flexRadioDefault2"/></td>
                        </tr>
                    <?php }
                        } ?>    
                        
                    </table>
                </div>
            </div>    
            <div class="col-md-4" style="padding-right: 5px;padding-left: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                        <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="https://erp.rbdemo.live/images/icons/ico-2.png" width="50"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="finding1" id="flexRadioDefault2"/></td>
                        </tr>
                    <?php }
                        } ?>   
                        
                        
                    </table>
                </div>
            </div>  
            <div class="col-md-4" style="padding-left: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                        <?php
                        foreach ($result3 as $val3) {
                            if(!empty($result3)){   ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val3['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="https://erp.rbdemo.live/images/icons/ico-2.png" width="50"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="finding1" id="flexRadioDefault2"/></td>
                        </tr>
                    <?php }
                        } ?>   
                        
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
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <tr>
                            <th colspan="4" valign="middle" height="40">Comments:</th>
                        </tr>
                        <tr>
                            <td colspan="4" valign="middle" height="40"><input type="text" class="input_style_1" name="comments" id="comments" /></td>
                        </tr>
                        <tr>
                            <td valign="bottom" height="40">Crane Operator signature:</td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td valign="bottom" height="40">Rig Manager or Supervisor Signature: </td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="signature" id="signature" /></td>
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
                <button type="button" class="btn btn-primary-new size-sm float-right"><i class="fa fa-save"></i> Submit</button>
            </div>
        </div>

    </div>
</div>
