<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$type_arr = array('' => $this->lang->line('common_select_type'), 'Standard' => $this->lang->line('common_standard'));
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$sold_arr = sold_to();
$ship_arr = ship_to();
$invoice_arr = invoice_to();
$umo_arr = array('' =>  $this->lang->line('common_select_uom'));
$segment_arr = fetch_segment();
$segment_arr_detail = fetch_segment(true);
$transaction_total = 100;
$claim_arr = fetch_claim_category();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$salary_categories_arr = salary_categories(array('A', 'D'));
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>

<form role="form" id="formula_taxcalculationmaster_form" class="form-group">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="segment"><?php echo $this->lang->line('tax_type'); ?></label>
                <select name="taxType" class="form-control" id="formulataxType" data-bv-field="taxType">
                    <option value="" selected="selected"><?php echo $this->lang->line('common_select'); ?></option>
                    <option value="1"><?php echo $this->lang->line('tax_sales_tax'); ?></option>
                    <option value="2"><?php echo $this->lang->line('tax_purchase_tax'); ?></option>
                </select>
            </div>

            <div class="col-sm-3">
                <div class="form-group ">
                    <label for="shippingAddressDescription">VAT Type</label>
                    <?php echo form_dropdown('vatType', vat_type_dropdown(), '', 'class="form-control select2 vatTypeID" id="vatType" required disabled'); ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group ">
                    <label for="shippingAddressDescription"> <?php echo $this->lang->line('common_description'); ?> </label>
                    <textarea class="form-control" rows="2" id="formulaDescription" name="Description"></textarea>
                </div>
            </div>
           <!--  <div class="col-md-3">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('tax_is_claimable'); ?></label>
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="isClaimable" type="checkbox" data-caption="" class="columnSelected" name="isClaimable" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div> -->
            <br>
            <button type="submit" id="dsablbtn" class="btn btn-primary-new size-sm"><?php echo $this->lang->line('common_update'); ?></button>
        </div>
</form>

<hr>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="openFormulaModel()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?>
        </button>
    </div>
</div>
<br>
<div class="table-responsive">
    <table id="tax_formula_details_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_tax'); ?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_description'); ?></th>
            <th style="min-width: 10%">Tax Percentage</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_sort_order'); ?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('tax_formula'); ?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="tax_formula_detail_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="formulaHead"></h3>
            </div>
            <form role="form" id="tax_formula_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="formulaDetailID" name="formulaDetailID">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('tax_type'); ?></label>
                            <div class="col-sm-6">
                                <?php
                                $companyID=current_companyID();
                                $taxCalculationformulaID=$this->input->post('page_id');
                                $taxType = $this->db->query("SELECT taxType,isClaimable FROM srp_erp_taxcalculationformulamaster WHERE taxCalculationformulaID='$taxCalculationformulaID'  AND companyID = $companyID; ")->row_array();
                                $taxTypeid=$taxType['taxType'];
                                $isClaimable=$taxType['isClaimable'];
                                $taxdrop = $this->db->query("SELECT taxMasterAutoID,taxDescription,taxShortCode FROM srp_erp_taxmaster WHERE taxType IN ($taxTypeid,0) AND companyID = $companyID AND isActive = 1")->result_array();
                                ?>
                                <select name="taxMasterAutoID" class="form-control" id="taxMasterAutoID">
                                    <option value="" selected="selected"><?php echo $this->lang->line('common_select'); ?></option>
                                    <?php
                                    foreach($taxdrop as $val){
                                        ?>
                                        <option value="<?php echo $val['taxMasterAutoID'] ?>"><?php echo $val['taxDescription'] ?></option>
                                    <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description'); ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="description" name="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_sort_order'); ?></label>
                            <div class="col-sm-6">
                                <input type="text" name="sortOrder" id="sortOrder" onkeypress="return validateFloatKeyPress(this,event)" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Tax Percentage</label>
                            <div class="col-sm-6">
                            <input type="text" name="taxpercentage" id="taxpercentage" onkeypress="return validateFloatKeyPress(this,event)" class="form-control" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$items = [

    'taxCalculationformulaID' => $this->input->post('page_id')
];
$data['items'] = $items;
$data['template_name'] = '1';
$this->load->view('system/tax/tax_formula-modal-view', $data);

