<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_depreciation_approval');
echo head_page($title, false);
/*echo head_page('Depreciation Approval', false); */?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                   <?php echo $this->lang->line('common_approved');?><!-- Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved');?> <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Asset_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="Asset_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('assetmanagement_dep_code');?><!--Dep Code--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('assetmanagement_dep_type');?><!--Dep Type--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('assetmanagement_dep_month');?><!--Dep Month--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="fa_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('assetmanagement_depreciation_approval');?><!--Depreciation Approval--></h4>
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">
                    <div id="conform_body"></div>
                    <hr>
                    <div class="ifApproved">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                <input type="hidden" name="Level" id="Level">
                                <input type="hidden" name="depMasterAutoID" id="depMasterAutoID">
                                <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ifApproved">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/asset_management/depreciation_approval','','Depreciation Approval');
        });
        Asset_table();
        $('#pv_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                faID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_id_is_required');?>.'}}},/*Asset ID is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('AssetManagement/save_depreciation_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if(data == true){
                        $("#fa_modal").modal('hide');
                        Asset_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function Asset_table() {
        var Otable = $('#Asset_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/fetch_depreciation_approval'); ?>",
            "aaSorting": [[1, 'desc']],
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
                {"mData": "depCode"},
                {"mData": "depCode"},
                {"mData": "depType"},
                {"mData": "depDate"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
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

    function fetch_approval(depMasterAutoID, documentApprovedID, Level, approvedYN) {
        if (approvedYN == 1) {
            $('.ifApproved').hide();
        } else {
            $('.ifApproved').show()
        }

        if (depMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'depMasterAutoID': depMasterAutoID, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_dep_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#depMasterAutoID').val(depMasterAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#fa_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    stopLoad();
                    refreshNotifications(true);
                    }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
</script>