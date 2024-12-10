<?php
$currency_arr = crm_all_currency_new_drop();
$product_arr = all_crm_product_master();
$admin = crm_isSuperAdmin();
$status_arr = lead_status();
$employees_arr = fetch_employees_by_company_multiple(false);
$organization_arr = load_all_organizations();
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
<?php
if (!empty($header)) {
//print_r($header);
    ?>
    <div class="row">
        <?php if(($header['isClosed'] == 0)){ ?>
            <div class="col-sm-1">
                <button type="button" class="btn btn-info"
                        onclick="check_edit_approval()">
                    Edit
                </button>
            </div>
        <?php } ?>
        <?php if(($header['isClosed'] == 0) || ($header['isClosed'] == 1)){ ?>
            <div class="col-sm-2">
                <button type="button" class="btn btn-info" onclick="convertToOpportunity(<?php echo $header['leadID']; ?>)">
                    Convert to Opportunity
                </button>
            </div>

        <?php } ?>

        <div class="col-sm-1 text-center">
            &nbsp;
        </div>
        <div class="col-sm-5">
            &nbsp;
        </div>
        <div class="col-sm-4 text-center">
            &nbsp;
        </div>
    </div>
    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <?php if($page == 'LeadTask'){?>
        <li><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <?php }else {?>
            <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <?php }?>
        <li><a href="#emails" onclick="emailssentview()"  data-toggle="tab"><i class="fa fa-television"></i>Emails </a></li>
        <li><a href="#notes" onclick="lead_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a></li>
        <li><a href="#files" onclick="lead_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Files
            </a></li>
        <?php if($page == 'LeadTask'){?>
        <li class="active"><a href="#tasks" onclick="lead_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
        <?php }else {?>
            <li><a href="#tasks" onclick="lead_tasks()" data-toggle="tab"><i class="fa fa-television"></i>Tasks </a></li>
        <?php }?>
        <li><a href="#products" onclick="lead_products()" data-toggle="tab"><i class="fa fa-television"></i>Products
            </a></li>
    </ul>
    <input type="hidden" id="editleadID" value="<?php echo $header['leadID'] ?>">
    <div class="tab-content">
        <?php if($page == 'LeadTask'){?>
        <div class="tab-pane" id="about">
            <?php }else {?>
            <div class="tab-pane active" id="about">
            <?php }?>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>LEAD INFORMATION</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Document Code</span></td>
                            <td><span
                                        class="tddata"><?php echo $header['documentSystemCodelead']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Full Name</span></td>
                            <td><span
                                    class="tddata"><?php echo $header['firstName'] . " " . $header['lastName']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Title</span></td>

                            <td><span class="tddata"><?php echo $header['title']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Organization</span></td>
                            <td><span class="tddata"><?php
                                    if ($header['organization'] == '') { ?>
                                        <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"  onclick="fetchPage('system/crm/organization_edit_view','<?php echo $header['linkedorganizationID'] ?>','View Organization','<?php echo $header['leadID'] ?>','Lead')"><?php echo $header['linkedOrganizationName'] ?></a></strong></div>
                                        <?php
                                    } else {
                                        echo $header['organization'];
                                    }
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Status</span></td>
                            <td><span class="tddata"><?php echo $header['statusdescription'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">User Responsible</span></td>
                            <td><span class="tddata"><?php echo $header['responsiblePerson'] ?></span>
                            </td>
                        </tr>
                        <!--                        <tr>
                            <td class="ralign"><span class="title">Lead Rating</span></td>
                            <td><span class="tddata"><?php /*//echo $header['statusdescription'] */ ?></span>
                            </td>
                        </tr>-->
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="fileinput-new">
                        <?php if ($header['leadImage'] != '') { ?>
                            <img src="<?php echo $lead; ?>"
                                 id="changeImg" class="img-responsive" style="width: 200px; height: 200px;border-radius: 100%;">
                            <?php
                        } else { ?>
                            <img src="<?php echo $noimage; ?>" id="changeImg"
                                 style="width: 200px; height: 200px;border-radius: 100%;">
                           <!-- <div style="width: 200px; height: 200px; background-color: <?php /*echo $color = getColor()*/?>; border-radius: 100%; padding-top: 25px " id="changeImg"><span style="font-size:100px; color: white;"><center><?php /*$str = $header['firstName']; echo $str[0];*/?></center></span></div>-->
                        <?php } ?>
                        <input type="file" name="leadImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>ADDITIONAL INFORMATION</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Email</span></td>

                            <td><span class="tddata"><?php echo $header['leademail'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Phone (Mobile)</span></td>
                            <td><span class="tddata"><?php echo $header['phoneMobile'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Phone (Home)</span></td>
                            <td><span class="tddata"><?php echo $header['phoneHome'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Fax</span></td>
                            <td><span class="tddata"><?php echo $header['fax'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Website</span></td>
                            <td><span class="tddata">
                                    <a class="link-person noselect" target="_blank" href="http://<?php echo $header['leadWebsite'] ?>"><?php echo $header['leadWebsite'] ?></a>
                                    </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Industry</span></td>
                            <td><span class="tddata"><?php echo $header['industry'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Number of Employees</span></td>
                            <td><span class="tddata"><?php echo $header['numberofEmployees'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Lead Source</span></td>
                            <td><span class="tddata"><?php echo $header['sourceDescription'] ?></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>ADDRESS</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Postal Code</span></td>

                            <td><span class="tddata"><?php echo $header['postalCode'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">City</span></td>
                            <td><span class="tddata"><?php echo $header['city'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">State</span></td>
                            <td><span class="tddata"><?php echo $header['state'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Country</span></td>
                            <td><span class="tddata"><?php echo $header['CountryDes'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Address</span></td>
                            <td><span class="tddata"><?php echo $header['address'] ?></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>LEAD DESCRIPTION</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td style="padding-left: 5%;"><span class="tddata"><?php echo $header['leadDescription'] ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
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
                    <td class="ralign"><span class="title">Lead Created By</span></td>
                    <td><span class="tddata"><?php echo $header['leadCreatedUser'] ?></span></td>
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
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Lead Notes </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Note
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_lead_add_notes"'); ?>
            <input type="hidden" name="leadID" value="<?php echo $header['leadID']; ?>">

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
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Lead Files </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Files
                    </button>
                </div>
            </div>
            <div class="row hide" id="add_attachemnt_show">
                <?php echo form_open_multipart('', 'id="lead_attachment_uplode_form" class="form-inline"'); ?>
                <div class="col-sm-10" style="margin-left: 3%">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="leadattachmentDescription"
                                   name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                            <input type="hidden" class="form-control" id="documentID" name="documentID" value="5">
                            <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                   value="Lead">
                            <input type="hidden" class="form-control" id="lead_documentAutoID" name="documentAutoID"
                                   value="<?php echo $header['leadID']; ?>">
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
            <?php if($page == 'LeadTask'){?>
        <div class="tab-pane active" id="tasks">
            <?php }else {?>
            <div class="tab-pane" id="tasks">
            <?php }?>
            <br>

            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Lead Tasks </h4></div>
                <div class="col-md-4">
                    <button type="button"
                            onclick="fetchPage('system/crm/create_new_task','','Create Task',5, <?php echo $header['leadID']; ?>);"
                            class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Task
                    </button>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12">

                    <div id="show_all_tasks">

                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="products">
            <br>

            <div class="row" id="show_add_product_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Lead Products </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_product()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Products
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_lead_add_product"'); ?>
            <input type="hidden" name="leadID" value="<?php echo $header['leadID']; ?>">
            <input type="hidden" name="leadProductID" id="leadProductID_edit">

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
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Subscription Amount</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="subscriptionamount" id="subscriptionamount" class="form-control number">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Implementation Amount
                        </label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="implementationamount" id="implementationamount" class="form-control number">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Other Amount</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="price" id="price" class="form-control number">
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
                    <input type="hidden" id="leadid" name="leadid" value="<?php echo $header['leadID']; ?>">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>To</label>
                            <input class="form-control" placeholder="To:" name="To" autocomplete="off" value="<?php echo $header['leademail'];?>" readonly>
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
    <?php
}
?>
            <div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="leadmodel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="usergroup-title">Convert To Opportunity<br><br><label style="font-size: 70%;font-weight:500; color:red;">Please fill the following fields before converting to opportunity</label></h4>
                        </div>
                        <?php echo form_open('', 'role="form" id="lead_validation_master_form"'); ?>
                        <div class="modal-body">
                            <input type="hidden" id="leadID" name="leadID">
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-3 col-md-offset-1">
                                    <label class="title">Status</label>
                                </div>
                                <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control select2" id="statusID"'); ?>
                    <!--<input type="text" name="partNumber" id="partNumber" class="form-control" placeholder="Part No" >-->
                    <span class="input-req-inner"></span>
                </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3 col-md-offset-1">
                                    <label class="title">User Responsible</label>
                                </div>
                                <div class="form-group col-sm-6">
                                      <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('responsiblePersonEmpID', $employees_arr,current_userID(), 'class="form-control select2" id="responsiblePersonEmpID"'); ?>
                                <span class="input-req-inner"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px;" id="organization_text">

                                <div class="form-group col-sm-3  col-md-offset-1" >
                                    <label class="title">Organization</label><!--Organization-->

                                </div>
                                <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                <input type="text" name="organization" id="organization" class="form-control"
                       placeholder="Organization" autocomplete="off"><!--Organization-->
                    <span class="input-req-innernotmandertory"></span></span>
                                </div>
                                <div class="col-sm-1 search_cancel" style="width: 3%;">
                                    <i class="fa fa-link" onclick="linkOrganization()" title="Link to Organization" aria-hidden="true"
                                       style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                                </div>
                            </div>
                            <div class="row hide" id="linkorganization_text" style="margin-top: 10px;">
                                <div class="form-group col-sm-1">
                                    &nbsp
                                </div>
                                <div class="form-group col-sm-2  col-md-offset-1">
                                    <label class="title">Link Organization</label><!--Link Organization-->

                                </div>
                                <div class="form-group col-sm-6">
                                    <?php echo form_dropdown('linkorganization', $organization_arr, '', 'class="form-control select2" id="linkorganization"'); ?>
                                </div>
                                <div class="col-sm-1 search_cancel" style="width: 3%;">
                                    <i class="fa fa-external-link" onclick="unlinkOrganization()" title="Link to Organization"
                                       aria-hidden="true" style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3 col-md-offset-1">
                                    <label class="title">Email</label>
                                </div>
                                <div class="form-group col-sm-6">
                                      <span class="input-req" title="Required Field">
                                    <input type="text" name="email" id="email"
                                           class="form-control valcontact"
                                           placeholder="<?php echo $this->lang->line('common_email');?>"
                                           autocomplete="off">
                                     <span class="input-req-inner"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3 col-md-offset-1">
                                    <label class="title">Phone No</label>
                                </div>
                                <div class="form-group col-sm-6">
                                      <span class="input-req" title="Required Field">
                                    <input type="text" name="phoneMobile" id="phoneMobile"
                                           class="form-control valcontact"
                                           placeholder="<?php echo $this->lang->line('crm_phone_mobile');?>"
                                           autocomplete="off">
                                     <span class="input-req-inner"></span>
                                </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" onclick="save_opportunity()" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                                           aria-hidden="true"></span> Save & Convert
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
        $('.select2').select2();
        <?php if($page == 'LeadTask'){?>
        lead_tasks();
        <?php }?>
        $("#description").wysihtml5();


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

        $('#frm_lead_add_notes').bootstrapValidator({
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
                url: "<?php echo site_url('CrmLead/add_lead_notes'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_note();
                        lead_notes();
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

        $('#frm_lead_add_product').bootstrapValidator({
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
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmLead/add_lead_product'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_product();
                        lead_products();
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

        number_validation();


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
            url: "<?php echo site_url('CrmMailBox/compose_email_lead'); ?>",
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


    function lead_notes() {
        var leadID = $('#editleadID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {leadID: leadID},
            url: "<?php echo site_url('CrmLead/load_lead_all_notes'); ?>",
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
        $('#frm_lead_add_notes')[0].reset();
        $('#frm_lead_add_notes').bootstrapValidator('resetForm', true);
    }

    function close_add_note() {
        $('#show_add_notes').addClass('hide');
        $('#show_all_notes').removeClass('hide');
        $('#show_add_notes_button').removeClass('hide');
    }

    function document_uplode() {
        var formData = new FormData($("#lead_attachment_uplode_form")[0]);
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
                    $('#leadattachmentDescription').val('');
                    lead_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function lead_attachments() {
        var leadID = $('#editleadID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {leadID: leadID},
            url: "<?php echo site_url('CrmLead/load_lead_all_attachments'); ?>",
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
                            lead_attachments();
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

    function lead_tasks() {
        var leadID = $('#editleadID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {leadID: leadID},
            url: "<?php echo site_url('CrmLead/load_lead_all_tasks'); ?>",
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

    function profileImageUploadLead() {
        var imgageVal = new FormData();
        imgageVal.append('leadID', $('#editleadID').val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#lead_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('CrmLead/lead_image_upload'); ?>",
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

    function show_add_product() {
        $('#show_all_product').addClass('hide');
        $('#show_add_product_button').addClass('hide');
        $('#show_add_product').removeClass('hide');
        $('#frm_lead_add_product')[0].reset();
        $('#frm_lead_add_product').bootstrapValidator('resetForm', true);
    }

    function close_add_product() {
        $('#show_add_product').addClass('hide');
        $('#show_all_product').removeClass('hide');
        $('#show_add_product_button').removeClass('hide');
    }

    function lead_products() {
        var leadID = $('#editleadID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {leadID: leadID},
            url: "<?php echo site_url('CrmLead/load_leads_all_product'); ?>",
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

    function convertToOpportunity(id) {
        swal({
                title: "Are you sure?",
                text: "You want to convert this Lead to Opportunity !",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Convert"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'leadID': id},
                    url: "<?php echo site_url('CrmLead/convert_leadToOpportunity'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data[0] == 's') {
                            myAlert(data[0], data[1], data[2]);
                            fetchPage('system/crm/lead_management', '', 'Leads');
                        }else if((data[0] == 'e')&&(data[2] == '1'))
                        {
                            edit_Opportunity(id);
                        }else
                        {
                            myAlert(data[0], data[1], data[2]);
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function edit_lead_product(leadProductID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'leadProductID': leadProductID},
                url: "<?php echo site_url('CrmLead/load_lead_productsEdit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    show_add_product();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#leadProductID_edit').val(data['leadProductID']);
                        $('#productID').val(data['productID']);
                        $('#product_description').val(data['productDescription']);
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']);
                        $('#price').val(data['price']);
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

    function delete_lead_product(leadProductID){
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
                    data: {leadProductID: leadProductID},
                    url: "<?php echo site_url('CrmLead/load_lead_productsDelete'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            lead_products();
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
                            lead_notes();
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
        if(('<?php echo $admin['isSuperAdmin']?>' == 1)){

            fetchPage('system/crm/create_lead',<?php echo $header['leadID'] ?>,'Edit Lead','CRM');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['crtduser'] ?>){
            fetchPage('system/crm/create_lead',<?php echo $header['leadID'] ?>,'Edit Lead','CRM');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['responsiblePersonEmpID'] ?>){
            fetchPage('system/crm/create_lead',<?php echo $header['leadID'] ?>,'Edit Lead','CRM');
        }else{
            myAlert('w','You do not have permission to edit this lead')
        }
    }
    function emailssentview() {
        var leadID = $('#editleadID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'leadID': leadID},
            url: "<?php echo site_url('CrmMailbox/leadwise_emails'); ?>",
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
    function compose_email_contact_lead()
    {
        $('#emailcomposemasterform')[0].reset();
        $('#emailcomposemasterform').bootstrapValidator('resetForm', true);

        $('#emailcomposemodel').modal('show');
    }

    function edit_Opportunity(id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {leadid: id},
            url: "<?php echo site_url('CrmLead/leads_covert_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#statusID').val(data['statusID']).change();
                    $('#responsiblePersonEmpID').val(data['responsiblePersonEmpID']).change();
                    $('#phoneMobile').val(data['phoneMobile']).change();
                    $('#email').val(data['email']).change();
                    $('#leadID').val(data['leadID']);
                    if (data['organization'] != '') {
                        $('#organization').val(data['organization']);
                    } else {
                        linkOrganization();
                        $('#linkorganization').val(data['linkedorganizationID']).change();
                    }
                    $('#leadmodel').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function linkOrganization() {
        $('#organization').val('');
        $('#linkorganization_text').removeClass('hide');
        $('#organization_text').addClass('hide');
    }

    function unlinkOrganization() {
        $('#linkorganization_text').addClass('hide');
        $('#organization_text').removeClass('hide');
    }
    function save_opportunity() {
        var data = $('#lead_validation_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CrmLead/save_lead_opportunity_convert_validation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    myAlert(data[0],data[1]);
                    $('#leadmodel').modal('hide');
                    setTimeout(function(){  fetchPage('system/crm/lead_management', '', 'Leads') }, 50);
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


