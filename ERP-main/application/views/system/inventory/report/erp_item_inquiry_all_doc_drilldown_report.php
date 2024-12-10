<!---- =============================================
-- File Name : erp_item_inquiry_all_doc_drilldown_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 16 - February 2017
-- Description : This file contains item inquiry all document drilldown.

-- REVISION HISTORY
-- =============================================-->
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_all_doc_drilldwon', 'Item Ledger',$excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = 'generateDrilldownReportPdf()');
        } ?>
    </div>
</div>
<div id="tbl_all_doc_drilldwon">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Un approved document</div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php
            if (!empty($output)) {
            ?>
            <table class="borderSpace report-table-condensed" id="tbl_report">
                <thead class="report-header">
                <tr>
                    <th>Doc ID</th>
                    <th>Doc Code</th>
                    <th>Doc Date</th>
                    <th>Qty</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                $category = array();
                if (!empty($output)) {
                    foreach ($output as $val) {
                        $total += $val["currentStock"];
                        echo "<tr class='hoverTr'>";
                        echo "<td>" . $val["documentID"] . "</td>";
                        if ($type == 'html') {
                            echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["autoID"] . ')">' . $val["documentCode"] . '</a></td>';
                        }else{
                            echo '<td>' . $val["documentCode"] . '</td>';
                        }
                        echo "<td>" . $val["documentDate"] . "</td>";
                        echo "<td class='text-right'>" . $val["currentStock"] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <?php
                    echo "<td colspan='3'><strong>Total</strong></td>";
                    echo "<td class='reporttotal text-right'>" . $total . "</td>";
                    ?>
                </tr>
                </tfoot>
                <?php
                } else {
                    echo warning_message("No Records Found!");
                }
                ?>
            </table>
        </div>
    </div>
</div>
<script>
    $('.filterDate').datepicker({
        format: 'yyyy-mm-dd'
    });
</script>