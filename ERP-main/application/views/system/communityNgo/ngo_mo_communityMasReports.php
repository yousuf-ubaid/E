<?php
echo head_page('Community Reports', false);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();

$com_area = load_region();
$commit_mem = load_committeesMem();
$house_fam = load_houseOwnership();
$house_type = load_houseTypes();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$titleTabFam = $this->lang->line('communityngo_familyRprt');
$titleTabComt = $this->lang->line('communityngo_CommitteesRprt');

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
    <style>
        .bgc {
            background-color: #e1f1e1;
        }

        .infoComm-box {
            display: block;
            min-height: 12px;
            background: #fff;
            width: 160px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }
        .infoComm-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 45px;
            width: 50px;
            text-align: center;
            font-size: 45px;
            line-height: 50px;
            background: rgba(0, 0, 0, 0.2);
        }
        .infoComm-box-content {
            padding: 5px 10px;
            margin-left: 50px;
        }

        .infoComm-box-text {
            display: block;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .infoComm-box-number {
            display: block;
            font-weight: bold;
            font-size: 14px;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel">
    </div>

    <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
        <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
            <li class="active">
                <a href="#familyAppTap" data-toggle="tab" aria-expanded="true" onclick="switchComMemFamRprt();"><?php echo $titleTabFam;?></a>
            </li>
            <li class="">
                <a href="#committeesAppTap" data-toggle="tab" aria-expanded="false" onclick="switchComMemCommRprt();"><?php echo $titleTabComt;?></a>
            </li>
        </ul>
        <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

            <input type="text" name="switchControlId" id="switchControlId" value="4" style="display:none;">

            <div class="tab-pane active disabled" id="familyAppTap">

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>

                    <form method="post" name="form_rpt_ngoFamily" id="form_rpt_ngoFamily" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12">

                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Families</label>
                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('FamMasterID[]', fetch_familyMaster(false), '', 'multiple  class="form-control" id="FamMasterID" required'); ?>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Area</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('famAreaId', $com_area, '', 'class="form-control select2" id="famAreaId" style="height:30px;width: 180px;font-size: 13px;" required '); ?>

                                </div>
                            </div>
                            <div class="form-group col-sm-3" style="margin-bottom: 0px">
                                <?php
                                $totHouses = load_totHouses();
                                if (!empty($totHouses)) {
                                    $houseTal= $totHouses['totHouseCount1'];
                                }
                                else{
                                    $houseTal= 0;
                                }
                                ?>
                                <div class="control-label infoComm-box">
                                    <span class="infoComm-box-icon bg-aqua"><i class="fa fa-home" title="Houses"></i></span>

                                    <div class="infoComm-box-content">
                                        <span class="infoComm-box-text" style="color: #0099CC;" onclick="fetch_comHousingData();">Total Enrolled</span>
                                        <label class="infoComm-box-number" style="text-align: center;"><span class="badge" style="background-color: lightgrey;color: #006f00;font-size:13px;" id="notRntHosId" title="Total Houses"><?php echo $houseTal; ?></span></label>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>

                            </div>
                            <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                                <button type="button" class="btn btn-primary pull-left" onclick="generateFamilyReport()" name="filterDivisubmit" id="filterDivisubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12">

                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="houseOwnshp">House Ownership</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('houseOwnshp', $house_fam, '', 'class="form-control select2" id="houseOwnshp" style="height:30px;width: 180px;font-size: 13px;" required '); ?>

                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="houseType">House Type</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('houseType', $house_type, '', 'class="form-control select2" id="houseType" style="height:30px;width: 180px;font-size: 13px;" required '); ?>

                                </div>
                            </div>
                        </div>
                    </form>

                </fieldset>
            </div>

            <div class="tab-pane" id="committeesAppTap">

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>

                    <form method="post" name="form_rpt_ngoCommittee" id="form_rpt_ngoCommittee" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12">

                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('communityngo_Committees'); ?></label>
                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('CommitteeID[]', fetch_committeesMaster(false), '', 'multiple  class="form-control" id="CommitteeID" required'); ?>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Area</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('commitAreaId', $com_area, '', 'class="form-control select2" id="commitAreaId" onchange="generateCommitteeReport();" style="height:30px;width: 180px;font-size: 13px;" required '); ?>


                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Head</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('commit_memId', $commit_mem, '', 'class="form-control select2" id="commit_memId" onchange="generateCommitteeReport();" style="height:30px;width: 180px;font-size: 13px;" required '); ?>

                                </div>
                            </div>
                            <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                                <button type="button" class="btn btn-primary pull-left" onclick="generateCommitteeReport()" name="filterCommitsubmit" id="filterCommitsubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>

                    </form>
                </fieldset>
            </div>

            <hr style="margin: 0px;">
            <div id="div_comm_contents">
            </div>
        </div>
    </div>



    <div class="modal fade" id="housing_femRpt_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="houseEnr_title">Community Housing Details</h4>
                </div>
                <form class="form-horizontal" id="housing_femDiv_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewHousing_view" class="active"><a href="#femHousingHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewHousingAttachment"><a href="#femHousing-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="femHousingHome-m">
                                            <div id="load_housing_femDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="femHousing-m">
                                            <div id="loadPageHousingAttachment" class="col-md-8">
                                                <div class="table-responsive">
                                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>&nbsp; <strong><?php echo $this->lang->line('common_attachments');?><!--Attachments--></strong>
                                                    <br><br>
                                                    <table class="table table-striped table-condensed table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                                            <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                                            <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="View_attachment_modal_body" class="no-padding">
                                                        <tr class="danger">
                                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script>

        Inputmask().mask(document.querySelectorAll("input"));

        $('#FamMasterID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 180,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#FamMasterID").multiselect2('selectAll', false);
        $("#FamMasterID").multiselect2('updateButtonText');
        // $('.select2').select2();
        $('#CommitteeID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 180,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#CommitteeID").multiselect2('selectAll', false);
        $("#CommitteeID").multiselect2('updateButtonText');

        $('.headerclose').click(function () {
            fetchPage('system/communityNgo/ngo_mo_communityMasReports','','Community Report')
        });
        $(document).ready(function (e) {

            //$('.select2').select2();

        });


        function switchComMemCommRprt() {

            $("#div_comm_contents").html('');
            $('#form_rpt_ngoCommittee')[0].reset();
            $('#form_rpt_ngoFamily')[0].reset();


            $('#FamMasterID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 180,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#FamMasterID").multiselect2('selectAll', false);
            $("#FamMasterID").multiselect2('updateButtonText');
            // $('.select2').select2();
            $('#CommitteeID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 180,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#CommitteeID").multiselect2('selectAll', false);
            $("#CommitteeID").multiselect2('updateButtonText');

            document.getElementById('switchControlId').value = 2;
            document.getElementById('diviMemTypeDiv').style.display = 'none';
            document.getElementById('CommitteeIDDiv').style.display = 'block';


        }

        function switchComMemFamRprt() {

            $("#div_comm_contents").html('');
            $('#form_rpt_ngoCommittee')[0].reset();
            $('#form_rpt_ngoFamily')[0].reset();


            $('#FamMasterID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 180,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#FamMasterID").multiselect2('selectAll', false);
            $("#FamMasterID").multiselect2('updateButtonText');
            // $('.select2').select2();
            $('#CommitteeID').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 180,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#CommitteeID").multiselect2('selectAll', false);
            $("#CommitteeID").multiselect2('updateButtonText');

            document.getElementById('switchControlId').value = 4;
            document.getElementById('CommitteeIDDiv').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'block';


        }

        //member committees

        function generateCommitteeReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_committees_report') ?>",
                data: $("#form_rpt_ngoCommittee").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_contents").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateFamilyReport() {

            var FamMasterID= document.getElementById('FamMasterID').value;
            var famAreaId= document.getElementById('famAreaId').value;

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_famReport') ?>",
                data: $("#form_rpt_ngoFamily").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_contents").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/get_totalFamHousing'); ?>",
                data: $("#form_rpt_ngoFamily").serialize(),
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,

                success: function (data) {
                    $("#notRntHosId").text(data.noOfHseId);
                }
            });
        }

        function fetch_comHousingData() {


            $("#femHousing-m").removeClass("active");
            $("#femHousingHome-m").addClass("active");
            $("#TabViewHousingAttachment").removeClass("active");
            $("#TabViewHousing_view").addClass("active");
            $('#load_housing_femDiv').html('');
            var titleHousing = 'Community Housing Details';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                url: "<?php echo site_url('CommunityNgo/get_houseEnrolling_del'); ?>",
                data: $("#form_rpt_ngoFamily").serialize(),
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#housing_femDiv_form')[0].reset();
                    $('#housing_femDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_housing_femDiv').html(data);
                    $('#houseEnr_title').html(titleHousing);
                    $('#housing_femRpt_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function generateReportPdf() {

            var switchControlId= document.getElementById('switchControlId').value;

           if(switchControlId == 4){

                var form = document.getElementById('form_rpt_ngoFamily');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_famReport_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 2){

                var form = document.getElementById('form_rpt_ngoCommittee');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_committees_report_pdf'); ?>';
                form.submit();
            }


        }



    </script>

<?php
