<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);

$country = load_country_drop();
$gl_code_arr = supplier_group_gl_drop();
$currncy_arr = all_currency_master_drop();
$country_arr = array('' => 'Select Country');
$customerCategory = party_group_category(2);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('config_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('config_supplier_header');?><!--Supplier Header--></a>
    <!--<a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">Step 2 - Supplier Link</a>-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="suppliermaster_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('config_secondary_code');?><!--Secondary Code--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="suppliercode" name="suppliercode">
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="supplierName" name="supplierName" required>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierName"><?php echo $this->lang->line('config_name_on_cheque');?><!--Name On Cheque--> <?php required_mark(); ?></label>
                <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="liabilityAccount"><?php echo $this->lang->line('config_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('liabilityAccount', $gl_code_arr, "", 'class="form-control select2" id="liabilityAccount" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">VAT <?php echo $this->lang->line('config_identification_no');?><!--Identification No--> </label>
                <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierEmail" name="supplierEmail">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierFax"><?php echo $this->lang->line('common_fax');?><!--FAX--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('config_credit_period');?><!--Credit Period--></label>
                <div class="input-group">
                    <div class="input-group-addon"><?php echo $this->lang->line('common_month');?><!--Month--></div>
                    <input type="text" class="form-control number" id="supplierCreditPeriod"
                           name="supplierCreditPeriod">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('config_credit_limit');?><!--Credit Limit--></label>
                <div class="input-group">
                    <div class="input-group-addon"><span class="currency">LKR</span></div>
                    <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierUrl">URL</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                    <input type="text" class="form-control" id="supplierUrl" name="supplierUrl">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierAddress1"><?php echo $this->lang->line('common_address');?> 1</label>
                <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="supplierAddress2"><?php echo $this->lang->line('common_address');?> 2</label>
                <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" id="supplier_btn" type="submit"><?php echo $this->lang->line('config_common_add_save');?><!--Add Save--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="table-responsive">
            <table id="supplier_link_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width:3% ;">#</th>
                    <th style="min-width:8% ;"><?php echo $this->lang->line('config_customer_code');?><!--Customer Code--></th>
                    <th style="min-width:10%;"><?php echo $this->lang->line('config_customer_company');?><!--Customer Company--></th>
                    <th style="min-width:40%;"><?php echo $this->lang->line('config_customer_details');?><!--Customer Details--></th>
                    <th style="min-width:30%;"><?php echo $this->lang->line('config_gl_descriprion');?><!--GL Description--></th>
                    <th style="min-width:1% ;"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var groupSupplierAutoID;
    $(document).ready(function () {
        $('#supplier_btn').text('<?php echo $this->lang->line('config_common_add_supplier');?>');/*Add Supplier*/
        $('.headerclose').click(function () {
            fetchPage('system/GroupMaster/erp_supplier_group_master', '', 'Supplier Master');
        });
        $('.select2').select2();
        groupSupplierAutoID = null;
        number_validation();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            groupSupplierAutoID = p_id;
            laad_supplier_header();
        } else {

        }

        $('#suppliermaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                suppliercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_supplier_code_is_required');?>.'}}},/*Supplier Code is required*/
                supplierName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_supplier_name_is_required');?>.'}}},/*Supplier Name is required*/
                suppliercountry: {validators: {notEmpty: {message: 'Supplier Country is required.'}}},
                liabilityAccount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_liability_account_is_required');?>.'}}},/*Liability Account is required*/
                supplierCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_supplier_currency_is_required');?>.'}}}/*Supplier Currency  is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'groupSupplierAutoID', 'value': groupSupplierAutoID});
            data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('SupplierGroup/save_suppliermaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/GroupMaster/erp_supplier_group_master', 'Test', 'Supplier Master');
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
    });

    function laad_supplier_header() {
        if (groupSupplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupSupplierAutoID': groupSupplierAutoID},
                url: "<?php echo site_url('SupplierGroup/load_supplier_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#supplier_btn').text('<?php echo $this->lang->line('config_update_supplier');?>');/*Update Supplier*/
                        groupSupplierAutoID = data['groupSupplierAutoID'];
                        $("#supplierTelephone").val(data['supplierTelephone']);
                        $("#suppliercode").val(data['secondaryCode']);
                        $('#supplierName').val(data['groupSupplierName']);
                        $('#supplierFax').val(data['supplierFax']);
                        $('#liabilityAccount').val(data['liabilityAutoID']).change();
                        $("#assteGLCode").prop("disabled", true);
                        $('#nameOnCheque').val(data['nameOnCheque']);
                        $('#vatIdNo').val(data['vatIdNo']);
                        $('#supplierCurrency').val(data['supplierCurrencyID']).change();
                        $("#supplierCurrency").prop("disabled", true);
                        $('#suppliercountry').val(data['supplierCountry']).change();
                        //$('#suppliertaxgroup').val(data['taxGroupID']).change();
                        $('#supplierTelephone').val(data['supplierTelephone']);
                        $('#supplierEmail').val(data['supplierEmail']);
                        $('#supplierUrl').val(data['supplierUrl']);
                        $('#supplierCreditPeriod').val(data['supplierCreditPeriod']);
                        $('#supplierCreditLimit').val(data['supplierCreditLimit']);
                        $('#supplierAddress1').val(data['supplierAddress1']);
                        $('#supplierAddress2').val(data['supplierAddress2']);
                        $('#partyCategoryID').val(data['partyCategoryID']).change();
                        load_supplier_link_table();
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function set_currency(val) {
            $('.currency').html(val);
        }
    }

    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#supplierCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#supplierCurrency').val();
        //currency_validation_modal(CurrencyID, 'SUP', '', 'SUP');
    }


    function load_supplier_link_table() {
        Otable = $('#supplier_link_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('SupplierGroup/fetch_supplier_link'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "groupSupplierDetailID"},
                {"mData": "supplierSystemCode"},
                {"mData": "company_name"},
                {"mData": "supplier_detail"},
                {"mData": "GLDescription"},
                {"mData": "edit"},
                {"mData": "supplierName"},
                {"mData": "supplierAddress1"},
                {"mData": "supplierAddress2"},
                {"mData": "company_name"}
                /*{"mData": "Amount"}*/
            ],
            "columnDefs": [{"targets": [5], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [6, 7, 8, 9]
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "groupSupplierMasterID", "value": groupSupplierAutoID});
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

    function delete_supplier_link(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_common_you_want_to_delete_this_supplier');?>",/*You want to delete this supplier!*/
                type: "warning",
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
                    data: {'groupSupplierDetailID': id},
                    url: "<?php echo site_url('SupplierGroup/delete_supplier_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    }
</script>