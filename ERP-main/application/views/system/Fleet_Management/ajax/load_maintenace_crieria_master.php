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

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
if (!empty($detail)) { ?>
    <div class="table-responsive mailbox-messages">
    <table class="table table-hover table-striped">
    <tbody>
    <tr class="task-cat noselect" style="background: white;">
        <td class="task-cat-upcoming" colspan="8">
            <div class="task-cat-upcoming-label">Maintenance Criteria</div>
            <div class="taskcount"><?php echo sizeof($detail) ?></div>
        </td>
    </tr>
    <tr>
        <td class="headrowtitle" style="border-top: 1px solid #F76F01;">#</td>
        <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Maintenance Criteria</td>
        <td class="headrowtitle" style="border-top: 1px solid #F76F01;width: 30%;">Status</td>
        <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Action</td>
    </tr>
    <?php
   $x = 1;
    foreach ($detail as $val) {
        $maintenacecatexist = $this->db->query("SELECT maintenanceDetailID FROM `fleet_maintenance_detail` where maintenanceCriteriaID = {$val['maintenanceCriteriaID']}")->row_array();
        ?>
        <tr>
            <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
            <td class="mailbox-name"><a class="link-person noselect" href="#" onclick=" "><?php echo $val['maintenanceCriteria']; ?></a></td>
            <td class="mailbox-name"><a href="#" class="numberColoring">

                    <?php if ($val['status'] == 1) {
                        echo ' <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Active</span>';
                    } else {
                        echo '<span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">In Active</span>';
                    } ?>
            </td>
            <td class="mailbox-name"><a href="#" class="numberColoring">

            <span class="pull-center">
                          <a href="#"
                               onclick="openmaintenacecriteriamasteredit(<?php echo $val['maintenanceCriteriaID'] ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>

            <?php if(empty($maintenacecatexist)){?>
                &nbsp;&nbsp;| <a
                                onclick="delete_maintenace_criteria(<?php echo $val['maintenanceCriteriaID'] ?>);"><span title="Delete"
                                                                                               rel="tooltip"
                                                                                               class="glyphicon glyphicon-trash"
                                                                                               style="color:rgb(209, 91, 71);"></span></a>
                       <?php }?>

                &nbsp;&nbsp;| <a
                        onclick="linkspareparts(<?php echo $val['maintenanceCriteriaID'] ?>);"><span title="Spare Parts"
                                                                                                                 rel="tooltip"
                                                                                                                 class="fa fa-wrench"></span></a>

                </span>

            </td>
        </div>
        </tr>

        <?php
       $x++;
    }
    ?>

    </tbody>
    </table>
    </div>
  <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO MAINTENANCE CRITERIA TO DISPLAY.</div>
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