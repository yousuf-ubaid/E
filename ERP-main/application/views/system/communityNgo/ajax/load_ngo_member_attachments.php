<?php
if (!empty($attachment)) {
    foreach ($attachment as $row) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">Files</div>
                    </div>
                    <div class="post-area">
                        <article class="post">

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
                                                                              href="<?php echo base_url() . 'attachments/' . $row['myFileName'] ?>"><?php echo $row['myFileName']; ?></a>
                                    <span style="display: inline-block;"><?php echo $row['fileSize'] ?> KB</span>

                                    <?php
                                    if (!empty($row['docExpiryDate']) && $row['docExpiryDate'] != '0000-00-00') { ?>

                                        <div><span
                                                class="attachemnt_title"><?php echo $row['attachmentDescription'] ?></span>
                                        </div>
                                        <div><span class="attachemnt_title"
                                                   style="display: inline-block;">Expiry Date : <?php echo date('dS F Y (l)', strtotime($row['docExpiryDate'])) ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } else { ?>
                                        <div><span
                                                class="attachemnt_title"
                                                style="display: inline-block;"><?php echo $row['attachmentDescription'] ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } ?>

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
                    <div class="toolbar-title">File</div>
                </div>
                <div class="post-area">
                    <article class="post">
                        <header class="infoarea">
                            <strong class="attachemnt_title">
                                <span
                                    style="text-align: center;font-size: 15px;font-weight: 800;">No Attachments Found </span>
                            </strong>
                        </header>
                    </article>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php
if (!empty($sickness_attachment)) {
    foreach ($sickness_attachment as $row) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">Files</div>
                    </div>
                    <div class="post-area">
                        <article class="post">

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
                                                                              href="<?php echo base_url() . 'attachments/' . $row['myFileName'] ?>"><?php echo $row['myFileName']; ?></a>
                                    <span style="display: inline-block;"><?php echo $row['fileSize'] ?> KB</span>

                                    <?php
                                    if (!empty($row['docExpiryDate']) && $row['docExpiryDate'] != '0000-00-00') { ?>

                                        <div><span
                                                class="attachemnt_title"><?php echo $row['attachmentDescription'] ?></span>
                                        </div>
                                        <div><span class="attachemnt_title"
                                                   style="display: inline-block;">Expiry Date : <?php echo date('dS F Y (l)', strtotime($row['docExpiryDate'])) ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } else { ?>
                                        <div><span
                                                class="attachemnt_title"
                                                style="display: inline-block;"><?php echo $row['attachmentDescription'] ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } ?>

                                </strong>
                            </header>
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
?>

<?php
if (!empty($occupation_attachment)) {
    foreach ($occupation_attachment as $row) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">Files</div>
                    </div>
                    <div class="post-area">
                        <article class="post">

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
                                                                              href="<?php echo base_url() . 'attachments/' . $row['myFileName'] ?>"><?php echo $row['myFileName']; ?></a>
                                    <span style="display: inline-block;"><?php echo $row['fileSize'] ?> KB</span>

                                    <?php
                                    if (!empty($row['docExpiryDate']) && $row['docExpiryDate'] != '0000-00-00') { ?>

                                        <div><span
                                                class="attachemnt_title"><?php echo $row['attachmentDescription'] ?></span>
                                        </div>
                                        <div><span class="attachemnt_title"
                                                   style="display: inline-block;">Expiry Date : <?php echo date('dS F Y (l)', strtotime($row['docExpiryDate'])) ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } else { ?>
                                        <div><span
                                                class="attachemnt_title"
                                                style="display: inline-block;"><?php echo $row['attachmentDescription'] ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } ?>

                                </strong>
                            </header>
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
?>

<?php
if (!empty($qualification_attachment)) {
    foreach ($qualification_attachment as $row) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">Files</div>
                    </div>
                    <div class="post-area">
                        <article class="post">

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
                                                                              href="<?php echo base_url() . 'attachments/' . $row['myFileName'] ?>"><?php echo $row['myFileName']; ?></a>
                                    <span style="display: inline-block;"><?php echo $row['fileSize'] ?> KB</span>

                                    <?php
                                    if (!empty($row['docExpiryDate']) && $row['docExpiryDate'] != '0000-00-00') { ?>

                                        <div><span
                                                class="attachemnt_title"><?php echo $row['attachmentDescription'] ?></span>
                                        </div>
                                        <div><span class="attachemnt_title"
                                                   style="display: inline-block;">Expiry Date : <?php echo date('dS F Y (l)', strtotime($row['docExpiryDate'])) ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } else { ?>
                                        <div><span
                                                class="attachemnt_title"
                                                style="display: inline-block;"><?php echo $row['attachmentDescription'] ?></span>
                                            <span class="deleteSpan" style="display: inline-block;"><a
                                                    onclick="delete_member_attachment(<?php echo $row['attachmentID']; ?>,'<?php echo $row['myFileName']; ?>');"><span
                                                        title="" rel="tooltip" class="glyphicon glyphicon-trash"
                                                        style="color:rgb(209, 91, 71);"
                                                        data-original-title="Delete"></span></a></span>
                                        </div>
                                    <?php } ?>

                                </strong>
                            </header>
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
}
?>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 1/30/2018
 * Time: 3:31 PM
 */