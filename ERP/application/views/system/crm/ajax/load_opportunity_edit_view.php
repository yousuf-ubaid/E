<?php
$currency_arr = crm_all_currency_new_drop();
$product_arr = all_crm_product_master();
$admin = crm_isSuperAdmin();

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/multipleattachment/fileinput.css'); ?>">
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
        color: #828282;
        font-weight: bold;
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
    .btn-filemultiup {
        position: relative;
        overflow: hidden;
    }

    .multipleattachmentbtn {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<?php
if (!empty($header)) {
    if ($header['closeStatus'] == 0) {
        ?>
        <div class="row">
            <div class="col-md-5">
                &nbsp;
            </div>
            <div class="col-md-4 text-center">
                &nbsp;
            </div>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="check_edit_approval()">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    Edit
                </button>
            </div>
        </div>
        <br>
        <?php
    }
    ?>
    <div class="row">
        <div class="col-md-5">
            &nbsp;
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">


        </div>
    </div>
    <ul class="nav nav-tabs" id="main-tabs">
        <?php if(($page == 'OpportunityTask') || ($page == 'Opportunityquatation')){?>
        <li><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <?php }else {?>
            <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <?php }?>

        <li><a href="#emails" onclick="emailssentview()"  data-toggle="tab"><i class="fa fa-television"></i>Emails </a></li>
        <li><a href="#notes" onclick="opportunity_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a>
        </li>
        <li><a href="#files" onclick="opportunity_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Files</a>
        </li>

        <?php if($page == 'OpportunityTask'){?>
        <li class="active"><a href="#tasks" onclick="opportunity_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
        <?php }else {?>
            <li><a href="#tasks" onclick="opportunity_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
        <?php }?>
        <li><a href="#products" onclick="opporunity_products()" data-toggle="tab"><i class="fa fa-television"></i>Products
            </a></li>
        <?php if($page == 'Opportunityquatation'){?>
        <li class="active"><a href="#quotation" onclick="opportunity_quotation()" data-toggle="tab"><i class="fa fa-television"></i>Quotation</a></li>
        <?php }else {?>
            <li><a href="#quotation" onclick="opportunity_quotation()" data-toggle="tab"><i class="fa fa-television"></i>Quotation</a></li>
        <?php }?>

    </ul>
    <input type="hidden" id="editopportunityID" value="<?php echo $header['opportunityID'] ?>">
    <div class="tab-content">
        <?php if(($page == 'OpportunityTask') || ($page == 'Opportunityquatation')){?>
        <div class="tab-pane " id="about">
            <?php }else {?>
            <div class="tab-pane active" id="about">
            <?php }?>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>OPPORTUNITY DETAILS</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Document Code</span></td>
                            <td><span class="tddata"><?php echo $header['documentSystemCodeoppor']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Description</span></td>
                            <td><span class="tddata"><?php echo $header['opportunityName']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Status</span></td>
                            <td><span class="label"
                                      style="background-color:#4caf50; color:#ffffff; font-size: 11px;"><?php echo $header['statusDescription'] ?></span>
                                <?php
                                if ($header['closeStatus'] == 0) { ?>
                                    <a class="nopjax" href="#" onclick="change_status()">&nbsp &nbsp Change</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Criteria</span></td>
                            <?php if($header['closeCriteriaID']==-1)
                            {
                                echo "<td><span class='tddata'>Other</span>";
                            }else if($header['closingcritiriaopp']!='')
                            {
                                echo "<td><span class='tddata'>".$header['criteriadescription']."</span>";
                            }else
                            {
                                 echo "-";
                            }
                            ?>


                            </td>
                        </tr>
                        <?php if($header['closeCriteriaID']==-1) {?>
                        <tr>
                            <td class="ralign"><span class="title">Remarks</span></td>
                            <td><span class='tddata'><?php echo $header['reason']?></span>
                            </td>
                        </tr>
                        <?php }?>


                        <tr>
                            <td class="ralign"><span class="title">Category</span></td>
                            <td><span class="tddata"><?php echo $header['categoryDescription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Value</span></td>
                            <!-- <td><span
                                    class="tddata"><?php echo $header['CurrencyCode'] . " " . number_format($header['transactionAmount'], 2) ?></span>
                            </td> -->
                            <td><span
                                    class="tddata"><?php echo $header['CurrencyCode'] . " " . number_format($opportunityProductValue, 2) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Probability Of Winning</span></td>
                            <td><span class="tddata"><?php echo $header['probabilityofwinning']; ?> %</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Forecast Close Date</span></td>
                            <td><span class="tddata"><?php echo $header['forcastCloseDate']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Type</span></td>
                            <td><span class="tddata"><?php echo $header['typedecription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">User Responsible</span></td>
                            <td><span class="tddata"><?php echo $header['responsiblePerson']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Converted from Lead</span></td>
                            <td><span class="tddata"><?php echo $header['fullname']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Description</span></td>
                            <td><span class="tddata"><?php echo $header['opportunityDescription']; ?></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
               <!-- <div class="col-sm-3">
                    <div class="fileinput-new thumbnail">
                        <img src="<?php /*echo base_url('images/item/no-image.png'); */?>" id="changeImg"
                             style="width: 200px; height: 145px;">
                    </div>
                </div>-->
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PIPELINE</h2>
                    </header>
                    <div class="row">
                        <div class="form-group col-sm-1">
                            &nbsp
                        </div>
                        <div class="col-sm-10">
                            <ul class="nav nav-tabs" id="pipelineTabs">
                                <div class="arrow-steps clearfix">
                                    <?php
                                    if (!empty($header['pipelineID'])) {
                                        $pipeline = $this->db->query("SELECT * FROM srp_erp_crm_pipelinedetails WHERE pipeLineID={$header['pipelineID']}")->result_array();
                                        if (!empty($pipeline)) {
                                            $count = count($pipeline);
                                            $percentage = 100 / $count;

                                            foreach ($pipeline as $pipe) {
                                                $active = 'not-current';
                                                $fontcolor = 'color: #666 !important;';
                                                if ($pipe['pipeLineDetailID'] == $header['pipelineStageID']) {
                                                    $active = "current";
                                                    $fontcolor = 'color: #fff !important;';
                                                } ?>

                                                <div class="step <?php echo $active ?>" style="margin-top:3px !important; ">
                                                    <li><a href="#stageID_<?php echo $pipe['pipeLineDetailID'] ?>"
                                                           data-toggle="tab"
                                                           onclick="checkCurrentTab(<?php echo $header['opportunityID'] ?>,<?php echo $pipe['pipeLineDetailID'] ?>)"
                                                    
                                                        style="font-size: 14px !important;text-align: center !important;cursor: default !important; <?php echo $fontcolor; ?>"><?php echo $pipe['stageName'] ?></span>
                                                        </a>
                                                    </li>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </ul>
                            <div class="form-group col-sm-1">
                                &nbsp
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <?php
                        if (!empty($pipeline)) {
                            foreach ($pipeline as $pipe) {
                                $active = 'not-current';
                                if ($pipe['pipeLineDetailID'] == $header['pipelineStageID']) {
                                    $active = "current";
                                } ?>
                                <div class="tab-pane tapPipeLine" id="stageID_<?php echo $pipe['pipeLineDetailID'] ?>">
                                    <div class="row">
                                        <div class="col-md-1">
                                        </div>
                                        <div class="col-md-3">
                                            <div
                                                style="font-weight: 500;font-size: 16px;color: slategrey;"><?php echo $pipe['stageName'] ?></div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <button type="button" class="btn btn-primary pull-right"
                                                    onclick="fetchPage('system/crm/create_new_task','','Create Task',44,[<?php echo $header['opportunityID'] ?>,<?php echo $pipe['pipeLineDetailID'] ?>]);">
                                                <i class="fa fa-plus"></i> Task
                                            </button>
                                        </div>
                                        <div class="col-md-1">
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-sm-1">
                                            &nbsp;
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="piplineview"
                                                 id="taskMaster_view_<?php echo $pipe['pipeLineDetailID'] ?>"></div>
                                        </div>
                                        <div class="col-sm-1">
                                            &nbsp;
                                        </div>
                                    </div>

                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>RECORD DETAILS</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td class="ralign"><span class="title">Created Date</span></td>
                    <td><span class="tddata"><?php echo $header['createdDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Last Updated</span></td>
                    <td><span class="tddata"><?php echo $header['modifydate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Opportunity Created By</span></td>
                    <td><span class="tddata"><?php echo $header['createdUserName'] ?></span></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="emails">
            <br>

            <div class="row">
                <div class="col-sm-12">
                    <!-- <button type="button" onclick="compose_email_contact()" class="btn btn-primary pull-right"><i
                             class="fa fa-plus"></i> Compose Email
                     </button>-->
                    <div class="emailssent">

                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="notes">
            <br>

            <div class="row" id="show_add_notes_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Opportunity Notes </h4></div>
                <div class="col-md-4">
                    <?php
                    if ($header['closeStatus'] == 0) { ?>
                        <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Note
                        </button>
                    <?php } ?>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_opportunity_add_notes"'); ?>
            <input type="hidden" name="opportunityID" value="<?php echo $header['opportunityID']; ?>">

            <div id="show_add_notes" class="hide">
                <div class="row">
                    <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field"><textarea class="form-control" rows="5"
                                                                                         name="description"
                                                                                         id="description"></textarea><span
                                        class="input-req-inner" style="top: 25px;"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <button class="btn btn-primary" type="submit">Add</button>
                        <button class="btn btn-danger" type="button" onclick="close_add_note()">Close</button>
                    </div>
                    <div class="form-group col-sm-6" style="margin-top: 10px;">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_notes"></div>
        </div>
        <div class="tab-pane" id="files">
            <br>

            <div class="row" id="show_add_files_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Opportunity Files </h4></div>
                <div class="col-md-4">
                    <?php
                    // if ($header['closeStatus'] == 0) { ?>
                        <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Files
                        </button>
                    <?php // } ?>
                </div>
            </div>
            <div class="row hide" id="add_attachemnt_show">
                <?php echo form_open_multipart('', 'id="opportunity_attachment_uplode_form" class="form-inline"'); ?>
                <div class="col-sm-10" style="margin-left: 3%">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="opportunityattachmentDescription"
                                   name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                            <input type="hidden" class="form-control" id="documentID" name="documentID" value="4">
                            <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                   value="Opportunity">
                            <input type="hidden" class="form-control" id="opportunity_documentAutoID"
                                   name="documentAutoID"
                                   value="<?php echo $header['opportunityID']; ?>">
                        </div>
                    </div>
                    <div class="col-sm-8" style="margin-top: -8px;">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput"><i
                                        class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                        class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                   data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                  aria-hidden="true"></span></a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                                class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                    </div>
                </div>

            </div>
            <br>

            <div id="show_all_attachments"></div>
        </div>
            <?php if($page == 'OpportunityTask'){?>
            <div class="tab-pane active" id="tasks">
                <?php }else {?>
                <div class="tab-pane" id="tasks">
                <?php }?>
            <br>

            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Opportunity Tasks </h4></div>
                <div class="col-md-4">
                    <?php
                    if ($header['closeStatus'] == 0) { ?>
                        <button type="button"
                                onclick="fetchPage('system/crm/create_new_task','','Create Task',4, <?php echo $header['opportunityID']; ?>);"
                                class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Task
                        </button>
                    <?php } ?>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-12">
                    <div id="show_all_tasks"></div>
                </div>
            </div>
        </div>
                <div class="tab-pane" id="products">
                    <br>

                    <div class="row" id="show_add_product_button">
                        <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Opportunity Products </h4></div>
                        <div class="col-md-4">
                            <button type="button" onclick="show_add_product()" class="btn btn-primary pull-right"><i
                                        class="fa fa-plus"></i> Add Products
                            </button>
                        </div>
                    </div>
                    <br>
                    <?php echo form_open('', 'role="form" id="frm_opportunity_add_product"'); ?>
                    <input type="hidden" name="opportunityid" value="<?php echo $header['opportunityID']; ?>">
                    <input type="hidden" name="opportunityproductid" id="opportunityproductid">

                    <div id="show_add_product" class="hide">
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Product Name</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('productID', $product_arr, '', 'class="form-control select2" id="productID" onchange="productdetails(this.value)" required'); ?>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Description</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <textarea name="description" id="product_description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Transaction Currency</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $header['transactionCurrencyID'], 'class="form-control select2" id="transactionCurrencyID" disabled="disabled"'); ?>
                            </div>
                        </div>
                        <!--<div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Subscription Amount</label>
                            </div>
                            <div class="form-group col-sm-4">-->
                                <input type="hidden" name="subscriptionamount" id="subscriptionamount" value="0" class="form-control number">
                            <!--</div>
                        </div>-->
                        <!--<div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Implementation Amount
                                </label>
                            </div>
                            <div class="form-group col-sm-4">-->
                                <input type="hidden" name="implementationamount" id="implementationamount" value="0" class="form-control number">
                            <!--</div>
                        </div>-->
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Price</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="number" name="price" id="edprice" value="0" class="form-control number" autocomplete="off">
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-6">
                                <button class="btn btn-danger pull-right" type="button" onclick="close_add_product()">Close
                                </button>
                                <button class="btn btn-primary pull-right" type="submit">Add</button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <div id="show_all_product"></div>
                </div>
                <?php if($page == 'Opportunityquatation'){?>
                 <div class="tab-pane active" id="quotation">
            <?php }else {?>
                    <div class="tab-pane" id="quotation">
                        <?php }?>
            <br>

            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Opportunity Quotation </h4></div>
                <div class="col-md-4">
                    <?php
                    if ($header['closeStatus'] == 0) { ?>
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="fetchPage('system/crm/create_new_quotation',null,'Add New Quotation',4,<?php echo $header['opportunityID']; ?>);">
                            <i
                                class="fa fa-plus"></i> Quotation
                        </button>
                    <?php } ?>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-12">
                    <div id="show_all_quotation"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Quotation</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="emailcomposemodel">
    <div class="modal-dialog" style="width: 59%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Compose Email</h4>
            </div>
            <?php echo form_open('', 'role="form" id="emailcomposemasterform"'); ?>
            <div class="modal-body">
                <input type="hidden" id="opportunitieid" name="opportunitieid" value="<?php echo $header['opportunityID']; ?>">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>To</label>
                        <input class="form-control" placeholder="To:" name="To" autocomplete="off">
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>cc</label>
                        <input class="form-control" placeholder="cc:" name="cc" autocomplete="off">
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Bcc</label>
                        <input class="form-control" placeholder="Bcc:" name="Bcc" autocomplete="off">
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Subject</label>
                        <input class="form-control" placeholder="Subject:" name="Subject" autocomplete="off">
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Body</label>
                        <textarea id="compose-textarea" name="compose-textarea"  class="form-control customerTypeDescription" style="height: 300px"></textarea>
                    </div>

                </div>
                <br>
                <div class="row" >
                    <div class="form-group col-sm-6 files" id="files3">
                        <span class="btn btn-default btn-filemultiup">
                               Attachment  <input type="file" name="files3" class="multipleattachmentbtn" multiple/>
                             </span>
                        <br/>
                        <ul class="fileList"></ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Send
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/multipleattachment/fileinput.min.js'); ?>"></script>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $(".select2").select2();
        <?php if($page == 'OpportunityTask'){?>
        opportunity_tasks();

        <?php }?>

        <?php if($page == 'Opportunityquatation'){?>
        opportunity_quotation();
        <?php }?>

        $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
            var fileIdCounter = 0;

            this.closest(".files").change(function (evt) {
                var output = [];

                for (var i = 0; i < evt.target.files.length; i++) {
                    fileIdCounter++;
                    var file = evt.target.files[i];
                    var fileId = sectionIdentifier + fileIdCounter;

                    filesToUpload.push({
                        id: fileId,
                        file: file
                    });

                    var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

                    output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
                };

                $(this).children(".fileList")
                    .append(output.join(""));

                //reset the input to null - nice little chrome bug!
                evt.target.value = null;
            });

            $(this).on("click", ".removeFile", function (e) {
                e.preventDefault();

                var fileId = $(this).parent().children("a").data("fileid");

                // loop through the files array and check if the name of that file matches FileName
                // and get the index of the match
                for (var i = 0; i < filesToUpload.length; ++i) {
                    if (filesToUpload[i].id === fileId)
                        filesToUpload.splice(i, 1);
                }

                $(this).parent().remove();
            });

            this.clear = function () {
                for (var i = 0; i < filesToUpload.length; ++i) {
                    if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                        filesToUpload.splice(i, 1);
                }

                $(this).children(".fileList").empty();
            }

            return this;
        };

        var filesToUpload = [];
        var files3Uploader = $("#files3").fileUploader(filesToUpload, "files3");

        $("#description").wysihtml5();

        $('#frm_opportunity_add_notes').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //campaign_name: {validators: {notEmpty: {message: 'Campaign Name is required.'}}},
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
                url: "<?php echo site_url('CrmLead/add_opportunity_notes'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_note();
                        opportunity_notes();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#frm_opportunity_add_product').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                productID: {validators: {notEmpty: {message: 'Product Name is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Transaction Currency is required.'}}},
                price: {validators: {notEmpty: {message: 'Price is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var transactionCurrencyID = $('#transactionCurrencyID').val();

            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            data.push({'name':'transactionCurrencyID','value':transactionCurrencyID});


            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/add_opportunity_product'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_product();
                        opporunity_products();
                        $('#frm_opportunity_add_product').trigger('reset');
                        $('#opportunityproductid').val('');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('#emailcomposemasterform').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                to: {validators: {notEmpty: {message: 'To is required'}}},/*To is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            var data = new FormData($("#emailcomposemasterform")[0]);

            for (var i = 0, len = filesToUpload.length; i < len; i++) {
                data.append("photo[]", filesToUpload[i].file);
            }

            /* $.each($(".email_attachment"), function(i, obj) {
             $.each(obj.files,function(i,file){
             var push =  data.append('photo[]', file);
             array_push($a);
             });
             });*/
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmMailBox/compose_email_opportunitie'); ?>",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.btn-primary').prop('disabled', false);
                        emailssentview();
                        files3Uploader.clear();
                        $('#emailcomposemodel').modal('hide');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


    });

    function opportunity_notes() {

        var opportunityID = $('#editopportunityID').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('CrmLead/load_opportunity_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_notes').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function show_add_note() {
        $('#show_all_notes').addClass('hide');
        $('#show_add_notes_button').addClass('hide');
        $('#show_add_notes').removeClass('hide');
        $('#frm_opportunity_add_notes')[0].reset();
        $('#frm_opportunity_add_notes').bootstrapValidator('resetForm', true);
    }

    function close_add_note() {
        $('#show_add_notes').addClass('hide');
        $('#show_all_notes').removeClass('hide');
        $('#show_add_notes_button').removeClass('hide');
    }

    function document_uplode() {
        var formData = new FormData($("#opportunity_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('crm/attachement_upload'); ?>",
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
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#opportunityattachmentDescription').val('');
                    opportunity_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function opportunity_attachments() {
        var opportunityID = $('#editopportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('CrmLead/load_opportunity_all_attachments'); ?>",
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

    function show_add_file() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function delete_crm_attachment(id, fileName) {
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
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('crm/delete_crm_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            opportunity_attachments();
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

    function opportunity_tasks() {
        var opportunityID = $('#editopportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('CrmLead/load_opportunity_all_tasks'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_tasks').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadLead();
        }
    }



    tinymce.init({
        selector: ".customerTypeDescription",
        height: 200,
        browser_spellcheck: true,
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
        ],
        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

        menubar: false,
        toolbar_items_size: 'small',

        style_formats: [{
            title: 'Bold text',
            inline: 'b'
        }, {
            title: 'Red text',
            inline: 'span',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Red header',
            block: 'h1',
            styles: {
                color: '#ff0000'
            }
        }, {
            title: 'Example 1',
            inline: 'span',
            classes: 'example1'
        }, {
            title: 'Example 2',
            inline: 'span',
            classes: 'example2'
        }, {
            title: 'Table styles'
        }, {
            title: 'Table row 1',
            selector: 'tr',
            classes: 'tablerow1'
        }],

        templates: [{
            title: 'Test template 1',
            content: 'Test 1'
        }, {
            title: 'Test template 2',
            content: 'Test 2'
        }]
    });

    function profileImageUploadLead() {
        var imgageVal = new FormData();
        imgageVal.append('opportunityID', $('#editopportunityID').val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#opportunity_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('CrmLead/opportunity_image_upload'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function change_status() {
        $('#statusID').val('');
        $('#reason').val('');
        $('.closedatehideshow').addClass('hide');
        $('.showotherreson').addClass('hide');
        $('#statusModal').modal({backdrop: "static"});
    }

    function checkCurrentTab(opporunityID, pipeLineDetailID) {
        $('.tapPipeLine').removeClass('active');
        $('#stageID_' + pipeLineDetailID).addClass('active');
        getTaskManagement_tableView(opporunityID, pipeLineDetailID)
    }

    function getTaskManagement_tableView(opporunityID, pipeLineDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {opporunityID: opporunityID, pipeLineDetailID: pipeLineDetailID,type:2},
            url: "<?php echo site_url('crm/load_taskManagement_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taskMaster_view_' + pipeLineDetailID).html(data['view']);
                $(".taskHeading_tr").hide();
                $(".taskaction_tr").hide();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function opportunity_quotation() {
        var opportunityID = $('#editopportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('CrmLead/load_opportunity_all_quotation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_quotation').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_quotation_printModel(quotationAutoID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {quotationAutoID: quotationAutoID, html: html},
            url: "<?php echo site_url('crm/quotation_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_crm_quotation(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'quotationAutoID': id},
                    url: "<?php echo site_url('Crm/delete_crm_quotation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        opportunity_quotation();
                        myAlert('s', 'Quotation Deleted Successfully');
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_note(notesID) {
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
                    data: {notesID: notesID},
                    url: "<?php echo site_url('crm/delete_master_notes_allDocuments'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Note Deleted Successfully');
                            opportunity_notes();
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

    function check_edit_approval(){
        if(<?php echo $this->common_data['current_userID']?>==<?php if(!empty($superadmn)){ echo $superadmn['isadmin'];}else{echo 000;}  ?>){
            fetchPage('system/crm/create_opportunity',<?php echo $header['opportunityID'] ?>,'Edit Opportunity','CRM');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['crtduser'] ?>){
            fetchPage('system/crm/create_opportunity',<?php echo $header['opportunityID'] ?>,'Edit Opportunity','CRM');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['responsibleEmpID'] ?>){
            fetchPage('system/crm/create_opportunity',<?php echo $header['opportunityID'] ?>,'Edit Opportunity','CRM');
        }else{
            myAlert('w','You do not have permission to edit this oppotunity')
        }
    }
    function emailssentview() {
        var opportunityID = $('#editopportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'opportunityID': opportunityID},
            url: "<?php echo site_url('CrmMailbox/opportunitieview'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.emailssent').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function compose_email_opportunitie()
    {
        $('#emailcomposemasterform')[0].reset();
        $('#emailcomposemasterform').bootstrapValidator('resetForm', true);

        $('#emailcomposemodel').modal('show');
    }
    function opporunity_products() {
        var opportunityid = $('#editopportunityID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityid: opportunityid},
            url: "<?php echo site_url('crm/load_opportunity_all_product'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_product').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_product() {
        //  $('#frm_opportunity_add_product')[0].reset();
        // $('#frm_opportunity_add_product').bootstrapValidator('resetForm', true);
        $('#show_all_product').addClass('hide');
        $('#show_add_product_button').addClass('hide');
         $("#productID").val(null).trigger("change");
       // $("#transactionCurrencyID").val(null).trigger("change");
        $('#show_add_product').removeClass('hide');
       
    }

    function close_add_product() {
        $('#show_add_product').addClass('hide');
        $('#show_all_product').removeClass('hide');
        $("#productID").val(null).trigger("change");
      // $("#transactionCurrencyID").val(null).trigger("change");
        $('#show_add_product_button').removeClass('hide');
    }
    function edit_opportunity_product(opportunityid){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'opporProductID': opportunityid},
            url: "<?php echo site_url('CrmLead/load_opportunity_productsEdit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                show_add_product();
                if (!jQuery.isEmptyObject(data)) {
                    $('#opportunityproductid').val(data['opportunityProductID']);
                    $('#productID').val(data['productID']).trigger("change");
                    $('#product_description').val(data['productDescription']);
                   // $('#transactionCurrencyID').val(data['transactionCurrencyID']);
                    $('#edprice').val(data['price']);
                    $('#subscriptionamount').val(data['subscriptionAmount']);
                    $('#implementationamount').val(data['ImplementationAmount']);

                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_opportunity_product(opportunityid){
       
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
                    data: {'opportunityid': opportunityid},
                    url: "<?php echo site_url('CrmLead/remove_opportunity_product'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        opporunity_products();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        });

       

    }


    function productdetails(val) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'productid': val},
            url: "<?php echo site_url('CrmLead/load_opportunity_products_prices'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#price').val(data['otherAmount']);
                    $('#subscriptionamount').val(data['subscriptionAmount']);
                    $('#implementationamount').val(data['ImplementationAmount']);

                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>


