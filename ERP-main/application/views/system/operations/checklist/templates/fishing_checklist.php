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
                            <td valign="middle" height="40">Rig Number</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Well Number</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Date Inspection Done </td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Time of Inspection Done </td>
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
            <div class="col-md-12">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center">No</th>
                            <th rowspan="2">Area of Inspection </th>
                            <th colspan="2" scope="colgroup" style="text-align:center">Condition</th>
                            <th rowspan="2" scope="colgroup" style="text-align:center">Comments</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center">Yes</th>
                            <th scope="col" style="text-align:center">No</th>
                        </tr>
                        <?php
                        $i=1;
                        foreach ($details as $val) {
                            if(!empty($details)){   ?>
                            <tr>
                                <td style="text-align:center"><?php echo $i; ?></td>
                                <td><?php echo $val['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
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
                            <col style="width: 33.3333%; height:20px" />
                            <col style="width: 33.3333%; height:20px" />
                            <col style="width: 33.3333%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Inspection done By</td>
                            <td valign="middle" height="40">Position</td>
                            <td valign="middle" height="40">Signature</td>
                        </tr>
                        <tr>
                            <td valign="bottom" height="40"></td>
                            <td valign="bottom" height="40"></td>
                            <td valign="bottom" height="40"></td>
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
                            <col style="width: 33.3333%; height:20px" />
                            <col style="width: 33.3333%; height:20px" />
                            <col style="width: 33.3333%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Reports Reviewed By </td>
                            <td valign="middle" height="40">Position</td>
                            <td valign="middle" height="40">Signature</td>
                        </tr>
                        <tr>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="reviewed_by" id="reviewed_by" /></td>
                            <td valign="bottom" height="40"><input type="text" class="input_style_1" name="position" id="position" /></td>
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
