<?php
$this->load->helper('report');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Back to Back Revenue Details';
$date_format_policy = date_format_policy();
$customer="";
$type = $this->session->userdata("companyType");

$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
echo head_page($title, false);


$companyType = $this->session->userdata("companyType");
if($companyType == 1){
    $customer = all_customer_drop(false,1);
    $customer[-1] = ('') . 'POS Customers' . ('');
    $customer[-2] = ('') . 'Direct Receipt voucher' . ('');
}else{
    $customer = all_group_customer_drop(false);
    $customer[-1] = ('') . 'POS Customers' . ('');
    $customer[-2] = ('') . 'Direct Receipt voucher' . ('');
}

$supplier_arr = all_supplier_drop(false, 1);
?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_invoice" id="frm_rpt_customer_invoice" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_from'); ?><!--Date From--></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                    </div>
                </div>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date_to'); ?><!--Date To--></label>
                    <div class="input-group datepicto">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                    </div>
                </div>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                    <?php // echo form_dropdown('customerID[]', $customer, '', 'multiple  class="form-control" id="customerID" required'); ?>
                    <div id="div_load_customers">
                        <select name="customerID[]" class="form-control" id="filter_customerID" multiple="">
                            <?php
                            unset($customer[""]);
                            if (!empty($customer)) {
                                foreach ($customer as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }

                            ?>
                        </select>
                    </div>
                </div>

                <?php if($companyType == 1){ ?>
                <div class="form-group col-sm-2">
                    <label>Supplier<!--Supplier --> </label>
                    <?php echo form_dropdown('supplier[]',$supplier_arr , '', 'class="form-control" id="supplier" multiple="multiple"  '); ?>
                </div>
                <?php } ?>

                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_search'); ?><!--Search --></label>
                        <input type="text" id="search" name="search" class="form-control">
                </div>
                <?php if($companyType == 1){ ?>
                <div class="form-group col-sm-2 ">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
                </div>
                <?php }  ?>

                <div class="form-group col-sm-1 pull-right">
                    <label for=""></label>
                    <button style="margin-top: 30%" type="button" onclick="get_customer_invoice()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?><!--Generate --></button>
                </div>

            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>

<hr style="margin: 0px;">

<div id="div_customer_invoice">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var type;
    var urlPdf;
    var selectall = ''
    var selectdeselectall = ''
    $(document).ready(function (e) {
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
       
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('#filter_customerID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_customerID").multiselect2('selectAll', false);
        $("#filter_customerID").multiselect2('updateButtonText');

        $('#supplier').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#supplier").multiselect2('selectAll', false);
        $("#supplier").multiselect2('updateButtonText');
        
        $('.headerclose').click(function () {
            fetchPage('system/sales/erp_back_to_back_revenue_report', '', 'Back to Back Revenue')
        });

        // pdf link
        urlPdf = '<?php echo site_url('sales/get_back_to_back_revenue_report_pdf'); ?>';
       
        //main table
        get_customer_invoice();
    });

    function get_customer_invoice() {
        $.ajax({
            type: "POST",
            url : '<?php echo site_url('sales/get_back_to_back_revenue_report'); ?>',
            data: $("#frm_rpt_customer_invoice").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_customer_invoice").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_customer_invoice');
        form.target = '_blank';
        form.action = urlPdf;
        form.submit();
    }


</script>
