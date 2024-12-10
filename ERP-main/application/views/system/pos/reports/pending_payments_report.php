<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


$title = '<i class="fa fa-bar-chart"></i> Pending Payments Report';
$locations = load_pos_location_drop();
$outlets = get_active_outletInfo_with_status();
//$outlets = get_active_outletInfo();
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
$customers=get_all_pos_customers(false,true,false);
echo head_page($title, false);
?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<form id="pendngPaymnt_rpt_form" method="post" class="form-inline" role="form">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <div class="row">
        <div class="col-sm-12">

            <div class="form-group col-sm-2">
                <label class="" for="">Outlets</label>
                </br>
                <select class="form-control input-sm" name="outlet[]" id="outletFranchise"
                        multiple>
                    <?php
                    foreach ($outlets as $outlet) {
                        echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . trim($outlet['wareHouseDescription'] ?? '') . '- ' . $outlet['isActive'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label for="">Customer </label>
                <?php echo form_dropdown('customer[]', get_all_pos_customers(true, true), '', 'class="form-control select2 input-sm" id="customer"  multiple="" style="z-index: 0;"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label class="" for="">Date From</label>
                </br>
                <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                       name="filterFrom" id="filterFrom_fr" value="<?php echo date('01/m/Y') ?>"
                       style="">
            </div>

            <div class="form-group col-sm-2">
                <label class="" for="">Date To</label>
                </br>
                <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                       name="filterto" id="filterto_to" value="<?php echo date('d/m/Y') ?>"
                       style="">
            </div>

            <div class="form-group col-sm-3">
                <label class="" style="color: white;">btn</label>
                </br>
                <button type="button" onclick="load_pendingPaymentsReport()" class="btn btn-primary btn-sm">
                    <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
            </div>
        </div>
    </div>


    <!--<div class="form-group">

        <label class="" for="">Customer</label>
        <?php /*echo form_dropdown('customer[]', $customers, '', 'multiple id="customer"  class="form-control input-sm"'); */?>
    </div>-->







</form>
<hr>
<div id="pos_modalBody_pospending_payments">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="pendingPaymentsDDModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Receipt Vouchers</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Receipt Voucher Code</td>
                            <td>Date</td>
                            <td>Amount</td>
                        </tr>
                    </thead>
                    <tbody id="receiptDDbody">
                        <tr>
                            <td colspan="4">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#filterFrom_fr,#filterTo2_fr").datepicker({
            format: 'dd/mm/yyyy'
        });
        $("#filterto_to,#filterTo2_fr").datepicker({
            format: 'dd/mm/yyyy'
        });


        $('.select2').select2();
        $("#outletFranchise").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#outletFranchise").multiselect2('selectAll', false);
        $("#outletFranchise").multiselect2('updateButtonText');

        /*$('#customer').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customer").multiselect2('selectAll', false);
        $("#customer").multiselect2('updateButtonText');*/
        load_pendingPaymentsReport();
    });

    function load_pendingPaymentsReport() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPendingPaymentsReport'); ?>",
            data: $("#pendngPaymnt_rpt_form").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_franchise").modal('show');
                $("#title_franchise").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                startLoadPos();
                $("#pos_modalBody_pospending_payments").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_pospending_payments").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



   function openPendingPaymentDD(invoiceID){
       $.ajax({
           type: 'POST',
           dataType: 'json',
           url: "<?php echo site_url('Pos_restaurant/loadPendingPaymentDD'); ?>",
           data: {'invoiceID': invoiceID},
           cache: false,
           beforeSend: function () {
               startLoad();
           },
           success: function (data) {
               stopLoad();
               $('#receiptDDbody').empty('');
               if (jQuery.isEmptyObject(data)) {
                   $('#receiptDDbody').append('<tr class="danger"><td colspan="4" class="text-center"><b>No Records Found</b></td></tr>');
               } else {
                   x = 1;
                   $.each(data, function (key, value) {
                       $('#receiptDDbody').append('<tr><td>' + x + '</td><td><a style="cursor: pointer" onclick="documentPageView_modal(\'RV\','+value['receiptVoucherAutoId']+')">' + value['RVcode'] + '</a></td><td>' + value['RVdate'] + '</td><td >' + value['receiptamnt'] + '</td></tr>');
                       x++;
                   });
               }
               $("#pendingPaymentsDDModel").modal('show');
           },
           error: function (jqXHR, textStatus, errorThrown) {
               stopLoad();
               myAlert('e', '<br>Message: ' + errorThrown);
           }
       });
   }


</script>