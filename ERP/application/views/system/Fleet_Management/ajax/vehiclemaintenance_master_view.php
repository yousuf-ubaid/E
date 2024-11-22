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

    blink {
        -webkit-animation: 1s linear infinite condemned_blink_effect;
    / / for android animation: 1 s linear infinite condemned_blink_effect;
    }

    @-webkit-keyframes condemned_blink_effect {

    /
    /
    for android

    0
    %
    {
        visibility: hidden
    ;
    }
    50
    %
    {
        visibility: hidden
    ;
    }
    100
    %
    {
        visibility: visible
    ;
    }
    }
    @keyframes condemned_blink_effect {
        0% {
            visibility: hidden;
        }
        50% {
            visibility: hidden;
        }
        100% {
            visibility: visible;
        }
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
                    <div class="task-cat-upcoming-label">Maintenance</div>
                    <div class="taskcount"><?php echo sizeof($master) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">
                    Description
                </td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;width: 37%;">
                    Maintenance
                </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center;">Action</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
            </tr>
            <?php
            $x = 1;
            foreach ($master as $val) {
                ?>
                <tr>
                    <td class="mailbox-name" style="text-align: center;">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">
                                    <div class="fileinput-new thumbnail" style="width: 120px; height: 90px;">
                                        <?php if ($val['vehicleImage'] != '') { ?>
                                            <img class="person-circle align-left"
                                                 style="width: 120px; height: 80px; cursor: pointer;"
                                                 src="<?php echo $this->s3->createPresignedRequest('uploads/Fleet/VehicleImg/'. $val['vehicleImage'] , '1 hour');?>">
                                            <?php
                                        } else { ?>
                                            <img class="person-circle align-left"
                                                 style="width: 120px; height: 80px; cursor: pointer;"
                                                 src="<?php echo base_url("images/item/no-image.png") ?>">
                                        <?php } ?>
                                    </div>

                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box">

                                <strong class="contacttitle">Number : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['VehicleNo'] ?></a> &nbsp;&nbsp;/&nbsp;

                                <strong class="contacttitle">Brand : </strong><a
                                        class="link-person noselect"
                                        href="#"><?php echo $val['brand_description'] ?></a>&nbsp;<br> <strong
                                        class="contacttitle">Model : </strong><a
                                        class="link-person noselect"
                                        href="#"><?php echo $val['model_description'] ?></a>&nbsp;&nbsp;/&nbsp;&nbsp;<strong
                                        class="contacttitle">Body Type : </strong><a
                                        class="link-person noselect"
                                        href="#"><?php echo $val['bodyType_description'] ?></a> <br><strong
                                        class="contacttitle">MF / Year : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['manufacturedYear'] ?></a>
                                &nbsp;&nbsp;/&nbsp;&nbsp; <strong class="contacttitle">Transmission : </strong><a
                                        class="link-person noselect"
                                        href="#"><?php echo $val['transmisson_description'] ?></a> &nbsp;&nbsp;/&nbsp;&nbsp;
                                <strong class="contacttitle">Fuel Type : </strong><a
                                        class="link-person noselect"
                                        href="#"><?php echo $val['fuel_type_description'] ?></a> <br><a
                                        class="link-person noselect" href="#"> </a><strong class="contacttitle">
                                    Description : </strong><a
                                        class="link-person noselect" href="#"><?php echo $val['vehDescription'] ?></a>
                                <br><a class="link-person noselect" href="#"> </a>
                            </div>
                        </div>
                    </td>
                    <?php if ($val['duration'] < 0) { ?>
                        <td class="mailbox-name" style="text-align: center; ">
                            <blink><strong class="contacttitle"
                                           style=" color: red;font-weight: 800;"> <?php echo $val['duration'] ?>
                                    Days </strong></blink>
                            <br>
                            <strong class="contacttitle">Next Maintenace Date : </strong><a
                                    class="link-person noselect" href="#">

                                <?php
                                if (!empty($val['nextMaintenanceDatecon'])) {
                                    echo $val['nextMaintenanceDatecon'];
                                } else {
                                    echo 'Not Found';
                                }


                                ?>

                            </a><br>
                            <?php if ($val['exeedkm'] < 0) { ?>
                                <blink><strong class="contacttitle"
                                               style=" color: red;font-weight: 800;"> <?php echo $val['exeedkm'] ?>
                                        KM/hrs </strong></blink>

                            <?php } else { ?>
                                <strong class="contacttitle" style=" color: green;font-weight: 800;">
                                    <?php if (!empty($val['exeedkm'])) { ?>
                                        <?php echo $val['exeedkm'] ?> KM/hrs
                                    <?php } else { ?>
                                        0 KM / hrs
                                    <?php } ?>
                                </strong>
                            <?php } ?><br>

                            <strong class="contacttitle">Next Maintenace KM/hrs : </strong><a
                                    class="link-person noselect" href="#">
                                <?php
                                if (!empty($val['nextMaintenanceONKM'])) {
                                    echo $val['nextMaintenanceONKM'];
                                } else {
                                    echo 'Not Found';
                                }


                                ?>

                            </a>
                            <br>

                        </td>
                    <?php } else { ?>
                        <td class="mailbox-name" style="text-align: center;"><strong class="contacttitle"
                                                                                     style=" color: green;font-weight: 800;"><?php echo $val['duration'] ?>
                                Days </strong> <br>
                            <strong class="contacttitle">Next Maintenace Date : </strong><a
                                    class="link-person noselect" href="#">

                                <?php
                                if (!empty($val['nextMaintenanceDatecon'])) {
                                    echo $val['nextMaintenanceDatecon'];
                                } else {
                                    echo 'Not Found';
                                }


                                ?>

                            </a><br>

                            <?php if ($val['exeedkm'] < 0) { ?>
                                <blink><strong class="contacttitle"
                                               style=" color: red;font-weight: 800;"> <?php echo $val['exeedkm'] ?>
                                        KM/hrs </strong></blink>

                            <?php } else { ?>
                                <strong class="contacttitle" style=" color: green;font-weight: 800;">
                                    <?php if (!empty($val['exeedkm'])) { ?>
                                        <?php echo $val['exeedkm'] ?> KM/hrs
                                    <?php } else { ?>
                                        0 KM/hrs
                                    <?php } ?>
                                </strong>
                            <?php } ?><br>

                            <strong class="contacttitle">Next Maintenace KM/hrs : </strong><a
                                    class="link-person noselect" href="#">
                                <?php
                                if (!empty($val['nextMaintenanceONKM'])) {
                                    echo $val['nextMaintenanceONKM'];
                                } else {
                                    echo 'Not Found';
                                }


                                ?>

                            </a>

                        </td>
                    <?php } ?>
                    <td class="mailbox-name" style="text-align: center;">

                        <a onclick="meter_reading(<?php echo $val['vehicleMasterID'] ?>);"><span
                                    title="Usage " rel="tooltip" class="fa fa-dashboard"></span></a> &nbsp;&nbsp;
                        |&nbsp;&nbsp;

                        <a href="#"
                           onclick="fetchPage('system/Fleet_Management/create_vehicale_maintenance',<?php echo $val['vehicleMasterID'] ?>,'Maintenance')"><span
                                    title="Maintenance" rel="tooltip"
                                    class="fa fa-wrench"></span></a>
                    </td>

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