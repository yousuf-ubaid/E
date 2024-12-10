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
                                        <th style="text-align:right;font-size: 18px">                                        
                                            <?php echo $title[0]['name']; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:right;font-size: 14px">
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
                    <table class="table table-bordered-0">
                        <colgroup>
                            <col style="width: 20%; height:20px" />
                            <col style="width: 30%; height:20px" />
                            <col style="width: 20%; height:20px" />
                            <col style="width: 30%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="20">Rig No.:</td>
                            <td valign="middle" height="20"></td>
                            <td valign="middle" height="20">Well No.:</td>
                            <td valign="middle" height="20"></td>
                        </tr>
                        
                        <tr>
                            <td colspan="4" valign="middle" height="20">Well Service Period; from __________________ to __________________ </td>
                        </tr>   
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>
        <table>
            <tr><td style="font-size: 18px !important;text-align:center"><strong>Documentation</strong></td></tr>
        </table>
        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>             
                            <th scope="col" style="text-align:center">SN</th>               
                            <th scope="col" style="text-align:center">Document</th>
                            <th scope="col" style="text-align:center">Requirement</th>
                            <th scope="col" style="text-align:center">Available</th>
                            <th scope="col" style="text-align:center">Comment</th>
                        </tr>                       
                       
                       
                            <tr>
                                <td style="text-align:center">1</td>
                                <td style="text-align:left">Well Program</td>
                                <td style="text-align:left">1 Copy</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">2</td>
                                <td style="text-align:left">Overhead Power line / pre-rig up checklists</td>
                                <td style="text-align:left">Singed Form</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">3</td>
                                <td style="text-align:left">BOP Pressure Test Chart + Accumulator Closing Test</td>
                                <td style="text-align:left">Pipe &amp; Blind rams, TIW and annular</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">4</td>
                                <td style="text-align:left">Delivery Tickets</td>
                                <td style="text-align:left">All tickets</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">5</td>
                                <td style="text-align:left">Rig Turn Over Report/ Well Program Checklist</td>
                                <td style="text-align:left">2 forms for each day</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">6</td>
                                <td style="text-align:left">Power Swivel inspection checklist</td>
                                <td style="text-align:left">If used</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">7</td>
                                <td style="text-align:left">Hydra-Walk Machine Checklists</td>
                                <td style="text-align:left">3 per day</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">8</td>
                                <td style="text-align:left">Work Plan JSA</td>
                                <td style="text-align:left">At least 1 per shift</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">9</td>
                                <td style="text-align:left">Supervisors 15 minutes Overview</td>
                                <td style="text-align:left">2 forms for each day</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr>  
                            <tr>
                                <td style="text-align:center">10</td>
                                <td style="text-align:left">Other Documents, ex Pipe Tally, Policy Variance, etc.</td>
                                <td style="text-align:left">If Used</td>
                                <td style="text-align:center"><input type="text" name="available_txt" id="available_txt"/></td>
                                <td style="text-align:center"><input type="text" name="com_txt" id="com_txt"/></td>                                
                            </tr> 
                    </table>
                </div>
            </div>            
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <table>
            <tr><td height="25px" style="font-size: 12px !important;height:25px">All documents were filed in order as per requirement;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 60%; height:20px" />
                            <col style="width: 40%; height:20px" />
                        </colgroup>
                        <tr>
                            <td height="25px" colspan="2" scope="col" valign="middle" style="background-color:#333;height:25px;padding:0;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="40" style="width: 60%;"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td scope="col" valign="bottom" height="40" style="width: 40%;"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
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
                        <colgroup>
                            <col style="width: 60%; height:20px" />
                            <col style="width: 40%; height:20px" />
                        </colgroup>
                        <tr>
                            <td height="25px" colspan="2" scope="col" valign="middle" style="background-color:#333;height:25px;padding:0;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td scope="col" valign="middle" height="40" style="width: 60%;"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td scope="col" valign="bottom" height="40" style="width: 40%;"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
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
