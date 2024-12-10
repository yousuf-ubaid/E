<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .numberOrder {

    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .numberColoring {
        font-size: 12px;
        font-weight: 500;
        color: saddlebrown;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #8bc34a;;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .tableHeader {
        border: solid 1px #e6e6e6 !important;
    }

    .btn-group-xs > .btn, .btn-xs {
        padding: 0px 3px !important;
    }

    .playbtn {
        background-color: #ddd;
        border: none;
        color:#8bc34a;
        padding: 15px -10px;
        text-align: center;
        font-size: 15px;
        margin: 4px 2px;
        transition: 0.3s;
        border-radius: 80%;
    }
    .chaticon {
        background-color: #ddd;
        border: none;
        color: black;
        padding: 15px -10px;
        text-align: center;
        font-size: 19px;
        margin: 4px 2px;
        transition: 0.3s;
        border-radius: 80%;
    }
    .attachment {
        border: none;
        color: black;
        padding: 15px -10px;
        text-align: center;
        font-size: 13px;
        margin: 4px 2px;


    }

    .playbtn:hover {
        background-color: #47b500;;
        border-radius: 50%;
    }

    .stopbtn {
        background-color: #ddd;
        border: none;
        color: #b50000;
        padding: 15px -10px;
        text-align: center;
        font-size: 13px;
        margin: 4px 2px;
        transition: 0.3s;

    }

   .stopbtn:hover {
        background-color: #b53a2e;;
        /* !*border-radius: 50%;*! */
    }
    @-webkit-keyframes blinkersubtask {
        from {opacity: 1.0;}
        to {opacity: 0.0;}
    }
    .blinksubtask{
        text-decoration: blink;
        -webkit-animation-name: blinkersubtask;
        -webkit-animation-duration: 0.6s;
        -webkit-animation-iteration-count:infinite;
        -webkit-animation-timing-function:ease-in-out;
        -webkit-animation-direction: alternate;
    }
    #time {
        font-size: 100%;
    }

