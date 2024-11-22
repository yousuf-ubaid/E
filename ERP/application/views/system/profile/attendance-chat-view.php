<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>

<style>
    .chat-img{max-width:100%;}

    .recent_heading h4 {
        color: #05728f;
        font-size: 21px;
        margin: auto;
    }

    .incoming_msg_img {
        display: inline-block;
        width: 6%;
    }

    .received_msg {
        display: inline-block;
        padding: 0 0 0 10px;
        vertical-align: top;
        width: 92%;
    }

    .received_width_msg p {
        background: #dcf8c6 none repeat scroll 0 0;
        border-radius: 3px;
        color: #000000;
        font-size: 14px;
        margin: 0;
        padding: 5px 10px 5px 12px;
        width: 100%;
    }

    .time_date {
        color: #747474;
        display: block;
        font-size: 12px;
        margin: 8px 0 0;
    }

    .received_width_msg { width: 57%;float: right;}

    .sent_msg p {
        background: #ece5dd none repeat scroll 0 0;
        border-radius: 3px;
        font-size: 14px;
        margin: 0; color: #000000;
        padding: 8px 10px 5px 12px;
        width:100%;
    }
    .outgoing_msg{ overflow:hidden; margin:30px 0 30px;}

    .sent_msg {
        padding-left: 9%;
        width: 60%;
    }

    .input_msg_write input {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        color: #4c4c4c;
        font-size: 15px;
        min-height: 48px;
        width: 100%;
    }

    .type_msg {border-top: 1px solid #c4c4c4;position: relative;}

    .msg_send_btn {
        background: #05728f none repeat scroll 0 0;
        border: medium none;
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        font-size: 17px;
        height: 33px;
        position: absolute;
        right: 0;
        top: 11px;
        width: 33px;
    }
    .messaging-cn { padding: 10px;}

    #msg_history {
        height: <?=($this->input->post('is_report') == 0)? '350px': '390px'?>;
        overflow-y: auto;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>
                <td><label ><?=$this->lang->line('common_employee_name')?></label>: <?=$this_emp_code?> | <?=$this_emp_name?></td>
                <td><label ><?=$this->lang->line('common_date')?></label> : <?=$attendance_date?> </td>
            </tr>
        </table>
    </div>
</div>

<div class="messaging-cn">
    <div id="msg_history">
        <?php foreach($chat_arr as $val){ ?>
            <?php if($val['createdUserID'] == current_userID()) { ?>
                <div class="incoming_msg" id="conversation_<?=$val['chatID']?>">
                    <div class="incoming_msg_img pull-right"> <img src="<?=$user_img[$val['createdUserID']]?>" class="chat-img" align="middle"> </div>
                    <div class="received_msg">
                        <div class="received_width_msg">
                            <p><?php echo $val['message']?></p>
                            <span class="time_date">
                                <?php echo date('g:i A', strtotime($val['createdDateTime'])) .' | '. date('F d', strtotime($val['createdDateTime'])) .' | '. $val['emp_name']?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } else {?>
                <div class="outgoing_msg" id="conversation_<?=$val['chatID']?>">
                    <div class="incoming_msg_img pull-left"> <img src="<?=$user_img[$val['createdUserID']]?>" class="chat-img" align="middle"> </div>
                    <div class="sent_msg">
                        <p><?php echo $val['message']?></p>
                        <span class="time_date">
                            <?php echo date('g:i A', strtotime($val['createdDateTime'])) .' | '. date('F d', strtotime($val['createdDateTime'])) .' | '. $val['emp_name']?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        <?php }?>

        <?php if(empty($chat_arr)){ ?>
            <div id="msg-empty"><?=$this->lang->line('common_no_message_found')?></div>
        <?php }?>
    </div>
    <br>
    <br>
    <?php if($this->input->post('is_report') == 0){ ?>
    <div class="type_msg">
        <div class="input_msg_write">
            <input type="hidden" id="review_id" value="<?php echo $review_id?>">
            <input type="text" style="background-color: #f3f3f3; padding: 5px;" id="review_comment" placeholder="Type a message" autocomplete="off"/>
            <button class="msg_send_btn" type="button" onclick="update_comment()"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
        </div>
    </div>
    <?php }?>
</div>

<script>
    setTimeout(function(){
        $('#msg_history')[0].scrollTop  = $('#msg_history')[0].scrollHeight;
    }, 300);


    function create_new_chat_node(chat_det){
        $('#msg-empty').hide();
        var str = '<div class="incoming_msg" id="conversation_'+chat_det['chatID']+'">';
        str += '<div class="incoming_msg_img pull-right"> <img src="<?=$this_emp_image?>" class="chat-img" align="middle"> </div>';
        str += '<div class="received_msg"> <div class="received_width_msg"> <p>'+chat_det['message']+'</p> <span class="time_date">';
        str += chat_det['ch_time']+' | '+chat_det['ch_date']+' | <?=$this_emp_name?> </span> </div> </div> </div>';

        $('#msg_history').append( str );

        $('#msg_history')[0].scrollTop  = $('#msg_history')[0].scrollHeight;
    }
</script>
<?php
