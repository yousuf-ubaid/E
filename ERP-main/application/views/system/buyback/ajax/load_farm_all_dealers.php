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
if (!empty($dealers)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Name</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($dealers as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="20%"><?php echo $val['customerSystemCode']; ?></td>
                    <td class="mailbox-star" width="20%"><?php echo $val['customerName']; ?></td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['isActive'] == 1) { ?>
                            <span class="label" style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Active</span>
                            <?php
                        } else { ?>
                            <span class="label" style="background-color: #F44336; color: #FFFFFF; font-size: 11px;">Not Active</span>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="mailbox-attachment">
                        <span class="pull-right"><a href="#" onclick="edit_farmDealers(<?php echo $val['farmDealerID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_farmDealers(<?php echo $val['farmDealerID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                    </td>
                </tr>
                <?php
                $x++;
            } ?>
            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO ASSIGN DEALERS TO DISPLAY.</div>
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