</style>
<br>
<?php
if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;">Task Description</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">Date</td>
             <!--   <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">Est.End Date
                </td>-->
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">In Days
                </td>

                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center;">
                    Assignee
                </td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center">Time</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center">Status</td>

                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center">Chat</td>
                <td class="headrowtitle tableHeader" style="border-top: 1px solid #ffffff;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($detail as $row) {

                $companyid = current_companyID();
                $timespentsubtask = $this->db->query("select SUM(timeSpent) as subtasktimespent from srp_erp_crm_subtasksessions where companyID = $companyid And taskID = '{$row['taskID']}' And subTaskID = '{$row['subTaskID']}'")->row_array();

                ?>

                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $row['taskDescription']; ?></a></td>
                    <td class="mailbox-name">
                            <div class="contact-box">
                                <div class="link-box"><strong>Est. Start Date : </strong><a
                                        class="link-person noselect" href="#"><?php echo $row['startDateTask']; ?></a><br><strong>Est.End Date : </strong><a
                                        class="link-person noselect" href="#"><?php echo $row['endDateTask']; ?></a>
                                </div>
                            </div>

                    </td>
                    <td class="mailbox-name" style="text-align: center;"><a href="#"><span data-toggle="tooltip" title="Total Days" style="background-color: lightgrey; color: black;font-size: 11px;" class="badge"><b><?php echo $row['estimatedDays']; ?></b></span></td>

                    <td class="mailbox-name"><a href="#"><?php
                            $companyID = $this->common_data['company_data']['company_id'];
                            $assignees = $this->db->query("select empID, employees.Ename2 as Assigneename from srp_erp_crm_assignees crmassignees LEFT join srp_employeesdetails employees on crmassignees.empID = employees.EIdNo where
crmassignees.companyID = '{$companyID}' And crmassignees.documentID = 10  And crmassignees.MasterAutoID = '{$row['subTaskID']}'")->result_array();
                            if (!empty($assignees)) {
                                foreach ($assignees as $val) {
                                    echo $val['Assigneename'] . ",";
                                }
                            }
                            ?></a>
                    </td>
                    <td class="mailbox-name">


                            <div class="contact-box">



                                <div class="link-box"><strong>Time Allocated : </strong>
                                    <?php
                                    $hours = floor($row['estimatedHours'] / 60);
                                    $minutes = ($row['estimatedHours'] % 60);
                                    echo sprintf('%02d hours %02d minutes', $hours, $minutes);
                                    ?>
                                    <br>

                                <div class="link-box"><strong>Time Spend : </strong>
                                    <?php

                                    $hoursNew = floor($timespentsubtask['subtasktimespent']/ 60);
                                    $minutesnew = ($timespentsubtask['subtasktimespent'] % 60);
                                    echo sprintf('%02d hours %02d minutes', $hoursNew, $minutesnew);
                                    ?>
                                    <br><strong>Remaining Time : </strong>

                                    <?php
                                    $remainingtime = ($row['estimatedHours'] - $timespentsubtask['subtasktimespent']);
                                    $remainingtimenegative = ($row['estimatedHours'] - $timespentsubtask['subtasktimespent']);

                                    if($remainingtime > 0) {
                                        $hoursNewremaining = floor($remainingtime/ 60);
                                        $minutesnewremaining = ($remainingtime % 60); ?>
                                        <a href="#"> <?php echo sprintf('%02d hours %02d minutes', $hoursNewremaining, $minutesnewremaining); ?> </a >
                                    <?php } else {
                                        $remainingtimenegative = $remainingtime * -1;
                                        $hoursNewremaining = floor($remainingtimenegative/ 60);
                                        $minutesnewremaining = ($remainingtimenegative % 60); ?>
                                        <a href="#">
                                           <strong style="color: red;"><?php echo sprintf('%02d hours %02d minutes', $hoursNewremaining, $minutesnewremaining);?></strong>
                                        </a>
                                    <?php }?>
                                </div>
                            </div>
                    </td>
                    <td class="mailbox-name">
                        <?php if($row['status'] == 0){?>
                            <a style="cursor: pointer"
                                                onclick="load_sub_task_status('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID'] ?>')"> <span
                                class="label"
                                style="background-color:rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Started <i
                                    class="fa fa-external-link" aria-hidden="true"></i></span></a>

                        <?php } else if($row['status'] == 1){?>
                            <a style="cursor: pointer"
                               onclick="load_sub_task_status('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID'] ?>')"> <span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">On Going<i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php } else if($row['status'] == 2) {?>
                            <a style="cursor: pointer"
                               onclick="load_sub_task_status('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID'] ?>')"> <span
                                    class="label"
                                    style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;">Completed<i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php }?>

                    </td>
                    <td class="mailbox-name">
                        <?php
                        $companyid = current_companyID();
                        $totalnoofcahts = $this->db->query("SELECT chat.* FROM srp_erp_crm_chat chat LEFT JOIN srp_employeesdetails empdetails ON empdetails.EIdNo = chat.empID WHERE companyID = $companyid AND taskID = '{$row['taskID']}' AND subTaskID =  '{$row['subTaskID']}' ORDER BY chatID asc")->result_array();
                        ?>
                            <a href="#"
                               onclick="chat_box_subtask('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID'] ?>')"><span
                                    title="Chat" rel="tooltip"
                                    class="fa fa-comments-o chaticon"> </span></a>

                              <strong style="font-size: 120%"><?php echo sizeof($totalnoofcahts)?></strong>


                    </td>

                    <td class="mailbox-name"><a href="#">

                      <span class="pull-right">
                          <?php
                          $companyid = current_companyID();
                          $subtasksession = $this->db->query("select *,DATE_FORMAT(createdDateTime, '%h:%i:%s') as timesessionstarted from srp_erp_crm_subtasksessions where companyID = $companyid And taskID  = '{$row['taskID']}' And subTaskID = '{$row['subTaskID']}' ORDER BY sessionID DESC LIMIT 1")->row_array();





                          if(is_array($subtasksession) && $subtasksession['status']== 0){ ?>
                          <a href="#"
                                   onclick="assign_validation_start('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID']?>','<?php echo $row['createdUserID']?>')"><span
                                        title="Start" rel="tooltip"
                                        class="fa fa-play-circle playbtn"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <?php } else if ($subtasksession['status'] ?? ''== 1) {?>
                              <a href="#"
                                 onclick="stop_sub_task('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID']?>','<?php echo $subtasksession['sessionID'] ?>','<?php echo $row['createdUserID']?>')"><span
                                      title="Stop" rel="tooltip"
                                      class="fa fa-stop stopbtn"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;

                                        <?php } else {?>
                              <a href="#"
                                 onclick="assign_validation_start('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID']?>','<?php echo $row['createdUserID']?>')"><span
                                      title="Start" rel="tooltip"
                                      class="fa fa-play-circle playbtn"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <?php }?>
                            <a href="#"
                               onclick="sub_task_attachment_model('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID']?>')"><span
                                    title="Start" rel="tooltip"
                                    class="glyphicon glyphicon-paperclip attachment"></span></a> |&nbsp; <a href="#"
                               onclick="edit_subtask('<?php echo $row['subTaskID'] ?>','<?php echo $row['taskID']?>')"><span
                                    title="Edit" rel="tooltip"
                                    class="glyphicon glyphicon-pencil"></span></a>



                          <?php if (is_array($subtasksession) && $subtasksession['status'] == 1 && $subtasksession['status'] != 2 && $subtasksession['status'] != 0){ ?>
                             <div id="time" style="width: 100%;">
                             <?php
                                      $timestartintominutes = explode(':', $subtasksession['timesessionstarted']);
                                      $convertedstarttimeminuts =  (($timestartintominutes[0]*60) + ($timestartintominutes[1]) + ($timestartintominutes[2]/60));
                                      $dateTimestoped = explode(' ',$this->common_data['current_date']);
                                      $dateTimestopedTime = explode(':',$dateTimestoped[1]);
                                      $stopedtimeminutes = (($dateTimestopedTime[0]*60) + ($dateTimestopedTime[1]) + ($dateTimestopedTime[2]/60) );
                                      $totaltimespentminutes = ($stopedtimeminutes - $convertedstarttimeminuts);
                                      $hours = floor($totaltimespentminutes / 60);
                                      $minutes = ($totaltimespentminutes % 60);
                                     ?></span>
<!--
                                     <h1 id="timer_"<?php /*echo $row['subTaskID'] */?>><time>00:00:00</time></h1>
<button id="start_"<?php /*echo $row['subTaskID'] */?> onclick="timer()">start</button>
<button id="stop_"<?php /*echo $row['subTaskID'] */?>>stop</button>
<button id="clear_"<?php /*echo $row['subTaskID'] */?>>clear</button>-->

                                 <!-- <span id="hours_<?php /*echo $row['subTaskID'] */?>"><?php /*echo sprintf('%2d', $hours);*/?></span> :
                                  <span id="minutes_<?php /*echo $row['subTaskID'] */?>"><?php /*echo sprintf('%2d', $minutes);*/?> </span> :
                                  <span id="seconds_<?php /*echo $row['subTaskID'] */?>">00</span> ::
                                  <span id="milliseconds_<?php /*echo $row['subTaskID'] */?>">000</span>-->

                                   <!-- <script>
                                          resume_time('<?php /*echo sprintf('%2d', $hours);*/?>','<?php /*echo sprintf('%2d', $minutes);*/?>','0','0','<?php /*echo$row['subTaskID']*/?>');
                                     </script>-->
                         </div>

                     <?php } else { ?>

                   <!--  <h1 id="timerid_<?php /*echo $row['subTaskID'] */?>"><time>00:00:00</time></h1>
<button id="start_<?php /*echo $row['subTaskID'] */?>" onclick="addTimer(<?php /*echo $row['subTaskID'] */?>);">start</button>-->


                       <!--   <div id="time" style="width: 100%;">
                              <span id="hours_<?php /*echo $row['subTaskID'] */?>">00</span> :
                              <span id="minutes_<?php /*echo $row['subTaskID'] */?>">00</span> :
                              <span id="seconds_<?php /*echo $row['subTaskID'] */?>">00</span> ::
                              <span id="milliseconds_<?php /*echo $row['subTaskID'] */?>">000</span>
                          </div>-->
                          <?php } ?>
                    </span>
                           </div><!--<span><a onclick="attachment_modal_subtask(<?php /*echo $row['subTaskID']*/?>,'Sub Task Attachments','Task')"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a></span> --></td>
                </tr>
                <?php
               $x++;
                ?>

            <?php }
            ?>
            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <div class="search-no-results">THERE ARE NO SUB TASK TO DISPLAY.</div>
    <?php
}
?>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();

    });
   /* function addTimer (subTaskID) {
        var h1 =  $('#timer_'+subTaskID).html();/!* document.getElementById('timer_'+subTaskID)*!/,
           /!* start = document.getElementById('start'),
             stop = document.getElementById('stop'),
            clear = document.getElementById('clear'),*!/
            seconds = 0, minutes = 0, hours = 0,
            t;
        alert(subTaskID);

        function add() {

            seconds++;
            if (seconds >= 60) {
                seconds = 0;
                minutes++;
                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                }
            }

            h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

            timer();
        }

        function timer() {
            t = setTimeout(add, 1000);
        }

        timer();
    }*/

    </script>