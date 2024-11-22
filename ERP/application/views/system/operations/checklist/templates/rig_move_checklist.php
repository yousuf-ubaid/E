<?php
$result1= array_slice($details,0,6);
$result2= array_slice($details,6,6);
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
                                            <?php echo $title[0]['name']; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                        <?php echo $title[0]['document_reference_code']; ?>
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
                            <col style="width: 15%; height:40px" />
                            <col style="width: 18%; height:40px" />
                            <col style="width: 15%; height:40px" />
                            <col style="width: 18%; height:40px" />
                            <col style="width: 15%; height:40px" />
                            <col style="width: 18%; height:40px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Unit Number:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Current Well No:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">New Well No:</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Date of Move:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Time of Move:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Distance of Move (in KM):</td>
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
                            <th style="text-align:center;background:#eee;">No.</th>
                            <th style="background:#eee;">Description</th>
                            <th style="text-align:center;background:#eee;">Yes</th>
                            <th style="text-align:center;background:#eee;">No</th>
                            <th style="text-align:center;background:#eee;">N/A</th>
                            <th style="text-align:center;background:#eee;">Comments</th>
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
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comments" id="comments"/></td>
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
                            <th style="text-align:center;background:#eee;">No.</th>
                            <th style="background:#eee;">Description</th>
                            <th style="text-align:center;background:#eee;">Yes</th>
                            <th style="text-align:center;background:#eee;">No</th>
                            <th style="text-align:center;background:#eee;">N/A</th>
                            <th style="text-align:center;background:#eee;">Comments</th>
                        </tr>
                        
                        <?php
                        $i=7;
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
                            <td><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comments" id="comments"/></td>
                        </tr>
                        <?php 
                        $i++;
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
                    <table class="table-border-none">
                        <colgroup>
                            <col style="width: 65%; height:20px" />
                            <col style="width: 35%; height:20px" />
                        </colgroup>
                        <tr>
                            <td style="font-size:14px !important">Any other additional information or requirement not stated above?<br>
                                 specify / write details below:</td>
                            <td style="font-size:14px !important">YES NO</td>
                        </tr>
                        <tr>                            
                            <td style="font-size:12px" colspan="2"><input type="text" class="input_style_1" name="comments" id="comments"/></td>
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
                    <table class="table table-bordered-1">
                        <tr>
                            <td>Name of person completing the checklist: </td>
                            <td width="20%"><input type="text" class="input_style_1" name="assit_driller_name" id="assit_driller_name" /></td>
                            <td>Position of person completing the checklist:</td>
                            <td width="20%"><input type="text" class="input_style_1" name="assit_driller_signature" id="assit_driller_signature" /></td>
                        </tr>
                        <tr>
                            <td>Signature of person completing the checklist:</td>
                            <td width="20%"><input type="text" class="input_style_1" name="rig_manager_name" id="rig_manager_name" /></td>
                            <td>Date and time: </td>
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
