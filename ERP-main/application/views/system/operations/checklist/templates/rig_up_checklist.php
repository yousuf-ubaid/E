<?php
$result1= array_slice($details,0,4);
$result2= array_slice($details,4,9);
$result3= array_slice($details,13,10);
$result4= array_slice($details,23,5);
$result5= array_slice($details,28,5);
$result6= array_slice($details,33,6);

$result7= array_slice($details,39,9);
$result8= array_slice($details,48,4);
$result9= array_slice($details,52,5);

$result10= array_slice($details,57,7);
$result11= array_slice($details,64,6);

$result12= array_slice($details,70,7);
$result13= array_slice($details,77,2);
$result14= array_slice($details,79,2);
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
                                        RIG UP CHECKLIST
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                        RAY-OG-FRM-412(1.0)
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
                            <td valign="middle" height="40">Rig / Hoist:</td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Well Location / No:</td>
                            <td valign="middle" height="40"></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Date of Inspection: </td>
                            <td valign="middle" height="40"></td>
                            <td valign="middle" height="40">Time of Inspection: </td>
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
                            <th><span>01</span>-&nbsp;Well Site</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result1 as $val1) {
                            if(!empty($result1)){   ?>                             
                            <tr>
                                <td><?php echo $val1['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>02</span>-&nbsp;Safety Equipment</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   ?>                             
                            <tr>
                                <td><?php echo $val2['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>03</span>-&nbsp;Well Site</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result3 as $val3) {
                            if(!empty($result3)){   ?>                             
                            <tr>
                                <td><?php echo $val3['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>04</span>-&nbsp;Rig Floor</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result4 as $val4) {
                            if(!empty($result4)){   ?>                             
                            <tr>
                                <td><?php echo $val4['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>05</span>-&nbsp;Electrical</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result5 as $val5) {
                            if(!empty($result5)){   ?>                             
                            <tr>
                                <td><?php echo $val5['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>06</span>-&nbsp;Pump & Tanks</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result6 as $val6) {
                            if(!empty($result6)){   ?>                             
                            <tr>
                                <td><?php echo $val6['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
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
                            <th><span>07</span>-&nbsp;Traveling Block</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result7 as $val7) {
                            if(!empty($result7)){   ?>                             
                            <tr>
                                <td><?php echo $val7['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>08</span>-&nbsp;Lifting Gear</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result8 as $val8) {
                            if(!empty($result8)){   ?>                             
                            <tr>
                                <td><?php echo $val8['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>09</span>-&nbsp;Caravans</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result9 as $val9) {
                            if(!empty($result9)){   ?>                             
                            <tr>
                                <td><?php echo $val9['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>10</span>-&nbsp;Tubing Equipment</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result10 as $val10) {
                            if(!empty($result10)){   ?>                             
                            <tr>
                                <td><?php echo $val10['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>11</span>-&nbsp;BOP & Equipment</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result11 as $val11) {
                            if(!empty($result11)){   ?>                             
                            <tr>
                                <td><?php echo $val11['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>12</span>-&nbsp;Accumulator</th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result12 as $val12) {
                            if(!empty($result12)){   ?>                             
                            <tr>
                                <td><?php echo $val12['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>13</span>-&nbsp;Driller control panel </th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result13 as $val13) {
                            if(!empty($result13)){   ?>                             
                            <tr>
                                <td><?php echo $val13['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
                            </tr>  
                        <?php }
                        } ?>

                        <tr>
                            <th><span>14</span>-&nbsp;Ram Stabilizer </th>
                            <th style="text-align:center">Yes</th>
                            <th style="text-align:center">No</th>
                            <th style="text-align:center">Comments</th>
                        </tr>   
                        <?php
                        foreach ($result14 as $val14) {
                            if(!empty($result14)){   ?>                             
                            <tr>
                                <td><?php echo $val14['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault1"/></td>
                                <td style="text-align:center"><input type="radio" name="finding1" id="flexRadioDefault2"/></td>
                                <td style="text-align:center"><input type="text" class="input_style_1" name="comment" id="comment" /></td>
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
