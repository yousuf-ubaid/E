<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
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
</style>
<?php
if (!empty($output)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Order Review Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Customer</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Narration</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Reference No</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Action-</td>
            </tr>
            <?php
            $x = 1;
            foreach ($output as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x ?></a></td>
                    <td class="mailbox-name"><?php echo $val['documentSystemCode'] ?></td>
                    <td class="mailbox-name"><?php echo $val['customerName'] ?></td>
                    <td class="mailbox-name"><?php echo $val['narration'] ?></td>
                    <td class="mailbox-name"><?php echo $val['referenceNo'] ?></td>
                    <td class="mailbox-attachment">
                        <span class="pull-right">
                            <a onclick="documentPageView_modal('ORD-RVW',<?php echo $val['orderreviewID'] ?>) "><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="Edit"></span></a>
                            <?php if ($val['confirmedYN'] == 0 || $val['confirmedYN']==3) { ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;<a href="#"
                                   onclick="fetchPage('system/srm/srm_order_review','<?php echo $val['orderreviewID'] ?>','Edit Order Review','<?php echo $val['inquiryID'] ?>')"><span
                                        title="Edit" rel="tooltip"
                                        class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;

                                <a onclick="delete_customer_order_review(<?php echo $val['orderreviewID'] ?>);"><span
                                            title="Delete" rel="tooltip"
                                            class="glyphicon glyphicon-trash"
                                            style="color:rgb(209, 91, 71);"></span></a>
                                <?php
                            } ?>

                            <?php if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 0) { ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="referbacksrmordrew(<?php echo $val['orderreviewID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" data-original-title="Refer Back"></span></a>
                                <?php
                            } ?>


                        </span>
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>
            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO CUSTOMER Review TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>