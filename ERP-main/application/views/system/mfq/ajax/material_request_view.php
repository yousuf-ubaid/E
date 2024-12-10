<form name="frm_material_request" id="frm_material_request" method="post">
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 75px"
                                     src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3>
                                    <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?></strong>
                                </h3>
                                <h4>Material Request </h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Material Request Number </strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['MRCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Material Request Date </strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['requestedDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Reference Number</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['referenceNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="table-responsive">
        <table style="width: 100%;font-size:12px;">
            <tbody>
            <tr>
                <td style="width:20%;"><strong>Requested By </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:78%;"><?php echo $master['employeeName'] . ' (' . $master['employeeCode'] . ' ) '; ?></td>
            </tr>
            <tr>
                <td style="width:15%;"><strong>Warehouse </strong></td>
                <td><strong>:</strong></td>
                <td style="width:85%;"><select name="wareHouseAutoID" id="wareHouseAutoID"
                                               class="form-control searchbox">
                        <option value="">Please Select</option>
                        <?php foreach ($location as $locat) { ?>
                            <option value="<?php echo $locat['wareHouseAutoID']; ?>" <?php echo ($master['wareHouseAutoID'] == $locat['wareHouseAutoID']) ? 'selected' : ''; ?>><?php echo $locat['wareHouseDescription'] ?></option>
                        <?php }; ?>
                    </select><input type="hidden" name="mrAutoID"
                                    value="<?php echo $master['mrAutoID']; ?>"></td>
            </tr>
            <tr>
                <td><strong>Narration </strong></td>
                <td><strong>:</strong></td>
                <td colspan="4"><?php echo $master['comment']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="table-responsive pt-20 scroll-set-1">
        <br>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 10%">Item Code</th>
                <th class='theadtr' style="min-width: 40%">Item Description</th>
                <th class='theadtr' style="min-width: 10%">UOM</th>
                <th class='theadtr' style="min-width: 10%">Current Qty</th>
                <th class='theadtr' style="min-width: 10%">Requested Qty</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            $total_count = 0;
            if (!empty($detail['detail'])) {
                foreach ($detail['detail'] as $val) { ?>
                    <tr>
                        <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center;"><?php echo $val['itemSystemCode']; ?></td>
                        <td><?php echo $val['itemDescription']; ?></td>
                        <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right;"><?php echo $val['currentWareHouseStock']; ?></td>
                        <td style="text-align:right;"><input type="hidden" name="mrDetailID[]"
                                                             value="<?php echo $val['mrDetailID']; ?>"> <input
                                    type="number" class="number"
                                    name="qtyRequested[]"
                                    value="<?php echo $val['qtyRequested']; ?>">
                        </td>
                    </tr>
                    <?php
                    $num++;
                }
            } else {
                echo '<tr class="danger"><td colspan="6" class="text-center">No Records Found</td></tr>';
            } ?>
            </tbody>
        </table>
    </div>
</form>

