<?php
$this->load->helper('configuration_helper');
$projectExist = project_is_exist();
?>
<div class="modal-body">

    <table class="table table-bordered table-striped table-condesed ">
        <thead>
        <tr>
            <th colspan="4">Invoice Details</th>
            <th colspan="6">Credit Note <span class="currency">( <?php echo $master['transactionCurrency']; ?> )</span>
            </th>
        </tr>
        <tr>
            <th style="width: 3%">#</th>
            <th style="width: 25%">Invoice Code</th>
            <th style="width: 10%">Amount</th>
            <!-- <th style="width: 10%">Paid </th>
            <th style="width: 10%">Dabit </th> -->
            <th style="width: 10%">Balance</th>
            <th style="width: 12%">GL Code</th>
            <th style="width: 10%">Segment</th>
            <?php if ($projectExist == 1) { ?>
                <th style="width: 10%">Project</th>
                <th style="width: 10%">Project Category</th>
                <th style="width: 10%">Project Subcategory</th>
            <?php } ?>
            <th style="width: 10%">Amount</th>
        </tr>
        </thead>
        <tbody id="table_inv_body">
        <?php


        if (!empty($detail)) {
            $segment_arr = fetch_segment();
            $gl_code_arr = dropdown_all_revenue_gl();
            $x = $PageStartNumber;
            for ($i = 0;
                 $i < count($detail);
                 $i++) {
                $balance = $detail[$i]['transactionAmount'] - ($detail[$i]['receiptTotalAmount'] + $detail[$i]['creditNoteTotalAmount']+ $detail[$i]['advanceMatchedTotal']+ $detail[$i]['salesreturnvalue']);
                if (number_format($balance, $master['transactionCurrencyDecimalPlaces'], '.', '') > 0) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    echo '<td>' . $detail[$i]['invoiceCode'] . ' - ' . $detail[$i]['invoiceDate'] . '</td>';
                    echo '<td class="text-right">' . number_format($detail[$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                    echo '<td class="text-right">' . number_format($balance, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                    echo '<td>' . form_dropdown('gl_code[]', $gl_code_arr, '', ' class="select2" id="gl_code_' . $detail[$i]['invoiceAutoID'] . '" style="width: 100px"') . '</td>';
                    echo '<td>' . form_dropdown('segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="select2" id="segment_' . $detail[$i]['invoiceAutoID'] . '" onchange="load_segmentBase_projectID_income(this,'.$detail[$i]['invoiceAutoID'].')" style="width: 100px"') . '</td>';
                    //echo '<td class="text-right">'.number_format($detail[$i]['receiptTotalAmount'],$master['transactionCurrencyDecimalPlaces']).'</td>';
                    //echo '<td class="text-right">'.number_format($detail[$i]['creditNoteTotalAmount'],$master['transactionCurrencyDecimalPlaces']).'</td>';
                    if ($projectExist == 1) {
                        echo '<td> <div class="div_projectID_income"><select name="projectID"><option value="">Select Project</option></select></div> </td>';
                        echo ' <td>' . form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID" id="project_categoryID_' . $detail[$i]['invoiceAutoID'] . '" onchange="fetch_project_sub_category_invoice(this,this.value)" style="width: 100px"') . '</td>';
                        echo ' <td>' . form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID" id="project_subCategoryID_' . $detail[$i]['invoiceAutoID'] . '" style="width: 100px"') . '</td>';
                    }
                    echo '<td class="text-right"><input type="hidden" name="code[]" style="width: 100px"
                                      id="code_' . $detail[$i]['invoiceAutoID'] . '"
                                      value="' . $detail[$i]['invoiceCode'] . '"><input type="text" name="amount[]"
                                                                                        style="width: 100px"
                                                                                        id="amount_' . $detail[$i]['invoiceAutoID'] . '"
                                                                                        onkeyup="select_check_box(this,' . $detail[$i]['invoiceAutoID'] . ',' . number_format($balance, $master['transactionCurrencyDecimalPlaces'], '.', '') . ')"
                                                                                        onkeypress="return validateFloatKeyPress(this,event)"
                                                                                        class="number"></td>
        ';
                    echo '
        <td class="text-right" style="display:none;"><input class="checkbox"
                                                            id="check_' . $detail[$i]['invoiceAutoID'] . '"
                                                            type="checkbox"
                                                            value="' . $detail[$i]['invoiceAutoID'] . '"></td>
        ';
                    echo '</tr>';
                    $x++;
                }


            }
        } else {
            echo '
        <tr class="danger">';
            echo '
            <td class="text-center" colspan="9">No Recode Found</td>
            ';
            echo '
        </tr>
        ';
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    creditNoteMasterAutoID = <?php echo json_encode(trim($master['creditNoteMasterAutoID'] ?? '')); ?>;
    currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;

    $( document ).ready(function() {
        number_validation();
        //$(".select2").select2();

    });


    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false);

        if (data.value > 0) {
            if (total >= data.value) {
                $("#check_" + id).prop("checked", true);
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
                myAlert('w', 'You can not enter an invoice amount greater than selected Credit Note Amount');
            }
        }
    }

    function save_debit_base_items() {
        var selected = [];
        var code = [];
        var amount = [];
        var segment = [];
        var segment_dec = [];
        var gl_code = [];
        var gl_code_dec = [];
        var project = [];
        var project_subCategoryID = [];
        var project_categoryID = [];

        $('#table_inv_body input:checked').each(function () {
            selected.push($(this).val());
            code.push($('#code_' + $(this).val()).val());
            amount.push($('#amount_' + $(this).val()).val());
            project.push($('#projectID_' + $(this).val()).val());
            project_subCategoryID.push($('#project_subCategoryID_' + $(this).val()).val());
            project_categoryID.push($('#project_categoryID_' + $(this).val()).val());
            segment.push($('#segment_' + $(this).val()).val());
            segment_dec.push($('#segment_' + $(this).val() + ' option:selected').text());
            gl_code.push($('#gl_code_' + $(this).val()).val());
            gl_code_dec.push($('#gl_code_' + $(this).val() + ' option:selected').text());
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'invoiceAutoID': selected,
                    'invoiceCode': code,
                    'project': project,
                    'project_subCategoryID': project_subCategoryID,
                    'project_categoryID': project_categoryID,
                    'segment': segment,
                    'segment_dec': segment_dec,
                    'amounts': amount,
                    'gl_code': gl_code,
                    'gl_code_dec': gl_code_dec,
                    'creditNoteMasterAutoID': creditNoteMasterAutoID
                },
                url: "<?php echo site_url('Receivable/save_credit_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if (data) {
                        $('#cn_detail_modal').modal('hide');
                        setTimeout(function () {
                            fetch_cn_details();
                        }, 300);
                    }
                }, error: function () {
                    $('#cn_detail_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function load_segmentBase_projectID_income(segment,detailID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple_dn"); ?>',
            dataType: 'html',
            data: {segment: segment.value, detailID:detailID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(segment).closest('tr').find('.div_projectID_income').html(data);
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function fetch_project_sub_category_invoice(element,categoryID) {
        var projectID = $(element).closest('tr').find('.projectID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_project_sub_category"); ?>',
            dataType: 'json',
            data: {categoryID: categoryID,projectID: projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).closest('tr').find('.project_subCategoryID').empty();
                var mySelect = $(element).closest('tr').find('.project_subCategoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Subcategory'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).html(text['description']));
                    });
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
</script>