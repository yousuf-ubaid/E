<strong>Billing based on Completion %</strong><br>
<button type="button" onclick="delete_all_project_detail(<?php echo $invoiceAutoID?>)" class="btn btn-danger btn-xs pull-right">Delete All</button><br><br>
<table class="table table-bordered table-condensed">
  <thead>
    <tr>
        <th style="width: 30%">Description</th>
        <th style="width: 20%">Total Amount - As per BOQ selleing price</th>
        <th style="width: 10%">Previous % caimed</th>
        <th style="width: 10%">current % claimed</th>
        <th style="width: 10%">Invoice Amount</th>
        <th style="width: 10%">Remaining</th>
    </tr>
 </thead>
  <tbody>
    <?php 
        $category = array();
        $totalvariationcontract = 0;
        $grandtotalinvoice = 0;
        foreach ($invoiceproject as $val) {
            $category[$val["isVariation"]][] = $val;
        }
         if (!empty($category)) {
         
           foreach ($category as $key => $mainCategory) {
           $totalamount = 0;
           $totalinvoiceamount = 0;
            foreach ($mainCategory as $key2 => $subCategory) {
                     $prevclaimepercentage = $subCategory['boqPreviousClaimPercentage'];

                 if($prevclaimepercentage > 0)
                {
                    $remainingamount = number_format((($subCategory['totalTransCurrency'] -$subCategory['transactionAmount'])-($subCategory['totalTransCurrency']*($prevclaimepercentage/100))),$master['transactionCurrencyDecimalPlaces']);
                }else
                {
                    $remainingamount = number_format(($subCategory['totalTransCurrency'] -$subCategory['transactionAmount']),$master['transactionCurrencyDecimalPlaces']);

                }


        echo "<tr>
                    <input type='hidden' id='prevclaimedpercentage_".$subCategory['invoiceDetailsAutoID']."' name='prevclaimedpercentage' value=".$prevclaimepercentage.">
                    <input type='hidden' id='remainingamount_".$subCategory['invoiceDetailsAutoID']."' name='remainingamount' value=".$remainingamount.">

                   <td>" . $subCategory["itemDescription"] . "</td>
                  <td style='text-align: right;'>" . number_format($subCategory['totalTransCurrency'],$master['transactionCurrencyDecimalPlaces']) . "</td>
                  <td style='text-align: right;'>".$subCategory['boqPreviousClaimPercentage']."%</td>
                  <td style='text-align: center;'> <input style='width: 60%;text-align: right;' type='text' value=".$subCategory['boqTotalClaimPercentage']." name='currenctclaimed' id='currenctclaimed_".$subCategory['invoiceDetailsAutoID']."' onchange='update_claimedamount(0,".$subCategory['totalTransCurrency'].",this.value,".$subCategory['invoiceDetailsAutoID'].",".$subCategory['transactionCurrencyDecimalPlaces'].",".$subCategory['invoiceDetailsAutoID'].",".$subCategory['unitRateTransactionCurrency'].",1,".$subCategory['headerID'].");'>&nbsp;%</td>
                     

                    <td style='text-align: right;'>
<input style='width: 60%;text-align: right;' type='text' value=".$subCategory['transactionAmount']."
 name='currenctclaimedamount' id='currenctclaimedamount_".$subCategory['invoiceDetailsAutoID'] ."' onchange='update_claimedamount_percentage(0,".$subCategory['totalTransCurrency'].",this.value,".$subCategory['invoiceDetailsAutoID'].",".$subCategory['transactionCurrencyDecimalPlaces'].",".$subCategory['invoiceDetailsAutoID'].",".$subCategory['unitRateTransactionCurrency'].",2,".$subCategory['headerID'].");'>
                      </td>

                      <td style='text-align: right;'>
                      
                        <label id='remaining_".$subCategory['invoiceDetailsAutoID']."'>".$remainingamount."</label>
                      </td>

              </tr>";

                $totalamount+= $subCategory['totalTransCurrency'];
                $totalinvoiceamount += $subCategory['transactionAmount'];
                $totalvariationcontract+= $subCategory['totalTransCurrency'];
                $grandtotalinvoice  += $subCategory['transactionAmount'];


                    }

               if($subCategory["isVariation"] == 0 )
               {
                   echo "
                        <tr style='background: #e1e1e18c'>
                        <td><b>Contract Value</b></td>
                              <td style='text-align: right;'><b>".number_format($totalamount,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
                   echo "<tr><td colspan='6'>&nbsp;</td></tr>";
                   echo "<tr style='background: #e1e1e18c'><td colspan='6'><b>Variations</b></td></tr>";
               }
           }
             echo "<tr style='background: #e1e1e18c'>

                       <td>total variations Amount</td>
                       <td style='text-align: right;'><b>".number_format($totalamount,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
             echo "<tr style='background: #e1e1e18c'>
                        <td><b>Total contract Value+ variations Amount</b>
                        <td style='text-align: right;'><b>".number_format($totalvariationcontract,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format($grandtotalinvoice,$master['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                        </td>
                        </tr>";

             echo "<tr><td colspan='6'>&nbsp;</td></tr>";
             echo "<tr><td colspan='6'>&nbsp;</td></tr>";
             echo "<tr style='background: #e1e1e18c'><td colspan='6'><b>Deductions</b></td></tr>";



             echo "<tr>

                       <td>Advance
                    <button type='button' onclick=\"advancematchcreation(".$subCategory['invoiceAutoID'].",".$subCategory['projectID'].")\" class='btn btn-primary pull-right btn-xs' data-toggle='tooltip' data-placement='left'><i class='fa fa-plus'></i></button>
                        </td>

                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format(get_advance_amount($subCategory['invoiceAutoID']),$master['transactionCurrencyDecimalPlaces'])."</b></td>
                     
                        </tr>";
             echo "<tr>

                    <td colspan='4'>Retention (".$subCategory['retensionPercentage']."%)</td>


                  <td style='text-align: right;'><b>".number_format((($grandtotalinvoice)*($subCategory['retensionPercentage']/100)),$master['transactionCurrencyDecimalPlaces'])."</b></td>
                    </tr>";
             echo "<tr style='background: #e1e1e18c'>

                       <td><b>Total Dedections</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format((get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100))),$master['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";

             echo "<tr style='background: #e1e1e18c'>
                       <td><b>Net Total</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>


      ".number_format(($grandtotalinvoice-(get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100)))),$master['transactionCurrencyDecimalPlaces'])."



      </b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
            
         
         }



    ?>


  </tbody>
