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
    .numberOrder{

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
        font-size: 12px;
        font-weight: 500;
        color: saddlebrown;
    }
</style>
<?php
$this->load->helper('operation_ngo_helper');
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
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) { ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <!--                    <td class="mailbox-star" width="5%"><span data-id="57698933" class="noselect follow following" title="Following"></span></td>-->
                    <td class="mailbox-name"><div class="contact-box">
                            <?php if($val['contactImage'] != ''){
                                $donorImg = get_all_operationngo_images($val['contactImage'],'uploads/ngo/donorsImage/','donorImg'); ?>
                                <img class="align-left" src="<?php echo $donorImg; ?>" width="40" height="40">
                                <?php
                            } else { ?>
                                <img class="align-left" src="<?php echo base_url("images/crm/icon-list-contact.png") ?>" alt="" width="40" height="40">
                            <?php } ?>
                            <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"  onclick="fetchPage('system/communityNgo/ngo_mo_donor_editView','<?php echo $val['contactID'] ?>','View Donor','CRM')"><?php echo $val['name']; ?></a><br><?php echo $val['email'] ?></a></strong></div></div>
                    </td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['MasterPrimaryNumber']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['CountryDes']; ?></a></td>
                    <td class="mailbox-attachment"><span class="pull-right">
                            <a href="#"
                               onclick="fetchPage('system/CommunityNgo/ngo_mo_donors_create','<?php echo $val['contactID'] ?>','Edit Community Donor','CRM')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_donor(<?php echo $val['contactID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
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
    <div class="search-no-results">THERE ARE NO DONORS TO DISPLAY.</div>
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
<?php
