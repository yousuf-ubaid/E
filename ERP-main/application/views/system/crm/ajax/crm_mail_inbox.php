<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }
    </style>

<table class="table table-hover table-striped">

    <tbody>
    <?php if (($emails)&&($emails[0]['uid']!='0')) {
        foreach ($emails as $val) {
            ?>
            <tr>
                <td><input type="checkbox"></td>
                <td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
                <td class="mailbox-name"><a href="#"
                                            onclick="readMails(<?php echo $val["uid"] ?>)"><?php
                        if($val["from"]['name'])
                        {
                            echo $val["from"]['name'];
                        }else
                        {
                            echo $val["from"]['email'];
                        } ?></a>
                </td>
                <td class="mailbox-subject">
                    <?php if ($val["read"]) {
                        ?>
                        <?php echo $val["subject"] ?>
                        <?php
                    } else {
                        ?>
                        <b><?php echo $val["subject"] ?></b>
                    <?php } ?>
                </td>
                <td class="mailbox-attachment"></td>
                <td class="mailbox-date">
                    <?php echo $val["date"] ?></td>
            </tr>
            <?php
        }
    }else if(($mailbox['successYN']!=1)){ ?>
        <div class="search-no-results">No Emails To Display.&nbsp;&nbsp;</div>
        <br>
   <?php }?>
    </tbody>
</table>

<!-- /.table -->