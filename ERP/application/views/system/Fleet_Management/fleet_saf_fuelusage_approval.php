<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_fuel_usage_approval');

$this->lang->load('fleet_lang', $primaryLanguage);
echo head_page($this->lang->line('fleet_fuel_usage_approval'), false);
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="FuelUsage_approval_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('fleet_document_no');?><!--FU Code--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('fleet_supplier');?><!--FU Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="jv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('fleet_fuel_usage_approval');?><!--Fuel Usage Approval--></h4>
            </div>
            <form class="form-horizontal" id="fuelusageApproval_form">
                <div class="modal-body">
                    <div class="col-sm-1">

                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?></a></li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment');?></a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('po_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="po_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        <input type="hidden" name="fuelusageID" id="fuelusageID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">&nbsp;
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1000000000;">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->

                        </div>
                        <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                            <!-- Tab panes -->
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="home-v">
                                    <div id="loaddocumentPageView" class="col-md-12"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_saf_fuelusage_approval','','Fuel Usage');
        });
        FuelUsage_approval_table();

        $('#fuelusageApproval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},

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
                url: "<?php echo site_url('Fleet/save_fuel_usage_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data == true){
                        $("#jv_modal").modal('hide');
                        Otable.draw();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function FuelUsage_approval_table() {
        Otable = $('#FuelUsage_approval_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Fleet/fuel_usage_table_approval'); ?>",
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
                {"mData": "fuelusageID"},
                {"mData": "documentCode"},
                {"mData": "details"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "supplierName"},
                {"mData": "transactionCurrency"},
                {"mData": "transactionAmount"}
            ],
            "columnDefs": [{"targets": [6,7,8], "orderable": false, "visible": false, "searchable": true},{"target": [0,2,4,5], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function fetch_approval(fuelusageID, documentApprovedID, Level) {
        if (fuelusageID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'fuelusageID': fuelusageID, 'html': true},
                url: "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#fuelusageID').val(fuelusageID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#jv_modal").modal({backdrop: "static"});
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

    function PageView_modal(documentID, para1, para2, approval=1) {

        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;

        $("#itemMasterSubTab_footer_div").html('');
        $(".itemMasterSubTab_footer").hide();

        switch (documentID) {

            case "FU": // Rental Item Issue
                siteUrl = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>";
                paramData.push({name: 'fuelusageID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('fleet_fuel_usage_approval');?>";
                break;

            default:
                notification('Document ID is not set .', 'w');
                return false;
        }
        paramData.push({name: 'html', value: true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: paramData,
            url: siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#documentPageViewTitle').html(title);
                $('#loaddocumentPageView').html(data);
                $('#documentPageView').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

</script>