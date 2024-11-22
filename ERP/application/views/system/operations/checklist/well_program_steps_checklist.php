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
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;">Location:</td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">Rig No.:</td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">Date:</td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">Well No.:</td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">Customer:</td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
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
                        <tbody>
                        <colgroup>
                            <col style="width: 5%; height:30px">
                            <col style="width: 35%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                            <col style="width: 10%; height:30px">
                        </colgroup>
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">#</th>
                            <th rowspan="2" style="background:#eee;">Well Program Activity</th>
                            <th colspan="2" style="text-align:center;background:#eee;">JSA Updated</th>
                            <th colspan="2" style="text-align:center;background:#eee;">Time</th>
                            <th rowspan="2" style="text-align:center;background:#eee;">Activity Completed</th>
                            <th rowspan="2" style="text-align:center;background:#eee;">Remarks</th>                            
                        </tr>
                        <tr>
                            <th style="text-align:center;background:#eee;">Operator</th>
                            <th style="text-align:center;background:#eee;">Supervisor</th>
                            <th style="text-align:center;background:#eee;">Start</th>
                            <th style="text-align:center;background:#eee;">Finish</th>                   
                        </tr>
                        <?php 
                        foreach (range(1, 10) as $i) {  ?>
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
                            <td> </td>
                            <td style="text-align:center"> </td>
                            <td style="text-align:center"> </td>
                            <td style="text-align:center"> </td>
                            <td style="text-align:center"> </td>
                            <td style="text-align:center"> </td>
                            <td style="text-align:center"> </td>
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
                    <table class="table">
                        <colgroup>
                            <col style="width: 15%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 30%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 20%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;vertical-align: bottom !important;">Operator Name:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;vertical-align: bottom !important;">Signature:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;padding: 0;"></td>
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
                            <col style="width: 20%; height:30px">
                            <col style="width: 30%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 20%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;vertical-align: bottom !important;">Supervisor Name:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;padding: 0;vertical-align: bottom !important;">Signature:</td>
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
