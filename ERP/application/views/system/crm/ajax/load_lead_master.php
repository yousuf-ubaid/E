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
    .actioniconclose {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #e64237;
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
            <div class="taskcount"><?php echo sizeof($headercount); ?></div>
        </td>
    </tr>
            <tr>
               <!-- <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"> </td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name');?></td><!--Name-->
               <!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_company');?></td><!--Company-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">User Responsible</td><!--Company-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_contact_no');?></td><!--Contact No-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_value');?></td><!--Value-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CREATED BY</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--Status-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                   <!-- <td class="mailbox-name"><a href="#" class="numberColoring"><?php /*echo $x; */?></a></td>-->
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">
                                    <a class="link-person noselect" href="#"
                                       onclick="fetchPage('system/crm/lead_edit_view','<?php echo $val['leadID'] ?>','View Lead','CRM')">
                                        <?php
                                        $contactimage = get_all_crm_images($val['leadImage'],'uploads/crm/lead/','contact');
                                        if ($val['leadImage'] != '') { ?>
                                            <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo $contactimage; ?>">
                                            <?php
                                        } else { ?>
                                            <!--<div class="person-circle align-left" style="width: 40px; height: 40px; background-color: <?php /*echo $color = getColor()*/?>; cursor: pointer; border-radius: 40px"><span style="font-size: 25px; color: white; vertical-align: middle;"><center><?php /*$str = $val['firstName']; echo strtoupper($str[0]);*/?></center></span></div>-->
                                            <img class="person-circle align-left" style="width: 40px; height: 40px; cursor: pointer; border-radius: 40px" src="<?php echo $contactimage; ?>">
                                        <?php  } ?>
                                    </a>
                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name">
                    <div class="link-box"><strong class="contacttitle "><a class="link-person noselect" href="#"  onclick="fetchPage('system/crm/lead_edit_view','<?php echo $val['leadID'] ?>','View Lead','CRM')">
                                <?php echo $val['documentSystemCode'] ?><br>
                                </a><?php echo $val['firstName']." ".$val['lastName'] ?><br><?php echo $val['email'] ?></strong></div></div>
                    </td>
                    <td class="mailbox-name"><a href="#">

                            <?php if(!empty($val['organization']) || !empty($val['linkedorganization'])) {
                                if (!empty($val['organization'])) {
                                    echo $val['organization'];
                                } else {
                                    echo $val['linkedorganization'];
                                }
                            }else
                            {
                                echo '-';
                            }
                            ?>
                        </a></td>

                    <td class="mailbox-name"><a href="#"><?php echo $val['userresponsiblename']; ?></a></td>
                <?php if(!empty($val['phoneMobile']))
                {?>
                     <td class="mailbox-name"><a href="#"><?php echo $val['phoneMobile']; ?></a></td>
                    <?php  }else {?>
                    <td class="mailbox-name"><a href="#">-</a></td>
                    <?php }?>





                    <td class="mailbox-name">
                        <?php
                        $companyID = current_companyID();
                        $product = $this->db->query("SELECT

                            SUM((price / companyLocalCurrencyExchangeRate)+(subscriptionAmount / companyLocalCurrencyExchangeRate)+(ImplementationAmount / companyLocalCurrencyExchangeRate)) AS Total,srp_erp_currencymaster.CurrencyCode as CurrencyCode
                        FROM
                            srp_erp_crm_leadproducts
                            INNER JOIN srp_erp_currencymaster ON srp_erp_crm_leadproducts.companyLocalCurrencyID = srp_erp_currencymaster.currencyID 
                        WHERE
                            companyID = {$companyID}
                            AND leadID = '{$val['leadID']}' 
                            GROUP BY
                            leadID ")->row_array();

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
                        <?php if(!empty($val['crmtblstatusdes'])) {?>
                       <span class="label"
                             style="background-color:<?php echo $val['statusBackgroundColorcrm'] ?>; color:<?php echo $val['statusColorcrm'] ?>; font-size: 11px;"><?php echo $val['crmtblstatusdes'] ?></span><br>                            <?php
                        if ($val['isClosed'] == 1) { ?>
                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;">Closed</div><!--Closed & Converted-->
                            <?php
                        }else if($val['isClosed'] == 2)
                        {
                            echo ' <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;">Closed & Converted</div><!--Closed & Converted-->';
                            echo '<div style="margin-top: 3%;color: #6b0a0a;font-weight: 700; font-size:10px;">'.$val['crmOpportunity'].'</div>';
                        } ?>
                        <?php }else {?>
                            <?php echo "-" ?>
                         <?php }?>



                        </td>



                <td class="mailbox-attachment">
                        <span class="pull-right">
                            <?php
                            if ($val['isClosed'] == 1) { ?>
                                <div class="actioniconclose"><span class="glyphicon glyphicon-ok"
                                                              style="color:rgb(255, 255, 255);" title="completed"></span
                                </div>
                            <?php
                            }else if($val['isClosed'] == 2)
                            {
                                echo '<div class="actionicon"><span class="glyphicon glyphicon-ok"style="color:rgb(255, 255, 255);" title="completed"></span></div>';
                            }

                            else {
                            ?>
                            <a href="#" onclick="edit_lead('<?php echo $val['leadID'] ?>','<?php echo $val['createduserlead'] ?>','<?php echo $val['responsiblePersonEmpID'] ?>')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_lead(<?php echo $val['leadID'] ?>);"><span title="Delete" rel="tooltip"
                                                                                           class="glyphicon glyphicon-trash"
                                                                                           style="color:rgb(209, 91, 71);"></span></a><!--Edit Lead-->
                        </span>
                        <?php } ?>
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