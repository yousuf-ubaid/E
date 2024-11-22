<?php
$result1= array_slice($details,0,1);
$result2= array_slice($details,1,8);

$result3= array_slice($details,9,10);
$result4= array_slice($details,10,16);

$result5= array_slice($details,26,27);
$result6= array_slice($details,27,5);

$result7= array_slice($details,32,33);
$result8= array_slice($details,33,4);

$result9= array_slice($details,37,38);
$result10= array_slice($details,38,7);

$result11= array_slice($details,45,46);
$result12= array_slice($details,46,5);

$result13= array_slice($details,51,52);
$result14= array_slice($details,52,2);

$result15= array_slice($details,54,55);
$result16= array_slice($details,55,5);
//print_r($result1);
//print_r($result2);
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
                                        DAILY DRILLER’S CHECKLIST
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                            RAY-OG-FRM-403 (4.0)
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
                            <col style="width: 10%; height:20px" />
                            <col style="width: 23%; height:20px" />
                            <col style="width: 10%; height:20px" />
                            <col style="width: 23%; height:20px" />
                            <col style="width: 10%; height:20px" />
                            <col style="width: 23%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Rig #</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Well #</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Date</td>
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
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                            <th rowspan="2" scope="colgroup" style="text-align:center;background:#eee;">Comments</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                    
                        <tr>
                            <td rowspan="9" style="text-align:center;writing-mode: tb-rl;">DRAWWORK</td>
                            <td scope="row"><?php echo $result1[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="17" style="text-align:center; writing-mode: tb-rl;">RIG FLOOR / MAST</td>
                            <td scope="row"><?php echo $result3[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result4 as $val4) {
                            if(!empty($result4)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val4['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                    </table>
                </div>
            </div>
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                            <th rowspan="2" scope="colgroup" style="text-align:center;background:#eee;">Comments</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                    
                        <tr>
                            <td rowspan="6" style="text-align:center; writing-mode: tb-rl;">WELL CONTROL</td>
                            <td scope="row"><?php echo $result5[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result6 as $val6) {
                            if(!empty($result6)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val6['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="5" style="text-align:center; writing-mode: tb-rl;">DRILLER’S PANEL</td>
                            <td scope="row"><?php echo $result7[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result8 as $val8) {
                            if(!empty($result8)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val8['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="8" style="text-align:center; writing-mode: tb-rl;">MUD PUMP</td>
                            <td scope="row"><?php echo $result9[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result10 as $val10) {
                            if(!empty($result10)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val10['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="6" style="text-align:center; writing-mode: tb-rl;">BRINE TANK SYSTEM</td>
                            <td scope="row"><?php echo $result11[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result12 as $val12) {
                            if(!empty($result12)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val12['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="3" style="text-align:center; writing-mode: tb-rl;">CATWALK</td>
                            <td scope="row"><?php echo $result13[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result14 as $val14) {
                            if(!empty($result14)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val14['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td rowspan="5" style="text-align:center; writing-mode: tb-rl;">WELL CONTROL</td>
                            <td scope="row"><?php echo $result15[0]['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
                        </tr>
                        <?php
                        foreach ($result16 as $val16) {
                            if(!empty($result16)){   ?>
                        <tr>                            
                            <td scope="row"><?php echo $val16['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1" checked/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                            <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault3"/></td>
                            <td style="text-align:center"><input type="text" class="input_style_1" name="comment_1" id="comment_1" /></td>
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
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Driller’s Name</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="driller_name" id="driller_name" /></td>
                            <td valign="middle" height="40">Driller’s Signature</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="driller_signature" id="driller_signature" /></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Name </td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="rig_manager_name" id="rig_manager_name" /></td>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Signature</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="rig_manager_signature" id="rig_manager_signature" /></td>
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


<script>
    function print_checklist(){       
            window.open("<?php echo site_url('Invoices/load_checklist_print') ?>");
    }
</script>