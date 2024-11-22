<?php
$result1= array_slice($details,0,6);
$result2= array_slice($details,6,6);
$result3= array_slice($details,12,6);

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
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Unit / location </td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Date & time of checks done</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Forklift Reg. # </td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Forklift Reg. Expiry Date</td>
                            <td valign="middle" height="40"></td>
                        </tr>   
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px;">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-md-4" style="padding-right: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                    <?php
                        foreach ($result1 as $val1) {
                            if(!empty($result1)){   ?>        
                            <tr>
                                <th style="text-align:left; font-size:13px !important"><?php echo $val1['qtn_name']; ?></th>
                                <th style="text-align:center"><i class="fa fa-check"></i></th>
                                <th style="text-align:center"><i class="fa fa-close"></i></th>
                            </tr>
                            <tr>
                                <td style="text-align:center">                                
                                    <img src="https://erp.rbdemo.live/images/icons/default-item.png" width="50"/>
                                </td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            </tr>
                    <?php }
                        } ?>  
                        
                    </table>
                </div>
            </div>    
            <div class="col-md-4" style="padding-right: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                    <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>        
                            <tr>
                                <th style="text-align:left; font-size:13px !important"><?php echo $val2['qtn_name']; ?></th>
                                <th style="text-align:center"><i class="fa fa-check"></i></th>
                                <th style="text-align:center"><i class="fa fa-close"></i></th>
                            </tr>
                            <tr>
                                <td style="text-align:center">                                
                                    <img src="https://erp.rbdemo.live/images/icons/default-item.png" width="50"/>
                                </td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            </tr>
                    <?php }
                        } ?>  
                        
                    </table>
                </div>
            </div>      
            <div class="col-md-4" style="padding-right: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                    <?php
                        foreach ($result3 as $val3) {
                            if(!empty($result3)){   ?>        
                            <tr>
                                <th style="text-align:left; font-size:13px !important"><?php echo $val3['qtn_name']; ?></th>
                                <th style="text-align:center"><i class="fa fa-check"></i></th>
                                <th style="text-align:center"><i class="fa fa-close"></i></th>
                            </tr>
                            <tr>
                                <td style="text-align:center">                                
                                    <img src="https://erp.rbdemo.live/images/icons/default-item.png" width="50"/>
                                </td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
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
                            <col style="width: 20%; height:20px" />
                            <col style="width: 30%; height:20px" />
                            <col style="width: 20%; height:20px" />
                            <col style="width: 30%; height:20px" />
                        </colgroup>
                        <tr>
                            <th colspan="4" valign="middle" height="40">Check and inspection done by:</th>
                        </tr>                        
                        <tr>
                            <td valign="bottom" height="40">Name & Position:</td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td valign="bottom" height="40">Signature: </td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="signature" id="signature" /></td>
                        </tr>
                        <tr>
                            <th colspan="4" valign="middle" height="40">Reports received & reviewed by:</th>
                        </tr>                        
                        <tr>
                            <td valign="bottom" height="40">Name & Position:</td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td valign="bottom" height="40">Signature: </td>
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
