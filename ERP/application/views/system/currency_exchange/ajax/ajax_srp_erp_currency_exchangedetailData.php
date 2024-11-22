<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$CI =& get_instance();
$rpt=$CI->common_data['company_data']['company_reporting_currency'];
$status=0;
?>
<div class="btn-group pull-right">

<!--    <button style="margin-right: 5px" type="button" class="btn btn-primary btn-xs " onclick="insert_new_currencyexchange(<?php /*echo $mastercurrencyassignAutoID */?>)"> Add New
    </button>-->
    <?php
    if(isset($basecurrencyCode)){

    if(isset($rpt) && trim($basecurrencyCode)==trim($rpt)){
        $status=1
        ?>

    <button type="button" class="btn btn-primary btn-xs " onclick="update_cross_exchange(<?php echo $mastercurrencyassignAutoID ?>)"> <?php echo $this->lang->line('treasury_tr_ce_update_cross_exchange');?><!--Update Cross Exchange-->
    </button>
    <?php
}} ?>
    </div>
<i class="fa fa-filter"></i> <span style=""><?php echo (isset($basecurrencyCode) ? $basecurrencyCode:'')?> - <?php echo $this->lang->line('treasury_tr_ce_currency_conversion');?><!--Currency Conversion--></span>
<table class="<?php echo table_class();?>" style="margin-top: 5px">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('treasury_tr_ce_currency_name');?><!--Currency Name--></th>
        <th><?php echo $this->lang->line('treasury_tr_ce_base_currency');?><!--Base Currency--></th>
        <th><?php echo $this->lang->line('treasury_common_exchange_rate');?><!--Exchange Rate--></th>

    </tr>
    </thead>
    <tbody>
    <?php if($details){
        foreach($details as $value){
        ?>
    <tr>
        <td style="text-align: center"><?php echo $value['subCurrency'] ?></td>
        <td style="text-align: center">= <?php echo $value['baseCurrency'] ?></td>
        <td style="width: 200px; text-align: right">
            <?php if($status==1){
                ?>
                <input onchange="update_exchange(<?php echo $value['currencyConversionAutoID'] ?>,<?php echo $value['mastercurrencyassignAutoID'] ?>,<?php echo $value['subcurrencyassignAutoID'] ?>,this.value)" style="text-align: right" type="text" value="<?php echo $value['conversion'] ?>" class="conversion" name="conversion">
                <?php
            }else{
                echo $value['conversion'];
            }?>
        </td>
       <!-- <td><button class="btn btn-primary btn-xs" onclick="detailcurrency(<?php /*echo $value['currencyConversionAutoID'] */?>)">Set conversion</button></td>-->
    </tr>
    <?php }} ?>
    </tbody>
</table>






<script>
    $(".conversion").keydown(function (event) {
        if (event.shiftKey == true) {
            event.preventDefault();
        }
        if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110) {
        } else {
            event.preventDefault();
        }
        if($(this).val().indexOf('.') !== -1 && (event.keyCode == 190 || event.keyCode == 110))
            event.preventDefault();
    });
    </script>







