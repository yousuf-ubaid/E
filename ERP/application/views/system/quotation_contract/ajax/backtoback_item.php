<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_transaction', $primaryLanguage);
    $this->lang->load('accounts_receivable', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $group_based_tax =  is_null(getPolicyValues('GBT', 'All'))?0:getPolicyValues('GBT', 'All') ;
    $total_net = 0;
    $po_total = 0;
    $selling_total = 0;
    $transaction_currency = '('.$master['transactionCurrency'].')';
?>
    <style>
        .withApplyToAll{
            width:80%;border:1px solid #d2d6de;border-top: 0px !important;border-right: 0 !important;border-left: 0 !important;
        }
    </style>
    <div class="row">
        <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_document_item_detail');?> </h4><h4></h4></div><!--Item Detail-->
        <div class="col-md-4">
            <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right" id="itemAddBtn"><i
                    class="fa fa-plus"></i><?php echo $this->lang->line('sales_markating_transaction_document_add_item');?> <!--Add Item-->
            </button>
        </div>
    </div>
    <br>
    
    <table>
        <tbody>
        <td>#</td>
            <td>
                <input class="form-check-input" type="radio" name="rounding" value="1" id="rounding_up" <?php echo ($master['rounding'] == 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="rounding_up">
                    &nbsp &nbsp Rounding Upword
                </label>
            </td>
            <td>
                <input class="form-check-input" type="radio" name="rounding" value="2" id="rounding_down" <?php echo ($master['rounding'] == 2) ? 'checked' : '' ?>>
                <label class="form-check-label" for="rounding_down">
                    &nbsp &nbsp Rounding Downword
                </label>
            </td>
        </tbody>    
    
    </table>


    <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th colspan="4" class=""> <?php echo 'Client Order';?></th><!--Item Details-->
                <th colspan="2" class=""> <?php echo 'PO cost'?></th><!--Item Details-->
                <th colspan="2" class=""><?php echo 'Cost Allocation'?><!--Amount--> <span class="currency"><?php echo $transaction_currency ?></span></th>
                <th colspan="2" class=""><?php echo 'Selling Price'?><!--Amount--> <span class="currency"><?php echo $transaction_currency ?></span></th>
                <th class="" colspan="2"><?php echo 'Discount';?><!--Amount--> <span class="currency"><?php echo $transaction_currency ?></span></th>
                <th class="" colspan="2"><?php echo 'Commission';?><!--Amount--> <span class="currency"><?php echo $transaction_currency ?></span></th>
                <th class="" colspan="1"><?php echo '';?><!--Amount--> <span class="currency"></span></th>
                <th class="" colspan="1"><?php echo 'Retension';?><!--Amount--> <span class="currency"></span></th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%" class=""><?php echo $this->lang->line('common_code');;?></th>
                <th style="min-width: 15%" class=""><?php echo $this->lang->line('common_description');?></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Code-->
                <th style="min-width: 5%" class="text-left">P/Unit </th><!--Description-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_total');?> </th><!--UOM-->
                <th style="min-width: 5%"><?php echo 'Allocation';?> </th><!--Qty-->
                <th style="min-width: 5%"><?php echo 'Final Price';?> </th><!--Unit-->
                <th style="min-width: 5%"><?php echo 'P/Unit';?> </th><!--Discount-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_total');?> </th><!--Net Unit Price-->
                <th class="">%</th>
                <th class="">Value</th>
                <th class="">%</th>
                <th class="">Value</th>
                <th style="min-width: 8%" colspan="1"><?php echo 'Net Value'?> </th><!--Action-->
                <th style="min-width: 8%" colspan="1"><?php echo 'Retension '.$master['retentionPercentage'].'%' ?> </th><!--Action-->
                <th></th>
            </tr>
            </thead>
            <tbody id="table_body_btb">

                <?php if(count($itemDetails) > 0){ ?>
                        <?php foreach($itemDetails as $item){ 
                                $total_net += $item['transactionAmount'];
                                $po_total += $item['poUnitPrice'];
                                if($master['rounding'] == 1){
                                    $item['unittransactionAmount'] = ceil($item['unittransactionAmount']);
                                }else{
                                    $item['unittransactionAmount'] = floor($item['unittransactionAmount']);
                                }

                                $selling_total += ($item['unittransactionAmount'] * $item['requestedQty']);//(($item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']);
                            ?>
                            <tr class="lineitem">
                                <td>
                                    <input type="hidden" name="id" id="id" class="id" value="<?php echo $item['contractDetailsAutoID'] ?>"/>
                                    <input type="hidden" name="contractAutoID" id="contractAutoID" class="contractAutoID" value="<?php echo $item['contractAutoID'] ?>"/>

                                </td>
                                <td><?php echo $item['itemSystemCode'] ?></td>
                                <td><?php echo $item['itemDescription'] ?></td>
                                <td class="text-right"><?php echo $item['requestedQty'] ?></td>
                                <td class="text-right"><?php echo format_number($item['unitAmount'],2) ?></td>
                                <td class="text-right"><?php echo format_number($item['poUnitPrice'],2) ?></td>
                                <td class="text-right"><?php echo format_number(($item['poUnitPrice'] * $item['ap_amount']),2) ?></td>
                                <td class="text-right"><?php echo format_number(($item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice'],2) ?></td>
                               
                                <!-- (($item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']) / $item['requestedQty'] -->
                                <?php if($master['rounding'] == 1) { ?>
                                    <td class="text-right"><?php echo format_number($item['unittransactionAmount'],2)  ?></td>
                                    <td class="text-right"><?php echo format_number(ceil($item['unittransactionAmount'] * $item['requestedQty']),2) ?></td>
                                <?php }else { ?>
                                    <td class="text-right"><?php echo format_number($item['unittransactionAmount'],2)  ?></td>
                                    <td class="text-right"><?php echo format_number(floor($item['unittransactionAmount'] * $item['requestedQty']),2) ?></td>
                                <?php } ?>
                                <!-- (($item['poUnitPrice'] * $item['ap_amount']) + $item['poUnitPrice']) -->
                                <td class="text-right"><input type="number" onchange="update_value(this,'discountPercentage')" class="form-control discountPercentage text-right" value="<?php echo $item['discountPercentage'] ?>" /> <a class="btn btn-default btn-sm" onclick="apply_to_all_discount(this)"><i class="fa fa-arrow-down"></i></a></td>
                                <td class="text-right"><?php echo format_number($item['discountAmount'],2) ?>  </td>
                                <td><input type="number" class="withApplyToAll commissionPercentage text-right" onchange="update_value(this,'commissionPercentage')" value="<?php echo $item['commissionPercentage'] ?>" /> <a class="btn btn-default btn-sm" onclick="apply_to_all_commission(this)"><i class="fa fa-arrow-down"></i></a></td>
                                <td class="text-right"><?php echo format_number($item['commissionValue'],2) ?></td>
                                <td class="text-right hide"><input type="number" class="form-control" onchange="update_value(this,'taxPercentage')" class="taxPercentage" value="<?php echo $item['taxPercentage'] ?>" /></td>
                                <td class="text-right hide"><?php echo format_number($item['taxAmount'],2) ?></td>
                                <td class="text-right"><?php echo format_number($item['transactionAmount'],2) ?></td>
                                <td class="text-right"><?php echo format_number($item['retensionValue'],2); ?></td>
                                <td class="text-right"> <a onclick="edit_item(<?php echo $item['contractDetailsAutoID'] ?>);"><span class="glyphicon glyphicon-pencil"></span></a> <a onclick="delete_item('<?php echo $item['contractDetailsAutoID'] ?>');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>
                            </tr>

                        <?php } ?>

                        <tr>
                            <td colspan="5" class="text-right">PO Total <span class="currency"><?php echo $transaction_currency ?></span></td>
                            <td class="text-right total"> <?php echo format_number($po_total,2) ?></td>
                            <td colspan="3" class="text-right">Selling Total <span class="currency"><?php echo $transaction_currency ?></span></td>
                            <td class="text-right total"> <?php echo format_number($selling_total,2) ?></td>
                            <td class="text-right" colspan="4"> Net Total <span class="currency"><?php echo $transaction_currency ?></span></td>
                            <td class="text-right total"> <?php echo format_number($total_net,2) ?> </td>
                        </tr>
                <?php }else{ ?>
                    <tr class="danger"> 
                        <td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                    </tr>
                <?php } ?>

            
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <br>

        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_conformation();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>



    <script>

        function update_value(ev,column){

            var value = $(ev).val();
            var contractAutoID = $(ev).closest('tr').find('.contractAutoID').val();
            var id = $(ev).closest('tr').find('.id').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID,'changed_value':value,'column':column,'id':id},
                url: "<?php echo site_url('Quotation_contract/update_contract_detail_value'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if (data[0] == 's') {
                        fn_backtoback_view(contractAutoID);
                    }
                
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }

            });


    }

    function apply_to_all_commission(ev){
        var percentage = $(ev).closest('tr').find('.commissionPercentage').val();
        var row_id = $(ev).closest('tr').find('td:eq(0)').text();

        swal({
                title: "Are you sure?",
                text: "You want to apply to all records below",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                $('.lineitem').each(function(i,row) {
                    $(row).closest('tr').find('.commissionPercentage').val(percentage).change();
                });
            });

     }

     function apply_to_all_discount(ev){
        var percentage = $(ev).closest('tr').find('.discountPercentage').val();
        var row_id = $(ev).closest('tr').find('td:eq(0)').text();

        swal({
            title: "Are you sure?",
            text: "You want to apply to all records below",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm"
        },
        function () {
            $('.lineitem').each(function(i,row) {
                $(row).closest('tr').find('.discountPercentage').val(percentage).change();
            });
        });

    }


     $('input[type=radio][name=rounding]').change(function() {

            var rounding = 1;
            if (this.value == '2') {
                rounding = 2;
            }

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID,'rounding':rounding},
                url: "<?php echo site_url('Quotation_contract/update_rounding_value'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    change_ap_amount();
                
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }

            });
    });

    </script>