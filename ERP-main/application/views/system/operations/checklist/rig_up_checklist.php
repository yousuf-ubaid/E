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

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_time = date('H:i:s');

$default_date = date('Y-m-d H:i:s',strtotime($current_date));
$default_time = date('H:i:s',strtotime($current_time));

$html = false;
$comment = '';

if($page_type =='html'){
    $html_img = '';
    $html_style = '';
    $html_radio = '';

}else{
    $html_img = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAhvL-Nr_RGSeRVENLybjH_3eqB7h71xy5xw&usqp=CAU" width="20"/>';
    $html_style = 'type="hidden"';
    $html_radio = '<input type="radio" />';
 
}

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
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
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
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <form role="form" id="data_check_list_form_rig" class="form-horizontal">
        <input type="hidden" name="header_id" value="<?php echo isset($header_record['id'])? $header_record['id'] : '' ?>">
            <input type="hidden" name="checklist_id" value="<?php echo isset($header_record['master_id'])? $header_record['master_id'] : '' ?>">
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
                            <td valign="middle" height="40"><?php echo $header_record['rig_hoist_name'] ?></td>
                            <td valign="middle" height="40">Well Location / No:</td>
                            <td valign="middle" height="40"><?php echo $header_record['well_name'] ?></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Date Inspection Done </td>
                            <td valign="middle" height="40">
                                <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="w-100" name="date_inspection" value="<?php echo isset($header_record['date_inspection'])? $header_record['date_inspection'] : $current_date ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="date_inspection" class="form-control" required>
                                </div>
                            </td>
                            <td valign="middle" height="40">Time of Inspection Done </td>
                            <td valign="middle" height="40">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="time" name="time_inspection" value="<?php echo isset($header_record['time_inspection'])? $header_record['time_inspection'] : '' ?>" id="time_inspection" class="form-control">
                                </div>
                            </td>
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
                        if(!empty($result1)){
                        foreach ($result1 as $val1) {
                              
                                $checked_id = isset($response_list[$val1['id']]['status']) ? $response_list[$val1['id']]['status'] : '';
                                $comment = isset($response_list[$val1['id']]['comments']) ? $response_list[$val1['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val1['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val1['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val1['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val1['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result2)){   
                                $checked_id = isset($response_list[$val2['id']]['status']) ? $response_list[$val2['id']]['status'] : '';
                                $comment = isset($response_list[$val2['id']]['comments']) ? $response_list[$val2['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val2['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val2['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val2['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val2['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result3)){   
                                $checked_id = isset($response_list[$val3['id']]['status']) ? $response_list[$val3['id']]['status'] : '';
                                $comment = isset($response_list[$val3['id']]['comments']) ? $response_list[$val3['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val3['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val3['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val3['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val3['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result4)){
                                $checked_id = isset($response_list[$val4['id']]['status']) ? $response_list[$val4['id']]['status'] : '';
                                $comment = isset($response_list[$val4['id']]['comments']) ? $response_list[$val4['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val4['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val4['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val4['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val4['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result5)){   
                                $checked_id = isset($response_list[$val5['id']]['status']) ? $response_list[$val5['id']]['status'] : '';
                                $comment = isset($response_list[$val5['id']]['comments']) ? $response_list[$val5['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val5['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val5['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val5['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val5['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result6)){   
                                $checked_id = isset($response_list[$val6['id']]['status']) ? $response_list[$val6['id']]['status'] : '';
                                $comment = isset($response_list[$val6['id']]['comments']) ? $response_list[$val6['id']]['comments'] : ''; 
                            ?>                             
                            <tr>
                                <td><?php echo $val6['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val6['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val6['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val6['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result7)){
                                $checked_id = isset($response_list[$val7['id']]['status']) ? $response_list[$val7['id']]['status'] : '';
                                $comment = isset($response_list[$val7['id']]['comments']) ? $response_list[$val7['id']]['comments'] : '';    
                            ?>                             
                            <tr>
                                <td><?php echo $val7['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val7['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val7['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val7['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result8)){ 
                                $checked_id = isset($response_list[$val8['id']]['status']) ? $response_list[$val8['id']]['status'] : '';
                                $comment = isset($response_list[$val8['id']]['comments']) ? $response_list[$val8['id']]['comments'] : '';   
                            ?>                             
                            <tr>
                                <td><?php echo $val8['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val8['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val8['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val8['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result9)){   
                                $checked_id = isset($response_list[$val9['id']]['status']) ? $response_list[$val9['id']]['status'] : '';
                                $comment = isset($response_list[$val9['id']]['comments']) ? $response_list[$val9['id']]['comments'] : ''; 
                            ?>                             
                            <tr>
                                <td><?php echo $val9['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val9['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val9['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val9['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result10)){  
                                $checked_id = isset($response_list[$val10['id']]['status']) ? $response_list[$val10['id']]['status'] : '';
                                $comment = isset($response_list[$val10['id']]['comments']) ? $response_list[$val10['id']]['comments'] : ''; 
                             ?>                             
                            <tr>
                                <td><?php echo $val10['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val10['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val10['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val10['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result11)){  
                                $checked_id = isset($response_list[$val11['id']]['status']) ? $response_list[$val11['id']]['status'] : '';
                                $comment = isset($response_list[$val11['id']]['comments']) ? $response_list[$val11['id']]['comments'] : ''; 
                             ?>                             
                            <tr>
                                <td><?php echo $val11['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val11['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val11['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val11['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result12)){  
                                $checked_id = isset($response_list[$val12['id']]['status']) ? $response_list[$val12['id']]['status'] : '';
                                $comment = isset($response_list[$val12['id']]['comments']) ? $response_list[$val12['id']]['comments'] : '';  
                            ?>                             
                            <tr>
                                <td><?php echo $val12['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val12['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val12['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val12['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result13)){   
                                $checked_id = isset($response_list[$val13['id']]['status']) ? $response_list[$val13['id']]['status'] : '';
                                $comment = isset($response_list[$val13['id']]['comments']) ? $response_list[$val13['id']]['comments'] : ''; 
                            ?>                             
                            <tr>
                                <td><?php echo $val13['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val13['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val13['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val13['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            if(!empty($result14)){   
                                $checked_id = isset($response_list[$val14['id']]['status']) ? $response_list[$val14['id']]['status'] : '';
                                $comment = isset($response_list[$val14['id']]['comments']) ? $response_list[$val14['id']]['comments'] : ''; 
                            ?>                             
                            <tr>
                                <td><?php echo $val14['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val14['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val14['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val14['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>
                                </td>
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
                            <?php if($page_type =='html') { ?>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="inspection_done_by" id="inspection_done_by" value="<?php echo isset($header_record['inspection_done_by'])? $header_record['inspection_done_by'] : '' ?>" /></td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="inspection_position" id="inspection_position" value="<?php echo isset($header_record['inspection_position'])? $header_record['inspection_position'] : '' ?>" /></td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="inspection_signature" id="inspection_signature" value="<?php echo isset($header_record['inspection_signature'])? $header_record['inspection_signature'] : '' ?>" /></td>
                            <?php }else{ ?>
                                <td valign="bottom" height="40"><?php echo isset($header_record['inspection_done_by'])? $header_record['inspection_done_by'] : '' ?></td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['inspection_position'])? $header_record['inspection_position'] : '' ?></td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['inspection_signature'])? $header_record['inspection_signature'] : '' ?></td>
                            <?php } ?>
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
                            <?php if($page_type =='html') { ?>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="report_review_by" id="report_review_by" value="<?php echo isset($header_record['report_review_by'])? $header_record['report_review_by'] : '' ?>" /></td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="report_review_position" id="report_review_position" value="<?php echo isset($header_record['report_review_position'])? $header_record['report_review_position'] : '' ?>" /></td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="report_review_signature" id="report_review_signature" value="<?php echo isset($header_record['report_review_signature'])? $header_record['report_review_signature'] : '' ?>" /></td>
                            <?php }else{ ?>
                                <td valign="bottom" height="40"><?php echo isset($header_record['report_review_by'])? $header_record['report_review_by'] : '' ?></td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['report_review_position'])? $header_record['report_review_position'] : '' ?></td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['report_review_signature'])? $header_record['report_review_signature'] : '' ?></td>
                            <?php } ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>


        <div class="row">
                        <div class="col-12 col-md-10">  
                                <label for="financeyear">Comment</label>
                                     <div>
                                            <textarea class="form-control" name="ChecklistComment" id="ChecklistComment"><?php echo $header_record['checklist_comment'] ?>  </textarea>
                                     </div>
                        </div>
        </div>
        

        
        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <?php if($header_record['is_confirmed']==1){ ?>
                    <!-- <button type="button" class="btn btn-primary-new size-sm float-right" onclick="save_checklist(1)"><i class="fa fa-save"></i> Edit</button> -->
                <?php } else { ?>
                <button type="button" class="btn btn-primary-new size-sm float-left" onclick="save_checklist()"><i class="fa fa-save"></i> Save Draft</button>  
                <button type="button" class="btn btn-primary-new size-sm float-right" onclick="save_checklist(1)"><i class="fa fa-save"></i> Submit</button>
                <?php } ?>
            </div>
        </div>
        </form>

        <div class="row">
            <div class="form-group col-md-6 pt-20">
                <label for="bob_number">Add Attachment</label>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="checklist_attachment_com" class="form-inline"'); ?>
                                    
                        <div class="col-sm-12">
                                <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                    value="header_checklist">
                                <input type="hidden" class="form-control" id="checklist_header_id"
                                    name="checklist_header_id" value="<?php echo isset($header_record['id'])? $header_record['id'] : '' ?>">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                        aria-hidden="true"></span></span><span
                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                            aria-hidden="true"></span></span><input
                                            type="file" name="document_file_bob" id="document_file_bob"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_checklist_header()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    
                </div>

                <div id="show_attachments_checklist"></div>

            </div>
        </div>
        
        <div>

        </div>
    </div>
</div>

<script>
        var csrf_token = '<?php echo $this->security->get_csrf_hash() ?>';

        $('#date_inspection').datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(ev){
        });

        function save_checklist(confirmedYN = null){

            var data = $('#data_check_list_form_rig').serializeArray();

            data.push({'name': 'confirmYN', 'value': confirmedYN});
            data.push({'name': 'csrf_token', 'value': csrf_token});

            swal({
                    title: "Are you sure?",
                    text: "You want to save this ",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes!"
                },
            function () {

                $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Jobs/save_checklist_response'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        $("#checklist_view_modal_response").modal('hide');
                        load_pre_job_checklist();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            
            })


        }

        function document_uplode_checklist_header() {
            var formData = new FormData($("#checklist_attachment_com")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Jobs/checklist_attachement_upload'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data['type'], data['message'], 1000);
                    if (data['status']) {
                       // $('#add_attachemnt_show').addClass('hide');
                        $('#remove_id').click();
                        //$('#opportunityattachmentDescription').val('');
                        op_checklist_attachments_com();
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function op_checklist_attachments_com() {
            var checklist_header_id = $('#checklist_header_id').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {checklist_header_id: checklist_header_id},
                url: "<?php echo site_url('Jobs/load_checklist_attachment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_attachments_checklist').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
</script>
