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
</style>
<?php
if (!empty($detail)) {

?>
<br>
    <div class="table-responsive mailbox-messages" id="advancerecid" style="width: 113%;">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Type</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Maintenance</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Comment</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($detail as $val) {
                ?>
            <tr>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['maintenanceCode']; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['maintenacetypedescription']; ?></a></td>
                <td class="mailbox-name"><div class="contact-box">
                        <div class="link-box"><strong class="contacttitle">Date:</strong><a class="link-person noselect" href="#"><?php echo $val['documentDatecon']; ?></a><br><strong class="contacttitle">Company :</strong><a class="link-person noselect" href="#"><?php echo ucwords(trim_value($val['supplierName'], 10)); ?></a><br><a class="link-person noselect" href="#"> </a>
                        </div>
                    </div>
                </td>
                <td class="mailbox-name">
                    <?php if ($val['status'] == 1){ ?>

                        <a style="cursor: pointer"
                           onclick="load_vehicale_maintenace_status(<?php echo $val['maintenanceMasterID'] ?>)"> <span
                                    class="label"
                                    style="background-color:rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Started <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php
                    } else if ($val['status'] == 2 && $val['status'] != 1 ){?>
                        <a style="cursor: pointer"
                           onclick="load_vehicale_maintenace_status(<?php echo $val['maintenanceMasterID'] ?>)"> <span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">On Going<i
                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                    <?php }else if($val['status'] == 3) {?>
                        <a style="cursor: pointer"
                           onclick="load_vehicale_maintenace_status(<?php echo $val['maintenanceMasterID'] ?>)"> <span
                                    class="label"
                                    style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;">Closed<i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                    <?php }?>
                </td>

                <td class="mailbox-name"><a href="#" class="numberColoring"style="text-align: center;"><?php echo ucwords(trim_value($val['comment'], 6)); ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring">
                        <?php if($val['status'] != 3){?>

                        <a href="#"
                           onclick="load_vehicale_maintenace_edit(<?php echo $val['maintenanceMasterID'] ?>)"><span
                                    title="Edit" rel="tooltip"
                                    class="glyphicon glyphicon-pencil"></span></a>
                        <?php }?>

                        <?php if($val['status'] == 3){?>

                       &nbsp;<a href="#"
                                                                                      onclick="load_vehicale_maintenace_edit(<?php echo $val['maintenanceMasterID'] ?>,'1')"><span
                                    title="View" rel="tooltip"
                                    class="glyphicon glyphicon-eye-open"></span></a>

                            <?php }?>

                    </a></td>
            </tr>
                <?php
                $x++;
            } ?>
            </tbody>
           <tfoot>

            </tfoot>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" id="advancerecid">NO RECORDS FOUND.</div><?php
} ?>
<script type="text/javascript">
    $("[rel=tooltip]").tooltip();
    $(document).ready(function () {
        $('.select2').select2();



    });
</script>
