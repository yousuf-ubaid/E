<div class="btn-group pull-right">
    <button type="button" class="btn btn-primary btn-xs " onclick="insert_new_currencyexchange(<?php echo $mastercurrencyassignAutoID ?>)"> Add New
    </button>
    <!-- <button type="button" class="btn btn-primary btn-xs " onclick="update_cross_exchange(<?php //echo $mastercurrencyassignAutoID ?>)"> Update Cross Exchange
    </button> -->
    </div>
<i class="fa fa-filter"></i> <span style=""><?php echo (isset($details[0]['baseCurrency']) ? $details[0]['baseCurrency']:'')?> - Currency Conversion</span><hr>
<table class="table table-striped table-condensed table-bordered" id="set_currency_table">
    <thead>
    <tr>
        <th>#</th>
        <th>Currency Name</th>
        <th>Base Currency</th>
        <th>Exchange Rate</th>
    </tr>
    </thead>
    <tbody>
    <?php if($details){ $x=1;
        foreach($details as $value){
        ?>
    <tr>
        <td><?php echo $x; ?></td>
        <td style="text-align: center"><?php echo $value['subCurrency'] ?></td>
        <td style="text-align: center">= <?php echo $value['baseCurrency'] ?></td>
        <td style="width: 150px;"><input onchange="update_exchange(<?php echo $value['currencyConversionAutoID'] ?>,<?php echo $value['mastercurrencyassignAutoID'] ?>,<?php echo $value['subcurrencyassignAutoID'] ?>,this.value)" style="text-align: right" type="text" value="<?php echo $value['conversion'] ?>" class="conversion" name="conversion"> </td>
    </tr>
    <?php $x++; }} ?>
    </tbody>
</table>
<div class="modal fade" id="AddNewcurrencyModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New <span id=""></span></h4></div>
            <?php echo form_open('', 'role="form" id="currency_exchange_newCurrency"'); ?>
            <input type="hidden" id="mastercurrencyassignAutoID" name="mastercurrencyassignAutoID">
            <div class="modal-body" id="">
                <div class="row">
                    <div class="form-group col-sm-12 "><label class="col-sm-3" for="">Currency </label> <span id="clearform"><?php echo form_dropdown('currency', $currency_arr, '', 'class="form-control" style="width:300px" id="currency" required"'); ?></span>

                    </div>
                    <div class="form-group col-sm-12 "><label class="col-sm-3" for="">Exchange Rate </label> <input style="width: 300px;text-align: right" type="text" class="form-control" name="conversion" id="conversion"  >

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnnewSave">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
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
    $('#set_currency_table').DataTable();

    function update_exchange(currencyConversionAutoID, mastercurrencyassignAutoID, subcurrencyassignAutoID, conversion) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'currencyConversionAutoID' : currencyConversionAutoID,'mastercurrencyassignAutoID' : mastercurrencyassignAutoID,'subcurrencyassignAutoID': subcurrencyassignAutoID,'conversion': conversion,companyid:companyid
            },
            url: "<?php echo site_url('Dashboard/update_currencyexchange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function insert_new_currencyexchange(mastercurrencyassignAutoID){
        $('#mastercurrencyassignAutoID').val(mastercurrencyassignAutoID);
        $('#AddNewcurrencyModal').modal({backdrop: "static"});
    }

    $('#currency_exchange_newCurrency').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            currency             : {validators : {notEmpty:{message:'Currency is required.'}}},
            conversion           : {validators : {notEmpty:{message:'Exchange Rate is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'companyid', 'value': companyid});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Dashboard/save_addNewcurrencyExchange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                setTimeout(function() {
                    set_conversion($('#mastercurrencyassignAutoID').val())
                }, 300);
                stopLoad();
                refreshNotifications(true);
                $form.bootstrapValidator('resetForm', true);
                $('#conversion').val('');
                $('#AddNewcurrencyModal').modal('hide');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
</script>