<?php
$this->load->helper('buyback_helper');
$supplier_arr = all_supplier_drop();
$customer_arr = all_customer_drop();
$warehouse_arr = all_delivery_location_drop();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employees_arr = all_employee_drop();
?>
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
</style>
<?php
if (!empty($header)) {
    //print_r($header);
    ?>
    <div class="row">
        <div class="col-md-5">
            <div style="font-size: 16px; font-weight: 600;"><?php echo $header['farmName']; ?></div>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/buyback/create_farm',<?php echo $header['farmID'] ?>,'Edit Contact','CRM');">
                <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                Edit
            </button>
        </div>
    </div>
    <br>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <li><a href="#cage" onclick="cage_master()" data-toggle="tab"><i class="fa fa-television"></i>Cage & Batch </a>
        </li>
        <!--<li><a href="#batch" onclick="batch_master()" data-toggle="tab"><i class="fa fa-television"></i>Batch Master</a>
        </li>-->
        <!--<li><a href="#party" onclick="party_master()" data-toggle="tab"><i class="fa fa-television"></i>Party</a></li>-->
        <li><a href="#dealers" onclick="dealers_master()" data-toggle="tab"><i class="fa fa-television"></i>Dealers</a>
        </li>
        <li><a href="#fieldOfficers" onclick="fieldOfficers_master()" data-toggle="tab"><i class="fa fa-television"></i>Field
                Officers</a>
        </li>
        <!--        <li><a href="#warehouse" onclick="warehouse_master()" data-toggle="tab"><i class="fa fa-television"></i>Warehouse</a>
                </li>-->
        <li><a href="#notes" onclick="farm_notes()" data-toggle="tab"><i class="fa fa-television"></i>Notes </a></li>
        <li><a href="#files" onclick="farm_attachments()" data-toggle="tab"><i class="fa fa-television"></i>Files</a>
        </li>
       <!-- <li><a href="#batchoverview" onclick="batchoverview()" data-toggle="tab"><i class="fa fa-television"></i>Batch
                overview</a>
        </li>-->
    </ul>
    <input type="hidden" id="editfarmID" value="<?php echo $header['farmID'] ?>">
    <div class="tab-content">
        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>FARM NAME AND DETAIL</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Name</span></td>
                            <td><span class="tddata"><?php echo $header['farmName']; ?></span></td>
                        </tr>
                         <tr>
                            <td class="ralign"><span class="title">Farm Code</span></td>
                            <td><span class="tddata"><?php echo $header['farmSystemCode']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Secondary Code</span></td>
                            <td><span class="tddata"><?php echo $header['farmSecondaryCode']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Email</span></td>
                            <td><span class="tddata"><?php echo $header['email']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Area</span></td>

                            <td><span class="tddata"><?php echo $header['locationDes']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Sub Area</span></td>

                            <td><span class="tddata"><?php echo $header['subdescription']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Farm Type</span></td>

                            <td><span class="tddata"><?php
                                    if ($header['farmType'] == 1) {
                                        echo "Third Party";
                                    } else {
                                        echo "Own";
                                    }
                                    ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Registered Date</span></td>

                            <td><span class="tddata"><?php echo $header['registeredDate']; ?></span></td>
                        </tr>
                       <!-- <tr>
                            <td class="ralign"><span class="title">No Of Cages</span></td>

                            <td><span class="tddata"><?php /*echo $header['noOfCages']; */?></span></td>
                        </tr>-->
                        <tr>
                            <td class="ralign"><span class="title">Birds Capacity</span></td>

                            <td><span class="tddata"><?php echo $header['capacity']; ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Farm Currency</span></td>

                            <td><span
                                        class="tddata"><?php echo $header['CurrencyName'] . " ( " . $header['CurrencyCode'] . " )"; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Farmer Deposit Account</span></td>

                            <td><span
                                        class="tddata"><?php echo $header['DepositAccountCode'] . " | " . $header['depositAccDescription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Farmer Liability Account</span></td>

                            <td><span
                                        class="tddata"><?php echo $header['systemAccountCode'] . " | " . $header['GLDescription']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Farmer Status</span></td>
                            <td><span class="tddata">
                                                            <?php if ($header['isActive'] == 1) { ?>
                                                                <span class="label"
                                                                      style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Active</span>
                                                            <?php } else { ?>
                                                                <span class="label"
                                                                      style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Active</span>
                                                            <?php } ?>
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="fileinput-new thumbnail" style="border: none">
                        <?php if ($header['farmImage'] != '') {
                            $farmImg = get_all_buyback_images($header['farmImage'],'uploads/buyback/farmMaster/'); ?>
                            <img src="<?php echo $farmImg; ?>"
                                 id="changeImg" style="width: 200px; height: 145px;">
                            <?php
                        } else { ?>
                           <!-- <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 200px; height: 145px;"> -->
                            <div style="width: 200px; height: 200px; background-color: <?php echo $color = getColor()?>; border-radius: 100%; padding-top: 25px " id="changeImg"><span style="font-size:100px; color: white;"><center><?php $str = $header['farmName']; echo $str[0];?></center></span></div>
                        <?php } ?>
                        <input type="file" name="contactImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>CONTACT DETAILS</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Contact Person</span></td>

                            <td><span class="tddata"><?php echo $header['contactPerson'] ?></span>
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
                            <td class="ralign"><span class="title">Address</span></td>
                            <td><span class="tddata"><?php echo $header['address'] ?></span></td>
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
                            <td class="ralign"><span class="title">Postal Code</span></td>

                            <td><span class="tddata"><?php echo $header['postalCode'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Country</span></td>
                            <td><span class="tddata"><?php echo $header['CountryDes'] ?></span></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <br>


            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>BANK DETAILS</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9">
                    <table class="property-table">
                        <tbody>
                        <tr>
                            <td class="ralign"><span class="title">Bank Name</span></td>
                            <td><span class="tddata"><?php echo $header['Bank'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Branch</span></td>
                            <td><span class="tddata"><?php echo $header['Branch'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Account Name</span></td>
                            <td><span class="tddata"><?php echo $header['bankAccountName'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Account Number</span></td>

                            <td><span class="tddata"><?php echo $header['bankAccountNo'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ralign"><span class="title">Address</span></td>
                            <td><span class="tddata"><?php echo $header['bankAddress'] ?></span></td>
                        </tr>

                        </tbody>
                    </table>
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
                    <td class="ralign"><span class="title">Contact Created By</span></td>
                    <td><span class="tddata"><?php echo $header['createdUserName'] ?></span></td>
                </tr>
                </tbody>
            </table>
        </div>
       <!-- <div class="tab-pane" id="batch">
            <br>

            <div class="row" id="show_add_batch_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Batches </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_batch()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Batch
                    </button>
                </div>
            </div>
            <div class="row">

            <div style="margin-left: 4%">
                    <strong>WIP Amount : &nbsp <?php /*echo $wip; */?></strong>
                </div>
            </div>
            <br>
            <?php /*echo form_open('', 'role="form" id="frm_farm_add_batch"'); */?>
            <input type="hidden" name="farmID" value="<?php /*echo $header['farmID']; */?>">
            <input type="hidden" name="batchMasterID" id="edit_batchMasterID">

            <div id="show_add_batch" class="hide">
                <header class="head-title">
                    <h2>Add New Batch</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Start Date</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <div class="input-group" id="datepic_batchStartDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchStartDate"
                                   value="<?php /*echo $current_date; */?>" id="batchStartDate" class="form-control">
                        </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Closing Date</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <div class="input-group" id="datepic_batchClosingDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchClosingDate"
                                   value="<?php /*echo $current_date; */?>" id="batchClosingDate" class="form-control">
                        </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Batch Days</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="head-title" id="daysShowing_div"
                              style="color: darkred;font-size: 18px;font-weight: 600;">0</span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="descrip" id="descrip" class="form-control"
                               placeholder="Description">
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Work in Progress</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                            <?php /*echo form_dropdown('WIPGLAutoID', buyback_workin_progress_gl_codes(), '', 'class="form-control select2" id="WIPGLAutoID"'); */?>
                                <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Direct Wages</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                            <?php /*echo form_dropdown('DirectWagesGLAutoID', buyback_all_gl_codes(), '', 'class="form-control select2" id="DirectWagesGLAutoID"'); */?>
                                <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row hidden" style="margin-top: 10px;">
                    <div class="form-group col-sm-2" style="padding-right: 0px;">
                        <label class="title">IS Closed</label>
                    </div>
                    <div class="form-group col-sm-1" style="padding-left: 0px;">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="isClosed" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isClosed" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_batch()">Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-7">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_batch"></div>
        </div>-->

        <div class="tab-pane" id="cage">
            <br>
            <div class="row" id="show_add_cage_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Cages </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_Cage()" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>Add Cage
                    </button>
                </div>
            </div>
            <br>
            <div id="show_all_Cages"></div>
            <?php echo form_open('', 'role="form" id="frm_farm_add_batch"'); ?>
            <input type="hidden" name="farmID" value="<?php echo $header['farmID']; ?>">
            <input type="hidden" name="cageID" id="cageID">
            <input type="hidden" name="batchMasterID" id="edit_batchMasterID">

            <div id="show_add_batch" class="hide">
                <header class="head-title">
                    <h2>Add New Batch</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="descrip" id="descrip" class="form-control"
                               placeholder="Description">
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Start Date</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <div class="input-group" id="datepic_batchStartDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchStartDate"
                                   value="<?php echo $current_date; ?>" id="batchStartDate" class="form-control">
                        </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Closing Date</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="input-req" title="Required Field">
                        <div class="input-group" id="datepic_batchClosingDate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchClosingDate"
                                   value="<?php echo $current_date; ?>" id="batchClosingDate" class="form-control">
                        </div>
                            <span class="input-req-inner" style="z-index: 100"></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Batch Days</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <span class="head-title" id="daysShowing_div"
                              style="color: darkred;font-size: 18px;font-weight: 600;">0</span>
                    </div>
                </div>
<hr>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Work in Progress</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('WIPGLAutoID', buyback_workin_progress_gl_codes(), '', 'class="form-control select2" id="WIPGLAutoID"'); ?>
                                <span class="input-req-inner"></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Direct Wages</label>
                    </div>
                    <div class="form-group col-sm-3">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('DirectWagesGLAutoID', buyback_all_gl_codes(), '', 'class="form-control select2" id="DirectWagesGLAutoID"'); ?>
                                <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row hidden" style="margin-top: 10px;">
                    <div class="form-group col-sm-2" style="padding-right: 0px;">
                        <label class="title">IS Closed</label>
                    </div>
                    <div class="form-group col-sm-1" style="padding-left: 0px;">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="isClosed" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isClosed" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-sm-10">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_batch()">Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-2">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_batch" class="hide">
            </div>
            <br>
            <div class="row hide" id="close_all_batch">
                <div class="form-group pull-right">
                    <button class="btn btn-danger pull-right" style="margin-right: 20px;" type="button" onclick="close_all_batch()">Close
                    </button>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="batchoverview">
            <br>

            <div class="row" id="show_add_batch_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Batch Overview </h4></div>
            </div>
            <br>

            <input type="hidden" id="farmID" value="<?php echo $header['farmID'] ?>">
            <div id="show_batch_overview">

            </div>
        </div>
        <!--<div class="tab-pane" id="party">
            <br>
            <div class="row" id="show_add_party_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Party</h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_party()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> Add Party
                    </button>
                </div>
            </div>
            <br>
            <?php /*echo form_open('', 'role="form" id="frm_farm_add_party"'); */ ?>
            <input type="hidden" name="farmID" value="<?php /*echo $header['farmID']; */ ?>">
            <input type="hidden" name="farmPartyAutoID" id="edit_farmPartyAutoID">

            <div id="show_add_party" class="hide">
                <header class="head-title">
                    <h2>Add New Party</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Party Type</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <?php /*echo form_dropdown('partyType', array('' => 'Select Type', '1' => 'Supplier', '2' => 'Customer'), '1', 'class="form-control" id="partyType" onchange="ChangeParty(this.value)"'); */ ?>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;" id="div_supplier_type">
                    <div class="form-group col-sm-2">
                        <label class="title">Supplier</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php /*echo form_dropdown('supplierPrimaryCode', $supplier_arr, '', 'class="form-control select2" id="supplierPrimaryCode"'); */ ?>
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="div_customer_type">
                    <div class="form-group col-sm-2">
                        <label class="title">Customer</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php /*echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID"'); */ ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">IS Active</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="isActive" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isActive" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_party()">Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-7">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_party"></div>
        </div>-->
        <div class="tab-pane" id="dealers">
            <br>

            <div class="row" id="show_add_dealers_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Dealers</h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_dealers()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Dealers
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_farm_add_dealers"'); ?>
            <input type="hidden" name="farmID" value="<?php echo $header['farmID']; ?>">
            <input type="hidden" name="farmDealerID" id="edit_farmDealerID">

            <div id="show_add_dealers" class="hide">
                <header class="head-title">
                    <h2>Add New Dealer</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Dealer</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="dealers_customerID"'); ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">IS Active</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="dealers_isActive" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isActive" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_dealers()">Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-7">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_dealers"></div>
        </div>
        <div class="tab-pane" id="fieldOfficers">
            <br>

            <div class="row" id="show_add_fieldOfficers_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Field Officers</h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_fieldOfficers()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Field Officer
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_farm_add_fieldOfficers"'); ?>
            <input type="hidden" name="farmID" value="<?php echo $header['farmID']; ?>">
            <input type="hidden" name="fieldOfficerID" id="edit_fieldOfficerID">

            <div id="show_add_fieldOfficers" class="hide">
                <header class="head-title">
                    <h2>Add New Field Officer</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Field Officer</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php echo form_dropdown('employeeID', $employees_arr, '', 'class="form-control select2" id="fieldOfficers_employeeID"'); ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">IS Active</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="fieldOfficers_isActive"
                                                                              type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isActive" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_fieldOfficers()">
                            Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-7">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_fieldOfficers"></div>
        </div>
        <div class="tab-pane" id="warehouse">
            <br>

            <div class="row" id="show_add_warehouse_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Warehouse</h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_warehouse()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Warehouse
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_farm_add_warehouse"'); ?>
            <input type="hidden" name="farmID" value="<?php echo $header['farmID']; ?>">
            <input type="hidden" name="farmWarehouseID" id="edit_farmWarehouseID">

            <div id="show_add_warehouse" class="hide">
                <header class="head-title">
                    <h2>Add New Warehouse</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Warehouse</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php echo form_dropdown('warehouseMasterID', $warehouse_arr, '', 'class="form-control select2" id="warehouseMasterID"'); ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">IS Active</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <div class="col-sm-1">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="warehouse_isActive" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="isActive" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-5">
                        <button class="btn btn-danger pull-right" type="button" onclick="close_add_warehouse()">Close
                        </button>
                        <button class="btn btn-primary pull-right" type="submit" style="margin-right: 1%;">Add</button>
                    </div>
                    <div class="form-group col-sm-7">
                        &nbsp
                    </div>
                </div>
            </div>
            </form>
            <div id="show_all_warehouse"></div>
        </div>
        <div class="tab-pane" id="notes">
            <br>

            <div class="row" id="show_add_notes_button">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Notes </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_note()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Note
                    </button>
                </div>
            </div>
            <br>
            <?php echo form_open('', 'role="form" id="frm_farm_add_notes"'); ?>
            <input type="hidden" name="farmID" value="<?php echo $header['farmID']; ?>">

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
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Farm Files </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> Add Files
                    </button>
                </div>
            </div>
            <div class="row hide" id="add_attachemnt_show">
                <?php echo form_open_multipart('', 'id="farm_attachment_uplode_form" class="form-inline"'); ?>
                <div class="col-sm-10" style="margin-left: 3%">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="farmattachmentDescription"
                                   name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                            <input type="hidden" class="form-control" id="documentID" name="documentID" value="1">
                            <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                   value="farmMaster">
                            <input type="hidden" class="form-control" id="farm_documentAutoID" name="documentAutoID"
                                   value="<?php echo $header['farmID']; ?>">
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
                        <button type="button" class="btn btn-default" onclick="farm_document_uplode()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                    </div>
                </div>

            </div>
            <br>

            <div id="show_all_attachments"></div>
        </div>
    </div>

    <?php
}
?>
<!--modal report-->
<div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Feed Schedule</h4>
            </div>
            <div class="modal-body">
                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="buyback_production_report_modal_farm_batch" tabindex="2" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Production Statement<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="modal-body" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979">
                <div id="productionReportDrilldown_batch"></div>
            </div>
            <div class="modal-body" id="PaymentHistoryModal" style="margin: 10px; box-shadow: 1px 1px 1px 1px #807979">
                <div id="PaymentHistory"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-lg" id="AddCage_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Create New Cage</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_CreateNewCage">
                    <input type="text" name="cageID" id="edit_cageID" class="form-control hidden">
                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label"> Cage Name </label>
                        <div class="col-sm-7">
                            <input type="text" name="cage_name" maxlength="10" id="cage_name" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Create_new_cage()">
                    <?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/knob/jquery.knob.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/countup/countUp.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        //Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('#datepic_batchStartDate').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('#datepic_batchClosingDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculateBatch_Date()
        });

        $('.select2').select2();

        $("#description").wysihtml5();

        $('#frm_farm_add_notes').bootstrapValidator({
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
                url: "<?php echo site_url('Buyback/add_farm_notes'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_note();

                        farm_notes();
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

        $('#frm_farm_add_batch').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                batchStartDate: {validators: {notEmpty: {message: 'Start Date is required.'}}},
                batchClosingDate: {validators: {notEmpty: {message: 'Closing Date is required.'}}},
                WIPGLAutoID: {validators: {notEmpty: {message: 'Work in Progress is required.'}}},
                DirectWagesGLAutoID: {validators: {notEmpty: {message: 'Direct Wages is required.'}}},
                descrip: {validators: {notEmpty: {message: 'Description is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            $("#batchStartDate").prop("disabled", false);
            $("#batchClosingDate").prop("disabled", false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/add_farm_batch'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_batch();
                        cage_master();
                     //   batch_master();
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

        $('#frm_farm_add_party').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                partyType: {validators: {notEmpty: {message: 'Party Type is required.'}}}
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
                url: "<?php echo site_url('Buyback/add_farm_party'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_party();
                        party_master();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        });

        $('#frm_farm_add_dealers').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                customerID: {validators: {notEmpty: {message: 'Dealer is required.'}}}
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
                url: "<?php echo site_url('Buyback/add_farm_dealers'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_dealers();
                        dealers_master();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        });

        $('#frm_farm_add_fieldOfficers').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                employeeID: {validators: {notEmpty: {message: 'Employee is required.'}}}
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
                url: "<?php echo site_url('Buyback/add_farm_fieldOfficer'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_fieldOfficers();
                        fieldOfficers_master();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        });

        $('#frm_farm_add_warehouse').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                customerID: {validators: {notEmpty: {message: 'Dealer is required.'}}}
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
                url: "<?php echo site_url('Buyback/add_farm_warehouse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        close_add_warehouse();
                        warehouse_master();
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        });

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function farm_notes() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_notes'); ?>",
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
        $('#frm_farm_add_notes')[0].reset();
        $('#frm_farm_add_notes').bootstrapValidator('resetForm', true);
    }

    function close_add_note() {
        $('#show_add_notes').addClass('hide');
        $('#show_all_notes').removeClass('hide');
        $('#show_add_notes_button').removeClass('hide');
    }

    function show_add_batch(id) {
        $('#show_all_Cages').addClass('hide');
        $('#show_add_cage_button').addClass('hide');
        $('#show_add_batch').removeClass('hide');
        $('#frm_farm_add_batch')[0].reset();
        $("#WIPGLAutoID").val(null).trigger("change");
        $("#DirectWagesGLAutoID").val(null).trigger("change");
        $('#frm_farm_add_batch').bootstrapValidator('resetForm', true);
        $('#cageID').val(id);
    }

    function show_all_batch(id) {
        $('#show_all_Cages').addClass('hide');
        $('#show_add_cage_button').addClass('hide');
        $('#show_all_batch').removeClass('hide');
        $('#close_all_batch').removeClass('hide');
        $('#show_add_batch_button').addClass('hide');
        batch_master(id);
    }

    function close_all_batch(id) {
        $('#show_all_Cages').removeClass('hide');
        $('#show_add_cage_button').removeClass('hide');
        $('#show_all_batch').addClass('hide');
        $('#close_all_batch').addClass('hide');
        $('#show_add_batch_button').removeClass('hide');
        $('#edit_batchMasterID').val('');
        $('#cageID').val('');
        $('#show_add_batch').addClass('hide');
    }

    function close_add_batch() {
        $('#edit_batchMasterID').val('');
        $('#cageID').val('');
        $('#show_add_batch').addClass('hide');
        $('#show_all_batch').addClass('hide');
        $('#show_add_batch_button').removeClass('hide');
        $('#show_all_Cages').removeClass('hide');
        $('#show_add_cage_button').addClass('hide');
        $('#close_all_batch').addClass('hide');
    }

    function farm_document_uplode() {
        var formData = new FormData($("#farm_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Buyback/attachement_upload'); ?>",
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
                    $('#farmattachmentDescription').val('');
                    farm_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function farm_attachments() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_attachments'); ?>",
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

    function delete_farmAttachment(id, fileName) {
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
                    url: "<?php echo site_url('Buyback/delete_farmAttachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            farm_attachments();
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

    function cage_master() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_cages'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_Cages').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_Cage() {
        //  $('#fuelTypeID').val('').change();
        $('#cage_name').val('');
        $('#AddCage_model').modal('show');
    }

    function Create_new_cage()
    {
        var farmID = $('#editfarmID').val();
        var cage_name = $('#cage_name').val();
        var cageID = $('#edit_cageID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmID' : farmID, 'cage_name': cage_name, 'cageID': cageID},
            url: "<?php echo site_url('Buyback/create_New_Cage'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#AddCage_model').modal('hide');
                    cage_master();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_Cage(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'cageID': id},
            url: "<?php echo site_url('Buyback/load_cage_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#editfarmID').val(data['farmID']);
                $('#cage_name').val(data['cageName']);
                $('#edit_cageID').val(data['cageID']);
                $('#AddCage_model').modal('show');
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function batch_master(cageID) {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID, cageID: cageID},
            url: "<?php echo site_url('Buyback/load_farm_all_batches'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_batch').html(data);
             //   batchLossWinChart();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function batchLossWinChart(){
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_batchProfitLossChartData'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var values3 = [1,-1,0,-1];
                alert(data);

                $('.sparktri').sparkline(data, {
                    type: 'tristate',
                    barWidth: 4,
                    barSpacing: 1,
                    fillColor: '',
                    lineColor: '999',
                    tooltipSuffix: 'Celsius',
                    width: 100,
                    barColor: '999',
                    posBarColor: '#228B22',
                    negBarColor: '#FF0000',
                    zeroBarColor: '000',
                    stackedBarColor: ['ff0', '9f0', '999', 'f60'],
                    sliceColors: ['ff0', '9f0', '000', 'f60'],
                    offset: '30',
                    borderWidth: 1,
                    borderColor: '000'
                });

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
            profileImageUploadContact();
        }
    }

    function profileImageUploadContact() {
        var imgageVal = new FormData();
        imgageVal.append('farmID', $('#editfarmID').val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#farm_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Buyback/farm_image_upload'); ?>",
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

    function edit_farmBatch(batchMasterID) {
        $('#frm_farm_add_batch')[0].reset();
        $('#frm_farm_add_batch').bootstrapValidator('resetForm', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchMasterID': batchMasterID},
            url: "<?php echo site_url('Buyback/load_farm_batch_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if (!jQuery.isEmptyObject(data['dnbatchid'])) {

                        $('#edit_batchMasterID').val(batchMasterID);
                        $('#cageID').val(data['cageID']);
                        $('#batchStartDate').val(data['batchStartDate']);
                        $('#batchClosingDate').val(data['batchClosingDate']);
                        $('#descrip').val(data['description']);
                        if (data['isclosed'] == 1) {
                            $('#isClosed').iCheck('check');
                        }
                        $('#WIPGLAutoID').val(data['WIPGLAutoID']).change();
                        $('#DirectWagesGLAutoID').val(data['DirectWagesGLAutoID']).change();
                        $('#show_all_batch').addClass('hide');
                        $('#show_add_batch_button').addClass('hide');
                        $('#show_add_batch').removeClass('hide');
                        if (data['confirmedYNbatch'] == 1) {
                            $('#WIPGLAutoID option:not(:selected)').prop('disabled', true);
                            $('#DirectWagesGLAutoID option:not(:selected)').prop('disabled', true);
                            $("#batchStartDate").attr("disabled", "disabled");
                            $("#batchClosingDate").attr("disabled", "disabled");
                        }
                    }
                    else {
                        $('#edit_batchMasterID').val(batchMasterID);
                        $('#cageID').val(data['cageID']);
                        $('#batchStartDate').val(data['batchStartDate']);
                        $('#batchClosingDate').val(data['batchClosingDate']);
                        $('#descrip').val(data['description']);
                        if (data['isclosed'] == 1) {
                            $('#isClosed').iCheck('check');
                        }
                        $('#WIPGLAutoID').val(data['WIPGLAutoID']).change();
                        $('#DirectWagesGLAutoID').val(data['DirectWagesGLAutoID']).change();
                        $('#show_all_batch').addClass('hide');
                        $('#show_add_batch_button').addClass('hide');
                        $('#show_add_batch').removeClass('hide');

                    }


                    calculateBatch_Date();
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }


    function generateProductionReport(batchMasterID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {batchMasterID: batchMasterID},
            url: '<?php echo site_url('Buyback/buyback_production_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#productionReportDrilldown_batch").html(data);
                $('#buyback_production_report_modal_farm_batch').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function party_master() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_party'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_party').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_party() {
        $('#show_all_party').addClass('hide');
        $('#show_add_party_button').addClass('hide');
        $('#show_add_party').removeClass('hide');
        $("#supplierPrimaryCode").val(null).trigger("change");
        $("#customerID").val(null).trigger("change");
        $('#frm_farm_add_party')[0].reset();
        $('#frm_farm_add_party').bootstrapValidator('resetForm', true);
    }

    function close_add_party() {
        $('#edit_partyMasterID').val('');
        $('#show_add_party').addClass('hide');
        $('#show_all_party').removeClass('hide');
        $('#show_add_party_button').removeClass('hide');
    }

    function ChangeParty(type) {
        if (type == 1) {
            $('#div_customer_type').addClass('hide');
            $('#div_supplier_type').removeClass('hide');
        } else {
            $('#div_supplier_type').addClass('hide');
            $('#div_customer_type').removeClass('hide');
        }
    }

    function edit_farmParty(farmPartyAutoID) {
        $('#isActive').iCheck('uncheck');
        $('#frm_farm_add_party')[0].reset();
        $('#frm_farm_add_party').bootstrapValidator('resetForm', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmPartyAutoID': farmPartyAutoID},
            url: "<?php echo site_url('Buyback/load_farm_party_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#edit_farmPartyAutoID').val(farmPartyAutoID);
                    $('#partyType').val(data['partyType']);
                    ChangeParty(data['partyType']);
                    $('#batchClosingDate').val(data['batchClosingDate']);
                    if (data['isActive'] == 1) {
                        $('#isActive').iCheck('check');
                    }
                    if (data['partyType'] == 1) {
                        $('#supplierPrimaryCode').val(data['partyAutoID']).change();
                    } else {
                        $('#customerID').val(data['partyAutoID']).change();
                    }
                    $('#show_all_party').addClass('hide');
                    $('#show_add_party_button').addClass('hide');
                    $('#show_add_party').removeClass('hide');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function dealers_master() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_dealers'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_dealers').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fieldOfficers_master() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_fieldOfficer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_fieldOfficers').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_dealers() {
        $('#dealers_isActive').iCheck('uncheck');
        $('#edit_dealersMasterID').val('');
        $('#edit_farmDealerID').val('');
        $('#show_all_dealers').addClass('hide');
        $('#show_add_dealers_button').addClass('hide');
        $('#show_add_dealers').removeClass('hide');
        $('#dealers_customerID').val(null).trigger("change");
        $('#frm_farm_add_dealers')[0].reset();
        $('#frm_farm_add_dealers').bootstrapValidator('resetForm', true);
    }

    function close_add_dealers() {
        $('#edit_dealersMasterID').val('');
        $('#show_add_dealers').addClass('hide');
        $('#show_all_dealers').removeClass('hide');
        $('#show_add_dealers_button').removeClass('hide');
    }

    function edit_farmDealers(farmDealerID) {
        $('#dealers_isActive').iCheck('uncheck');
        $('#frm_farm_add_dealers')[0].reset();
        $('#frm_farm_add_dealers').bootstrapValidator('resetForm', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmDealerID': farmDealerID},
            url: "<?php echo site_url('Buyback/load_farm_dealers_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#edit_farmDealerID').val(farmDealerID);
                    $('#dealers_customerID').val(data['customerAutoID']).change();
                    if (data['isActive'] == 1) {
                        $('#dealers_isActive').iCheck('check');
                    }
                    $('#show_all_dealers').addClass('hide');
                    $('#show_add_dealers_button').addClass('hide');
                    $('#show_add_dealers').removeClass('hide');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function show_add_fieldOfficers() {
        $('#fieldOfficers_isActive').iCheck('uncheck');
        $('#edit_fieldOfficerID').val('');
        $('#show_all_fieldOfficers').addClass('hide');
        $('#show_add_fieldOfficers_button').addClass('hide');
        $('#show_add_fieldOfficers').removeClass('hide');
        $('#fieldOfficers_employeeID').val(null).trigger("change");
        $('#frm_farm_add_fieldOfficers')[0].reset();
        $('#frm_farm_add_fieldOfficers').bootstrapValidator('resetForm', true);
    }

    function close_add_fieldOfficers() {
        $('#edit_fieldOfficerID').val('');
        $('#show_add_fieldOfficers').addClass('hide');
        $('#show_all_fieldOfficers').removeClass('hide');
        $('#show_add_fieldOfficers_button').removeClass('hide');
    }

    function warehouse_master() {
        var farmID = $('#editfarmID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/load_farm_all_warehouse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_warehouse').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_add_warehouse() {
        $('#show_all_warehouse').addClass('hide');
        $('#show_add_warehouse_button').addClass('hide');
        $('#show_add_warehouse').removeClass('hide');
        $('#warehouse_customerID').val(null).trigger("change");
        $('#frm_farm_add_warehouse')[0].reset();
        $('#frm_farm_add_warehouse').bootstrapValidator('resetForm', true);
    }

    function close_add_warehouse() {
        $('#edit_warehouseMasterID').val('');
        $('#show_add_warehouse').addClass('hide');
        $('#show_all_warehouse').removeClass('hide');
        $('#show_add_warehouse_button').removeClass('hide');
    }

    function edit_farmWarehouse(farmWarehouseID) {
        $('#warehouse_isActive').iCheck('uncheck');
        $('#frm_farm_add_warehouse')[0].reset();
        $('#frm_farm_add_warehouse').bootstrapValidator('resetForm', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmWarehouseID': farmWarehouseID},
            url: "<?php echo site_url('Buyback/load_farm_warehouse_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#edit_farmWarehouseID').val(farmWarehouseID);
                    $('#warehouseMasterID').val(data['warehouseMasterID']).change();
                    if (data['isActive'] == 1) {
                        $('#warehouse_isActive').iCheck('check');
                    }
                    $('#show_all_warehouse').addClass('hide');
                    $('#show_add_warehouse_button').addClass('hide');
                    $('#show_add_warehouse').removeClass('hide');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function edit_fieldOfficer(fieldOfficerID) {
        $('#fieldOfficers_isActive').iCheck('uncheck');
        $('#frm_farm_add_fieldOfficers')[0].reset();
        $('#frm_farm_add_fieldOfficers').bootstrapValidator('resetForm', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'fieldOfficerID': fieldOfficerID},
            url: "<?php echo site_url('Buyback/load_farm_fieldOfficer_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#edit_fieldOfficerID').val(fieldOfficerID);
                    $('#fieldOfficers_employeeID').val(data['empID']).change();
                    if (data['isActive'] == 1) {
                        $('#fieldOfficers_isActive').iCheck('check');
                    }
                    $('#show_all_fieldOfficers').addClass('hide');
                    $('#show_add_fieldOfficers_button').addClass('hide');
                    $('#show_add_fieldOfficers').removeClass('hide');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function delete_farmDealers(farmDealerID) {
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
                    data: {farmDealerID: farmDealerID},
                    url: "<?php echo site_url('Buyback/delete_farm_Dealers'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            dealers_master();
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

    function delete_farmBatch(batchMasterID) {
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
                    data: {batchMasterID: batchMasterID},
                    url: "<?php echo site_url('Buyback/delete_batch_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                          //  batch_master();
                            $('#show_all_Cages').removeClass('hide');
                            $('#show_add_cage_button').removeClass('hide');
                            $('#show_all_batch').addClass('hide');
                            $('#close_all_batch').addClass('hide');
                            $('#show_add_batch_button').removeClass('hide');
                            $('#edit_batchMasterID').val('');
                            $('#cageID').val('');
                            $('#show_add_batch').addClass('hide');
                            cage_master();
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

    function delete_fieldOfficer(fieldOfficerID) {
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
                    data: {'fieldOfficerID': fieldOfficerID},
                    url: "<?php echo site_url('Buyback/delete_farm_fieldOfficer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            fieldOfficers_master();
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

    function calculateBatch_Date() {
        var startDate = moment($("#batchStartDate").val(), "DD.MM.YYYY");
        var endDate = moment($("#batchClosingDate").val(), "DD.MM.YYYY");
        var days = endDate.diff(startDate, 'days');
        var formattedDate = days + 1;
        $('#daysShowing_div').html(formattedDate);
    }

    function feedScheduleReport_view(batchMasterID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {batchMasterID: batchMasterID},
            url: '<?php echo site_url('Buyback/load_feedSchedule_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent").html(data);
                $('#finance_report_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function deletenotesfarm(notesID) {
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
                    url: "<?php echo site_url('Buyback/delete_buyback_notes'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Note Deleted Successfully');
                            farm_notes();
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

    function batchoverview() {
        var farmID = $('#farmID').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Buyback/load_batch_overview'); ?>",
            data:{'farmID':farmID},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $("#show_batch_overview").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }


</script>


