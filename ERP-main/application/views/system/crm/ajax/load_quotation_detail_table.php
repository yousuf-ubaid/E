<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$currencydecimalplaces =  $masterrec['transactionCurrencyDecimalPlaces'] ?? 0;


?>
<style>
    .hide {
        display: none;
    }

    .alert.alert-danger {
        border-top: 1px solid rgba(140, 0, 0, 0.4);
        border-bottom: 1px solid rgba(140, 0, 0, 0.4);
    }

    .alert.alert-success {
        border-top: 1px solid limegreen;
        border-bottom: 1px solid limegreen;
    }

    .alert {
        padding-left: 30px;
        margin-left: 15px;
        position: relative;
        font-size: 12px;
    }

    .alert {
        background-position: 2% 7px;
        background-repeat: no-repeat;
        background-size: auto 35px;
        background-color: rgba(0, 0, 0, 0);
        border: 0;
        min-width: auto !important;
        text-align: left;
        padding-left: 68px;
    }

    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-danger, .alert-error {
        color: #b94a48;
        background-color: #f2dede;
        border-color: #eed3d7;
    }

    .alert, .alert h4 {
        color: #c09853;
    }

    .alert {
        padding: 8px 35px 8px 14px;
        margin-bottom: 20px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .bordertype {
        border-left: 3px solid #daa520;
    }
    .bordertypePRO {
        border-left: 3px solid #f7f4f4;
    }
    .tableth {
        background-color: #f7f7f7;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcol2 {
        background-color: #ececec;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .tablethcoltotal {
        background-color: #fde49d;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    .vl {
        border-left: 3px solid #f7f4f4;
        height: 500px;
    }

    .buttonacceptanddecline {
        border-radius: 0;
    }
</style>
<div>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12">

                <div class="table-responsive">
                   <!-- <form action="" role="form" id="frm_quotationdet">-->
                        <input type="hidden" name="quotationid" id="quotationid" value="<?php echo $masterrec['quotationAutoID'] ?? null ?>">
                    <table class="table"  id="quotationtbl">
                        <thead class='thead'>
                        <tr>

                            <th style="min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth'><?php echo $this->lang->line('common_description')?><!--Description-->
                            </th>

                            <th style="min-width: 50%;text-align:left;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth'><?php echo $this->lang->line('common_uom')?><!--UOM-->
                            </th><!--Product-->
                            <th style="width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth'><?php echo $this->lang->line('common_qty')?><!--Qty-->
                            </th><!--UOM-->
                            <th style="width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tablethcol2'><?php echo $this->lang->line('common_price')?><!--Price-->
                            </th><!--Delivery Date-->
                            <th style="min-width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth' colspan="2"><?php echo $this->lang->line('common_discount')?><!--Discount--> %
                            </th><!--Narration-->
                            <th style="min-width: 10%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth' colspan="2"><?php echo $this->lang->line('common_tax')?><!--TAX--> %
                            </th><!--Qty-->

                            <th style="width: 8%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tablethcoltotal'><?php echo $this->lang->line('common_total')?><!--Total-->
                            </th>
                            <!--Price-->
                            <th style="min-width: 5%;text-align:center;color: #585858;font-family: tahoma;font-weight: 600;border-bottom: 2px solid #ffffff;"
                                class='tableth'><?php echo $this->lang->line('crm_delivery_date')?><!--Delivery Date-->
                            </th>


                            <th style="min-width: 5%">
                                <div class=" pull-right">
                                    <button type="button" data-text="Add"
                                            onclick="add_more_row()"
                                            class="button button-square button-tiny button-royal button-raised">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </th>

                        </tr>
                        </thead>
                        <tbody id="salesquotation_body">

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right">&nbsp;
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="8">
                                <?php echo $this->lang->line('common_sub_total')?><!--SUB TOTAL-->
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="1">
                                <div id="tot_totalValue" style="text-align: right">0.00</div>
                             <!--   --><?php /*echo number_format($subtotal, $master['transactionCurrencyDecimalPlacesqut']) */?>
                            </td>
                        </tr>
                        <tr>
                            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right">&nbsp;
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="8">
                                <?php echo $this->lang->line('common_discount')?><!--DISCOUNT-->
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="1">
                                <div id="tot_totalValuediscount" style="text-align: right">0.00</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right">&nbsp;
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="8">
                                TAX
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #f7f4f4;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right" colspan="1">
                                <div id="tot_tax" style="text-align: right">0.00</div>
                            </td>
                        </tr>
                        <tr style="border-bottom: 2px solid #ffffff;">
                            <td style="min-width: 85%;border-bottom: 2px solid #ffffff;color:#636363ad;font-family: inherit;font-weight: 500;font-size: 13px;"
                                class="text-right">&nbsp;
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                                class="text-right" colspan="8">
                                <?php echo $this->lang->line('common_grand_total')?><!--GRAND TOTAL-->
                            </td>
                            <td style="min-width: 85%;border-bottom: 1px solid #ffffff;color:#565555;font-family: tahoma;font-weight: bold;font-size: 13px;"
                                class="text-right" colspan="1">
                                <div id="tot_grandtotal" style="text-align: right">0.00</div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                </div>
                <br>
               <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button class="btn btn-primary" type="button"
                                    onclick="saveCustomerOrderDetails()">
                                Save
                            </button>
                        </div>
                    </div>
                </div>-->
               <!-- </form>-->
        </div>
    </div>

    <script>
        var currency_decimal = 2;
        $(document).ready(function () {

            initializeitemTypeahead();
            p_id = "<?php echo $masterrec['quotationAutoID'] ?? ''?> ";
            if (p_id) {
                load_standard_job_card(p_id);
            } else {
                init_standardjobcardform();
            }


        });


        function initializeitemTypeahead(id) {

            $('#f_search_' + id).autocomplete({
                serviceUrl: '<?php echo site_url();?>crm/fetch_productCode/?&qutDetailID='+$('#f_search_' + id).closest('tr').find('.qutdetailID').val(),
                onSelect: function (suggestion) {
                    setTimeout(function () {

                        $('#f_search_' + id).closest('tr').find('.productID').val(suggestion.productID);
                        $('#f_search_' + id).closest('tr').find('.itemautoid').val(suggestion.itemAutoID);

                        $('#f_search_' + id).closest('tr').find('.UnitOfMeasureID').val(suggestion.defaultUnitOfMeasureID);
                        $('#f_search_' + id).closest('tr').find('.UOM').val(suggestion.defaultUnitOfMeasure);
                    }, 200);
                    fetch_related_tax(1,this);

                    $(this).closest('tr').css("background-color", 'white');

                }

            });
            $(".tt-dropdown-menu").css("top", "");
        }

        function add_more_row() {
            search_id += 1;
            $('select.select2').select2('destroy');
            var appendData = $('#quotationtbl tbody tr:first').clone();
            appendData.find('.f_search').attr('id', 'f_search_' + search_id);
            //appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
            appendData.find('input').val('');
            appendData.find('.Qty').val('0.00');
            appendData.find('.unitcost').val('0.00');
            appendData.find('.discount').val('0.00');
            appendData.find('.vat').val('0.00');
            appendData.find('.item_taxPercentage').val('0.00');
            appendData.find('textarea').val('');
            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
            $('#quotationtbl').append(appendData);
            $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
            initializeitemTypeahead(search_id);
            $('.select2').select2();
            $('.datepic').datetimepicker({
                useCurrent: false,
                format: 'YYYY-MM-DD',
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'bottom'
                }
            }).on('dp.change', function (ev) {

            });
            number_validation();

        }

    function init_standardjobcardform()
    {

        var Tax = '<select name="ItemTax[]" class="form-control ItemTax select2" onchange="fetch_tax_percentage(this),caltaxamt(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" required><option value="">Select Tax Type</option></select>';

    $('#salesquotation_body').html('');
    $('#salesquotation_body').append('<tr><td style="width: 21%;"><input type="text" class="form-control f_search" name="search[]"   onkeydown="remove_item_all_description(event,this);"  placeholder="Item Description..." id="f_search_1"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"><input type="hidden" class="form-control productID" name="productID[]"><br><textarea class="form-control" rows="2" name="comment[]"placeholder="Item Comment..."></textarea></td>' + '<td style="width: 8%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="UOM" class="form-control number UOM" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]"></td><td style="width: 8%;"><input type="text" name="Qty[]" value="0.00" class="form-control number Qty" onkeyup="calculateTotal(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" onkeypress="return validateFloatKeyPress(this,event);" onfocus="this.select();"> </td><td style="background-color: #ececec;width: 9%;"><input type="text" name="unitcost[]" value="0.00" class="form-control number unitcost" onkeyup="calculateTotal(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();" > </td><td style="width: 7%;"><input type="text" name="discount[]" onkeyup="cal_discount(this),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();"  onkeypress="return validateFloatKeyPress(this,event)" value="0.00%" class="form-control number discount" onfocus="this.select();" ><input type="hidden" name="discountamt[]" value="0.00%" class="form-control number discountamt" onfocus="this.select();" ></td><td style="width: 7%;"><input type="text" name="discountamountcal[]" onkeyup="cal_discount_amount(this),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();"  onkeypress="return validateFloatKeyPress(this,event)" value="0.00" class="form-control number discountamountcal" onfocus="this.select();" ></td><td style="width: 10%;">' + Tax + '</td><td style="width: 5%;"><input type="text" name="item_taxPercentage[]"  onkeypress="return validateFloatKeyPress(this,event)" value="0.00" class="form-control number item_taxPercentage" onkeyup="caltaxamt(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax()" ocus="this.select();"><input type="hidden" name="taxamt[]" value="0.00%" class="form-control number taxamt" onfocus="this.select();" > </td><td style="width: 5%;background-color: #fde49d;"><input type="text" name="totalcost[]" value="0.00" class="form-control number totalcost" onfocus="this.select();" readonly><input type="hidden" class="form-control totalwithoutdedu" name="totalwithoutdedu[]"> </td><td style="width: 17%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expectedDeliveryDate[]"  value="" class="form-control expectedDeliveryDate"> </div> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
    number_validation();
    $('.select2').select2();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: 'YYYY-MM-DD',
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }

        }).on('dp.change', function (ev) {

        });
    setTimeout(function () {
        initializeitemTypeahead(1);
    }, 500);
    }
        $(document).on('click', '.remove-tr2', function () {
            $(this).closest('tr').remove();
        });
    function calculateTotal(element) {

        var expectedQty = $(element).closest('tr').find('.Qty').val();
        var amount = $(element).closest('tr').find('.unitcost').val();
        var discountamt = 0;
        var discount = $(element).closest('tr').find('.discount').val();
        var ItemTax = $(element).closest('tr').find('.ItemTax').val();
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

        $(element).closest('tr').find('.totalcost').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));

        $(element).closest('tr').find('.totalwithoutdedu').val(((parseFloat(expectedQty) * parseFloat(amount))).toFixed(<?php echo $currencydecimalplaces?>));
        $(element).closest('tr').find('.taxamt').val(parseFloat(taxamount).toFixed(<?php echo $currencydecimalplaces?>));

    }
        function rawmaterialCostTotal() {
            var tot_Qty = 0;
            var tot_UnitCost = 0;
            var tot_TotalCost = 0;
            $('#salesquotation_body tr').each(function () {
                var tot_value = getNumberAndValidate($('td', this).eq(8).find('.totalwithoutdedu').val());
                tot_TotalCost += tot_value;
            });

            $("#tot_totalValue").text(commaSeparateNumber(tot_TotalCost, <?php echo $currencydecimalplaces?>));
           // calculateTotalCost();
        }
        function getNumberAndValidate(thisVal) {
            thisVal = $.trim(thisVal);
            thisVal = removeCommaSeparateNumber(thisVal);
            //thisVal = thisVal.toFixed(<?php echo $currencydecimalplaces?>);
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
                var ItemTax = $(element).closest('tr').find('.ItemTax').val();
                var Taxpercentage = $(element).closest('tr').find('.item_taxPercentage').val();
                var taxamount = 0;



                if (estimatedAmount) {
                    var discountamount = (estimatedAmount / 100) * parseFloat(element.value);

                    taxamount = (((parseFloat(Qty) * parseFloat(unitcost))- parseFloat(discountamount))/ 100)* parseFloat(Taxpercentage);

                    $(element).closest('tr').find('.totalcost').val((parseFloat(estimatedAmount) - parseFloat(discountamount)) + parseFloat(taxamount));
                    $(element).closest('tr').find('.discountamt').val(discountamount);
                    $(element).closest('tr').find('.discountamountcal').val(discountamount);
                    $(element).closest('tr').find('.taxamt').val(parseFloat(taxamount).toFixed(<?php echo $currencydecimalplaces?>));
                }
            }
        }
        function rawmaterialCostTotal_discount() {
            var tot_Qty = 0;
            var tot_UnitCost = 0;
            var tot_TotalCost = 0;

            $('#salesquotation_body tr').each(function () {
                var tot_value = getNumberAndValidate($('td', this).eq(5).find('.discountamountcal').val());
                tot_TotalCost += tot_value;
            });

            $("#tot_totalValuediscount").text(commaSeparateNumber(tot_TotalCost, <?php echo $currencydecimalplaces?>));
        }


        function load_standard_job_card(QuotationAutoID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {QuotationAutoID: QuotationAutoID},
                url: "<?php echo site_url('Crm/load_quotation_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var Tax = '<select name="ItemTax[]" class="form-control ItemTax select2" onchange="fetch_tax_percentage(this),caltaxamt(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" required><option value="">Select Tax Type</option></select>';
                    $('#salesquotation_body').html('');
                    var i = 0;
                    var isRecordExist = 0;
                    var uomid;
                    var uomdescription;
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, v) {
                            if ((v.unitOfMeasureID != null) || (v.unitOfMeasureID != 0)) {
                                uomid = v.unitOfMeasureID;
                                uomdescription = v.unitOfMeasure;

                            }
                            var Tax = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ci_\'+ search_id +\'"'), form_dropdown('ItemTax[]', all_tax_drop(1), 'Each', 'class="form-control select2 ItemTax" onchange="fetch_tax_percentage(this),caltaxamt(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();"'))
                                ?>';


                            $('#salesquotation_body').append('<tr id="rowMC_' + v.contractDetailsAutoID + '"><td style="width: 21%;"><input type="text" class="form-control f_search"  name="search[]" onkeydown="remove_item_all_description(event,this);"   placeholder="Item Description..."  value= "' + v.productName + '"  id="f_search_' + search_id + '"> <input type="hidden" class="form-control qutdetailID" name="qutdetailID[]" value="' + v.contractDetailsAutoID + '"><input type="hidden" class="form-control itemautoid" name="itemautoid[]" value="' + v.itemAutoID + '"><input type="hidden" class="form-control productID" name="productID[]"  value="' + v.productID + '"><br><textarea class="form-control" rows="2" name="comment[]" placeholder="Item Comment...">' + v.comment + '</textarea></td>' + '<td style="width: 8%;"> <input type="text" name="UOM[]" style="text-align: left;"  value="' + uomdescription + '" placeholder="UOM" class="form-control number UOM" readonly><input type="hidden" class="form-control UnitOfMeasureID" value=' + uomid + ' name="UnitOfMeasureID[]"></td><td style="width: 8%;"><input type="text" name="Qty[]" onkeypress="return validateFloatKeyPress(this,event);" value="' + v.requestedQty + '" class="form-control number Qty" onkeyup="caltaxamt(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax()" onfocus="this.select();"> </td><td style="background-color: #ececec;width: 9%;"><input type="text" name="unitcost[]" onkeypress="return validateFloatKeyPress(this,event);" value="' + v.unittransactionAmount + '" class="form-control number unitcost" onkeyup="calculateTotal(this),rawmaterialCostTotal(),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" onfocus="this.select();" > </td><td style="width: 7%;"><input type="text" name="discount[]" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="cal_discount(this),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" value="' + v.discountPercentage + '"  class="form-control number discount" onfocus="this.select();" ><input type="hidden" name="discountamt[]" class="form-control number discountamt" value="' + v.discountAmount + '"  onfocus="this.select();" > </td><td style="width: 7%;"><input type="text" name="discountamountcal[]" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="cal_discount_amount(this),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" value="' + v.discountAmount + '" class="form-control number discountamountcal" onfocus="this.select();" ></td><td style="width: 10%;">' + Tax + '</td><td style="width: 10%;"><input type="text" name="item_taxPercentage[]" onkeypress="return validateFloatKeyPress(this,event);" value="' + v.taxPercentage + '" class="form-control number item_taxPercentage" onkeyup="cal_discount(this),rawmaterialCostTotal_discount(),grandtotal(),rawmaterialCostTotal_Tax();" focus="this.select();"><input type="hidden" name="taxamt[]" value="' + v.taxAmount + '" class="form-control number taxamt"  onfocus="this.select();" > </td><td style="width: 5%;background-color: #fde49d;"><input type="text" name="totalcost[]"   value="' + v.transactionAmount + '" class="form-control number totalcost" onfocus="this.select();" readonly><input type="hidden" class="form-control totalwithoutdedu" name="totalwithoutdedu[]"  value="' + (v.requestedQty * v.unittransactionAmount) + '" > </td><td style="width: 17%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expectedDeliveryDate[]"   value=' + v.expectedDeliveryDate + ' class="form-control expectedDeliveryDate"> </div> </td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_quotation_detail(' + v.contractDetailsAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td>  </tr>');
                            initializeitemTypeahead(search_id);
                            if(v.taxMasterAutoID!=0)
                            {
                                $('#ci_' + search_id).val(v.taxMasterAutoID);
                            }



                            search_id++;
                            i++;
                        });
                    } else {
                        init_standardjobcardform();
                    }
                    rawmaterialCostTotal();
                    rawmaterialCostTotal_discount();
                    rawmaterialCostTotal_Tax();
                    grandtotal();
                    $('.select2').select2();
                    $('.datepic').datetimepicker({
                        useCurrent: false,
                        format: 'YYYY-MM-DD',
                        widgetPositioning: {
                            horizontal: 'left',
                            vertical: 'bottom'
                        }
                    }).on('dp.change', function (ev) {

                    });
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function grandtotal() {

            var tot_TotalCostdiscount = 0;
            var tot_TotalCostsubtotal = 0;
            var tot_grandtotal = 0;
            var tot_TotalCostTax = 0;
            $('#salesquotation_body tr').each(function () {
                var tot_valuedis = getNumberAndValidate($('td', this).eq(5).find('.discountamountcal').val());
                var tot_valuesub = getNumberAndValidate($('td', this).eq(8).find('.totalwithoutdedu').val());
                var tot_valueta = getNumberAndValidate($('td', this).eq(7).find('.taxamt').val());
                tot_TotalCostdiscount += tot_valuedis;
                tot_TotalCostsubtotal += tot_valuesub;
                tot_TotalCostTax += tot_valueta;
                tot_grandtotal += ((parseFloat(tot_valuesub) - parseFloat(tot_valuedis))) + parseFloat(tot_valueta);



            });



            $("#tot_grandtotal").text(commaSeparateNumber(tot_grandtotal, <?php echo $currencydecimalplaces?>));
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
            $(element).closest('tr').find('.totalwithoutdedu').val(((parseFloat(expectedQty) * parseFloat(amount))).toFixed(<?php echo $currencydecimalplaces?>));
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

            $("#tot_tax").text(commaSeparateNumber(tot_TotalCost, <?php echo $currencydecimalplaces?>));
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
        function remove_item_all_description(e, ths) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9 || keyCode == 13) {
                //e.preventDefault();
            } else {
                $(ths).closest('tr').find('.itemautoid').val('');
                $(ths).closest('tr').find('.productID').val('');
            }

        }


    </script>