</table>





<script type="text/javascript">
  var matchID = '';
  var currencydecimal = <?php echo $master['transactionCurrencyDecimalPlaces']?>;
  var invoiceAutoID = <?php echo $invoiceAutoID?>;
function update_claimedamount(Previousclaimed,totalunitamount,claimedpercentage,detailID,transactionCurrencyDecimalPlaces,invoiceDetailsAutoID,unitRateTransactionCurrency,type,headerID) {
    var prevpersentage = $('#prevclaimedpercentage_'+detailID).val();

    var currentprvpercentage = (100 - $('#prevclaimedpercentage_'+detailID).val());
    var invoiceamount = (totalunitamount*((claimedpercentage)-(prevpersentage))/100);
    var invoiceamount_totalclaimed = (totalunitamount*(claimedpercentage)/100);

   if(totalunitamount <= 0) {
        myAlert('w','Total Amount should be greater than 0');
        $('#currenctclaimedamount_' + detailID).val(0);
        $('#currenctclaimed_' + detailID).val(0);
    }else if(claimedpercentage > currentprvpercentage)
    {
        myAlert('w','You cannot invoice more than 100%');
        $('#currenctclaimed_'+detailID).val(0);
        $('#currenctclaimedamount_' + detailID).val(0);
    }  else
    {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'detailID': invoiceDetailsAutoID,'amount':invoiceamount,'unitRateTransactionCurrency':unitRateTransactionCurrency,'claimedpercentage':claimedpercentage,'type':type,'totalunitamount':totalunitamount,'boqheaderID':headerID,'prevpersentage':prevpersentage,'invoiceamount_totalclaimed':invoiceamount_totalclaimed},
                url: '<?php echo site_url("Invoices/update_invoiceamount"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_details(2);

                    }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
    }



    
}
function update_claimedamount_percentage(Previousclaimed,totalunitamount,invoiceamount,detailID,transactionCurrencyDecimalPlaces,invoiceDetailsAutoID,unitRateTransactionCurrency,type,headerID)
    {
        var prevpersentage = $('#prevclaimedpercentage_'+detailID).val();
    var remainingamoint =  parseFloat($('#remainingamount_'+detailID).val().replace(/,/g, ''));
        if(totalunitamount <= 0) {
            myAlert('w','Total Amount should be greater than 0');
            $('#currenctclaimedamount_' + detailID).val(0);
            $('#currenctclaimed_' + detailID).val(0);
        }else if(invoiceamount > remainingamoint) {
            myAlert('w', 'You cannot invoice amount more than Remaining');
            $('#currenctclaimedamount_' + detailID).val(0);
            $('#currenctclaimed_' + detailID).val(0);
        }else
        {
            var percentage =  ((invoiceamount/totalunitamount)*100);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'detailID': invoiceDetailsAutoID,'amount':invoiceamount,'totalunitamount':totalunitamount,'type':type,'boqheaderID':headerID,'prevpersentage':prevpersentage},
                url: '<?php echo site_url("Invoices/update_invoiceamount"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_details(2);

                    }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }


    }
 function commaSeparateNumber(val) {
        while (/(\d+)(\d{3})/.test(val.toString())) {
            val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return val;
    }
function advancematchcreation(invoiceautoID,projectID)
  { 

             $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'invoiceautoID':invoiceautoID},
                        url: '<?php echo site_url("Invoices/chk_exist_advancematch"); ?>',
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 's') {
                                matchID = data[1];
                                Receipt_match_detail_model(matchID,projectID);
                          }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
  }


  function select_check_box_project(data, id, total) {
        $("#check_" + id).prop("checked", false)
        var balamount=$('#balamount_'+id).html();
        balamount = balamount.replace(/,/g, "");
        var invoicebalanceamount = parseFloat($('#grandtotal_amount').text().replace(/,/g, ''));
        if(invoicebalanceamount == 0)
        {
            myAlert('w', 'You cannot Invoice greater than Balance Amount');
            return false
        } else if (data.value >= 0) {

            if (total >= data.value) {

                $("#check_" + id).prop("checked", true);
                if((parseFloat(balamount)< data.value)&&(data.value!=0)){

                    $( "#check_"+id ).prop( "checked", false);
                    $( "#amount_"+id ).val('');
                    myAlert('w', 'Payment Matching Amount cannot be greater than Invoice Balance Amount');
                    total_calculation();/**/
                    return false
                }
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
                myAlert('w', 'Payment Matching Amount cannot be greater than Advance Balance Amount');
                total_calculation();
                return false
            }

            var amnt=0;
            if((parseFloat(balamount)<amnt) && data.value!=0){

                $( "#check_"+id ).prop( "checked", false);
                $( "#amount_"+id ).val('');
                myAlert('w', 'Payment Matching Amount cannot be greater than Invoice Balance Amount');/**/
                total_calculation();
                return false
            }
        }
    }






    function Receipt_match_detail_model(matchID,projectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'matchID': matchID,'projectID':projectID,'invoiceAutoID':invoiceAutoID},
            url: "<?php echo site_url('Receipt_voucher/fetch_rv_advance_detail_project'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#match_ad_table').empty();
                x = 1;

                if (jQuery.isEmptyObject(data['receipt'])) {
                    $('#match_ad_table').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {

                    $.each(data['receipt'], function (key, value) {
                        currency_decimal = currencydecimal;

                        var ad_detail = ' - ';
                        var paid_amount = 0;

                        // if (value['purchaseOrderID']!=0) {
                        //    ad_detail = value['POCode'];//+' '+value['PODescription'];
                        // }
                        if (value['paid'] != 'null') {
                            paid_amount = value['paid'];
                        }

                            $('#match_ad_table').append('<tr><td>' + x + '</td><td><input type="hidden" name="totalamountlaimed" id="totalamountlaimed" value='+data['totalamountretention']+'>' + value['RVcode'] + '</td><td>' + value['RVdate'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"  id="balamount_'+value['receiptVoucherDetailAutoID']+'">' + parseFloat((value['balanceamount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><input type="text" name="amount[]" value="'+paid_amount+'" style="width: 100px" id="amount_' + value['receiptVoucherDetailAutoID'] + '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="total_calculation();select_check_box_project(this,' + value['receiptVoucherDetailAutoID'] + ',' + (parseFloat(value['balanceamount'])).toFixed(value['decimalplaces']) + ')" class="number"></td><td class="text-right" style="display:none;"><input class="checkbox" id="check_' + value['receiptVoucherDetailAutoID'] + '" type="checkbox" value="' + value['receiptVoucherDetailAutoID'] + '"></td></tr>');


                        x++;
                    });
                }

                $("#total_invoice_amount").text(commaSeparateNumber((parseFloat(data['totalamountretention'])), currencydecimal));
                $("#total_advance_percentage_amount").text(commaSeparateNumber((parseFloat(data['total_advanceinvoiceamt'])), currencydecimal));
                $("#total_invoice_total").text(commaSeparateNumber((parseFloat(data['paidamount'])), currencydecimal));
                $("#grandtotal_amount").text(commaSeparateNumber(( (parseFloat((data['totalamountretention'])))-parseFloat((data['paidamount'])) ), currencydecimal));
                $("#receipt_match_model").modal({backdrop: "static"});
                number_validation();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }



      function save_match_items_project() {
        var selected = [];
        var amount = [];
        var invoiceamount = $('#totalamountlaimed').val();
        var totalinvoiceamount =  parseFloat($('#total_invoice_total').text().replace(/,/g, ''));
        $('#match_ad_table input:checked').each(function () {
            selected.push($(this).val());
            amount.push($('#amount_' + $(this).val()).val());
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'receiptVoucherDetailAutoID': selected,
                    'amounts': amount,
                    'invoiceAutoID': invoiceAutoID,
                    'matchID': matchID,
                    'invoiceamount': invoiceamount,
                    'totalinvoiceamount': totalinvoiceamount
                },
                url: "<?php echo site_url('Receipt_voucher/save_match_amount_project'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['messsage'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $('#receipt_match_model').modal('hide');
                        setTimeout(function () {
                            fetch_detail();
                        }, 300);
                    }
                }, error: function () {
                    $('#receipt_match_model').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function validateFloatKeyPress(el, evt) {
        currency_decimal =currencydecimal;
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
       function total_calculation()
      {
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        var tot_TotalCostoverhead = 0;
        $('.invoice_base tr').each(function () {
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(5).find('input').val());
            tot_TotalCostoverhead += tot_valueoverhead;
        });

        $("#total_invoice_total").text(commaSeparateNumber((parseFloat(tot_TotalCostoverhead)), 3));
        deduct_total_amount();
    }

  function deduct_total_amount()
  {
      var tot_TotalCost = parseFloat($('#total_invoice_total').text().replace(/,/g, ''));
      var amount = parseFloat($('#total_invoice_amount').text().replace(/,/g, ''));
      $("#grandtotal_amount").text(commaSeparateNumber((  parseFloat(amount) - parseFloat(tot_TotalCost)),currency_decimal));
  }
  function getNumberAndValidate(thisVal, dPlace=2) {
      thisVal = $.trim(thisVal);
      thisVal = parseFloat(thisVal.replace(/,/g, ""));
      thisVal = thisVal.toFixed(dPlace);

      if ($.isNumeric(thisVal)) {
          return parseFloat(thisVal);
      }
      else {
          return parseFloat(0);
      }
  }
  function delete_all_project_detail(invoiceAutoID) {


          swal({
                  title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                  text: "You want to delete all the records",/*You want to confirm this document*/
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Delete",/*Confirm*/
                  cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
              },
              function () {
                  $.ajax({
                      async: true,
                      type: 'post',
                      dataType: 'json',
                      data: {'invoiceAutoID': invoiceAutoID},
                      url: "<?php echo site_url('Invoices/delete_project_detail_invoice'); ?>",
                      beforeSend: function () {
                          startLoad();
                      },
                      success: function (data) {
                          //refreshNotifications(true);
                          myAlert(data[0], data[1]);
                          if(data[0]=='s'){
                              fetch_details(2);
                          }
                          setTimeout(function(){
                              stopLoad();
                          }, 500);
                      }, error: function () {
                          stopLoad();
                          swal("Cancelled", "Your file is safe :)", "error");
                      }
                  });
              });




  }



</script>