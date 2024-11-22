<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

//echo head_page('Asset Depreciation Details', false);
$financeyear_arr = all_financeyear_drop();
$page_id = $depMasterAutoID;
$confirmed = $confirmedYN;

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];

?>
<!--<div class="pull-right">
    <button class="btn btn-xs btn-danger" onclick="toggleDivs('#assetDepDivHistory', 'Asset Depreciation History')"><i
            class="fa fa-remove"></i></button>
</div>-->
<div class="row">
    <div class="col-md-12" id="assetDepHeader">
        <div class="col-md-8">
            <?php echo $header ?>
        </div>
        <div class="col-md-4">
            <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php echo site_url('/Double_entry/fetch_double_entry_asset_depreciation_master/' . $page_id . '/FAD') ?>"><span
                            class=" glyphicon glyphicon-random" aria-hidden="true"></span> <?php echo $this->lang->line('assetmanagement_account_review_entries');?><!--Account Review entries-->
                </a>
            </span>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_fa_code');?><!--FA Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_asset_description');?><!--Asset Description--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_date_capitalized');?><!--Date Capitalized--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_date_start_depreciation');?><!--Date Start Depreciation--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_debit_gl_code');?><!--Debit GL Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_debit_gl_code');?><!--Debit GL Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('assetmanagement_credit_gl_code');?><!--Credit GL Code--></th>
                <th rowspan="2" style=""><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                <th rowspan="2"><?php echo $this->lang->line('assetmanagement_dep');?><!--DEP--> %</th>
                <th colspan="2"><?php echo $this->lang->line('assetmanagement_cost_amount');?><!--Cost Amount--></th>
                <th colspan="2"><?php echo $this->lang->line('assetmanagement_dep_amount');?><!--Dep Amount--></th>
            </tr>
            <tr>
                <th style="">(<?php echo $com_currency; ?>)</th>
                <th style="">(<?php echo $rep_currency; ?>)</th>
                <th style="">(<?php echo $com_currency; ?>)</th>
                <th style="">(<?php echo $rep_currency; ?>)</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-sm-9">
    </div>
    <div class="col-sm-3">
        <div class="pull-right">
            <button class="btn btn-primary" onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <a class="btn btn-success <?php echo $confirmed == 1 ? 'disabled' : '' ?> "
               onclick="assetDepConfirm(this)" style="" id="assetDepConfirm"><?php echo $this->lang->line('common_confirm');?><!--Confirm-->
            </a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        feedAssetdep();
    });

    function feedAssetdep() {
        var Otable = $('#assetMaster_dep_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_asset_dep_generated'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {
            },
            "aoColumns": [
                {"mData": "faCode"},
                {"mData": "assetDescription"},
                {"mData": "postDate"},
                {"mData": "dateDEP"},
                {"mData": "dateDEP"},
                {"mData": "DEPGLCODE"},
                {"mData": "ACCDEPGLCODE"},
                {"mData": "segmentCode"},
                {"mData": "depPercent"},
                {"mData": "companyLocalAmount"},
                {"mData": "companyReportingAmount"},
                {"mData": "DepcompanyLocalAmount"},
                {"mData": "DepcompanyReportingAmount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "depMasterAutoID", "value": "<?php echo $page_id;  ?>"});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "columnDefs": [{"searchable": false, "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12]}],
            "fnDrawCallback": function (oSettings) {
                makeTdAlign('assetMaster_dep_table', 'right', [9, 10, 11, 12]);
            }
        });
    }

    function save_draft() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                fetchPage('system/asset_management/asset_depreciation_master', '', 'Asset Depreciation');
                hideModal();
            });
    }

    function hideModal() {
        $("#depreciation_edit_view_model").removeClass("in");
        $(".modal-backdrop").remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        $("#depreciation_edit_view_model").hide();
    }

    function assetDepConfirm(item) {
        bootbox.confirm('Are you sure ? You want to confirm?', function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('AssetManagement/assetDepConfirm'); ?>",
                    data: {depMasterAutoID: '<?php echo $page_id  ?>'},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        if (data.status == true) {
                            $(item).addClass('disabled');
                            Otable.draw();
                            $('#depreciation_edit_view_model').modal('hide');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            }
        })
    }

</script>
