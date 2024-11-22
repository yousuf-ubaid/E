

<div class="row">
    <div class="col-md-12" style="background: #fff;padding: 25px;">

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    
                    <table>
                        <colgroup>
                            <col style="width: 25%; height:40px">
                            <col style="width: 50%; height:40px">
                            <col style="width: 25%; height:40px">
                        </colgroup>
                        <tbody><tr>
                            <td width="25%" valign="middle" height="40" style="font-size: 16px !important">
                                <img alt="Logo" style="height: 65px" src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="50%" valign="middle" height="40">
                                <table>
                                    <tbody><tr>
                                        <th style="text-align:center;font-size: 16px">COMMERCIAL INVOICE</th>
                                    </tr>
                                    
                                </tbody></table>        
                            </td>
                             <td width="25%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                                <table>
                                    <tbody><tr>
                                        <td style="text-align:right;font-size: 16px;width: 30%;">Form</td>
<td style="text-align:left;font-size: 16px;width: 70%;padding-left: 5px;"></td>
                                    </tr>
                                    
                                <tr>
                                        <td style="text-align:right;font-size: 16px;width: 30%;">Revision</td>
<td style="text-align:left;font-size: 16px;width: 70%;padding-left: 5px;">0</td>
                                    </tr><tr>
                                        <td style="text-align:right;font-size: 16px;width: 30%;">Issue</td>
<td style="text-align:left;font-size: 16px;width: 70%;padding-left: 5px;"></td>
                                    </tr></tbody></table>
                            </td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
        </div>

        <table>
            <tbody><tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </tbody></table>
        <input type="hidden" name="activity_billing_id_standard" id="activity_billing_id_standard" value="19">
        <input type="hidden" name="activity_confirmed_yn_standard" id="activity_confirmed_yn_standard" value="1">
    
        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table>
                        <tbody>
                        <tr>
                        <td style="width: 40%">
                            <table style="width: 100%;" class="table table-bordered-0">
                                <tbody><tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;text-align: left;"><?php echo $this->common_data['company_data']['company_name']; ?>
                                    <br><?php echo $this->common_data['company_data']['company_address1']; ?> <br>  <?php echo $this->common_data['company_data']['company_country']; ?>
                                    <br>TRN:  

                                    </td><!--Customer Name-->
                                    
                                </tr>
                                
                                
                                
                                
                                
                            </tbody></table>
                        </td>
                        <td style="width: 27%;"></td>
                        <td style="width: 33%;">
                            <table style="width: 100%;" class="table table-bordered-0">
                                <tbody><tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;text-align: right;">Invoice Number:</td><!--Customer Name-->
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;"> </td>
                                </tr>
                                <tr>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;border-bottom: 1px solid #f4f4f4;text-align: right;">Date:</td>
                                    <td height="25px" style="font-size:12px !Important;padding: 2px 5px;border-bottom: 1px solid #f4f4f4;"></td>
                                </tr>
                                
                                
                                
                                
                                
                            </tbody></table>
                        </td>
                        </tr>
                        
                    </tbody></table>
                </div>
            </div>
        </div>

        <table>
            <tbody><tr><td height="20px" style="height:20px">&nbsp;</td></tr>
        </tbody></table>    
        
        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <colgroup>
                            
                            <col style="width: 30%; height:25px">
                            <col style="width: 30%; height:25px">
                            <col style="width: 20%; height:25px">
                            <col style="width: 20%; height:25px">
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                  
                                <th style="text-align: left;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">CONSIGNEE:</th>  
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">SHIP TO:</th>  
                                <th style="text-align: right;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">CUSTOMER PO:</th>
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px"><?php echo $mfq_header['ClientReferenceNo']; ?>

</th>
                            </tr>

                                                        <tr>         
                                <td style="
    text-align: left;
"><?php echo $mfq_header['customerName']; ?>
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b>SALES REP:</b></td>
                                <td> &nbsp;</td>
                                
                            </tr>     
                                                      
                    <tr>         
                                <td style="
    text-align: left;
">
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b>DATE SHIPPED:</b></td>
                                <td> </td>
                                
                            </tr><tr>         
                                <td style="
    text-align: left;
