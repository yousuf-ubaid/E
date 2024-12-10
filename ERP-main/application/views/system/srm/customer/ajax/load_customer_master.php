<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
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
if (!empty($output)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_name');?></td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_code');?></td><!--Code-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_Country');?></td><!--Country-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_currency');?></td><!--Currency-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_status');?></td><!--Status-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            $x = 1;
            foreach ($output as $val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x; ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 200px">
                        <div class="contact-box">
                            <img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>"
                                 alt="" width="40" height="40">

                            <div class="link-box"><strong class="contacttitle"><a class="link-person noselect"
                                                                                  href="#"
                                                                                  onclick="fetchPage('system/srm/customer/customer_edit_view','<?php echo $val['CustomerAutoID'] ?>','View Customer','SRM')"><?php echo $val['CustomerName'] ?></a><br><?php echo $val['customerEmail'] ?></a>
                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <a href="#">
                            <?php echo $val['CustomerSystemCode'] ?>
                        </a>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <a href="#">
                            <?php echo $val['CountryDes'] ?>
                        </a>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <a href="#">
                            <?php echo $val['customerCurrency'] ?>
                        </a>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <span class="label "
                              style=" color: #FFFFFF; font-size: 11px;">
                            <?php if ($val['isActive'] == 1) {
                                $active= $this->lang->line('common_active');
                                echo '<span class="label" style="color: #0ab39c; background-color: rgba(10,179,156,.1); font-size: 11px;">'.$active.'</span>';
                            } else {
                                $notactive= $this->lang->line('srm_not_active');
                                echo '<span class="label" style="color: #0ab39c; background-color: rgba(10,179,156,.1); font-size: 11px;">'.$notactive.'</span>';
                            } ?>
                            <!--Active-->
                            <!--Not Active-->

                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <span class="pull-right">
                         <a onclick="fetchPage('system/srm/customer/srm_create_customer',<?php echo $val['CustomerAutoID'] ?>,'<?php echo $this->lang->line('srm_edit_customer');?>','SRM');"><!--Edit Customer-->
                            <span title="" rel="tooltip" class="glyphicon glyphicon-pencil"
                                  data-original-title="Edit"></span></a>&nbsp;&nbsp;<a onclick="delete_customer(<?php echo $val['CustomerAutoID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"></span></a></span>

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
    <div class="search-no-results"><?php echo $this->lang->line('srm_there_are_no_customers_to_display');?>.</div><!--THERE ARE NO CUSTOMERS TO DISPLAY-->
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