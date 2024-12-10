<div class="row">
    <div class="col-sm-12">
        <div class="past-info">
            <div id="toolbar">
                <div class="toolbar-title">Lead Notes</div>
            </div>
            <?php
            if (!empty($notes)) {
                foreach ($notes as $row) {
                    ?>
                    <div class="post-area">
                        <article class="post" style="padding-bottom: 2%">
                            <div class="item-label file">Note</div>
                            <div class="time"><!--<span class="hithighlight"><a href="#" onclick="edit_note(<?php /*echo $row['notesID']; */?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;--><a onclick="delete_note(<?php echo $row['notesID']; ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span></div>
                            <div class="icon">
                                <img src="<?php echo base_url('images/crm/icon-file.png'); ?>" width="16"
                                     height="16"
                                     title="File">
                            </div>
                            <header class="infoarea">
                                <strong class="attachemnt_title">
                                    <span class="attachemnt_title"><?php echo $row['description'] ?></span> <br>
                                    <span class="">Created By : <?php echo $row['createdUserName'] ?> &nbsp; | &nbsp; Created Date : <?php echo $row['createdDateTime'] ?></span>
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

