<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
<?php
$tr_currency = $this->common_data['company_data']['company_default_currency'];
$isMinusAllowed = getPolicyValues('MQT', 'All');
$isTaxAllowed = getPolicyValues('TAX', 'All');
$isTaxChangeAllowed = getPolicyValues('PTAX', 'All');
$isExchangeThroughInvoice = getPolicyValues('EXINV', 'All');

if ($isMinusAllowed == ' ' || empty($isMinusAllowed) || $isMinusAllowed == null) {
    $isMinusAllowed = 0;
}

if ($isTaxChangeAllowed == ' ' || empty($isTaxChangeAllowed) || $isTaxChangeAllowed == null) {
    $isTaxChangeAllowed = 0;
}

$companycountry = fetch_company_country();

?>

<!-- Modal image start-->
<div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close close-btn-pos" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="myModalLabel"> Item Preview </h4>
       </div>
       <div class="modal-body" id="getCode">
          
       </div>
    </div>
   </div>
 </div>
<!-- Modal image end-->

<script>
    var base_url = '<?php echo base_url();?>';
    var qtyinlineeditcnt = 0;
    

    function clearInvoice() {
        $("#totSpan").html('0.00');
        $("#totVal").val('0');

        $("#discSpan").html('0.00');
        $("#discVal").val('0');
        $("#netTotSpan").html('0.00');
        $("#netTotVal").val('0');

        $("#item-image").attr('src', base_url + "/images/item/no-image.png");

        $(".itemCount").html('0');
    }

    function close_posPrint() {
        $("#print_template").modal('hide');
        $("#tender_modal").modal('hide');
        newInvoice(1);
        clearInvoice();
    }

    var d = <?php echo get_company_currency_decimal() ?>;
    $(document).ready(function (e) {
        $.fn.numpad.defaults.gridTpl = '<table class="modal-content table" style="width:200px" ></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in" style="z-index: 5000;"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:16px; font-weight: 600;" />';
        $.fn.numpad.defaults.buttonNumberTpl = '<button type="button" class="btn btn-xl-numpad btn-numpad-default"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-xl-numpad" style="width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function () {
            $(this).find('.done').addClass('btn-primary');
            /*$(this).find('.del').addClass('btn-numpad-default');
             $(this).find('.clear').addClass('btn-numpad-default');*/
        };
        initNumPad();

        $("#tempQty").numpad();

        /*$("#gen_disc_percentage").change(function (e) {
            calculate_general_discount($("#gen_disc_percentage"));
        });
        $("#gen_disc_percentage").change(function (e) {
            calculate_general_discount($("#gen_disc_percentage"));
        });
        $("#gen_disc_amount").change(function (e) {
            calculate_general_discount($("#gen_disc_amount"));
        });
        $("#gen_disc_amount").change(function (e) {
            calculate_general_discount($("#gen_disc_amount"));
        });*/
        /*$("input[type='text']").click(function () {
         $(this).select();
         });*/

        $(".allownumericwithdecimal").on("keypress keyup blur", function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $("#itemQty, #salesPrice").keyup(function (e) {
            if (e.which == 13) {
                 $("#pos_form").submit();
                //$("#pos-add-btn").click();
                //$('#pos_form').bootstrapValidator();
            }
        });

        $("#disPer").keyup(function (e) {
            localStorage.setItem('last_update','disPer');
            if (e.which == 13) {
                checkPosAuthentication(19,'disPer');
                //gpos_discount_cal('disAmount');
                //$("#pos_form").submit();
                //$("#pos-add-btn").click();
                //$('#pos_form').bootstrapValidator();
            }
        });

        $("#disAmount").keyup(function (e) {
            localStorage.setItem('last_update','disAmount');
            if (e.which == 13) {
                checkPosAuthentication(19,'disAmount');
                gpos_discount_cal('disAmount');
                //$("#pos_form").submit();
                //$("#pos-add-btn").click();
                //$('#pos_form').bootstrapValidator();
            }
        });

    });


    function calculate_general_discount(that, isWithOutAuth = 0, capdetails = null, processMasterID = null) {
        var net_amount_with_item_discount = parseFloat($("#netTotVal").val());
        var discount = parseFloat(that.val());
        var div_id = that.attr('id');
        //debugger;

        if (div_id == 'gen_disc_percentage') {
            if (capdetails != null) {
                calculateDiscount_byPercentage(capdetails, processMasterID)
            } else {
                calculateDiscount_byPercentage()
            }
        } else if (div_id == 'gen_disc_amount') {
            /** Discount Amount */
            if (net_amount_with_item_discount < discount) {
                neutral_generalDiscount();
                myAlert('e', 'Discount Amount can not be more than net total!');
            } else if (discount < 0) {
                neutral_generalDiscount();
                myAlert('e', 'Discount Amount can not be minus value!');
            } else if (net_amount_with_item_discount >= discount) {
                /*debugger;*/
                var newNetAmount = net_amount_with_item_discount - discount;
                if (newNetAmount == 0) {
                    var zero = 0;
                    $("#gen_disc_percentage").val('');
                    $("#netTot_after_g_disc_div").html(zero.toFixed(d));
                    $("#netTot_after_g_disc").val(zero.toFixed(d));
                } else {
                    var discountPercentage = (discount * 100) / net_amount_with_item_discount
                    $("#gen_disc_percentage").val(discountPercentage.toFixed(d));
                    $("#netTot_after_g_disc_div").html(newNetAmount.toFixed(d));
                    $("#netTot_after_g_disc").val(newNetAmount.toFixed(d));
                    $("#gen_disc_amount_hide").val(discount.toFixed(d));
                    if (capdetails != null) {
                        if (parseFloat(capdetails['capAmount']) > 0 && parseFloat(capdetails['capPercentage']) > 0) {
                            if (discountPercentage > parseFloat(capdetails['capPercentage']) && net_amount_with_item_discount > parseFloat(capdetails['capAmount'])) {
                                openUserPosAuthProcessModal(processMasterID);
                            }
                        } else if (parseFloat(capdetails['capAmount']) == 0 && parseFloat(capdetails['capPercentage']) == 0) {
                            openUserPosAuthProcessModal(processMasterID);
                        } else if (parseFloat(capdetails['capAmount']) == 0 && parseFloat(capdetails['capPercentage']) > 0) {
                            if (discountPercentage > parseFloat(capdetails['capPercentage'])) {
                                openUserPosAuthProcessModal(processMasterID);
                            }
                        } else if (parseFloat(capdetails['capAmount']) > 0 && parseFloat(capdetails['capPercentage']) == 0) {
                            if (net_amount_with_item_discount > parseFloat(capdetails['capAmount'])) {
                                openUserPosAuthProcessModal(processMasterID);
                            }
                        }
                    }
                }
            } else {
                neutral_generalDiscount();
            }
        } else {

        }
        if (isWithOutAuth == 0) {
            checkPosAuthentication(2, that);
        }
    }

    /** Core Function : explicitly call in another method */
    function calculateDiscount_byPercentage(capdetails = null, processMasterID = null) {
        var net_amount_with_item_discount = parseFloat($("#netTotVal").val());
        var discount = parseFloat($("#gen_disc_percentage").val());

        /** Discount Percentage  */
        if (discount <= 100 && discount > 0) {
            var discountAmount = net_amount_with_item_discount * (discount / 100);
            var newNetTotal = net_amount_with_item_discount - discountAmount;
            $("#netTot_after_g_disc_div").html(newNetTotal.toFixed(d));
            $("#netTot_after_g_disc").val(newNetTotal.toFixed(d));
            $("#gen_disc_amount").val(discountAmount.toFixed(d));
            $("#gen_disc_amount_hide").val(discountAmount.toFixed(d));

            if (capdetails != null) {

                if (parseFloat(capdetails['capAmount']) > 0 && parseFloat(capdetails['capPercentage']) > 0) {
                    if (discount > parseFloat(capdetails['capPercentage']) && net_amount_with_item_discount > parseFloat(capdetails['capAmount'])) {
                        openUserPosAuthProcessModal(processMasterID);
                    }
                } else if (parseFloat(capdetails['capAmount']) == 0 && parseFloat(capdetails['capPercentage']) == 0) {
                    openUserPosAuthProcessModal(processMasterID);
                } else if (parseFloat(capdetails['capAmount']) == 0 && parseFloat(capdetails['capPercentage']) > 0) {
                    if (discount > parseFloat(capdetails['capPercentage'])) {
                        openUserPosAuthProcessModal(processMasterID);
                    }
                } else if (parseFloat(capdetails['capAmount']) > 0 && parseFloat(capdetails['capPercentage']) == 0) {
                    if (net_amount_with_item_discount > capdetails['capAmount']) {
                        openUserPosAuthProcessModal(processMasterID);
                    }
                }
            }

        } else if (discount < 0) {
            neutral_generalDiscount();
            myAlert('e', 'Discount can not be minus value!');
        } else {
            neutral_generalDiscount();
        }

        update_invoice_item_count();
    }

    function reset_generalDiscount() {
        var zero = 0;
        $("#netTot_after_g_disc").val(zero.toFixed(d));
        $("#netTot_after_g_disc_div").html(zero.toFixed(d));
        $("#gen_disc_amount").val('');
        $("#gen_disc_percentage").val('');
        $("#gen_disc_amount_hide").val(zero.toFixed(d));
    }

    function neutral_generalDiscount() {
        var zero = 0;
        var net_amount_with_item_discount = (parseFloat($("#netTotVal").val()));
        $("#netTot_after_g_disc").val(net_amount_with_item_discount.toFixed(d));
        $("#netTot_after_g_disc_div").html(net_amount_with_item_discount.toFixed(d));
        $("#gen_disc_amount").val('');
        $("#gen_disc_percentage").val('');
        $("#gen_disc_amount_hide").val(zero.toFixed(d));
    }


</script>

