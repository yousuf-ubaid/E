<?php
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
                        class=" glyphicon glyphicon-random" aria-hidden="true"></span> Account Review entries
                </a>
            </span>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="table-responsive">
        <table id="assetMaster_dep_table_adhoc" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th rowspan="2" style="">FA Code</th>
                <th rowspan="2" style="">Asset Description</th>
                <th rowspan="2" style="">Date Capitalized</th>
                <th rowspan="2" style="">Date Start Depreciation</th>
                <th rowspan="2" style="">Debit GL Code</th>
                <th rowspan="2" style="">Debit GL Code</th>
                <th rowspan="2" style="">Credit GL Code</th>
                <th rowspan="2" style="">Segment</th>
                <th rowspan="2">DEP %</th>
                <th colspan="2">Cost Amount</th>
                <th colspan="2">Dep Amount</th>
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
            <button class="btn btn-primary" onclick="save_draft()">Save as Draft</button>
            <a class="btn btn-success <?php echo $confirmed == 1 ? 'disabled' : '' ?> "
               onclick="assetDepConfirm(this)" style="" id="assetDepConfirm">Confirm
            </a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        feedAssetdep();
    });

    function feedAssetdep() {
        var Otable = $('#assetMaster_dep_table_adhoc').DataTable({
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
            "fnDrawCallback": function (oSettings) {
                makeTdAlign('assetMaster_dep_table_adhoc', 'right', [9, 10, 11, 12]);
            }
        });
    }

    function save_draft() {
        swal({
                title: "Are you sure?",
                text: "You want to save this document!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Save as Draft"
            },
            function () {
                //fetch_dep_master_adhoc();
                hideModal();
                fetchPage('system/asset_management/asset_depreciation_master','','Asset Depreciation');
                setTimeout(function () {
                    $('.nav-tabs a:last').tab('show');
                }, 500);
            });
    }

    function hideModal() {
        $("#depreciation_edit_view_model_adhoc").removeClass("in");
        $(".modal-backdrop").remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        $("#depreciation_edit_view_model_adhoc").hide();
    }

    function assetDepConfirm(item) {
        bootbox.confirm('Are you sure ?,  You want to confirm?', function (confirmed) {
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
                            fetch_dep_master_adhoc();
                            $('#depreciation_edit_view_model_adhoc').modal('hide');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            }
        })
    }

</script>
