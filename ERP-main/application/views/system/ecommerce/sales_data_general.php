<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page($title, false);
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Processed </td><!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> Not Proccessed </td><!-- Not Approved-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved') /*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="clent_general" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
           
           
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>



<div class="modal fade" id="sales_return_approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="slr_attachement_approval_Tabview_v" class="active">
                                <a href="#Tab-home-v" data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?></a><!--View-->
                            </li>
                            <li id="slr_attachement_approval_Tabview_a">
                                <a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()"> <?php echo $this->lang->line('common_attachment');?></a><!--Attachment-->
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('' =>  $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' =>  $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="salesReturnAutoID" id="salesReturnAutoID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment');?></label><!--Comments-->

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong><?php echo $this->lang->line('common_attachments');?></strong><!--Invoice Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                            <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                            <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="slr_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Scripting area -->
<script type="text/javascript">


sales_client_mapping_table();


function sales_client_mapping_table() {
            var Otable = $('#clent_general').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('DataSync/fetch_client_data'); ?>",
                "aaSorting": [[0, 'desc']],
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
                    {"mData": "id"},
                    {"mData": "company_name"},
                    // {"mData": "service_type"},
                    // {"mData": "store"},
                    // {"mData": "order"},
                    // {"mData": "order_total"},
                    // {"mData": "delivery_fee"},
                    // {"mData": "vat_delivery_fee"},
                    // {"mData": "total_bill"},
                    // {"mData": "view"},
                    // {"mData": "erp_cr_dr"},
                    // {"mData": "client_sales_header"},
                    // {"mData": "erp_column_name"},
                    // {"mData": "delete"},
                    // {"mData": "edit"}
                ],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
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


</script>