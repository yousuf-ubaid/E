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
            <div class="task-cat-upcoming-label">Leads</div><!--Latest Tasks-->
            <div class="taskcount"><?php echo sizeof($header); ?></div>
        </td>
    </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name');?></td><!--Name-->
               <!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_company');?></td><!--Company-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_contact_no');?></td><!--Contact No-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_value');?></td><!--Value-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CREATED BY</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--Status-->
                <!--<td class="headrowtitle" style="border-top: 1px solid #ffffff;">Action</td>-->
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">
                                    <a class="link-person noselect" href="#"
                                       onclick="lead_edit_view('system/crm/lead_edit_view','<?php echo $val['leadID'] ?>','View Lead','CRM')">
                                        <?php  if ($val['leadImage'] != '') { ?>
                                            <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo base_url('uploads/crm/lead/'.$val['leadImage']. '?' . time()); ?>">
                                            <?php
                                        } else { ?>
                                            <!--<div class="person-circle align-left" style="width: 40px; height: 40px; background-color: <?php /*echo $color = getColor()*/?>; cursor: pointer; border-radius: 40px"><span style="font-size: 25px; color: white; vertical-align: middle;"><center><?php /*$str = $val['firstName']; echo strtoupper($str[0]);*/?></center></span></div>-->
                                            <img class="align-left" src="<?php echo base_url('images/crm/icon-list-contact.PNG'); ?>" width="40" height="40" style="border-radius: 40px;">
                                        <?php  } ?>
                                    </a>
                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name">
                    <div class="link-box"><strong class="contacttitle "><?php echo $val['documentSystemCode'] ?><br><a class="link-person noselect" href="#"  onclick="lead_edit_view('system/crm/lead_edit_view','<?php echo $val['leadID'] ?>','View Lead','CRM')"><?php echo $val['firstName']." ".$val['lastName'] ?></a><br><?php echo $val['email'] ?></strong></div></div>
                    </td>
                    <td class="mailbox-name"><a href="#">
                            <?php
                            if(!empty($val['organization'])){
                                echo $val['organization'];
                            } else{
                                echo $val['linkedorganization'];
                            }

                            ?>
                        </a></td>

                    <td class="mailbox-name"><a href="#"><?php echo $val['phoneMobile']; ?></a></td>

                    <td class="mailbox-name">
                        <?php
                        $companyID = current_companyID();
                        $product = $this->db->query("SELECT CurrencyCode,companyLocalCurrencyExchangeRate,SUM(price/companyLocalCurrencyExchangeRate) AS Total,companyLocalCurrencyDecimalPlaces FROM srp_erp_crm_leadproducts INNER JOIN srp_erp_currencymaster ON srp_erp_crm_leadproducts.companyLocalCurrencyID = srp_erp_currencymaster.currencyID WHERE companyID = {$companyID}  AND leadID ='{$val['leadID']}' ")->row_array();
                        if(!empty($product)){

                            echo  '<a href="#">'.$product['CurrencyCode'].' : '.number_format($product['Total'],2).'</a>';
                        }
                        ?>
                        <a href="#"><?php echo ''; ?></a>
                    </td>
                <td class="mailbox-name">
                    <strong class="contacttitle">
                        <a class="link-person noselect" href="#">User: <?php echo $val['createdUserNamelead'] ?>
                            <br>Date: <?php echo $val['createdDateTimelead'] ?>
                    </strong></a>
                </td>
                    <td class="mailbox-name">
                        <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $val['statusDescription']; ?></span><br>                            <?php
                        if ($val['isClosed'] == 1) { ?>
                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;"><?php echo $this->lang->line('crm_closed_and_converted');?></div><!--Closed & Converted-->
                            <?php
                        } ?></td>

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
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_lead_to_display');?>.</div><!--THERE ARE NO LEAD TO DISPLAY-->
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