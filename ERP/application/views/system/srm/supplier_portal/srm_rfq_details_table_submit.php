<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="table-responsive">
    <table class="table">
        <thead class='thead'>
        <tr>

            <th style="width: 10%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>item
            </th>
            <th style="width: 5%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>QTY
            </th>
            <th style="width: 5%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class=' tablethcol2'>UOM
            </th>

            <!--Product-->
            <th style="min-width: 20%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Delivery Date
            </th><!--UOM-->
            <th style="width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Unit Price
            </th><!--Delivery Date-->
            <th style="min-width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>QTY
            </th><!--Narration-->
            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Discount %
            </th><!--Qty-->
            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>Tax %
            </th><!--Qty-->
            <th style="width: 12%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tablethcoltotal'>Total
            </th><!--Price-->
    
            
            <!-- <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                class='tableth'>
            </th> -->
        </tr>
        </thead>
        <tbody id="salesquotation_body">
        <?php
        $total = 0;
        $num = 1;
        $lineTotal = 0;
        $TaxTotal = 0;
        $SubTotalLine = 0;
        $discountamt = 0;
        $grandTotal = 0;
        $subtotal = 0;
        if (!empty($detail)) {
        foreach ($detail as $val) {
        // if ($val['selectedByCustomer'] == 1) {
        //     $status = "checked";
        // } else {
        //     $status = "";
        // }

            ?>


        <tr>
            

            <td class="text-left tableth" style="max-width: 10%">
            <input type="hidden" name="inquiryDetailID[]" value="<?php echo $val['inquiryDetailID'] ?>">
            <label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['itemName']; ?>
                    <label><hr>&nbsp; &nbsp;&nbsp; &nbsp;
                    <b>Technical Specification</b>
                    <!-- <div class="row"> -->
                          <!-- <div class="form-group col-sm-12" style="margin-top: 4%;"> -->
                              <p><?php echo $val['supplierTechnicalSpecification'] ?></p>
                          <!-- </div> -->
                      <!-- </div> -->
                    </td>
            </td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['requestedQty'],2)?>
                    <label></td>
            </td>
            <td class="text-right tablethcol2" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['UnitShortCode']?>
                    <label></td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo $val['supplierExpectedDeliveryDate'] ?><label>
            </td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['supplierPrice'],2)?><label>
            </td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['supplierQty'],2)?><label>
            </td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['supplierDiscountPercentage'],2)?><label>
            </td>
            <td class="text-right tableth" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['supplierTaxPercentage'],2)?><label>
            </td>
            <td class="text-right tablethcoltotal" style="width: 10%"><label
                        style="color:#636363ad;font-family: inherit;font-weight: 100;font-size: 13px;"><?php echo number_format($val['lineSubTotal'],2)?><label>
            </td>
            




        </tr>
        <?php
        $num++;
        $total += 1;
        $grandTotal += $lineTotal;
        $subtotal += $SubTotalLine;
        $discountamt += 1;
        $TaxTotal += 1;
        }
        } else {
            $norecfound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="7" class="text-center">' . $norecfound . '</td></tr>';
        } ?><!--No Records Found-->
        </tbody>
        <tfoot>
        <tr>
            <!-- <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td> -->
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="6">

                SUB TOTAL
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <input type="hidden" name="subt" id="subt">
                <!-- <div id="tot_totalValue" style="text-align: right">0.00</div> -->
                <?php echo number_format($is_submit['subTotal'], $company['company_default_decimal']) ?>
            </td>
        </tr>
        <tr>
            <!-- <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td> -->
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="6">
                DISCOUNT
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <input type="hidden" name="dist" id="dist">
                <!-- <div id="tot_totalValuediscount" style="text-align: right">0.00</div> -->
                <?php echo number_format($is_submit['discountPrice'], $company['company_default_decimal']) ?>
            </td>
        </tr>
        <tr>
            <!-- <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td> -->
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="6">
                
                TAX
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right" colspan="3">
                <input type="hidden" name="tax" id="tax">
                <!-- <div id="tot_tax" style="text-align: right">0.00</div> -->
                <?php echo number_format($is_submit['taxPrice'], $company['company_default_decimal']) ?>
            </td>
        </tr>
        <tr style="border-bottom: 2px solid #ffffff;">
            <!-- <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                class="text-right">&nbsp;
            </td> -->
            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                class="text-right" colspan="6">
               
                GRAND TOTAL
            </td>
            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                class="text-right" colspan="3">
                <input type="hidden" name="gtot" id="gtot">
                <!-- <div id="tot_grandtotal" style="text-align: right">0.00</div> -->
                <?php echo number_format($is_submit['grandTotal'], $company['company_default_decimal']) ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<script>


    var quatationdetailid = '<?php echo $quatationId?>'
    var companyID = '<?php echo $companyID?>'
    var quatationId = '<?php echo $quatationId?>'
    $('input').on('ifChecked', function (event) {
        update_item_detail_active(this.value, 1,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId);

    });

    $('input').on('ifUnchecked', function (event) {
        update_item_detail_active(this.value, 2,'<?=$csrf['name'];?>','<?=$csrf['hash'];?>',quatationId,companyID,quatationId);

    });

    $(document).ready(function () {
$('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: 'YYYY-MM-DD',
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }

        }).on('dp.change', function (ev) {

        });
    });
    function update_item_detail_active(detailid,val,csrf,hash,quatationId,companyID,quatationId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {DetailID: detailid,val:val,companyID:companyID,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/save_sales_qutation_accepted_item'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if(data[0]='s')
                {
                    detailtable(quatationId,companyID,csrf,hash);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
    function save_customer_remarks(detailid,val,csrf,hash,quatationId,companyID) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {DetailID: detailid,val:val,companyID:companyID,csrf_token:hash},
            url: "<?php echo site_url('QuotationPortal/save_sales_qutation_accepted_item_remarks'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if(data[0]='s')
                {
                    detailtable(quatationId,companyID,csrf,hash);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }
    var currency_decimal = 2;
    function calculateTotal(element) {

        var expectedQty = $(element).closest('tr').find('.Qty').val();
        var amount = $(element).closest('tr').find('.unitcost').val();
        var discountamt = 0;
        var discount = $(element).closest('tr').find('.discount').val();
         //var ItemTax = $(element).closest('tr').find('.ItemTax').val();
         var Taxpercentage = $(element).closest('tr').find('.item_taxPercentage').val();
        var taxamount = 0;
        var total = 0;

        taxamount = ((parseFloat(expectedQty) * parseFloat(amount))/ 100)* parseFloat(Taxpercentage);


        if(discount > 0)
        {
            discountamt = (parseFloat(expectedQty) * parseFloat(amount) / 100) * parseFloat(discount);
            total = ((parseFloat(expectedQty) * parseFloat(amount)) - parseFloat(discountamt)) + parseFloat(taxamount);

            $(element).closest('tr').find('.discountamt').val(discountamt);
            $(element).closest('tr').find('.discountamountcal').val(discountamt);
        }else
        {
            total = (parseFloat(expectedQty) * parseFloat(amount)) + parseFloat(taxamount);
        // alert(total);
        }

        $(element).closest('tr').find('.totalcost').val(parseFloat(total).toFixed(currency_decimal));

        $(element).closest('tr').find('.totalwithoutdedu').val(((parseFloat(expectedQty) * parseFloat(amount))).toFixed(currency_decimal));
        $(element).closest('tr').find('.taxamt').val(parseFloat(taxamount).toFixed(currency_decimal));

    }

    function rawmaterialCostTotal() {
            var tot_Qty = 0;
            var tot_UnitCost = 0;
            var tot_TotalCost = 0;
            $('#salesquotation_body tr').each(function () {
                var tot_value = getNumberAndValidate($('td', this).eq(8).find('.totalwithoutdedu').val());
                tot_TotalCost += tot_value;
            });

            $("#tot_totalValue").text(commaSeparateNumber(tot_TotalCost, currency_decimal));
            $("#subt").val(commaSeparateNumber(tot_TotalCost, currency_decimal));
           // calculateTotalCost();
    }

    function getNumberAndValidate(thisVal) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        //thisVal = thisVal.toFixed(currency_decimal);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    function cal_discount(element) {

        if (element.value < 0 || element.value > 100 || element.value == '') {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
            $(element).closest('tr').find('.Qty').val(0);
            $(element).closest('tr').find('.unitcost').val(0);
            $(element).closest('tr').find('.totalcost').val(0);
        } else {
            var Qty = parseFloat($(element).closest('tr').find('.Qty').val());
            var unitcost = parseFloat($(element).closest('tr').find('.unitcost').val());
            var estimatedAmount = (parseFloat(Qty)*parseFloat(unitcost));
            // var ItemTax = $(element).closest('tr').find('.ItemTax').val();
              var Taxpercentage = $(element).closest('tr').find('.item_taxPercentage').val();
            var taxamount = 0;



            if (estimatedAmount) {
                var discountamount = (estimatedAmount / 100) * parseFloat(element.value);

            taxamount = (((parseFloat(Qty) * parseFloat(unitcost))- parseFloat(discountamount))/ 100)* parseFloat(Taxpercentage);

                $(element).closest('tr').find('.totalcost').val((parseFloat(estimatedAmount) - parseFloat(discountamount))+ parseFloat(taxamount));
                $(element).closest('tr').find('.discountamt').val(discountamount);
                $(element).closest('tr').find('.discountamountcal').val(discountamount);
                 $(element).closest('tr').find('.taxamt').val(parseFloat(taxamount).toFixed(currency_decimal));
            }
        }
    }

    function rawmaterialCostTotal_discount() {
        var tot_Qty = 0;
        var tot_UnitCost = 0;
        var tot_TotalCost = 0;

        $('#salesquotation_body tr').each(function () {
            var tot_value = getNumberAndValidate($('td', this).eq(6).find('.discountamountcal').val());
            tot_TotalCost += tot_value;
        });
      
        $("#tot_totalValuediscount").text(commaSeparateNumber(tot_TotalCost, currency_decimal));
        
        $("#dist").val(commaSeparateNumber(tot_TotalCost, currency_decimal));
    }

    function   grandtotal() {

        var tot_TotalCostdiscount = 0;
        var tot_TotalCostsubtotal = 0;
        var tot_grandtotal = 0;
        var tot_TotalCostTax = 0;
        $('#salesquotation_body tr').each(function () {
            var tot_valuedis = getNumberAndValidate($('td', this).eq(6).find('.discountamountcal').val());
            var tot_valuesub = getNumberAndValidate($('td', this).eq(8).find('.totalwithoutdedu').val());
            var tot_valueta = getNumberAndValidate($('td', this).eq(7).find('.taxamt').val());
            tot_TotalCostdiscount += tot_valuedis;
            tot_TotalCostsubtotal += tot_valuesub;
            tot_TotalCostTax += tot_valueta;
            tot_grandtotal += ((parseFloat(tot_valuesub) - parseFloat(tot_valuedis)))+ parseFloat(tot_valueta);

        });

       
    
        $("#tot_grandtotal").text(commaSeparateNumber(tot_grandtotal, currency_decimal));
       // gtot
        $("#gtot").val(commaSeparateNumber(tot_grandtotal, currency_decimal));
        // calculateTotalCost();
    }

    function fetch_related_tax(type,element) {
        $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'type': type},
        url: "<?php echo site_url('Crm/itemtax'); ?>",
        success: function (data) {
            $(element).closest('tr').find('.ItemTax').empty()
            var mySelect = $(element).parent().closest('tr').find('.ItemTax');
            mySelect.append($('<option></option>').val('').html('Select Tax Type'));
            if (!jQuery.isEmptyObject(data)) {
                $.each(data, function (val, text) {
                    mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['tax']));
                });
            }/*$data_arr[trim($row['taxMasterAutoID'] ?? '')] = trim($row['taxShortCode'] ?? '') . ' | ' . trim($row['taxDescription'] ?? '') . ' | ' . trim($row['taxPercentage'] ?? '') . ' %';*/
        }, error: function () {
            swal("Cancelled", "Your " + value + " file is safe :)", "error");
        }
        });
    }

    function fetch_tax_percentage(element) {
        if (element.value != 0) {
            var result = $(element).children(':selected').text().split('|');
            var res = parseFloat(result[2].replace("%", "")).toFixed(2);
            $(element).closest('tr').find('.item_taxPercentage').val(res);
            $(element).closest('tr').find('.item_taxPercentage').prop('readonly', false)
        } else {
            $(element).closest('tr').find('.item_taxPercentage').val(0);
            $(element).closest('tr').find('.item_taxPercentage').prop('readonly', true)
        }
    }

    function caltaxamt(element) {
        var expectedQty = $(element).closest('tr').find('.Qty').val();
        var amount = $(element).closest('tr').find('.unitcost').val();
        var discountamt = 0;
        var discount = $(element).closest('tr').find('.discount').val();
        var ItemTax = $(element).closest('tr').find('.ItemTax').val();
        var Taxpercentage = $(element).closest('tr').find('.item_taxPercentage').val();
        var taxamount = 0;
        var total = 0;
        discountamt = (parseFloat(expectedQty) * parseFloat(amount) / 100) * parseFloat(discount);
        taxamount = (((parseFloat(expectedQty) * parseFloat(amount))- parseFloat(discountamt))/ 100)* parseFloat(Taxpercentage);


        if(discount > 0)
        {
            total = (((parseFloat(expectedQty) * parseFloat(amount)) - parseFloat(discountamt)) + parseFloat(taxamount));
            $(element).closest('tr').find('.discountamt').val(discountamt)
        }else
        {
            total = (parseFloat(expectedQty) * parseFloat(amount)) + parseFloat(taxamount);
            // alert(total);
        }

        $(element).closest('tr').find('.totalcost').val(parseFloat(total));
        $(element).closest('tr').find('.totalwithoutdedu').val(((parseFloat(expectedQty) * parseFloat(amount))).toFixed(currency_decimal));
        $(element).closest('tr').find('.taxamt').val(parseFloat(taxamount));

    }
    function rawmaterialCostTotal_Tax() {
        var tot_Qty = 0;
        var tot_UnitCost = 0;
        var tot_TotalCost = 0;

        $('#salesquotation_body tr').each(function () {
            var tot_value = getNumberAndValidate($('td', this).eq(7).find('.taxamt').val());
            tot_TotalCost += tot_value;
        });

        $("#tot_tax").text(commaSeparateNumber(tot_TotalCost, currency_decimal));
        $("#tax").val(commaSeparateNumber(tot_TotalCost, currency_decimal));
        
    }

    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.totalcost').val());

        var expectedQty = $(element).closest('tr').find('.Qty').val();
        var amount = $(element).closest('tr').find('.unitcost').val();
        var discountamt = $(element).closest('tr').find('.discountamountcal').val();

        var total = 0;
        var total1 = 0;
        var Newtotal = 0;
        var Taxpercentage = $(element).closest('tr').find('.item_taxPercentage').val();
        var taxamount = 0;
        taxamount = (((parseFloat(expectedQty) * parseFloat(amount))- parseFloat(discountamt))/ 100)* parseFloat(Taxpercentage);
        total = (parseFloat(expectedQty) * parseFloat(amount))
        if (element.value > total) {
            myAlert('w', 'Discount amount should be less than or equal to Amount');
            $(element).closest('tr').find('.discount').val(0);
            $(element).closest('tr').find('.Qty').val(0);
            $(element).closest('tr').find('.unitcost').val(0);
            $(element).closest('tr').find('.totalcost').val(0);
            $(element).val(0)
        } else {
            if (total) {

                total1 = ((parseFloat(expectedQty) * parseFloat(amount)) - parseFloat(discountamt)) + parseFloat(taxamount);
                $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / total) * 100).toFixed(3))

                $(element).closest('tr').find('.totalcost').val(parseFloat(total1));
            }
        }
    }
function validateFloatKeyPress(el, evt) {
//alert(currency_decimal);

var charCode = (evt.which) ? evt.which : event.keyCode;
var number = el.value.split('.');
if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
}
//just one dot
if(number.length>1 && charCode == 46){
    return false;
}
//get the carat position
var caratPos = getSelectionStart(el);
var dotPos = el.value.indexOf(".");
if( caratPos > dotPos && dotPos>-(currency_decimal-1) && (number[1] && number[1].length > (currency_decimal-1))){
    return false;
}
return true;
}

function getSelectionStart(o) {
            if (o.createTextRange) {
                var r = document.selection.createRange().duplicate()
                r.moveEnd('character', o.value.length)
                if (r.text == '') return o.value.length
                return o.value.lastIndexOf(r.text)
            } else return o.selectionStart
        }


        function commaSeparateNumber(val, dPlace = 2) {
        var toFloat = parseFloat(val);
        var a = toFloat.toFixed(dPlace);
        while (/(\d+)(\d{3})/.test(a.toString())) {
            a = a.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return a;
    }

    function removeCommaSeparateNumber(val) {
        return parseFloat(val.replace(/,/g, ""));
    }
   



</script>