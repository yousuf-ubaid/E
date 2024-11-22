<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>



<style>

    #cTable tbody tr.highlight td {
        background-color: #FFEE58;
    }



</style>
<div style="padding-bottom: 27px;">
<?php /* echo export_buttons('cTable', 'Chart of Account',true,false) */ ?>
</div>
<table id="cTable" class="table " style="width: 100%">
    <thead>
    <tr>
        <!-- <th>Account</th>-->
        <th style="width: 200px"><?php echo $this->lang->line('finance_tr_jv_system_code');?><!--System Code--></th>
        <th style="width: 150px"><?php echo $this->lang->line('finance_common_secondary_code');?><!--Secondary Code--></th>
        <th><?php echo $this->lang->line('finance_common_gl_description');?><!--GL Description--></th>
        <th style="width: 50px;"><?php echo $this->lang->line('common_type');?><!--Type--></th>
        <?php if($deletedYN == 0){ ?>
        <th style="width: 100px;"><?php echo $this->lang->line('finance_common_balance');?><!--Balance--></th>
        <?php } ?>
        <th><?php echo $this->lang->line('common_status');?><!--Status--></th>
        <th> <?php if($deletedYN == 1) {
        echo $this->lang->line('finance_ms_ca_master_account')/*Master Account*/; ?>
        </th><th>
        <?php } ?></th>

    </tr>
    </thead>
    <tbody>
    <?php
    if ($header) {

        foreach ($header as $value) {
            ?>


            <tr class="header">
                <td colspan="3"><i class="fa fa-minus-square"
                                   aria-hidden="true"></i> <?php echo $value['CategoryTypeDescription'] ?></td>

                <td><?php echo $value['Type'] ?></td>

                <td colspan="3"></td>


            </tr>


            <?php
            if ($details) {
                foreach ($details as $account) {
                    if ($account['masterAccountYN'] == 1 && $account['accountCategoryTypeID'] == $value['accountCategoryTypeID']) {

                        ?>

                        <tr class="subheader">
                            <!--         <td></td>-->
                            <td style="padding-left: 30px"><i class="fa fa-minus"
                                                              aria-hidden="true"></i> <?php echo $account['systemAccountCode'] ?>
                            </td>
                            <td><?php echo $account['GLSecondaryCode'] ?></td>
                            <td><?php echo $account['GLDescription'] ?></td>
                            <td><?php echo $account['masterCategory'] ?></td>
                            <td style="text-align: right"><?php echo($account['companyReportingAmount'] != '' && $account['masterCategory'] == 'BS' ? number_format($account['companyReportingAmount'], $subAccount['companyReportingCurrencyDecimalPlaces']) : ($account['masterCategory'] == 'BS' ? '' : '')) ?></td>
                            <td style="text-align: center"><?php if ($account['isActive'] == 1) {
                                    ?><span class="label label-success">&nbsp;</span><?php
                                } else {
                                    ?>
                                    <span class="label label-danger">&nbsp;</span>
                                    <?php
                                } ?></td>
                        <?php if($deletedYN == 0){ ?>
                            <td><span class="pull-right"><a
                                        onclick="edit_chart_of_accont(<?php echo $account['GLAutoID'] ?>)"><span title="Edit" rel="tooltip"
                                            class="glyphicon glyphicon-pencil"></span></a>
                                     <?php if ($account['dataExist'] == 0 && $account['controllAccountYN'] == 0){?>
                                    &nbsp;&nbsp;<a
                                        onclick="delete_chart_of_account(<?php echo $account['GLAutoID'] ?>)"><span title="Delete" rel="tooltip"
                                                                                                                   class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                                <?php } ?>
                                </span></td>
                       <?php } ?>
                        </tr>
                        <?php
                        if ($details) {
                            foreach ($details as $subAccount) {
                                if ($subAccount['masterAutoID'] == $account['GLAutoID']) {
                                    ?>
                                    <tr class="subdetails">
                                        <!--    <td></td>-->
                                        <td style="padding-left: 60px"><?php echo $subAccount['systemAccountCode'] ?></td>
                                        <td><?php echo $subAccount['GLSecondaryCode'] ?></td>
                                        <td><?php echo $subAccount['GLDescription'] ?></td>
                                        <td><?php echo $subAccount['masterCategory'] ?></td>
                                        <td style="text-align: right"><?php echo($subAccount['companyReportingAmount'] != '' && $account['masterCategory'] == 'BS' ? number_format($subAccount['companyReportingAmount'], $subAccount['companyReportingCurrencyDecimalPlaces']) : ($subAccount['masterCategory'] == 'BS' ? '0.00' : '')) ?></td>
                                        <td style="text-align: center"><?php if ($subAccount['isActive'] == 1) {
                                                ?><span class="label label-success">&nbsp;</span><?php
                                            } else {
                                                ?>
                                                <span class="label label-danger">&nbsp;</span>
                                                <?php
                                            } ?></td>

                                        <td><span class="pull-right">
                                                <a onclick="edit_chart_of_accont(<?php echo $subAccount['GLAutoID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                                             <?php if($deletedYN == 0 && $subAccount['controllAccountYN'] == 0){ ?>
                                                <?php if ($subAccount['dataExist'] == 0 && $subAccount['controllAccountYN'] == 0){?>
                                                &nbsp;&nbsp;
                                                <a onclick="delete_chart_of_account(<?php echo $subAccount['GLAutoID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                                           <?php } ?>
                                            </span>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
        }

    } else {
        if ($details) {
            foreach ($details as $account) { ?>
                <tr class="">
                    <!--         <td></td>-->
                    <td style="padding-left: 30px"><i class="fa fa-minus"
                                                      aria-hidden="true"></i> <?php echo $account['systemAccountCode'] ?>
                    </td>
                    <td><?php echo $account['GLSecondaryCode'] ?></td>
                    <td><?php echo $account['GLDescription'] ?></td>
                    <td><?php echo $account['masterCategory'] ?></td>
                    <td style="text-align: center"><?php if ($account['isActive'] == 1) {
                            ?><span class="label label-success">&nbsp;</span><?php
                        } else {
                            ?>
                            <span class="label label-danger">&nbsp;</span>
                            <?php
                        } ?></td>
                    <td><?php echo $account['masterAccountDescription'] ?></td>
                    <td>
                        <span class="pull-right"><a onclick="refer_back_chart_of_accont(<?php echo $account['GLAutoID'] ?>)">
                                <span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat refer-back-icon"></span>
                            </a>
                        </span>
                    </td>
                </tr>
            <?php }
        }
    }
    ?>
    </tbody>
</table>

<script>

    $('#cTable').on('click', 'tr', function (e) {
        $('#cTable').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#cTable tr').each(function () {
            $(this).removeClass('highlight');
        });
        if(searchtext !==''){
        $('#cTable tr').each(function () {
            if ($(this).find('td').text().toLowerCase().indexOf(searchtext.toLowerCase()) == -1) {

                $(this).removeClass('highlight');
            }
            else {
                $(this).addClass('highlight');
            }
        });
        }
    }


</script>