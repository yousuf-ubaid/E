



<!--Translation added by Naseek-->





<?php
$fedeclarationMasterID = $output['fedeclarationMasterID'];
$declarationMasterData = $this->db->query("SELECT * FROM srp_erp_ot_fixedelementdeclarationmaster
                                            WHERE fedeclarationMasterID={$fedeclarationMasterID}")->row_array();

$declarationdetailTable = $this->db->query("SELECT feDeclarationDetailID,feDeclarationMasterID,employeeNo,
                                            transactionAmount,srp_erp_ot_fixedelements.fixedElementDescription,
                                            effectiveDate, payDate
                                            FROM srp_erp_ot_fixedelementdeclarationdetails LEFT JOIN srp_erp_ot_fixedelements ON srp_erp_ot_fixedelements.fixedElementID = srp_erp_ot_fixedelementdeclarationdetails.fixedElementID
                                            WHERE feDeclarationMasterID ={$fedeclarationMasterID} ORDER BY employeeNo")->result_array();
?>
<style>
    .declarationTable td:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable th:not(:first-child) {
        width: 100px !important;
    }

    /*.assetRegisterTable td:last-child,.assetRegisterTable th:last-child {
        background-color: rgba(119, 119, 119, 0.33);
    }*/

    /*.assetRegisterTable tr:not(:first-child):hover td:not(:last-child) {*/
    .declarationTable tbody td:not(:first-child):not(:last-child):hover {
        cursor: pointer !important;
        background-color: #DEDEDE;
    }

    .drill-table {
        text-align: center;
        background-color: #dedede;
        padding-top: 8px;
        padding-bottom: 8px;
        line-height: 1.42857143;
        font-size: 12px !important;
        font-weight: bold;
    }
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_over_time_declaration_code');?><!--Declaration Code--></td>
                    <td class="bgWhite"><strong><?php echo $output['documentSystemCode'] ?></strong></td>
                    <td colspan="2"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                    <td class="bgWhite"><strong><?php echo $output['transactionCurrency']; ?></strong></td>
                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $output['Description'] ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>

<h4>
    <?php echo $this->lang->line('hrms_over_time_fixed_element_declaration_detail');?><!--Fixed Element Declaration Detail-->
    <button type="button" class="btn btn-primary btn-sm pull-right"
            onclick="open_salaryDeclarationModal()"><i
            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_detail');?><!--Add Detail-->

    </button>
</h4>
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_category');?><!--Category--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_over_time_effective_date');?><!--Effective Date--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_amount');?><!--Amount--></td>
                    <td style="font-weight: 700; text-align: center"></td>
                </tr>
                <?php
                $newArray = array();
                $empTotal = 0;
                $totalbalance = 0;
                $n = 0;
                if (!empty($declarationdetailTable)) {
                    foreach ($declarationdetailTable as $val) {
                        $newArray[$val['employeeNo']][] = $val;
                    }
                    foreach ($newArray as $key => $value) {
                        $n++;
                        $empTotal = 0;
                        $empname = 0;
                        $totalbalance = 0;
                        foreach ($value as $val) {
                            if ($empname == 0) {
                                ?>
                                <tr>
                                    <td colspan="4"><strong>
                                            <?php

                                            $empid = fetch_employeeNo($val['employeeNo']);
                                            echo '[ ' . $n . ' ] ' . $empid['ECode'] . '-' . $empid['Ename2'];
                                            $empname++;
                                            ?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td><?php echo $val['fixedElementDescription']; ?></td>
                                <td><?php $convertFormat = convert_date_format();
                                    echo format_date($val['effectiveDate'], $convertFormat); ?></td>
                                <td style="text-align: right"><?php
                                    $amt = $val['transactionAmount'];
                                    echo number_format($val['transactionAmount'], 2);
                                    ?></td>
                                <td style="text-align: right"><a
                                        onclick="delete_item(<?php echo $val['feDeclarationDetailID']; ?>,<?php echo $val['feDeclarationMasterID']; ?>);"><span
                                            class="glyphicon glyphicon-trash"
                                            style="color:rgb(209, 91, 71);"></span></a></td>
                            </tr>
                            <?php
                            $empTotal += $val['transactionAmount'];
                        }
                        ?>
                        <tr>
                            <td colspan="2" style="background-color: rgba(119, 119, 119, 0.33)">Total</td>
                            <td class="text-right total"><?php echo number_format($empTotal, 2); ?></td>

                            <td style="background-color: rgba(119, 119, 119, 0.33"></td>

                        </tr>
                        <?php

                    }
                } else { ?>
                    <tr>
                        <td colspan="7" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php

            $confirmed = $this->lang->line('common_confirmed');
            $approved = $this->lang->line('common_approved');
            if (!empty($declarationdetailTable)) {
                if ($declarationMasterData['confirmedYN'] != 1) { ?>
                    <div id="sdd_footer" style="margin: 16px 0px 1px 0px;" class="pull-right">
                        <button class="btn btn-success submitWizard" onclick="confirmSalaryDeclaration()"><?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                        </button>
                    </div>
                <?php } else {
                    if ($declarationMasterData['confirmedYN'] == 1 && $declarationMasterData['approvedYN'] == 1) {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i>'.$confirmed.' <!--Confirmed--> &nbsp;&nbsp;&nbsp;&nbsp; &amp; &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-check"></i>'.$approved.'  <!--Approved--> </div>  ';
                    } else {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i>'.$confirmed.'  <!--Confirmed--></div>  ';
                    }

                }
            } ?>
        </div>
    </div>
</div>


<script type="text/javascript">

    var documentDate = '<?php echo convert_date_format($declarationMasterData['documentDate']); ?>';
    var isInitialDeclaration = '<?php echo $declarationMasterData['isInitialDeclaration']; ?>';

    $('#MasterCurrency').val('<?php echo $output['transactionCurrencyID'];?>');

    function open_salaryDeclarationModal() {
        getDrilldownTableData();
        $("#employee").prop("disabled", false).val(null).trigger("change");
        $('#declaration_save_detail_form').bootstrapValidator('resetForm', true);
        //$("#amount").val('');
        $("#effectiveDate").val(documentDate).attr('data-value', documentDate);
        //$("#payDate").val(documentDate);
        //$('#declaration_save_detail_form').bootstrapValidator('resetField', 'effectiveDate');
        $("#fixedElementDeclarationDetailModal").modal({backdrop: "static"});
    }

    function getEffectiveDate() {

        //$("#salarySubCatID").val('').removeAttr('onchange').change().attr('onchange', 'getMasterCatType(this)');
        //$('#declaration_save_detail_form').bootstrapValidator('resetField', 'cat[]');

        $(".salaryType, #currentAmount, #newAmount, #amount").val('');
        if (isInitialDeclaration == 1) {
            $("#effectiveDate").val($('#employee :selected').attr('data-value'));
        }
    }

</script>