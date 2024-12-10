<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_transaction', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);

    $total_value_sum_1 = 0;
    $total_value_sum_2 = 0;
    $total_margin = 0;
    $total_markup = 0;
    $total_commision = 0;
    $transaction_currency = '('.$master['transactionCurrency'].')';
?>


<table class="table table-bordered table-striped table-condesed">
    <thead>
    <tr>
        <th colspan="3" class=""> <?php echo $this->lang->line('sales_markating_transaction_cost_details');?></th><!--Item Details-->
        <th colspan="2" class=""> <?php echo $this->lang->line('sales_markating_transaction_tax');?></th><!--Item Details-->
        <th class="hide" colspan="2"><?php echo $this->lang->line('sales_markating_transaction_commision');?><!--Amount--> <span class="currency"><?php echo $transaction_currency  ?></span></th>
        <th class="hide" colspan="1"><?php echo $this->lang->line('sales_markating_transaction_top_margin');?><!--Amount--> <span class="currency"><?php echo $transaction_currency ?></span></th>
        <th>&nbsp;</th>
    </tr>
    <tr>
        <th style="min-width: 5%">#</th>
        <th style="min-width: 15%">Cost Sheet</th>
        <th colspan="1" class="text-center" style="min-width: 10%"><?php echo $this->lang->line('common_value');?></th><!--Code-->
        <th  class="text-center" style="min-width: 5%">%</th><!--Description-->
        <th  class="text-center" style="min-width: 10%"><?php echo $this->lang->line('common_value');?> </th><!--UOM-->
        <th  class="text-center hide" style="min-width: 5%" class="text-left">%</th><!--Description-->
        <th  class="text-center hide" style="min-width: 10%"><?php echo $this->lang->line('common_value');?> </th><!--UOM-->
        <th class="text-center"  style="min-width: 10%"><?php echo 'Total '.$this->lang->line('common_value');?></th><!--Discount-->
        <th  class="text-center hide" style="min-width: 10%"></th><!--Discount-->
    </tr>
    </thead>
    <tbody id="table_body2">
        <?php foreach($charge_data as $data) { 
            $total_value_sum_1 += $data['extraCostValue'];
            $total_margin += $data['top_margin_value'];
            $total_commision += $data['commission_value'];
            $total_markup += $data['markup_value'];
            ?>
            <tr class="danger lineitem">
                <td>#
                    <input type="hidden" name="id" id="id" class="id" value="<?php echo $data['id'] ?>"/>
                    <input type="hidden" name="contractAutoID" id="contractAutoID" class="contractAutoID" value="<?php echo $data['purchaseOrderID'] ?>"/>
                    <input type="hidden" name="extraCostID" id="extraCostID" class="extraCostID" value="<?php echo $data['extraCostID'] ?>"/>
                </td>
                <td><?php echo $data['extraCostName'] ?></td>
                <td><input  type="number" class="form-control chargeValue text-right" onchange="update_values(this,'extraCostValue')" value="<?php echo $data['extraCostValue'] ?>" /></td>
                
                <td><input  type="number" class="form-control chargeValue text-right" onchange="update_values(this,'tax_percentage')" value="<?php echo $data['tax_percentage'] ?>" /></td>
                <td><span class="pull-right"><?php echo format_number($data['tax_value'],2) ?></span></td>

                 <!-- <td class="hide"><input  type="number" class="form-control commissionPercentage text-right" onchange="update_values(this,'commission_percentage')" value="<?php echo $data['commission_percentage'] ?>" /></td>
                <td class="hide"><span class="pull-right"><?php // echo format_number($data['commission_value'],2) ?></span></td> -->

                <td><span class="pull-right"><?php echo format_number($data['top_margin_value'],2) ?></span></td> 
                <td>
                    <button class="btn btn-danger" onclick="delete_extra_charge(<?php echo $data['id'] ?>)"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        <?php } ?>
        
        <?php if(count($charge_data) > 0) { ?>
        <tr class="hide">
            <td></td>
            <td>Total Cost</td>
            <td class="pull-right"><?php echo format_number($total_value_sum_1,2) ?></td>
        </tr>
        <tr class="hide">
            <?php foreach($total_data as $data){ 
                    $total_value_sum_2 += $data['extraCostValue'];
                    $total_margin += $data['top_margin_value'];
                    $total_commision += $data['commission_value'];
                    $total_markup += $data['markup_value'];
                ?>
                
                    <td>#
                        <input type="hidden" name="id" id="id" class="id" value="<?php echo $data['id'] ?>"/>
                        <input type="hidden" name="contractAutoID" id="contractAutoID" class="contractAutoID" value="<?php echo $data['purchaseOrderID'] ?>"/>
                        <input type="hidden" name="extraCostID" id="extraCostID" class="extraCostID" value="<?php echo $data['extraCostID'] ?>"/>
                    </td>
                    <td><?php echo $data['extraCostName'] ?></td>
                    <td class="pull-right"><?php echo format_number($data['extraCostValue'],2) ?></td>
                    <td><input  type="number" class="form-control markupPercentage text-right" onchange="update_values(this,'markup_percentage')" value="<?php echo $data['markup_percentage'] ?>" /></td>
                    <td><span class="pull-right"><?php echo format_number($data['markup_value'],2) ?></span></td>
                    <td class="hide"><input  type="number" class="form-control commissionPercentage text-right" value="<?php echo $data['commission_percentage'] ?>" /></td>
                    <td class="hide"><span class="pull-right"><?php echo format_number($data['commission_value'],2) ?></span></td>
                    <td><span class="pull-right"><?php echo format_number($data['top_margin_value'],2) ?></span></td>
                    <td>
                        
                    </td>
                
            <?php } ?>
        </tr>
        <tr class="hide">
            <td></td>
            <td>Total Cost</td>
            <td class="text-right"><?php echo format_number(($total_value_sum_1 +  $total_value_sum_2),2) ?></td>
        </tr>
        <tr class="hide">
            <td></td>
            <td>Total Margin</td>
            <td class="text-right"><?php echo format_number($total_margin,2) ?></td>
            <td></td>
            <td class="text-right"><?php echo format_number($total_markup,2) ?></td>
            
            <td class="text-right"><?php echo format_number($total_margin,2) ?></td>

            <!-- <td class="hide"><?php //echo format_number($total_commision,2) ?></td> -->
        </tr>
        <tr class="hide">
            <td></td>
            <td>For Allocation</td>
            <td class="text-right"><?php echo format_number(($total_value_sum_1 + $total_margin),2) ?></td>
        </tr>
        <tr class="hide">
            <td></td>
            <td> Apportionment P/Unit ( PO Value )</td>
            <?php if($total_value_sum_2 > 0){ ?>
                <td class="text-right" style=""><?php echo format_number((($total_value_sum_1 + $total_margin)/$total_value_sum_2),3) ?>
                    <input type="hidden" id="ap_unit" name="ap_unit" value ="<?php echo format_number((($total_value_sum_1 + $total_margin)/$total_value_sum_2),3) ?>" />
                </td>
            <?php } ?>
            
        </tr>
        <?php } else { ?>
            <tr>
                <<td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
            </tr>
        <?php } ?>

    </tbody>
    <tfoot id="table_tfoot">
        
    </tfoot>