<script type="text/javascript">

    var isGroupBasedTaxPolicy = '<?php echo $group_based_tax = getPolicyValues('GBT', 'All');?>';
    var isBatchPolicy = '<?php echo $is_batch = getPolicyValues('IB', 'All');?>';
    var isTaxPolicy = '<?php echo $is_batch = getPolicyValues('TAX', 'All');?>';
    var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>;
    var accountsreceivable = <?php echo $this->common_data['controlaccounts']['ARA'];?>;
    var currency = <?php echo $this->common_data['company_data']['company_default_currencyID'];?>;
    var country = <?php echo $this->common_data['company_data']['countryID'];?>;
    var pos_form = $('#pos_form');
    var itemDisplayTB = $('#itemDisplayTB');
    var enable_BC = $('#enable_BC');
    var formInput = $('.formInput');
    var itemCount = $('.itemCount');
    var itemSearch = $('#itemSearch');
    var itemAutoID = $('#itemAutoID');
    var itemDescription = $('#itemDescription');
    var barcode = $('#barcode');
    var itemDescription2 = $('#itemDescription2');
    var currentStockDsp = $('#currentStockDsp');
    var currentStock = $('#currentStock');
    var itemUOM = $('#itemUOM');
    var itemQty = $('#itemQty');
    var disPer = $('#disPer');
    var disAmount = $('#disAmount');
    var salesPrice = $('#salesPrice');
    var salesPricehn = $('#salesPricehn');
    var netTotSpan = $('#netTotSpan');
    var discSpan = $('#discSpan');
    var totSpan = $('#totSpan');
    var netTotVal = $('#netTotVal');
    var discVal = $('#discVal');
    var totVal = $('#totVal');
    var error_modal = $('#error_modal');
    var tender_modal = $('#tender_modal');
    var customer_modal = $('#customer_modal');
    var item_batch_modal = $('#item_batch_modal');
    var item_warehouse_modal = $('#item_warehouse_modal');
    var showSweetAlert = $('.showSweetAlert');
    var modal_search_item = $('#modal_search_item');
    var pos_payments_modal = $('#pos_payments_modal');
    var cardDet_modal = $('#cardDet_modal');
    var cheque_modal = $('#cheque_modal');
    var recall_modal = $('#recall_modal');
    var till_modal = $('#till_modal');
    var tenderPay = $('.tenderPay');
    var customerSearchTB = $('#customerSearchTB');
    var creditNoteTB = $('#creditNoteTB');
    var invoiceSearchTB = $('#invoiceSearchTB');
    var selectedCusArray = [];
    var selectedItemArray = [];
    var exceededItemArray = [];
    var freeIssueData;
    var returnInvoiceTB = $('#returnInvoiceTB');
    var itemBatchSearch = $('#itemBatchSearch');
    var itemWareHouseQty = $('#itemWareHouseQty');

    var _referenceNO = $('#_referenceNO');
    var _cardNumber = $('#_cardNumber');
    var _bank = $('#_bank');

    var _chequeNO = $('#_chequeNO');
    var _chequeCashDate = $('#_chequeCashDate');
    var isPromo = $('#isPromo');
    var taxrowCount = 0;
    var selectedValueTax = 0;
    var taxCalTotal = $('#taxCalTotal');
    var isNonDefaultDiscount = $('#isNonDefaultDiscount');
    var uomItemList;
    var to = 0;
    var holdbillTax = 0;
    var invoice_no = $('#doSysCode_refNo').text();

    var edit_item_umo_detail;
    var init_umo;
    var edit_item_current_stock;
    var tax_item_arr = [];

    var item_set_error = 0;

    till_modal.on('shown.bs.modal', function (e) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'<?php echo $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash()?>'},
            url: "<?php echo site_url('Pos/load_currencyDenominationPage'); ?>",
            beforeSend: function () {
                $('#currencyDenomination_data').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#currencyDenomination_data').html(data);
                if ($('#isStart').val() == 1) {
                    $('#counterID').prop('disabled', false);
                } else {
                    $('#counterID').prop('disabled', true);
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error in loading currency denominations.')
                }
            }
        });
    });

    <?php
    if ($isHadSession == 0) {
        echo '$("#isStart").val(1);';
        echo '$(".tillModal_close").hide();';
        echo '$("#tillModal_title").text("Day Start");';
        echo '$("#tillSave_Btn").attr("onclick", "shift_create()");';
        echo 'till_modal.modal({backdrop:"static"});';
    }
    ?>


    netTotSpan.text(commaSeparateNumber(0, dPlaces));
    discSpan.text(commaSeparateNumber(0, dPlaces));
    totSpan.text(commaSeparateNumber(0, dPlaces));


    $(document).ready(function () {
        setTimeout(function () {
            itemSearch.focus();
        }, 500);
        itemSearch_typeHead();
        $('.select2').select2();

        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function () {
            if ($(this).attr('id') == 'return-date') {
                var invDate = $('#return-inv-date').val();
                var thisDate = $(this).val();

                if (invDate > thisDate) {
                    $(this).datepicker('update', invDate);
                    myAlert('w', 'Return date cannot be laser than invoice date <br> [ ' + invDate + ' ]');
                }

            }
            $(this).datepicker('hide');
        });

        $('.pastDateFields').datepicker({
            format: 'yyyy-mm-dd',
            endDate: '+0d'
        }).on('changeDate', function () {
            if ($(this).attr('id') == 'return-date') {
                var invDate = $('#return-inv-date').val();
                var thisDate = $(this).val();

                if (invDate > thisDate) {
                    $(this).datepicker('update', invDate);
                    myAlert('w', 'Return date cannot be laser than invoice date <br> [ ' + invDate + ' ]');
                }

            }
            $(this).datepicker('hide');
        });

        itemDisplayTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        customerSearchTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        creditNoteTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        invoiceSearchTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        returnInvoiceTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        pos_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                itemSearch: {validators: {notEmpty: {message: '&nbsp;'}}}, /*Item Code is required.*/
                itemUOM: {validators: {notEmpty: {message: '&nbsp;'}}}, /*UOM is required.*/
                itemQty: {validators: {notEmpty: {message: '&nbsp;'}}}, /*QTY is required.*/
                salesPrice: {validators: {notEmpty: {message: '&nbsp;'}}} /*Price is required.*/
            },
        }).on('success.form.bv',
            function (e) {
                e.preventDefault();
               
                var qty = $.trim(itemQty.val());
                if ($.isNumeric(qty) && qty > 0) {
                    if(isNonDefaultDiscount.val() > 0 && (disPer.val() > isNonDefaultDiscount.val())){
                        myAlert('e', 'Applied discount exceeds maximum allowed ' + isNonDefaultDiscount.val() + '.');
                        return false;
                    }

                    var amount = qty * salesPrice.val();
                    var discAmount = amount * disPer.val() * 0.01;
                    var thisNetTot = (amount - discAmount);

                    //Sales price calculations
                    if(typeof(edit_item_umo_detail) != "undefined" && edit_item_umo_detail !== null){
                        var item_selected_uom =  itemUOM.val();
                       // itemUOM.val(init_umo);
                        var selected_conversion = 1;
                        $.each(edit_item_umo_detail, function(index,itemObj){
                            if(itemObj.UnitShortCode == item_selected_uom){
                               // qty = qty / itemObj.conversion;
                                selected_conversion =  itemObj.conversion;
                                itemQty.val(qty);
                                pos_form.bootstrapValidator('revalidateField', 'itemQty');
                            }
                        });

                        //reserve quantity
                       // barcode.val() 
                       reserved_item(barcode.val(),qty,'plus',null,selected_conversion);

                       uomItemList = JSON.stringify(edit_item_umo_detail);

                       edit_item_umo_detail = null;
                    }
                  
                  //  alert(currentStock.val());

                    var last_update=localStorage.getItem('last_update');
                    if(last_update=='disPer'){
                        var discAmount = amount * disPer.val() * 0.01;
                        var thisNetTot = (amount - discAmount);
                    }else if(last_update=='disAmount'){
                        var discAmount = $("#disAmount").val();
                        var thisNetTot = (amount - discAmount);
                        var disPercentage = (discAmount*100)/amount;
                        disPer.val(disPercentage);
                    }else{
                        var discAmount = amount * disPer.val() * 0.01;
                        var thisNetTot = (amount - discAmount);
                    }
                   /* onchange="load_line_tax_amount_prq()"*/


                    var image_hidden = $('#item-image-hidden').val();
                    var image_hidden2 = "'"+image_hidden+"'";

                    var isPromo = $("#isPromo").val();

                    var tax_select;

                    if(<?php echo $isTaxChangeAllowed ?> == 1){
                        tax_select = '<select class="lntax_drop tax_type" onchange="load_line_tax_amount_gpos(this)" style="width: 110px;color: black;" id="tax_type_0" ><option value="">Select Tax Type </option></select>';
                    }else{
                        tax_select = '<select class="lntax_drop tax_type" onchange="load_line_tax_amount_gpos(this)" disabled style="width: 110px;color: black;" id="tax_type_0" ><option value="">Select Tax Type </option></select>';
                    }
                    

                    if ($('#is-edit').val() == 1) {
                         load_line_tax_amount_gpos_test(qty,amount,discAmount,itemAutoID.val())
                    }

                    var itemDet = '';
                    itemDet += '<td align="right"></td>';
                    itemDet += '<td onclick="open_item_batch_modal()">' + itemSearch.val() + '</td>';
                    itemDet += '<td>' + barcode.val() + '</td>';
                    itemDet += '<td>' + itemDescription.val() + '</td>';
                    itemDet += '<td>' + itemUOM.val() + '</td>';
                    itemDet += '<td align="right" id="_showqtyeditfield" onclick="showqtyeditfield(this)"><span class="itemQtySpan">' + qty + '</span> <input onchange="updateqtyval(this)" type="number" step="any" name="itemQty[]" class="itemQty hidden" value="' + itemQty.val() + '"></td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(salesPrice.val(), dPlaces) + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                    itemDet += '<td align="right">' + getNumberAndValidate(disPer.val()) + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';

                    if(isGroupBasedTaxPolicy ==1){
                        itemDet += '<td align="center">' + tax_select + '</td>';
                        itemDet += '<td align="right"><span class="taxAmount"> 0.00</span></td>';
                    }


                    itemDet += '<td align="right">' + commaSeparateNumber(thisNetTot, dPlaces) + '</td>';

                    itemDet += '<td align="right" class="">';
                    itemDet += '<a><span class="glyphicon glyphicon-pencil editRow" style="position: static"></span></a>';
                    itemDet += '&nbsp;&nbsp;';
                    itemDet += '<span class="glyphicon glyphicon-trash deleteRow" style="position: static"></span>';
                    itemDet += '&nbsp;&nbsp;';
                    itemDet += '<a onclick="viewItemImg('+ itemAutoID.val() +', '+image_hidden2+' )"><span class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn viewItemImg"  style="position: static"></span></a>';
                    itemDet += '<input type="hidden" name="itemID[]"  class="itemID" value="' + itemAutoID.val() + '" >';

                    itemDet += '<input type="hidden" name="itemName[]" class="itemName" value="' + itemDescription.val() + '" >';
                    itemDet += '<input type="hidden" name="taxamountCal[]" class="taxamountCal"  value="'+taxCalTotal.val()+'">';
                    itemDet += '<input type="hidden" name="itemUOM[]" class="itemUOM" value="' + itemUOM.val() + '" >';
                    itemDet += '<input type="hidden" name="taxFormula[]" class="taxFormula" value="0" >';
                    //itemDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itemQty.val() + '" >';
                    itemDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + salesPrice.val() + '" >';
                    itemDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disPer.val() + '" >';
                    itemDet += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + discAmount + '" >';
                    itemDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStock.val() + '" >';
                    itemDet += '<input type="hidden" class="discountAmount" value="' + discAmount + '" >';
                    itemDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                    itemDet += '<input type="hidden" class="netAmount" value="' + thisNetTot + '" >';
                    itemDet += '<input type="hidden" class="item-image-hidden" value="' + image_hidden + '" >';
                    itemDet += '<input type="hidden" class="isPromo" value="' + isPromo + '" >';
                    itemDet += '<input type="hidden" class="isNonDefaultDiscount" value="' + isNonDefaultDiscount.val() + '" >';
                    itemDet += "<input type='hidden' class='uomList' value='" + uomItemList + "'>";
                    itemDet += '</td>';

                    if ($('#is-edit').val() != 1) {
                        //alert("was up");
                        $('#itemDisplayTB tr').removeClass('selectedTR');
                        itemDisplayTB.append('<tr class="selectedTR">' + itemDet + '</tr>');
                    } else {
                        $('#is-edit').val(0);
                        $('#itemDisplayTB .editTR').html(itemDet).removeClass('editTR');
                    }


                    itemSearch.prop('readonly', false);
                    itemAdd_sub_function();
                    isThereAnyPromotion(itemAutoID.val());

                    var noofitems = 0;
                    $('#itemDisplayTB tr').each(function (index,item) {
                        var noqty = $(item).find('td:eq(5)').text();
                        if (noqty) {
                            noofitems = noofitems + parseInt(noqty);
                        }
                    });

                    itemCount.html(parseInt(noofitems));
                    formInput.val('');
                    itemSearch.typeahead('val', '');
                    itemUOM.empty();
                    itemUOM.css('background', '#eee');
                    formInput.prop('readonly', true);
                    itemUOM.prop('readonly', true);
                    itemSearch.focus();
                    calculateDiscount_byPercentage();
                   // taxCalTotal.val(load_line_tax_amount_gpos_test(172,(126*2),0,2));
                   // load_line_tax_amount_gpos_test(itemAutoID.val(),(qty*salesPrice.val()));
                    //gpos_discount_cal('disAmount');//this is not here. only for testing.

                    pos_form[0].reset();
                    pos_form.bootstrapValidator('resetForm', true);
                    $("#pos-add-btn").attr('disabled','disabled');



                } else {
                    itemQty.val('');
                    pos_form.bootstrapValidator('revalidateField', 'itemQty');
                    myAlert('e', 'Qty is not valid');
                }
            });

        // $('#return-date').datepicker({
        //     format: 'mm-dd-yyyy',
        //     startDate: '-15d',
        //     endDate: '+0d',
        // });

    });

    $(function() {
        $('#return-date').datepicker({
            format: 'mm-dd-yyyy',
            startDate: '-15d',
            endDate: '+0d',
        });
    });


    function itemAdd_sub_function() {
        getTot();
        getDiscountTot();
        getNetTot();
        addTrNumber();
    }


    function itemSearch_typeHead() {
      
        var tmpBC = $("#enable_BC").is(":checked");

        var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace();
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Pos/item_search/?q=%QUERY&bc=" + tmpBC
        });

        item.initialize();
        itemSearch.typeahead(null, {
            minLength: 3,
            highlight: true,
            displayKey: 'itemSystemCode',
            source: item.ttAdapter(),
            templates: {
                empty: [].join('\n'),
                suggestion: Handlebars.compile('<div><strong>{{itemDescription}}</strong> – {{itemSystemCode}}</div>')
            }
        }).on('typeahead:selected', function (object, datum) {
            setValues_masterForm(datum);

        });
    }

    function item_search_loadToInvoice(barCode) {
        
        $("#itemSearch").attr("disabled", "disabled");
        var customer_selected = $('#customerCode').val();

        if (barCode !== "") {
            $.ajax({
                async: true,
                type: 'get',
                dataType: 'json',
                url: "<?php echo site_url('Pos/item_search_outlet_barcode?q='); ?>" + barCode + '&customer='+customer_selected,
                beforeSend: function () {
                    $("#pos-add-btn").prop('disabled', false);
                    $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-refresh fa-spin"></i> Add');
                },
                success: function (data) {
                    $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-plus"></i> Add')
                    var itemVal = $("#itemSearch").val();

                    if (data == null) {
                        if (itemVal.trim() != '') {
                            myAlert('e', 'Item ' + barCode + ' does not exist');
                            $("#itemSearch").val('');
                            pos_form[0].reset();
                            pos_form.bootstrapValidator('resetForm', true);
                        }
                    } else {
                        if (<?php echo $isMinusAllowed ?> == 1){
                            check_if_item_qty_exceeded(data)
                        }else{
                            setValues_masterForm(data);
                        }
                    }
                    $("#itemSearch").removeAttr("disabled");

                }, error: function (jqXHR, textStatus, errorThrown) {
                    $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-plus"></i> Add')
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Error while loading')
                    }
                    $("#itemSearch").removeAttr("disabled");
                }
            });
        } else {
            myAlert('e', 'Please scan barcode');
            $("#itemSearch").removeAttr("disabled");
            $("#itemSearch").focus();
        }
    }

    function fetch_related_uom_posgen_new(datum, short_code, select_value) {
  
        if(item_set_error == 1){
            return false;
        }

        formInput.prop('readonly', false);
        itemUOM.prop('readonly', false);
        itemUOM.css('background', '#fff');
        itemUOM.empty();
        itemUOM.append($('<option></option>').val('').html('Select  UOM'));
        /*if (!jQuery.isEmptyObject(datum.uom_output)) {
            $.each(datum.uom_output, function (val, text) {
                itemUOM.append('<option value="' + datum.uom_output[0].UnitShortCode + '" data-value="' + datum.uom_output[0].conversion + '">' + datum.uom_output[0].UnitShortCode + ' | ' + datum.uom_output[0].UnitDes + '</option>');
            });
            if (select_value) {
                itemUOM.val(select_value);
            }
        }*/
        itemUOM.append('<option value="' + short_code + '" >' + short_code + '</option>');
        itemUOM.val(short_code);
        re_validate();
        if (enable_BC.prop('checked') == true) {
            $('#pos_form').bootstrapValidator();
        } else {
            itemUOM.focus();
        }
        $("#pos-add-btn").prop('disabled', false);
        $("#pos-add-btn").click();
       

        pos_form.bootstrapValidator('resetForm', true);
        calculateDiscount_byPercentage();

        /* scroll down after item add */
        var fixHeader_Div_objDiv = $(".fixHeader_Div");
        var __h = fixHeader_Div_objDiv.get(0).scrollHeight;
        fixHeader_Div_objDiv.animate({scrollTop: __h});

    }

    function setValues_masterForm(datum) {
       
        var add_i_currentStock = datum.currentStock;
        var qty_init = 1;

        itemSearch.val(datum.seconeryItemCode);
        barcode.val(datum.barcode);
        itemAutoID.val(datum.itemAutoID);
        itemDescription.val(datum.itemDescription);
        itemDescription2.val(datum.itemDescription);
        $('#item-image').attr('src', "<?php echo base_url('images/item/');?>/" + datum.itemImage);
        $('#item-image-hidden').val("<?php echo base_url('images/item/');?>/" + datum.itemImage);
        currentStockDsp.val(datum.currentStock);
        currentStock.val(datum.currentStock);
        currentStock.attr('data-value', datum.currentStock);
      
        taxCalTotal.val((datum.default_taxAmount));
        isNonDefaultDiscount.val((datum.discountPromotionNotDefault));
        uomItemList = datum.uom_output;
        salesPrice.val(parseFloat(datum.companyLocalSellingPrice).toFixed(d));
        isPromo.val(0);
        if(datum.isPromotionApplicable==1){
            disPer.val(datum.discountPercentage);
            var discount_temp = parseFloat(((datum.companyLocalSellingPrice)*disPer)/100).toFixed(d);
            disAmount.val((parseFloat(datum.companyLocalSellingPrice).toFixed(d)- discount_temp));
            isPromo.val(1);
        }else{
            disAmount.val(0);
        }

        if(0 < add_i_currentStock && add_i_currentStock < 1){
            qty_init = add_i_currentStock;
        }

        //set quantity
        itemQty.val(qty_init);

 

        //fetch_related_uom_posgen(datum.defaultUnitOfMeasure, datum.defaultUnitOfMeasure);
        //fetch_related_uom_posgen_qty(datum.defaultUnitOfMeasure, datum.defaultUnitOfMeasure);
        //load_line_tax_amount_gpos_test(datum.itemAutoID,datum.companyLocalSellingPrice)

        //initial enter reserved 1 quantity
        reserved_item(datum.barcode,qty_init)
            .then(fetch_related_uom_posgen_new(datum, datum.defaultUnitOfMeasure, datum.unitOfMeasureID));
       
        setTimeout(function () {
            itemSearch.focus();
        }, 500);

    }

    function setValues_masterForm_exchange(datum) {
        

        var add_i_currentStock = datum.currentStock;
        var qty_init = 1;

        itemSearch.val(datum.seconeryItemCode);
        barcode.val(datum.barcode);
        itemAutoID.val(datum.itemAutoID);
        itemDescription.val(datum.itemDescription);
        itemDescription2.val(datum.itemDescription);
        $('#item-image').attr('src', "<?php echo base_url('images/item/');?>/" + datum.itemImage);
        $('#item-image-hidden').val("<?php echo base_url('images/item/');?>/" + datum.itemImage);
        currentStockDsp.val(datum.currentStock);
        currentStock.val(datum.currentStock);
        currentStock.attr('data-value', datum.currentStock);
        
        // taxCalTotal.val((datum.default_taxAmount));
       // taxCalTotal.val((datum.requested_tax));
        isNonDefaultDiscount.val((datum.discountPromotionNotDefault));
        uomItemList = datum.uom_output;
    
        var short_code = datum.defaultUnitOfMeasure;

        itemUOM.prop('readonly', false);
        itemUOM.css('background', '#fff');
        itemUOM.empty();
        itemUOM.append($('<option></option>').val('').html('Select  UOM'));
        itemUOM.append('<option value="' + short_code + '" >' + short_code + '</option>');
        itemUOM.val(short_code);


        salesPrice.val(parseFloat(datum.companyLocalSellingPrice).toFixed(d));
        isPromo.val(0);
        if(datum.isPromotionApplicable==1){
            disPer.val(datum.discountPercentage);
            var discount_temp = parseFloat(((datum.companyLocalSellingPrice)*disPer)/100).toFixed(d);
            disAmount.val((parseFloat(datum.companyLocalSellingPrice).toFixed(d)- discount_temp));
            isPromo.val(1);
        }else{
            disAmount.val(0);
        }

        if(0 < add_i_currentStock && add_i_currentStock < 1){
            qty_init = add_i_currentStock;
        }

        qty_init = '-'+qty_init;

        //set quantity
        itemQty.val(qty_init);

        item_set_error = 0;
        
        var tax_select;

        if(<?php echo $isTaxChangeAllowed ?> == 1){
            tax_select = '<select class="lntax_drop tax_type" onchange="load_line_tax_amount_gpos(this)" style="width: 110px;color: black;" id="tax_type_0" ><option value="">Select Tax Type </option></select>';
        }else{
            tax_select = '<select class="lntax_drop tax_type" onchange="load_line_tax_amount_gpos(this)" disabled style="width: 110px;color: black;" id="tax_type_0" ><option value="">Select Tax Type </option></select>';
        }


        var itemDet = '';
        var image_hidden = '';

        itemDet += '<td align="right"></td>';
        itemDet += '<td onclick="open_item_batch_modal()">' + itemSearch.val() + '</td>';
        itemDet += '<td>' + barcode.val() + '</td>';
        itemDet += '<td>' + itemDescription.val() + ' (Exchange)' + '</td>';
        itemDet += '<td>' + itemUOM.val() + '</td>';
        itemDet += '<td align="right" id="_showqtyeditfield" onclick="showqtyeditfield(this)"><span class="itemQtySpan">' + qty_init + '</span> <input onchange="updateqtyval(this)" type="number" step="any" name="itemQty[]" class="itemQty hidden" value="' + itemQty.val() + '"></td>';
        itemDet += '<td align="right">' + commaSeparateNumber(salesPrice.val(), dPlaces) + '</td>';
        itemDet += '<td align="right">' + commaSeparateNumber(salesPrice.val(), dPlaces) + '</td>';
        itemDet += '<td align="right">' + getNumberAndValidate(disPer.val()) + '</td>';
        itemDet += '<td align="right">' + commaSeparateNumber(0, dPlaces) + '</td>';

        if(isGroupBasedTaxPolicy ==1){
            itemDet += '<td align="center">' + tax_select + '</td>';
            itemDet += '<td align="right"><span class="taxAmount"> '+ commaSeparateNumber(datum.requested_tax, dPlaces)+'</span></td>';
        }


        itemDet += '<td align="right">' + commaSeparateNumber(datum.requested_price, dPlaces) + '</td>';

        itemDet += '<td align="right" class="td-inline">';
        itemDet += '<a><span class="glyphicon glyphicon-pencil editRow" style="position: static"></span></a>';
        itemDet += '&nbsp; &nbsp;';
        itemDet += '<span class="glyphicon glyphicon-trash deleteRow" style="position: static"></span>';
        itemDet += '<input type="hidden" name="itemID[]"  class="itemID" value="' + itemAutoID.val() + '" >';

        itemDet += '<input type="hidden" name="itemName[]" class="itemName" value="' + itemDescription.val() + ' (Exchange)' +  '" >';
        itemDet += '<input type="hidden" name="taxamountCal[]" class="taxamountCal"  value="'+taxCalTotal.val()+'">';
        itemDet += '<input type="hidden" name="itemUOM[]" class="itemUOM" value="' + itemUOM.val() + '" >';
        itemDet += '<input type="hidden" name="taxFormula[]" class="taxFormula" value="0" >';
        //itemDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itemQty.val() + '" >';
        itemDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + salesPrice.val() + '" >';
        itemDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disPer.val() + '" >';
        itemDet += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + 0 + '" >';
        itemDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStock.val() + '" >';
        itemDet += '<input type="hidden" class="discountAmount" value="' + datum.requested_disAmount + '" >';
        itemDet += '<input type="hidden" class="totalAmount" value="' + datum.requested_price + '" >';
        itemDet += '<input type="hidden" class="netAmount" value="' + datum.requested_price + '" >';
        itemDet += '<input type="hidden" class="item-image-hidden" value="' + image_hidden + '" >';
        itemDet += '<input type="hidden" class="isPromo" value="' + isPromo + '" >';
        itemDet += '<input type="hidden" class="isNonDefaultDiscount" value="' + isNonDefaultDiscount.val() + '" >';
        itemDet += "<input type='hidden' class='uomList' value='" + uomItemList + "'>";
        itemDet += "<input type='hidden' class='exchange' value='1'>";
        itemDet += '</td>';

        $('#itemDisplayTB tr').removeClass('selectedTR');
        itemDisplayTB.append('<tr class="selectedTR">' + itemDet + '</tr>');



      
        setTimeout(function () {
           itemSearch.focus();
           itemAdd_sub_function();
           neutral_generalDiscount();
        }, 500);
    
    }


    function isThereAnyPromotion(itemAutoID) {

    }

    async function reserved_item(barcode,qty,type="add",batch_number = null,conversion = 1){

        qty = qty / conversion;

        if(isBatchPolicy != 1){
            return true;
        }

        if (barcode !== "") {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: { 'barcode':barcode,'qty':qty,'invoice_no':invoice_no,'batch_number':batch_number,'type':type },
                url: "<?php echo site_url('Pos/item_reserved_qty'); ?>",
                beforeSend: function () {
                   
                },
                success: function (data) {
             
                    if(data && data.status == 'e'){

                        myAlert('e', data.message);
                        item_set_error = 1;

                        setTimeout(function () {
                            $('#itemDisplayTB tr.selectedTR').find('.editRow').click();
                            $('#itemQty').val(1);
                            $('#pos_form').submit();
                        }, 1000);
                       
                        
                        // $('#itemDisplayTB tr.selectedTR').find('.itemQtySpan').text('1');
                        
                        return false;

                    }else{
                        item_set_error = 0;
                        return 1;
                    }

                  

                }, error: function (jqXHR, textStatus, errorThrown) {
                   
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Error while loading')
                    }
                    
                }
            });
        } 

    }

    //prompt warehouse
    function promptWareHousQty(barcode){
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': barcode,'invoice_no': invoice_no},
            url: "<?php echo site_url('Pos/item_warehouse_search'); ?>",
            success: function (data) {
                $("#itemWareHouseQty > tbody").empty();
                var appData = ''; 

                if (data.length > 0) {

                    $.each(data, function (i, val) {
                        
                            appData += '<tr class="validTR" ">';
                            
                            appData += '<td></td><td>' + val['wareHouseDescription'] +'</td><td>' + val['wareHouseLocation'] + '</td>' +'<td>' + val['unitOfMeasure'] + '</td><td>' + val['currentStock'] + '</td>';

                        // appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        // appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td><td>' + display + '</td></tr>';
                    });
                    itemWareHouseQty.append(appData);

                } else {
                    itemWareHouseQty.append('<tr><td colspan="6">No data</td></tr>');
                }

                item_warehouse_modal.modal();

            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
    });

    }

    // itemUOM.change(function () {

    //     var conversion = getNumberAndValidate($(this).find('option:selected').attr('data-value'));
    //     var defaultStk = currentStock.attr('data-value');
    //     currentStockDsp.val(defaultStk * conversion);
    //     currentStock.val(defaultStk * conversion);

    //     itemQty.val('');
    //     pos_form.bootstrapValidator('revalidateField', 'itemQty');


    // });

    itemSearch.keyup(function (e) {

        if (e.keyCode == 13) {
            item_search_loadToInvoice(itemSearch.val())
        }

        return false;

        if (e.keyCode == 8) {
            var thisVal = $(this).val();
            pos_form.bootstrapValidator('resetForm', true);
            $(this).val(thisVal);
            itemDescription2.val('');
            itemUOM.empty();
            itemUOM.css('background', '#eee');
            formInput.prop('readonly', true);
            itemUOM.prop('readonly', true);
        } else if (e.keyCode != 13) {
            formInput.val('');
            itemDescription2.val('');
            itemUOM.empty();
            itemUOM.css('background', '#eee');
            formInput.prop('readonly', true);
            itemUOM.prop('readonly', true);
        }
    });

    function fetch_related_uom(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                formInput.prop('readonly', false);
                itemUOM.prop('readonly', false);
                itemUOM.css('background', '#fff');
                itemUOM.empty();
                itemUOM.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        itemUOM.append('<option value="' + text['UnitShortCode'] + '" data-value="' + text['conversion'] + '">' + text['UnitShortCode'] + ' | ' + text['UnitDes'] + '</option>');
                    });
                    if (select_value) {
                        itemUOM.val(select_value);
                    }
                }
                re_validate();

                if (enable_BC.prop('checked') == true) {
                    $('#pos_form').bootstrapValidator();
                } else {
                    itemUOM.focus();
                }
                calculateDiscount_byPercentage();
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error in UMO fetching.')
                }
            }
        });
    }

    function re_validate() {
        pos_form.bootstrapValidator('revalidateField', 'itemUOM');
        pos_form.bootstrapValidator('revalidateField', 'itemQty');
        pos_form.bootstrapValidator('revalidateField', 'salesPrice');
    }

    salesPrice.keyup(function (e) {
        if (e.keyCode == 13) {
            $('#pos_form').bootstrapValidator();
        } else {
            var thisVal = getNumberAndValidate($(this).val());
            var disCountPer = getNumberAndValidate(disPer.val());

            if (thisVal > 0 && disCountPer > 0) {
                var m = thisVal * disCountPer * 0.01;
                disAmount.val(commaSeparateNumber(m, dPlaces));
            } else {
                disAmount.val(commaSeparateNumber(0, dPlaces));
            }

        }
    });

    $(document).on('keypress', '.number', function (event) {
        var amount = $(this).val();
        if (amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        } else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
    });

    $(document).on('click', '.deleteRow', function () {
        if ($(this).closest('tr').hasClass('editTR')) {
            myAlert('e', 'You cannot delete this item while it is edit mode.');
        } else {

            //release item
            var parentTr = $(this).closest('tr');

            var barcode = parentTr.find("td:eq(2)").text();
            var qty = parentTr.find("td:eq(5)").text();
            var type = 'delete';

            reserved_item(barcode,qty,type);

            parentTr.remove();
            trRemove();
        }

    });

    function viewItemImg(itemID,img) {
        
        //var itemImage = "no-image.png";
        var string = img;
        var parts = string.split("/");
        var itemImage = parts[parts.length - 1];
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemID,'itemImage': itemImage},
                url: "<?php echo site_url('Pos/get_item_master_image'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    $("#getCodeModal").modal("show");
                    $("#getCode").html(data).show();
                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
        });
    }


    $(document).on('click', '.editRow', function () {

        localStorage.setItem('last_update','');
        $("#pos-add-btn").removeAttr('disabled');
        var currentQty = $(this).closest('tr').find('.itemQty').val();
        clearData();
        $('#itemDisplayTB tr').removeClass('selectedTR editTR');
        $('#itemUOM').prop('disabled',false);
        $(this).closest('tr').addClass('selectedTR editTR');
        var parentTr = $('#itemDisplayTB').find('.selectedTR');
        barcode.val(parentTr.find('td:eq(2)').html());//barcode td has no id. this line works until barcode column is in second column of the datatable.
        $('#itemSearch').val($.trim(parentTr.find('td:eq(1)').html())).prop('readonly', true);

        if(isGroupBasedTaxPolicy == 0)
        {
            itemAutoID.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemID').val());
            itemDescription.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemName').val());
            currentStock.val(' ' + parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .thisCurrentStk').val());
            $('#currentStockDsp').val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .thisCurrentStk').val());
            $('#itemDescription2').val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemName').val());
            itemQty.val(parentTr.closest('td').find('.itemQty').val());
            salesPrice.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val());
            salesPricehn.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val());
            disPer.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemDis').val());
        }else {
            itemAutoID.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemID').val());
            itemDescription.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemName').val());
            currentStock.val(' ' + parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .thisCurrentStk').val());
            $('#currentStockDsp').val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .thisCurrentStk').val());
            $('#itemDescription2').val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemName').val());
            itemQty.val(parentTr.closest('td').find('.itemQty').val());
            salesPrice.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val());
            salesPricehn.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val());
            disPer.val(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemDis').val());
            selectedValueTax =  $(this).closest('tr').find('.tax_type').val();
            var discAmount = (parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val()*parentTr.closest('td').find('.itemQty').val()) * disPer.val() * 0.01;
            var totalAmount = ((parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val()*parentTr.closest('td').find('.itemQty').val()));
            var itemQtyTax = parentTr.closest('td').find('.itemQty').val();

            //.val(61.992);
            //alert(totalAmount);
            // load_line_tax_amount_gpos_test(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemID').val(),totalAmount,discAmount,itemQtyTax);

        }

        disPerDecimal = parseFloat(disPer.val());
        if(isNaN(disPerDecimal)){
            disPerDecimal = 0;
        }
        disPer.val(disPerDecimal.toFixed(2));
        if(isGroupBasedTaxPolicy == 0){
            var discountAmount_tmp = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemDis').val();
            var salesPrice_tmp = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val();
        }else {
            var discountAmount_tmp = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemDis').val();
            var salesPrice_tmp = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val();
        }


        var discountAmount_tmp = parseFloat(discountAmount_tmp);
        if (discountAmount_tmp > 0) {
            var unitDiscount = parseFloat(salesPrice_tmp) * (parseFloat(discountAmount_tmp) / 100)
            disAmount.val(unitDiscount.toFixed(2));
        }
        if(isGroupBasedTaxPolicy == 0) {
            var edit_umo = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemUOM').val();
        }else {
            var edit_umo = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemUOM').val();
        }

        init_umo = edit_umo;

      //  itemUOM.append('<option value="' + edit_umo + '" >' + edit_umo + '</option>');
        $('.formInput').not('#currentStockDsp').prop('readonly', false);
        if(isGroupBasedTaxPolicy == 0) {
            var isPromo = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .isPromo').val();
        }else {
            var isPromo = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .isPromo').val();
        }

        var setDiscount = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .isNonDefaultDiscount').val();
        $('#isNonDefaultDiscount').val(setDiscount);

        var uomList2 = JSON.parse(parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .uomList').val());
        edit_item_umo_detail = uomList2;
        itemUOM.empty();
        $.each(uomList2, function(index,itemObj){
            if(edit_umo == itemObj.UnitShortCode){
                itemUOM.append('<option selected value="' + itemObj.UnitShortCode + '" >' + itemObj.UnitShortCode + '</option>');
            }else{
                itemUOM.append('<option value="' + itemObj.UnitShortCode + '" >' + itemObj.UnitShortCode + '</option>');
            }
        });

        edit_item_current_stock = parentTr.find('td:eq('+localStorage.getItem('dataStoredTD')+') .thisCurrentStk').val();
        
       
       // $('#isNonDefaultDiscount').val(setDiscount);

        if(isPromo==1){

            disPer.prop('readonly',true);
            disAmount.prop('readonly',true);
        }else{

            disPer.prop('readonly',false);
            disAmount.prop('readonly',false);
        }

        $('#is-edit').val(1);
        // $("#itemQty").val(currentQty);
        if(Object.keys(uomList2).length > 1){
            $("#itemQty").val(1);
        }else{
            $("#itemQty").val(currentQty);
        }
        
        setTimeout(function () {
            $("#itemQty").select();
        }, 200);

    });

    $(document).on('change', '#itemUOM', function () {

        var selected_short_code = $('#itemUOM :selected').val();
        
        $.each(edit_item_umo_detail, function(index,itemObj){
            if(itemObj.UnitShortCode == selected_short_code){
                $('#salesPrice').val(itemObj.salesPrice);
                // currentStock
                var temp_currenct_stock = edit_item_current_stock;

                if(itemObj.conversion == 1){
                    $('#currentStockDsp').val(temp_currenct_stock);
                }else{
                    $('#currentStockDsp').val(temp_currenct_stock*itemObj.conversion);
                }

               // $('#itemQty').val(1);
            }
        });
        
    });

    function trRemove() {

        addTrNumber();
       
        update_invoice_item_count();
        getTot();
        getDiscountTot();
        getNetTot();
        calculateDiscount_byPercentage();
    }


    function update_invoice_item_count(){

        var noofitems = 0;
        $('#itemDisplayTB tr').each(function (index, item) {
            var noqty = $(item).find('td:eq(5)').text();
            if (noqty) {
                noofitems = noofitems + parseInt(noqty);
            }
        });
        itemCount.html(parseInt(noofitems));

    }

    /*disPer.keyup(function () {
        var disc = $.trim($(this).val());
        if (disc != '') {
            if (disc > 100) {
                myAlert('e', 'Discount Percentage must be lesser than 100');
                $(this).val('');
                disAmount.val('');
            } else if (disc > 0) {
                var price = $.trim(salesPrice.val());
                if (price != '') {
                    var m = price * disc * 0.01;
                    disAmount.val(commaSeparateNumber(m, dPlaces));
                }
            } else if (disc == 0) {
                disAmount.val(commaSeparateNumber(0, dPlaces));
            }
        }
    });*/

    /*disAmount.keyup(function () {
        var disAmount_val = $(this).val();
        if (disAmount_val != '') {
            disPer.val('');
            var salesPrice_val = getNumberAndValidate(salesPrice.val());
            if (salesPrice_val < disAmount_val) {
                myAlert('e', 'Discount amount could not be greater than sales price');
                $(this).val('');
                disPer.val('');
            } else if (disAmount_val > 0) {
                var discountInPercentage = parseFloat((disAmount_val * 100) / salesPrice_val);
                disPer.val(discountInPercentage);
            }

        } else {
            disPer.val('');
        }
    });*/

    function getTot() {

        var sum = 0;
        var taxAmount = 0;
        $('.totalAmount').each(function () {
            sum += parseFloat($(this).val());

        });
        totVal.val(sum);
        totSpan.text(commaSeparateNumber(sum, dPlaces));
        return sum;
    }

    function getDiscountTot() {
        var sum = 0;
        $('.discountAmount').each(function () {
            sum += parseFloat($(this).val());
        });

        discVal.val(sum);
        discSpan.text(commaSeparateNumber(sum, dPlaces));

        return sum;
    }

    function getNetTot() {

        var sum = 0;
        var taxAmount = 0;
        $('.netAmount').each(function () {
            sum += parseFloat($(this).val());
        });

        if(isGroupBasedTaxPolicy == 1){
            $('.taxamountCal').each(function () {
                if (isNaN(parseFloat($(this).val())) == true) {
                    taxAmount += 0;
                } else {
                    taxAmount += parseFloat($(this).val());
                }
            });

            netTotSpan.text(commaSeparateNumber(((sum)+(taxAmount)), dPlaces));
            $('#tenderNetTotal').val(commaSeparateNumber(((sum)+(taxAmount)), dPlaces));
            netTotVal.val((sum)+(taxAmount));

            return ((sum)+(taxAmount));

        }else {
            netTotSpan.text(commaSeparateNumber(((sum)), dPlaces));
            $('#tenderNetTotal').val(commaSeparateNumber(((sum)), dPlaces));
            netTotVal.val(sum);

            return sum;
        }



    }

 /*   function addTrNumber() {
        var i = 0;

        $('#itemDisplayTB tr').each(function () {
            $(this).find('td:eq(0)').html(i);
            $(this).closest('tr').find('.tax_type').attr('id', 'tax_type_'+i);
            /!*if($(this).closest('tr').find('.itemID').val() > 0 && ($(this).closest('tr').find('.tax_type').val()=='' || $(this).closest('tr').find('.tax_type').val()=='undefined')){*!/
                fetch_tax_drop(i,$(this).closest('tr').find('.itemID').val());
           /!* }*!/
            i+= 1;
        });
    }*/
    function addTrNumber() {
        var i = 0;
        $('#itemDisplayTB tr').each(function () {
            $(this).find('td:eq(0)').html(i);

            if(isGroupBasedTaxPolicy ==1){
                $(this).closest('tr').find('.tax_type').attr('id', 'tax_type_'+i);

                if($(this).closest('tr').find('.itemID').val() > 0 && ($(this).closest('tr').find('.tax_type').val()=='' || $(this).closest('tr').find('.tax_type').val()=='undefined'))
                {
                    fetch_tax_drop(i,$(this).closest('tr').find('.itemID').val());

                }
            }
            i += 1;
        });
    }

    tenderPay.keyup(function () {
        var sum = 0;
        tenderPay.each(function () {
            var thisAmount = getNumberAndValidate($(this).val());
            sum += (thisAmount == '') ? 0 : parseFloat(thisAmount);
        });
        $('#tenderAmountTotal').val(commaSeparateNumber(sum, dPlaces));

        var tenderNetTotal = parseFloat(removeCommaSeparateNumber($('#tenderNetTotal').val()));
        if (sum > tenderNetTotal) {
            var change = sum - tenderNetTotal;
            $('#tenderChangeAmount').val(commaSeparateNumber(change, dPlaces));
            $('#tenderDueAmount').val(commaSeparateNumber(0, dPlaces));
        } else {
            var due = tenderNetTotal - sum;
            $('#tenderDueAmount').val(commaSeparateNumber(due, dPlaces));
            $('#tenderChangeAmount').val(commaSeparateNumber(0, dPlaces));
        }
    });

    tenderPay.bind('focus blur', function () {
        //$(this).css('border', 'none');
        //$(this).css('background', 'green');
    });

    $('#tenderBtn').click(function () {
        var errorCount = 0;
        var cashAmount = getNumberAndValidate($('#cashAmount').val());
        var cardAmount = getNumberAndValidate($('#cardAmount').val());
        var chequeAmount = getNumberAndValidate($('#chequeAmount').val());
        var giftCardAmount = getNumberAndValidate($('#giftCard').val());
        var creditNoteAmount = getNumberAndValidate($('#creditNote').val());
        var creditSalesAmount = getNumberAndValidate($('#creditSalesAmount').val());
        var customerID = $('#customerID').val();
        var card_chequeTot = cardAmount + chequeAmount + giftCardAmount + creditNoteAmount;
        var netTot = getNumberAndValidate(netTotVal.val());
        var totalPayment = cashAmount + cardAmount + chequeAmount + creditNoteAmount;
        //quenty
        if (totalPayment < netTot && customerID == 0) {
            myAlert('e', 'Payment not equal to Net total.');
            errorCount++;
        }
        if (card_chequeTot > netTot) {
            myAlert('e', 'Card and Cheque Amount sum can not be greater than net total.');
            errorCount++;
        }

        if ((creditSalesAmount < netTot || creditSalesAmount > netTot) && creditSalesAmount > 0) {
            myAlert('e', 'Payment not equal to Net total.');
            errorCount++;
        }

        if (errorCount == 0) {
            $('#h_cardAmount').val(cardAmount);
            $('#h_cashAmount').val(cashAmount);
            $('#h_chequeAmount').val(chequeAmount);
            $('#h_giftCardAmount').val(giftCardAmount);
            $('#h_creditNoteAmount').val(creditNoteAmount);
            $('#h_creditSalesAmount').val(creditSalesAmount);

            var postData = $('#my_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Pos/invoice_create'); ?>",
                success: function (data) {
                    myAlert(data[0], data[1]);
                    $('#isInvoiced').val('');
                    if (data[0] == 's') {
                        var doSysCode_refNo = $('#doSysCode_refNo').text();
                        invoicePrint(data[2], data[3], doSysCode_refNo);
                        searchByKeyword(1);
                    }

                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        }
    });

    $('#customerSearch').keyup(function (e) {
        if (e.keyCode != 9 && e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 13 && e.keyCode != 27 && e.keyCode != 46) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'key': this.value},
                url: "<?php echo site_url('Pos/customer_search'); ?>",
                success: function (data) {
                    $("#customerSearchTB > tbody").empty();
                    var appData = '';

                    if (data.length > 0) {
                        var display = '';
                        $.each(data, function (i, val) {
                            if (val['iscardexist'] > 0 && val['iscardexist'] != 2) {
                                display = val['loyalitycardno'];
                            } else if (val['iscardexist'] != 2) {
                                display = '<a onclick="generate_loyality(' + val['customerAutoID'] + ',' + val['customerTelephone'] + ')">Generate</a>';
                            }
                            appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                            appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td><td>' + display + '</td></tr>';
                        });
                        customerSearchTB.append(appData);
                    } else {
                        customerSearchTB.append('<tr><td colspan="3">No data</td></tr>');
                    }

                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        }
    });

    function selectEmployee() {
        $('#customerID').val(selectedCusArray[0]);
        $('#customerCode').val(selectedCusArray[2]);
        $('.customerSpan').text(selectedCusArray[3]);
        var cus = $('#customerID').val();
        if (cus != 0) {
            set_customer_inloyalty(selectedCusArray[0]);
            $('#creditsalesfield').removeClass('hidden');
        } else {
            $('#creditsalesfield').addClass('hidden');
        }
        $('#customer_modal').modal('hide');
        y = 0;
    }

    $(document).on('click', '#customerSearchTB tr', function () {
        if ($(this).hasClass('validTR') == true) {
            $('#customerSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');
            var dataID = $(this).attr('data-id');
            var dataCurrency = $(this).attr('data-currency');
            var dataCode = $.trim($(this).find('td:eq(1)').text());
            var dataName = $.trim($(this).find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
        } else {
            myAlert('w', 'Please select a valid customer')
        }
    });

    $(document).on('click', '#invoiceSearchTB tr', function () {
        if ($(this).hasClass('headerTR') == false) {
            $('#invoiceSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');


        }
    });

    $(document).on('click', '#itemDisplayTB tr', function () {
        qty_adjustProcess();

        if ($(this).hasClass('header_tr') == false) {
            $(this).addClass('selectedTR');

            var dataID = $(this).attr('data-id');
            var dataCurrency = $(this).attr('data-currency');
            var dataCode = $.trim($(this).find('td:eq(1)').text());
            var dataName = $.trim($(this).find('td:eq(2)').text());
            var img = $.trim($(this).find('td:eq(10) .item-image-hidden').val());

            $('#item-image').attr('src', img);
            selectedItemArray = [dataID, dataCurrency, dataCode, dataName];
        }
    });

    $(document).on('click', '#creditNoteTB tr', function () {
        if ($(this).hasClass('validTR') == true) {
            $('#creditNoteTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');
        } else {
            myAlert('w', 'Please select a valid note')
        }
    });

    function selectInvoice() {
        var invTB = $('#invoiceSearchTB tr.selectedTR');
        var selectedRowCount = 0;
        $.each(invTB, function () {
            selectedRowCount++
        });
        if (selectedRowCount == 1) {
            var selectedInv = $('#invoiceSearchTB').find('tr.selectedTR').attr('data-id');
            $("#itemDisplayTB > tbody").empty();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'holdID': selectedInv},
                url: "<?php echo site_url('Pos/load_holdInv'); ?>",
                success: function (data) {
                    var masterData = data[0];
                    var invItems = data[1];

                    $('#customerID').val(masterData['customerID']);
                    $('#_trCurrency').val(masterData['transactionCurrency']);
                    $('#customerCode').val(masterData['customerCode']);
                    $('.customerSpan').text(masterData['cusName']);
                    $('#isInvoiced').val(masterData['invoiceID']);
                    itemCount.html(0);
                    exceededItemArray = [];

                    var appendDet = '';
                    var isCurrentQtyExceed = 0;
                    $.each(invItems, function (i, elm) {
                        var qty = elm['qty'];
                        var qtys = elm['qty'];
                        var convertUOM = elm['conversionRateUOM'];
                        var currentStk = elm['currentStk'] * convertUOM;

                        if (qty > currentStk) {
                            exceededItemArray[isCurrentQtyExceed] = [];
                            exceededItemArray[isCurrentQtyExceed]['itemCode'] = elm['itemSystemCode'];
                            exceededItemArray[isCurrentQtyExceed]['requestItem'] = elm['itemDescription'];
                            exceededItemArray[isCurrentQtyExceed]['UMO'] = elm['unitOfMeasure'];
                            exceededItemArray[isCurrentQtyExceed]['requestQty'] = qty;
                            exceededItemArray[isCurrentQtyExceed]['availableQty'] = currentStk;
                            isCurrentQtyExceed++;
                        }


                        if (qty > currentStk) {
                            qty = currentStk
                        }
                        var amount = (qtys * elm['price']);
                        var disCountPer = getNumberAndValidate(elm['discountPer']);
                        let discountVal = (elm['discountAmount'] > 0) ? qtys * elm['discountAmount'] : 0;
                        var total = amount - discountVal;
                        discountVal = discountVal.toFixed(2);
                        var isPromo = $("#isPromo").val();
                        var tax_select = '<select class="lntax_drop tax_type" onchange="load_line_tax_amount_gpos(this)" style="width: 110px;color: black;" id="tax_type_0"><option value="">Select Tax Type </option></select>';

                        load_line_tax_amount_gpos_test(qty,amount,discountVal,elm['itemAutoID'])

                        appendDet += '<tr><td></td>';
                        appendDet += '<td>' + elm['itemSystemCode'] + '</td>';
                        appendDet += '<td>' + elm['barcode'] + '</td>';
                        appendDet += '<td>' + elm['itemDescription'] + '</td>';
                        appendDet += '<td>' + elm['unitOfMeasure'] + '</td>';
                        appendDet += '<td align="right">' + qtys + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(elm['price'], dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(elm['discountPer'], dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(discountVal, dPlaces) + '</td>';
                        if(isGroupBasedTaxPolicy ==1){
                            appendDet += '<td align="center">' + tax_select + '</td>';
                            appendDet += '<td align="right"><span class="taxAmount"> 0.00</span></td>';

                        }
                        appendDet += '<td align="right">' + commaSeparateNumber(total, dPlaces) + '</td>';
                        appendDet += '<td align="right">';
                        appendDet += '<span class="glyphicon glyphicon-pencil editRow" style="color: #3c8dbc; position: static"></span> | ';
                        appendDet += '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>';
                        appendDet += '<input type="hidden" name="taxamountCal[]" class="taxamountCal"  value="' + elm['taxAmount'] + '">';
                        appendDet += '<input type="hidden" name="itemID[]" class="itemID" value="' + elm['itemAutoID'] + '" >';
                        appendDet += '<input type="hidden" name="itemName[]" class="itemName" value="' + elm['itemDescription'] + '" >';
                        appendDet += '<input type="hidden" name="itemUOM[]" class="itemUOM" value="' + elm['unitOfMeasure'] + '" >';
                        appendDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + qtys + '" >';
                        appendDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + elm['price'] + '" >';
                        appendDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disCountPer + '" >';
                        appendDet += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + discountVal + '" >';
                        appendDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStk + '" >';
                        appendDet += '<input type="hidden" class="discountAmount" value="' + discountVal + '" >';
                        appendDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                        appendDet += '<input type="hidden" class="netAmount" value="' + total + '" >';
                        appendDet += '<input type="hidden" class="item-image-hidden" value="<?php echo base_url('images/item/');?>/' + elm['itemImage'] + '" >';
                        appendDet += '<input type="hidden" class="isPromo" value="' + isPromo + '" >';
                        appendDet += '</td></tr>';
                        itemCount.html(parseInt($('.itemCount:first').text()) + 1);
                        itemUOM.empty();
                    });

                    itemDisplayTB.append(appendDet);
                    itemAdd_sub_function();

                    setTimeout(function () {
                        recall_modal.modal('hide');
                    }, 500);
                    calculateDiscount_byPercentage();

                    $("#gen_disc_amount, #gen_disc_amount_hide").val(masterData['discountAmount']);
                    calculate_general_discount($("#gen_disc_amount"), 1);

                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Error in loading hold invoice details .');
                    }

                }
            });
        } else {
            myAlert('e', 'Please Select a Invoice to recall.');
        }
    }

    itemQty.keyup(function () {
        var thisQty = $.trim($(this).val());
        var currentQty = parseFloat($.trim(currentStock.val()));
        thisQty = (thisQty != '') ? parseFloat(thisQty) : parseFloat(0);


    });

    $(document).on('keyup', '#qtyAdj', function (e) {
        if (e.keyCode == 13) {
            qty_adjustProcess();
        } else {
            var thisVal = $.trim($(this).val());
            var availableStk = parseFloat($.trim($(this).attr('data-stock')));
            thisVal = parseFloat(thisVal);

            /*if($.isNumeric(thisVal) && availableStk < thisVal ){
             $(this).val('');
             myAlert('e', 'Available Stock is only '+availableStk);
             }*/
        }
    });

    $(document).on('onChange', '#qtyAdj', function (e) {
        qty_adjustProcess();
    });

    function qty_adjustProcess() {
        var lastSelectedTR = $('#itemDisplayTB tr.selectedTR');

        //validate if a qty adjustment not finished
        if (lastSelectedTR.find('td:eq(4) #qtyAdj').length) {

            var qtyAdj = $('#qtyAdj');
            var lastAdjQty = $.trim(qtyAdj.val());
            lastAdjQty = (lastAdjQty == '' || lastAdjQty == '.') ? 0 : lastAdjQty;
            lastAdjQty = parseFloat(lastAdjQty);
            qtyAdj.closest('td').css('padding', '5px');
            qtyAdj.remove();

            if (lastAdjQty != 0) {
                lastSelectedTR.find('td:eq(4)').html(lastAdjQty);
                lastSelectedTR.find('td:eq(10) .itemQty').val(lastAdjQty);
                var itemPrice = parseFloat(lastSelectedTR.find('td:eq(10) .itemPrice').val());
                var itemDisPer = $.trim(lastSelectedTR.find('td:eq(10) .itemDis').val());
                itemDisPer = (itemDisPer == '') ? 0 : parseFloat(itemDisPer);

                var amount = (itemPrice * lastAdjQty);
                var total = amount;

                lastSelectedTR.find('td:eq(6)').text(commaSeparateNumber(amount, dPlaces));


                if (itemDisPer != 0) {
                    var discountAmount = amount * itemDisPer * 0.01;
                    lastSelectedTR.find('td:eq(8)').text(commaSeparateNumber(discountAmount, dPlaces));
                    lastSelectedTR.find('td:eq(9)').text(commaSeparateNumber((total - discountAmount), dPlaces));

                    lastSelectedTR.find('td:eq(10) .discountAmount').val((discountAmount));
                    lastSelectedTR.find('td:eq(10) .totalAmount').val((total));
                    lastSelectedTR.find('td:eq(10) .netAmount').val((total - discountAmount));

                } else {
                    lastSelectedTR.find('td:eq(9)').text(commaSeparateNumber(total, dPlaces));
                    lastSelectedTR.find('td:eq(10) .totalAmount').val(total);
                    lastSelectedTR.find('td:eq(10) .netAmount').val(total);
                }

            } else {
                lastSelectedTR.find('td:eq(4)').html(qtyAdj.attr('data-value'));
                lastSelectedTR.find('td:eq(10) .itemQty').val(qtyAdj.attr('data-value'));
            }

            getTot();
            getDiscountTot();
            getNetTot();
            calculateDiscount_byPercentage();

        }

        $('#itemDisplayTB tr').removeClass('selectedTR');
    }

    function save_moreCardDetails() {
        var hCardAmount = getNumberAndValidate($('#cardAmount_cardDet').val());
        var bankID = $.trim($('#bank').val());
        if (hCardAmount != 0 && bankID != '') {
            $('#cardAmount').val(hCardAmount);
            $('#h_cardAmount').val(hCardAmount);
            _referenceNO.val($.trim($('#referenceNO').val()));
            _cardNumber.val($.trim($('#cardNumber').val()));
            _bank.val(bankID);
            tenderPay.keyup();
            cardDet_modal.modal('hide');
        } else if (hCardAmount == 0) {
            myAlert('e', 'Please enter a valid card amount');
        } else if (bankID == '') {
            myAlert('e', 'Please select a bank');
        }
    }

    function save_moreChequeDetails() {
        var hChequeAmount = getNumberAndValidate($('#chequeAmount_cheqDet').val());
        var chequeNumber = $.trim($('#chequeNumber').val());
        if (hChequeAmount != 0 && chequeNumber != '') {
            $('#chequeAmount').val(hChequeAmount);
            $('#h_chequeAmount').val(hChequeAmount);
            _chequeCashDate.val($.trim($('#cashDate').val()));
            _chequeNO.val(chequeNumber);
            tenderPay.keyup();
            cheque_modal.modal('hide');
        } else if (hChequeAmount == 0) {
            myAlert('e', 'Please enter a valid cheque amount');
        } else if (chequeNumber == '') {
            myAlert('e', 'Please enter a valid cheque no');
        }
    }

    function getNumberAndValidate(thisVal, dPlace = 2) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        } else {
            return parseFloat(0);
        }
    }

    $('.searchData').click(function () {
        var thisID = $(this).attr('id');
        if (thisID == 'creditNote') {
            $('#creditNote_modal').modal({backdrop: 'static'});
            $("#creditNoteTB > tbody").empty();
            creditNoteTB.append('<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>');
        } else if (thisID == 'cardAmount') {
            cardDet_modal.modal({backdrop: 'static'});
            setTimeout(function () {
                $('#cardAmount_cardDet').focus();
            }, 500);
        } else if (thisID == 'giftCard') {
            $('#giftCard_modal').modal({backdrop: 'static'});
        } else if (thisID == 'chequeAmount') {
            $('#cheque_modal').modal({backdrop: 'static'});
        }

    });

    function session_close() {
        $("#isStart").val(0);
        $(".tillModal_close").show();
        $("#tillModal_title").text("Day End");
        $("#tillSave_Btn").attr("onclick", "shift_close()");
        till_modal.modal({backdrop: "static"});
    }

    function invoicePrint(invID, invCode, doSysCode_refNo) {

        //window.open("<?php echo site_url('Pos/invoice_print'); ?>/"+invID+"/"+invCode, "", "width=700,height=400");
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            data: {'doSysCode_refNo': doSysCode_refNo, 'isVoid': 0},
            url: "<?php echo site_url('Pos/invoice_print'); ?>/" + invID + "/" + invCode,
            success: function (data) {
                $('#print_template').modal({backdrop: 'static'});
                $('#print_content').html(data);
                $("#gposvoidbillbtn").addClass('hidden');
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error in print call. ' + jqXHR.status + ': ' + jqXHR.statusText)
                }

            }
        });
    }

    $('#creditNote_search').keyup(function () {
        var key = $(this).val();
        var letterCount = key.length;

        if (letterCount > 1) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'key': this.value},
                url: "<?php echo site_url('Pos/creditNote_search'); ?>",

                success: function (data) {
                    $("#creditNoteTB > tbody").empty();
                    var appData = '';

                    if (data.length > 0) {
                        $.each(data, function (i, val) {
                            //, documentSystemCode, salesReturnDate, netTotal
                            appData += '<tr class="validTR" data-id="' + val['salesReturnID'] + '"  data-amount="' + val['netTotal'] + '">';
                            appData += '<td>' + (i + 1) + '</td><td>' + val['documentSystemCode'] + '</td><td>' + val['salesReturnDate'] + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(val['netTotal'], dPlaces) + '</td></tr>';
                        });
                        creditNoteTB.append(appData);
                    } else {
                        creditNoteTB.append('<tr><td colspan="4">No data</td></tr>');
                    }

                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Error in Customer search.')
                    }
                }
            });
        }
    });

    function clearPayments() {
        $("#isCreditSale").val(0);
        $("#paid").val(0);
        $("#paid_temp").html(0);
        $(".paymentInput").val(0);
        $('.cardRef').val('');
    }


    function selectCreditNote() {
        //clearPayments();
        calculateReturn();

        var discount = parseFloat(0);
        var advancePaymets = $("#delivery_advancePaymentAmount").val();
        var deduction = discount + advancePaymets;
        var subTotal = $("#total_payable_amt").val();
        var netTotal = subTotal - deduction;


        var returnID = creditNoteTB.find('tr.selectedTR').attr('data-id');
        var returnAmount = creditNoteTB.find('tr.selectedTR').attr('data-amount');

        if (netTotal < returnAmount) {
            myAlert('e', 'You can not select the credit note greater than Net Total.');
            return false;
        }

        $('#creditNote-invID').val(returnID);
        //alert(commaSeparateNumber(returnAmount, dPlaces));
        $('#creditNote').val(returnAmount);
        $('.CreditNoteAmnt').val(returnAmount);
        calculatePaidAmount($('.CreditNoteAmnt').val());
        tenderPay.keyup();
    }

    $('#clear-credit-amount').click(function () {
        $('#creditNote-invID').val('');
        $('#creditNote').val('');
        tenderPay.keyup();
    });

    $('#clear-card-amount').click(function () {
        $('#cardAmount_cardDet').val('');
        $('#cardAmount').val('');
        $('#h_cardAmount').val('');
        $('#referenceNO').val('');
        $('#cardNumber').val('');


        _referenceNO.val('');
        _cardNumber.val('');
        _bank.val('');

        tenderPay.keyup();
    });

    $('#clear-cheque-amount').click(function () {
        $('#cardAmount_cardDet').val('');
        $('#chequeAmount').val('');
        $('#h_chequeAmount').val('');
        $('#referenceNO').val('');
        $('#cardNumber').val('');


        _chequeNO.val('');
        _chequeCashDate.val('');
        _bank.val('');

        tenderPay.keyup();
    });

    function print_pos_report() {
        $.print("#print_content");
        return false;
    }

    $('#invoiceCode').autocomplete({
        serviceUrl: '<?php echo site_url();?>Pos/invoice_searchLiveSearch/',
        onSelect: function (suggestion) {

        }
    });

    $('#invoiceCodeDate').autocomplete({
        serviceUrl: '<?php echo site_url();?>Pos/invoice_searchLiveSearch/',
        onSelect: function (suggestion) {

        }
    });

    $('#invoiceItemCode').autocomplete({
        serviceUrl: '<?php echo site_url();?>Pos/invoice_itemLiveSearch/',
        onSelect: function (suggestion) {
            $('#itemIdhn').val(suggestion.data)
        }
    });
