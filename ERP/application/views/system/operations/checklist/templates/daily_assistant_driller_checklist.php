<?php
$result1= array_slice($details,0,15);
$result2= array_slice($details,15);
//print_r($result1);
//print_r($result2);
?>
<div class="row">
    <div class="col-md-12" style="background: #fff;padding: 25px;">
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table>
                        <colgroup>
                            <col style="width: 20%; height:40px" />
                            <col style="width: 60%; height:40px" />
                            <col style="width: 20%; height:40px" />
                        </colgroup>
                        <tr>
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="60%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 18px">
                                            DAILY ASSISTANT DRILLER CHECKLIST
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                            RAY-OG-FRM-413 (4.0)
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important">
                                &nbsp;
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
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Rig #</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Well #</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Date</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>
        <div class="row">
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                        <?php
                        $i=1;
                        foreach ($result1 as $val1) {
                            if(!empty($result1)){   ?>
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
                            <td><?php echo $val1['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                        </tr>
                        <?php 
                        $i++;
                         }
                        } ?>
                        
                    </table>
                </div>
            </div>
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                    
                        <?php
                        $i=16;
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
                            <td><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                        </tr>
                        <?php $i++;
                            }
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
                        <tr>
                            <td>Assistant Driller’s Name </td>
                            <td width="20%"><input type="text" class="input_style_1" name="assit_driller_name" id="assit_driller_name" /></td>
                            <td>Assistant Driller’s Signature </td>
                            <td width="20%"><input type="text" class="input_style_1" name="assit_driller_signature" id="assit_driller_signature" /></td>
                        </tr>
                        <tr>
                            <td>Rig Manager / Toolpusher Name </td>
                            <td width="20%"><input type="text" class="input_style_1" name="rig_manager_name" id="rig_manager_name" /></td>
                            <td>Rig Manager / Toolpusher Signature </td>
                            <td width="20%"><input type="text" class="input_style_1" name="rig_manager_signature" id="rig_manager_signature" /></td>
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