">
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b>MODE OF SHIPMENT:</b></td>
                                <td> </td>
                                
                            </tr><tr>         
                                <td style="
    text-align: left;
">
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b>TYPE OF EQUIPMENT:</b></td>
                                <td> </td>
                                
                            </tr><tr>         
                                <td style="
    text-align: left;
">&nbsp;
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b>TERMS</b></td>
                                <td> </td>
                                
                            </tr><tr>         
                                <td style="
    text-align: left;
"> &nbsp;
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b></b></td>
                                <td> </td>
                                
                            </tr><tr>         
                                <td style="
    text-align: left;
"> &nbsp;
</td>
                                <td></td>
                                <td style="
    background: #fce4d6;
    text-align: right;
"><b></b></td>
                                <td> </td>
                                
                            </tr></tbody></table>
                </div>
            </div>
            
        </div>

        <table>
            <tbody><tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </tbody></table>  

        

        <div class="row">
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <colgroup>
                            <col style="width: 5%;height:25px">
                            <col style="width: 15%;height:25px">
                            <col style="width: 15%;height:25px">
                            <col style="width: 35%;height:25px">
                            <col style="width: 15%;height:25px">
                            <col style="width: 15%;height:25px">
                            
                            
                            
                        </colgroup>
                        <tbody>
                            
                            <tr>                           
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">QTY</th>  
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">HS Code</th>  
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Country of Origin</th>  
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size:12px !important">Description</th>  
                                
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Unit Price</th>
                                <th style="text-align:center;background: #fce4d6;padding-bottom: 0px;padding-top: 0;height: 25px;font-size: 12px !important;width:50px">Total</th>
                            </tr>
                                                        <tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"> </td>
                                <td style="
    text-align: right;
"> </td>
                                
                            </tr>     
                                                      
                    <tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"></td>
                                <td style="
    text-align: right;
"></td>
                                
                            </tr><tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"></td>
                                <td style="
    text-align: right;
"></td>
                                
                            </tr><tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"></td>
                                <td style="
    text-align: right;
"></td>
                                
                            </tr><tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"></td>
                                <td style="
    text-align: right;
"></td>
                                
                            </tr><tr>         
                                <td style="
    text-align: center;
">&nbsp;</td>
                                <td style="
    text-align: center;
">
</td>
                                <td style="
    text-align: center;
"></td>
                                <td style="
    text-align: left;
"></td>
                                
                                <td style="
    text-align: right;
"><table><tbody><tr><td style="
    border: 0 !important;
    width: 50%;
">CURRENCY:</td><td style="
    border: 0 !important;
    width: 50%;
">AED</td></tr></tbody></table></td>
                                <td style="
    text-align: right;
"></td>
                                
                            </tr><tr>         
                                <td style="
    text-align: right;
" colspan="5"><b>TOTAL VALUE</b></td>
                                
                                
                                
                                
                                
                                <td style="
    text-align: right;
"></td>
                                
                            </tr></tbody></table>
                </div>
            </div>            
        </div><table>
            <tbody><tr><td height="15px" style="height:15px">&nbsp;</td></tr>
        </tbody></table>

        <table>
            <tbody><tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </tbody></table><table style="
    width: 50%;
">
                        <colgroup>
                            <col style="width: 25%; height:40px">
                            <col style="width: 50%; height:40px">
                            <col style="width: 25%; height:40px">
                        </colgroup>
                        <tbody><tr>
                            
                            
                             <td width="100%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                                <table>
                                    <tbody><tr>
                                        <td style="text-align: left;font-size: 16px;width: 45%;">Signature of Authorized Person:

</td>
<td style="text-align:left;font-size: 16px;width: 55%;padding-left: 5px;border-bottom: 1px solid;"></td>
                                    </tr>
                                    
                                </tbody></table>
                            </td>
                        </tr>
                    </tbody></table>

        

        <table>
            <tbody><tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </tbody></table><!-- <div class="row">
            <div class="col-12 col-md-12">  
                <button type="button" class="btn btn-primary-new size-sm float-right"><i class="fa fa-save"></i> Submit</button>
            </div>
        </div> -->

    </div>
</div>



