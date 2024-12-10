<?php
$decimalPlace = 2;
if ($detail) { ?>
    <!--   <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="auditreport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php /*echo current_companyName(); */?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Audit Report</strong></div>
        </div>
    </div>-->
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 90px;width: 20%;"  src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>

                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h4>  <strong>Audit Report</strong></h4>
                            </td>


                        </tr>
                        <tr>
                        </tr>

                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <br>
    <div>
        <table id="tbl_rpt_audit" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Column Name</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>Updated Time</th>
                <th>Updated By</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $x = 1;
            foreach ($detail as $val) {
                ?>
                <tr>
                    <td width="10px;"><?php echo $x ?></td>
                    <td><?php echo $val['projectName'] ?></td>
                    <td><?php echo $val['display_name'] ?></td>
                    <td><?php echo $val['display_old_val'] ?></td>
                    <td><?php echo $val['display_new_val'] ?></td>
                    <td><?php echo $val['timestamp'] ?></td>
                    <td><?php echo $val['updateemployee'] ?></td>
                </tr>
                <?php
                $x++;
            }

            ?>

            </tbody>
        </table>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">No Records found</div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_audit').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>