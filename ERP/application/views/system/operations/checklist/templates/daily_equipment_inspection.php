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
                            <col style="width: 15%; height:20px">
                            <col style="width: 15%; height:20px">
                            <col style="width: 20%; height:20px">
                            <col style="width: 15%; height:20px">
                            <col style="width: 20%; height:20px">
                            <col style="width: 15%; height:20px">
                        </colgroup>
                        <tbody>
                        <tr>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Date:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Rig:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Rig Supervisor:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Rig Operator:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Asst. Rig Operator:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Derrick Hand:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Floor Hands:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">And:</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;">Assistant Floor Hand:	</td>
                            <td valign="middle" height="20" style="padding-bottom: 0;padding-top: 0;"></td>
                        </tr>
                    </tbody></table>
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
                        <colgroup>
                            <col style="width: 70%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;">1- Rig Carrier And Rig Engine( Rig Operator) </th>  
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Pass</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Fail</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">N/A</th>   
                            </tr>
                        
                        <?php
                        $i=1;
                        foreach ($details_cat_one as $val) {
                            if(!empty($details_cat_one)){   ?>                
                        <tr>         
                            <td><?php echo $val['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3" disabled=""></td>
                            
                        </tr>     
                        <?php
                            $i++;
                            } else{
                                ?>
                                <tr>
                                    <td>No Records Found</td>
                                </tr>
                                <?php
                            }
                        }  ?>         
                    </tbody></table>
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
                        <colgroup>
                            <col style="width: 70%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;">2- Mud Pump #_________(Derrick Hand) </th>  
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Pass</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Fail</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">N/A</th>   
                            </tr>
                        
                        <?php
                        $i=1;
                        foreach ($details_cat_two as $val) {
                            if(!empty($details_cat_two)){   ?>                
                        <tr>         
                            <td><?php echo $val['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3" disabled=""></td>
                            
                        </tr>     
                        <?php
                            $i++;
                            } else{
                                ?>
                                <tr>
                                    <td>No Records Found</td>
                                </tr>
                                <?php
                            }
                        }  ?>         
                    </tbody></table>
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
                        <colgroup>
                            <col style="width: 70%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                            <col style="width: 10%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;">3- Generator / light Towers (Floor Hand)</th>  
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Pass</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Fail</th>
                                <th style="text-align:center;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">N/A</th>   
                            </tr>
                        
                        <?php
                        $i=1;
                        foreach ($details_cat_three as $val) {
                            if(!empty($details_cat_three)){   ?>                
                        <tr>         
                            <td><?php echo $val['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2" disabled=""></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3" disabled=""></td>
                            
                        </tr>     
                        <?php
                            $i++;
                            } else{
                                ?>
                                <tr>
                                    <td>No Records Found</td>
                                </tr>
                                <?php
                            }
                        }  ?>         
                    </tbody></table>
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
