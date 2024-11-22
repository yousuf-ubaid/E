<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>


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
    .headrowtitle{
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
    .icon_div{width: 100%; clear: both; height: 45px;}
    .user_icon{border-radius:50%; color: #fff;padding: 15px;text-transform: uppercase; position: absolute;}
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">
                <td class="task-cat-upcoming" colspan="12">
                    <div class="task-cat-upcoming-label">Contacts</div><!--Latest Tasks-->
                    <div class="taskcount"><?php echo sizeof($header); ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td><!--Name-->
            <!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Organization</td><!--Phone No-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Phone No</td><!--address-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created By</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <tr>
                    <td class="mailbox-name suptable"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>

                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">
                                    <a class="link-person noselect" href="#"
                                       onclick="contact_edit_view('system/crm/contact_edit_view','<?php echo $val['contactID'] ?>','View Contact','CRM')">
                                        <?php  if ($val['contactImage'] != '') { ?>
                                           <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo base_url('uploads/crm/profileimage/'.$val['contactImage']); ?>">
                                        <?php
                                           } else { ?>
                                            <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo base_url('images/crm/icon-list-contact.PNG'); ?>">
                                        <?php  } ?>
                                    </a>
                                </strong></div>
                        </div>
                    </td>

                    <td class="mailbox-name suptable">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle"><?php echo $val['documentSystemCode'] ?><br><a class="link-person noselect" href="#"
                                                                                  onclick="contact_edit_view('system/crm/contact_edit_view','<?php echo $val['contactID'] ?>','View Contact','CRM')"><?php echo $val['firstName'] . " " . $val['lastName'] ?></a>
                                    <br><?php echo $val['email'] ?></a></strong></div>
                        </div>
                    </td>

                    <td class="mailbox-name suptable"><a href="#">
                            <?php
                            if (!empty($val['organization'])) {
                                echo $val['organization'];
                            } else {
                                echo $val['linkedorganization'];
                            }
                            ?>
                        </a></td>
                    <td class="mailbox-name suptable"><a href="#"><?php echo $val['phoneMobile']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle" style="color:#aaa;">User: </strong><a class="link-person noselect" href="#"><?php echo $val['createdUserNamecrm'] ?></a><br>
                                <strong class="contacttitle"style="color:#aaa;"> Date: </strong><a class="link-person noselect" href="#"><?php echo $val['createddatecontact']; ?></a>
                            </div>
                        </div>
                    </td>
                  
                </tr>
                <?php
                $x++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_organization_to_display');?>.</div><!--THERE ARE NO ORGANIZATION TO DISPLAY-->
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>