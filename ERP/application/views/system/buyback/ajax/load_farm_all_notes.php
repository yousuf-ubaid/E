<div class="row">
    <div class="col-sm-12">
        <div class="past-info">
            <div id="toolbar">
                <div class="toolbar-title">Farm Notes</div>
            </div>
            <?php
            if (!empty($notes)) {
                foreach ($notes as $row) {
                    ?>
                    <div class="post-area">
                        <article class="post">
                            <div class="item-label file">Note</div>
                            <!--<div class="time"><span class="hithighlight">Just now</span></div>-->
                            <div class="icon">
                                <img src="<?php echo base_url('images/crm/icon-file.png'); ?>" width="16"
                                     height="16"
                                     title="File">
                            </div>
                            <header class="infoarea">
                                <strong class="attachemnt_title">
                                    <div><span
                                                class="attachemnt_title"><?php echo $row['description'] ?></span>
                                    </div>
                                </strong>
                                <br>
                                <span class="pull-right">&nbsp;&nbsp;<a
                                            onclick="deletenotesfarm(<?php echo $row['notesID'] ?>);"><span
                                                title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                                style="color:rgb(209, 91, 71);"></span></a>
                            </header>
                        </article>
                    </div>
                <?php }
            } else { ?>
                <div class="post-area">
                    <article class="post">
                        <header class="infoarea">
                            <strong class="attachemnt_title">
                                <span
                                        style="text-align: center;font-size: 15px;font-weight: 800;">No Records Found </span>
                            </strong>
                        </header>
                    </article>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

