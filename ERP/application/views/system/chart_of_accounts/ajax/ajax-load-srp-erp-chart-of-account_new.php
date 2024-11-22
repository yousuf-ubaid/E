<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$companyID = $this->common_data['company_data']['company_id'];
?>
<style>
    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
        padding: 4px;
    }

    .master {
        color: black;
        font-weight: bolder;
        font-size: 12px;
        background-color: #fbfbfb;
        padding: 4px;
    }

    .subdetails {
        font-size: 12px;
        padding: 4px;
    }

    .balance {
        float: right !important;
        width: 60px;
        text-align: right;
    }

    .common {
        float: right !important;
        width: 10px;
    }

    .status {
        float: right !important;
        width: 60px;
        text-align: center;
    }

    .edit {
        float: right !important;
        width: 10px;
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <table id="cTable" class="table " style="width: 100%">
            <thead>
            <tr>
                <th style="width:85%">GL <?php echo $this->lang->line('common_account')?><!--GL Account--></th>
                <th style="width:5%"><?php echo $this->lang->line('common_balance')?><!--Balance--></th>
                <th style="width:5%"><?php echo $this->lang->line('common_status')?><!--Status--></th>
                <th style="width:3%"></th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php
        foreach ($header as $row) {
            echo "<div class='header'>" . $row['CategoryTypeDescription'] . '</div>';
            $details = $this->db->query("SELECT ca.GLAutoID,ca.levelNo,ca.GLDescription,ca.masterAutoID,ca.systemAccountCode,ca.GLSecondaryCode,companyReportingAmount,companyReportingCurrencyDecimalPlaces,ca.isActive FROM srp_erp_chartofaccounts ca LEFT JOIN (SELECT SUM(companyReportingAmount) AS companyReportingAmount,GLAutoID,companyReportingCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE companyID = {$companyID} GROUP BY srp_erp_generalledger.GLAutoID) gl ON (gl.GLAutoID = ca.GLAutoID) WHERE ca.companyID = {$companyID} AND ca.levelNo IS NOT NULL AND accountCategoryTypeID = {$row['accountCategoryTypeID']} GROUP BY ca.GLAutoID")->result_array();

            printListRecursive($details);
        }

        function printListRecursive(&$details, $parent = 0)
        {
            $foundSome = false;
            $class = '';
            for ($i = 0, $c = count($details); $i < $c; $i++) {
                if ($details[$i]['masterAutoID'] == $parent) {
                    if ($foundSome == false) {
                        echo '<ul>';
                        $foundSome = true;
                    }
                    if ($details[$i]['isActive'] == 1) {
                        $status = "<span class='label label-success'>&nbsp;</span>";
                    } else {
                        $status = "<span class='label label-danger'>&nbsp;</span>";
                    }
                    if ($details[$i]['levelNo'] == 0) {
                        echo '<li class= "master">' . $details[$i]['systemAccountCode'] . " - " . $details[$i]['GLSecondaryCode'] . " - " . $details[$i]['GLDescription'] . '</li>';
                    } else {
                        echo '<li class="subdetails">' . $details[$i]['systemAccountCode'] . " - " . $details[$i]['GLSecondaryCode'] . " - " . $details[$i]['GLDescription'] . '<span class="edit"><a onclick="edit_chart_of_accont(' . $details[$i]['GLAutoID'] . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span><span class="status">' . $status . '</span><span class="balance">' . number_format($details[$i]['companyReportingAmount'], $details[$i]['companyReportingCurrencyDecimalPlaces']) . '</span></li>';
                    }

                    printListRecursive($details, $details[$i]['GLAutoID']);
                }
            }
            if ($foundSome) {
                echo '</ul>';
            }
        }

        ?>
    </div>
</div>
