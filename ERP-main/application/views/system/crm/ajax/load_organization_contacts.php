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
    .contact-box .align-left{
        float: left;
        margin: -7px;
        padding: 2px;
        border: 1px solid #ccc;
    }
    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Phone No</td><!--address-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created By</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <tr>
                    <td class="mailbox-name"><td class="contact-box">
                            <?php if($val['contactImage'] != ''){ ?>
                                <img class="align-left" src="<?php echo base_url('uploads/crm/profileimage/'.$val['contactImage']); ?>" width="40" height="40">
                                <?php
                            } else { ?>
                                <img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>" alt="" width="40" height="40">
                            <?php } ?></td></td>

                    <td class="mailbox-name"><div class="link-box"><strong class="contacttitle"><?php echo $val['documentSystemCode'] ?><br><a class="link-person noselect" href="#"  onclick="fetchPage('system/crm/contact_edit_view','<?php echo $val['contactID'] ?>','View Contact','CRM',<?php echo $masterID ?>)"><?php echo $val['firstName']." ".$val['lastName'] ?></a><br><?php echo $val['email'] ?></strong></div></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['phoneMobile']; ?></a></td> 
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
    <div class="search-no-results">THERE ARE NO CONTACTS TO DISPLAY.</div>
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