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
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">
                <td class="task-cat-upcoming" colspan="12">
                    <div class="task-cat-upcoming-label">Organizations</div><!--Latest Tasks-->
                    <div class="taskcount"><?php echo sizeof($header); ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name');?></td><!--Name-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_phone_no');?></td><!--Phone No-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_address');?></td><!--address-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CREATED</td><!--Name-->
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><div class="contact-box">
                            <div class="link-box">
                                <?php if($val['organizationLogo'] != ''){ ?>
                                    <img class="align-left" src="<?php echo base_url('uploads/crm/organizationLogo/'.$val['organizationLogo']); ?>" width="40" height="40" style="border-radius: 40px;">
                                    <?php
                                } else { ?>
                                    <img class="align-left" src="<?php echo base_url('images/crm/organization.PNG'); ?>" width="40" height="40" style="border-radius: 40px;">
                                    <!--<div class="person-circle align-left" style="width: 40px; height: 40px; background-color: <?php /*echo $color = getColor()*/?>; cursor: pointer; border-radius: 40px"><span style="font-size: 25px; color: white; vertical-align: middle;"><center><strong><?php /*$str = $val['Name']; echo strtoupper($str[0]);*/?></center></strong></span></div>-->
                                <?php } ?>
                                <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"  onclick="organization_edit_view('system/crm/organization_edit_view','<?php echo $val['organizationID'] ?>','View Organization','CRM')"></a></strong></div></div>
                    </td>

                    <td class="mailbox-name"><div class="contact-box">
                            <div class="link-box">
                                <div class="link-box"><strong class="contacttitle"><?php echo $val['documentSystemCode'] ?><br><a class="link-person noselect" href="#"  onclick="organization_edit_view('system/crm/organization_edit_view','<?php echo $val['organizationID'] ?>','View Organization','CRM')"><?php echo $val['Name'] ?></a><br><?php echo $val['email'] ?></a></strong></div></div>
                    </td>

                    <td class="mailbox-name"><a href="#"><?php echo $val['telephoneNo']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['billingAddress']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle" style="color:#aaa;">User: </strong><a class="link-person noselect" href="#"><?php echo $val['createdUserName'] ?></a><br>
                                <strong class="contacttitle"style="color:#aaa;"> Date: </strong><a class="link-person noselect" href="#"><?php echo $val['createdDate']; ?></a>
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