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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
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
</style>
<?php
if (!empty($header)) {
    ?>
    <div class="row">
        <div class="col-sm-8">
            <?php echo $header['emailDescription']; ?>
        </div>
        <div class="col-sm-4">
            <h4><i class="fa fa-hand-o-right"></i> Email Send Users</h4>
            <?php
            if (!empty($attendees)) {
            ?>
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Email</td>
                    </tr>
                    <?php
                    $x = 1;
                    foreach ($attendees as $att) {
                        ?>
                        <tr>
                            <td class="mailbox-name"><a href="#" class=""><?php echo $x ?></a></td>
                            <td class="mailbox-name"><a href="#" class=""><?php echo $att['fullName']; ?></a></td>
                            <td class="mailbox-name"><a href="#" class=""><?php echo $att['email']; ?></a></td>
                        </tr>
                        <?php
                        $x++;
                    }
                    ?>
                    </tbody>
                </table><!-- /.table -->
            </div>
                <br>
                <div class="text-right m-t-xs">
                    <div class="form-group col-sm-12" style="margin-top: 10px;">
                        <button class="btn btn-primary" type="button" onclick="send_email(<?php echo $header['campaignID']; ?>)">Send Email</button>
                    </div>
                </div>
            <?php
            }else {
                echo "NO USERS ADDED";
            }
            ?>
            <br>
        </div>
    </div>

    <?php
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
                                <span
                                    style="text-align: center;font-size: 15px;font-weight: 800;">No Files Found </span>
                            </strong>
                        </header>
                    </article>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
