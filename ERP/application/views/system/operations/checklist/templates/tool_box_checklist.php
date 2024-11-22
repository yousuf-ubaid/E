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
            <tr><td height="10px" style="height:10px;border-bottom: 2px solid #000;">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table">
                        <colgroup>
                            <col style="width: 30%; height:30px" />
                            <col style="width: 20%; height:30px" />
                            <col style="width: 30%; height:30px" />
                            <col style="width: 20%; height:30px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="30" style="border-top: 0;">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> DAY SHIFT
                                &nbsp;&nbsp;<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> NIGHT SHIFT
                            </td>
                            <td valign="middle" height="30" style="border-top: 0;">
                                <table class="table">                                    
                                    <tr>
                                        <td valign="middle" height="30" style="border-top: 0;width: 30%;">Date:</td>
                                        <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;width: 70%;"></td>                                        
                                    </tr>
                                </table>
                            </td>
                            <td valign="middle" height="30" style="border-top: 0;">
                                <table class="table">                                    
                                    <tr>
                                        <td valign="middle" height="30" style="border-top: 0;width: 50%;">Well / Location #:</td>
                                        <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;width: 50%;"></td>                                        
                                    </tr>
                                </table>
                            </td>
                            <td valign="middle" height="30" style="border-top: 0;">
                                <table class="table">                                    
                                    <tr>
                                        <td valign="middle" height="30" style="border-top: 0;width: 50%;">Unit #:</td>
                                        <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;width: 50%;"></td>                                        
                                    </tr>
                                </table>
                            </td>
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
                            <col style="width: 25%; height:30px" />
                            <col style="width: 25%; height:30px" />
                            <col style="width: 25%; height:30px" />
                            <col style="width: 25%; height:30px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="30" style="border-top: 0;">Driller / Operator Name:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">Driller / Operator Signature:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
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
                            <th colspan="4" scope="colgroup" style="width:80%"><b>Job / activity to be perform:</b></th>
                            <th colspan="1" scope="colgroup" style="width:20%">Time of TBT:</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;width:20%">JSA to be reviewed</th>
                            <th scope="col" style="text-align:center;width:20%">Additional hazards Identified not captured in the existing JSA</th>
                            <th scope="col" style="text-align:center;width:20%">Additional controls to be taken</th>
                            <th scope="col" style="text-align:center;width:20%">Name & signature of person attending the TBT</th>
                            <th scope="col" style="text-align:center;width:20%">Name & signature of person attending the TBT</th>
                        </tr>
                        <?php
                            foreach (range(1, 12) as $i) {  ?>
                            <tr>    
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php    } ?>
                        
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
                            <th colspan="4" scope="colgroup" style="width:80%"><b>Job / activity to be perform:</b></th>
                            <th colspan="1" scope="colgroup" style="width:20%">Time of TBT:</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;width:20%">JSA to be reviewed</th>
                            <th scope="col" style="text-align:center;width:20%">Additional hazards Identified not captured in the existing JSA</th>
                            <th scope="col" style="text-align:center;width:20%">Additional controls to be taken</th>
                            <th scope="col" style="text-align:center;width:20%">Name & signature of person attending the TBT</th>
                            <th scope="col" style="text-align:center;width:20%">Name & signature of person attending the TBT</th>
                        </tr>
                        <?php
                            foreach (range(1, 12) as $i) {  ?>
                            <tr>    
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php    } ?>
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
                            <col style="width: 35%; height:20px" />
                            <col style="width: 20%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 20%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="30" style="border-top: 0;">Rig Manager / Toolpusher name & signature:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;">WSM name & signature:</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
