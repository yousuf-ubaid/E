<?php
if (!empty($attachment)) {
    foreach ($attachment as $row) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="past-info">
                    <div class="post-area">
                        <article class="post">

                            <div class="time"><span class="hithighlight"></span></div>
                            <header class="infoarea">
                                <strong class="attachemnt_title">
                                      <span
                                          class="attachemnt_title"
                                          style="display: inline-block;"><?php echo $row['attachmentDescription'] ?></span>
                                    <img src="<?php echo base_url('images/crm/icon_pic.gif'); ?>"
                                         style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax"
                                                                              href="<?php echo base_url() . 'attachments/NGO/NoticeBoard/' . $row['attachmentFileName'] ?>"><?php echo $row['attachmentFileName']; ?></a>
                                </strong>
                            </header>
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
} else {
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="past-info">
                <div class="post-area">
                    <article class="post">
                        <header class="infoarea">
                            <strong class="attachemnt_title">
                                <span
                                    style="text-align: center;">No Attachments Found </span>
                            </strong>
                        </header>
                    </article>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

