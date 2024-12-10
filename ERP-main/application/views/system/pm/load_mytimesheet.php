<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

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
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>

<?php
if (!empty($timesheetmaster)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">

                <td class="task-cat-upcoming" colspan="6">
                    <div class="task-cat-upcoming-label">My Timesheets</div>
                    <div class="taskcount"><?php echo sizeof($timesheetmaster) ?></div>
                </td>
            </tr>
            <tr>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 12%;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 25%" >TIMESHEET CODE</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 25%;">FROM DATE</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">TO DATE</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">ACTION</td>
            </tr>

            <?php
            $x = 1;
            foreach ($timesheetmaster as $val) { ?>
                <tr>

                    <td class="mailbox-name"><?php echo $x ?></td>
                    <td class="mailbox-name"><?php echo $val['DocumentCode'] ?> </td>
                    <td class="mailbox-name"><?php echo $val['fromDate'] ?> </td>
                    <td class="mailbox-name"><?php echo $val['toDate'] ?> </td>
                    <td class="mailbox-name">
                        <span class="pull-center">
                            <a href="#" onclick="view_mytimesheets('<?php echo $val['timesheetMasterID'] ?>')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a onclick=""><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </span>

                    </td>
                </tr>
                <?php
                $x++;
            }


            ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <br>
    <div class="search-no-results col-sm-12">THERE ARE NO RECORDS TO DISPLAY.</div>
<?php } ?>

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