?>
<script type="text/javascript">
    var urlSave = '<?php echo site_url('TaxCalculationGroup/saveFormula_tax') ?>';
    var isPaySheetGroup = 0;
    var taxCalculationformulaID;
    var fromTax = true;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/tax/tax_calculation_group', '', '<?php echo $this->lang->line('tax_formula_group'); ?>');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            taxCalculationformulaID=p_id;
            open_calculation_group_edit(taxCalculationformulaID);
            formula_detail_group_table();
        } else {

        }

        $('#formula_taxcalculationmaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                /*taxType: {validators: {notEmpty: {message: 'Tax Type is required.'}}},
                Description: {validators: {notEmpty: {message: 'Description is required.'}}}*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('#formulataxType').attr('disabled',false);
            $('#vatType').attr('disabled',false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'taxCalculationformulaID', 'value': taxCalculationformulaID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('TaxCalculationGroup/save_tax_calculation_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data[0],data[1]);
                    if(data[0]=='s'){
                        formula_detail_group_table();
                        $('#dsablbtn').attr('disabled',false);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });


        $('#tax_formula_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                taxMasterAutoID: {validators: {notEmpty: {message: "<?php echo $this->lang->line('tax_type_is_required'); ?>"}}},
                description: {validators: {notEmpty: {message: "<?php echo $this->lang->line('common_description_is_required'); ?>" }}},
                sortOrder: {validators: {notEmpty: {message: "<?php echo $this->lang->line('tax_sort_order_is_required'); ?>"}}},
                taxpercentage: {validators: {notEmpty: {message: "Tax percentage is required"}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'taxCalculationformulaID', 'value': taxCalculationformulaID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('TaxCalculationGroup/save_tax_formula_detail_form'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data[0],data[1]);
                    if(data[0]=='s'){
                        formula_detail_group_table();
                        $("#tax_formula_detail_model").modal("hide");
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });


        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function open_calculation_group_edit(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxCalculationformulaID': id},
            url: "<?php echo site_url('TaxCalculationGroup/load_calculation_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
                $('#formulataxType').val(data['taxType']);
                $('#vatType').val(data['vatTypeID']).change();
                $('#formulaDescription').val(data['Description']);
                if (data['isClaimable'] == 1) {
                    $('#isClaimable').iCheck('check');
                } else {
                    $('#isClaimable').iCheck('uncheck');
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function formula_detail_group_table(){
        Otable = $('#tax_formula_details_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('TaxCalculationGroup/fetch_formula_detail'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    $('#formulataxType').attr('disabled',true);
                    $('#vatType').attr('disabled',true);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "formulaDetailID"},
                {"mData": "type_detail"},
                {"mData": "description"},
                {"mData": "taxPercentage"},
                {"mData": "sortOrder"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "taxCalculationformulaID","value": taxCalculationformulaID});
                /*aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
                 aoData.push({"name": "currency", "value": $("#currency").val()});*/
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function openFormulaModel(){
        $('#formulaDetailID').val('');
        $('#tax_formula_detail_form')[0].reset();
        $('#tax_formula_detail_form').bootstrapValidator('resetForm', true);
        $('#formulaHead').html("<?php echo $this->lang->line('tax_create_formula_detail'); ?>");
        $("#tax_formula_detail_model").modal({backdrop: "static"});
    }

    function open_formula_detail_edit(formulaDetailID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'formulaDetailID': formulaDetailID},
            url: "<?php echo site_url('TaxCalculationGroup/load_formula_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#formulaDetailID').val(data['formulaDetailID']);
                $('#taxMasterAutoID').val(data['taxMasterAutoID']);
                $('#description').val(data['description']);
                $('#sortOrder').val(data['sortOrder']);
                $('#taxpercentage').val(data['taxPercentage']);
                $("#tax_formula_detail_model").modal({backdrop: "static"});
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function validateFloatKeyPress(el, evt) {
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
    function delete_tax_calculation(formulaDetailID){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'formulaDetailID':formulaDetailID},
                    url :"<?php echo site_url('TaxCalculationGroup/delete_taxFormula'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if(data[0]=='s'){
                            formula_detail_group_table();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }



</script>