<?php
$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$segment = fetch_mfq_segment(true);
$gl_code = fetch_all_mfq_gl_codes();
$page_id = isset($page_id) && $page_id ? $page_id : 0;
$umo_arr2 = all_umo_new_drop();

$transactioncurrencyid = $this->common_data['company_data']['company_default_currencyID'];
$currencydecimalplaces = fetch_currency_desimal_by_id($transactioncurrencyid);
$openContract = getPolicyValues('SUOM', 'All');
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
echo head_page($_POST["page_name"], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
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
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .arrow-steps .step.current {
        color: #fff !important;
        background-color: #657e5f !important;
    }

    .table-responsive {
        overflow: visible !important
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
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .select2-container .select2-choice > .select2-chosen {
        min-width: 200px;
    }

    .familyContainer {
        padding: 3px;
    }

    .familyMasterContainer {
        border: 1px dashed #a3a3a3;
        margin-bottom: 10px;
        background-color: #ffffff;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    .familyImgSize {
        max-width: 180px;
        max-height: 150px;
    }

    .account-in-activate {
        color: #c5bdc2;
        text-align: center;
    }

    .account-activate {
        color: #109400;
        text-align: center;
    }

    .bankInfoContainer {
        padding: 2px;
        font-size: 12px;
        border: 1px solid rgba(215, 215, 215, 0.54);
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        margin-bottom: 10px;
    }

    .bankItem {
        padding: 2px 0px;
    }

    .hrms_imageDiv2 {
        max-height: 52px;
        max-width: 52px;
        margin-top: -17px;
        border: 1px solid rgba(0, 0, 0, 0.25);
        padding: 0px;
    }

    .hrms_imageSize2 {
        max-height: 50px !important;
        max-width: 50px !important;
    }

    .ar {
        text-align: right !important;
    }

    div.show-image {
        position: relative;

        margin: 5px;
    }

    div.show-image:hover img {
        opacity: 0.5;
    }

    div.show-image:hover button {
        display: block;
    }

    div.show-image button {
        position: absolute;
        display: none;
    }

    div.show-image button.update {
        top: 0;
        left: 0;
    }

    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    .progress {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }

    .pendingApproval {
        color: #adad45 !important;
    }

    #msg-div {
        color: red;
    / / font-weight: bold;
        display: none;
    }

    .thumbnailDoc {
        width: 100px;
        height: 110px !important;
        text-align: center;
        display: inline-block;
        margin: 0 10px 10px 0;
        float: left;
    }

</style>

<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#jobcard" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_job')?><!--Job--></a>
    </li>
    <li><a href="#crew" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_crew')?><!--Crew--> </a></li>
    <li><a href="#machine" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--> </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="jobcard">
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" id="invoiceAutoID" name="invoiceAutoID" value="<?php echo $page_id ?>">

                <?php
                $current_companyid = current_companyID();
                $detail = $this->db->query("SELECT
                        srp_erp_mfq_standardjob.*,
                        DATE_FORMAT( srp_erp_mfq_standardjob.documentDate, '%Y-%m-%d') AS ProductionDate,
                        DATE_FORMAT( srp_erp_mfq_standardjob.createdDateTime, '%Y-%m-%d') AS createdDate,
                        DATE_FORMAT( srp_erp_mfq_standardjob.expiryDate, '%Y-%m-%d') AS ExpiryDate,
                        srp_erp_mfq_warehousemaster.warehouseDescription,
                        Concat(segmentCode,' - ',description) as segmentdescription,
                        srp_erp_currencymaster.CurrencyName
                    FROM
                        `srp_erp_mfq_standardjob`
                        LEFT JOIN srp_erp_mfq_warehousemaster on srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_standardjob.warehouseID
                        LEFT JOIN srp_erp_mfq_segment on srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_standardjob.segmentID
                        LEFT JOIN srp_erp_currencymaster on srp_erp_currencymaster.currencyID = srp_erp_mfq_standardjob.transactionCurrencyID
                        where
                        srp_erp_mfq_standardjob.companyID = $current_companyid And srp_erp_mfq_standardjob.jobAutoID = $page_id  ")->row_array();

                $bom_detail = fetch_bom_detail($detail['bomID']);

                ?>

                <div class="table-responsive">
                    <table style="width: 100%">
                        <tbody>
                        <tr>
                            <td style="width:60%;">
                                <table>
                                    <tr>
                                        <td>
                                            <table>
                                                <tbody>
                                                <tr>
                                                    <td class="td"
                                                        style="text-align: center; padding-left: 0;padding-right: 0; background:#E78800;font-family: arial;color: #ffffff;font-weight: 800;">
                                                        <strong style="font-size: 17px;"><?php echo $this->lang->line('manufacturing_standard_job_card')?><!--Standard Job Card--></strong>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>

                                    <table>
                                        <tbody>
                                        <br>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('manufacturing_job_number')?><!--Job Number--> </b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $detail['documentSystemCode'] ?></td>

                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b
                                                    id="_documetdate"><?php echo $this->lang->line('common_document_date')?><!--Document Date-->
                                                    </b></td>
                                            <td>:</td>
                                            <td>
                                                <?php
                                                $expDate = '0000-00-00';
                                                $expDate2 = '0000-00-00';

                                                /*  $isPending = search_pendingData($pendingData, 'documentDate');
                                                  if ($isPending !== null) {
                                                      $expDate = format_date_dob($isPending);
                                                      $expDate2 = $isPending;
                                                      echo "<script> colorLabel('_documetdate'); </script>";
                                                  } else {*/
                                                $expDate = (empty($detail['ProductionDate'])) ? '' : $detail['ProductionDate'];
                                                $expDate = format_date($detail['ProductionDate']);
                                                $expDate2 = $detail['ProductionDate'];
                                                /* }*/
                                                ?>
                                                <a href="#" data-type="combodate" data-placement="bottom" id="documentdate" data-url="<?php echo site_url('MFQ_Job_standard/update_sj_header_details') ?>" data-pk="<?php echo $page_id ?>"
                                                   data-name="documentDate" data-title="Document Date"
                                                   class="xEditableDate"
                                                   data-value="<?php echo $expDate2 ?>"
                                                   data-related="_documetdate">
                                                    <?php echo $detail['ProductionDate']; ?>
                                                </a>
                                            </td>


                                            <!--  <td style="font-size: 11px;">

                                       <?php /*echo $detail['ProductionDate'] */ ?>




                                    </td>-->

                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('common_warehouse')?><!--Ware House--> </b>
                                            </td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $detail['warehouseDescription'] ?></td>

                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('common_segment')?><!--Segment--></b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $detail['segmentdescription'] ?></td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('common_created_date')?><!--Created Date--> </b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $detail['createdDate'] ?></td>

                                            <!-- <td style="font-size: 11px;font-weight: 800;width: 13%"><b>Expiry
                                            Date</b></td>
                                    <td>:</td>
                                    <td style="font-size: 11px;"><?php /*echo $detail['ExpiryDate'] */ ?></td>
-->
                                            <td style="font-size: 11px;font-weight: 800;width: 13%">
                                                <b><?php echo $this->lang->line('common_currency')?><!--Currency--> </b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><?php echo $detail['CurrencyName'] ?></td>
                                        </tr>


                                        <tr>
                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('manufacturing_batch_number')?><!--Batch Number--> </b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;">

                                                <a href="#" data-type="text" data-placement="bottom"
                                                   data-url="<?php echo site_url('MFQ_Job_standard/update_sj_header_details_batchno') ?>"
                                                   data-pk="<?php echo $page_id ?>"
                                                   data-title="Batch Number" class="xEditable"
                                                   data-value="<?php echo $detail['batchNumber'] ?>"
                                                   data-related="_batchno">
                                                    <?php echo $detail['batchNumber'] ?>
                                                </a>

                                            </td>

                                            <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('manufacturing_progress')?><!--Progress--></b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;"><input id="progress" data-slider-id='ex1Slider'
                                                                                type="text" data-slider-min="0"
                                                                                data-slider-max="100"
                                                                                data-slider-step="1"
                                                                                data-slider-value="<?php echo $detail['completionPercenatage'] ?>"
                                                                                name="progress"/>&nbsp; <strong><label
                                                        id="progressprecentage"> <?php echo $detail['completionPercenatage'] ?>
                                                        % </label> </strong></td>
                                        </tr>

                                        <tr>

                                            <td style="font-size: 11px;font-weight: 800;width: 13%">
                                                <b><?php echo $this->lang->line('common_narration')?><!--Narration--> </b></td>
                                            <td>:</td>
                                            <td style="font-size: 11px;">

                                                <a href="#" data-type="text" data-placement="bottom"
                                                   data-url="<?php echo site_url('MFQ_Job_standard/update_sj_header_details_narration') ?>"
                                                   data-pk="<?php echo $page_id ?>"
                                                   data-title="Narration" class="xEditable"
                                                   data-value="<?php echo $detail['narration'] ?>"
                                                   data-related="_narration">
                                                    <?php echo $detail['narration'] ?>
                                                </a>

                                                <?php /*echo $detail['narration'] */ ?></td>

                                        </tr>
                                        <?php if($bom_detail) { ?>
                                            <tr>
                                                <td style="font-size: 11px;font-weight: 800;width: 13%"><b><?php echo $this->lang->line('common_bom_number')?><!--Ware House--> </b>
                                                </td>
                                                <td>:</td>
                                                <td style="font-size: 11px;"><?php echo $bom_detail['documentCode'] ?></td>

                                            </tr>
                                        <?php } ?>


                                        </tbody>
                                    </table>

                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <?php echo form_open('', 'role="form" id="standard_job_cardform"'); ?>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-12">
                        <input type="hidden" id="standardjobcardAutoid" name="standardjobcardAutoid"
                               value="<?php echo $page_id ?>">
                        <div class="table-responsive">
                            <table style="width: 100%">
                                <tbody>
                                <tr>
                                    <td class="td"
                                        style="text-align: center; padding-left: 0;padding-right: 0; background:#c1c1c1;font-family: arial;color: #ffffff;font-weight: 800;">
                                        <strong style="font-size: 14px;"><?php echo $this->lang->line('manufacturing_input')?><!--Input--></strong></td>
                                </tr>
                                </tbody>
                            </table>

                            <table style="width: 100%">
                                <tbody>
                                <br>
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_raw_material')?><!--Raw Material--></h2>
                                </header>

                                <table id="mfq_standard_job_card"
                                       class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_item_description')?><!--Item Description--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UOM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_qty')?><!--Qty--></th>
                                        <?php if($openContract == 1) { ?>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_suom')?><!--SUOM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_secondary_qty')?><!--Secondary Qty--></th>
                                        <?php } ?>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost')?><!--Unit Cost--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_cost')?><!--Total Cost--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add" id="btnAdd"
                                                                        onclick="add_more_row()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="standard_job_card_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <?php if($openContract == 1) { ?> <td colspan="6"> <?php } else { ?> <td colspan="4"><?php } ?>
                                        
                                            <div class="text-right"><?php echo $this->lang->line('common_total')?><!--Total--></div>
                                        </td>
                                        <td>
                                            <div id="tot_totalValue" style="text-align: right">0.00</div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <br>
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_labour')?><!--Labour--></h2>
                                </header>

                                <table id="mfq_labour_task" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_labour_tasks')?><!--Labour Tasks--></th>
                                        <!--<th style="min-width: 12%">Activity Code</th>-->
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UOM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_amount')?><!--Total Amount--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_labour()"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="labour_task_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-right"><?php echo $this->lang->line('common_total')?></div>
                                        </td>

                                        <td>
                                            <div id="tot_totalValue_labour" style="text-align: right">0.00</div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <br>
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_overhead')?><!--Over Head--></h2>
                                </header>

                                <table id="mfq_overhead_task" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_overhead')?><!--Over Head--></th>
                                        <!--<th style="min-width: 12%">Activity Code</th>-->
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UOM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_amount')?><!--Total Amount--></th>

                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_overhead_cost()"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="overheadcost_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-right"><?php echo $this->lang->line('common_total')?></div>
                                        </td>

                                        <td>
                                            <div id="tot_totalValue_overhead" style="text-align: right">0.00</div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                        </div>

                        <br>
                        <br>
                        <div class="row">
                            <div class="table-responsive">
                                <div class="col-md-12" style="font-size:15px;color: #4a8cdb">
                                    <div class="col-md-12" style="text-align: right;"><strong><?php echo $this->lang->line('manufacturing_total_input')?></strong>&nbsp;
                                        <span id="grandtotalinput">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="form-group col-sm-12">
                        <input type="hidden" id="standardjobcardAutoid" name="standardjobcardAutoid"
                               value="<?php echo $page_id ?>">
                        <div class="table-responsive">
                            <table style="width: 100%">
                                <tbody>
                                <tr>
                                    <td class="td"
                                        style="text-align: center; padding-left: 0;padding-right: 0; background:#c1c1c1;font-family: arial;color: #ffffff;font-weight: 800;">
                                        <strong style="font-size: 14px;"><?php echo $this->lang->line('manufacturing_output')?></strong></td>
                                </tr>
                                </tbody>
                            </table>

                            <table style="width: 100%">
                                <tbody>
                                <br>
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_finish_goods')?></h2>
                                </header>

                                <table id="mfq_standard_job_card_output"
                                       class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_item_description')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_warehouse')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_expire_date')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_qty')?></th>
                                        <?php if($openContract == 1) { ?>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_suom')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_secondary_qty')?></th>
                                        <?php } ?>
                                        <th style="min-width: 12%">%</th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_cost')?></th>

                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add" id="btnAdd"
                                                                        onclick="add_more_row_output()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="standard_job_card_body_output">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <?php if($openContract == 1) { ?> <td colspan="9"> <?php } else { ?> <td colspan="7"><?php } ?>
                                            <div class="text-right"><?php echo $this->lang->line('common_total')?></div>
                                        </td>
                                        <td>
                                            <div id="tot_totalValue_output" style="text-align: right">0.000</div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                        </div>
                        </form>
                        <br>
                        <br>

                        <div class="row">
                            <div class="table-responsive">
                                <div class="col-md-12" style="font-size:15px;color: #4a8cdb">
                                    <div class="col-md-12" style="text-align: right ;"><strong><?php echo $this->lang->line('manufacturing_total_output')?></strong>&nbsp;
                                        <span id="grandtotaloutput">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
                <div class="text-right m-t-xs" style="margin-top: 10px;">
                    <button class="btn btn-primary" type="button" onclick="save_standard_jobcard_input()"><?php echo $this->lang->line('common_save')?></button>
                    <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm')?></button>
                </div>
                <!--  <div class="text-right m-t-xs" style="margin-top: 10px;">
                      <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
                  </div>-->
            </div>
        </div>
    </div>
    <div class="tab-pane" id="machine">
        <br>
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_machine')?></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_machine">
                            <input type="hidden" name="jobcardid" id="jobcardid"
                                   value="<?php echo $page_id ?>">
                            <div class="table-responsive">
                                <table id="mfq_machine_<?php echo $page_id ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_machine')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_description')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_start_time')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_end_time')?></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_hours_spent')?></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_machine_sj(<?php echo $page_id ?>)"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="machine_body">

                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary-new size-lg" type="button"
                                                onclick="save_machine(<?php echo $page_id ?>)">
                                            <?php echo $this->lang->line('common_save')?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="crew">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2> <?php echo $this->lang->line('manufacturing_crew')?></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_crew">
                            <input type="hidden" name="jobcardid" id="jobcardid"
                                   value="<?php echo $page_id ?>">
                            <div class="table-responsive">
                                <table id="mfq_crew_<?php echo $page_id ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"> <?php echo $this->lang->line('common_name')?></th>
                                        <th style="min-width: 12%"> <?php echo $this->lang->line('common_designation')?></th>
                                        <th style="min-width: 12%"> <?php echo $this->lang->line('common_start_time')?></th>
                                        <th style="min-width: 12%"> <?php echo $this->lang->line('common_end_time')?></th>
                                        <th style="min-width: 12%"> <?php echo $this->lang->line('common_hours_spent')?></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_crew_sj(<?php echo $page_id ?>)"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="crew_body">

                                    </tbody>
                                </table>
                            </div>
                            <br>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary-new size-lg" type="button"
                                                onclick="save_crew(<?php echo $page_id ?>)">
                                             <?php echo $this->lang->line('common_save')?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script src="<?php echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); ?>"></script>

