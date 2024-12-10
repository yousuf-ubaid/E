<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
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

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }

    .deleted {
        text-decoration: line-through;

    }

    . deleted div {
        text-decoration: line-through;

    }
</style>
<?php
if (!empty($master)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Journey Plans</div>
                    <div class="taskcount"><?php echo sizeof($master) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">JP Number</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Vehicle</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Driver</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Depart</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Arrive</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Document Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">JP Status</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Action</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentCode']; ?></a></td>
                    <td class="mailbox-name"><div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Code : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['vehicleCode']; ?> </a><br><strong class="contacttitle">Desc : </strong><a
                                    class="link-person noselect" href="#"><?php echo ucwords(trim_value($val['vehDescription'], 10)); ?></a><br><a class="link-person noselect" href="#"> </a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name"><div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Code : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['driverCode']; ?> </a><br><strong class="contacttitle">Name : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['driverName']; ?> </a><br><strong class="contacttitle">Tel No : </strong><a
                                    class="link-person noselect" href="#"><?php echo ucwords(trim_value($val['driverMobileNumber'], 10)); ?></a><br><a class="link-person noselect" href="#"> </a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name"><div class="link-box"><strong class="contacttitle">Place : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['departplace']; ?> </a><br><strong class="contacttitle">Date : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['datedep']; ?></a><br><strong class="contacttitle">Time : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['departime']; ?> </a><br><a class="link-person noselect" href="#"> </a>
                        </div></td>
                    <td class="mailbox-name"><div class="link-box"><strong class="contacttitle">Place : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['arriveplace']; ?> </a><br><strong class="contacttitle">Date : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['arriveda']; ?></a><br><strong class="contacttitle">Time : </strong><a
                                class="link-person noselect" href="#"><?php echo $val['arrivetime']; ?> </a><br><a class="link-person noselect" href="#"> </a>
                        </div></td>
                    <td class="mailbox-name" style="text-align: center;">
                        <?php if ($val['confirmedYN'] != 1){
                            ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Draft</span>
                            <?php
                        }else if($val['confirmedYN'] == 1 && $val['approvedYN'] != 1){ ?>

                            <a style="cursor: pointer"
                               onclick="fetch_all_approval_users_modal('JP','<?php echo $val['journeyPlanMasterID'] ?>')"><span
                                    class="label"
                                    style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;">Confirmed <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>

                        <?php } else if($val['confirmedYN'] == 2  && $val['approvedYN'] != 1){?>
                            <a style="cursor: pointer"
                               onclick="fetch_approval_reject_user_modal('JP','<?php echo $val['journeyPlanMasterID'] ?>')"> <span
                                    class="label"
                                    style="background-color:#ff784f; color: #FFFFFF; font-size: 11px;">Referred Back <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php } else if ($val['approvedYN'] == 1 && $val['confirmedYN'] == 1 ){?>
                            <a style="cursor: pointer"
                               onclick="fetch_approval_user_modal('JP','<?php echo $val['journeyPlanMasterID'] ?>')"><span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Approved <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php }?>

                    </td>
                    <td class="mailbox-name">
                        <?php if($val['status'] != 0){?>
                            <?php if($val['status'] == 2){?>
                                <a style="cursor: pointer"
                                   onclick="fetch_jp_status_modal('<?php echo $val['journeyPlanMasterID'] ?>')">  <span class="label" style="background-color:#8bc34a;  color: #FFFFFF; font-size: 11px;"> Started <i class="fa fa-external-link" aria-hidden="true"></i></span></a>
                            <?php }?>

                            <?php if($val['status'] == 3){?>
                                <a style="cursor: pointer"
                                   onclick="fetch_jp_status_modal('<?php echo $val['journeyPlanMasterID'] ?>')">  <span class="label" style="background-color:#00c0ef;  color: #FFFFFF; font-size: 11px;"> Closed <i class="fa fa-external-link" aria-hidden="true"></i></span></a>

                            <?php }?>
                            <?php if($val['status'] == 4){?>
                                <a style="cursor: pointer"
                                   onclick="fetch_jp_status_modal('<?php echo $val['journeyPlanMasterID'] ?>')">  <span class="label" style="background-color:#eed313;  color: #FFFFFF; font-size: 11px;"> Cancelled <i class="fa fa-external-link" aria-hidden="true"></i></span></a>

                            <?php }?>
                            <?php if($val['status'] == 5){?>
                                <a style="cursor: pointer"
                                   onclick="fetch_jp_status_modal('<?php echo $val['journeyPlanMasterID'] ?>')">  <span class="label" style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;"> On Hold <i class="fa fa-external-link" aria-hidden="true"></i></span></a>

                            <?php }?>
                        <?php } else {?>
                            <?php if($val['confirmedYN'] == 1 && $val['approvedYN'] == 1){ ?>
                                <a style="cursor: pointer"
                                   onclick="fetch_jp_status_modal('<?php echo $val['journeyPlanMasterID'] ?>')">  <span class="label" style="background-color:rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"> Not Started <i class="fa fa-external-link" aria-hidden="true"></i></span></a>
                            <?php }else { ?>
                                <span class="label" style="background-color:rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"> Not Started</span>
                            <?php }?>
                        <?php }?>
                    </td>

                    <td class="mailbox-name"><a href="#">
                            <?php if($val['approvedYN']!=1 && $val['confirmedYN'] != 1){?>

                                <a href="#"
                                   onclick="fetchPage('system/journeyplan/create_journey_plan_map_tour','<?php echo $val['journeyPlanMasterID'] ?>','Edit Journey Plan')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>
                                &nbsp;&nbsp;|&nbsp;
                            <?php }?>

                            <?php if($val['approvedYN'] == 1 || $val['confirmedYN'] == 1){?>
                                <a href="#"
                                   onclick="fetchPage('system/journeyplan/create_journey_plan_approval_map_tour','<?php echo $val['journeyPlanMasterID'] ?>','View Journey Plan')"><span
                                        title="View" rel="tooltip"
                                        class="glyphicon glyphicon-eye-open"></span></a>
                                &nbsp;&nbsp;|&nbsp;
                            <?php }?>
                            <?php if($val['confirmedYN'] == 1 && $val['approvedYN'] == 1){?>
                                <a onclick="jp_createInvoice(<?php echo $val['journeyPlanMasterID'] ?>);"><span
                                        title="Create Invoice" rel="tooltip" class="fa fa-file"></span></a>&nbsp;&nbsp;
                                |&nbsp;&nbsp;
                            <?php }?>
                            <?php if($val['confirmedYN'] ==1 && $val['approvedYN'] !=1){?>
                                <a onclick="referback_jp(<?php echo $val['journeyPlanMasterID'] ?>);"><span
                                        title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"
                                        style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;
                                |&nbsp;&nbsp;
                            <?php }?>

                            &nbsp;<a target="_blank" href="<?php echo site_url('Journeyplan/load_jp_view_tour/') . '/' . $val['journeyPlanMasterID'] ?>"><span
                                    title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp;

                            <a onclick="configure_ivms_no(<?php echo $val['journeyPlanMasterID'] ?>);"><span
                                    title="Journey Tracing" rel="tooltip" class="fa fa-search"></span></a>&nbsp;&nbsp;

                        </a></td>

                </tr>


                <?php
                $x++;
            } ?>
            </tbody>
        </table>
    </div>
    <?php


} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}

?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>