<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');

?>
    <style>

        #search_fam_cancel img {
            background-color: #f3f3f3;
            border: solid 1px #dcdcdc;
            padding: 3px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }

        #profileInfoTable tr td:first-child {
            color: #095db3;
        }

        #profileInfoTable tr td:nth-child(2) {
            /* font-weight: bold;*/
        }

        #recordInfoTable tr td:first-child {
            color: #095db3;
        }

        #recordInfoTable tr td:nth-child(2) {
            font-weight: bold;
        }

        .title {
            color: #aaa;
            padding: 4px 10px 0 0;
            font-size: 13px;
        }

        .nav-tabs > li > a {
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            position: relative;
            padding: 0 25px;
            float: left;
            display: block;
            /*color: rgb(44, 83, 158);*/
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
        }

        .nav-tabs > li > a:hover {
            background: rgb(230, 231, 234);
            font-size: 12px;
            line-height: 30px;
            height: 30px;
            position: relative;
            padding: 0 25px;
            float: left;
            display: block;
            /*color: rgb(44, 83, 158);*/
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            border-radius: 3px 3px 0 0;
            border-color: transparent;
        }

        .nav-tabs > li.active > a,
        .nav-tabs > li.active > a:hover,
        .nav-tabs > li.active > a:focus {
            color: #50749f;
            cursor: default;
            background-color: #fff;
            font-weight: bold;
            border-bottom: 3px solid #638bbe;
        }

        ul.circleUl {
            list-style-type: circle;
            font-size: 14px;
            font-weight: inherit;
            font-style: italic;
        }
        li.merr-item {
            margin:0 0 10px 0;
        }
    </style>
    <!-- Morris.js charts -->

    <script src="<?php echo base_url('plugins/morris/morris.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/community_ngo/raphael-min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/morris/morris.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">

