<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }
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
    .headrowtitle{
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
    </style>

<table class="table table-hover table-striped">
    <tbody>
    <?php if ($empemails) {
        foreach ($empemails as $val) {
            ?>

            <tr>


            </tr>

            <?php
        }
    }?>
    </tbody>
</table>

<?php
if (!empty($empemails)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Email From</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Email To</td>
              <!--  <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CC Email</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Subject</td>

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: left">Sent By</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Email Date</td>
            </tr>
            <?php
            $x = 1;
            foreach ($empemails as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><?php echo $val["fromEmailAddress"]; ?></a></td>
                    <td class="mailbox-name"><?php echo $val["toEmailAddress"]; ?></a></td>
                   <!-- <?php /*if(!empty($val["ccEmail"])){*/?>
                        <td class="mailbox-name"><?php /*echo (trim_value( $val["ccEmail"], 30)); */?></a></td>
                    --><?php /*} else {
                        echo '<td class="mailbox-name"> - </td>';
                    } */?>
                    <td class="mailbox-subject"><b><a href="#" onclick="readMails_inbox_organization(<?php echo $val["crmEmailID"] ?>)"><?php echo $val["emailSubject"] ?></b></td>
                    <td class="mailbox-date"><?php echo $val["nameemployee"] ?></td>
                    <td class="mailbox-date"><?php echo $val["createdDateTimeconverted"] ?></td>
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
    <div class="search-no-results">No Records Found.</div><!--THERE ARE NO TASKS TO DISPLAY-->
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

<!-- /.table -->