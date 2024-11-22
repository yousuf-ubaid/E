<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_depreciation_history');
echo head_page($title, false);

/*echo head_page('Asset Depreciation History', false);*/
$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];
?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" style="border-top: 1px solid #f4f4f4;">
                <li class="active"><a href="#accTab" id="accountsTab" class="empTabs" data-toggle="tab"
                                      aria-expanded="true" data-value="0"><?php echo $this->lang->line('assetmanagement_monthly_depreciation');?><!--Monthly Depreciation--></a></li>
                <li class=""><a href="#salaryDecTab" id="salaryDecTabLink" class="empTabs" data-toggle="tab"
                                aria-expanded="false" data-value="0"><?php echo $this->lang->line('assetmanagement_ad_hoc_depreciation');?><!--Ad hoc Depreciation--></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active disabled" id="accTab"> <!-- /.tab-pane -->
                    <div id="assetDepDivHistory">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" onclick="getDepGenerate()">
                                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('assetmanagement_generate_depreciation');?><!--Generate Depreciation-->
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive">
                                <table id="assetMaster_dep_master_table" class="<?php echo table_class() ?>">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" style="min-width: 5%">#</th>
                                        <th rowspan="2" style="min-width: 10%"><?php echo $this->lang->line('assetmanagement_dep_code');?><!--Dep Code--></th>
                                        <th rowspan="2" style="min-width: 400px !important;"><?php echo $this->lang->line('common_month');?><!--Month--></th>
                                        <th colspan="2"><?php echo $this->lang->line('common_cost');?><!--Cost--></th>
                                        <th rowspan="2" style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                                        <th rowspan="2" style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                                        <th rowspan="2" style="min-width: 8%"></th>
                                        <th rowspan="2" style="min-width: 1%">&nbsp;</th>
                                    </tr>
                                    <tr>
                                        <th><?php echo $com_currency ?></th>
                                        <th style=""><?php echo $rep_currency ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="assetDepDivGenerate" style="display: none;"></div>
                    <div id="assetDepDivHistoryDetail" style="display: none;"></div>
                </div>
                <div class="tab-pane" id="salaryDecTab"> <!-- /.tab-pane -->
                    <div class="row">
                        <div class="table-responsive">
                            <table id="assetMaster_dep_master_table_adhoc" class="<?php echo table_class() ?>">
                                <thead>
                                <tr>
                                    <th rowspan="2" style="min-width: 5%">#</th>
                                    <th rowspan="2" style="min-width: 10%"><?php echo $this->lang->line('assetmanagement_dep_code');?><!--Dep Code--></th>
                                    <th rowspan="2" style="min-width: 400px !important;"><?php echo $this->lang->line('common_month');?><!--Month--></th>
                                    <th colspan="2"><?php echo $this->lang->line('common_cost');?><!--Cost--></th>
                                    <th rowspan="2" style=""><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                                    <th rowspan="2" style=""><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                                    <th rowspan="2" style=""></th>
                                </tr>
                                <tr>
                                    <th><?php echo $com_currency ?></th>
                                    <th style=""><?php echo $rep_currency ?></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="depreciation_edit_view_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('assetmanagement_asset_monthly_depreciation');?><!--Asset Monthly Depreciation--></h4>
            </div>
            <div class="modal-body">
                <div id="assetDepDivHistoryDetail_editView"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="depreciation_edit_view_model_adhoc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('assetmanagement_asset_ad_hoc_depreciation');?><!--Asset Ad hoc Depreciation--></h4>
            </div>
            <div class="modal-body">
                <div id="assetDepDivHistoryDetail_editView_adhoc"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script>
    var Otable;
    var Otable_adhoc;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/asset_management/asset_depreciation_master','','Asset Depreciation');
        });
        fetch_dep_master();
        fetch_dep_master_adhoc();
        /*$('.with-border').remove();*/
    });

    function fetch_dep_master() {
         Otable = $('#assetMaster_dep_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_asset_dep_master'); ?>",
            "aaSorting": [[0, 'desc']],
          /*  "columnDefs": [
                {
                    "targets": [ 8 ],
                    "visible": false,
                    "searchable": false
                },{
                    "targets": [ 9 ],
                    "visible": false,
                    "searchable": true
                },
                {
                    "targets": [2, 3, 4, 5, 6],
                    "orderable": false, "searchable": true
                }, {"searchable": false, "targets": [0]}
            ],*/
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "depMasterAutoID"},
                {"mData": "depCode"},
                {"mData": "depMonthYear"},
                {"mData": "sumcompanyLocalAmount"},
                {"mData": "sumcompanyReportingAmount"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "companyLocalAmount"},
                {"mData": "companyReportingAmount"}
            ],
            "columnDefs": [{"targets": [7], "visible": false, "searchable": true,
                "targets": [8,9]},{"targets": [0,3,4], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
        });
    }

    function fetch_dep_master_adhoc() {
         Otable_adhoc = $('#assetMaster_dep_master_table_adhoc').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_asset_dep_master_adhoc'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "depMasterAutoID"},
                {"mData": "depCode"},
                {"mData": "depMonthYear"},
                {"mData": "sumcompanyLocalAmount"},
                {"mData": "sumcompanyReportingAmount"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "companyLocalAmount"},
                {"mData": "companyReportingAmount"}
            ],
            "columnDefs": [{"targets": [7], "visible": false, "searchable": true,
                "targets": [8,9]},{"targets": [0,3,4], "searchable": false}],
            /*"columnDefs": [{
                "targets": [3, 4, 5, 6, 7],
                "orderable": false
            }, {"searchable": false, "targets": [0]}],*/
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },

        });
    }

    function getAssetDepDetail(index, confirmedYN) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {depMasterAutoID: index, confirmedYN: confirmedYN},
            url: "<?php echo site_url('AssetManagement/get_asset_dep_details'); ?>",
            beforeSend: function () {
                startLoad();
                toggleDivs('#assetDepDivHistoryDetail', 'Asset Depreciation Details')
            },
            success: function (data) {
                $('#assetDepDivHistoryDetail').html(data);
                $('.headerclose').attr('onclick', "toggleDivs('#assetDepDivHistory', 'Asset Depreciation History')");
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getAssetDepDetail_editView(index, confirmedYN) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {depMasterAutoID: index, confirmedYN: confirmedYN},
            url: "<?php echo site_url('AssetManagement/get_asset_dep_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#depreciation_edit_view_model").modal({backdrop: "static", keyboard: true});
                $('#assetDepDivHistoryDetail_editView').html(data);
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getAssetDepDetail_editView_adhoc(index, confirmedYN) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {depMasterAutoID: index, confirmedYN: confirmedYN},
            url: "<?php echo site_url('AssetManagement/get_asset_dep_details_adhoc'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#depreciation_edit_view_model_adhoc").modal({backdrop: "static", keyboard: true});
                $('#assetDepDivHistoryDetail_editView_adhoc').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getDepGenerate() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: '',
            url: "<?php echo site_url('AssetManagement/get_asset_dep_generate'); ?>",
            beforeSend: function () {
                startLoad();
                toggleDivs('#assetDepDivGenerate', 'Asset Depreciation')
            },
            success: function (data) {
                $('#assetDepDivGenerate').html(data);
                $('.headerclose').attr('onclick', "toggleDivs('#assetDepDivHistory', 'Asset Depreciation History')");
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function toggleDivs(id, headerText) {
        $("div[id^='assetDepDiv']").not(id).hide();
        $(id).show();
        $("#box-header-title").text(headerText);
    }


    function deleteAssetDep(index, confirmedYN) {
        bootbox.confirm('Are you sure? You want to delete this Asset Depreciation?', function (confirmed) {
            if (confirmed) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {depMasterAutoID: index, confirmedYN: confirmedYN},
                    url: "<?php echo site_url('AssetManagement/delete_asset_depreciation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        Otable.draw();
                        stopLoad();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        })
    }

    function referback_bankrec(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
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
                    data: {'depMasterAutoID': id},
                    url: "<?php echo site_url('AssetManagement/referback_grv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_adhoc(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
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
                    data: {'depMasterAutoID': id},
                    url: "<?php echo site_url('AssetManagement/referback_grv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable_adhoc.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>