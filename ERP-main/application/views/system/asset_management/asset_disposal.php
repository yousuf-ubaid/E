<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_disposal');
echo head_page($title, false);


/*echo head_page('Asset Disposal', false); */?>
<div id="assetDisposalMaster">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-5 text-center">
            <!--<table class="<?php /*echo table_class(); */ ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Confirmed /
                    Approved
                </td>
                <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                    / Not Approved
                </td>
                <td><span class="label label-warning">&nbsp;</span> Refer-back
                </td>
            </tr>
        </table>-->
        </div>
        <div class="col-md-4 text-center"></div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="getAssetDisposalDetails();">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_update_add_new');?><!--Add New-->
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="disposalMaster_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th>#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('assetmanagement_disposal_code');?><!--Disposal Code--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('assetmanagement_doc_date');?><!--Doc Date--></th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                <th style="min-width: 6%"></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div id="assetDisposalDetail" style="display: none;">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feedDisposalMaster();
    });

    function feedDisposalMaster() {
        var Otable = $('#disposalMaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_disposal'); ?>",
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
                {"mData": "assetdisposalMasterAutoID"},
                {"mData": "disposalDocumentCode"},
                {"mData": "disposalDocumentDate"},
                {"mData": "narration"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{
                "targets": [5, 6],
                "orderable": false
            }, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

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

    function referbackDisposal(id) {
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
                    data: {'assetdisposalMasterAutoID': id},
                    url: "<?php echo site_url('AssetManagement/referback_disposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            feedDisposalMaster();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function getAssetDisposalDetails(index) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {assetdisposalMasterAutoID: index},
            url: "<?php echo site_url('AssetManagement/get_asset_disposal_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#assetDisposalDetail').html(data);
                toggleMasterDetails('#assetDisposalDetail', '<?php echo $this->lang->line('assetmanagement_asset_disposal')?>');/*Asset Disposal*/
                $('.headerclose').attr('onclick', "toggleMasterDetails('#assetDisposalMaster', '<?php echo $this->lang->line('assetmanagement_asset_disposal')?>')");
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function deleteDisposal(assetdisposalMasterAutoID, confirmedYN) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
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
                    data: {'assetdisposalMasterAutoID': assetdisposalMasterAutoID},
                    url: "<?php echo site_url('AssetManagement/deleteDisposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        feedDisposalMaster();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function toggleMasterDetails(id, headerText) {
        $("div[id^='assetDisposal']").not(id).hide();
        $(id).show();
        $("#box-header-title").text(headerText);
    }


</script>