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
if (!empty($output)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Created Task</div>
                    <div class="taskcount"><?php echo sizeof($output) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 32%;text-align: center">Task</td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Month</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Reported By</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">	Assigned Emp</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Target Date</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Completed Date</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Company</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Segment</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>


            </tr>
            <?php
            $x = 1;
            foreach ($output as $val) {
                $month = date('F - Y', strtotime($val['MONTH']));
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"> <?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['Task']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $month ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['composedby'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['responsibleEmpname'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['targetDate'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['completedDate'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['company'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['segment'] ?></a></td>
                    <td class="mailbox-name">

                        <a onclick="view_mpr_view_myprofile(<?php echo $val['actionID']?>);"><span
                                    title="View" rel="tooltip"
                                    class="glyphicon glyphicon-eye-open"></span></a>

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
    <div class="search-no-results">THERE ARE NO ASSIGNED TASK TO DISPLAY.</div>
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