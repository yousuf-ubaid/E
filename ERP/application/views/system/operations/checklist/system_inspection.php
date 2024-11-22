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
                    <table class="table">
                        <colgroup>
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Asset Number:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Inspection Date:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table">
                        <colgroup>
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Manager:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Inspected By:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table">
                        <colgroup>
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Supervisor:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">Operator:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
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
                        <tbody><tr>
                            <th style="text-align:center;background:#eee;">No.</th>
                            <th style="background:#eee;">Description</th>
                            <th style="text-align:center;background:#eee;">Yes</th>
                            <th style="text-align:center;background:#eee;">No</th>
                            <th style="text-align:center;background:#eee;">N/A</th>                            
                        </tr>
                        
                        <?php
                        $i=1;
                        foreach ($details as $val) {
                            if(!empty($details)){   ?>                
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
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

            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <colgroup>
                            <col style="width: 20%; height:25px">
                            <col style="width: 20%; height:25px">
                            <col style="width: 20%; height:25px">
                            <col style="width: 20%; height:25px">
                            <col style="width: 20%; height:25px">
                        </colgroup>
                        <tbody>
                            <tr>                            
                                <th colspan="3" style="text-align:left;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">2. OVERALL INSPECTION RATING</th>               
                                <th colspan="2" style="text-align:left;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;"><input type="checkbox" name="finding1" id="flexRadioDefault1" disabled=""> Pass &nbsp;&nbsp; <input type="checkbox" name="finding1" id="flexRadioDefault1" disabled="">Fail</th>   
                            </tr>
                            <tr>                            
                                <th style="text-align:left;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">List Item</th>
                                <th colspan="4" style="text-align:left;background:#eee;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;">Repair</th> 
                            </tr>
                        
                            <?php 
                            foreach (range(1, 38) as $i) {  ?>
                            <tr>
                                <td height="25" style="text-align:center"></td>
                                <td colspan="4"></td>
                            </tr>     
                            <?php } ?>
                    </tbody></table>
                </div>
            </div>
            
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

       
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-0">                      
                        <tr>
                            <td scope="col" valign="middle" height="40" style="width: 100%;padding: 0;"><u>Comments:</u></td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="30" style="width: 100%;padding: 0;"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-0">
                        <colgroup>
                            <col style="width: 20%; height:20px" />
                            <col style="width: 80%; height:20px" />
                        </colgroup>
                        <tr>
                            <td colspan="2" scope="col" valign="middle" height="40" style="width: 100%;padding: 0;"><u>Crew Members:</u></td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="30" style="width: 20%;padding: 0;">Operator:</td>
                            <td scope="col" valign="bottom" height="30" style="width: 80%;padding: 0;"></td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="30" style="width: 20%;padding: 0;">Derrick Man:</td>
                            <td scope="col" valign="bottom" height="30" style="width: 80%;padding: 0;"></td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="30" style="width: 20%;padding: 0;">Floor Hand:</td>
                            <td scope="col" valign="bottom" height="30" style="width: 80%;padding: 0;"></td>
                        </tr>
                        
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table">
                        <colgroup>
                            <col style="width: 10%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 40%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 20%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;">Signature:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;">Date:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;padding: 0;"></td>
                        </tr>
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
