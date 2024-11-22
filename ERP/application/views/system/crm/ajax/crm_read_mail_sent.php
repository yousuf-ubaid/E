
<style type="text/css">
    .dataTable_selectedTr {
        background-color: #B0BED9 !important;
    }

    .progressbr {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }

    /*Access Denied modal*/
    .fade-scale {
        transform: scale(0);
        opacity: 0;
        -webkit-transition: all .25s linear;
        -o-transition: all .25s linear;
        transition: all .25s linear;
    }

    .fade-scale.in {
        opacity: 1;
        transform: scale(1);
    }
</style>
<div class="mailbox-read-message">
    <div class="mailbox-read-info">
        <h3 id="subject_sent"> <?php echo $details['emailSubject']; ?> </h3>
        <h5>To: <span id="from_sent"> <?php echo $details['toEmailAddress']; ?></span><span class="mailbox-read-time pull-right"
                                                    id="date"></span>
        </h5>
    </div>
    <?php echo $details['emailBody']; ?>
 <!--   <ul class="mailbox-attachments clearfix">

            <li>
                <span class="mailbox-attachment-icon">
                    <i class="fa fa-paperclip"></i>
                </span>
                <div class="mailbox-attachment-info">
                    <a href="#" target="_blank" class="mailbox-attachment-name"><i
                            class="fa fa-paperclip"></i> <?php /*echo 'asd' */?></a>
                <span class="mailbox-attachment-size">
                          <?php /*echo 10*/?>
                        </span>
                </div>
            </li>

    </ul>-->
</div><!-- /.mailbox-read-message -->
<hr>
<?php /*if ($attachments) { */?>

<?php /*} */?>