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
</style>
<?php
$date_format_policy = date_format_policy();
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Name</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Age</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Economic Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total Zakat Amount</td>
                <!--    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Is Qualified</td>-->
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
            </tr>
            <?php
            $x = 1;
            $totalZakat = 0;
            foreach ($header as $val) {

                $totalZakat += $val['totalEstimatedValue'];
                
                if($val['FamMasterID']){

                    $comage = $this->db->query("SELECT Age FROM srp_erp_ngo_com_communitymaster WHERE companyID='" . $val['companyID'] . "' AND Com_MasterID='".$val['Com_MasterID']."'");
                    $rowAge = $comage->row();

                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['benCode']; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['name']; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $rowAge->Age; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['EconStateDes']; ?></td>
                    <td class="mailbox-star" width="10%" style="color:#3c8dbc;text-align: right;" onclick="fetch_zakatDistributedDel(<?php echo $val['proposalBeneficiaryID']; ?>,<?php echo $val['proposalID'] ?>,<?php echo $val['FamMasterID']; ?>,<?php echo '\''.$val['proposalTitle'].'\''; ?>,<?php echo '\''.$val['name'].'\''; ?>);"><?php echo format_number($val['totalEstimatedValue'], $this->common_data['company_data']['company_default_decimal']); ?></td>
                    <!-- <td class="mailbox-star" width="3%" style="text-align: center">
                         <div class="skin-section extraColumns" style="text-align: center">
                             <input id="isqualified_"
                                    type="checkbox" data-caption=""
                                    class="columnSelected"
                                    name="isapproved" value=" "
                             >
                         </div>

                     </td>-->

                    <td class="mailbox-star" width="5%" style="text-align: center;">
                        <span><a onclick="edit_beneficiarySetup(<?php echo $val['proposalBeneficiaryID'] ?>,<?php echo $val['beneficiaryID']; ?>,<?php echo '\''.$val['proposalTitle'].'\''; ?>,<?php echo '\''.$val['name'].'\''; ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-edit" style=""></span></a></span>
&nbsp;&nbsp;
                        <span><a onclick="delete_beneficiary(<?php echo $val['proposalBeneficiaryID'] ?>,<?php echo $val['proposalID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                    </td>
                </tr>
                <?php
                $x++;
            }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td>
                <td class="text-right" colspan="4">
                    Total
                </td>
                <td class="text-right">
                    <?php echo number_format($totalZakat, 2) ?>
                </td>
                <td>

                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
</script>

<?php
