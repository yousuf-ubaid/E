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
                                            <?php echo $title[0]['name'] ?? ''; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                        <?php echo $title[0]['document_reference_code'] ?? ''; ?>
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
                            <col style="width: 16%; height:20px" />
                            <col style="width: 16%; height:20px" />
                            <col style="width: 16%; height:20px" />
                            <col style="width: 16%; height:20px" />
                            <col style="width: 16%; height:20px" />
                            <col style="width: 16%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="20">Date</td>
                            <td valign="middle" height="20"></td>
                            <td valign="middle" height="20">Inspected By</td>
                            <td valign="middle" height="20"></td>
                            <td valign="middle" height="20">Svc Loc</td>
                            <td valign="middle" height="20"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="20">Svc Line Mgr </td>
                            <td valign="middle" height="20"></td>
                            <td valign="middle" height="20">Oper Mgr </td>
                            <td valign="middle" height="20"></td>
                            <td valign="middle" height="20">Oper Supv</td>
                            <td valign="middle" height="20"></td>
                        </tr>   
                        <tr>
                            <td valign="middle" height="20">Unit Oper </td>
                            <td colspan="2" valign="middle" height="20"></td>
                            <td valign="middle" height="20">Asset # </td>
                            <td colspan="2" valign="middle" height="20"></td>
                        </tr>   
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
                            <th></th>               
                            <th>Any point with (No) should have a comment in comment section below with an action plan to correct</th>
                            <th scope="col" style="text-align:center">Yes</th>
                            <th scope="col" style="text-align:center">No</th>
                            <th scope="col" style="text-align:center">N/A</th>
                        </tr>
                        <!-- First table layer -------------->
                        <tr>                            
                            <td colspan="5" align="center" style="text-align:center; background-color:#333;color:#fff">A. Power Swivel Asset #</td>
                        </tr>
                       
                        <?php
                        $i=1;
                        $details_cat_one = isset($details_cat_one) && is_array($details_cat_one) ? $details_cat_one : [];
                        foreach ($details_cat_one as $val) {
                            if(!empty($details_cat_one)){   ?>
                            <tr>
                                <td style="text-align:center"><?php echo $i; ?></td>
                                <td><?php echo $val['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                                
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

                        <!-- second table layer -------------->
                        <tr>                            
                            <td colspan="5" align="center" style="text-align:center; background-color:#333;color:#fff">B. Power Swivel Head Asset #</td>
                        </tr>
                        <tr>                       
                            <td align="center" style="text-align:left;"> &nbsp;</td>
                            <td colspan="4" align="center" style="text-align:left;">Rating # &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Pressure # &nbsp; &nbsp; </td>
                        </tr>
                        <?php
                        $i=1;
                        $details_cat_two = isset($details_cat_two) && is_array($details_cat_two) ? $details_cat_two : [];
                        foreach ($details_cat_two as $val) {
                            if(!empty($details_cat_two)){   ?>
                            <tr>
                                <td style="text-align:center"><?php echo $i; ?></td>
                                <td><?php echo $val['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                                
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
                            <col style="width: 80%; height:20px" />
                        </colgroup>
                        <tr>
                            <td scope="col" valign="middle" height="40" style="width: 20%;">Comments:</td>
                            <td scope="col" valign="bottom" height="40" style="width: 80%;"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
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