<script>
    var openContract = '<?php echo $openContract ?>';
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var search_id = 1;
    var search_id2 = 1;
    var search_id3 = 1;
    var search_id4 = 1;
    var search_id6 = 1;
    var search_id7 = 1;
    var invoiceAutoID = "";
    var deliveryNoteID = "";
    var qty = '0.00';
    var unitPrice = '0.00';
    var currency_decimal = <?php echo $currencydecimalplaces?>;
    var standardjobcard;
    var originalVal;
    var totalinput = 0;
    $(document).ready(function () {
        $('.xEditable').editable({
            success: function () {
                colorLabel($(this).data('related'));
            }
        });

        $('.xEditableDate').editable({
            format: 'YYYY-MM-DD',
            viewformat: 'YYYY.MM.DD',
            template: 'YYYY / MMMM / D ',
            combodate: {
                minYear: 1930,
                maxYear: <?php echo format_date_getYear() + 10 ?>,
                minuteStep: 1
            },
            success: function (response) {
                colorLabel($(this).data('related'));
                var thisID = $(this).attr('id');
                if (thisID == 'dob' || thisID == 'visaExpiryDate') {
                    var dataArr = JSON.parse(response);
                    setTimeout(function () {
                        $('#' + thisID).text(dataArr[2]);
                    }, 300);
                }
            }
        });
        $('.select2').select2();
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_standard_job_card', '', 'Standard Job Card');
        });

        $('#progress').slider({
            formatter: function (value) {
                return 'Current value: ' + value + '%';
            }
        }).slider('enable');

        Inputmask().mask(document.querySelectorAll("input"));
        <?php
        if ($page_id) {
        ?>
        load_standard_job_card('<?php echo $page_id  ?>');
        load_standard_job_card_labour('<?php echo $page_id  ?>');
        load_standard_job_card_overhead('<?php echo $page_id  ?>');
        load_standard_job_card_output('<?php echo $page_id  ?>');
        load_standard_job_card_machine('<?php echo $page_id  ?>');
        load_standard_job_card_crew('<?php echo $page_id  ?>');

        <?php
        }else{
        ?>
        $('.btn-wizard').addClass('disabled');
        init_standardjobcardform();
        initializestandardjobTypeahead(1);
        init_standardjobcardform_labour();
        initializestandardjobTypeahead_labour(1);
        init_standardjobcardform_overhead();
        initializestandardjobTypeahead_overhead(1);
        init_standardjobcardform_output();
        initializestandardjobTypeahead_output(1);
        standardjobcard = <?php echo $page_id  ?>;
        initi_standardjc_machine_crew(standardjobcard);
        //initializCrewTypeahead(1 , standardjobcard);
        <?php
        }
        ?>
        rawmaterialCostTotal();
        labourcosttoal();
        overheadcosttoal();
        calculateTotalCost();
        OutPutfinishgood();
        calculateTotalCostoutput();
        standardjobcard = <?php echo $page_id  ?>;
        initi_standardjc_machine(standardjobcard);
        //initi_standardjc_machine_crew(standardjobcard);


        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.remove-tr2', function () {
            $(this).closest('tr').remove();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $("#mfqCustomerAutoID").change(function () {
            get_delivery_note($(this).val())
        });

        $("#deliveryNoteID").change(function () {
            qty = $(this).find(":selected").data("qty");
            unitPrice = $(this).find(":selected").data("unitprice");
            var comment = $(this).find(":selected").data("description") + " " + $(this).find(":selected").data("jobno");
            $("#invoiceNarration").val(comment);

            if ($(this).val() == "") {
                $('#job_item_body').html('<tr class="danger"><td colspan="5" class="text-center"><b>No Records Found </b></td></tr>');
            } else {
                $('#job_item_body').html('');
                $('#job_item_body').append('<tr> <td>' + $(this).find(":selected").data("itemdescription") + '<input type="hidden" class="form-control itemInvoiceDetailsAutoID" name="itemInvoiceDetailsAutoID" value=""><input type="hidden" class="form-control itemAutoID" name="itemAutoID" value="' + $(this).find(":selected").data("itemautoid") + '"></td> <td>' + $(this).find(":selected").data("uom") + '</td> <td><input type="text" name="expectedQty" value="' + qty + '"  onkeyup="calculateTotal(this)"  class="form-control number requestedQty" onfocus="this.select();" readonly> </td> <td><input type="text" name="unitRate" value="' + unitPrice + '"  onkeyup="calculateTotal(this)" onkeypress="return" class="form-control number amount" onfocus="this.select();"> </td> <td class="text-right" style="vertical-align: middle"> <span class="totalAmount">' + commaSeparateNumber((qty * unitPrice), 2) + '</span></td> </tr>');
            }

        });
    });


    $('#startDate_machine_' + standardjobcard + '_' + search_id6).datetimepicker({
        showTodayButton: true,
        format: date_format_policy + " hh:mm A",
        sideBySide: false,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        }
    }).on('dp.change', function (ev) {
        var start_time = $(this).closest('tr').find('.txtstartTime').val(),
            end_time = $(this).closest('tr').find('.txtendTime').val(),
            d1 = moment(start_time, date_format_policy + " HH:mm"),
            d2 = moment(end_time, date_format_policy + " HH:mm"),
            duration = d2.diff(d1, 'hours');
        if ($.isNumeric(duration)) {
            $(this).closest('tr').find('.hoursSpent').val(duration);
        } else {
            $(this).closest('tr').find('.hoursSpent').val(0);
        }
    });

    $('#endDate_machine_' + standardjobcard + '_' + search_id6).datetimepicker({
        showTodayButton: true,
        format: date_format_policy + " hh:mm A",
        sideBySide: false,
        useCurrent: false,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        }
    }).on('dp.change', function (ev) {
        var start_time = $(this).closest('tr').find('.txtstartTime').val(),
            end_time = $(this).closest('tr').find('.txtendTime').val(),
            d1 = moment(start_time, date_format_policy + " HH:mm"),
            d2 = moment(end_time, date_format_policy + " HH:mm"),
            duration = d2.diff(d1, 'hours');
        if ($.isNumeric(duration)) {
            $(this).closest('tr').find('.hoursSpent').val(duration);
        } else {
            $(this).closest('tr').find('.hoursSpent').val(0);
        }
    });

    $('#startDate_machine_' + standardjobcard + '_' + search_id6).on('dp.change', function (e) {
        var d_id = $(this).data('id');
        $('#' + d_id).data('DateTimePicker').minDate(e.date);
        $(this).data("DateTimePicker").hide();
    });

    $('#endDate_machine_' + standardjobcard + '_' + search_id6).on('dp.change', function (e) {
        var d_id = $(this).data('id');
        $('#' + d_id).data('DateTimePicker').maxDate(e.date);
        $(this).data("DateTimePicker").hide();
    });
    $('#startDate_crew_' + standardjobcard + '_' + search_id7).datetimepicker({
        showTodayButton: true,
        format: date_format_policy + " hh:mm A",
        sideBySide: false,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        }
    }).on('dp.change', function (ev) {
        var start_time = $(this).closest('tr').find('.txtstartTime').val(),
            end_time = $(this).closest('tr').find('.txtendTime').val(),
            d1 = moment(start_time, date_format_policy + " HH:mm"),
            d2 = moment(end_time, date_format_policy + " HH:mm"),
            duration = d2.diff(d1, 'hours');
        if ($.isNumeric(duration)) {
            $(this).closest('tr').find('.hoursSpent').val(duration);
        } else {
            $(this).closest('tr').find('.hoursSpent').val(0);
        }
    });

    $('#endDate_crew_' + standardjobcard + '_' + search_id7).datetimepicker({
        showTodayButton: true,
        format: date_format_policy + " hh:mm A",
        sideBySide: false,
        useCurrent: false,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        }
    }).on('dp.change', function (ev) {
        var start_time = $(this).closest('tr').find('.txtstartTime').val(),
            end_time = $(this).closest('tr').find('.txtendTime').val(),
            d1 = moment(start_time, date_format_policy + " HH:mm"),
            d2 = moment(end_time, date_format_policy + " HH:mm"),
            duration = d2.diff(d1, 'hours');
        if ($.isNumeric(duration)) {
            $(this).closest('tr').find('.hoursSpent').val(duration);
        } else {
            $(this).closest('tr').find('.hoursSpent').val(0);
        }
    });

    $('#startDate_crew_' + standardjobcard + '_' + search_id7).on('dp.change', function (e) {
        var d_id = $(this).data('id');
        $('#' + d_id).data('DateTimePicker').minDate(e.date);
        $(this).data("DateTimePicker").hide();
    });

    $('#endDate_crew_' + standardjobcard + '_' + search_id7).on('dp.change', function (e) {
        var d_id = $(this).data('id');
        $('#' + d_id).data('DateTimePicker').maxDate(e.date);
        $(this).data("DateTimePicker").hide();
    });
    function initializestandardjobTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_standard/fetch_mfq_standard_item/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemautoid').val(suggestion.itemAutoID);
                }, 200);
                $('#f_search_' + id).closest('tr').find('.unitcost').val(suggestion.wacamount);
                $('#f_search_' + id).closest('tr').find('.UnitOfMeasureID').val(suggestion.defaultUnitOfMeasureID);
                $('#f_search_' + id).closest('tr').find('.UOM').val(suggestion.uomdescription);
                if(openContract == 1){
                    $('#f_search_' + id).closest('tr').find('.SecondaryUnitOfMeasureID').val(suggestion.secondaryUOMID);
                    $('#f_search_' + id).closest('tr').find('.SUOM').val(suggestion.suomDes);
                }

                //getJobQty(this);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }
    function initializestandardjobTypeahead_output(id) {
        $('#f_search4_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_standard/fetch_mfq_standard_item_output/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search4_' + id).closest('tr').find('.itemautoidoutput').val(suggestion.itemAutoID);
                }, 200);
                //$('#f_search4_' + id).closest('tr').find('.unitcostoutput').val(suggestion.wacamount);
                $('#f_search4_' + id).closest('tr').find('.UnitOfMeasureIDoutput').val(suggestion.defaultUnitOfMeasureID);
                $('#f_search4_' + id).closest('tr').find('.UOMoutput').val(suggestion.uomdescription);
                $('#f_search4_' + id).closest('tr').find('.assetglautoid').val(suggestion.GLAutoID);
                $('#f_search4_' + id).closest('tr').find('.warehouseitemtype').val(suggestion.warehouseitemtype);
                if(openContract == 1) {
                    $('#f_search4_' + id).closest('tr').find('.SecondaryUnitOfMeasureIDoutput').val(suggestion.secondaryUOMID);
                    $('#f_search4_' + id).closest('tr').find('.SUOMoutput').val(suggestion.suomDes);
                }
                fetch_related_warehouse(suggestion.warehouseitemtype, this);
                //getJobQty(this);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }
    function initializestandardjobTypeahead_labour(id) {
        $('#f_search2_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_standard/fetch_mfq_standard_labourtask/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search2_' + id).closest('tr').find('.labourautoid').val(suggestion.overHeadID);
                }, 200);
                $('#f_search2_' + id).closest('tr').find('.UnitOfMeasureIDLabour').val(suggestion.uom);
                $('#f_search2_' + id).closest('tr').find('.UOMLabour').val(suggestion.UnitDes);
                $('#f_search2_' + id).closest('tr').find('.unitrate').val(suggestion.rate);
                $('#f_search2_' + id).closest('tr').find('.glautoidlabour').val(suggestion.financeGLAutoID);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }
    function initializestandardjobTypeahead_overhead(id) {
        $('#f_search3_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_standard/fetch_mfq_standard_overhead/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search3_' + id).closest('tr').find('.overheadautoid').val(suggestion.overHeadID);
                }, 200);
                $('#f_search3_' + id).closest('tr').find('.UnitOfMeasureIDoverhead').val(suggestion.uom);
                $('#f_search3_' + id).closest('tr').find('.UOMoverhead').val(suggestion.UnitDes);
                $('#f_search3_' + id).closest('tr').find('.unitrateoverhead').val(suggestion.rate);
                $('#f_search3_' + id).closest('tr').find('.glautoidoverhead').val(suggestion.financeGLAutoID);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function getJobQty(element) {
        $(element).closest('tr').find('.requestedQty').val(qty);
        $(element).closest('tr').find('.amount').val(unitPrice);
        var total = parseFloat(qty) * parseFloat(unitPrice);
        $(element).closest('tr').find('.totalAmount').text(commaSeparateNumber(parseFloat(total), 2));
    }

    function add_more_row() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#mfq_standard_job_card tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        //appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.requestedQty').val('0.00');
        appendData.find('.amount').val('0.00');
        appendData.find('.totalAmount').text('0.00');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_standard_job_card').append(appendData);
        initializestandardjobTypeahead(search_id);
        $('.select2').select2();
        number_validation();
    }

    function add_more_labour() {
        search_id2 += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#mfq_labour_task tbody tr:first').clone();
        appendData.find('.f_search2').attr('id', 'f_search2_' + search_id2);
        //appendData.find('.f_search2').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.unitrate').val('0.00');
        appendData.find('.usagehours').val('0.00');
        appendData.find('.totalhours').text('0.00');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_labour_task').append(appendData);
        initializestandardjobTypeahead_labour(search_id2);
        $('.select2').select2();
        number_validation();
    }
    function add_more_overhead_cost() {
        search_id3 += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#mfq_overhead_task tbody tr:first').clone();
        appendData.find('.f_search3').attr('id', 'f_search3_' + search_id3);
        //appendData.find('.f_search2').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.unitrateoverhead').val('0.00');
        appendData.find('.usagehoursoverhead').val('0.00');
        appendData.find('.totalhoursoverhead').val('0.00');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_overhead_task').append(appendData);
        initializestandardjobTypeahead_overhead(search_id3);
        $('.select2').select2();
        number_validation();
    }
    function add_more_row_output() {
        search_id4 += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#mfq_standard_job_card_output tbody tr:first').clone();
        appendData.find('.f_search4').attr('id', 'f_search4_' + search_id4);
        //appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.Qtyoutput').val('0.00');
        appendData.find('.unitcostoutput').val('0.00');
        appendData.find('.totalcostoutput').text('0.00');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_standard_job_card_output').append(appendData);
        initializestandardjobTypeahead_output(search_id4);
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: 'YYYY-MM-DD',
        }).on('dp.change', function (ev) {
            finishgoodsvalidationend(this);
            var documentdate = $.trim( $("#documentdate").text().replace(/,/g, ''));
            var expiryDate = $(this).closest('tr').find('.expiryDate').val();
            if(expiryDate)
            {
                if (expiryDate < documentdate) {
                    myAlert('w','Expiry Date cannot be less than Document Date');
                    $(this).closest('tr').find('.expiryDate').val('');
                }
            }
        });
        $('.select2').select2();
        number_validation();
    }

    function add_more_machine_sj(documentID) {
        search_id6 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_machine_' + documentID + ' tbody tr:first').clone();
        //appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.m_search').attr('id', 'm_search_' + documentID + '_' + search_id6);
        appendData.find('.m_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.startTime').removeClass('dateTimePicker_machine_' + documentID + '_1');
        appendData.find('.endTime').removeClass('dateTimePicker_machine_' + documentID + '_1');
        appendData.find('.startTime').removeClass('dateTimePicker_machine_' + documentID + '_' + search_id6 - 1);
        appendData.find('.endTime').removeClass('dateTimePicker_machine_' + documentID + '_' + search_id6 - 1);
        appendData.find('.startTime').addClass('dateTimePicker_machine_' + documentID + '_' + search_id6);
        appendData.find('.endTime').addClass('dateTimePicker_machine_' + documentID + '_' + search_id6);
        appendData.find('.startTime').attr('id', 'startDate_machine_' + documentID + '_' + search_id6);
        appendData.find('.startTime').attr('data-id', 'endDate_machine_' + documentID + '_' + search_id6);
        appendData.find('.endTime').attr('id', 'endDate_machine_' + documentID + '_' + search_id6);
        appendData.find('.endTime').attr('data-id', 'startDate_machine_' + documentID + '_' + search_id6);
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_machine_' + documentID).append(appendData);
        var lenght = $('#mfq_machine_' + documentID + ' tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializMachineTypeahead(search_id6, documentID);
        $('#startDate_machine_' + documentID + '_' + search_id6).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#endDate_machine_' + documentID + '_' + search_id6).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#startDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        //initializematerialTypeahead(1);
    }
    function add_more_crew_sj(documentID) {
        search_id7 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_crew_' + documentID + ' tbody tr:first').clone();
        //appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.c_search').attr('id', 'c_search_' + documentID + '_' + search_id7);
        appendData.find('.startTime').removeClass('dateTimePicker_crew_' + documentID + '_1');
        appendData.find('.startTime').removeClass('dateTimePicker_crew_' + documentID + '_' + search_id7 - 1);
        appendData.find('.endTime').removeClass('dateTimePicker_crew_' + documentID + '_1');
        appendData.find('.endTime').removeClass('dateTimePicker_crew_' + documentID + '_' + search_id7 - 1);
        appendData.find('.startTime').addClass('dateTimePicker_crew_' + documentID + '_' + search_id7);
        appendData.find('.endTime').addClass('dateTimePicker_crew_' + documentID + '_' + search_id7);
        appendData.find('.startTime').attr('id', 'startDate_crew_' + documentID + '_' + search_id7);
        appendData.find('.startTime').attr('data-id', 'endDate_crew_' + documentID + '_' + search_id7);
        appendData.find('.endTime').attr('id', 'endDate_crew_' + documentID + '_' + search_id7);
        appendData.find('.endTime').attr('data-id', 'startDate_crew_' + documentID + '_' + search_id7);
        appendData.find('.c_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_crew_' + documentID).append(appendData);
        var lenght = $('#mfq_crew_' + documentID + ' tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializCrewTypeahead(search_id7, documentID);
        $('#startDate_crew_' + documentID + '_' + search_id7).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#endDate_crew_' + documentID + '_' + search_id7).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#startDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        //initializematerialTypeahead(1);
    }
    function initi_standardjc_machine(documentID) {
        $('#machine_body').html('');
        $('#machine_body').append('<tr><td><input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control m_search" name="search[]" placeholder="Machine" id="m_search_' + documentID + '_' + search_id6 + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]"></td><td><input type="text" name="assetDescription[]" class="form-control assetDescription" readonly=""></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id6 + ' startTime" id="startDate_machine_' + documentID + '_' + search_id6 + '" data-id="endDate_machine_' + documentID + '_' + search_id6 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id6 + ' endTime" id="endDate_machine_' + documentID + '_' + search_id6 + '" data-id="startDate_machine_' + documentID + '_' + search_id6 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" required=""></div></td> <td><input type="text" name="hoursSpent[]" class="form-control hoursSpent" readonly><input type="hidden" name="hoursSpentminutes[]" class="form-control hoursSpentminutes" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        number_validation();
        $('.select2').select2();
        $('#startDate_machine_' + documentID + '_' + search_id6).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm");
            var seconds = (d2 - d1) / 1000;
            var numdays = Math.floor(seconds / 86400);
            var numhours = Math.floor((seconds % 86400) / 3600);
            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
            var totalminutes = Math.floor(seconds / 60);
            if ($.isNumeric(seconds)) {
                $(this).closest('tr').find('.hoursSpent').val(final);
                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
                $(this).closest('tr').find('.hoursSpentminutes').val(0);
            }
        });

        $('#endDate_machine_' + documentID + '_' + search_id6).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm");
            var seconds = (d2 - d1) / 1000;
            var numdays = Math.floor(seconds / 86400);
            var numhours = Math.floor((seconds % 86400) / 3600);
            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
            var totalminutes = Math.floor(seconds / 60);
            if ($.isNumeric(seconds)) {
                $(this).closest('tr').find('.hoursSpent').val(final);
                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
                $(this).closest('tr').find('.hoursSpentminutes').val(0);
            }
        });

        $('#startDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        setTimeout(function () {
            initializMachineTypeahead(1, documentID);
        }, 500);
    }
    function initi_standardjc_machine_crew(documentID) {

        $('#crew_body').html('');
        $('#crew_body').append('<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control c_search" name="search[]" placeholder="Crew" id="c_search_' + documentID + '_' + search_id7 + '"> <input type="hidden" class="form-control crewID" name="crewID[]"><input type="hidden" class="form-control EIdNo" name="EIdNo[]"></td> <td><input type="text" name="designation[]" class="form-control designation" readonly=""></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id7 + ' startTime" id="startDate_crew_' + documentID + '_' + search_id7 + '" data-id="endDate_crew_' + documentID + '_' + search_id7 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id7 + ' endTime" id="endDate_crew_' + documentID + '_' + search_id7 + '" data-id="startDate_crew_' + documentID + '_' + search_id7 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" required=""></div></td> <td><input type="text" name="hoursSpent[]" class="form-control hoursSpent" readonly><input type="hidden" name="hoursSpentminutes[]" class="form-control hoursSpentminutes" readonly></td></tr>');
        number_validation();
        $('.select2').select2();
        $('#startDate_crew_' + documentID + '_' + search_id7).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm");
            var seconds = (d2 - d1) / 1000;
            var numdays = Math.floor(seconds / 86400);
            var numhours = Math.floor((seconds % 86400) / 3600);
            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
            var totalminutes = Math.floor(seconds / 60);
            if ($.isNumeric(seconds)) {
                $(this).closest('tr').find('.hoursSpent').val(final);
                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
                $(this).closest('tr').find('.hoursSpentminutes').val(0);
            }
        });

        $('#endDate_crew_' + documentID + '_' + search_id7).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {

            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm");
            var seconds = (d2 - d1) / 1000;
            var numdays = Math.floor(seconds / 86400);
            var numhours = Math.floor((seconds % 86400) / 3600);
            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
           var totalminutes = Math.floor(seconds / 60);
            if ($.isNumeric(seconds)) {
                $(this).closest('tr').find('.hoursSpent').val(final);
                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
                $(this).closest('tr').find('.hoursSpentminutes').val(0);
            }

            /* var duration = moment.duration(seconds, 'seconds');
             var formatted = duration.format("hh:mm:ss");*/


        });

        $('#startDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        setTimeout(function () {
            initializCrewTypeahead(1, documentID);
        }, 500);
    }

    function initializMachineTypeahead(id, documentID) {
        $('#m_search_' + documentID + '_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_standard/fetch_machine/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.mfq_faID').val(suggestion.mfq_faID);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faCat').val(suggestion.faCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faCat').val(suggestion.faCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faSubCat').val(suggestion.faSubCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faSubSubCat').val(suggestion.faSubSubCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faCode').val(suggestion.faCode);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.partNumber').val(suggestion.partNumber);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.assetDescription').val(suggestion.assetDescription);
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }
    function initializCrewTypeahead(id, documentID) {
        $('#c_search_' + documentID + '_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Template/fetch_crew/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.crewID').val(suggestion.crewID);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.designation').val(suggestion.DesDescription);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.EIdNo').val(suggestion.EIdNo);
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function init_standardjobcardform() {
        $('#standard_job_card_body').html('');
        if (openContract == 1 ){
            $('#standard_job_card_body').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." id="f_search_1"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"></td>' + '<td style="width: 10%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]"></td><td style="width: 6%;"><input type="text" name="Qty[]" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td><td style="width: 10%;"> <input type="text" name="SUOM[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOM" readonly><input type="hidden" class="form-control SecondaryUnitOfMeasureID" name="SecondaryUnitOfMeasureID[]"></td><td style="width: 6%;"><input type="text" name="SecondaryQty[]" value="0.00" onkeyup="" class="form-control number SecondaryQty" onfocus="this.select();"> </td><td><input type="text" name="unitcost[]" value="0.00" class="form-control number unitcost" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcost[]" value="0.00" class="form-control number totalcost" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');

        } else {
            $('#standard_job_card_body').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." id="f_search_1"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"></td>' + '<td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]"></td><td style="width: 9%;"><input type="text" name="Qty[]" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td><td><input type="text" name="unitcost[]" value="0.00" class="form-control number unitcost" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcost[]" value="0.00" class="form-control number totalcost" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');

        }
        //$('#standard_job_card_body').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." id="f_search_1"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"></td>' + '<td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]"></td><td style="width: 9%;"><input type="text" name="Qty[]" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td><td><input type="text" name="unitcost[]" value="0.00" class="form-control number unitcost" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcost[]" value="0.00" class="form-control number totalcost" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        number_validation();
        $('.select2').select2();
        setTimeout(function () {
            initializestandardjobTypeahead(1);
        }, 500);
    }
    function init_standardjobcardform_output() {
        $('#standard_job_card_body_output').html('');
        /*var itemautoidoutput = $('.itemautoidoutput').val();
         alert(itemautoidoutput);
         ';
         */
        var warehouse = '<select name="warehouse[]" class="form-control warehouse select2" required><option value="">Select Warehouse</option><!--Select UOM--> </select>';

       if(openContract == 1){
           $('#standard_job_card_body_output').append('<tr><td style="width: 20%;"><input type="text" class="form-control f_search4" name="searchoutput[]" placeholder="Item Description..." id="f_search4_1"> <input type="hidden" class="form-control itemautoidoutput" name="itemautoidoutput[]"><input type="hidden" class="form-control assetglautoid" name="assetglautoid[]"><input type="hidden" class="form-control warehouseitemtype" name="warehouseitemtype[]"></td>' +
               '<td> <input type="text" name="UOMoutput[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoutput" disabled><input type="hidden" class="form-control UnitOfMeasureIDoutput" name="UnitOfMeasureIDoutput[]"></td><td style="width: 14%;">' + warehouse + '</td> <td style="width: 13%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expiryDate[]"  value="" class="form-control expiryDate"> </div> </td><td style="width: 6%;"><input type="text" name="Qtyoutput[]" value="0.00" onkeyup="calculateTotal_output(this),OutPutfinishgood()" class="form-control number Qtyoutput" onfocus="this.select();"> </td><td> <input type="text" name="SUOMoutput[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOMoutput" readonly><input type="hidden" class="form-control SeconaryUnitOfMeasureIDoutput" name="SeconaryUnitOfMeasureIDoutput[]"></td><td style="width: 6%;"><input type="text" name="SecondaryQtyoutput[]" value="0.00" onkeyup="" class="form-control number SecondaryQtyoutput" onfocus="this.select();"> </td><td style="width: 4%;"><input type="text" name="percentageoutput[]" value="0.00" class="form-control number percentageoutput" onkeyup="calculateTotal_output_percentage(this),OutPutfinishgood()" onfocus="this.select();"> </td><td><input type="text" name="unitcostoutput[]"  value="0.00" class="form-control number unitcostoutput" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcostoutput[]" value="0.00" class="form-control number totalcostoutput" onkeyup="calculateTotal_output(this),OutPutfinishgood()" onkeypress="return validateFloatKeyPress(this,event)"  onfocus="this.select();"> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');

       }else{
           $('#standard_job_card_body_output').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search4" name="searchoutput[]" placeholder="Item Description..." id="f_search4_1"> <input type="hidden" class="form-control itemautoidoutput" name="itemautoidoutput[]"><input type="hidden" class="form-control assetglautoid" name="assetglautoid[]"><input type="hidden" class="form-control warehouseitemtype" name="warehouseitemtype[]"></td>' +
               '<td> <input type="text" name="UOMoutput[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoutput" disabled><input type="hidden" class="form-control UnitOfMeasureIDoutput" name="UnitOfMeasureIDoutput[]"></td><td style="width: 14%;">' + warehouse + '</td> <td style="width: 13%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expiryDate[]"  value="" class="form-control expiryDate"> </div> </td><td style="width: 9%;"><input type="text" name="Qtyoutput[]" value="0.00" onkeyup="calculateTotal_output(this),OutPutfinishgood()" class="form-control number Qtyoutput" onfocus="this.select();"> </td><td style="width: 9%;"><input type="text" name="percentageoutput[]" value="0.00" class="form-control number percentageoutput" onkeyup="calculateTotal_output_percentage(this),OutPutfinishgood()" onfocus="this.select();"> </td><td><input type="text" name="unitcostoutput[]"  value="0.00" class="form-control number unitcostoutput" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcostoutput[]" value="0.00" class="form-control number totalcostoutput" onkeyup="calculateTotal_output(this),OutPutfinishgood()" onkeypress="return validateFloatKeyPress(this,event)"  onfocus="this.select();"> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
       }
        // $('#standard_job_card_body_output').append('<tr><td style="width: 20%;"><input type="text" class="form-control f_search4" name="searchoutput[]" placeholder="Item Description..." id="f_search4_1"> <input type="hidden" class="form-control itemautoidoutput" name="itemautoidoutput[]"><input type="hidden" class="form-control assetglautoid" name="assetglautoid[]"><input type="hidden" class="form-control warehouseitemtype" name="warehouseitemtype[]"></td>' +
        //  '<td> <input type="text" name="UOMoutput[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoutput" readonly><input type="hidden" class="form-control UnitOfMeasureIDoutput" name="UnitOfMeasureIDoutput[]"></td><td style="width: 14%;">' + warehouse + '</td> <td style="width: 13%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expiryDate[]"  value="" class="form-control expiryDate"> </div> </td><td style="width: 9%;"><input type="text" name="Qtyoutput[]" value="0.00" onkeyup="calculateTotal_output(this),OutPutfinishgood()" class="form-control number Qtyoutput" onfocus="this.select();"> </td><td> <input type="text" name="SUOMoutput[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOMoutput" readonly><input type="hidden" class="form-control SeconaryUnitOfMeasureIDoutput" name="SeconaryUnitOfMeasureIDoutput[]"></td><td style="width: 9%;"><input type="text" name="SecondaryQtyoutput[]" value="0.00" onkeyup="" class="form-control number SecondaryQtyoutput" onfocus="this.select();"> </td><td style="width: 6%;"><input type="text" name="percentageoutput[]" value="0.00" class="form-control number percentageoutput" onkeyup="calculateTotal_output_percentage(this),OutPutfinishgood()" onfocus="this.select();"> </td><td><input type="text" name="unitcostoutput[]"  value="0.00" class="form-control number unitcostoutput" onfocus="this.select();" readonly> </td><td><input type="text" name="totalcostoutput[]" value="0.00" class="form-control number totalcostoutput" onkeyup="calculateTotal_output(this),OutPutfinishgood()" onkeypress="return validateFloatKeyPress(this,event)"  onfocus="this.select();"> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');


        number_validation();
        $('.select2').select2();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: 'YYYY-MM-DD',
        }).on('dp.change', function (ev) {
            var documentdate = $.trim( $("#documentdate").text().replace(/,/g, ''));

            var expiryDate = $(this).closest('tr').find('.expiryDate').val();

            if(expiryDate)
            {
                if (expiryDate < documentdate) {
                    myAlert('w','Expiry Date cannot be less than Document Date');
                    $(this).closest('tr').find('.expiryDate').val('');
                }
            }
        });
        setTimeout(function () {
            initializestandardjobTypeahead_output(1);
        }, 500);
    }

    function init_standardjobcardform_labour() {
        $('#labour_task_body').html('');
        $('#labour_task_body').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search2" name="searchlabour[]" placeholder="Item Description..." id="f_search2_1"> <input type="hidden" class="form-control labourautoid" name="labourautoid[]"><input type="hidden" class="form-control glautoidlabour" name="glautoidlabour[]"></td>' +
            '<td style="width: 21%;"> <input type="text" name="UOMLabour[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMLabour" disabled><input type="hidden" class="form-control UnitOfMeasureIDLabour" name="UnitOfMeasureIDLabour[]"></td><td style="width: 9%;"><input type="text" name="unitrate[]" value="0.00" onkeyup="calculateTotallabour(this),labourcosttoal()" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitrate" onfocus="this.select();"> </td><td><input type="text" name="usagehours[]" onkeyup="calculateTotallabourusage(this),labourcosttoal()"  value="0.00" class="form-control number usagehours" onfocus="this.select();"> </td><td><input type="text" name="totalhours[]" value="0.00" class="form-control number totalhours" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        number_validation();
        $('.select2').select2();
        setTimeout(function () {
            initializestandardjobTypeahead_labour(1);
        }, 500);
    }
    function init_standardjobcardform_overhead() {
        $('#overheadcost_body').html('');
        $('#overheadcost_body').append('<tr><td style="width: 31%;"><input type="text" class="form-control f_search3" name="searchoverhead[]" placeholder="Item Description..." id="f_search3_1"> <input type="hidden" class="form-control overheadautoid" name="overheadautoid[]"><input type="hidden" class="form-control glautoidoverhead" name="glautoidoverhead[]"></td>' +
            '<td style="width: 21%;"> <input type="text" name="UOMoverhead[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoverhead" disabled><input type="hidden" class="form-control UnitOfMeasureIDoverhead" name="UnitOfMeasureIDoverhead[]"></td><td style="width: 9%;"><input type="text" name="unitrateoverhead[]" value="0.00" onkeyup="calculateoverhead(this),overheadcosttoal()" class="form-control number unitrateoverhead" onkeypress="return validateFloatKeyPress(this,event)"  onfocus="this.select();"> </td><td><input type="text" name="usagehoursoverhead[]" onkeyup="calculateoverhead(this),overheadcosttoal()"  value="0.00" class="form-control number usagehoursoverhead" onfocus="this.select();"> </td><td><input type="text" name="totalhoursoverhead[]" value="0.00" class="form-control number totalhoursoverhead" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        number_validation();
        $('.select2').select2();
        setTimeout(function () {
            initializestandardjobTypeahead_overhead(1);
        }, 500);
    }


    function loadCustomerInvoice() {
        if (invoiceAutoID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_CustomerInvoice/load_mfq_customerInvoice"); ?>',
                dataType: 'json',
                data: {invoiceAutoID: invoiceAutoID},
                async: false,
                success: function (data) {
                    $("#invoiceDate").val(data['invoiceDate']).change();
                    $("#invoiceDueDate").val(data['invoiceDueDate']).change();
                    $("#currencyID").val(data['transactionCurrencyID']).change();
                    $("#invoiceNarration").val(data['invoiceNarration']);
                    deliveryNoteID = data["deliveryNoteID"];
                    setTimeout(function () {
                        $("#mfqCustomerAutoID").val(data['mfqCustomerAutoID']).change();
                    }, 500);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }

    function saveCustomerInvoice(type) {
        var data = $(".frm_customerInvoice").serializeArray();
        data.push({'name': 'status', 'value': type});
        $.ajax({
            url: "<?php echo site_url('MFQ_CustomerInvoice/save_customer_invoice'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    if (type == 2) {
                        $('.headerclose').trigger('click');
                    } else {
                        $("#invoiceAutoID").val(data[2]);
                        invoiceAutoID = data[2];
                        $("#documentSystemCode").val(data[2]);
                        $('.btn-wizard').removeClass('disabled');
                        load_customer_invoice_detail(data[2]);
                    }

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }


    function confirmation() {

        var totalinput = $("#grandtotalinput").text().replace(/,/g, '');
        var totaloutput = $("#grandtotaloutput").text().replace(/,/g, '');
        var data = $('#standard_job_cardform').serializeArray();
        data.push({'name': 'standardjobcard', 'value': standardjobcard});
        data.push({'name': 'totalinput', 'value': totalinput});
        data.push({'name': 'totaloutput', 'value': totaloutput});
        if (totalinput != totaloutput) {
            swal(" ", "Total input value should be equal to total output", "error");
            /*myAlert('w','Total input value should be equal to total output')*/
        } else {
            if (standardjobcard) {

                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('MFQ_Job_standard/standardjobcard_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();

                                if (data['error'] == 1) {
                                    myAlert('e', data['message']);
                                } else if (data['error'] == 2) {

                                    myAlert('w', data['message']);
                                } else if (data['error'] == 4) {
                                    swal(" ", "Please save your unsaved works before confirm this document", "error");
                                }
                                else {
                                    myAlert('s', data['message']);
                                    save_standard_jobcard_input();
                                    fetchPage('system/mfq/mfq_standard_job_card', '', '<?php echo $this->lang->line('manufacturing_standard_job_card')?>')
                                }

                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
            ;
        }


    }

    function delete_customerInvoiceDetail(invoiceDetailsID, masterID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_CustomerInvoice/delete_customerInvoiceDetail'); ?>",
                    type: 'post',
                    data: {invoiceDetailsID: invoiceDetailsID, masterID: masterID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            if (data.code == 1) {
                                init_standardjobcardform();
                            }
                            $("#rowCI_" + invoiceDetailsID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Attachment/do_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    load_attachments('CI', $("#documentSystemCode").val());
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function load_attachments(documentID, invoiceAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {documentID: documentID, documentSystemCode: invoiceAutoID},
            url: "<?php echo site_url('MFQ_CustomerInvoice/load_attachments'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_attachments').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_attachment(id, myFileName) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {attachmentID: id, myFileName: myFileName},
                    url: "<?php echo site_url('Attachment/delete_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            myAlert('s', 'Deleted Successfully');
                            load_attachments('CI', $('#documentSystemCode').val());
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function customer_invoice_print() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                invoiceAutoID: $('#invoiceAutoID').val()
            },
            url: "<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#review").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_delivery_note(mfqCustomerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                mfqCustomerAutoID: mfqCustomerAutoID,
                invoiceAutoID: $('#invoiceAutoID').val()

            },
            url: "<?php echo site_url('MFQ_CustomerInvoice/fetch_delivery_note'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#deliveryNoteID').empty();
                var mySelect = $('#deliveryNoteID');
                mySelect.append($('<option></option>').val("").html("Select"));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option data-itemdescription="' + text['itemSystemCode'] + ' - ' + text['itemDescription'] + '" data-uom="' + text['defaultUnitOfMeasure'] + '"  data-itemAutoID="' + text['mfqItemID'] + '" data-description="' + text['description'] + '" data-jobNo="' + text['documentCode'] + '" data-qty="' + text['qty'] + '" data-unitprice = "' + text['unitPrice'] + '"></option>').val(text['deliverNoteID']).html(text['deliveryNoteCode']));
                    });
                }
                if (deliveryNoteID) {
                    mySelect.val(deliveryNoteID);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function calculateTotal(element) {
        var expectedQty = $(element).closest('tr').find('.Qty').val();
        var amount = $(element).closest('tr').find('.unitcost').val();

        var total = parseFloat(expectedQty) * parseFloat(amount);
        $(element).closest('tr').find('.totalcost').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));

    }

    function calculateTotal_qty_unitcost(element) {

        var qty = $(element).closest('tr').find('.Qtyoutput').val();
        var totalcost = $(element).closest('tr').find('.totalcostoutput').val();
        var totalcostinput = $(element).closest('tr').find('.tot_totalValue_output').val();

        if (qty == 0 || qty == '') {
            myAlert('w', 'Please enter a output qty!');
            $(element).closest('tr').find('.totalcostoutput').val('');
        } else {
            var total = totalcost / qty;
            $(element).closest('tr').find('.unitcostoutput').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));

        }


    }
    function calculateTotal_output(element) {

        var qty = $(element).closest('tr').find('.Qtyoutput').val();
        var totalcost = $(element).closest('tr').find('.totalcostoutput').val();
        var grandtotalinput = $("#grandtotalinput").text().replace(/,/g, '');
        var percentageoutput = $(element).closest('tr').find('.percentageoutput').val();
        var percentage = (parseFloat(totalcost) / parseFloat(grandtotalinput)) * 100;

        if (qty == 0 || qty == ' ') {
            myAlert('w', 'qty cannot be empty');
            $(element).closest('tr').find('.percentageoutput').val(0);
            $(element).closest('tr').find('.totalcostoutput').val(0);
            $(element).closest('tr').find('.unitcostoutput').val(0);
        } else if (qty != ' ' && (totalcost == 0 || totalcost == ' ')) {
            $(element).closest('tr').find('.percentageoutput').val(0);
            $(element).closest('tr').find('.unitcostoutput').val(0);
        }
        else {
            var unitcost = (parseFloat(totalcost) / parseFloat(qty));
            if (totalcost != '0.00' || totalcost != ' ') {
                $(element).closest('tr').find('.unitcostoutput').val(parseFloat(unitcost).toFixed(<?php echo $currencydecimalplaces?>));
                $(element).closest('tr').find('.percentageoutput').val(parseFloat(percentage).toFixed(<?php echo $currencydecimalplaces?>));
            }
            /*  var unitcost =(parseFloat(totalcost) / parseFloat(qty));
             var totalcost = (parseFloat(grandtotalinput) / 100) * percentageoutput;
             var percentage = (parseFloat(totalcost) / parseFloat(grandtotalinput)) * 100;
             */


            /*  $(element).closest('tr').find('.totalcostoutput').val(((parseFloat(grandtotalinput) / 100) * percentageoutput).toFixed(<?php echo $currencydecimalplaces?>));
             $(element).closest('tr').find('.percentageoutput').val(parseFloat(percentage).toFixed(<?php echo $currencydecimalplaces?>));*/


        }


        /*  var expectedQty = $(element).closest('tr').find('.Qtyoutput').val();
         var amount = $(element).closest('tr').find('.unitcostoutput').val();
         var totalcostoutput = $(element).closest('tr').find('.totalcostoutput').val();

         var totalcost =  (parseFloat(totalcostoutput) / parseFloat(expectedQty));

         if(totalcost)
         $(element).closest('tr').find('.unitcostoutput').val(parseFloat(totalcost).toFixed(<?php echo $currencydecimalplaces?>));

         /!*var total = parseFloat(expectedQty) * parseFloat(amount);
         $(element).closest('tr').find('.totalcostoutput').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));*!/

         /!* if(totalcostoutput)
         {

         }*!/*/

    }
    function calculateTotal_output_totalcost(element) {
        var totalcost = $(element).closest('tr').find('.totalcostoutput').val();
        var qty = $(element).closest('tr').find('.Qtyoutput').val();
        var grandtotalinput = $("#grandtotalinput").text().replace(/,/g, '');
        var percentage = (parseFloat(totalcost) / parseFloat(grandtotalinput)) * 100;
        var unitcost = (parseFloat(totalcost) / parseFloat(qty));
        $(element).closest('tr').find('.unitcostoutput').val(parseFloat(unitcost).toFixed(<?php echo $currencydecimalplaces?>));
        $(element).closest('tr').find('.percentageoutput').val(parseFloat(percentage).toFixed(<?php echo $currencydecimalplaces?>));
    }
    function calculateTotal_output_percentage(element) {

        var percentageoutput = $(element).closest('tr').find('.percentageoutput').val();
        var grandtotalinput = $("#grandtotalinput").text().replace(/,/g, '');
        //var htm = $(element).closest('tr').find('.totalcostoutput').val(5565.658);
        var totoalcost = $(element).closest('tr').find('.totalcostoutput').val();
        var QTY = $(element).closest('tr').find('.Qtyoutput').val();


        if (QTY == 0 || QTY == ' ') {
            myAlert('w', 'qty cannot be empty');
            $(element).closest('tr').find('.percentageoutput').val(0);
            $(element).closest('tr').find('.totalcostoutput').val(0);
            $(element).closest('tr').find('.unitcostoutput').val(0);
        } else {
            $(element).closest('tr').find('.totalcostoutput').val(((parseFloat(grandtotalinput) / 100) * percentageoutput));

            var unitcost = (parseFloat(((parseFloat(grandtotalinput) / 100) * percentageoutput)) / parseFloat(QTY));

            $(element).closest('tr').find('.unitcostoutput').val(parseFloat(unitcost).toFixed(<?php echo $currencydecimalplaces?>));
        }


        /* if(totoalcost)
         {
         $(element).closest('tr').find('.unitcostoutput').val((parseFloat(totoalcost) / QTY).toFixed(<?php echo $currencydecimalplaces?>));
         }*/


        //debugger;


        //$('.totalcostoutput').val(((parseFloat(grandtotalinput) / 100) * percentageoutput));
    }

    function calculateTotallabour(element) {
        var UsageHours = $(element).closest('tr').find('.usagehours').val();
        var unitrate = $(element).closest('tr').find('.unitrate').val();

        var total = parseFloat(UsageHours) * parseFloat(unitrate);
        $(element).closest('tr').find('.totalhours').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));

    }
    function calculateTotallabourusage(element) {
        var UsageHours = $(element).closest('tr').find('.usagehours').val();
        var unitrate = $(element).closest('tr').find('.unitrate').val();

        var total = parseFloat(UsageHours) * parseFloat(unitrate);
        $(element).closest('tr').find('.totalhours').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));
    }
    function calculateoverhead(element) {
        var UsageHours = $(element).closest('tr').find('.usagehoursoverhead').val();
        var unitrate = $(element).closest('tr').find('.unitrateoverhead').val();

        var total = parseFloat(UsageHours) * parseFloat(unitrate);
        $(element).closest('tr').find('.totalhoursoverhead').val(parseFloat(total).toFixed(<?php echo $currencydecimalplaces?>));
    }
    function validateFloatKeyPress(el, evt) {

    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }


    function save_standard_jobcard_input() {
        $('.umoDropdown').prop("disabled", false);
        $('.UOM').prop("disabled", false);
        $('.UOMLabour').prop("disabled", false);
        $('.UOMoverhead').prop("disabled", false);
        $('.UOMoutput').prop("disabled", false);
        var data = $('#standard_job_cardform').serializeArray();
        data.push({'name': 'totalinput', 'value': totalinput});
        $.ajax({
            url: "<?php echo site_url('MFQ_Job_standard/save_mfq_sd_job_input'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.umoDropdown').prop("disabled", true);
                    load_standard_job_card(standardjobcard);
                    load_standard_job_card_labour(standardjobcard);
                    load_standard_job_card_overhead(standardjobcard);
                    load_standard_job_card_output(standardjobcard);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }
    function standard_job_cardform_output() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#standard_job_cardform_output').serializeArray();

        $.ajax({
            url: "<?php echo site_url('MFQ_Job_standard/save_mfq_sd_job_output'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_standard_job_card_output(standardjobcard);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function load_standard_job_card(StandardJobcard) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: StandardJobcard},
            url: "<?php echo site_url('MFQ_Job_standard/load_mfq_standard_job_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#standard_job_card_body').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        if(openContract == 1 ){
                            $('#standard_job_card_body').append('<tr id="rowMC_' + v.jobItemID + '"> <td style="width: 20%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 10%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" disabled><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 6%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td> <td style="width: 10%;"> <input type="text" name="SUOM[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOM" value="' + v.suom + '" readonly><input type="hidden" class="form-control SecondaryUnitOfMeasureID" name="SecondaryUnitOfMeasureID[]" value="' + v.suomID + '"></td><td style="width: 6%;"><input type="text" name="SecondaryQty[]"  value="' + v.suomQty + '" value="0.00" onkeyup="" class="form-control number SecondaryQty" onfocus="this.select();"> </td><td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();" readonly> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();" readonly></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_raw_material(' + v.jobItemID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');

                        } else {
                            $('#standard_job_card_body').append('<tr id="rowMC_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" disabled><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td> <td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();" readonly> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();" readonly></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_raw_material(' + v.jobItemID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');

                        }
                       // $('#standard_job_card_body').append('<tr id="rowMC_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 15%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this),rawmaterialCostTotal()" class="form-control number Qty" onfocus="this.select();"> </td> <td style="width: 15%;"> <input type="text" name="SUOM[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOM" value="' + v.suom + '" readonly><input type="hidden" class="form-control SecondaryUnitOfMeasureID" name="SecondaryUnitOfMeasureID[]" value="' + v.suomID + '"></td><td style="width: 9%;"><input type="text" name="SecondaryQty[]"  value="' + v.suomQty + '" value="0.00" onkeyup="" class="form-control number SecondaryQty" onfocus="this.select();"> </td><td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();" readonly> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();" readonly></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_raw_material(' + v.jobItemID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializestandardjobTypeahead(search_id);
                        search_id++;
                        i++;
                    });
                } else {
                    init_standardjobcardform();
                }
                rawmaterialCostTotal();
                calculateTotalCost();
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function load_standard_job_card_output(StandardJobcard) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: StandardJobcard},
            url: "<?php echo site_url('MFQ_Job_standard/load_mfq_standard_job_details_output'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#standard_job_card_body_output').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        if (v.mfqItemType == 2) {
                            var warehouse = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ci_\'+ search_id4 +\'"'), form_dropdown('warehouse[]', all_mfq_warehouse_drop_finih_goods(2), 'Each', 'class="form-control select2 warehouse"  required'))
                                ?>';

                        } else {
                            var warehouse = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ci_\'+ search_id4 +\'"'), form_dropdown('warehouse[]', all_mfq_warehouse_drop_finih_goods(), 'Each', 'class="form-control select2 warehouse"  required'))
                                ?>';

                        }

                        if(openContract == 1){
                            $('#standard_job_card_body_output').append('<tr id="rowMCoutput_' + v.jobItemID + '"> <td style="width: 20%;"><input type="text" class="form-control f_search4" name="searchoutput[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search4_' + search_id4 + '"> <input type="hidden" class="form-control itemautoidoutput" name="itemautoidoutput[]"  value="' + v.itemAutoID + '"><input type="hidden" class="form-control assetglautoid" name="assetglautoid[]" value="' + v.glAutoID + '"><input type="hidden" class="form-control warehouseitemtype" name="warehouseitemtype[]" value="' + v.mfqItemType + '"></td> <td style="width: 6%;"> <input type="text" name="UOMoutput[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoutput" value="' + v.unitOfMeasure + '" disabled><input type="hidden" class="form-control UnitOfMeasureIDoutput" name="UnitOfMeasureIDoutput[]" value="' + v.uomID + '"></td><td style="width: 14%;">' + warehouse + '</td><td style="width: 13%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expiryDate[]"  value="' + v.expiryDate + '" class="form-control expiryDate"> </div> </td></td> <td style="width: 6%;"><input type="text" name="Qtyoutput[]"  value="' + v.qty + '"  onkeyup="calculateTotal_output(this),OutPutfinishgood()" class="form-control number Qtyoutput" onfocus="this.select();"> </td><td style="width: 6%;"> <input type="text" name="SUOMoutput[]" style="text-align: left;" placeholder="Select SUOM" class="form-control number SUOMoutput" value="' + v.suom + '" readonly><input type="hidden" class="form-control SecondaryUnitOfMeasureIDoutput" name="SecondaryUnitOfMeasureIDoutput[]" value="' + v.suomID + '"></td><td style="width: 6%;"><input type="text" name="SecondaryQtyoutput[]"  value="' + v.suomQty + '"  onkeyup="" class="form-control number SecondaryQtyoutput" onfocus="this.select();"> </td><td style="width: 4%;"><input type="text" name="percentageoutput[]" value="' + v.costAllocationPrc + '" class="form-control number percentageoutput" onkeyup="calculateTotal_output_percentage(this),OutPutfinishgood()" onfocus="this.select();"> </td> <td><input type="text" name="unitcostoutput[]"  value="' + v.unitCost + '" class="form-control number unitcostoutput" onfocus="this.select();" readonly> </td> <td><input type="text" name="totalcostoutput[]" value="' + v.totalCost + '"  class="form-control number totalcostoutput" onkeyup="calculateTotal_output(this),OutPutfinishgood();" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" ></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_finishgoodsOutput(' + v.jobItemID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');

                        } else {
                            $('#standard_job_card_body_output').append('<tr id="rowMCoutput_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search4" name="searchoutput[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search4_' + search_id4 + '"> <input type="hidden" class="form-control itemautoidoutput" name="itemautoidoutput[]"  value="' + v.itemAutoID + '"><input type="hidden" class="form-control assetglautoid" name="assetglautoid[]" value="' + v.glAutoID + '"><input type="hidden" class="form-control warehouseitemtype" name="warehouseitemtype[]" value="' + v.mfqItemType + '"></td> <td> <input type="text" name="UOMoutput[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoutput" value="' + v.unitOfMeasure + '" disabled><input type="hidden" class="form-control UnitOfMeasureIDoutput" name="UnitOfMeasureIDoutput[]" value="' + v.uomID + '"></td><td style="width: 14%;">' + warehouse + '</td><td style="width: 13%;"><div class="input-group datepic"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="expiryDate[]"  value="' + v.expiryDate + '" class="form-control expiryDate"> </div> </td></td> <td style="width: 9%;"><input type="text" name="Qtyoutput[]"  value="' + v.qty + '"  onkeyup="calculateTotal_output(this),OutPutfinishgood()" class="form-control number Qtyoutput" onfocus="this.select();"> </td><td style="width: 9%;"><input type="text" name="percentageoutput[]" value="' + v.costAllocationPrc + '" class="form-control number percentageoutput" onkeyup="calculateTotal_output_percentage(this),OutPutfinishgood()" onfocus="this.select();"> </td> <td><input type="text" name="unitcostoutput[]"  value="' + v.unitCost + '" class="form-control number unitcostoutput" onfocus="this.select();" readonly> </td> <td><input type="text" name="totalcostoutput[]" value="' + v.totalCost + '"  class="form-control number totalcostoutput" onkeyup="calculateTotal_output(this),OutPutfinishgood();" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" ></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_finishgoodsOutput(' + v.jobItemID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        }

                        initializestandardjobTypeahead_output(search_id4);
                        if (v.manufacturingwarehouse != 0) {
                            $('#ci_' + search_id4).val(v.manufacturingwarehouse);
                        } else {
                            $('#ci_' + search_id4).val(null).trigger("change");
                        }
                        search_id4++;
                        i++;

                    });
                } else {
                    init_standardjobcardform_output();

                }
                OutPutfinishgood();
                Inputmask().mask(document.querySelectorAll("input"));
                $('.datepic').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY-MM-DD',
                }).on('dp.change', function (ev) {
                    var documentdate = $.trim( $("#documentdate").text().replace(/,/g, ''));

                    var expiryDate = $(this).closest('tr').find('.expiryDate').val();

                    if(expiryDate)
                    {
                        if (expiryDate < documentdate) {
                            myAlert('w','Expiry Date cannot be less than Document Date');
                            $(this).closest('tr').find('.expiryDate').val('');
                        }
                    }
                });
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_standard_job_card_labour(StandardJobcard) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: StandardJobcard},
            url: "<?php echo site_url('MFQ_Job_standard/load_mfq_standard_job_details_labour'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#labour_task_body').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        /* $('#standard_job_card_body').append('<tr id="rowMclabour_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this)" class="form-control number Qty" onfocus="this.select();"> </td> <td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();"> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();"></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');*/
                        $('#labour_task_body').append('<tr id="rowMclabour_' + v.jobLabourTaskID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search2" value="' + v.description + '" name="searchlabour[]" placeholder="Description..." id="f_search2_' + search_id2 + '"> <input type="hidden" class="form-control labourautoid" name="labourautoid[]" value="' + v.labourTaskID + '"><input type="hidden" class="form-control glautoidlabour" name="glautoidlabour[]" value="' + v.glAutoID + '" ></td>' +
                            '<td style="width: 21%;"> <input type="text" name="UOMLabour[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOMLabour" value="' + v.unitOfMeasure + '"  disabled><input type="hidden" class="form-control UnitOfMeasureIDLabour" name="UnitOfMeasureIDLabour[]" value="' + v.uomID + '"></td><td style="width: 9%;"><input type="text" name="unitrate[]"  value="' + v.hourlyRate + '" onkeyup="calculateTotallabour(this),labourcosttoal()" onkeypress="return validateFloatKeyPress(this,event)"  class="form-control number unitrate" onfocus="this.select();"> </td><td><input type="text" name="usagehours[]" value="' + v.totalHours + '"  onkeyup="calculateTotallabourusage(this),labourcosttoal()" class="form-control number usagehours" onfocus="this.select();"> </td><td><input type="text" name="totalhours[]" value="' + v.totalValue + '" class="form-control number totalhours" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_labour(' + v.jobLabourTaskID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                        initializestandardjobTypeahead_labour(search_id2);
                        search_id2++;
                        i++;
                    });
                } else {
                    init_standardjobcardform_labour();
                }
                labourcosttoal();
                calculateTotalCost();
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function load_standard_job_card_overhead(StandardJobcard) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: StandardJobcard},
            url: "<?php echo site_url('MFQ_Job_standard/load_mfq_standard_job_overhead'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#overheadcost_body').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        /* $('#standard_job_card_body').append('<tr id="rowMclabour_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this)" class="form-control number Qty" onfocus="this.select();"> </td> <td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();"> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();"></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');*/
                        $('#overheadcost_body').append('<tr id="rowMcoverhead_' + v.jobOverHeadID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search3" name="searchoverhead[]" placeholder="Item Description..." id="f_search3_' + search_id3 + '" value="' + v.Description + '" > <input type="hidden" class="form-control overheadautoid" name="overheadautoid[]"  value="' + v.overHeadID + '"><input type="hidden" class="form-control glautoidoverhead" name="glautoidoverhead[]"  value="' + v.glAutoID + '"></td>' +
                            '<td style="width: 21%;"> <input type="text" name="UOMoverhead[]" value="' + v.unitOfMeasure + '"  style="text-align: left;" placeholder="Select UOM" class="form-control number UOMoverhead" disabled><input type="hidden" class="form-control UnitOfMeasureIDoverhead" name="UnitOfMeasureIDoverhead[]" value="' + v.uomID + '"></td><td style="width: 9%;"><input type="text" name="unitrateoverhead[]"  value="' + v.hourlyRate + '" onkeypress="return validateFloatKeyPress(this,event)"  onkeyup="calculateoverhead(this),overheadcosttoal()" class="form-control number unitrateoverhead" onfocus="this.select();"> </td><td><input type="text" name="usagehoursoverhead[]" onkeyup="calculateoverhead(this),overheadcosttoal()"  value="' + v.totalHours + '"  class="form-control number usagehoursoverhead" onfocus="this.select();"> </td><td><input type="text" name="totalhoursoverhead[]" value="' + v.totalValue + '"  class="form-control number totalhoursoverhead" onfocus="this.select();" readonly> </td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_OverHead(' + v.jobOverHeadID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializestandardjobTypeahead_overhead(search_id3);
                        search_id3++;
                        i++;
                    });
                } else {
                    init_standardjobcardform_overhead();
                }
                overheadcosttoal();
                calculateTotalCost();
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function rawmaterialCostTotal() {
        var tot_Qty = 0;
        var tot_UnitCost = 0;
        var tot_TotalCost = 0;
        $('#standard_job_card_body tr').each(function () {
            if (openContract == 1 ){
                var tot_value = getNumberAndValidate($('td', this).eq(6).find('input').val());
            }else{
                var tot_value = getNumberAndValidate($('td', this).eq(4).find('input').val());
            }

            tot_TotalCost += tot_value;
        });

        $("#tot_totalValue").text(commaSeparateNumber(tot_TotalCost, <?php echo $currencydecimalplaces?>));
        calculateTotalCost();
    }

    function labourcosttoal() {

        var tot_TotalCostlabour = 0;
        $('#labour_task_body tr').each(function () {
            var tot_valuelabour = getNumberAndValidate($('td', this).eq(4).find('input').val());
            tot_TotalCostlabour += tot_valuelabour;
        });

        $("#tot_totalValue_labour").text(commaSeparateNumber(tot_TotalCostlabour, <?php echo $currencydecimalplaces?>));
        calculateTotalCost();
    }
    function overheadcosttoal() {

        var tot_TotalCostoverhead = 0;
        $('#overheadcost_body tr').each(function () {
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(4).find('input').val());
            tot_TotalCostoverhead += tot_valueoverhead;
        });

        $("#tot_totalValue_overhead").text(commaSeparateNumber(tot_TotalCostoverhead, <?php echo $currencydecimalplaces?>));
        calculateTotalCost();
    }
    function OutPutfinishgood() {

        var tot_TotalCostoutput = 0;
        $('#standard_job_card_body_output tr').each(function () {
            if (openContract == 1 ){
                var tot_valueoutput = getNumberAndValidate($('td', this).eq(9).find('input').val());
            }else{
                var tot_valueoutput = getNumberAndValidate($('td', this).eq(7).find('input').val());
            }
            tot_TotalCostoutput += tot_valueoutput;
        });


        $("#tot_totalValue_output").text(commaSeparateNumber((tot_TotalCostoutput),<?php echo $currencydecimalplaces?>));
        calculateTotalCostoutput();
    }

    function calculateTotalCost() {
        var tot_TotalCost = parseFloat($('#tot_totalValue').text().replace(/,/g, ''));
        var tot_TotalCostlabour = parseFloat($('#tot_totalValue_labour').text().replace(/,/g, ''));
        var tot_TotalCostoverhead = parseFloat($('#tot_totalValue_overhead').text().replace(/,/g, ''));
        $("#grandtotalinput").text(commaSeparateNumber((tot_TotalCost + tot_TotalCostlabour + tot_TotalCostoverhead), <?php echo $currencydecimalplaces?>));
        totalinput = (tot_TotalCost + tot_TotalCostlabour + tot_TotalCostoverhead);
    }
    function calculateTotalCostoutput() {
        var tot_totalValue_output = parseFloat($('#tot_totalValue_output').text().replace(/,/g, ''));
        $("#grandtotaloutput").text(commaSeparateNumber((tot_totalValue_output),<?php echo $currencydecimalplaces?>));

    }


    function getNumberAndValidate(thisVal) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        //thisVal = thisVal.toFixed(<?php echo $currencydecimalplaces?>);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }
    function fetch_related_warehouse(itemtype, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemtype': itemtype},
            url: "<?php echo site_url('MFQ_Job_standard/warehousefinishgoods'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.warehouse').empty()
                var mySelect = $(element).parent().closest('tr').find('.warehouse');
                mySelect.append($('<option></option>').val('').html('Select Warehouse'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['wareHouseAutoID']).html(text['wareHouseDescription']));
                    });
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function save_machine(documentID) {
        var data = $('#frm_machine').serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_standard/save_sd_machine'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                load_standard_job_card_machine(documentID);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function load_standard_job_card_machine(documentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: documentID},
            url: "<?php echo site_url('MFQ_Job_standard/load_mfq_standard_job_machine'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#machine_body').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var seconds = (v.hoursSpent * 60);


                        var numdays = Math.floor(seconds / 86400);
                        var numhours = Math.floor((seconds % 86400) / 3600);
                        var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                        var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                        var totalminutes = Math.floor(seconds / 60);
                        /* $('#standard_job_card_body').append('<tr id="rowMclabour_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this)" class="form-control number Qty" onfocus="this.select();"> </td> <td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();"> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();"></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');*/
                        $('#machine_body').append('<tr><td><input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control m_search" name="search[]" placeholder="Machine" id="m_search_' + documentID + '_' + search_id6 + '" value="' + v.Match + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"></td><td><input type="text" name="assetDescription[]" class="form-control assetDescription"  value="' + v.Description + '" readonly=""></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id6 + ' startTime" id="startDate_machine_' + documentID + '_' + search_id6 + '" data-id="endDate_machine_' + documentID + '_' + search_id6 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" value="' + v.startTime + '" required=""></div></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id6 + ' endTime" id="endDate_machine_' + documentID + '_' + search_id6 + '" data-id="startDate_machine_' + documentID + '_' + search_id6 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime"  value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" value="' + final + '" class="form-control hoursSpent" readonly><input type="hidden" name="hoursSpentminutes[]"  value="' + totalminutes + '" class="form-control hoursSpentminutes" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_machine(' + v.jobMachineID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializMachineTypeahead(search_id6, documentID);


                        $('#startDate_machine_' + documentID + '_' + search_id6).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'bottom'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm");
                            var seconds = (d2 - d1) / 1000;
                            var numdays = Math.floor(seconds / 86400);
                            var numhours = Math.floor((seconds % 86400) / 3600);
                            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                            var totalminutes = Math.floor(seconds / 60);
                            if ($.isNumeric(seconds)) {
                                $(this).closest('tr').find('.hoursSpent').val(final);
                                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                                $(this).closest('tr').find('.hoursSpentminutes').val(0);
                            }


                        });

                        $('#endDate_machine_' + documentID + '_' + search_id6).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'bottom'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm");
                            var seconds = (d2 - d1) / 1000;
                            var numdays = Math.floor(seconds / 86400);
                            var numhours = Math.floor((seconds % 86400) / 3600);
                            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                            var totalminutes = Math.floor(seconds / 60);
                            if ($.isNumeric(seconds)) {
                                $(this).closest('tr').find('.hoursSpent').val(final);
                                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                                $(this).closest('tr').find('.hoursSpentminutes').val(0);
                            }


                        });

                        $('#startDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_machine_' + documentID + '_' + search_id6).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        search_id6++;
                        i++;
                    });
                } else {
                    initi_standardjc_machine(documentID);
                }
                overheadcosttoal();
                calculateTotalCost();
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function save_crew(documentID, workFlowID) {
        var data = $('#frm_crew').serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job_standard/save_mfq_crew'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                load_standard_job_card_crew(documentID);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function load_standard_job_card_crew(documentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {StandardJobcard: documentID},
            url: "<?php echo site_url('MFQ_Job_standard/fetch_crew_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#crew_body').html('');
                var i = 0;
                var isRecordExist = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var seconds = (v.hoursSpent * 60);


                        var numdays = Math.floor(seconds / 86400);
                        var numhours = Math.floor((seconds % 86400) / 3600);
                        var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                        var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                        var totalminutes = Math.floor(seconds / 60);
                        /* $('#standard_job_card_body').append('<tr id="rowMclabour_' + v.jobItemID + '"> <td style="width: 31%;"><input type="text" class="form-control f_search" name="search[]" placeholder="Item Description..." value="' + v.Match + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control itemautoid" name="itemautoid[]"  value="' + v.itemAutoID + '"></td> <td style="width: 21%;"> <input type="text" name="UOM[]" style="text-align: left;" placeholder="Select UOM" class="form-control number UOM" value="' + v.unitOfMeasure + '" readonly><input type="hidden" class="form-control UnitOfMeasureID" name="UnitOfMeasureID[]" value="' + v.uomID + '"></td></td> <td style="width: 9%;"><input type="text" name="Qty[]"  value="' + v.qty + '" value="0.00" onkeyup="calculateTotal(this)" class="form-control number Qty" onfocus="this.select();"> </td> <td><input type="text" name="unitcost[]"  value="' + v.unitCost + '" class="form-control number unitcost" onfocus="this.select();"> </td> <td><input type="text" name="totalcost[]" value="' + v.totalCost + '"  class="form-control number totalcost" onfocus="this.select();"></td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');*/
                        $('#crew_body').append('<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control c_search" name="search[]" placeholder="Crew" id="c_search_' + documentID + '_' + search_id7 + '" value="' + v.Ename1 + '"> <input type="hidden" class="form-control crewID" name="crewID[]" value="' + v.crewID + '"><input type="hidden" class="form-control EIdNo" name="EIdNo[]" value="' + v.empID + '"></td> <td><input type="text" name="designation[]" class="form-control designation" value="' + v.Description + '" readonly=""></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id7 + ' startTime" id="startDate_crew_' + documentID + '_' + search_id7 + '" data-id="endDate_crew_' + documentID + '_' + search_id7 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" value="' + v.startTime + '"  class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id7 + ' endTime" id="endDate_crew_' + documentID + '_' + search_id7 + '" data-id="startDate_crew_' + documentID + '_' + search_id7 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" class="form-control hoursSpent" value="' + final + '" readonly><input type="hidden" name="hoursSpentminutes[]" class="form-control hoursSpentminutes" value="' + totalminutes + '" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_crew_details(' + v.jobCrewID + ',' + v.jobAutoID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                        initializCrewTypeahead(search_id7, documentID);
                        $('#startDate_crew_' + documentID + '_' + search_id7).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'bottom'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm");
                            var seconds = (d2 - d1) / 1000;
                            var numdays = Math.floor(seconds / 86400);
                            var numhours = Math.floor((seconds % 86400) / 3600);
                            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                            var totalminutes = Math.floor(seconds / 60);
                            if ($.isNumeric(seconds)) {
                                $(this).closest('tr').find('.hoursSpent').val(final);
                                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                                $(this).closest('tr').find('.hoursSpentminutes').val(0);
                            }
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id7).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'bottom'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm");
                            var seconds = (d2 - d1) / 1000;
                            var numdays = Math.floor(seconds / 86400);
                            var numhours = Math.floor((seconds % 86400) / 3600);
                            var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
                            var final = numdays + " days " + numhours + " hours " + numminutes + " minute ";
                            var totalminutes = Math.floor(seconds / 60);
                            if ($.isNumeric(seconds)) {
                                $(this).closest('tr').find('.hoursSpent').val(final);
                                $(this).closest('tr').find('.hoursSpentminutes').val(totalminutes);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                                $(this).closest('tr').find('.hoursSpentminutes').val(0);
                            }
                        });

                        $('#startDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id7).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });
                        search_id7++;
                        i++;
                    });
                } else {
                    initi_standardjc_machine_crew(documentID);
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function delete_raw_material(jobItemID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_rawmaterial'); ?>",
                    type: 'post',
                    data: {jobItemID: jobItemID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            init_standardjobcardform();
                            load_standard_job_card(jobAutoID);
                            /* load_standard_job_card_labour(jobAutoID);
                             load_standard_job_card_overhead(jobAutoID);
                             load_standard_job_card_output(jobAutoID);*/
                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function delete_labour(jobLabourTaskID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_labourtask'); ?>",
                    type: 'post',
                    data: {jobLabourTaskID: jobLabourTaskID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            init_standardjobcardform_labour();
                            //load_standard_job_card(jobAutoID);
                            load_standard_job_card_labour(jobAutoID);
                            /*   load_standard_job_card_overhead(jobAutoID);
                             load_standard_job_card_output(jobAutoID);*/
                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function delete_OverHead(jobOverHeadID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_OverHead'); ?>",
                    type: 'post',
                    data: {jobOverHeadID: jobOverHeadID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            init_standardjobcardform_overhead();
                            load_standard_job_card_overhead(jobAutoID);
                            /* load_standard_job_card_output(jobAutoID);*!/*/
                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function delete_finishgoodsOutput(jobItemID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_finishgoods'); ?>",
                    type: 'post',
                    data: {jobItemID: jobItemID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            init_standardjobcardform_output();
                            load_standard_job_card_output(jobAutoID);
                            /* load_standard_job_card_overhead(jobAutoID);*/

                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function delete_crew_details(jobCrewID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_crew'); ?>",
                    type: 'post',
                    data: {jobCrewID: jobCrewID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            initi_standardjc_machine_crew(jobAutoID);
                            load_standard_job_card_crew(jobAutoID);

                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function delete_machine(jobMachineID, jobAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_standard/delete_machine'); ?>",
                    type: 'post',
                    data: {jobMachineID: jobMachineID, jobAutoID: jobAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            initi_standardjc_machine(jobAutoID);
                            load_standard_job_card_machine(jobAutoID);
                            //search_id6 = 1;
                            //$("#rowMC_" + jobItemID).remove();
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }
    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }
    $('#progress').slider().on('slideStart', function (ev) {
        originalVal = $('#progress').data('slider').getValue();
    });

    $('#progress').slider().on('slideStop', function (ev) {
        var newVal = $('#progress').data('slider').getValue();
        if (originalVal != newVal) {
            $('#progressprecentage').html(newVal + '%');
            sildervalue(newVal);
        }
    });
    function sildervalue(value) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {progressvalue: value, jobAutoID: standardjobcard},
            url: "<?php echo site_url('MFQ_Job_standard/save_standardjobcard_progress_value'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function colorLabel(labelID) {
        $('#' + labelID).addClass('pendingApproval');
        $('#msg-div').show();

    }
    function hmsToSeconds() {
        var start_time = $(this).closest('tr').find('.txtstartTime').val(),
            end_time = $(this).closest('tr').find('.txtendTime').val();
        var seconds = (start_time - end_time) / 1000;
    }
</script>
