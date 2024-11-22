<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$date_format_policy = date_format_policy();
$current_date = current_format_date();
//echo $assetdisposalMasterAutoID;
$financeyear_arr = all_financeyear_drop();
$segment_arr = fetch_segment();

$company=group_company_drop_without_current();

$finanaceYear = $datas['companyFinanceYearID'] ?? $this->common_data['company_data']['companyFinanceYearID'];


$finanaceYearPeriod = financeYearPeriod($finanaceYear);

$finanaceYearPeriodId = ($datas['FYPeriodDateFrom'] ?? $this->common_data['company_data']['FYPeriodDateFrom']) 
                        . '|' . 
                        ($datas['FYPeriodDateTo'] ?? $this->common_data['company_data']['FYPeriodDateTo']);


?>
<style>
    #not_selected_asset_table tbody td {
        padding: 2px 5px;
    }

    #not_selected_asset_table tbody td:nth-child(8) {
        vertical-align: middle;
    }
</style>
<!--<div class="pull-right">
    <button class="btn btn-xs btn-danger" onclick="toggleMasterDetails();"><i class="fa fa-remove"></i></button>
</div>-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('assetmanagement_disposal_header');?><!--Disposal Header--></a>
    <a class="btn btn-default btn-wizard disabled" href="#step2" data-toggle="tab"><?php echo $this->lang->line('assetmanagement_disposal_detail');?><!--Disposal Detail--></a>
    <!--<a class="btn btn-default btn-wizard" href="#step3" data-toggle="tab">Asset Valuation</a>-->
    <!--<a class="btn btn-default btn-wizard" href="#step4" data-toggle="tab">Asset Attachment</a>-->
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <?php echo form_open('', 'role="form" id="add_new_disposal_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('assetmanagement_document_code');?><!--Document Code--></label>
                <h4 id="disposalDocumentCode"><?php echo $datas['disposalDocumentCode'] ?? ''; ?></h4>
            </div>

            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_type');?><!--Type--> <?php required_mark(); ?></label>
                <select name="assetType" id="assetType" class="form-control select2" onchange="checkInterCompany()">
                    <option value="1" <?php echo (($datas['type'] ?? null) == 1) ? 'selected' : ''; ?>>Standard</option>
                    <option value="2" <?php echo (($datas['type'] ?? null) == 2) ? 'selected' : ''; ?>>Inter Company Transaction</option>
                </select>
            </div>

            <div class="form-group col-sm-4" id="interCompanyDiv" style="display:none;">
                <label for="Inter_Company"><?php echo $this->lang->line('common_interCompnay');?><?php required_mark(); ?></label>
                <?php echo form_dropdown('interCompanyID', array_column($company, 'cName', 'company_id'),array($datas['interCompanyID'] ?? ''), 'class="form-control select2" id="interCompanyID"' );?>
               
            </div>

           
        </div>

        <div class="row">
          <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('assetmanagement_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                <?php

                echo form_dropdown('companyFinanceYearID', $financeyear_arr, array($finanaceYear), 'class="form-control" id="companyFinanceYearID" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('assetmanagement_financial_period');?><!--Financial Period--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear_period', $finanaceYearPeriod, array($finanaceYearPeriodId), 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_document_date');?><!--Document Date--> <?php required_mark(); ?></label>
                <div class=" input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="disposalDocumentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $datas['disposalDocumentDate'] ?? '' ?>" id="disposalDocumentDate"
                           class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-6">
                <label for=""><?php echo $this->lang->line('common_narration');?><!--Narration--><?php required_mark(); ?></label>
                <textarea tabindex="12" class="form-control" rows="2" id="narration"
                          name="narration"><?php echo $datas['narration'] ?? '' ?></textarea>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, array($datas['segment'] ?? ''), 'class="form-control select2" id="segment" required'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit" id="assetDisposalNext"><?php echo $this->lang->line('common_next');?><!--Next--></button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!--step2-->
    <div id="step2" class="tab-pane" style="box-shadow: none;">
        <div>
            <button class="btn btn-primary btn-sm pull-right" type="button" style="margin-bottom: 2px;"
                    id="add_asset_btn"
                    onclick="feedNotSelectedAsset();"><i class="glyphicon glyphicon-plus"></i> <?php echo $this->lang->line('assetmanagement_add_asset');?><!--Add Asset-->
            </button>
            <span class="no-print pull-right" style="margin-right: 5px;">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php echo site_url('/Double_entry/fetch_double_entry_asset_disposal/' . $assetdisposalMasterAutoID . '/ADSP') ?>"><span
                        class="glyphicon glyphicon-random" aria-hidden="true"></span>&nbsp;&nbsp;<?php echo $this->lang->line('assetmanagement_account_review_entries');?><!--Account Review entries-->
                </a>
            </span>
        </div>
        <div>
            <table id="selectedAssetTable" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_asset_no');?><!--Asset No--></th>
                    <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_serial_no');?><!--Serial No--></th>
                    <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th style="width: 54px;"><?php echo $this->lang->line('assetmanagement_acq_date');?><!--Acq Date--></th>
                    <th style="width: 60px;"><?php echo $this->lang->line('common_cost');?><!--Cost--></th>
                    <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_acq_dep');?><!--Acc Dep--></th>
                    <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_nbv');?><!--NBV--></th>
                    <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_disposal_amt');?><!--Disposal Amt--></th>
                    <th style="width: 23px;">#</th>
                </tr>
                </thead>
            </table>
            <div style="float: right; width: 185px;">
                <a class="btn btn-primary " onclick="saveAsDraft()" style=""
                   id="saveAsDraft"><?php echo $this->lang->line('common_save_as_draft');?><!--Save As Draft--> </a> &nbsp;
                <a class="btn btn-success pull-right " onclick="assetDisposalConfirm(this)" style=""
                   id="assetDisposalConfirm"><?php echo $this->lang->line('common_confirm');?><!--Confirm--> </a>
            </div>
        </div>
    </div>
    <!--step3-->
    <div id="step3" class="tab-pane" style="box-shadow: none;">

    </div>
    <!--step4-->
    <div id="step4" class="tab-pane" style="box-shadow: none;">

    </div>
</div>

<!--Modal-->
<div aria-hidden="true" role="dialog" tabindex="-1" id="not_selected_asset_modal" class="modal fade in"
     style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('assetmanagement_assets');?><!--Assets--></h4>
            </div>
            <div class="modal-body" id="not_selected_asset_modal_body">
                <table class="<?php echo table_class(); ?>" cellSpacing='0' cellPadding='0'
                       id="not_selected_asset_table">
                    <thead>
                    <tr>
                        <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_asset_no');?><!--Asset No--></th>
                        <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_serial_no');?><!--Serial No--></th>
                        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th style="width: 54px;"><?php echo $this->lang->line('assetmanagement_acq_date');?><!--Acq Date--></th>
                        <th style="width: 60px;"><?php echo $this->lang->line('common_cost');?><!--Cost--></th>
                        <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_acq_dep');?><!--Acc Dep--></th>
                        <th style="width: 60px;"><?php echo $this->lang->line('assetmanagement_nbv');?><!--NBV--></th>
                        <th style="width: 80px;"><?php echo $this->lang->line('assetmanagement_disposal_amt');?><!--Disposal Amt--></th>
                        <th style="width: 23px;">#</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <!--<button class="btn btn-primary" onclick="add_to_disposal()" type="button">Add to Dispose</button>-->
            </div>
        </div>
    </div>
</div>
<!--//Modal-->


<div aria-hidden="true" role="dialog" tabindex="-1" id="not_approved_disposal_model" class="modal fade in"
     style="display: none;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Following Asset Depreciation documents are not approved. Please approve these documents and try again</h5>
            </div>
            <div class="modal-body" id="not_selected_asset_modal_body">
                <table class="<?php echo table_class(); ?>" cellSpacing='0' cellPadding='0' >
                    <thead>
                    <tr>
                        <th style="width: 10px;">#</th>
                        <th style="width: 80px;">Code</th>
                        <th style="width: 80px;">Amount</th>

                    </tr>
                    </thead>
                    <tbody id="not_approved_disposal">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <!--<button class="btn btn-primary" onclick="add_to_disposal()" type="button">Add to Dispose</button>-->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var assetdisposalMasterAutoID = '<?php echo $assetdisposalMasterAutoID;  ?>';
    var totalSelectedAssets = 0;

    $(document).ready(function () {
        feedSelectedAsset();

        checkInterCompany();
        
        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        /*save header*/
        $('#add_new_disposal_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                companyFinanceYearID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_financial_year_is_required');?>.'}}},/*Financial Year is required*/
                financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_financial_period_is_required');?>.'}}},/*Financial Period is required*/
                disposalDocumentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_narration_is_required');?>.'}}},/*Narration is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#segment").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            var data = $form.serializeArray();

            data.push({'name': 'assetdisposalMasterAutoID', 'value': assetdisposalMasterAutoID});

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                url: "<?php echo site_url('AssetManagement/save_disposal_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        $('#disposalDocumentCode').html(data.disposalDocumentCode);
                        assetdisposalMasterAutoID = data.pk_id;
                        $("#segment").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                        if (data.is_reload == true) {
                            feedDisposalMaster();
                        }
                    } else {
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

        });
        /*//save header*/

        /*Wizad*/
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
        /*Wizad*/

        /*If edit*/
        if (assetdisposalMasterAutoID) {
            $('.btn-wizard').removeClass('disabled');
            $("#segment").prop("disabled", true);
            var confirmedYN = '<?php echo $datas['confirmedYN'] ?? '' ?>';
            if (confirmedYN == 1) {
                $('.remove_from_disposal').addClass('disabled');
                $('#assetDisposalConfirm,#saveAsDraft').addClass('disabled');
                $('#add_asset_btn').attr('disabled', true);
                $('#add_new_disposal_form input,#add_new_disposal_form select,#add_new_disposal_form textarea,#add_new_disposal_form button').attr('disabled', true);
            }


        } else {
            //fetch_finance_year_period(FinanceYearID, DateFrom + '|' + DateTo);
            $('.btn-wizard').addClass('disabled');
            $("#segment").prop("disabled", false);
        }
        /*//If edit*/
    });

    function feedSelectedAsset() {
        var Otable = $('#selectedAssetTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_selected_disposal_asset'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSetting) {
                totalSelectedAssets = oSetting._iRecordsTotal;
                makeTdAlign('selectedAssetTable', 'right', [4, 5, 6, 7])
            },
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "serialNo"},
                {"mData": "assetDescription"},
                {"mData": "dateAQ"},
                {"mData": "companyLocalAmount"},
                {"mData": "accLocalAmount"},
                {"mData": "netBookValueLocalAmount"},
                {"mData": "disposalAmount"},
                {"mData": "edit"}
            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push(
                    {name: "assetdisposalMasterAutoID", value: assetdisposalMasterAutoID}
                );
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function checkInterCompany(){
        var id=$('#assetType').val();
        if(id==2){
           $("#interCompanyDiv").show();
        }
        else{
            $("#interCompanyDiv").hide();
            $('#interCompanyID').val('');
        }
    }

    function feedNotSelectedAsset() {
        $('#not_selected_asset_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_not_disposed_asset'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {

                $('#not_selected_asset_modal').modal('show');

                $(".disposalAmount").keydown(function (event) {
                    if (event.shiftKey == true) {
                        event.preventDefault();
                    }
                    if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110) {
                    } else {
                        event.preventDefault();
                    }
                    if ($(this).val().indexOf('.') !== -1 && (event.keyCode == 190 || event.keyCode == 110))
                        event.preventDefault();
                });

                makeTdAlign('not_selected_asset_table', 'right', [4, 5, 6])
                makeTdAlign('not_selected_asset_table', 'center', [7, 8])
            },
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "serialNo"},
                {"mData": "assetDescription"},
                {"mData": "dateAQ"},
                {"mData": "companyLocalAmount"},
                {"mData": "accLocalAmount"},
                {"mData": "netBookValueLocalAmount"},
                {"mData": "disposalAmount"},
                {"mData": "add"}
            ],
            "columnDefs": [{
                "targets": [4, 5, 6, 7, 8],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push(
                    {name: "documentDate", value: $('#disposalDocumentDate').val()},
                    {name: "segment", value: $('#segment').val()}
                );
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'ADSP'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['dateFrom'] + '|' + text['dateTo']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });

                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;


    /*var selectedForDisposal = [];
     function selected_to_disposal(item) {
     var value = $(item).val();
     if ($(item).is(':checked')) {
     var inArray = $.inArray(value, selectedForDisposal);
     if (inArray == -1) {
     selectedForDisposal.push(value);
     }
     }
     else {
     var i = selectedForDisposal.indexOf(value);
     if (i != -1) {
     selectedForDisposal.splice(i, 1);
     }
     }
     }*/


    function add_to_disposal(faId) {
        bootbox.confirm('Are you sure? You want to add this Asset.', function (confirmed) {
            var disposalAmount = $('#disposalAmount_' + faId).val();

            if (confirmed) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        faId: faId,
                        assetdisposalMasterAutoID: assetdisposalMasterAutoID,
                        disposalAmount: disposalAmount
                    },
                    cache: false,
                    url: "<?php echo site_url('AssetManagement/add_to_disposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        feedNotSelectedAsset();
                        feedSelectedAsset();


                        if(data[0]=='w'){
                            x = 1;
                            if (jQuery.isEmptyObject(data[2])) {
                                $('#not_approved_disposal').empty();
                            } else {
                                $('#not_approved_disposal').empty();
                                $.each(data[2], function (key, value) {
                                    if(value['accLocalAmount']>0){
                                        $('#not_approved_disposal').append('<tr><td>' + x + '</td> <td>' + value['depCode'] + '</td> <td>' + value['accLocalAmount'] + '</td></tr>');
                                        x++;
                                    }

                                });
                                $('#not_approved_disposal_model').modal('show');
                            }
                        }else{
                            myAlert(data[0],data[1]);
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        })
    }

    function remove_from_disposal(assetDisposalDetailAutoID, faId) {
        bootbox.confirm('Are you sure?, You want to delete this Asset.', function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        assetDisposalDetailAutoID: assetDisposalDetailAutoID,
                        faId: faId
                    },
                    cache: false,
                    url: "<?php echo site_url('AssetManagement/remove_asset_from_disposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        feedSelectedAsset();
                        if (data['status']) {
                        } else {
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        });
    }

    function assetDisposalConfirm(item) {
        if (totalSelectedAssets > 0) {
            bootbox.confirm("Are you sure? You want to confirm?", function (confirmed) {
                if (confirmed) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('AssetManagement/assetDisposalConfirm'); ?>",
                        data: {assetdisposalMasterAutoID: assetdisposalMasterAutoID},
                        dataType: "json",
                        cache: false,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            if (data.status == true) {
                                $(item).addClass('disabled');
                                $('#remove_from_disposal').addClass('disabled');
                                $('#add_asset_btn').attr('disabled', true);
                                $('#add_new_disposal_form input,#add_new_disposal_form select,#add_new_disposal_form textarea,#add_new_disposal_form button').attr('disabled', true);
                                feedDisposalMaster();
                                toggleMasterDetails('#assetDisposalMaster', 'Asset Disposal')
                            } else {
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                        }
                    });
                }
            });
        } else {
            notification('No asset selected for Dispose.')
        }
    }

    function saveAsDraft() {
        feedDisposalMaster();
        toggleMasterDetails('#assetDisposalMaster', 'Asset Disposal')
    }
</script>