<?php echo head_page($_POST['page_name'], false);
$this->load->helper('inventory_helper');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currency'];
$currncy_arr    = all_currency_new_drop(true);
$sub_category_arr = all_sub_category_drop();
$department_array=fetch_employee_department2(true);
$designation_array=getDesignationDrop(false);
?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">


    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 1 - <?php echo  $this->lang->line('sales_maraketing_masters_header');?><!--Step 1 - Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail()" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 2 - <?php echo  $this->lang->line('common_details');?><!--Step 2 - Details--></a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="load_confirmation()" data-toggle="tab"><?php echo $this->lang->line('common_step') ?> 3 - <?php echo  $this->lang->line('sales_maraketing_masters_confirmation');?><!--Step 3 - Confirmation--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="commision_scheme_header"'); ?>
            <input class="hidden" id="schemeID" name="schemeID">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="department"><?php echo $this->lang->line('common_department');?> <?php required_mark(); ?></label><!--Department-->
                    <?php echo form_dropdown('department', $department_array, '', 'class="form-control select2" id="department" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="designation"><?php echo $this->lang->line('common_designation');?> <?php required_mark(); ?></label><!--Designation-->
                    <?php echo form_dropdown('designation[]', $designation_array, '', 'class="form-control " id="designation" multiple="multiple" required '); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="narration"><?php echo $this->lang->line('common_narration');?> </label><!--Narration-->
                <!--    <textarea type="text" name="narration" id="narration" class="form-control"
                            placeholder="Comments"></textarea> -->
                        <input type="text" name="narration" id="narration" class="form-control" value="" >

                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="documentDate"><?php echo $this->lang->line('common_document_date');?> <?php required_mark(); ?></label><!--Document Date-->
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="documentDate" class="form-control">
                    </div>    
                </div>
                <div class="form-group col-sm-4">
                    <label for="currency"><?php echo $this->lang->line('common_currency');?> <?php required_mark(); ?></label><!--Currency-->
                    <input type="text" name="currency" id="currency" class="form-control" value="<?php echo $defaultCurrencyID; ?>" readonly>
                    
                </div>
                <div class="form-group col-sm-4">
                    
                </div>
            </div>
            <div class="row">
                <div class="text-right m-t-xs">
                    <div class="form-group col-sm-12" style="margin-top: 10px;">
                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save') ?><!--Save--></button>
                    </div>
                </div>
            </div>
          
            </form>

        </div>

        <div id="step2" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 ">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> Commission Scheme Details</h4>
                        </div>
                        <div class="col-md-4">
                            <button type="button" onclick="commission_scheme_item_pull_modal()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add Item</button>
                        </div>
                    </div>
                    
                    <div class="row" style="margin: 5px;">
                        <div class="commission_scheme_details" id="commission_scheme_details"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="step3" class="tab-pane">
            <div class="row" style="margin: 5px;">
                <div id="confirm_body"></div>
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous') ?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft') ?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm') ?><!--Confirm--></button>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" data-backdrop="static"
     id="commissionSchemePullItem">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Items</h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>Sub Category</label>
                            <?php echo form_dropdown('subCategoryID', $sub_category_arr, 'Each', 'class="form-control select2" id="subCategoryID" onchange="LoadSubSubCategory()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <label>Sub Sub Category</label>
                            <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox">
                                <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code'); ?><!--Item Code--></th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('common_item_description'); ?><!--Item Description--></th>
                                <th style="min-width: 10%"><abbr title="Secondary Code"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--></abbr></th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addItem_commision_sheme()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?> <!--Add Items-->
                                    </button>
                                   <!--  <input id="isActive" type="checkbox" data-caption="" class="columnSelected addItemz" name="isActive"  onclick="oTable2.draw()"> -->
                                </th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
    <script type="text/javascript">

        var schemeID = '';
        var designation = '';
        var selectedItemsSync = [];
        $(document).ready(function () {

            $('.headerclose').click(function () {
                fetchPage('system/sales/commission_scheme', 'Commission Scheme','');
            });
            //$('.select2').select2();

            $('#designation').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                buttonWidth: 345,
                maxHeight: 200,
                numberDisplayed: 1
            });
           // $("#designation").multiselect2('selectAll', false);
           // $("#designation").multiselect2('updateButtonText');

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                schemeID = p_id;
                load_commission_scheme_header();
                load_confirmation();
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.addTableView').addClass('hide');
                $('.btn-wizard').addClass('disabled');
                $('.addTableView').addClass('hide');
            }

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });

            $('.next').click(function () {
                var nextId = $(this).parents('.tab-pane').next().attr("id");
                $('[href=#' + nextId + ']').tab('show');
            });

            $('.prev').click(function () {
                var prevId = $(this).parents('.tab-pane').prev().attr("id");
                $('[href=#' + prevId + ']').tab('show');
            });

            $('#commision_scheme_header').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    department: {validators: {notEmpty: {message: 'Department is required.'}}},/*Department Date is required*/
                    designation: {validators: {notEmpty: {message: 'Designation is required.'}}},/*Designation Date is required*/
                    documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                    //narration: {validators: {notEmpty: {message: '<?php // echo $this->lang->line('transaction_goods_received_narration_is_required');?>.'}}},/*Narration is required*/
                    currency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $('#department').attr('disabled',false);
                $('#designation').attr('disabled',false);
                $('#documentDate').attr('disabled',false);
                //$("#narration").multiselect2("disable");
                //$("#search_to").prop('disabled', false);
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CommissionScheme/save_commission_scheme_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#schemeID').val(data[2]);
                            schemeID = data[2];
                            fetch_detail();
                            $('.btn-wizard').removeClass('disabled');
                            $('#documentDate').attr('disabled',true);
                            //$("#search_to").prop('disabled', true);

                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary'); 
                            $('.addTableView').removeClass('hide');
                        } else {
                            $('.btn-primary').prop('disabled', false);
                            $('.btn-wizard').removeClass('disabled');
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });

            
            sync_cs_item_table();

            $("#subsubcategoryID").change(function () {
                oTable2.draw();
            });

          /*   $('.addItemz input').iCheck({
                checkboxClass: 'icheckbox_square_relative-purple',
                radioClass: 'iradio_square_relative-purple',
                increaseArea: '20%'
            }); */
    
        });
        
        function load_commission_scheme_header() {
            
            if (schemeID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'schemeID': schemeID},
                    url: "<?php echo site_url('CommissionScheme/load_commission_scheme_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            schemeID = data['header']['schemeID'];
                            $('#schemeID').val(schemeID);
                            $('#department').val(data['header']['departmentID']);
                            $('#narration').val(data['header']['Narration']);
                            $('#currency').val(data['header']['CurrencyName']);
                            $("#documentDate").val(data['header']['documentDate']);

                            setTimeout(function () {
                                $('#designation').multiselect2("deselectAll", false).multiselect2("refresh");
                                $('#designation').multiselect2('select',data['designation']);

                                if(data['item'] && data['item'].length > 0){
                                    $("#designation").multiselect2("disable");
                                }else{
                                    $("#designation").multiselect2("enable");
                                }
                            }, 500);

                            $("#documentDate").prop('disabled', true);
                            fetch_detail();                      
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
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
        }

        function load_confirmation() {
            if (schemeID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'schemeID': schemeID, 'html': true},
                    url: "<?php echo site_url('CommissionScheme/load_commission_scheme_confirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#confirm_body').html(data);
                        //$(".itemCommission").attr("disabled", true);
                       // $( ".itemCommission" ).prop( "disabled", true );
                        refreshNotifications(true);

                    }, error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        refreshNotifications(true);
                    }
                });
            }
        }

        function save_draft() {
            if (schemeID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                        type: "warning",/*warning*/
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>"/*Save as Draft*/
                    },
                    function () {
                        fetchPage('system/sales/commission_scheme', 'Commission Scheme');
                    });
            }
        }

        function confirmation() {
            if (schemeID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                        type: "warning",/*warning*/
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>"/*Confirm*/
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'schemeID': schemeID},
                            url: "<?php echo site_url('CommissionScheme/commission_scheme_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                myAlert(data[0], data[1]);
                                stopLoad();
                                if (data[0] == 's') {
                                    fetchPage('system/sales/commission_scheme', 'Customer Price Setup','CUS');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                                stopLoad();
                            }
                        });
                    });
            }
        }

    function commission_scheme_item_pull_modal()
    {
        oTable2.search('').columns().search('').draw();
        //oTable2.draw();
        //$('#isActive').iCheck('uncheck');
        $("#commissionSchemePullItem").modal({backdrop: "static"});
    }

    /* function commission_scheme_item_pull_modal()
    {
        oTable2.draw();
        //$('#isActive').iCheck('uncheck');
        $("#commissionSchemePullItem").modal({backdrop: "static"});
    } */

    function LoadSubSubCategory() {
        $('#subsubcategoryID').val("");
        //$('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        oTable2.draw();
    }

    function load_itemMaster_subsubCategory() {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        var subsubid = $('#subCategoryID').val();
        if(subsubid) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
                dataType: 'json',
                data: {'subsubid': subsubid},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subsubcategoryID').empty();
                        var mySelect = $('#subsubcategoryID');
                        mySelect.append($('<option></option>').val('').html('Select Option'));
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        } else {
            $('#subsubcategoryID').empty();
            var mySelect = $('#subsubcategoryID');
            mySelect.append($('<option></option>').val('').html('Select Option'));
        }
    }

    function sync_cs_item_table() {
        oTable2 = $('#item_table_sync').DataTable({
            "pageLength": 100,
            lengthMenu: [10, 25, 50, 100, 250, 500, 1000],
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('CommissionScheme/fetch_item'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {

                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');

                        // $("#selectItem_" + value).prop("checked", true);
                    });
                }
                if ($('#isActive').is(":checked")){
                    $('.item-iCheck').iCheck('check');
                    selectedItemsSync = [];
                    $('.columnSelected').each(function () {
                        var id = $(this).val();
                        if(id != 'on'){
                            selectedItemsSync.push(id);
                        }
                    });

                } else{
                    $('.item-iCheck').iCheck('uncheck');
                    selectedItemsSync = [];
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "SubCategoryDescription"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "seconeryItemCode"},
                {"mData": "edit"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "subcategory", "value": $("#subCategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "schemeID", "value": schemeID});
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
    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }
    function addItem_commision_sheme()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("CommissionScheme/add_commission_scheme_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync, 'schemeID': schemeID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['error'], data['message']);
              
                if (data['error'] == 's') {
                    $("#designation").multiselect2("disable");
                    oTable2.draw();
                    selectedItemsSync = [];
                    setTimeout(function () {
                        fetch_detail();
                    }, 300);
                }
               
            },
            error: function (xhr, ajaxOptions, thrownError) {
               // myAlert(data['error'], data['message']);

               alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_detail() {
        var itemSearch = $('#itemSearch').val();
        if (schemeID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'schemeID': schemeID, 'itemSearch': itemSearch},
                url: "<?php echo site_url('CommissionScheme/fetch_commissionScheme_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.commission_scheme_details').html(data);
                    stopLoad();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }
    </script>
