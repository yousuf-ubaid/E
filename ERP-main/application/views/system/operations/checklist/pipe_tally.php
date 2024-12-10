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
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">SUPERVISOR</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">DATE</td>
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
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">COMPANY</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">WELL</td>
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
            <div class="col-md-4">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:center;background:#eee;width: 30px;">NO</th>
                            <th style="text-align:center;background:#eee;width: 35px;">FEET</th>
                            <th style="text-align:center;background:#eee;width: 35px;">10TH</th>
                            <th style="text-align:center;background:#eee;width: 35px;">SUBTOTAL</th>
                        </tr>
                       
                        <?php 
                            foreach (range(1, 33) as $i) {  ?>
                        <tr>
                            <td style="text-align:center;width: 30px;"><?php echo $i; ?></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault2" disabled="" style="width: 100%;"></td>
                        </tr>
                        <?php } ?>                 
                    </table>
                </div>
            </div>   
            <div class="col-md-4" style="padding:0px">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:center;background:#eee;width: 30px;">NO</th>
                            <th style="text-align:center;background:#eee;width: 35px;">FEET</th>
                            <th style="text-align:center;background:#eee;width: 35px;">10TH</th>
                            <th style="text-align:center;background:#eee;width: 35px;">SUBTOTAL</th>
                        </tr>
                       
                        <?php 
                            foreach (range(34, 66) as $i) {  ?>
                        <tr>
                        <td style="text-align:center;width: 30px;"><?php echo $i; ?></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault2" disabled="" style="width: 100%;"></td>
                        </tr>
                        <?php } ?>                        
                    </table>
                </div>
            </div>   
            <div class="col-md-4">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:center;background:#eee;width: 30px;">NO</th>
                            <th style="text-align:center;background:#eee;width: 35px;">FEET</th>
                            <th style="text-align:center;background:#eee;width: 35px;">10TH</th>
                            <th style="text-align:center;background:#eee;width: 35px;">SUBTOTAL</th>
                        </tr>
                       
                        <?php 
                            foreach (range(67, 99) as $i) {  ?>
                        <tr>
                        <td style="text-align:center;width: 30px;"><?php echo $i; ?></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault1" disabled="" style="width: 100%;"></td>
                            <td style="text-align:center;width: 35px;"><input type="text" name="finding1" id="flexRadioDefault2" disabled="" style="width: 100%;"></td>
                        </tr>
                        <?php } ?>                 
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
                    <table class="table table-bordered-0">
                        <colgroup>
                            <col style="width: 15%; height:20px" />
                            <col style="width: 85%; height:20px" />
                        </colgroup>
                        <tr>
                            <td scope="col" valign="middle" height="40" style="width: 15%;vertical-align: bottom !important;padding: 0;">REMARKS</td>
                            <td scope="col" valign="bottom" height="40" style="width: 85%;border-bottom: 1px solid #000;"></td>
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
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                            <col style="width: 20%; height:30px">
                            <col style="width: 15%; height:30px">
                            <col style="width: 25%; height:30px">
                        </colgroup>
                        <tbody><tr>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">TALLIED BY</td>
                            <td valign="middle" height="30" style="border-top: 0;border-bottom: 1px solid #000;"></td>
                            <td valign="middle" height="30" style="border-top: 0;"></td>
                            <td valign="middle" height="30" style="border-top: 0;vertical-align: bottom !important;padding: 0;">CHECKED BY</td>
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
            <div class="col-12 col-md-12">  
                <button type="button" class="btn btn-primary-new size-sm float-right"><i class="fa fa-save"></i> Submit</button>
            </div>
        </div>

    </div>
</div>