</script>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
<script type="text/javascript">
    function initNumPad() {
        $('.numpad').unbind();
        $('.numpad').numpad();
    }

    var y = 0;
    var st = 0;
    var printcunt = 0;
    shortcut.add("F1", function () {
        if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            submit_pos_payments();
        } else {
            open_pos_payments_modal();
        }
    });

    shortcut.add("F4", function () {
        searchItem_modal();
    });

    shortcut.add("F9", function () {
        $("#itemSearch").focus();
    });

    shortcut.add("F2", function () {
        hold_invoice();
    });

    shortcut.add("F3", function () {
        checkPosAuthentication(14);
    });

    shortcut.add("F4", function () {
        var fnTr = $('#itemDisplayTB tr.selectedTR');

    });

    shortcut.add("F5", function () {
        newInvoice();
    });

    shortcut.add("F6", function () {
        open_customer_modal();
    });

    shortcut.add("F7", function () {
        checkifItemExsist();
    });

    shortcut.add("F8", function () {
        //adjust_qty();
        //alert();
        //showqtyeditfield(this);
        $(".selectedTR").find("#_showqtyeditfield").click();
    });

    shortcut.add("tab", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            $('#customerSearchTB tbody').find('tr:first').removeClass('selectedTR');
            $('#customerSearchTB tbody').find('tr:first').addClass('selectedTR');

            var dataID = $('#customerSearchTB tbody').find('tr:first').attr('data-id');
            var dataCurrency = $('#customerSearchTB tbody').find('tr:first').attr('data-currency');
            var dataCode = $.trim($('#customerSearchTB tbody').find('tr:first').find('td:eq(1)').text());
            var dataName = $.trim($('#customerSearchTB tbody').find('tr:first').find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
        } else {
            preventDefault()
        }
    });


    shortcut.add("F10", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            $("#customerSearch").focus();
        }
    });

    shortcut.add("down", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            goDownTable('customerSearchTB');
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            goDownTable('posg_search_item_modal');
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            goDownTable('posg_payment_modal_table');
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            goDownTable('invoiceSearchTB');
        } else {
            goDownTable('itemDisplayTB');
        }
    });

    shortcut.add("up", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            goUpTable('customerSearchTB');
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            goUpTable('posg_search_item_modal');
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            goUpTable('posg_payment_modal_table');
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            goUpTable('invoiceSearchTB');
        } else {
            /*Customer */
            goUpTable('itemDisplayTB');
        }
    });


    shortcut.add("ctrl+Q", function () {
        $("#disPer").focus();
        $("#disPer").prop('readonly',false);
    });


    shortcut.add("ctrl+P", function () {
        //Clear tax applicable array
        tax_item_arr = [];

        payInCashAutomated();
    });

    shortcut.add("ctrl+D", function () {
        $("#disAmount").focus();
    });

    shortcut.add("ctrl+E", function () {
        $("#salesPrice").focus();
    });

    shortcut.add("ctrl+B", function () {
        //item batch
       // $(".selectedTR").find("#_showqtyeditfield").click();

        open_item_batch_modal();
   

    });

    shortcut.add("ctrl+I", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
        } else {
            $('#itemDisplayTB .selectedTR .editRow').click()
        }
    });

    shortcut.add("ctrl+F", function (e) {
        if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            $("#searchKeyword").focus();
        } else {
            e.preventDefault();
        }
    });

    shortcut.add("esc", function () {
        $("#itemSearch").focus();
    });


    function goUpTable(id) {
        var rowIndex = $('#' + id + ' tbody').find('tr.selectedTR').index();
        var rowcount = document.getElementById(id).rows.length;

        if (rowIndex == 0) {
            var count = rowcount - 2;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + count + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + count + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + count + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + count + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + count + ')').find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            //$(".fixHeader_Div").scrollTop(200000);
        } else {
            var x = 26;
            var index = rowIndex - 1;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + index + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            st = x + st;
            y = y - 26;
            //var scroll=index*20;
            $(".fixHeader_Div").scrollTop(-st);
        }
    }

    function goDownTable(id) {
        var rowIndex = $('#' + id + ' tbody').find('tr.selectedTR').index();
        var rowcount = document.getElementById(id).rows.length;
        if ((rowIndex + 2) == rowcount) {
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:first').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:first').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:first').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:first').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:first').find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            //$(".fixHeader_Div").scrollTop(0);
        } else {
            var x = 26;

            var index = rowIndex + 1;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + index + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(3)').text());
            dataCurrency = (dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            y = x + y;
            st = st - 26;
            //var scroll=index*20;
            $(".fixHeader_Div").scrollTop(y);
        }
    }

    function deleteRow() {
        //release item
        var barcode = $(".selectedTR").find("td:eq(2)").text();
        var qty = $(".selectedTR").find("td:eq(5)").text();
        var type = 'delete';

        reserved_item(barcode,qty,type);

        $('#itemDisplayTB .selectedTR').remove();
        getTot();
        getDiscountTot();
        getNetTot();
        calculateDiscount_byPercentage();
    }


    shortcut.add("enter", function (e) {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            selectEmployee();
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            $("#itemSearchResultTblBody .selectedTR").find('button').click()
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            $('#posg_payment_modal_table .selectedTR').find('button').click();
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            selectInvoice();
        } else {
            e.preventDefault()
        }
    });

    /*shortcut.add("esc", function () {
     if (typeof $(customer_modal).data()['bs.modal'] !== "undefined") {
     if ($(customer_modal).data()['bs.modal'].isShown) {
     $('#customer_modal').modal('hide');
     y = 0;
     }
     }
     });*/

    shortcut.add("delete", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            defaultCustomer();
        } else {
            deleteRow()
        }
    });

    shortcut.add("F11", function () {
        if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
        } else {
            $('#gen_disc_percentage').select();
        }

    });

    shortcut.add("F12", function () {
        if (printcunt == 0) {

            //Clear tax applicable array
            tax_item_arr = [];

            payInCashAutomated();
        } else if (printcunt == 1) {
            if (typeof $('#print_template').data()['bs.modal'] !== "undefined" && $('#print_template').data()['bs.modal'].isShown) {
                print_pos_report();
            } else {
                printcunt = 0
            }
        }

    });

    function hold_invoice() {
        var tot = getTot();
        if (tot > 0) {
            swal({
                    title: "Are you sure ?",
                    text: "You want to hold this invoice!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    var postData = $('#my_form').serializeArray();
                    postData.push({'name': 'customerID', 'value': $('#customerID').val()});
                    var d = new Date();
                    var clientDateTime = d.getFullYear()+'-'+ (d.getMonth() + 1) + '-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
                    postData.push({'name': 'clientDateTime', 'value': clientDateTime});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postData,
                        url: "<?php echo site_url('Pos/invoice_hold'); ?>",
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            $('#isInvoiced').val('');
                            if (data[0] == 's') {
                                var zero = 0;
                                newInvoice(1);
                                $('#totSpan').html(zero.toFixed(dPlaces));
                                $('#netTotSpan').html(zero.toFixed(dPlaces));
                                $("#netTot_after_g_disc_div").html(zero.toFixed(dPlaces));
                                $("#netTot_after_g_disc").val(zero);
                                $("#gen_disc_percentage").val(zero);
                            }


                        }, error: function (jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == false) {
                                myAlert('w', 'No Internet, Please try again');
                            } else {
                                myAlert('e', 'Error in hold invoice process.')
                            }

                        }
                    });
                }
            );
        } else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    function newInvoice(isFromInvCreate = null) {
        var tot = getTot();

        //rest tax array
        tax_item_arr = [];

        if (tot > 0 && isFromInvCreate == null) {
            swal({
                    title: "Are you sure ?",
                    text: "This invoice cannot be recalled !",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    var zero = 0;
                    clearForNewInvoice();
                    clearform_pos_receipt();
                    $('#totSpan').text(zero.toFixed(dPlaces));
                    $('#netTotSpan').text(zero.toFixed(dPlaces));
                    $('.itemCount').text(zero);
                    $('#isInvoiced').val('');
                    $("#netTot_after_g_disc_div").html(zero.toFixed(dPlaces));
                    $("#netTot_after_g_disc").val(zero);
                    $("#gen_disc_percentage").val(zero);
                    $("#netTotVal").val(zero.toFixed(dPlaces));
                    pos_form[0].reset();
                    $('#itemSearch').attr('readonly', false);
                    reset_generalDiscount();
                    /**window.location = "<?php //echo site_url('Pos/')?>"; */
                }
            );
        } else {
            clearForNewInvoice();
            /**window.location = "<?php //echo site_url('Pos/')?>";*/
        }
    }


    function open_customer_modal() {

        if($('#itemDisplayTB tr').length > 1){
            myAlert('e', 'Please remove added items to proceed.');
            return false;
        }

        customer_modal.modal({backdrop: 'static'});
        setTimeout(function () {
            $('#customerSearch').focus();
        }, 500);
        LoadCustomers();

    }

    function open_item_batch_modal() {
        // item batch

        if(isBatchPolicy != 1){
            return true;
        }

        item_batch_modal.modal({backdrop: 'static'});

        var selected_barcode = $(".selectedTR").find("td:eq(2)").text();
        var quantity = $(".selectedTR").find("td:eq(5)").text();


        $('#item_bar_code').val('');
        $('#item_bar_code').val(selected_barcode);
        $('#batch_modal_qty').text(quantity);

        // setTimeout(function () {
        //     $('#customerSearch').focus();
        // }, 500);
        LoadBatchList();

    }

    function deleteItem() {
        var count = 0;
        var selectedTR = $('#itemDisplayTB tr.selectedTR');


        if (selectedTR.hasClass('header_tr') == false) {

            selectedTR.each(function () {
                count++;
            });

            if (count == 1) {
                selectedTR.remove();
                trRemove();
            } else {
                myAlert('e', 'Please select item to remove.');
            }
        }
    }

    function open_tenderModal() {
        //var tot = getTot();
        var tot = $('#netTotSpan').text();
        if (parseInt(tot) > 0) {
            itemAdd_sub_function();
            tenderPay.keyup();
            tender_modal.modal({backdrop: 'static'});

            setTimeout(function () {
                $('#cashAmount').focus();
            }, 500);
        } else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    /* adjust qty new function open numpad  */
    function adjust_qty_new() {
        $(".selectedTR").find("#_showqtyeditfield").click();
    }

    function adjust_qty() {
        $("#temp_number").html('');
        var count = 0;
        var selectedTR = $('#itemDisplayTB tr.selectedTR');

        selectedTR.each(function () {
            count++;
        });
    

        if (count == 1) {
            var qtyTD = selectedTR.find('td:eq(4)');
            var thisCurrentStk = selectedTR.find('td:eq(10) .thisCurrentStk').val();
            var qty = $.trim(qtyTD.text());
            qtyTD.html('');
            var thisWidth = '80'; //qtyTD.width();

            qtyTD.append('<input type="text" class="qtyAdjTxt number" id="qtyAdj" value="' + qty + '" data-value="' + qty + '" data-stock="' + thisCurrentStk + '" style="width: ' + thisWidth + 'px" autocomplete="off"/>');

            qtyTD.css({
                'width': thisWidth + 'px',
                'padding': '0px',
                'vertical-align': 'middle'
            });
            $('#qtyAdj').focus();
            $('#qtyAdj').select();
            focusOnLastCharacter('qtyAdj');
        } else {
            myAlert('e', 'Please select item to proceed.');
        }
    }

    function open_recallHold_modal() {
        $('#recall_search').val('');
        var recall_search = $('#recall_search').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value, 'recall_search': recall_search},
            url: "<?php echo site_url('Pos/recall_hold_invoice'); ?>",
            success: function (data) {
                $("#invoiceSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {

                        appData += '<tr class="validTR" data-id="' + val['invoiceID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['documentSystemCode'] + '</td><td>' + val['customerCode'] + '</td><td>' + val['cusName'] + '</td>';
                        appData += '<td align="center">' + val['createdDateTime'] + '</td>';
                        appData += '<td><a onclick="delete_gpos_hold_bills(' + val['invoiceID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                    });
                    invoiceSearchTB.append(appData);
                } else {
                    invoiceSearchTB.append('<tr><td colspan="5">No data</td></tr>');
                }
                calculateDiscount_byPercentage();

            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error in hold invoice loading.')
                }

            }
        });
        recall_modal.modal({backdrop: 'static'});
    }

    function recall_invoice() {
        $('#return_modal').modal({backdrop: 'static'});
        //$('#credit-to-customer-btn').hide();
        setTimeout(function () {
            $('#invoiceCode').focus();
            focusOnLastCharacter('invoiceCode');
            $('.returnTxt').val('');
            $('#invoiceCode').val('');
            $('#returnInvoiceTB tbody>tr').remove();
            $('#return-item-image').attr("src", "<?php echo base_url('images/item/no-image.png');?>");
            $('#return-date').datepicker('update', '<?php echo date('Y-m-d'); ?>');
        }, 500);
    }


    function invoice_search() {
        $('#returnInvoiceTB tbody>tr').remove();
        var invoiceCode = $('#invoiceCode').val();
        var crToCustomer_Btn = $('#credit-to-customer-btn');

        crToCustomer_Btn.hide();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceCode': invoiceCode},
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_search'); ?>",
            success: function (data, status, request) {
                stopLoad();
                if (data[0] != 's') {
                    myAlert(data[0], data[1]);
                } else {
                    var invData = data[1];
                    var invDet = data[2];
                    if (invData['customerID'] != 0) {
                        //crToCustomer_Btn.show();
                    }

                    if (invData['isGroupBasedTax'] == 1) {
                        $('.tax_columns_return').show();//displaying tax columns.
                    }else{
                        $('.tax_columns_return').hide();//hiding tax columns.
                    }

                    $('#returnCreditNo').val(data[3]);
                    $('#return-invoiceID').val(invData['invoiceID']);
                    $('#return-customerID').val(invData['customerID']);
                    $('#return-cusCode').val(invData['customerCode']);
                    $('#return-cusName').val(invData['cusName']);
                    $('#return-cusBalance').val(commaSeparateNumber(invData['cusBalance'], dPlaces));
                    $('#return-inv-date').val(invData['invoiceDate']);
                    //$('#return-date').datepicker('update', invData['invoiceDate']);
                    var invctt = parseFloat(invData['netTotal']) + parseFloat(invData['generalDiscountAmount']) + parseFloat(invData['promotiondiscountAmount']);
                  
                    $('#return-calculate-invDiscperc_hn').val(invData['generalDiscountPercentage']);
                    $('#gendiscref').html("(" + invData['generalDiscountPercentage'] + "%)");
                    localStorage.setItem('promotionDiscountAmount', invData['promotiondiscountAmount']);
                    $('#promodiscref').html("(" + invData['promotiondiscount'] + "%)");
                    $('#promotionDiscountID').val(invData['promotionID']);
                    $('#promo-discount').val(invData['promotiondiscount']);
                    $('#return-calculate-invBalance').val(commaSeparateNumber(invData['balanceAmount'], dPlaces));
                 
                    
                    //var refundAmount = (getNumberAndValidate(invData['netTotal']) - (invData['balanceAmount']));
                    var refundAmount = (parseFloat(invData['netTotal']) - parseFloat(invData['balanceAmount']));
                    refundAmount = (refundAmount > 0) ? refundAmount : 0;
                    
                    $('#return-refundable-hidden').val(refundAmount);
                   
                    var appData = '';
                    var item_available_to_exchange = 0;
                    
                    $.each(invDet, function (i, itmData) {
                        var returnPrice = itmData['price'];
                        var returnAmount = returnPrice * itmData['balanceQty'];
                        var returnDicPer = itmData['discountPer'];
                        var discAmount = (returnDicPer > 0) ? (returnAmount * returnDicPer * 0.01) : 0;
                        var lineNetTot = returnAmount - discAmount + parseFloat(itmData['taxAmount']);
                        var returnQTY = '';
                        var otherData = '';
                        if (itmData['balanceQty'] > 0) {
                            item_available_to_exchange = 1;
                            var taxPerUnit = parseFloat(itmData['taxAmount'])/parseFloat(itmData['balanceQty']);
                            returnQTY = '<input type="text" name="return_QTY[]" id="returnQTY' + i + '" class="returnQTY number" ';
                            returnQTY += 'data-maxqty="' + itmData['balanceQty'] + '" data-uom="' + itmData['unitOfMeasure'] + '" ';
                            returnQTY += 'value="' + itmData['balanceQty'] + '" data-tax_per_unit="'+taxPerUnit+'" data-discount_per_unit="'+ discAmount +'" data-unit_price="'+returnPrice+'" style="width: 65px; color:#000; padding:0px 5px; height: 16px">';

                            otherData += '<input type="hidden" name="invoiceDetailsID[]" value="' + itmData['invoiceDetailsID'] + '" >';
                            otherData += '<input type="hidden" name="itemID[]" value="' + itmData['itemAutoID'] + '" >';
                            otherData += '<input type="hidden" name="itemName[]" value="' + itmData['itemDescription'] + '" >';
                            otherData += '<input type="hidden" name="itemUOM[]" value="' + itmData['defaultUOM'] + '" >';
                            otherData += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itmData['qty'] + '" >';
                            otherData += '<input type="hidden" name="itemMaxQty[]" class="itemMaxQty" value="' + itmData['balanceQty'] + '" >';
                            otherData += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + returnPrice + '" >';
                            otherData += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + returnDicPer + '" >';
                            otherData += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + discAmount + '" >';
                            otherData += '<input type="hidden" class="return-discountAmount" value="' + discAmount + '" >';
                            otherData += '<input type="hidden" class="return-totalAmount" value="' + returnAmount + '" >';
                            otherData += '<input type="hidden" class="return-netAmount" value="' + lineNetTot + '" >';
                            otherData += '<input type="hidden" name="creditNoteID" value="' + invData['creditNoteID'] + '" >';
                            var taxPerUnit = parseFloat(itmData['taxAmount'])/parseFloat(itmData['balanceQty']);
                            otherData += '<input type="hidden" class="taxPerUnit" name="taxPerUnit[]" value="' + taxPerUnit + '" >';

                            appData = '<tr>';
                            appData += '<td align="right"></td>';
                            appData += '<td>' + itmData['seconeryItemCode'] + '</td>';
                            appData += '<td>' + itmData['itemDescription'] + '</td>';
                            appData += '<td>' + itmData['defaultUOM'] + '</td>';
                            appData += '<td align="right">' + itmData['balanceQty'] + '</td>';
                            appData += '<td align="right" style="width: 70px">' + returnQTY + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnPrice, dPlaces) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnAmount, dPlaces) + '</td>';
                            appData += '<td align="right">' + getNumberAndValidate(returnDicPer) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';


                            if(isGroupBasedTaxPolicy==1){

                                appData += '<td align="right">'+itmData['Description']+'</td>';
                                appData += '<td align="right">'+commaSeparateNumber(itmData['taxAmount'], dPlaces)+'</td>';
                            }

                            appData += '<td align="right">' + commaSeparateNumber(lineNetTot, dPlaces) + '</td>';
                            appData += '<td align="right">';
                            appData += '<span class="glyphicon glyphicon-trash deleteRow-return" style="color:rgb(209, 91, 71); position: static"></span>';
                            appData += otherData;
                            appData += '<input type="hidden" class="return-item-image-hidden" value="<?php echo base_url('images/item/');?>/' + itmData['itemImage'] + '" >';
                            appData += '</td>';
                            appData += '</tr>';
                            returnInvoiceTB.append(appData);
                        }


                    });

                    if(item_available_to_exchange == 1){

                        $('#return-calculate-invTot').val(commaSeparateNumber(invctt, dPlaces));
                        $('#return-calculate-invDisc').val(commaSeparateNumber(invData['generalDiscountAmount'], dPlaces));
                        $('#promoDiscountAmount').val(invData['promotiondiscountAmount']);
                        $('#return-credit-total').val(commaSeparateNumber(invData['netTotal'], dPlaces));
                        $('#return-refund').val(commaSeparateNumber(refundAmount, dPlaces));

                        getReturnSubTotal();
                        getReturnNetTotal();
                        getReturnDiscTotal();

                    }else{
                        myAlert('e', 'No items available to exchange or refund.');
                    }
                  
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Error in invoice calling loading.')
            }
        });
    }

    $(document).on('click', '#returnInvoiceTB tr', function () {
        if ($(this).hasClass('header_tr') == false) {
            $('#returnInvoiceTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');
            var r_image = $.trim($(this).find('td:eq('+localStorage.getItem('dataStoredTD')+') .return-item-image-hidden').val());
            $('#return-item-image').attr("src", r_image);
        }
    });

    $(document).on('keyup', '.returnQTY', function () {
        var maxQty = parseFloat($(this).attr('data-maxqty'));
        var qty = getNumberAndValidate($(this).val());

        if (qty > maxQty) {
            var umo = $.trim($(this).attr('data-uom'));
            $(this).val('');
            qty = 0;
            myAlert('w', 'Quantity cannot be exceed than the balance quantity<br>[ ' + maxQty + ' ' + umo + ' ]');
        }

        var itemPrice = getNumberAndValidate($(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemPrice').val());
        var returnDisPer = getNumberAndValidate($(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .itemDis').val());
        var taxPerUnit = getNumberAndValidate($(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .taxPerUnit').val());
        var returnDisAmount = 0;
        var thisSubTot = itemPrice * qty;
        var thisNetTot = thisSubTot;
        var thisTaxTotal = taxPerUnit * qty;

        if (returnDisPer > 0) {
            returnDisAmount = (thisSubTot * returnDisPer * 0.01);
            thisNetTot = thisSubTot - returnDisAmount;
        }

        $(this).closest('tr').find('td:eq(7)').text(commaSeparateNumber(thisSubTot, dPlaces));
        $(this).closest('tr').find('td:eq(9)').text(commaSeparateNumber(returnDisAmount, dPlaces));
        if(localStorage.getItem('dataStoredTD')=='13'){//group based tax enabled.
            $(this).closest('tr').find('td:eq(11)').text(commaSeparateNumber(thisTaxTotal, dPlaces));
        }
        $(this).closest('tr').find('td:eq(12)').text(commaSeparateNumber(thisNetTot, dPlaces));

        $(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .return-discountAmount').val(returnDisAmount);
        $(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .return-totalAmount').val(thisSubTot);
        $(this).closest('tr').find('td:eq('+localStorage.getItem('dataStoredTD')+') .return-netAmount').val(thisNetTot);

        getReturnSubTotal();
        getReturnNetTotal();
        getReturnDiscTotal();
    });

    function getReturnNetTotal() {
        var returnNetTot = 0;
        $('.returnQTY').each(function () {
            //var thisVal = parseFloat($(this).val());//getNumberAndValidate($(this).val());
            var retQty = parseFloat($(this).val());
            var itemPrice = parseFloat($(this).data('unit_price'));
            var taxPerUnit= parseFloat($(this).data('tax_per_unit'));
            var discPerUnit= parseFloat($(this).data('discount_per_unit'));

            var taxForReturnedUnits = retQty*taxPerUnit;
            var lineReturnTotal = ((retQty*itemPrice)+taxForReturnedUnits) - discPerUnit;
            returnNetTot += lineReturnTotal;
        });
        var return_invBalance = 0; //SME-2604 - getNumberAndValidate($('#return-calculate-invBalance').val());
        var refundAmount = returnNetTot - return_invBalance;
        refundAmount = (refundAmount > 0) ? refundAmount : 0;
        var disc = (refundAmount * parseFloat($('#return-calculate-invDiscperc_hn').val())) / 100;
        //var promoDiscount = parseFloat(localStorage.getItem('promotionDiscountAmount'));
        // var refamnt = refundAmount - disc - promoDiscount;
        // var crdamnt = returnNetTot - disc - promoDiscount;
        var netBeforePromoDiscount = refundAmount - disc;
        var promDiscount = (netBeforePromoDiscount * parseFloat($('#promo-discount').val())) / 100;
        var refamnt = refundAmount - (disc + promDiscount);
        var crdamnt = returnNetTot - (disc + promDiscount);

        $('#promoDiscountAmount').val(commaSeparateNumber(promDiscount, dPlaces));
        $('#return-credit-total').val(commaSeparateNumber(crdamnt, dPlaces));
        $('#return-calculate-invDisc').val(disc);

        $('#return-refund').val(commaSeparateNumber(refamnt, dPlaces));
        $('#return-refundable-hidden').val(getNumberAndValidate(refamnt, dPlaces));

    }

    function getReturnSubTotal() {
        var returnSubTot = 0;
        $('.return-netAmount').each(function () {
            var thisVal = getNumberAndValidate($(this).val());
            returnSubTot += thisVal;
        });

        $('#return-subTotalAmount').val(commaSeparateNumber(returnSubTot, dPlaces));
        return returnSubTot;
    }

    function getReturnDiscTotal() {
        var returnDiscTot = 0;
        $('.return-discountAmount').each(function () {
            var thisVal = getNumberAndValidate($(this).val());
            returnDiscTot += thisVal;
        });
        $('#return-discTotal').val(commaSeparateNumber(returnDiscTot, dPlaces));
        return returnDiscTot;
    }

    $(document).on('click', '.deleteRow-return', function () {
        var parentTr = $(this).closest('tr');
        parentTr.remove();

        getReturnSubTotal();
        getReturnNetTotal();
        getReturnDiscTotal();

        setTimeout(function () {
            $('#return-item-image').attr("src", "<?php echo base_url('images/item/no-image.png');?>");
        }, 100);


    });

    $(document).on('keyup', '#return-refund', function () {
        var refundAmount = getNumberAndValidate($(this).val());
        var applicable_refundAmount = getNumberAndValidate($('#return-refundable-hidden').val());

        if (applicable_refundAmount < refundAmount) {
            $(this).val('');
            myAlert('w', 'Maximum refund amount is ' + commaSeparateNumber(applicable_refundAmount, dPlaces));
        }

    });

    function focusOnLastCharacter(id) {
        var inputField = document.getElementById(id);
        if (inputField != null && inputField.value.length != 0) {
            if (inputField.createTextRange) {
                var FieldRange = inputField.createTextRange();
                FieldRange.moveStart('character', inputField.value.length);
                FieldRange.collapse();
                FieldRange.select();
            } else if (inputField.selectionStart || inputField.selectionStart == '0') {
                var elemLen = inputField.value.length;
                inputField.selectionStart = elemLen;
                inputField.selectionEnd = elemLen;
                inputField.focus();
                inputField.select();
            }
        } else {
            inputField.focus();
            inputField.select();
        }
    }


    //return
    function exchangeAddInvoice(){

        var postData = $('#return_form').serializeArray();

        swal({
            title: "Are you sure ?",
            text: "Add this Exchange to the Current invoice? This action cannot be recalled!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            showCloseButton: true,
            cancelButtonText: "No"
        },
        function (inputValue) {

            if (inputValue===false) {
                itemReturn('exchange');
            } else {
                 $.ajax({
                //async: true,
                type: 'post',
                html: 'json',
                url: "<?php echo site_url('Pos/get_exchange_selected_items'); ?>",
                data:postData,
                success: function (data) {
                    var data = JSON.parse(data);
                    $.each(data, function (i, datum) {
                        setValues_masterForm_exchange(datum);
                    });
                    itemReturn('exchange','toInvoice');

                    myAlert('s', 'Successfully added the exchange.');
                
                }, error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
                });
            }
           

            // return false;
          }
        );

        //if not confirmed to add to the invoice
        // itemReturn('exchange');
    
       
    }

    function itemReturn(returnMode,called=null) {

        if(returnMode == 'Refund'){
            var refund_amount = parseFloat($('#return-refundable-hidden').val()).toFixed(dPlaces);
            $('#RefundGrosstotal').text(refund_amount);
            $('#RefundGrossTotal_in').val(refund_amount);
            $('#RefundNetTotalAmount_in').val(refund_amount);
            $('#RefundNetTotalAmount').text(refund_amount);


            $('#pos_return_payments_modal').toggle();
            return false;
        }else{
            itemReturnFn(returnMode,2,called);
            return false;
        }

    }

    function itemReturnFn(returnMode,paymentType=1,calledFrom=null){
        // 1- Cash payment
        var postData = $('#return_form').serializeArray();
        postData.push({'name': 'returnMode', 'value': returnMode});
        postData.push({'name': 'paymentType', 'value': paymentType});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_return'); ?>",
            success: function (data, status, request) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {

                    $('#return_modal').modal('hide');

                    if(calledFrom == 'toInvoice'){
                        $('#btn_print_template').attr("onclick","");
                        $('#creditNote-invID').val(data[2]);
                    }else{
                        $('#btn_print_template').attr("onclick","newInvoice(1)");
                    }

                    exchangePrint(data[2], data[3],returnMode);
                    $('#isInvoiced').val('');

                    if (returnMode == 'exchange') {

                    } else {
                        //close modals
                        $('#pos_return_payments_modal').toggle();

                    }

                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error in invoice return.')
                }
            }
        });
    }

    function close_payment_refund(){
        $('#pos_return_payments_modal').toggle();
    }

    function exchangePrint(returnID, returnCode, returnMode) {
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            url: "<?php echo site_url('Pos/return_print'); ?>/" + returnID + "/" + returnCode,
            data:{returnMode:returnMode},
            success: function (data) {
                $('#print_template').modal({backdrop: 'static'});
                $('#print_content').html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function clearData() {
        $('.formInput').val('');
        $('.item-search-container input').each(function () {
            $(this).val('');
        });
        $('#item-image-hidden').val();
        $('#is-edit').val('');
    }

</script>
<script type="text/javascript">
    var tDay = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var tMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    function getClock() {
        var date = new Date();
        var nHour = date.getHours(), nMin = date.getMinutes(), nSec = date.getSeconds(), ap;

        if (nHour == 0) {
            ap = " AM";
            nHour = 12;
        } else if (nHour < 12) {
            ap = " AM";
        } else if (nHour == 12) {
            ap = "PM";
        } else if (nHour > 12) {
            ap = "PM";
            nHour -= 12;
        }

        if (nMin <= 9) {
            nMin = "0" + nMin;
        }
        if (nSec <= 9) {
            nSec = "0" + nSec;
        }

        $('#timeBox').text(nHour + " : " + nMin + " : " + nSec + " " + ap);

    }

    function getDate() {
        var toDay = new Date();
        var day = toDay.getDay(), cMonth = toDay.getMonth(), nDate = toDay.getDate(), nYear = toDay.getYear();
        if (nYear < 1000) {
            nYear += 1900;
        }
        //$('#dateBox').html(tDay[day] + " &nbsp;" + nDate + " &nbsp;" + tMonth[cMonth] + " &nbsp;" + nYear);
        $('#dateBox').html(nDate + " &nbsp;" + tMonth[cMonth] + " &nbsp;" + nYear);
    }

    window.onload = function () {
        getClock();
        getDate();
        setInterval(getClock, 1000);
        setupPromotionData();
    };

    function setupPromotionData() {
        var freeIssueItems = window.localStorage.getItem('freeIssueItems');
        freeIssueData = JSON.parse(freeIssueItems);
    }


    /** Created  */
    function clearForNewInvoice() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/loadNewInvoiceNo'); ?>",

            beforeSend: function () {
                $("#tbody_itemList").html('<tr><th colspan="11" style="text-align:center;"><i class="fa fa-refresh fa-spin"></i> Loading </th></tr>');
                $("#doSysCode_refNo").html('<i class="fa fa-refresh fa-spin"></i>');
                $('.tenderTBTxt').val('');
            },
            success: function (data) {
                $("#tbody_itemList").html('');
                $("#doSysCode_refNo").html(data['refCode']);
                invoice_no = data['refCode'];
                defaultCustomer();
                reset_generalDiscount();
            },
            error: function (xhr) {
                $("#tbody_itemList").html('');
                $("#doSysCode_refNo").html('');
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function validateCreditSales() {
        var creditSalesAmount = $('#creditSalesAmount').val();
        if (creditSalesAmount > 0) {
            $('#cashAmount').attr('disabled', true);
            $('#chequeAmount').attr('disabled', true);
            $('#cardAmount').attr('disabled', true);
            $('#giftCard').attr('disabled', true);
            $('#creditNote').attr('disabled', true);

            $('#cashAmount').val(0);
            $('#chequeAmount').val(0);
            $('#cardAmount').val(0);
            $('#giftCard').val(0);
            $('#creditNote').val(0);
        } else {
            $('#cashAmount').attr('disabled', false);
            $('#chequeAmount').attr('disabled', false);
            $('#cardAmount').attr('disabled', false);
            $('#giftCard').attr('disabled', false);
            $('#creditNote').attr('disabled', false);
            $('#creditSalesAmount').val(0);
        }
    }

    function selectBatch(){

        var barcode = $(".selectedTR").find("td:eq(2)").text();
        var qty = $(".selectedTR").find("td:eq(5)").text();
        var type = 'change_batch';
        var selected_batch_list = [];

        $('input[name="selected_batch"]:checked').each(function() {
            selected_batch_list.push(this.value);
        });

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: { 'barcode':barcode,'qty':qty,'invoice_no':invoice_no,'batch_number':selected_batch_list,'type':type },
            url: "<?php echo site_url('Pos/item_reserved_qty'); ?>",
            beforeSend: function () {
                
            },
            success: function (data) {
               
                if(data && data.status == 's'){
                    myAlert('s', data.message);
                    return true;
                }else{
                    myAlert('w', 'Please try again');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Error while loading')
                }
                
            }
        });


    }

    function LoadBatchList(){
        //item batch
        var item_bar_code = $('#item_bar_code').val();

        if(item_bar_code == ''){
            item_bar_code = $(".selectedTR").find("td:eq(2)").text();
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': item_bar_code,'invoice_no': invoice_no},
            url: "<?php echo site_url('Pos/item_batch_search'); ?>",
            success: function (data) {
                $("#itemBatchSearch > tbody").empty();
                var appData = '';

                if (data.length > 0) {

                    $.each(data, function (i, val) {
                        
                        appData += '<tr class="validTR" ">';

                        if(val['qtr'] > 0 || val['selected'] == 1){ 
                            if(val['selected'] == 1){
                                appData += '<td></td><td>' + val['batchNumber'] +'</td><td>' + val['batchCode'] + '</td><td>' + val['defaultUnitOfMeasure'] + '</td>' +'<td>' + val['batchExpireDate'] + '</td><td>' + val['qtr'] + '</td><td><input type="text" class="form-control text-right" name="requested_qty[]" value="'+val['reserved_qty']+'" disabled/></td><td><input type="checkbox" checked name="selected_batch" value="'+val['batchNumber']+'"/></td>';
                            }else{
                                appData += '<td></td><td>' + val['batchNumber'] +'</td><td>' + val['batchCode'] + '</td><td>' + val['defaultUnitOfMeasure'] + '</td>' +'<td>' + val['batchExpireDate'] + '</td><td>' + val['qtr'] + '</td><td><input type="text" class="form-control text-right" name="requested_qty[]" value="'+val['reserved_qty']+'" disabled/></td><td><input type="checkbox" name="selected_batch" value="'+val['batchNumber']+'"/></td>';
                            }
                           
                        }

                        // appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        // appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td><td>' + display + '</td></tr>';
                    });
                    itemBatchSearch.append(appData);
                } else {
                    itemBatchSearch.append('<tr><td colspan="6">No data</td></tr>');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });

    }

    function LoadCustomers() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/customer_search'); ?>",
            success: function (data) {
                $("#customerSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {

                    $.each(data, function (i, val) {
                        var display = '';
                        if (val['iscardexist'] > 0 && val['iscardexist'] != 2) {
                            display = val['loyalitycardno'];
                        } else if (val['iscardexist'] != 2) {
                            display = '<a onclick="generate_loyality(' + val['customerAutoID'] + ',' + val['customerTelephone'] + ')">Generate</a>';
                        }
                        appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td><td>' + display + '</td></tr>';
                    });
                    customerSearchTB.append(appData);
                } else {
                    customerSearchTB.append('<tr><td colspan="3">No data</td></tr>');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function defaultCustomer() {
        $('#customerID').val(0);
        $('#customerCode').val('CASH');
        $('.customerSpan').text('Cash');
        $('#creditsalesfield').addClass('hidden');
        $('#cashAmount').attr('disabled', false);
        $('#chequeAmount').attr('disabled', false);
        $('#cardAmount').attr('disabled', false);
        $('#giftCard').attr('disabled', false);
        $('#creditNote').attr('disabled', false);

        $("#rdmCustomerName").val('');
        $("#rdmcustomerID").val(0);
        $("#rdmcustomerTelephone").val('');
        $("#rdmbarcode").val('');
        $("#availablepoints").val(0);
        $("#redeempts").val('');
        $("#rdmCustomerName").prop('disabled', true);
        $('#customer_modal').modal('hide');
        y = 0;
    }

    function open_recallHold_modal_search() {
        var recall_search = $('#recall_search').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value, 'recall_search': recall_search},
            url: "<?php echo site_url('Pos/recall_hold_invoice'); ?>",
            success: function (data) {
                $("#invoiceSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {
                        appData += '<tr class="validTR" data-id="' + val['invoiceID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['documentSystemCode'] + '</td><td>' + val['customerCode'] + '</td><td>' + val['cusName'] + '</td>';
                        appData += '<td align="center">' + val['timestamp'] + '</td>';
                        appData += '<td><a onclick="delete_gpos_hold_bills(' + val['invoiceID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                    });
                    invoiceSearchTB.append(appData);
                } else {
                    invoiceSearchTB.append('<tr><td colspan="5">No data</td></tr>');
                }

                calculateDiscount_byPercentage();
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
        recall_modal.modal({backdrop: 'static'});
    }

    function checkifItemExsist() {
        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    open_recallHold_modal()
                }
            );
        } else {
            open_recallHold_modal()
        }
    }

    function checkifItemExsistReturn() {

        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    recall_invoice()
                }
            );
        } else {
            recall_invoice()
        }

    }


    function checkifItemExsistpower() {
        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    session_close()
                }
            );
        } else {
            session_close()
        }
    }

    function open_pos_payments_modal() {
        var customerID = selectedCusArray[0];
        if ($('#customerID').val() > 0) {
            customerloyalitydetails($('#customerID').val());
        } else {
            $('.loylitycusdetail').addClass('hide');
        }

        chkifinputisempty();
        clearform_pos_receipt();

        //var tot = $('#netTotSpan').text();
        /*var tot = parseFloat($('#netTot_after_g_disc').val());*/
        var netTotal_tmp = parseFloat($('#netTotVal').val());
        var netTotal = netTotal_tmp.toFixed(dPlaces);
        var netTotalAfterDiscount = parseFloat($('#netTot_after_g_disc').val());

        var discountTmp = $("#gen_disc_percentage").val();
        var discountPer = parseFloat(discountTmp);
        if ((discountPer > 0 && discountPer <= 100)) {
            var discountAmount = netTotal_tmp - netTotalAfterDiscount;
        } else {
            var discountAmount = 0;
        }
        discountAmount = discountAmount.toFixed(dPlaces);

        if (parseFloat(netTotal) > 0) {
            if ($('#customerID').val() > 0) {
                $('.creditSalesPayment').attr('readonly', false);
                $('#rcgcbtn').attr('disabled', false);
            } else {
                $('.creditSalesPayment').attr('readonly', true);
                $('#rcgcbtn').attr('disabled', true);
            }
            var gross = $('#totSpan').text();
            var disc = $('#discSpan').text();
            $('#Grosstotal').text((netTotal - discountAmount).toFixed(dPlaces));
            //$('#discounttxt').text(disc);
            $('#discounttxt').text(discountAmount);
            $("#pos_payments_modal").modal('show');

            //$("#paid_by").select2("val", "");
            setTimeout(function () {
                calculateReturn();
                //calculateDiscount_byPercentage()
                $("#paid").focus();
            }, 500);

        } else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    function calculateReturn() {
        var zero = 0;
        var sum = 0;
        $('.netAmount').each(function () {
            sum += parseFloat($(this).val());
        });
        var fi = sum.toFixed(dPlaces);
        var total = fi;
        total = parseFloat($("#netTot_after_g_disc").val());
        total = total.toFixed(dPlaces)
        $("#final_payableNet_amt").text(total);
        var paidAmount = parseFloat(0);
        var return_amount = 0
        calculateDelivery()

        if (total < paidAmount || true) {
            return_amount = paidAmount - total;
            if (return_amount < 0) {
                $("#return_change").text(zero.toFixed(dPlaces))
                $("#returned_change").val(zero.toFixed(dPlaces))
            } else {
                if (isNaN(return_amount)) {
                    var return_amount = 0;
                }
                $("#return_change").text(return_amount.toFixed(dPlaces))
                $("#returned_change").val(return_amount.toFixed(dPlaces))
            }

        } else {
            $("#return_change").text(zero.toFixed(dPlaces))
            $("#returned_change").val(zero.toFixed(dPlaces))
        }
        if (total <= paidAmount && total != 0) {
            document.getElementById("submit_btn").style.display = "block"
            $("#total_payable_amt").val(total)
        } else {
            $("#total_payable_amt").val(total)
            document.getElementById("submit_btn").style.display = "block" //none
        }
        var totalamnt = 0;
        $(".paymentInput").each(function (e) {
            var valueThis = $.trim($(this).val());
            totalamnt += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        $("#paid").val(totalamnt.toFixed(dPlaces));

        calculatePromo();
        $("#final_payableNet_amt").text((parseFloat($("#total_payable_amt").val()) - parseFloat($("#promotional_discount").val())).toFixed(dPlaces));
        $("#total_payable_amt").val((parseFloat($("#total_payable_amt").val()) - parseFloat($("#promotional_discount").val())).toFixed(dPlaces));
    }

    function calculatePromo() {
        var payableAmount = $("#total_payable_amt").val();
        var promotion = $("#promotionID option:selected").data('cp');
        if (!isNaN(promotion) && promotion > 0) {
            var promotionAmount = (promotion / 100) * payableAmount;
            $("#promotional_discount").val((promotionAmount).toFixed(2))//this should set to company decimal value.
        } else {
            $("#promotional_discount").val(0)

        }
    }

    function calculateDelivery() {
        var zero = 0;
        var total = parseFloat($("#final_payableNet_amt").text());
        var paidAmount = parseFloat($("#paid").val());
        var return_amount = 0;
        // var elementid = $(element).attr('id')
        var delivery = $("#deliveryPersonID option:selected").data('cp');
        if (typeof (delivery) != "undefined") {

            var commission = total * (delivery / 100);
            /**/
            var deliveryPayable = total - commission;
            /**/

            $("#totalPayableAmountDelivery_id").val(deliveryPayable.toFixed(dPlaces));
            if (total < paidAmount || true) {
                return_amount = paidAmount - deliveryPayable;
                if (return_amount < 0) {
                    //return_amount = 0;
                }
                if (!isNaN(return_amount)) {
                    $("#returned_change_toDelivery").val(return_amount.toFixed(dPlaces));
                } else {
                    $("#returned_change_toDelivery").val(zero);
                }
            } else {
                $("#returned_change_toDelivery").val(zero.toFixed(dPlaces));
            }

        } else {
            $("#totalPayableAmountDelivery_id").val(zero);
            $("#returned_change_toDelivery").val(zero);
        }
    }

    function updateNoteValue(tmpValue) {
        var totalAmount = $("#final_payableNet_amt").text();
        var loyality = $('#loyaltyPoints').val();
        var customerAutoID = $('#customerID').val();
        /////////
        if (customerAutoID > 0) {
            var poinforPuchaseAmount = $("#poinforPuchaseAmount").val();
            var purchaseRewardPoint = $("#purchaseRewardPoint").val();
            $('#earnedpoints').html((parseFloat(totalAmount) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
            earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
            $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        }
        $(".paymentInput").val('');
        var id = '<?php echo get_pos_paymentConfigID_cash() ?>';
        var noteValue = $(tmpValue).text();
        $("#paid_temp").html(noteValue);
        $("#paid").val(parseFloat(noteValue));
        $("#paymentType_" + id).val(parseFloat(noteValue));
        /*$("#paid").focus();*/
        calculateReturn();
        calculatePaidAmount();

    }

    function calculatePaidAmount(tmpThis) {
        var total = 0;
        $(".paymentInput").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        if ($('#paymentType_7').val() > 0) {
            var paymentType = $('#paymentType_7').val()
            $('.paymentInput').val(0)
            $('#paymentType_7').val(paymentType)
        }
        $("#paid").val(total.toFixed(d));
        var payable = $("#total_payable_amt").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_change").val(returnAmount);
            $("#return_change").html(returnAmount.toFixed(dPlaces))
        }

        setTimeout(function () {
            var discount = parseFloat(0);
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paid").val());

            var advancePaymets = $("#delivery_advancePaymentAmount").val();
            netTotal = netTotal - advancePaymets;
            var returnChange = paidAmountTmp - netTotal;
            $("#final_payableNet_amt").html(netTotal.toFixed(dPlaces));
            if (returnChange > 0 || true) {
                $("#return_change").html(returnChange.toFixed(dPlaces))
            }


            /** Total card amount should not be more than the NET Total */
            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOther").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });

                netTotal = netTotal.toFixed(dPlaces);
                if (cardTotal > netTotal) {
                    $(".paymentOther ").val(0);
                    calculateReturn();
                    $("#cardTotalAmount").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');
                } else {
                    $("#cardTotalAmount").val(cardTotal);
                }
                $("#netTotalAmount").val(netTotal);
            }
        }, 50);

    }

    function updateExactCash() {
        var loyality = $('#loyaltyPoints').val();
        var customerAutoID = $('#customerID').val();
        $(".paymentInput").val('');
        var id = '<?php echo get_pos_paymentConfigID_cash() ?>';
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paid").val(parseFloat(totalAmount));
        if (customerAutoID > 0) {
            var poinforPuchaseAmount = $("#poinforPuchaseAmount").val();
            var purchaseRewardPoint = $("#purchaseRewardPoint").val();
            $('#earnedpoints').html((parseFloat(totalAmount) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
            earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
            $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        }

        $("#paymentType_" + id).val(parseFloat(totalAmount));
        calculateReturn();
    }

    function updatePaidAmount(tmpValue) {
        var cPaidAmount = $("#paid").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_temp").text();
        var loyality = $('#loyaltyPoints').val();
        var totalAmount = $("#final_payableNet_amt").text();
        var customerAutoID = $('#customerID').val();
        if (customerAutoID > 0) {
            var poinforPuchaseAmount = $("#poinforPuchaseAmount").val();
            var purchaseRewardPoint = $("#purchaseRewardPoint").val();
            $('#earnedpoints').html((parseFloat(totalAmount) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
            earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
            $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        }
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
            }
            $("#paid_temp").html(tmpAmount_output);
            $("#paid").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            clearPayments();
        }
        calculateReturn();
    }

    function updateExactCard(paymentTypeID) {
  
        $("#paid_temp").html(0);
        $(".paymentInput").val('');
        $('.cardRef').val();
        var totalAmount = $("#final_payableNet_amt").text();
        var poinforPuchaseAmount = $("#poinforPuchaseAmount").val();
        var purchaseRewardPoint = $("#purchaseRewardPoint").val();
        $('#earnedpoints').html((parseFloat(totalAmount) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
        earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
        $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount));
        calculateReturn();
    }

    function updateRefundExactCard(paymentTypeID) {
        
        $("#refund_paid_temp").html(0);
        $(".paymentInput").val('');
        $('.cardRef').val();

        var totalAmount = parseFloat($("#RefundNetTotalAmount_in").val()).toFixed(d);
        $("#refund_paymentType_" + paymentTypeID).val(totalAmount);
        $("#refund_paymentType_" + paymentTypeID).trigger("change");
        $('#RefundPaymentType').val(paymentTypeID);
       
        // $('#earnedpoints').html((parseFloat(totalAmount) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
        // earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
        // $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        // $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount));
        // calculateReturn();
    }

    function calculateRefundPaidAmount(payment){
        var payment_v = parseFloat($(payment).val()).toFixed(d);
        $('#refund_paid').val(payment_v);

    }

    function submit_pos_refund_payments(){
        var refund_payment_type =  $('#RefundPaymentType').val();
        var refund_amount = $('#RefundNetTotalAmount_in').val();
        var returnMode = 'Refund';

        itemReturnFn(returnMode,refund_payment_type);
        
    }

    function updateCreditSale(paymentTypeID) {
        if ($("#customerID").val() > 0) {
            $("#paid_temp").html(0);
            $(".paymentInput").val('');
            $('.cardRef').val('');
            var totalAmount = $("#final_payableNet_amt").text();
            $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount));
            calculateReturn();
        } else {
            myAlert('i', '<strong>Customer not Selected.</strong> <br/>Credit Sales required customer')
            return false;
        }

    }

    $('#modal_search_item').on('hidden.bs.modal', function () {
        $("#pos-add-btn").attr('disabled','disabled');
    });

    function submit_pos_payments() {

        if ($('.creditSalesPayment').val() > 0) {
            $('#CreditSalesAmnt').val($('.creditSalesPayment').val());
            $('#isCreditSale ').val(1);
        } else {
            $('#CreditSalesAmnt').val(0)
        }
        var postData = $('.form_pos_receipt').serializeArray();
            $(".itemID").each(function (e) {
        });

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/submit_pos_payments'); ?>",
            data: postData,
            cache: false,
            beforeSend: function () {
                $("#submit_btn").prop('disabled', true);

            },
            success: function (data) {
                $('.cardRef').val('');
                $("#submit_btn").prop('disabled', false);
                if (data[0] != 'd'){
                    myAlert(data[0], data[1]);
                }
                if (data[0] == 's') {
                    var zero = 0;
                    $('#isInvoiced').val('');
                    $('#customerTelephoneTmp').val('');
                    $('#customerNameTmp').val('');
                    $('#customerAddressTmp').val('');
                    $('#customerEmailTmp').val('');
                    $('#loyalitycardno_gpos').val('');
                    $('#totSpan').html(zero.toFixed(dPlaces));
                    $('#netTotSpan').html(zero.toFixed(dPlaces));
                    $('#customerID').val(0);
                    $("#netTotVal").val(zero.toFixed(dPlaces));

                    $("#pos_payments_modal").modal('hide');
                    newInvoice(1);
                    clearform_pos_receipt();
                    clearCustomerVal_gpos();
                    searchByKeyword();
                    var doSysCode_refNo = $('#doSysCode_refNo').text();
                    invoicePrint(data[2], data[3], data[4]);
                    searchByKeyword(1);
                    reset_generalDiscount();
                    clearPromotionDiscount();

                    //Clear tax applicable array
                    tax_item_arr = [];

                }
                if (data[0] == 'w') {
                    $('#errormsgInsuf').empty();
                    $.each(data[2], function (key, value) {
                        $('#errormsgInsuf').append('<tr><td>' + value['itemCode'] + '</td><td>' + value['itemDesc'] + '</td><td>' + value['cruuentStock'] + '</td></tr>');
                    });
                    $('#insufficentmodel').modal('show');
                    $("#pos_payments_modal").modal('hide');
                }

                if (data[0] == 'd') {
                    //submit_pos_payments();
                    myAlert('e', 'Request cancelled due to server load. Please resubmit.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false);
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function clearPromotionDiscount() {
        $("#total_payable_amt").val('');
        $("#promotionID").val('');
        $("#gross_total_input").val('');
        $("#tmp_promotion").val('');
        $("#promotional_discount").val('');
        $("#final_payableNet_amt").text('');
    }

    function clearform_pos_receipt() {
        var zero = 0;
        $('#final_payableNet_amt').html(zero.toFixed(dPlaces));
        $('#return_change').html(zero.toFixed(dPlaces));
        $('.paymentInput').val(zero.toFixed(dPlaces));
        /*$('.ar ').val('');*/
        $('#paid').val(zero.toFixed(dPlaces));
        $('#paymentType_1').val(zero);
        $('#paymentType_26').val(zero);
        $('#isCreditSale ').val(zero);
        $('#CreditSalesAmnt ').val('');
        $('#customerTelephone ').val('');
        $('#cardTotalAmount ').val('');
        $('#netTotalAmount ').val('');
        $('#isDelivery ').val('');
        $('#frm_isOnTimePayment ').val('');
        $('#total_payable_amt ').val('');
        $('#delivery_advancePaymentAmount ').val('');
        $('#Grosstotal').text(zero.toFixed(dPlaces));
        $('#discounttxt').text(zero.toFixed(dPlaces));
        $('#memberidhn').val('');
        $('#membernamehn').val('');
        $('#contactnumberhn').val('');
        $('#mailaddresshn').val('');
    }


    function openCreditSalesModal() {
        $('#creditNote_modal').modal('show');
        $("#creditNoteTB > tbody").empty();
        creditNoteTB.append('<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>');
        loadCreditnotes();
    }

    function loadCreditnotes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/creditNote_load'); ?>",
            success: function (data) {
                $("#creditNoteTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {
                        appData += '<tr class="validTR" data-id="' + val['salesReturnID'] + '"  data-amount="' + val['netTotal'] + '">';
                        appData += '<td>' + (i + 1) + '</td><td>' + val['documentSystemCode'] + '</td><td>' + val['salesReturnDate'] + '</td>';
                        appData += '<td align="right">' + commaSeparateNumber(val['netTotal'], dPlaces) + '</td></tr>';
                    });
                    creditNoteTB.append(appData);
                } else {
                    creditNoteTB.append('<tr><td colspan="4">No data</td></tr>');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }


    function fetch_related_uom_posgen(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                formInput.prop('readonly', false);
                itemUOM.prop('readonly', false);
                itemUOM.css('background', '#fff');
                itemUOM.empty();
                itemUOM.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        itemUOM.append('<option value="' + text['UnitShortCode'] + '" data-value="' + text['conversion'] + '">' + text['UnitShortCode'] + ' | ' + text['UnitDes'] + '</option>');
                    });
                    if (select_value) {
                        itemUOM.val(select_value);
                    }
                }
                re_validate();
                if (enable_BC.prop('checked') == true) {
                    $('#pos_form').bootstrapValidator();
                } else {
                    itemUOM.focus();
                }
                $("#pos-add-btn").prop('disabled', false);
                $("#pos-add-btn").click();
                pos_form.bootstrapValidator('resetForm', true);
                calculateDiscount_byPercentage();
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });

        /* scroll down after item add */
        var fixHeader_Div_objDiv = $(".fixHeader_Div");
        var __h = fixHeader_Div_objDiv.get(0).scrollHeight;
        fixHeader_Div_objDiv.animate({scrollTop: __h});
    }

    function fetch_related_uom_posgen_qty(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                formInput.prop('readonly', false);
                itemUOM.prop('readonly', false);
                itemUOM.css('background', '#fff');
                itemUOM.empty();
                itemUOM.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        itemUOM.append('<option value="' + text['UnitShortCode'] + '" data-value="' + text['conversion'] + '">' + text['UnitShortCode'] + ' | ' + text['UnitDes'] + '</option>');
                    });

                    if (select_value) {
                        itemUOM.val(select_value);
                    }
                }
                re_validate();
                if (enable_BC.prop('checked') == true) {
                    $('#pos_form').bootstrapValidator();
                } else {
                    itemUOM.focus();
                }
                itemQty.focus();
                $("#pos-add-btn").prop('disabled', false);
                //$("#pos-add-btn").click();
                /*pos_form.bootstrapValidator('resetForm', true);
                calculateDiscount_byPercentage();*/
            }, error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }


    function openRCGCmodel(paymentglautoid) {
        $("#rcgc_modal").modal('show');
    }

    function setrcgccustdetails() {
        var memberid = $('#memberid').val();
        var membername = $('#membername').val();
        if (memberid == '') {
            myAlert('w', 'Member ID id required')
        } else if (membername == '') {
            myAlert('w', ' Member Name id required')
        } else {
            $('#memberidhn').val($('#memberid').val());
            $('#membernamehn').val($('#membername').val());
            $('#contactnumberhn').val($('#contactnumber').val());
            $('#mailaddresshn').val($('#mailaddress').val());
            $('#paymentType_26').val($('#final_payableNet_amt').text());
            $('#memberid').val('');
            $('#membername').val('');
            $('#contactnumber').val('');
            $('#mailaddress').val('');
            $("#rcgc_modal").modal('hide');
            calculatePaidAmount($('#paymentType_26').val())
        }
    }

    function add_new_customer_modal() {
        $('.loyalitycardgen').addClass('hide');
        $('#customer_master_add').modal('show');
        $('#customer_master_form')[0].reset();
        $('#customer_master_form').bootstrapValidator('resetForm', true);
        $('#receivableAccount').val(accountsreceivable);
        $('#customerCurrency').val(currency);
        $('#customercountry').val(country);
    }

    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#customerCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID, 'CUS', '', 'CUS');
    }

    $('table.arrow-nav').keydown(function (e) {
        /*alert('hi');*/
        var $table = $(this);
        var $active = $('input:focus,select:focus', $table);
        var $next = null;
        var focusableQuery = 'input:visible,select:visible,textarea:visible';
        var position = parseInt($active.closest('td').index()) + 1;
        switch (e.keyCode) {
            case 37: // <Left>
                $next = $active.parent('td').prev().find(focusableQuery);
                break;
            case 38: // <Up>
                $next = $active
                    .closest('tr')
                    .prev()
                    .find('td:nth-child(' + position + ')')
                    .find(focusableQuery);
                break;
            case 39: // <Right>
                $next = $active.closest('td').next().find(focusableQuery);
                break;
            case 40: // <Down>
                $next = $active
                    .closest('tr')
                    .next()
                    .find('td:nth-child(' + position + ')')
                    .find(focusableQuery)
                ;
                break;
        }
        if ($next && $next.length) {
            $next.focus();
        }
    });

    function getRowIdx() {
        return $('#data-table').DataTable().cell({
            focused: true
        }).index().row;
    }

    function open_void_Modal_g_pos() {
        var id = 0;
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos/load_void_receipt'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                $("#gpos_open_void_receipt").modal('show');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#voidReceiptgpos").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function void_gpos() {
        var invoiceID = $("#voidhnid").val();

        swal({
                title: "Are you sure ?",
                text: "You want to Void this invoice!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos/void_gpos'); ?>",
                    data: {invoiceID: invoiceID},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#print_template').modal('hide');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        if (jqXHR.status == false) {
                            myAlert('w', 'No Internet, Please try again');
                        } else {
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    }
                });
            }
        );
    }

    var keyActivityLog = 0;

    function showqtyeditfield(dt) {

        $('.itemQty').addClass('hidden');
        $('.itemQtySpan').removeClass('hidden');
        $('.itemQty').removeAttr("id", "qtynumbrpd");
        $(dt).closest('td').find('.itemQty').removeClass('hidden');
        $(dt).closest('td').find('.itemQty').select();
        $(dt).closest('td').find('.itemQty').attr("id", "qtynumbrpd");
        $(dt).closest('td').find('.itemQty').css("color", "black");
        $(dt).closest('td').find('.itemQtySpan').addClass('hidden');
        $(dt).closest('td').find('.itemQty').focus();
        qtyinlineeditcnt = 1

        //$('#qtynumbrpd').val('');
        /*$(".itemQty").blur(function(){
            hideqtyeditfield(dt)
            updateqtyval(dt)
        });*/
        var currentQty = $(dt).closest('td').find('.itemQty').val();

        $("#tempQty").val(currentQty).change();
        $("#tempQty").click();
        setTimeout(function () {
            $('.nmpd-display').select();
        }, 200);
        init_keyActivities();


    }

    function init_keyActivities() {
        if (keyActivityLog == 0) {
            if ($('.nmpd-grid').is(':visible')) {
                $(".nmpd-display").keyup(function (e) {
                    if (e.which == 13) {
                        $('.done').click();
                    } else if (e.which == 27) {
                        $('.cancel').click();
                    } else {
                        e.preventDefault();
                    }

                })
            }
        }
        keyActivityLog++
        ''

    }


    function hideqtyeditfield(dt) {
        $('.itemQty').addClass('hidden');
        $('.itemQtySpan').removeClass('hidden');
        $(dt).closest('td').find('.itemQty').addClass('hidden');
        $(dt).closest('td').find('.itemQtySpan').removeClass('hidden');
        $(dt).closest('td').find('.itemQty').removeAttr("id", "qtynumbrpd");
        updateQtylineedit(dt)
    }

    function updateqtyval(dt) {
        var newQty = $("#tempQty").val();
        var uppdatQty = $(dt).closest('td').find('.itemQty').val();
        $(dt).closest('td').find('.itemQtySpan').html(newQty);
        $(dt).closest('td').find('.itemQtySpan').removeClass('hidden');
        $(dt).closest('td').find('.itemQty').val(uppdatQty);
        $(dt).closest('td').find('.itemQty').addClass('hidden');
        //$(dt).closest('td').find('.itemQty').removeAttr("id", "qtynumbrpd");
        updateQtylineedit(dt);
        $("#itemSearch").focus();

        /*jQuery(document).click(function (e) {
            var target = e.target; //target div recorded

            $("#itemSearch").focus();

            /*if (!jQuery(target).is('.numberPlateBtn')) {
                $("#itemSearch").focus();
                // $('.itemQty').addClass('hidden');
                // $('.itemQtySpan').removeClass('hidden');
                // $(dt).closest('td').find('.itemQty').removeAttr("id", "qtynumbrpd");
                // updateQtylineedit(dt);
            }
        });*/

    }

    $('#modal_search_item').on('hidden.bs.modal', function () {
        $("#itemSearch").focus();
    })

    function updateQtylineedit(dt) {
        //if changes are done in this function changes should be made in "updateQtylineedit" this function as well

        var Qtyitm = $(dt).closest('td').find('.itemQty').val();
        clearData();

        //currentStock.val(' ' + parentTr.find('td:eq(10) .thisCurrentStk').val());


        var discper = $(dt).closest('tr').find('.itemDis').val();

        var discountAmount_tmp = $(dt).closest('tr').find('.itemDis').val();
        var salesPrice_tmp = $(dt).closest('tr').find('.itemPrice').val();
        var discountAmount_tmp = parseFloat(discountAmount_tmp);

        /*if (discountAmount_tmp > 0) {
            var unitDiscount = parseFloat(salesPrice_tmp) * (parseFloat(discountAmount_tmp) / 100);
            disAmount.val(unitDiscount);
        }*/
 
        var amount = parseFloat(Qtyitm * salesPrice_tmp).toFixed(d);
        var discAmount = parseFloat(amount * discper * 0.01).toFixed(d);
        var thisNetTot = parseFloat(amount - discAmount).toFixed(d);
        $(dt).closest('tr').find('.discountAmount').val(discAmount);
        $(dt).closest('tr').find('td:eq(9)').html(discAmount);
        $(dt).closest('tr').find('.totalAmount').val(amount);
        $(dt).closest('tr').find('td:eq(7)').html(amount);
        $(dt).closest('tr').find('.netAmount').val(thisNetTot);
        if(isGroupBasedTaxPolicy == 0){
            $(dt).closest('tr').find('td:eq(10)').html(thisNetTot);
        }else{
            $(dt).closest('tr').find('td:eq(12)').html(thisNetTot);
            load_line_tax_amount_gpos(dt);

        }
        $(dt).closest('tr').find('.thisCurrentStk').val(currentStock.val());

        /*itemDet += '<input type="hidden" class="discountAmount" value="' + discAmount + '" >';
        itemDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
        itemDet += '<input type="hidden" class="netAmount" value="' + thisNetTot + '" >';*/

        //reserved item
        var barcode = $(".selectedTR").find("td:eq(2)").text();
        var qty = $(".selectedTR").find("td:eq(5)").text();
        var type = 'plus';

        reserved_item(barcode,qty,type);

        itemAdd_sub_function();
        isThereAnyPromotion(itemAutoID.val());
        calculateDiscount_byPercentage()
    }

    function chkifinputisempty() {
        $('.itemQty').each(function () {
            if ($(this).val() == '') {
                $(this).val(1);
                $(this).closest('td').find('.itemQtySpan').html(1);
                updateQtylineedit(this)
            }
        });
    }

    function selectvalue(dt) {
        $(dt).select();
    }


    function check_if_item_qty_exceeded(datas) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/check_if_item_qty_exceeded'); ?>",
            data: {itemAutoID: datas['itemAutoID']},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 's') {
                    setValues_masterForm(datas);
                } else {
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function delete_gpos_hold_bills(invoiceID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/delete_gpos_hold_bills'); ?>",
            data: {invoiceID: invoiceID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    open_recallHold_modal_search();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }

    function redeem_loyalty(paymentTypeID) {
        var customerAutoID = $('#customerID').val();
        var rdmcustomerTelephone = $("#rdmcustomerTelephone").val();
        if ((customerAutoID) && (rdmcustomerTelephone == '')) {
            set_customer_inloyalty(customerAutoID);
        }
        $("#loyalty_redeem_modal").modal('show');
        var totalAmount = $("#final_payableNet_amt").text();
        $("#rdmbillamnt").val(parseFloat(totalAmount));
        $("#rdmpaymentTypeID").val(paymentTypeID);
        /*$("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount));
        calculateReturn();*/
    }

    /*$("#rdmcustomerTelephone").keyup(function (e) {
        var keyValue = e.which;
        if (keyValue == 13) {
            load_redeem_details_from_barcode_telno($("#rdmcustomerTelephone").val(),$("#rdmbarcode").val(),'Tel');
        }
    });*/

    $('#rdmcustomerTelephone').autocomplete({
        serviceUrl: '<?php echo site_url();?>Pos/load_redeem_details_from_telno/',
        onSelect: function (suggestion) {
            $("#rdmCustomerName").val(suggestion.CustomerName);
            $("#rdmcustomerID").val(suggestion.data);
            $("#priceToPointsEarned").val(suggestion.data);
            $("#rdmcustomerTelephone").val(suggestion.tel);
            $("#rdmbarcode").val(suggestion.barcode);
            $("#availablepoints").val(suggestion.totpoints);
            // $("#redeempts").val('');
            $("#rdmCustomerName").prop('disabled', true);
            selectedCusArray = [suggestion.data, suggestion.customerCurrency, suggestion.customerSystemCode, suggestion.CustomerName];
            selectEmployee();
        }
    });

    $("#rdmbarcode").keyup(function (e) {
        var keyValue = e.which;
        if (keyValue == 13) {
            load_redeem_details_from_barcode_telno($("#rdmcustomerTelephone").val(), $("#rdmbarcode").val(), 'bcod');
        }
    });

    function load_redeem_details_from_barcode_telno(telephone, barcode, valu) {
        //
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {telephone: telephone, barcode: barcode, valu: valu},
            url: "<?php echo site_url('Pos/load_redeem_details_from_barcode_telno'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#rdmCustomerName").val(data.CustomerName);
                    $("#rdmcustomerID").val(data.posCustomerAutoID);
                    $("#rdmcustomerTelephone").val(data.customerTelephone);
                    $("#rdmbarcode").val(data.barcode);
                    $("#availablepoints").val(data.totpoints);
                    // $("#redeempts").val('');
                    $("#rdmCustomerName").prop('disabled', true);
                    selectedCusArray = [data.posCustomerAutoID, data.customerCurrency, data.customerSystemCode, data.CustomerName];
                    selectEmployee();
                } else {
                    $("#rdmCustomerName").val('');
                    $("#rdmcustomerID").val(0);
                    $("#rdmcustomerTelephone").val('');
                    $("#rdmbarcode").val('');
                    $("#availablepoints").val(0);
                    // $("#redeempts").val('');
                    $("#rdmCustomerName").prop('disabled', true);
                    myAlert('i', 'Not Registered yet.')
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function check_loyalty_points_redeem() {
        var rdmbillamnt = $("#rdmbillamnt").val();
        var redeempts = ($("#redeempts").val()) ? $("#redeempts").val() : 0;
        var exchangeRate = $("#exchangeRate").val();
        var redeem_points = parseFloat(redeempts) / parseFloat(exchangeRate);
        var loyalty_balance_amount = $("#loyalty_balance_amount").val();
        var paymentTypeIDrdm = $("#rdmpaymentTypeID").val();
        var availablepoints = $("#availablepoints").val();
        var priceToPointsEarned = $("#pointsToPriceRedeemed").val();
        var minimumPointstoRedeem = $("#minimumPointstoRedeem").val();
        var earnedpoints_val = $("#earnedpoints_val").val();
        var totalAmount_amt_net = $("#final_payableNet_amt").text();
        var loyaltyPoints = $("#loyaltyPoints").val();
        if (redeempts != 0) {
            if (parseFloat(availablepoints) < parseFloat(priceToPointsEarned)) {
                myAlert('e', 'loyality point are not sufficient to perform redemption');
                $("#redeempts").val('');
                return false;
            }
        }


        if (redeempts != 0) {
            if (parseFloat(redeem_points) < parseFloat(minimumPointstoRedeem)) {
                myAlert('e', 'Minimum ' + minimumPointstoRedeem + ' pts should be used for redemption');
                $("#redeempts").val('');
                return false;
            }
        }


        if (redeempts != 0) {
            if (parseFloat(redeempts) > parseFloat(rdmbillamnt)) {
                myAlert('e', 'You canot redeem more than bill amount');
                $("#redeempts").val('');
                return false;
            }
        }

        if (parseFloat(redeempts) > parseFloat(loyalty_balance_amount)) {
            myAlert('e', 'You canot redeem more than available points');//
            $("#redeempts").val('');
            return false;
        }

        if (parseFloat(redeempts) < 0) {
            myAlert('e', 'Redeemable amount should be greater than zero');
            $("#redeempts").val('');
            return false;
        }

        $("#paymentType_" + paymentTypeIDrdm).val(parseFloat(redeempts));

        calculatePaidAmount();
        $("#loyalty_redeem_modal").modal('hide');
        var poinforPuchaseAmount = $("#poinforPuchaseAmount").val();
        var purchaseRewardPoint = $("#purchaseRewardPoint").val();
        $('#earnedpoints').html(((parseFloat(totalAmount_amt_net) - parseFloat(redeempts)) * (parseFloat(purchaseRewardPoint) / parseFloat(poinforPuchaseAmount))).toFixed(d));
        earned_amount = app.exRate * parseFloat($('#earnedpoints').text());
        $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
        setTimeout(function () {
            $("#rdmCustomerName").val('');
            $("#rdmcustomerTelephone").val('');
            $("#rdmbarcode").val('');
            $("#availablepoints").val('');
            // $("#redeempts").val('');
            $("#rdmbillamnt").val('');
            $("#rdmpaymentTypeID").val();
            $("#rdmCustomerName").prop('readonly', true);
        }, 200);

    }

    function set_customer_inloyalty(posCustomerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {posCustomerAutoID: posCustomerAutoID},
            url: "<?php echo site_url('Pos/set_customer_inloyalty'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#rdmCustomerName").val(data.CustomerName);
                    $("#rdmcustomerID").val(data.posCustomerAutoID);
                    $("#rdmcustomerTelephone").val(data.customerTelephone);
                    $("#loyaltyPoints").val(data.loyaltyPoints);

                    $("#rdmbarcode").val(data.barcode);
                    $("#loyalitycardno").html(data.barcode);
                    $("#availablepoints_loyality").html(data.totpoints);
                    $("#customer_loyalty_amount").html((parseFloat(data.totpoints) * parseFloat(data.exchangeRate)).toFixed(d));
                    $("#loyalty_balance_amount").val((parseFloat(data.totpoints) * parseFloat(data.exchangeRate)).toFixed(d));
                    $("#availablepoints").val(data.totpoints);
                    //$("#redeempts").val('');
                    $("#pointsToPriceRedeemed").val(data.pointsToPriceRedeemed);
                    $("#minimumPointstoRedeem").val(data.minimumPointstoRedeem);
                    $("#pointstoprice").html(data.pointsToPriceRedeemed);
                    $("#minimupoints").html(data.minimumPointstoRedeem);
                    $('.loylitycusdetail').removeClass('hide');
                    $("#exchangeRate").val(data.exchangeRate);
                    $("#poinforPuchaseAmount").val(data.poinforPuchaseAmount);
                    $("#purchaseRewardPoint").val(data.purchaseRewardPoint);
                    app.exRate = data.exchangeRate;
                    if (data.pointsToPriceRedeemed != '') {
                        $("#msgpoints").html('Minimum ' + data.pointsToPriceRedeemed + ' pts should be available to perform redemption');
                    } else {
                        $("#msgpoints").html('Minimum 0 pts should be available to perform redemption');
                    }

                    $("#rdmCustomerName").prop('disabled', true);
                    /*selectedCusArray = [data.posCustomerAutoID, data.customerCurrency, data.customerSystemCode, data.CustomerName];
                    selectEmployee();*/
                } else {
                    $("#rdmCustomerName").val('');
                    $("#rdmcustomerID").val(0);
                    $("#rdmcustomerTelephone").val('');
                    $("#rdmbarcode").val('');
                    $("#availablepoints").val('');
                    // $("#redeempts").val('');
                    $("#minimumPointstoRedeem").val('');
                    $("#priceToPointsEarned").val('');
                    $("#pointstoprice").html(0);
                    $("#minimupoints").html(0);
                    $("#rdmCustomerName").prop('disabled', true);
                    //myAlert('i', 'Not Registered yet.')
                }
                $("#myInputautocompleteBarcode-list").html("");
            }, error: function () {
                stopLoad();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }


    function advance_invoice_search_modal() {
        $('#invoiceCodeDate').val('');
        $('#invoiceItemCode').val('');
        $('#itemIdhn').val('');
        $('#Returninvload').empty();
        $('#ReturninvItemload').empty();
        $('#advance_return_modal').modal('show');
    }

    function load_date_wise_return() {
        var filterFrom2 = $('#filterFrom2').val();
        var filterTo2 = $('#filterTo2').val();
        var invoiceCodeDate = $('#invoiceCodeDate').val();

        if (jQuery.isEmptyObject(filterFrom2)) {
            myAlert('e', 'From Date is Required');
            return false;
        }

        if (jQuery.isEmptyObject(filterTo2)) {
            myAlert('e', 'To Date is Required');
            return false;
        }


        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/invoice_searchLiveSearch_date_wise'); ?>",
            data: {filterFrom2: filterFrom2, filterTo2: filterTo2, invoiceCode: invoiceCodeDate},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                x = 1;
                $('#Returninvload').empty();
                if (data[0] == 's') {
                    $.each(data[2], function (key, value) {
                        $('#Returninvload').append('<tr><td>' + x + '</td><td>' + value['invoiceCode'] + '</td><td><button class="btn btn-md btn-primary" onclick="addInvoiceReturnSearch(\'' + value['invoiceCode'] + '\')">Add</button></td></tr>');
                        x++;
                    });
                    $('#dateWiseinvtable').dataTable();
                } else {
                    myAlert(data[0], data[1])
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }


    function addInvoiceReturnSearch(invoiceCode) {
        $('#returnInvoiceTB tbody>tr').remove();
        var crToCustomer_Btn = $('#credit-to-customer-btn');

        crToCustomer_Btn.hide();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceCode': invoiceCode},
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_search'); ?>",
            success: function (data, status, request) {

                stopLoad();
                if (data[0] != 's') {
                    myAlert(data[0], data[1]);
                } else {
                    var invData = data[1];
                    var invDet = data[2];
                    if (invData['customerID'] != 0) {
                        //crToCustomer_Btn.show();
                    }

                    $('#returnCreditNo').val(data[3]);
                    $('#return-invoiceID').val(invData['invoiceID']);
                    $('#return-customerID').val(invData['customerID']);
                    $('#return-cusCode').val(invData['customerCode']);
                    $('#return-cusName').val(invData['cusName']);
                    $('#return-cusBalance').val(commaSeparateNumber(invData['cusBalance'], dPlaces));
                    $('#return-inv-date').val(invData['invoiceDate']);
                    //$('#return-date').datepicker('update', invData['invoiceDate']);
                    var invctt = parseFloat(invData['netTotal']) + parseFloat(invData['generalDiscountAmount']) + parseFloat(invData['promotiondiscountAmount']);
                    $('#return-calculate-invTot').val(commaSeparateNumber(invctt, dPlaces));
                    $('#return-calculate-invDisc').val(commaSeparateNumber(invData['generalDiscountAmount'], dPlaces));
                    $('#return-calculate-invDiscperc_hn').val(invData['generalDiscountPercentage']);
                    $('#gendiscref').html("(" + invData['generalDiscountPercentage'] + "%)");
                    localStorage.setItem('promotionDiscountAmount', invData['promotiondiscountAmount']);
                    $('#promodiscref').html("(" + invData['promotiondiscount'] + "%)");
                    $('#promotionDiscountID').val(invData['promotionID']);
                    $('#promo-discount').val(invData['promotiondiscount']);
                    $('#promoDiscountAmount').val(invData['promotiondiscountAmount']);
                    $('#return-calculate-invBalance').val(commaSeparateNumber(invData['balanceAmount'], dPlaces));
                    $('#return-credit-total').val(commaSeparateNumber(invData['netTotal'], dPlaces));

                    // var refundAmount = (getNumberAndValidate(invData['netTotal']) - (invData['balanceAmount']));
                    var refundAmount = parseFloat(invData['netTotal']) - parseFloat(invData['balanceAmount']);
                    refundAmount = (refundAmount > 0) ? refundAmount : 0;
                    $('#return-refund').val(commaSeparateNumber(refundAmount, dPlaces));
                    $('#return-refundable-hidden').val(refundAmount);

                    var appData = '';
                    $.each(invDet, function (i, itmData) {
                        var returnPrice = itmData['price'];
                        var returnAmount = returnPrice * itmData['balanceQty'];
                        var returnDicPer = itmData['discountPer'];
                        var discAmount = (returnDicPer > 0) ? (returnAmount * returnDicPer * 0.01) : 0;
                        var lineNetTot = returnAmount - discAmount;
                        var returnQTY = '';
                        var otherData = '';
                        if (itmData['balanceQty'] > 0) {
                            returnQTY = '<input type="text" name="return_QTY[]" id="returnQTY' + i + '" class="returnQTY number" ';
                            returnQTY += 'data-maxqty="' + itmData['balanceQty'] + '" data-uom="' + itmData['unitOfMeasure'] + '" ';
                            returnQTY += 'value="' + itmData['balanceQty'] + '" data-unit_price="'+returnPrice+'" data-discount_per_unit="'+returnDicPer+'" style="width: 65px; color:#000; padding:0px 5px; height: 16px">';

                            otherData += '<input type="hidden" name="invoiceDetailsID[]" value="' + itmData['invoiceDetailsID'] + '" >';
                            otherData += '<input type="hidden" name="itemID[]" value="' + itmData['itemAutoID'] + '" >';
                            otherData += '<input type="hidden" name="itemName[]" value="' + itmData['itemDescription'] + '" >';
                            otherData += '<input type="hidden" name="itemUOM[]" value="' + itmData['defaultUOM'] + '" >';
                            otherData += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itmData['qty'] + '" >';
                            otherData += '<input type="hidden" name="itemMaxQty[]" class="itemMaxQty" value="' + itmData['balanceQty'] + '" >';
                            otherData += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + returnPrice + '" >';
                            otherData += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + returnDicPer + '" >';
                            otherData += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + discAmount + '" >';
                            otherData += '<input type="hidden" class="return-discountAmount" value="' + discAmount + '" >';
                            otherData += '<input type="hidden" class="return-totalAmount" value="' + returnAmount + '" >';
                            otherData += '<input type="hidden" class="return-netAmount" value="' + lineNetTot + '" >';
                            otherData += '<input type="hidden" name="creditNoteID" value="' + invData['creditNoteID'] + '" >';

                            appData = '<tr>';
                            appData += '<td align="right"></td>';
                            appData += '<td>' + itmData['seconeryItemCode'] + '</td>';
                            appData += '<td>' + itmData['itemDescription'] + '</td>';
                            appData += '<td>' + itmData['defaultUOM'] + '</td>';
                            appData += '<td align="right">' + itmData['balanceQty'] + '</td>';
                            appData += '<td align="right" style="width: 70px">' + returnQTY + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnPrice, dPlaces) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnAmount, dPlaces) + '</td>';
                            appData += '<td align="right">' + getNumberAndValidate(returnDicPer) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(lineNetTot, dPlaces) + '</td>';
                            appData += '<td align="right">';
                            appData += '<span class="glyphicon glyphicon-trash deleteRow-return" style="color:rgb(209, 91, 71); position: static"></span>';
                            appData += otherData;
                            appData += '<input type="hidden" class="return-item-image-hidden" value="<?php echo base_url('images/item/');?>/' + itmData['itemImage'] + '" >';
                            appData += '</td>';
                            appData += '</tr>';
                            returnInvoiceTB.append(appData);
                        }

                    });
                    $('#advance_return_modal').modal('hide');

                    getReturnSubTotal();
                    getReturnNetTotal();
                    getReturnDiscTotal();
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Error in invoice calling loading.')
            }
        });
    }


    function load_item_wise_return() {
        var itemAutoID = $('#itemIdhn').val();


        if (jQuery.isEmptyObject(itemAutoID)) {
            myAlert('e', 'Select Item');
            return false;
        }

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/invoice_searchLiveSearch_item_wise'); ?>",
            data: {itemAutoID: itemAutoID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                x = 1;
                $('#ReturninvItemload').empty();
                if (data[0] == 's') {
                    $.each(data[2], function (key, value) {
                        $('#ReturninvItemload').append('<tr><td>' + x + '</td><td>' + value['invoiceCode'] + '</td><td><button class="btn btn-md btn-primary" onclick="addInvoiceitemReturnSearch(\'' + value['invoiceCode'] + '\')">Add</button></td></tr>');
                        x++;
                    });
                } else {
                    myAlert(data[0], data[1])
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            }
        });
    }


    function addInvoiceitemReturnSearch(invoiceCode) {
      
        var itemAutoID = $('#itemIdhn').val();
        $('#returnInvoiceTB tbody>tr').remove();
        var crToCustomer_Btn = $('#credit-to-customer-btn');

        crToCustomer_Btn.hide();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceCode': invoiceCode},
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_search'); ?>",
            success: function (data, status, request) {

                stopLoad();
                if (data[0] != 's') {
                    myAlert(data[0], data[1]);
                } else {
                    var invData = data[1];
                    var invDet = data[2];
                    if (invData['customerID'] != 0) {
                        //crToCustomer_Btn.show();
                    }

                    $('#returnCreditNo').val(data[3]);
                    $('#return-invoiceID').val(invData['invoiceID']);
                    $('#return-customerID').val(invData['customerID']);
                    $('#return-cusCode').val(invData['customerCode']);
                    $('#return-cusName').val(invData['cusName']);
                    $('#return-cusBalance').val(commaSeparateNumber(invData['cusBalance'], dPlaces));
                    $('#return-inv-date').val(invData['invoiceDate']);
                    //$('#return-date').datepicker('update', invData['invoiceDate']);
                    var invctt = parseFloat(invData['netTotal']) + parseFloat(invData['generalDiscountAmount']) + parseFloat(invData['promotiondiscountAmount']);
                    $('#return-calculate-invTot').val(commaSeparateNumber(invctt, dPlaces));
                    $('#return-calculate-invDisc').val(commaSeparateNumber(invData['generalDiscountAmount'], dPlaces));
                    $('#return-calculate-invDiscperc_hn').val(invData['generalDiscountPercentage']);
                    $('#gendiscref').html("(" + invData['generalDiscountPercentage'] + "%)");
                    localStorage.setItem('promotionDiscountAmount', invData['promotiondiscountAmount']);
                    $('#promodiscref').html("(" + invData['promotiondiscount'] + "%)");
                    $('#promotionDiscountID').val(invData['promotionID']);
                    $('#promo-discount').val(invData['promotiondiscount']);
                    $('#promoDiscountAmount').val(invData['promotiondiscountAmount']);
                    $('#return-calculate-invBalance').val(commaSeparateNumber(invData['balanceAmount'], dPlaces));
                    $('#return-credit-total').val(commaSeparateNumber(invData['netTotal'], dPlaces));

                    // var refundAmount = (getNumberAndValidate(invData['netTotal']) - (invData['balanceAmount']));
                    var refundAmount = parseFloat(invData['netTotal']) - parseFloat(invData['balanceAmount']);
                    refundAmount = (refundAmount > 0) ? refundAmount : 0;
                    $('#return-refund').val(commaSeparateNumber(refundAmount, dPlaces));
                    $('#return-refundable-hidden').val(refundAmount);

                    var appData = '';
                    $.each(invDet, function (i, itmData) {
                        if (itemAutoID == itmData['itemAutoID']) {
                            var returnPrice = itmData['price'];
                            var returnAmount = returnPrice * itmData['balanceQty'];
                            var returnDicPer = itmData['discountPer'];
var discAmount = (returnDicPer > 0) ? (returnAmount * returnDicPer * 0.01) : 0;
                            var lineNetTot = returnAmount - discAmount;
                            var returnQTY = '';
                            var otherData = '';
                            if (itmData['balanceQty'] > 0) {
                                returnQTY = '<input type="text" name="return_QTY[]" id="returnQTY' + i + '" class="returnQTY number" ';
                                returnQTY += 'data-maxqty="' + itmData['balanceQty'] + '" data-uom="' + itmData['unitOfMeasure'] + '" ';
                                returnQTY += 'value="' + itmData['balanceQty'] + '" data-unit_price="'+returnPrice+'" style="width: 65px; color:#000; padding:0px 5px; height: 16px">';

                                otherData += '<input type="hidden" name="invoiceDetailsID[]" value="' + itmData['invoiceDetailsID'] + '" >';
                                otherData += '<input type="hidden" name="itemID[]" value="' + itmData['itemAutoID'] + '" >';
                                otherData += '<input type="hidden" name="itemName[]" value="' + itmData['itemDescription'] + '" >';
                                otherData += '<input type="hidden" name="itemUOM[]" value="' + itmData['defaultUOM'] + '" >';
                                otherData += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itmData['qty'] + '" >';
                                otherData += '<input type="hidden" name="itemMaxQty[]" class="itemMaxQty" value="' + itmData['balanceQty'] + '" >';
                                otherData += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + returnPrice + '" >';
                                otherData += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + returnDicPer + '" >';
                                otherData += '<input type="hidden" name="itemDisAmount[]" class="discountAmount" value="' + discAmount + '" >';
                                otherData += '<input type="hidden" class="return-discountAmount" value="' + discAmount + '" >';
                                otherData += '<input type="hidden" class="return-totalAmount" value="' + returnAmount + '" >';
                                otherData += '<input type="hidden" class="return-netAmount" value="' + lineNetTot + '" >';
                                otherData += '<input type="hidden" name="creditNoteID" value="' + invData['creditNoteID'] + '" >';

                                appData = '<tr>';
                                appData += '<td align="right"></td>';
                                appData += '<td>' + itmData['seconeryItemCode'] + '</td>';
                                appData += '<td>' + itmData['itemDescription'] + '</td>';
                                appData += '<td>' + itmData['defaultUOM'] + '</td>';
                                appData += '<td align="right">' + itmData['balanceQty'] + '</td>';
                                appData += '<td align="right" style="width: 70px">' + returnQTY + '</td>';
                                appData += '<td align="right">' + commaSeparateNumber(returnPrice, dPlaces) + '</td>';
                                appData += '<td align="right">' + commaSeparateNumber(returnAmount, dPlaces) + '</td>';
                                appData += '<td align="right">' + getNumberAndValidate(returnDicPer) + '</td>';
                                appData += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';
                                appData += '<td align="right">' + commaSeparateNumber(lineNetTot, dPlaces) + '</td>';
                                appData += '<td align="right">';
                                appData += '<span class="glyphicon glyphicon-trash deleteRow-return" style="color:rgb(209, 91, 71); position: static"></span>';
                                appData += otherData;
                                appData += '<input type="hidden" class="return-item-image-hidden" value="<?php echo base_url('images/item/');?>/' + itmData['itemImage'] + '" >';
                                appData += '</td>';
                                appData += '</tr>';
                                returnInvoiceTB.append(appData);
                            }
                        }


                    });
                    $('#advance_return_modal').modal('hide');

                    getReturnSubTotal();
                    getReturnNetTotal();
                    getReturnDiscTotal();
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Error in invoice calling loading.')
            }
        });
    }

    app = {};

    function customerloyalitydetails(customerAutoID) {
        $('#loyalitycardno').html('');
        $('#availablepoints_loyality').html('');
        $("#customer_loyalty_amount").html('');
        $('#earnedpoints').html('');
        $('#earnedpoints_val').val('');
        if (customerAutoID > 0) {
            var totalpayment = $('#netTot_after_g_disc').val();
            var availablepoints = $("#availablepoints").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    customerAutoID: customerAutoID,
                    'totalpayment': totalpayment,
                    'availablepoints': availablepoints
                },
                url: "<?php echo site_url('Pos/fetch_loyalitydetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#loyalitycardno').html((data['pos_loyality']['loyalitycardno']));
                    $('#availablepoints_loyality').html(parseFloat(data['pos_loyality']['totpoints']).toFixed(d));
                    $("#customer_loyalty_amount").html((parseFloat(data['loyalty_setup']['amount']) * parseFloat(data['pos_loyality']['totpoints'])).toFixed(d));
                    $('#earnedpoints').html(parseFloat(data['totpts']).toFixed(d));
                    app.exRate = data['loyalty_setup']['amount'];
                    earned_amount = app.exRate * data['totpts'];
                    $('#earnedpoints_amount').html(parseFloat(earned_amount).toFixed(d));
                    $('#earnedpoints_val').val(data['totpts']);
                    $('#loyaltyPoints').val(data['loyaltyPoints']);
                    $('.loylitycusdetail').removeClass('hide');
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error has occurred.')
                }
            });

        } else {
            $('.loylitycusdetail').addClass('hide');
        }
    }

    function generate_loyality(customerAutoID, telephoneno) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "you want to generate a loyalty card",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos/generateloyalitycard'); ?>",
                    data: {customerAutoID: customerAutoID, 'telephoneno': telephoneno},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            LoadCustomers();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        if (jqXHR.status == false) {
                            myAlert('w', 'No Internet, Please try again');
                        } else {
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    }
                });
            });

    }

    function openCustomerModal_general() {
        $('#loyalitycardno_gpos').prop('readonly', true);
        $("#pos_payments_customer_modal").modal('show');
        initializeitemTypeahead()
        loadCountryDetail_customer();
    }

    function loadCountryDetail_customer() {
        $("#country-select").empty();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_customerMaster/loadCountryDetail"); ?>',
            dataType: 'json',
            data: '',
            async: false,
            success: function (data) {
                $("#country-select").append(data);
                $("#customerCountry").val('<?php echo $companycountry['CountryDes']?>');
                $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
                $('#customerCountryId_o').val('<?php echo $companycountry['countryID']?>');
                $("#customerCountry").select2();
            }
        });
    }

    function initializeitemTypeahead() {

        $('#customerTelephoneTmp').autocomplete({
            serviceUrl: '<?php echo site_url();?>Pos/fetch_pos_customer_details_general/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#customerIDTmp').val(suggestion.posCustomerAutoID);
                    $('#customerID').val(suggestion.posCustomerAutoID);
                    $('#customerNameTmp').val(suggestion.CustomerName);
                    $('#customerAddressTmp').val(suggestion.CustomerAddress1);
                    $('#customerTelephoneTmp').val(suggestion.customerTelephone);
                    $('#customerEmailTmp').val(suggestion.customerEmail);
                    if ((suggestion.loyalityno != 0)) {
                        $('#loyalitycardno_gpos').val(suggestion.loyalityno);
                        $('#loyalitycardno_gpos').prop('readonly', true);
                        $('.loyalitycardgen').addClass('hide');
                    } else {
                        load_barcode_loyalty();
                        $('#loyalitycardno_gpos').prop('readonly', false);
                    }


                    if (suggestion.customerCountry) {
                        $("#customerCountry").val(suggestion.customerCountry);
                        $('#customerCountry').select2().trigger('change');
                    }
                }, 150);

            }
        });
        $('#customerTelephoneTmp').off('focus.autocomplete');
    }

    function remove_item_all_description_edit_gpos(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $('#customerIDTmp').val('');
            $('#customerNameTmp').val('');
            $('#customerAddressTmp').val('');
            $('#customerEmailTmp').val('');
            $('#customerCountryId').val(<?php echo $companycountry['countryID']?>);
            $('#customerCountry').val('<?php echo $companycountry['CountryDes']?>');
            $('#customerCountry').select2().trigger('change');
            $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
            $('#customerCountryId_o').val('<?php echo $companycountry['countryID']?>');
            $('#loyalitycardno_gpos').val('');
            $('.customerSpan').text('Cash');
            $('#loyalitycardno_gpos').prop('readonly', true);
            $('.loyalitycardgen').addClass('hide');
        }
    }

    function setCustomerInfo_gpos() {

        var customerEmail = $("#customerEmailTmp").val();
        if (customerEmail != null && customerEmail != '') {
            if (!validateEmail(customerEmail)) {
                myAlert('e', 'Please enter valid email');
                return false;
            }
        }
        var customerName = $("#customerNameTmp").val();
        var customerTel = $("#customerTelephoneTmp").val();
        var customerCountry = $("#customerCountry").val();
        var customerCountryCode = $("#customerCountryCode").val();
        var customerCountryId = $("#customerCountryId").val();
        var customerAddress = $("#customerAddressTmp").val();
        var customerID = $("#customerIDTmp").val();

        if ($("#customerTelephoneTmp").val() == '') {
            myAlert('e', 'Telephone Number is Required');
            return false;
        }

        if ($("#customerNameTmp").val() == '') {
            myAlert('e', 'Customer Name is Required');
            return false;
        }
        $("#customerName").val(customerName);
        $("#customerTelephone").val(customerTel);
        $("#customerAddress").val(customerAddress);
        $("#customerID").val(customerID);
        $("#customerEmail").val(customerEmail);
        $('.customerSpan').text(customerName);
        if (customerID > 0) {
            customerloyalitydetails(customerID);
            set_customer_inloyalty(customerID)
            $("#pos_payments_customer_modal").modal('hide');
        } else {
            save_customer_detail_gpos();

        }

    }

    function clearCustomerVal_gpos() {
        $("#customerName").val('');
        $("#customerTelephone").val('');
        $("#customerAddressTmp").val('');
        $("#customerIDTmp").val('');
        $("#customerEmail").val('');
        $("#customerEmailTmp").val('');
        $('.customerSpan').text('Cash');

        $("#customerCountry").val('<?php echo $companycountry['CountryDes']?>');
        $('#customerCountryCode').val('<?php echo $companycountry['countryCode']?>');
        $('#customerCountryId').val('<?php echo $companycountry['countryID']?>');
        $("#customerID").val('');
        $('#loyalitycardno').html('');
        $('#availablepoints_loyality').html('');
        $("#customer_loyalty_amount").html('');
        $('#earnedpoints').html('');
        $('#earnedpoints_val').val(' ');
        $('#loyaltyPoints').val(' ');

        $("#rdmCustomerName").val('');
        $("#rdmcustomerID").val(0);
        $("#rdmcustomerTelephone").val('');
        $("#rdmbarcode").val('');
        $("#availablepoints").val('');
        // $("#redeempts").val('');
        $("#minimumPointstoRedeem").val('');
        $("#priceToPointsEarned").val('');
        $("#pointstoprice").html(0);
        $("#minimupoints").html(0);
        $("#rdmCustomerName").prop('disabled', true);
        $('.loylitycusdetail').addClass('hide');
        $('#customerCountry').select2().trigger('change');
        $('.CreditSalesRefNo').val('');
    }

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function save_customer_detail_gpos() {
        var data = $('#frm_pos_customer').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos/save_customer_posgen'); ?>",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#customerIDTmp").val(data[2]);
                    $("#customerID").val(data[2]);
                    $('#loyalitycardno_gpos').prop('readonly', false);
                    load_barcode_loyalty();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function load_barcode_loyalty() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {telephone: 0},
            url: "<?php echo site_url('Pos/load_barcode_loyalty'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#loyalitycardno_gpos').val(data)
                $('.loyalitycardgen').removeClass('hide');
            }, error: function () {
                stopLoad();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function save_loyalty_card() {
        var barcode = $("#loyalitycardno_gpos").val();
        var gc_customerTelephone = $("#customerTelephoneTmp").val();
        var gc_CustomerName = $("#customerNameTmp").val();
        var customerID = $("#customerID").val();
        var cardMasterID = ' ';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                barcode: barcode,
                customerTelephone: gc_customerTelephone,
                gc_CustomerName: gc_CustomerName,
                customerID: customerID,
                cardMasterID: cardMasterID
            },
            url: "<?php echo site_url('Pos/save_loyalty_card'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $("#pos_payments_customer_modal").modal('hide');
                    $(".loyalitycardgen").addClass('hide');
                    $('#loyalitycardno_gpos').prop('readonly', true);
                    customerloyalitydetails(customerID);
                    set_customer_inloyalty(customerID)
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    function fetch_tax_drop(id,itemAutoID) {
        

        //taxedit
        if(tax_item_arr[itemAutoID] !== undefined){
            attach_drop_down(id,tax_item_arr[itemAutoID]);
        }else{
            $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Pos/fetch_tax_drop_itemwise'); ?>",
            success: function (data) {
                //alert(id);
                tax_item_arr[itemAutoID] = data;
                attach_drop_down(id,data)
               
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
            });
        }
       
       
    }

    function attach_drop_down(id,data){

        $('#tax_type_'+id).empty();
        var mySelect = $('#tax_type_'+id);
        mySelect.append($('<option></option>').val('').html('Select Tax Type'));
        
        if (!jQuery.isEmptyObject(data)) {
            $.each(data['tax_drop'], function (val, text) {
                mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
            });

            if(data['selected_itemTax'] != 0){
                selectedValueTax = 1;
            }else{
                selectedValueTax = 0;
            }

            if(data['selected_itemTax']!=0 && selectedValueTax ==0 && holdbillTax ==0){
                $('#tax_type_'+id).val(data['selected_itemTax']).change();
            }else if(selectedValueTax!=0 && holdbillTax ==0) {
                $('#tax_type_'+id).val(selectedValueTax).change();
            }else if (holdbillTax!=0){
                $('#tax_type_'+id).val(holdbillTax).change();
            }
        }

    }

    function load_line_tax_amount_gpos(element){
        var qut = $(element).closest('tr').find('.itemQty').val();
        var itemAutoID = $(element).closest('tr').find('.itemID').val();
        var amount = $(element).closest('tr').find('.totalAmount').val();
        var discoun = $(element).closest('tr').find('.discountAmount').val();
        var taxtype = $(element).closest('tr').find('.tax_type').val();
        var discper = $(element).closest('tr').find('.itemDis').val();
        var exchange = $(element).closest('tr').find('.exchange').val();

        if(exchange == 1){
            return false;
        }

        var Qtyitm = qut;

        var discountAmount_tmp = $(element).closest('tr').find('.itemDis').val();
        var salesPrice_tmp = $(element).closest('tr').find('.itemPrice').val();
        var discountAmount_tmp = parseFloat(discountAmount_tmp);

        var lintaxappamnt = 0;

        var amount_edit = Qtyitm * salesPrice_tmp;
        var discAmount = amount_edit * discper * 0.01;
        var thisNetTot = (amount_edit - discAmount);
        var totalTaxAmt = 0;

        if (jQuery.isEmptyObject(qut)) {
            qut = 0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        lintaxappamnt = (amount);

        if (!jQuery.isEmptyObject(taxtype)) {
            $(element).closest('tr').find('.taxFormula').val(taxtype);
            $(element).closest('tr').find('.taxamountCal').empty();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'applicableAmnt': amount_edit,
                    'taxtype': taxtype,
                    'itemAutoID': itemAutoID,
                    'discount': discoun,
                    'quantityRequested': qut,
                },
                url: "<?php echo site_url('Pos/fetch_linewiseTax_calculation_gpos'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $(element).closest('tr').find('.taxAmount').html((parseFloat(data)).toFixed(d));
                    $(element).closest('tr').find('.taxamountCal').val((parseFloat(data)));
                    $(element).closest('tr').find('.totalAmount').val((parseFloat(( (parseFloat(thisNetTot))+(parseFloat(data))))));
                    $(element).closest('tr').find('td:eq(12)').html(( (parseFloat(thisNetTot))+(parseFloat(data))).toFixed(d));
                    setTimeout(function () {
                        itemAdd_sub_function();
                        neutral_generalDiscount();
                    }, 1000);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $(element).closest('tr').find('.taxAmount').html((parseFloat(0)).toFixed(d));
            $(element).closest('tr').find('.taxamountCal').val((parseFloat(0)));
            $(element).closest('tr').find('.totalAmount').val((parseFloat(( (parseFloat(thisNetTot))))));
            $(element).closest('tr').find('td:eq(12)').html((parseFloat(thisNetTot)).toFixed(d));
            setTimeout(function () {
                itemAdd_sub_function();
                neutral_generalDiscount();
            }, 1000);
        }
       // taxCalTotal.val(parseFloat(1));

    }
    var taxAmountCalulation =  0;
    function load_line_tax_amount_gpos_test(qty,amount,discoun,itemAutoID){
        var qut = qty;
        var itemAutoID = itemAutoID;
        var amount = amount;
        var discoun = discoun;
        var taxtype = selectedValueTax;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'applicableAmnt': amount,
                'taxtype': taxtype,
                'itemAutoID': itemAutoID,
                'discount': discoun,
                'quantityRequested': qut,
            },
            url: "<?php echo site_url('Pos/fetch_linewiseTax_calculation_gpos'); ?>",
            beforeSend: function () {

                startLoad();
            },
            success: function (data) {

                setTimeout(function () {
                    taxCalTotal.val(data);
                    itemAdd_sub_function();
                    neutral_generalDiscount();
                }, 1500);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });

    }

</script>