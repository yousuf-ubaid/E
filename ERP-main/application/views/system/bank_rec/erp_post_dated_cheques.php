<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_tr_lm_post_dated_cheques');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customer_arr =all_customer_drop(false,1);
$customer_arr["Others"] = "Others";

$supplier_arr =all_supplier_drop(false);
$supplier_arr["Others"] = "Others";

/*echo head_page('Post Dated Cheques',false); */?>

<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="m-b-md" id="wizardControl">
    <a id="tab1" class="btn btn-default" href="#step1" onclick="get_post_dated_cheques();" data-toggle="tab"><?php echo $this->lang->line('treasury_tr_lm_received_post_dated_cheques');?><!--Received Post Dated Cheques--></a>
    <a id="tab2" class="btn btn-default btn-wizard" href="#step2" onclick="get_post_dated_chequespayment();"  data-toggle="tab"><?php echo $this->lang->line('treasury_tr_lm_issued_post_dated_cheques');?><!--Issued Post Dated Cheques--></a>

</div><hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="received_PO_cheques" autocomplete="off"'); ?>
        <div class="row">
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_from');?><!--Date From--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="datefrom" class="datefrom-customer"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="datefrom" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('common_date_to');?><!--Date To--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto" class="dateto-customer"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="dateto" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" onchange="load_statusbased_customer()" '); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_customer');?><!--Customer--></label>
                <div class="input-group ">
                    <?php //echo form_dropdown('customerID[]', $customer_arr, '', 'class="form-control" id="customerID" multiple="multiple"'); ?>
                    <div id="div_load_customers">
                        <select name="customerID[]" class="form-control customerID" id="customerID" multiple="multiple">
                            <?php
                                if (!empty($customer_arr)) {
                                    foreach ($customer_arr as $key => $val) {
                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
           
            <div class="form-group col-sm-1">

                <label for="">&nbsp;</label>
                <div class="input-group ">
                <button type="button" style="" class="btn btn-primary "
                        onclick="clear_data(1)"
                        id="filtersubmit"><i
                            class="fa fa-paint-brush"></i>Clear
                </button><!--Generate-->
                </div>
            </div>
            <div class="form-group col-sm-1">

                <label for="">&nbsp;</label>
                <div class="input-group ">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="get_post_dated_cheques()" name="filtersubmit"
                        id="filtersubmit"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate'); ?>
                </button><!--Generate-->
                </div>
            </div>
        </div>
        </form>
        <div id="load_generated_tables"></div>
    </div>
    <div id="step2" class="tab-pane">
        <?php echo form_open('', 'role="form" id="issued_PO_cheques" autocomplete="off"'); ?>
        <div class="row">
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_from');?><!--Date From--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="datefrom" class="datefrom-sup"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="datefrom" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_date_to');?><!--Date To--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateto" class="dateto-sup"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="" id="dateto" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('common_supplier');?><!--Supplier--> </label>
                <div class="input-group ">
                    <?php echo form_dropdown('supplierAutoID[]', $supplier_arr, '', 'class="form-control" id="supplierAutoID" multiple="multiple"'); ?>

                </div>
            </div>
            <div class="form-group col-sm-1">
            </div>
            <div class="form-group col-sm-1">

                <label for="">&nbsp;</label>
                <button type="button" style="margin-top: 30%;" class="btn btn-primary pull-right"
                        onclick="clear_data(2)"
                        id="filtersubmit"><i
                            class="fa fa-paint-brush"></i>Clear
                </button><!--Generate-->

            </div>
            <div class="form-group col-sm-1">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-primary pull-right"
                        onclick="get_post_dated_chequespayment()" name="filtersubmit"
                        id="filtersubmit"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate'); ?>
                </button><!--Generate-->
            </div>
        </div>
        </form>
        <div id="load_generated_tables2"></div>
    </div>

    </div>


<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/erp_post_dated_cheques','','Postdated cheque');
        });
        get_post_dated_cheques();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('#customerID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#customerID").multiselect2('selectAll', false);
        $("#customerID").multiselect2('updateButtonText');

        $('#supplierAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#supplierAutoID").multiselect2('selectAll', false);
        $("#supplierAutoID").multiselect2('updateButtonText');

    });
    function clear_data(type)
    {

        if(type == 2)
        {
            $('.datefrom-sup').val("");
            $('.dateto-sup').val("");
            $("#supplierAutoID").multiselect2('selectAll', false);
            $("#supplierAutoID").multiselect2('updateButtonText');
            get_post_dated_chequespayment();
        }else
        {
            $('.datefrom-customer').val("");
            $('.dateto-customer').val("");
            $("#customerID").multiselect2('selectAll', false);
            $("#customerID").multiselect2('updateButtonText');
            get_post_dated_cheques()
        }

    }

    function get_post_dated_cheques() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: $("#received_PO_cheques").serialize(),
            url: "<?php echo site_url('Bank_rec/get_post_dated_cheques'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_generated_tables').html(data);
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function get_post_dated_chequespayment() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: $("#issued_PO_cheques").serialize(),
            url: "<?php echo site_url('Bank_rec/get_post_dated_cheques_payment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_generated_tables2').html(data);
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function load_statusbased_customer() {
        var status_filter = $('#status_filter').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {type:1,activeStatus:status_filter,document:'PostDatedCheques'},
            url: "<?php echo site_url('Report/load_statusbased_customer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_customers').html(data);
               
                $('#customerID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    maxHeight: '30px',
                    allSelectedText: 'All Selected'
                });
                $("#customerID").multiselect2('selectAll', false);
                $("#customerID").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>