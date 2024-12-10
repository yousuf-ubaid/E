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
    <?php if ($empemails) {
        foreach ($empemails as $val) {
            ?>
            <tr>
                <td><input type="checkbox"></td>
                <td class="mailbox-star"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
                <td class="mailbox-name"><a href="#" onclick="readMails_sent(<?php echo $val["crmEmailID"] ?>)"><?php echo $val["toEmailAddress"]; ?></a>
                </td>
                <td class="mailbox-subject"><b><?php echo $val["emailSubject"] ?></b></td>
                <td class="mailbox-attachment"></td>
                <td class="mailbox-date"><?php echo $val["createdDateTime"] ?></td>
            </tr>

            <?php
        }
    }?>
    </tbody>
</table>

<!-- /.table -->