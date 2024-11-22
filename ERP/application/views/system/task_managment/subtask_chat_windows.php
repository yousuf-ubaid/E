<?php
$currentuser = current_userID();
?>

    <style>
        .container{max-width:1170px; margin:auto;}
        img{max-width:100%;}
        .inbox_people {
            background: #f8f8f8 none repeat scroll 0 0;
            float: left;
            overflow: hidden;
            width: 40%; border-right:1px solid #c4c4c4;
        }
        .inbox_msg {
            clear: both;
            overflow: hidden;
        }
        .top_spac{ margin: 20px 0 0;}


        .recent_heading {float: left; width:40%;}
        .srch_bar {
            display: inline-block;
            text-align: right;
            width: 60%; padding:
        }
        .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

        .recent_heading h4 {
            color: #05728f;
            font-size: 21px;
            margin: auto;
        }
        .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
        .srch_bar .input-group-addon button {
            background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
            border: medium none;
            padding: 0;
            color: #707070;
            font-size: 18px;
        }
        .srch_bar .input-group-addon { margin: 0 0 0 -27px;}

        .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
        .chat_ib h5 span{ font-size:13px; float:right;}
        .chat_ib p{ font-size:14px; color:#989898; margin:auto}
        .chat_img {
            float: left;
            width: 11%;
        }
        .chat_ib {
            float: left;
            padding: 0 0 0 15px;
            width: 88%;
        }

        .chat_people{ overflow:hidden; clear:both;}
        .chat_list {
            border-bottom: 1px solid #c4c4c4;
            margin: 0;
            padding: 18px 16px 10px;
        }
        .inbox_chat { height: 550px; overflow-y: scroll;}

        .active_chat{ background:#ebebeb;}

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
        .received_withd_msg p {
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
        .received_withd_msg { width: 57%;float: right;}
        .mesgs {
            /*float: left;*/
            padding: 30px 15px 0 25px;
            width: 48%;
        }

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
        .messaging { padding: 0 0 50px 0;}
        .msg_history {
            height: 450px;
            overflow-y: auto;
        }

    </style>

    <!--<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->

<?php
if(!empty($chat)){?>

    <div class="container">
        <div class="messaging">
            <div class="inbox_msg">
                <div class="mesgs" style="background-color: #ffffff;border-width:thin;height:588px;border: 1px solid #ddd;">
                    <div class="msg_history">
                        <?php foreach($chat as $val){ ?>
                            <?php if($val['createdUserID'] == $currentuser) { ?>
                                <div class="incoming_msg">
                                <div class="incoming_msg_img pull-right"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil" align="middle"> </div>
                                <div class="received_msg">
                                    <div class="received_withd_msg">
                                        <p><?php echo $val['chatDescription']?></p>
                                        <span class="time_date"> <?php echo date('g:i A', strtotime($val['createdDateTime'])) ?> |  <?php echo $val['datemonth']?> | <?php echo $val['employeename']?></span></div>
                                </div>
                                </div>
                            <?php } else {?>
                                <div class="outgoing_msg">
                                    <div class="incoming_msg_img pull-left"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil" align="middle"> </div>
                                    <div class="sent_msg">
                                        <p><?php echo $val['chatDescription']?></p>
                                        <span class="time_date"> <?php echo date('g:i A', strtotime($val['createdDateTime'])) ?>   |   <?php echo $val['datemonth']?> | <?php echo $val['employeename']?></span>
                                    </div>
                                </div>
                            <?php } ?>



                        <?php }?>
                    </div>
                    <br>
                    <br>
                    <div class="type_msg">
                        <div class="input_msg_write">
                           <!-- --><?php /*echo form_open('', 'role="form" id="chat_subtask_pop"'); */?>
                            <input type="hidden" id="subtaskid" name="subtaskid" value="<?php echo $subtaskid?>">
                            <input type="hidden" id="taskid" name="taskid" value="<?php echo $taskID?>">
                            <input type="text" style="background-color: #FAFFBD;" id="commentsubtask" name="commentsubtask" class="write_msg" placeholder="Type a message" />
                         <!--   </form>-->
                            <button class="msg_send_btn" id="chat_enter_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="container">
        <div class="messaging">
            <div class="inbox_msg">
                <div class="mesgs" style="background-color: #ffffff;border-width:thin;height:588px;border: 1px solid #ddd;">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <!-- --><?php /*echo form_open('', 'role="form" id="chat_subtask_pop"'); */?>
                            <input type="hidden" id="subtaskid" name="subtaskid" value="<?php echo $subtaskid?>">
                            <input type="hidden" id="taskid" name="taskid" value="<?php echo $taskID?>">
                            <input type="text" style="background-color: #FAFFBD;" id="commentsubtask" name="commentsubtask" class="write_msg" placeholder="Type a message" />
                            <!--   </form>-->
                            <button class="msg_send_btn" id="chat_enter_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<script>

    $("#commentsubtask").keypress(function(e) {
        if(e.which == 13) {
            save_subtask_comment();
          //  $("#chat_enter_btn").click();
        }
    });
    $("#chat_enter_btn").click(function(e){
        save_subtask_comment();
    });

    function save_subtask_comment()
    {
        var subtask = $('#subtaskid').val();
        var task = $('#taskid').val();
        var comment_box = $('#commentsubtask').val();
        //var data = $('#chat_subtask_pop').serializeArray();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Task_management/sub_task_comment"); ?>',
            dataType: 'json',
            data: {subtaskid :subtask,taskid:task,commentsubtask:comment_box},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                chat_box_subtask(subtask,task);
                subtaskview();
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

</script>