</table>

<script>

    change_ap_amount();

    function update_values(ev,column){

        var contractAutoID = $(ev).closest('tr').find('.contractAutoID').val();
        var extraCostID = $(ev).closest('tr').find('.extraCostID').val();
        var id = $(ev).closest('tr').find('.id').val();
        var changed_value = $(ev).val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID,'extraChargeID':extraCostID,'column':column,'changed_value':changed_value,'id':id},
            url: "<?php echo site_url('Procurement/update_contract_extra_charge'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if (data[0] == 's') {
                    fetch_cost_addon_po();
                }
              
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
                stopLoad();
            }

        });


    }

    function change_ap_amount(){
       var ap_unit = $('#ap_unit').val();

       $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID,'ap_unit':ap_unit},
            url: "<?php echo site_url('Quotation_contract/update_ap_value'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
              
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }

        });

    }

    function delete_extra_charge(id){

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id': id,'type':'PO'},
                url: "<?php echo site_url('Quotation_contract/delete_extra_charge_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if (data[0] == 's') {
                        fetch_cost_addon_po();
                    }
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });


    }

    function apply_to_all_commission(ev){
        var percentage = $(ev).closest('tr').find('.markupPercentage').val();
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
                    $(row).closest('tr').find('.markupPercentage').val(percentage).change();
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

    function item_extra_cost_modal(){

        $("#addcontractextracost").modal({backdrop: "static"});
    }


</script>