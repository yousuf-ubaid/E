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

    .numberOrder {

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

    .numberColoring {
        font-size: 12px;
        font-weight: 500;
        color: saddlebrown;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #8bc34a;;
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
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <!--<td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Phone No</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Country</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Confirm</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {

                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <!--                    <td class="mailbox-star" width="5%"><span data-id="57698933" class="noselect follow following" title="Following"></span></td>-->
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <?php if ($val['benificiaryImage'] != '') { ?>
                                <img class="align-left"
                                     src="<?php echo base_url('uploads/NGO/beneficiaryImage/' . $val['benificiaryImage']); ?>"
                                     width="40" height="40">
                                <?php
                            } else { ?>
                                <img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>"
                                     alt="" width="40" height="40">
                            <?php } ?>
                            <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"
                                                                                  onclick="fetchPage('system/communityNgo/ngo_mo_comBeneficiary_editView','<?php echo $val['benificiaryID'] ?>','View Beneficiary','NGO')"><?php echo $val['fullName']; ?>
                                        <br><?php echo $val['email'] ?><br><?php echo $val['systemCode'] ?></a>
                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['MasterPrimaryNumber']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['CountryDes']; ?></a></td>
                    <td class="mailbox-name" width="10%" style="text-align: center">
                        <?php if ($val['confirmedYN'] == 0) { ?>
                            <span class="label" style="background-color: #F44336; color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                            <?php
                        } else { ?>
                            <span class="label" style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="mailbox-attachment">
                        <span class="pull-right">
                            <?php
                            $status = '<span class="pull-right">';
                            if ($val['confirmedYN'] == 0) { ?>

                                <?php
                               if (empty($val['Com_MasterID']) || $val['Com_MasterID'] == NULL || $val['Com_MasterID'] == '0' || $val['Com_MasterID'] == '') {
                                ?>
                                   <a href="#"
                                      onclick="fetchPage('system/communityNgo/ngo_mo_beneficiary_create','<?php echo $val['benificiaryID'] ?>','Edit Beneficiary','NGO')"><span
                                           title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;
                                 <?php } else{ ?>
                                   <a href="#"
                                      onclick="fetchPage('system/communityNgo/ngo_mo_ComBeneficiary_create','<?php echo $val['benificiaryID'] ?>','Edit Community Beneficiary','NGO')"><span
                                           title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
                               <?php }  ?>

                                <a onclick="delete_beneficiary(<?php echo $val['benificiaryID'] ?>);"><span title="Delete" rel="tooltip"
                                   class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                                <?php
                            } else {
                                $status .= '<a target="_blank" href="' . site_url('CommunityNgo/load_comBeneficiary_print_view/') . '/' . $val['benificiaryID'] . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

                                $status .= "<div class='actionicon'><span class='glyphicon glyphicon-ok' style='color:rgb(255, 255, 255);' title='Confirmed'></div>";
                                ?>
                            <?php }
                            $status .= '</span>';

                            echo $status;
                            ?>
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
    <div class="search-no-results">THERE ARE NO BENEFICIARIES TO DISPLAY.</div>
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