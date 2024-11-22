<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_tr_ce_currency_exchange');
echo head_page($title, false);


/*echo head_page('Currency Exchange', false);*/

?>
<style>
    .sweet-alert h2 {

        font-size: 18px;
    }
    .sweet-alert p {

        font-size: 14px;
    }
    </style>

<div id="filter-panel" class="collapse filter-panel"></div>

<!--<div class="row">
    <div class="col-md-5">

    </div>
    </div>-->

<div class="row">
    <div class="col-md-5" id="masterTable">
        <table class="<?php echo table_class(); ?>" style="margin-top: 5px">
            <thead>
            <tr>
                <td><?php echo $this->lang->line('treasury_tr_ce_currency_name');?><!--Currency Name--></td>
                <td><?php echo $this->lang->line('treasury_tr_ce_currency_code');?><!--Currency Code--></td>
                <td><?php echo $this->lang->line('treasury_tr_ce_decimal_place');?><!--Decimal Place--></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>

    </div>
    <div class="col-md-6 col-md-offset-1" id="detailTable">


    </div>
</div>




<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/erp_currency_exchange','','Currency Exchange');
        });
        /*     bank_rec();*/
        assignedcurrency_company();


    });


    function assignedcurrency_company() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Bank_rec/get_assignedcurrency_company'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#masterTable').html(data)

            }, error: function () {

            }
        });
    }

    function update_exchange(currencyConversionAutoID, mastercurrencyassignAutoID, subcurrencyassignAutoID, conversion) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                currencyConversionAutoID: currencyConversionAutoID,
                mastercurrencyassignAutoID: mastercurrencyassignAutoID,
                subcurrencyassignAutoID: subcurrencyassignAutoID,
                conversion: conversion
            },
            url: "<?php echo site_url('Bank_rec/update_currencyexchange'); ?>",
            beforeSend: function () {
            },
            success: function (data) {


            }, error: function () {

            }
        });
    }

    function detailcurrency(mastercurrencyassignAutoID,status) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {mastercurrencyassignAutoID: mastercurrencyassignAutoID,status:status},
            url: "<?php echo site_url('Bank_rec/detail_assignedcurrency_company'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#detailTable').html(data)

            }, error: function () {

            }
        });
    }

    function insert_new_currencyexchange(mastercurrencyassignAutoID){
        $('#mastercurrencyassignAutoID').val(mastercurrencyassignAutoID);
        $('#AddNewcurrencyModal').modal({backdrop: "static"});
    }

    function opencurrencyModal() {

        $('#currencyModal').modal({backdrop: "static"});
    }

    function update_cross_exchange(mastercurrencyassignAutoID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_common_you_want_to_continue');?>",/*You want to continue!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'mastercurrencyassignAutoID': mastercurrencyassignAutoID},
                    url: "<?php echo site_url('Bank_rec/update_cross_exchange'); ?>",
                    beforeSend: function () {
                        /*                 startLoad();*/
                    },
                    success: function (data) {
                        /*    stopLoad();*/


                        if (data['validate']) {
                            setTimeout(function () {
                                swal({
                                    html: true,
                                    title: data['title'],
                                    text: data['validate'],

                                });
                            }, 300);
                            /*         refreshNotifications(true);*/
                        } else {
                            refreshNotifications(true);
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>