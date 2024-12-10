<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title=$this->lang->line('sales_markating_customer_price_setup');
echo head_page($title, false);
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span>
                        <?php echo $this->lang->line('common_approved');?><!--Approved-->
                    </td>
                    <td><span class="label label-danger">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!-- Not Approved-->
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-center">
            <?php echo form_dropdown('approvedYN', array('0' => 'Pending', '1' => 'Approved'), '', 'class="form-control" id="approvedYN" required onchange="CustomerPriceSetup_table()"'); ?>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="customer_PriceSetup_approval" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 12%"><?php echo $this->lang->line('common_document_code');?><!-- Document Code--></th>
                <th style="width: 30%"><?php echo $this->lang->line('common_customer_name');?><!-- Customer Name--></th>
                <th style="width: 12%"><?php echo $this->lang->line('common_document_date');?><!-- Document Date--></th>
                <th style="width: 12%"><?php echo $this->lang->line('common_narration');?><!-- Narration--></th>
                <th style="width: 10%"><?php echo $this->lang->line('common_action');?><!-- Action--></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="jv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document" style="width: 80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Customer Price Setup Approval</h4>
                </div>
                <form class="form-horizontal" id="CustomerPriceSetup_approval_form">
                    <div class="modal-body">
                        <!-- <div class="col-sm-1">
                              Nav tabs
                             <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                 <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">View</a></li>
                                 <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()">Attachment</a>
                                 </li>
                             </ul>
                         </div> -->
                        <div class="col-sm-12">
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="Tab-home-v">
                                    <div id="conform_body"></div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label">Status</label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('po_status', array('' => 'Please Select','1' => 'Approved', '2' => 'Referred-back'), '', 'class="form-control" id="po_status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                            <input type="hidden" name="cpsAutoID" id="cpsAutoID">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label">Comments</label>

                                        <div class="col-sm-8">
                                            <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                                <div class="tab-pane hide" id="Tab-profile-v">
                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong>Customer Sales Price Attachments</strong>
                                        <br><br>
                                        <table class="table table-striped table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>File Name</th>
                                                <th>Description</th>
                                                <th>Type</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="po_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center">No Attachment Found</td>
                                            </tr>
                                            </tbody>
                                        </table>
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

<script>
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/invoices/customer_price_setup_approval','','Customer Price Setup');
        });
        CustomerPriceSetup_table();

        $('#CustomerPriceSetup_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: 'Status is required.'}}},
                Level: {validators: {notEmpty: {message: 'Level Order Status is required.'}}},
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},

                documentApprovedID: {validators: {notEmpty: {message: 'Document Approved ID is required.'}}}
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
                url: "<?php echo site_url('Customer/save_CustomerPriceSetup_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data == true){
                        $("#jv_modal").modal('hide');
                        CustomerPriceSetup_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function CustomerPriceSetup_table() {
        var Otable = $('#customer_PriceSetup_approval').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Customer/customer_PriceSetup_approval'); ?>",
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
                {"mData": "cpsAutoID"},
                {"mData": "documentSystemCode"},
                {"mData": "customerSystemCode"},
                {"mData": "documentDate"},
                {"mData": "narration"},
                {"mData": "edit"}
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

    function fetch_approval(cpsAutoID, documentApprovedID, Level) {
        if (cpsAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'cpsAutoID': cpsAutoID, 'html': true,'size':1},
                url: "<?php echo site_url('Customer/load_Customer_PriceConfirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#cpsAutoID').val(cpsAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#jv_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                  //  dispatch_note_attachment_View_modal('BBDPN', dispatchAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function documentPageView_modal(documentID, para1, para2, approval=1) {

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

            case "CPS": // Rental Item Issue
                siteUrl = "<?php echo site_url('Customer/load_Customer_PriceConfirmation'); ?>";
                paramData.push({name: 'cpsAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Customer Price Setup";
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

<?php
