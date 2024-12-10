<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$masterID = $masterData['vpMasterID'];

$confirmed = $masterData['confirmedYN'];

if($isGroupAccess == 1) {
    if ($totalEntries != count($detailRecords)) {
        if ($confirmed != 1) {
            $confirmed = 1;
            echo '<script type="text/javascript"> msg_popup("confirm-btn"); </script>';
        }
    }
}
$docDate = $masterData['documentDate'];
$docDateStr = convert_date_format($docDate);

$dPlace = $masterData['trCurrencyDPlaces'];
$disableDate = $isInitialDeclaration = $masterData['isInitialDeclaration'];
$isInitialDeclaration = ($masterData['isInitialDeclaration'] == 1)? 'Yes': 'No';
$date_format_policy = date_format_policy();
$title = $this->lang->line('hrms_payroll_variable_pay_declaration');
?>

<div class="table-responsive">
    <table style="width: 100%; margin-bottom: 10px" border="0px">
        <tbody>
        <tr>
            <td style="width:40%; height: 80px">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px"
                                 src="<?php echo $imgPath.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2>
                                <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px"><?=$title?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr/>

<div class="masterContainer">
    <div class="row" style="margin-right: 0px; margin-left: 0px;">
        <?php if($isPrint == 'N'){ ?>
        <div class="col-md-12 well">
            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $masterData['documentCode'] ?></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('hrms_payroll_initial_declaration');?></td>
                        <td class="bgWhite details-td" id="inv_type" width="200px"><strong><?php echo $isInitialDeclaration ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('hrms_payroll_document_date');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $docDateStr ?></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_description');?></td>
                        <td class="bgWhite details-td" id="documentCode" width="200px"><strong><?php echo $masterData['description'] ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table table-bordered table-condensed" style="background-color: #bed4ea;">
                    <tr>
                        <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                        <td class="bgWhite details-td" id="inv_type" width="200px">
                            <strong><?php echo $masterData['trCurr'] ?></strong>
                            <input type="hidden" id="docCurrency" value="<?php echo $masterData['trCurrencyID'];?>">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
        }
        else{
        ?>

        <div class="row" style="margin-bottom: 15px">
            <div class="col-md-12">
                <table class="table table-bordered table-condensed" id="SD-header-print-tb" style="background-color: #bed4ea; ">
                    <tr>
                        <td style="width: 110px;"><?php echo $this->lang->line('common_document_code');?></td>
                        <td class="bgWhite"><strong><?php echo $masterData['documentCode'] ?></strong></td>

                        <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_document_date');?></td>
                        <td class="bgWhite"> <?php echo $docDateStr; ?> </td>

                        <td ><?php echo $this->lang->line('common_currency');?></td>
                        <td class="bgWhite"><strong><?php echo $masterData['trCurr'];?></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><?php echo $this->lang->line('hrms_payroll_initial_declaration');?></td>
                        <td class="bgWhite"> <strong><?php echo $isInitialDeclaration ?></strong> </td>

                        <td><?php echo $this->lang->line('common_description');?></td>
                        <td class="bgWhite" colspan="3"><strong><?php echo $masterData['description'] ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php } ?>

        <div class="row">
            <div class="table-responsive">
                <table class="<?php echo table_class() ?> drill-table" >
                    <thead>
                    <tr>
                        <th style="width: 30px"> # </th>
                        <th style=""> <?php echo $this->lang->line('common_type');?>  </th>
                        <th style="min-width: 300px">  <?php echo $this->lang->line('common_category');?>  </th>
                        <th style="width: 110px"> <?php echo $this->lang->line('hrms_payroll_current_amount')?> <!--Current Amount--> </th>
                        <th style="width: 100px"><!-- New Amount--> <?php echo $this->lang->line('hrms_payroll_new_amount')?></th>
                        <th style="width: 105px"> <!--Effective Date--> <?php echo $this->lang->line('hrms_payroll_effective_date')?> </th>
                        <th style="width: 200px"> <?php echo $this->lang->line('common_narration')?><!--Narration--> </th>
                        <th style="width: 60px"> <?php echo $this->lang->line('hrms_payroll_isactive')?><!-- Is Active--> </th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $i = 1; $m = 0; $incAmountTot= 0;
                    if(!empty($detailRecords)){
                        $detailRecords = array_group_by($detailRecords, 'empID');

                        foreach ($detailRecords as $empID=>$row){
                            $currentAmountTot = 0; $newAmountTot = 0;
                            $firstRow = $row[0];
                            $empID = $firstRow['empID']; $empName =  $firstRow['ECode'].' | '.$firstRow['Ename2'];

                            echo '<tr>
                            <td class="right-align"><b>'.$i.'</b></td><td colspan="7"><b>'.$empName.'</b></td>                             
                          </tr>';


                            foreach ($row as $key=>$det){
                                $detID = $det['detailID']; $catID = $det['salaryCategoryID'];
                                $amount = round($det['amount'], $dPlace); $incAmountTot += $amount;
                                $currentAmount = round($det['currentAmount'], $dPlace); $currentAmountTot += $currentAmount;
                                $effectiveDate = $det['effectiveDate']; $effectiveDate = convert_date_format($effectiveDate);
                                $newAmount = (!empty($amount) && $amount != 0)? ($amount + $currentAmount): 0;
                                $newAmountTot += $newAmount; $newAmountTxt = $newAmount;
                                $isActive = ($det['isActive'] == 1)? 'Yes': 'No';

                                $type = ($det['salaryCategoryType'] == 'A')? 'Addition': 'Deduction';
                                $m++;

                                echo '<tr>
                                <td >&nbsp;</td>                                       
                                <td >'.$type.'</td>                                       
                                <td style="width: 200px">'.$det['description'].'</td>
                                <td class="right-align">'.number_format($currentAmount, $dPlace, '.', '').'</td>                                                                                                                                               
                                <td class="right-align">'.number_format($newAmountTxt, $dPlace, '.', '').'</td>                                        
                                <td style="text-align:center">'.$effectiveDate.'</td>
                                <td class="">'.$det['narration'].'</td>
                                <td style="text-align:center; width: 100px">'.$isActive.'</td>
                              </tr>';
                            }

                            $i++;
                        }
                    }
                    else{
                        $no_record_found = $this->lang->line('common_no_records_found');
                        echo '<tr><td colspan="9" align="center">'.$no_record_found.'</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
