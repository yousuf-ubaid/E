<?php
if (!empty($attachment)) {
    foreach ($attachment as $row) {
       /* $file = base_url() . 'attachments/CRM/' . $row['myFileName'];
        $link=generate_encrypt_link_only($file);*/
        $link = $this->s3->createPresignedRequest($row['fileName'], '1 hour');
        ?>
        <div class="row">
            <div class="col-sm-8">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">Files</div>
                    </div>
                    <div class="post-area">
                        <article class="post">
                            <a target="_blank" class="nopjax" href="<?php echo $link ?>">
                                <div class="item-label file">File</div>
                            </a>

                            <div class="time"><span class="hithighlight"></span></div>
                            <div class="icon">
                                <img src="<?php echo base_url('images/crm/icon-file.png'); ?>" width="16"
                                     height="16"
                                     title="File">
                            </div>
                            <header class="infoarea">
                                <strong class="attachemnt_title">
                                    <img src="<?php echo base_url('images/crm/icon_pic.gif'); ?>"
                                         style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax"
                                                                              href="<?php echo $link ?>"><?php echo $row['fileName']; ?></a>
                                    <span style="display: inline-block;"><?php echo $row['fileSize'] ?> KB</span>

                                    <div><span
                                            class="attachemnt_title"><?php echo $row['attachmentDescription'] ?></span>
                                    </div>
                                    <div>
                                        <span class="deleteSpan" style="display: inline-block;"><a
                                                onclick="delete_job_attachment(<?php echo $row['id']; ?>,'<?php echo $row['fileName']; ?>');"><span
                                                    title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                    style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span>
                                    </div>
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
                <div id="toolbar">
                    <div class="toolbar-title">Files</div>
                </div>
                <div class="post-area">
                    <article class="post">
                        <header class="infoarea">
                            <strong class="attachemnt_title">
                                <span style="text-align: center;font-size: 15px;font-weight: 800;">No Files Found </span>
                            </strong>
                        </header>
                    </article>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