<?php
if (!empty($masterCom)) {

    ?>

    <div class="no_padding col-md-12" style="padding: 2px;">
        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <!-- zakat-->
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_mahallas_zakat_progress');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">

                        <?php if(!empty($zakatBeneficiary)){ ?>
                            <div>
                                <div class="box box-info collapsed-box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"
                                            style="font-size: 12px;font-weight: bold;"><?php echo $this->lang->line('comNgo_dash_total_beneficiary_families');?> &nbsp;&nbsp;&nbsp;&nbsp;<span class="pull-right badge bg-gradient-light"><?php echo $totFemAddedtoBen['totFemAddedBen']; ?></span></h3>

                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <ul class="chart-responsive circleUl">
                                            <li class="merr-item">
                                                <div class="bg-success"><span style="color: transparent;">...</span> <?php echo $this->lang->line('common_confirmed');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge" style="text-align: center;font-weight: bolder;"> <?php
                                                        echo $totConfirmed['totConfirmed']; ?> </span>
                                                    <span onclick="fetch_beneficiaryDel(<?php echo $totConfirmed['projectID']; ?>,1,<?php echo '\''.$totConfirmed['documentSystemCode'].'\''; ?>,<?php echo '\''.$totConfirmed['projectName'].'\''; ?>);" style="float: right;font-size: 12px;color: #0099CC;" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></div>
                                            </li>
                                            <li class="merr-item">
                                                <div class="bg-danger"><span style="color: transparent;">...</span> <?php echo $this->lang->line('common_not_confirmed');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge" style="text-align: center;font-weight: bolder;"><?php echo $totNotConfirmed['totNotConfirmed']; ?> </span><span onclick="fetch_beneficiaryDel(<?php echo $totConfirmed['projectID']; ?>,2,<?php echo '\''.$totConfirmed['documentSystemCode'].'\''; ?>,<?php echo '\''.$totConfirmed['projectName'].'\''; ?>);" style="float: right;font-size: 12px;color: #0099CC;" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->

                                <div class="box box-success collapsed-box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"
                                            style="font-size: 12px;font-weight: bold;"><?php echo $this->lang->line('comNgo_dash_project_proposals');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pull-right badge bg-gradient-light"><?php echo $totZakatProposals['totZakatProposal']; ?></span></h3>

                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div id="" class="table-responsive" style="overflow-x:auto; margin: 1%;">
                                            <table id="" class="nowrap" cellspacing="0" width="100%"
                                                   border="1"
                                                   style="border: 1px; border-collapse: collapse;font-size: 13px;">
                                                <thead>
                                                <tr style="background-color: #81dce4;font-size: 11px;font-weight: bold;border-top: 1px solid #ffffff;">
                                                    <th>#</th>
                                                    <th><?php echo $this->lang->line('comNgo_dash_code');?></th>
                                                    <th><?php echo $this->lang->line('comNgo_dash_proposal_description');?></th>
                                                    <th><?php echo $this->lang->line('comNgo_dash_confirm');?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $zk =1;
                                                foreach($ZakatProposeList as $row_propose) {

                                                    $qualifiedFamilies = $this->db->query("Select COUNT(ppd.proposalBeneficiaryID) AS zakFamilies,SUM(ppd.totalEstimatedValue) AS totZakatAmount from srp_erp_ngo_projectproposalbeneficiaries ppd WHERE ppd.`proposalID` = {$row_propose['proposalID']} ORDER BY `proposalBeneficiaryID` DESC")->row_array();

                                                    ?>
                                                    <tr>
                                                        <td><?php echo $zk; ?></td>
                                                        <td><?php echo $row_propose['documentSystemCode']; ?></td>
                                                        <td><div class="contact-box">
                                                                <div class="link-box">
                                                                    <strong class="contacttitle"><?php echo $this->lang->line('comNgo_dash_proposal_name');?> : </strong><a style="color: black;" class="link-person noselect"><?php echo $row_propose['proposalName']; ?></a>
                                                                    <br><strong class="contacttitle"><?php echo $this->lang->line('comNgo_dash_document_date');?> : </strong><a style="color: black;" class="link-person noselect"><?php echo $row_propose['DocumentDate']; ?></a>
                                                                    <br><strong class="contacttitle"><?php echo $this->lang->line('comNgo_dash_qualified_families');?> : <?php echo $qualifiedFamilies['zakFamilies'] ?> </strong>  <a class="link-person noselect"> </a><br><strong class="contacttitle">Committed Amount (LKR) : <a href="#" onclick="fetch_zakatAmntDel_modal(<?php echo $row_propose['proposalID']; ?>,<?php echo '\''.$row_propose['projectID'].'\''; ?>,<?php echo '\''.$row_propose['documentSystemCode'].'\''; ?>,<?php echo '\''.$row_propose['proposalTitle'].'\''; ?>)"><?php echo number_format($qualifiedFamilies['totZakatAmount'],2)?></a></strong><a class="link-person noselect" href="#"> </a>
                                                                </div>
                                                            </div></td>
                                                        <td><?php echo confirmation_status($row_propose['confirmedYN']); ?></td>

                                                    </tr>
                                                    <?php $zk++; } ?>


                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        <?php }else{ ?>

                            <div>
                                <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                            </div>

                        <?php } ?>


                    </div>

                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">
                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_community_economic_status');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">
                        <?php if(!empty($loadEconState)){ ?>
                            <div class="chart">
                                <canvas id="EconomicStatusDiv" style=""></canvas>
                            </div>
                        <?php }else{ ?>

                            <div class="well well-sm" style="margin-bottom: 0px; text-align: center;">
                                <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                            </div>

                        <?php } ?>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">

                <div class="box box-primary" style="margin-bottom: 12px;">
                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_rental_items_status');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<li class="fa fa-circle-o text"> <?php echo $this->lang->line('common_total');?>:</li> <span><input id="rentTotal" value="" style="border: none;font-weight: bold;"></span>
                                <br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="fetch_rentalDel_modal(1);"><i class="fa fa-cart-arrow-down" style="font-size:49px;color:#088da5;">  </i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<li class="fa fa-circle-o text"> <?php echo $this->lang->line('comNgo_dash_products_goods');?>:</li> <span><input id="prGoodsTotal" value="" style="border: none;font-weight: bold;"></span></span>
                                <br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick="fetch_rentalDel_modal(2);"><i class="fa fa-cubes" style="font-size:40px;color:#20b2aa;"></i> &nbsp;&nbsp;&nbsp;<li class="fa fa-circle-o text"> <?php echo $this->lang->line('comNgo_dash_fixed_assets');?>:</li> <span><input id="assetTotal" value="" style="border: none;font-weight: bold;"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">

                        <!-- THE Status -->
                        <div class="chart-responsive">
                            <div class="chart" id="donut_forMah_rentals" style="height: 175px; position: relative;"></div>
                        </div>
                        <!-- /.row -->

                    </div>

                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">

                <div class="box box-primary" style="margin-bottom: 12px;">
                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_renting_process');?></h3><div class="box-tools pull-right"><span onclick="fetch_comRentingData();" style="float: right;font-size: 12px;color: #0099CC;" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></div></div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div>
                                <li class="fa fa-circle-o text"> <?php echo $this->lang->line('comNgo_dash_total_renting');?> &nbsp;&nbsp; :</li> <span><input id="rentingProTotal" value="" style="border: none;font-weight: bold;"></span>

                                <br>
                                <li class="fa fa-circle-o text-green"> <?php echo $this->lang->line('comNgo_dash_returned');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</li> <span><input id="rentingReturnDiv" value="" style="border: none;font-weight: bold;"></span>
                                <br>
                                <li class="fa fa-circle-o text-red"> <?php echo $this->lang->line('comNgo_dash_not_return');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</li> <span><input id="rentingNtReturnDiv" value="" style="border: none;font-weight: bold;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if(!empty($rentingTot)){ ?>
                            <div class="chart">
                                <canvas id="donut_forMah_rentingPro" style=""></canvas>
                            </div>
                        <?php }else{ ?>

                            <div class="well well-sm" style="margin-bottom: 0px; text-align: center;">
                                <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                            </div>

                        <?php } ?>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
}
?>

    <div class="modal fade" id="bene_memDiv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="beneMem_title"><?php echo $this->lang->line('comNgo_dash_beneficiary_families');?></h4>
                </div>
                <form class="form-horizontal" id="bene_memDiv_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewbene_view" class="active"><a href="#beneHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewbene_attachment"><a href="#benePro-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="beneHome-m">
                                            <div id="load_bene_memDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="benePro-m">
                                            <div id="loadPageBeneAttachment" class="col-md-8">
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

    <div class="modal fade" id="rental_memDiv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="rentalMem_title"><?php echo $this->lang->line('comNgo_dash_rentals');?></h4>
                </div>
                <form class="form-horizontal" id="rental_memDiv_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewRent_view" class="active"><a href="#rentHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewRentalAttachment"><a href="#RentProfile-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="rentHome-m">
                                            <div id="load_rental_memDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="RentProfile-m">
                                            <div id="loadPageRentAttachment" class="col-md-8">
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

    <div class="modal fade" id="zakatAmnt_memDiv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="zakatAmnt_title"><?php echo $this->lang->line('comNgo_dash_zakat');?></h4>
                </div>
                <form class="form-horizontal" id="zakatAmnt_memDiv_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewZakatAmnt_view" class="active"><a href="#zakatAmntHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewZakatAmntAttachment"><a href="#zakatAmnt-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="zakatAmntHome-m">
                                            <div id="load_zakatAmnt_memDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="zakatAmnt-m">
                                            <div id="loadPageZakatAttachment" class="col-md-8">
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

    <div class="modal fade" id="renting_memDiv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="renting_title"><?php echo $this->lang->line('comNgo_dash_renting_process');?></h4>
                </div>
                <form class="form-horizontal" id="renting_memDiv_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewRenting_view" class="active"><a href="#rentingHome-m" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewRentingAttachment"><a href="#renting-m" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="rentingHome-m">
                                            <div id="load_renting_memDiv" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="renting-m">
                                            <div id="loadPageRentingAttachment" class="col-md-8">
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

    <script type="text/javascript">

        //rental donut chart

        $(function () {

            <?php
            $prGoodsPecrnt = round((($prGoods/$rentTotals)*100),0);
            $assetsPecrnt = round((($assets/$rentTotals)*100), 0);

            ?>

            document.getElementById('rentTotal').value =  '<?php echo $rentTotals; ?>';
            document.getElementById('prGoodsTotal').value = '<?php echo $prGoods.' ('.$prGoodsPecrnt.'% )'; ?>';
            document.getElementById('assetTotal').value = '<?php echo $assets .' ('.$assetsPecrnt.'% )'; ?>';
            //DONUT CHART
            var donut = new Morris.Donut({
                element: 'donut_forMah_rentals',
                resize: true,
                colors: ["#088da5","#20b2aa"],
                data: [
                    {label: "Products / Goods (%)", value: '<?php echo $prGoodsPecrnt; ?>'},
                    {label: "Fixed Assets (%)", value: '<?php echo $assetsPecrnt; ?>'}

                ],
                hideHover: 'auto'
            });

        });

        //end of rental donut chart

        //start  Econ State
        <?php if(!empty($loadEconState)){ ?>
        $(function () {
          
            var areaChartDataEcon = {
                labels: [<?php foreach(array_reverse($loadEconState) as $row){echo "'".$row['EconStateDes']."'," ;} ?>],
                datasets: [
                    {
                        label: "<?php echo $this->lang->line('comNgo_dash_community_economic_status');?>",
                        fillColor: "rgba(120,211,120,0.6)",
                        strokeColor: "rgba(120,211,120,0.8)",
                        pointColor: "#3aba5a",
                        pointStrokeColor: "rgba(120,211,120,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(120,211,120,1)",
                        data: [<?php foreach(array_reverse($loadPerEconState) as $row){echo "'".$row['count']."'," ;} ?>]
                    }
                ]
            };

            var areaChartEconDel = document.getElementById('EconomicStatusDiv').getContext('2d');
            new Chart(areaChartEconDel).Line(areaChartDataEcon);


        });
        <?php } ?>

        
        function fetch_beneficiaryDel(projectID,confirmState,documentSystemCode,projectName)  {

            if(confirmState==2){
                var conState ='Not Confirmed Beneficiary Families';
            }
            else{
                var conState='Confirmed Beneficiary Families ';
            }
            $("#benePro-m").removeClass("active");
            $("#beneHome-m").addClass("active");
            $("#TabViewbene_attachment").removeClass("active");
            $("#TabViewbene_view").addClass("active");
            // attachment_View_modal(documentID, para1);
            $('#load_bene_memDiv').html('');
            var title = conState +' ( '+ projectName +' |'+ documentSystemCode +' )';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {projectID:projectID,confirmState:confirmState},
                url: "<?php echo site_url('CommunityNgoDashboard/load_beneficiary_family_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#bene_memDiv_form')[0].reset();
                    $('#bene_memDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_bene_memDiv').html(data);
                    $('#beneMem_title').html(title);
                    $('#bene_memDiv_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function fetch_rentalDel_modal(rentalState)  {

            if(rentalState==2){
                var rentState ='Fixed Assets';
            }
            else{
                var rentState='Products / Goods';
            }
            $("#RentProfile-m").removeClass("active");
            $("#rentHome-m").addClass("active");
            $("#TabViewRentalAttachment").removeClass("active");
            $("#TabViewRent_view").addClass("active");
            // attachment_View_modal(documentID, para1);
            $('#load_rental_memDiv').html('');
            var titleRent = 'Rental' +' ( '+ rentState +')';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {rentalState:rentalState},
                url: "<?php echo site_url('CommunityNgoDashboard/load_rental_family_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#rental_memDiv_form')[0].reset();
                    $('#rental_memDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_rental_memDiv').html(data);
                    $('#rentalMem_title').html(titleRent);
                    $('#rental_memDiv_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function fetch_zakatAmntDel_modal(proposalID,projectID,documentSystemCode,proposalTitle)  {


            $("#zakatAmnt-m").removeClass("active");
            $("#zakatAmntHome-m").addClass("active");
            $("#TabViewZakatAmntAttachment").removeClass("active");
            $("#TabViewzakatAmnt_view").addClass("active");
            // attachment_View_modal(documentID, para1);
            $('#load_zakatAmnt_memDiv').html('');
            var titleZakAmnt = proposalTitle +' ( '+ documentSystemCode +')';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID:proposalID,projectID:projectID},
                url: "<?php echo site_url('CommunityNgoDashboard/load_zakat_families_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#zakatAmnt_memDiv_form')[0].reset();
                    $('#zakatAmnt_memDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_zakatAmnt_memDiv').html(data);
                    $('#zakatAmnt_title').html(titleZakAmnt);
                    $('#zakatAmnt_memDiv_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }


        //renting donut chart

        <?php if(!empty($rentingTot)){ ?>
        $(function () {

            <?php
            $rentReturnPrcnt = round((($ReturnRentTot/$rentingTot)*100),0);
            $rentntReturnPrcnt = round((($notReturnRentTot/$rentingTot)*100), 0);

            ?>

            document.getElementById('rentingProTotal').value =  '<?php echo $rentingTot; ?>';
            document.getElementById('rentingReturnDiv').value = '<?php echo $ReturnRentTot.' ('.$rentReturnPrcnt.'% )'; ?>';
            document.getElementById('rentingNtReturnDiv').value = '<?php echo $notReturnRentTot .' ('.$rentntReturnPrcnt.'% )'; ?>';

            var rentingData = {

                labels: ['Returned','Not Return'],
                datasets: [
                    {
                        label: "<?php echo $this->lang->line('comNgo_dash_renting_process');?>",
                        fillColor: "rgba(60,141,188,0.7)",
                        strokeColor: "rgba(60,141,188,0.7)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(60,141,188,0.7)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,0.7)",
                        data: [<?php echo $ReturnRentTot; ?>,<?php echo $notReturnRentTot; ?>]
                    }
                ]
            };

            var rentingBarData = $("#donut_forMah_rentingPro").get(0).getContext("2d");
            var barChart = new Chart(rentingBarData);
            var rentingBarDataData = rentingData;

            var rentingBarDataOptions = {
                responsive: true,
                maintainAspectRatio: true
            };

            rentingBarDataOptions.datasetFill = false;
            barChart.Bar(rentingBarDataData, rentingBarDataOptions);


        });
        <?php } ?>
        //end of renting donut chart

        function fetch_comRentingData() {


            $("#renting-m").removeClass("active");
            $("#rentingHome-m").addClass("active");
            $("#TabViewRentingAttachment").removeClass("active");
            $("#TabViewRenting_view").addClass("active");
            // attachment_View_modal(documentID, para1);
            $('#load_renting_memDiv').html('');
            var titleRenting = '<?php echo $this->lang->line('comNgo_dash_renting_process');?>';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {},
                url: "<?php echo site_url('CommunityNgoDashboard/load_rentingPro_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#renting_memDiv_form')[0].reset();
                    $('#renting_memDiv_form').bootstrapValidator('resetForm', true);

                    $('#load_renting_memDiv').html(data);
                    $('#renting_title').html(titleRenting);
                    $('#renting_memDiv_modal').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }
    </script>

<?php
