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

$html = true;
$comment = '';

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_time = date('H:i:s');

$default_date = date('Y-m-d H:i:s',strtotime($current_date));
$default_time = date('H:i:s',strtotime($current_time));

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
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <form role="form" id="data_check_list_form" class="form-horizontal">

        <input type="hidden" name="header_id" value="<?php echo isset($header_record['id'])? $header_record['id'] : '' ?>">
        <input type="hidden" name="checklist_id" value="<?php echo isset($header_record['master_id'])? $header_record['master_id'] : '' ?>">
        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive">
                    <table class="table table-bordered-1" style="width:100%">
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
                            <td valign="middle" height="40"><?php echo $header_record['rig_hoist_name'] ?></td>
                            <td valign="middle" height="40">Well #</td>
                            <td valign="middle" height="40"><?php echo $header_record['well_name'] ?></td>
                            <td valign="middle" height="40">Date</td>
                            <td valign="middle" height="40">
                                <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="w-100" name="date_inspection" value="<?php echo isset($header_record['date_inspection'])? $header_record['date_inspection'] : $current_date ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="date_inspection" class="form-control" required>
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
                            <?php 
                                 $checked_id = isset($response_list[$result1[0]['id']]['status']) ? $response_list[$result1[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result1[0]['id']]['comments']) ? $response_list[$result1[0]['id']]['comments'] : '';
                            ?>
                        
                            <td style="text-align:center">
                                <input type="radio" class="html" name="radio_<?php echo $result1[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? "checked ": ''; echo $html_style ?> /> <?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?>
                            </td>
                            <td style="text-align:center">
                                <input type="radio" name="radio_<?php echo $result1[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? "checked ": ''; echo $html_style ?> />  <?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?> </td>
                            <td style="text-align:center">
                                <input type="radio" name="radio_<?php echo $result1[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? "checked ": ''; echo $html_style ?> />  <?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result1[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                           
                        </tr>
                        <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){   
                                    $checked_id = isset($response_list[$val2['id']]['status']) ? $response_list[$val2['id']]['status'] : '';
                                    $comment = isset($response_list[$val2['id']]['comments']) ? $response_list[$val2['id']]['comments'] : '';
                                ?>
                                <tr>                            
                                    <td scope="row"><?php echo $val2['qtn_name']; ?></td>
                                    <td style="text-align:center"><input type="radio" name="radio_<?php echo $val2['id'] ?>" id="radioActionBtn" value="1" <?php echo ($checked_id == '1') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                    <td style="text-align:center"><input type="radio" name="radio_<?php echo $val2['id'] ?>" id="radioActionBtn" value="2" <?php echo ($checked_id == '2') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                    <td style="text-align:center"><input type="radio" name="radio_<?php echo $val2['id'] ?>" id="radioActionBtn" value="3" <?php echo ($checked_id == '3') ? "checked ": ''; echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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
                            <td rowspan="17" style="text-align:center; writing-mode: tb-rl;">RIG FLOOR / MAST</td>
                            <td scope="row"><?php echo $result3[0]['qtn_name']; ?></td>
                            <?php 
                                 $checked_id = isset($response_list[$result3[0]['id']]['status']) ? $response_list[$result3[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result3[0]['id']]['comments']) ? $response_list[$result3[0]['id']]['comments'] : '';
                            ?>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result3[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result3[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result3[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result3[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result4 as $val4) {
                            if(!empty($result4)){ 
                                $checked_id = isset($response_list[$val4['id']]['status']) ? $response_list[$val4['id']]['status'] : '';
                                $comment = isset($response_list[$val4['id']]['comments']) ? $response_list[$val4['id']]['comments'] : '';    
                            ?>
                            <tr>                            
                                <td scope="row"><?php echo $val4['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val4['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? "checked ": ''; echo $html_style ?>/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val4['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? "checked ": ''; echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val4['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? "checked ": ''; echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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
                            <?php 
                                 $checked_id = isset($response_list[$result5[0]['id']]['status']) ? $response_list[$result5[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result5[0]['id']]['comments']) ? $response_list[$result5[0]['id']]['comments'] : '';
                            ?>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result5[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result5[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result5[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result5[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result6 as $val6) {
                            if(!empty($result6)){ 
                                $checked_id = isset($response_list[$val6['id']]['status']) ? $response_list[$val6['id']]['status'] : '';
                                $comment = isset($response_list[$val6['id']]['comments']) ? $response_list[$val6['id']]['comments'] : '';     
                            ?>
                        <tr>                            
                            <td scope="row"><?php echo $val6['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val6['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val6['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val6['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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

                        <tr>
                            <td rowspan="5" style="text-align:center; writing-mode: tb-rl;">DRILLER’S PANEL</td>
                            <td scope="row"><?php echo $result7[0]['qtn_name']; ?></td>

                            <?php 
                                 $checked_id = isset($response_list[$result7[0]['id']]['status']) ? $response_list[$result7[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result7[0]['id']]['comments']) ? $response_list[$result7[0]['id']]['comments'] : '';
                            ?>

                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result7[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result7[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result7[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result7[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result8 as $val8) {
                            if(!empty($result8)){ 
                                $checked_id = isset($response_list[$val8['id']]['status']) ? $response_list[$val8['id']]['status'] : '';
                                $comment = isset($response_list[$val8['id']]['comments']) ? $response_list[$val8['id']]['comments'] : '';        
                            ?>
                            <tr>                            
                                <td scope="row"><?php echo $val8['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val8['id'] ?>" id="flexRadioDefault1" value="1"  <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?>  /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val8['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" name="radio_<?php echo $val8['id'] ?>" id="flexRadioDefault3" value="3"  <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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
                            <td rowspan="8" style="text-align:center; writing-mode: tb-rl;">MUD PUMP</td>
                            <td scope="row"><?php echo $result9[0]['qtn_name']; ?></td>

                            <?php 
                                 $checked_id = isset($response_list[$result9[0]['id']]['status']) ? $response_list[$result9[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result9[0]['id']]['comments']) ? $response_list[$result9[0]['id']]['comments'] : '';
                            ?>


                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result9[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result9[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result9[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result9[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result10 as $val10) {
                            if(!empty($result10)){ 
                                $checked_id = isset($response_list[$val10['id']]['status']) ? $response_list[$val10['id']]['status'] : '';
                                $comment = isset($response_list[$val10['id']]['comments']) ? $response_list[$val10['id']]['comments'] : '';   
                            ?>
                        <tr>                            
                            <td scope="row"><?php echo $val10['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val10['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val10['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val10['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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
                            <td rowspan="6" style="text-align:center; writing-mode: tb-rl;">BRINE TANK SYSTEM</td>
                            <td scope="row"><?php echo $result11[0]['qtn_name']; ?></td>

                            <?php 
                               $checked_id = isset($response_list[$result11[0]['id']]['status']) ? $response_list[$result11[0]['id']]['status'] : '';
                               $comment = isset($response_list[$result11[0]['id']]['comments']) ? $response_list[$result11[0]['id']]['comments'] : '';
                            ?>

                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result11[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result11[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result11[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result11[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result12 as $val12) {
                            if(!empty($result12)){ 
                                $checked_id = isset($response_list[$val12['id']]['status']) ? $response_list[$val12['id']]['status'] : '';
                                $comment = isset($response_list[$val12['id']]['comments']) ? $response_list[$val12['id']]['comments'] : '';       
                            ?>
                        <tr>                            
                            <td scope="row"><?php echo $val12['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val12['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val12['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val12['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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
                            <td rowspan="3" style="text-align:center; writing-mode: tb-rl;">CATWALK</td>
                            <td scope="row"><?php echo $result13[0]['qtn_name']; ?></td>

                            <?php 
                                $checked_id = isset($response_list[$result13[0]['id']]['status']) ? $response_list[$result13[0]['id']]['status'] : '';
                                $comment = isset($response_list[$result13[0]['id']]['comments']) ? $response_list[$result13[0]['id']]['comments'] : '';
                            ?>


                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result13[0]['id'] ?>" id="flexRadioDefault1" value="1"  <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result13[0]['id'] ?>" id="flexRadioDefault2" value="2"  <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result13[0]['id'] ?>" id="flexRadioDefault3" value="3"  <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1"  name="radio_<?php echo $result13[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result14 as $val14) {
                            if(!empty($result14)){ 
                                $checked_id = isset($response_list[$val14['id']]['status']) ? $response_list[$val14['id']]['status'] : '';
                                $comment = isset($response_list[$val14['id']]['comments']) ? $response_list[$val14['id']]['comments'] : '';      
                            ?>
                        <tr>                            
                            <td scope="row"><?php echo $val14['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val14['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val14['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val14['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
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

                        <tr>
                            <td rowspan="5" style="text-align:center; writing-mode: tb-rl;">WELL CONTROL</td>
                            <td scope="row"><?php echo $result15[0]['qtn_name']; ?></td>

                            <?php 
                                 $checked_id = isset($response_list[$result15[0]['id']]['status']) ? $response_list[$result15[0]['id']]['status'] : '';
                                 $comment = isset($response_list[$result15[0]['id']]['comments']) ? $response_list[$result15[0]['id']]['comments'] : '';
                            ?>


                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result15[0]['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result15[0]['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $result15[0]['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $result15[0]['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                    <?php } ?>

                            </td>
                        </tr>
                        <?php
                        foreach ($result16 as $val16) {
                            if(!empty($result16)){ 
                                $checked_id = isset($response_list[$val16['id']]['status']) ? $response_list[$val16['id']]['status'] : '';
                                $comment = isset($response_list[$val16['id']]['comments']) ? $response_list[$val16['id']]['comments'] : '';   
                            ?>
                        <tr>                            
                            <td scope="row"><?php echo $val16['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val16['id'] ?>" id="flexRadioDefault1" value="1" <?php echo ($checked_id == '1') ? 'checked': '';echo $html_style ?> /><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val16['id'] ?>" id="flexRadioDefault2" value="2" <?php echo ($checked_id == '2') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" name="radio_<?php echo $val16['id'] ?>" id="flexRadioDefault3" value="3" <?php echo ($checked_id == '3') ? 'checked': '';echo $html_style ?>/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            
                            <td style="text-align:center">
                                    <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val16['id'] ?>" value="<?php echo $comment ?>" id="comment" />
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
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <?php if($page_type =='html') { ?>
                        <tr>
                            <td valign="middle" height="40">Driller’s Name</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="driller_name" id="driller_name" value="<?php echo $header_record['driller_name'] ?>" /></td>
                            <td valign="middle" height="40">Driller’s Signature</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="driller_signature" id="driller_signature" value="<?php echo $header_record['driller_signature'] ?>" /></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Name </td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="rig_manager_name" id="rig_manager_name" value="<?php echo $header_record['rig_manager_name'] ?>" /></td>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Signature</td>
                            <td valign="middle" height="40"><input type="text" class="input_style_1" name="rig_manager_signature" id="rig_manager_signature" value="<?php echo $header_record['rig_manager_signature'] ?>" /></td>
                        </tr>
                        <?php }else{ ?>
                            <tr>
                            <td valign="middle" height="40">Driller’s Name</td>
                            <td valign="middle" height="40"><?php echo $header_record['driller_name'] ?></td>
                            <td valign="middle" height="40">Driller’s Signature</td>
                            <td valign="middle" height="40"><?php echo $header_record['driller_signature'] ?></td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Name </td>
                            <td valign="middle" height="40"><?php echo $header_record['rig_manager_name'] ?></td>
                            <td valign="middle" height="40">Rig Manager / Toolpusher’s Signature</td>
                            <td valign="middle" height="40"><?php echo $header_record['rig_manager_signature'] ?></td>
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
    </div>
</div>


<script>

                    
    var csrf_token = '<?php echo $this->security->get_csrf_hash() ?>';
    // function print_checklist(){       
    //     window.open("<?php //echo site_url('Invoices/load_checklist_print') ?>");
    // }

    $('#date_inspection').datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(ev){
    });

    function save_checklist(confirmedYN = null){

        var data = $('#data_check_list_form').serializeArray();

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