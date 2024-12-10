<?php
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<style>
    .dd-body{
        margin-top: 50px !important;
    }
</style>
<div class="modal-header dd-body">
    <h4 class="modal-title" id="dd-documentPageViewTitle">Modal title</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-1">
                <!-- Nav tabs -->
                <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                    <li id="dd-TabViewActivation_view" class="active"><a href="#dd-home-v"
                                                                      data-toggle="tab">
                            <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                    <li id="dd-TabViewActivation_attachment">
                        <a href="#dd-profile-v" data-toggle="tab">
                            <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                    </li>
                    <li class="itemMasterSubTab_footer hide" id="dd-tab_itemMasterTabF">
                        <a href="#subItemMaster-v" data-toggle="tab">
                            <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item&nbsp;Master&nbsp;Sub--></a>
                    </li>
                    <li class="dodelivered_footer dd_delivered hide" id="dd-tab_dodelivered">
                        <a href="#dodelivered-v" data-toggle="tab">Delivered</a>
                    </li>

                </ul>
            </div>
            <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                <!-- Tab panes -->
                <div class="zx-tab-content">
                    <div class="zx-tab-pane active" id="dd-home-v">
                        <div id="dd-loaddocumentPageView" class="col-md-12"></div>
                    </div>
                    <div class="zx-tab-pane" id="dd-profile-v">
                        <div id="loadPageViewAttachment" class="col-md-8">
                            <div class="table-responsive">
                                                <span aria-hidden="true"
                                                      class="glyphicon glyphicon-hand-right color"></span>
                                &nbsp <strong>
                                    <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                <br><br>
                                <table class="table table-striped table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                        <th>
                                            <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                        <th>
                                            <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                    </tr>
                                    </thead>
                                    <tbody id="View_attachment_modal_body" class="no-padding">
                                    <tr class="danger">
                                        <td colspan="5" class="text-center">
                                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="zx-tab-pane" id="subItemMaster-v">
                        <div class="itemMasterSubTab_footer">
                            <h4>
                                <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item Master Sub--></h4>
                            <div id="itemMasterSubTab_footer_div"></div>
                        </div>
                    </div>
                    <div class="zx-tab-pane" id="dodelivered-v">
                        <div class="dodelivered_footer">
                            <h4> </h4>
                            <div id="deliveredTab_footer_div"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var  drilldown_documentID = window.localStorage.getItem('drill-down-document-id');
    var  drilldown_siteUrl = window.localStorage.getItem('drill-down-site-url');
    var  drilldown_parameterData = window.localStorage.getItem('drill-down-parameter-data');
    var  drilldown_pageTitle = window.localStorage.getItem('drill-down-page-title');
    var  drilldownmasterid = window.localStorage.getItem('drill-down-master-id');
    $(document).ready(function(){
        document.title = drilldown_pageTitle;

        $("#dd-profile-v").removeClass("active");
        $("#dd-home-v").addClass("active");
        $("#dd-TabViewActivation_attachment").removeClass("active");
        $("#dd-tab_itemMasterTabF").removeClass("active");
        $("#dd-tab_dodelivered").removeClass("active");
        $("#dd-TabViewActivation_view").addClass("active");
        drilldown_parameterData = JSON.parse(drilldown_parameterData);
        drilldown_parameterData.push({'name':'<?=$csrf['name']?>', 'value':'<?=$csrf['hash']?>'});
        drilldown_parameterData.push({'name':'html', 'value': true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: drilldown_parameterData,
            url: drilldown_siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                attachment_View_modal(drilldown_documentID,drilldownmasterid);
                $('#dd-documentPageViewTitle').html(drilldown_pageTitle);
                $('#dd-loaddocumentPageView').html(data);
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


    });
</script>
