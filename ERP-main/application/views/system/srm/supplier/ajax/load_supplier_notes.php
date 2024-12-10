<div class="row">
    <div class="col-sm-12">
        <div class="past-info">
            <div id="toolbar">
                <div class="toolbar-title">Supplier Notes</div>
            </div>
            <?php
            if (!empty($notes)) {
                foreach ($notes as $row) {
                    ?>
                    <div class="post-area">
                        <article class="post">
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

