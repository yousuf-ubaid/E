<?php echo head_page($_POST['page_name'], false);
$this->load->helper('fleet_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$maintenancecompany = all_maintenancecompany_drop();
$maintenancetype = load_all_maintenacetype();
$maintenancecriteria = load_all_maintenacecriteria();
$loadallcrewtype = load_all_crew();
$currency_arr = all_currency_new_drop();
$crew_arr = all_employee_drop();
$supplier_drop = all_supplier_group_drop();
$gl_drop = dropdown_all_revenue_gl_JV();
$segment = fetch_segment();
$umo_arr = array('' => 'Select UOM');
$location_arr = all_delivery_location_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .titlebalance {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 12px;
        color: #151212;
        font-weight: bold;
        padding: 4px 10px 0 0;
    }

    .totalbal {
        float: left;
        width: 170px;
        text-align: left;
        font-size: 12px;
        color: #f76f01;
        font-weight: bold;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
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
<?php echo form_open('', 'role="form" id="journeyplanheader_form"'); ?>
<input type="hidden" name="vehicalemasterid" id="vehicalemasterid">
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <!--<header class="head-title">
            <h2>Vehicle Maintenance</h2>
        </header>-->
        <div class="row">
            <div class="col-md-4" style="padding-right: 0;">
                <div class="col-sm-12">
                    <!--  <div class="fileinput-new thumbnail" style="width: 180px; height: 150px;text-align: center;">
                        <img src="<?php /*echo base_url('images/item/no-image.png'); */ ?>" id="changeImg"
                             class="img-responsive" style="width: 200px; height: 140px;">
                        <input type="file" name="itemImage" id="itemImage" style="display: none;">

                    </div>-->
                    <table class="table" id="profileInfoTable"
                           style="background-color: #ffffff;border-width:thin;height:40px;border: 1px solid #ddd;border-top: 0;">
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <div style="padding-left: 50px;">
                                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                         class="img-responsive" style="width: 200px; height: 140px;">
                                    <input type="file" name="itemImage" id="itemImage" style="display: none;">

                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong> Brand</strong>
                            </td>
                            <td id="brand" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong> Model</strong>
                            </td>
                            <td id="model" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong> Year</strong>
                            </td>
                            <td id="year" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Body Type</strong>
                            </td>
                            <td id="bodytype" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Capacity</strong>
                            </td>
                            <td id="enginecapacity" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Colour</strong>
                            </td>
                            <td id="colour" style="width: 70%;">.......</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Fuel Type</strong>
                            </td>
                            <td id="fueltype" style="width: 70%;">.......</td>
                        </tr>

                        <tr>
                            <td>
                                <strong>Transmission</strong>
                            </td>
                            <td id="transmission" style="width: 70%;">.......</td>
                        </tr>
                       <tr>
                             <td>
                                 <strong>Initial Usage</strong>
                             </td>
                             <td id="initialmilage" style="width: 70%;">.......</td>
                         </tr>

                          <tr>
                             <td>
                                 <strong>Current Usage</strong>
                             </td>
                             <td id="currentmilage" style="width: 70%;">.......</td>
                         </tr>

                         <tr>
                             <td>
                                 <strong>Chessi/Part No</strong>
                             </td>
                             <td id="chessino" style="width: 70%;">.......</td>
                         </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-8">
                <header class="head-title">
                    <h2 id="titlecarname">...</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="vehiclemaintenace_model()">
                            <i class="fa fa-plus"></i> Create Maintenance
                        </button>
                    </div>
                </div>
                <br>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="vehicale_maintenace_view"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>
</div>
</form>

<div aria-hidden="true" role="dialog" id="vehicale_maintenance" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="titlecarnames">Create Maintenance</h4>
            </div>
            <div class="modal-body">

                <ul class="nav nav-tabs" id="main-tabs">
                    <li class="active"><a href="#maintenance" data-toggle="tab"><i class="fa fa-television"></i>Maintenance</a>
                    </li>
                    <li class="attachmentview hide"><a href="#attachments" onclick="attachments_view();" data-toggle="tab"><i class="fa fa-television"></i>Attachments</a></li>
                </ul>
                <br>
                <div class="tab-content">
                    <div class="tab-pane active" id="maintenance">
                        <form role="form" id="vehicale_master_form" class="form-horizontal">

                            <header class="head-title">
                                <h2>Maintenance HEADER</h2>
                            </header>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">
                                        <div class="form-group col-sm-2">
                                            <label class="title">Maintenance Code</label>
                                        </div>
                                        <div class="form-group col-sm-4">

                                            <input type="text" name="documentsystemcode" id="documentsystemcode"
                                                   class="form-control" readonly>
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title">Document Date</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="documentdate"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                       value="<?php echo $current_date; ?>" id="documentdate"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">

                                        <div class="form-group col-sm-2">
                                            <label class="title">Maintenance Type</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                       <?php echo form_dropdown('maintenancetype', $maintenancetype, '', 'class="form-control maintenancetype select2" id="maintenancetype"'); ?>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>

                                        <div class="form-group col-sm-2">
                                            <label class="title">Currency</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                       <?php echo form_dropdown('transactioncurrencyid', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control transactioncurrencyid select2" id="transactioncurrencyid" disabled'); ?>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>
                                    </div>


                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">
                                        <div class="form-group col-sm-2">
                                            <label class="title">Maintenance Date From</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                       <span class="input-req" title="Required Field">
                             <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="maintenancedatefrom"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="maintenancedatefrom" class="form-control">
                        </div>
                           <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>
                                        <div class="form-group col-sm-2">
                                            <label class="title">Maintenance Date To</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                     <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="maintenancedateto"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="maintenancedateto" class="form-control">
                        </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">
                                        <div class="form-group col-sm-2">
                                            <label class="title"><!--Current Meter Reading-->Current Usage</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field">
                            <div class="input-group">
                                 <div class="input-group-addon">KM/hrs</div>
                                 <input type="text" class="form-control number" id="currentmeterreading" name="currentmeterreading">
                                </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>

                                        </div>

                                        <div class="form-group col-sm-2">
                                            <label class="title">Maintained By</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                         <?php echo form_dropdown('maintenancedoneby', array(''=>'Select Maintenance By','1'=>'In House','2'=>'Third Party'), '', 'class="form-control maintenancedoneby select2" id="maintenancedoneby"'); ?>
                                <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>


                                    </div>


                                    <div class="row thirdpartydrop hide" style="margin-top: 10px;margin-left: 25px;">
                                        <div class="thirdpartydrop hide">
                                            <div class="form-group col-sm-2">
                                                <label class="title">Maintenance Company</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                       <span class="input-req" title="Required Field">
                              <?php echo form_dropdown('maintenancecompany', $maintenancecompany, '', 'class="form-control maintenancecompany  select2" id="maintenancecompany"'); ?>
                           <input type="hidden" name="vehicalemaintenaceid" id="vehicalemaintenaceid">
                       <span class="input-req-inner" style="z-index: 100"></span></span>
                                            </div>

                                            <div class="form-group col-sm-2">
                                                <label class="title">Supplier  - DocRef No</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                                                <input type="text" name="supplierreferenceno" id="supplierreferenceno" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">


                                        <div class="form-group col-sm-2 thirdpartydrop hide">
                                            <label class="title">Segment</label>
                                        </div>
                                        <div class="form-group col-sm-4 thirdpartydrop hide">
                                    <span class="input-req" title="Required Field">
                              <?php echo form_dropdown('segment',$segment, '', 'class="form-control segment select2" id="segment"'); ?>
                                        <span class="input-req-inner" style="z-index: 100"></span></span>

                                        </div>



                                        <div class="form-group col-sm-2 thirdpartydrop hide">
                                            <label class="title">Gl Code </label>
                                        </div>
                                        <div class="form-group col-sm-4 thirdpartydrop hide">
                                    <span class="input-req" title="Required Field">
                              <?php echo form_dropdown('glcode',$gl_drop, '', 'class="form-control glcode select2" id="glcode"'); ?>
                                        <span class="input-req-inner" style="z-index: 100"></span></span>
                                        </div>




                                    </div>


                                    <div class="row" style="margin-top: 10px;margin-left: 25px;">

                                        <div class="form-group col-sm-2 warehouselocation hide">
                                            <label class="title">Ware House</label>
                                        </div>
                                        <div class="form-group col-sm-4 warehouselocation hide">
                                            <?php echo form_dropdown('warehouse',$location_arr, '', 'class="form-control warehouse select2" id="warehouse"'); ?>
                                        </div>

                                        <div class="form-group col-sm-2">
                                            <label class="title">Comments</label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <textarea class="form-control" rows="3" id="commentvehicalmainte"
                                                      name="commentvehicalmainte"></textarea>
                                        </div>
                                    </div>


                                    <fieldset>
                                        <legend style="font-size: 16px;"><strong>Criteria</strong></legend>
                                        <div class="row" style="margin-top: 10px;margin-left: 25px;">
                                            <div class="form-group col-sm-2">
                                                <label class="title">Last Maintenance Date </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                       <span class="input-req" title="Required Field">
                             <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="lastmaintenancedate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="lastmaintenancedate" class="form-control"
                                   readonly>
                        </div>
                           <span class="input-req-inner" style="z-index: 100"></span></span>
                                            </div>

                                            <div class="form-group col-sm-2">
                                                <label class="title">Next Maintenance Date </label>
                                            </div>
                                            <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                     <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="nextmaintenancedate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="nextmaintenancedate" class="form-control">
                        </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                                            </div>
                                        </div>


                                        <div class="row" style="margin-top: 10px;margin-left: 25px;">
                                            <div class="form-group col-sm-2">
                                                <label class="title">Last Maintenance</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                            <div class="input-group">
                                 <div class="input-group-addon">KM/hrs</div>
                                 <input type="text" class="form-control " id="maintenancekm" name="maintenancekm"
                                        readonly>
                                </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>

                                            </div>
                                            <div class="form-group col-sm-2">
                                                <label class="title">Next Maintenance</label>
                                            </div>
                                            <div class="form-group col-sm-4">
                      <span class="input-req" title="Required Field">
                            <div class="input-group">
                                 <div class="input-group-addon">KM/hrs</div>
                                 <input type="text" class="form-control number" id="nextmaintenance" name="nextmaintenance">
                                </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>

                                            </div>
                                        </div>
                                    </fieldset>

                                    <br>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-12">
                                            <button class="btn btn-primary pull-right" type="button" id="save_btn"
                                                    onclick="save_mainte_header()">Save
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </form>
                        <div class="row addTableView">
                            <div class="col-md-12 animated zoomIn">
                                <header class="head-title">
                                    <h2>Maintenance Crew</h2>
                                </header>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-primary pull-right" id="maintenacecrewadd"
                                                onclick="add_maintenance_crew()">
                                            <i class="fa fa-plus"></i> Add Crew
                                        </button>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-11">
                                        <div id="crew_details_view"></div>
                                    </div>
                                    <div class="col-sm-1">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row addTableView">
                            <div class="col-md-12 animated zoomIn">
                                <header class="head-title">
                                    <h2>Maintenance Detail</h2>
                                </header>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-primary pull-right"
                                                id="addmaintenancedetail"
                                                onclick="add_maintenance_detail()">
                                            <i class="fa fa-plus"></i> Add Maintenance Detail
                                        </button>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-11">
                                        <div id="add_maintenance_details_view"></div>
                                    </div>
                                    <div class="col-sm-1">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="tab-pane" id="attachments">

                        <div id="attachments_view">

                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>

</div>

<div aria-hidden="true" role="dialog" id="maintenace_crew" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 50%;z-index: 1000000000;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">MAINTENANCE CREW</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="vehiclemaintenace_crew_add_form" class="form-horizontal">
                    <input type="hidden" class="form-control" name="maintananceMasterIDcrew"
                           id="maintananceMasterIDcrew">
                    <table class="table table-bordered table-condensed no-color"
                           id="detail_vehiclemaintenace_crew_add_table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_maintenace_crew()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body_maintenance_crew">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control crewname"
                                           name="crewname[]" id="crewname_1">
                                    <input type="hidden" name="selectedemployee[]" id="selectedemployee_1"
                                           class="form-control selectedemployee">
                                    <span class="input-group-btn">
                                                    <button class="btn btn-default btn_class_clear" type="button"
                                                            title="Clear" rel="tooltip"
                                                            onclick="clearEmployee(this)" data-id="1"
                                                            style="height: 29px; padding: 2px 10px;"><i
                                                                class="fa fa-repeat"></i></button>
                                                 <button class="btn btn-default btn_class_more" type="button"
                                                         title="Add Crew" rel="tooltip"
                                                         onclick="link_employee_model(this)" data-id="1"
                                                         style="height: 29px; padding: 2px 10px;"><i
                                                             class="fa fa-plus"></i></button>
                    </span>
                                </div>
                            </td>
                            <td>
                                <?php echo form_dropdown('crewtype[]', $loadallcrewtype, '', 'class="form-control select2 crewtype" name ="crewtype[]" '); ?></td>
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_details_maintenace_crew()">Save
                    changes
                </button>
            </div>

        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="maintenace_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 70%;z-index: 1000000000;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">MAINTENANCE DETAIL</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="vehiclemaintenace_add_form" class="form-horizontal">
                    <input type="hidden" class="form-control" name="maintananceMasterIDdetail"
                           id="maintananceMasterIDdetail">
                    <table class="table table-bordered table-condensed no-color"
                           id="detail_vehiclemaintenace_add_table">
                        <thead>
                        <tr>
                            <th>Maintenance Criteria<?php required_mark(); ?></th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                            <th>Crew Member</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_maintenace()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr>
                            <td>
                                <?php echo form_dropdown('maintenancecriteria[]', $maintenancecriteria, '', 'class="form-control select2" name ="maintenancecriteria[]" '); ?>

                            </td>
                            <td>
                                <input type="text" class="form-control Qty number " name="Qty[]"
                                       onkeyup="calculatetotalamount(this)">
                            </td>
                            <td>
                                <input type="text" class="form-control unitcost number" name="unitcost[]" value="0"
                                       onkeyup="calculatetotalamount(this)">
                            </td>

                            <td>
                                <input type="text" class="form-control totalamt" name="total[]" readonly>
                            </td>
                            <td>
                                <div id="div_crew_member">
                                    <select name="crewmember[]" class="form-control select2" id="crewmember">
                                        <option value="" selected="selected">Select a Crew Member</option>
                                    </select>
                                </div>

                            </td>
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_details_maintenace_details()">Save
                    changes
                </button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="z-index:1000000000">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Link Crew</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Crew Memeber Name</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $crew_arr, '', 'class="form-control select2" id="employee_id"  required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()">Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="vehicale_maintenace_details_frm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Maintenance</h4>
            </div>
            <?php echo form_open('', 'role="form" id="usergroup_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="userGroupID" name="userGroupID">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Maintenace Type</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="maintenacetype" name="maintenacetype" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Maintenace Done By</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="maintenacedoneby" name="maintenacedoneby" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Maintenace Done</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">Yes&nbsp;&nbsp;</label>
                                    <input id="maintenacedoneyes" type="radio" data-caption="" class="columnSelected"
                                           name="maintenacedone" value="1">
                                </div>
                            </label>
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">No&nbsp;</label>
                                    <input id="maintenacedoneno" type="radio" data-caption="" class="columnSelected"
                                           name="maintenacedone" value="0">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>



                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comments</label>
                    </div>
                    <div class="form-group col-sm-6">
                         <textarea class="form-control" rows="3" id="commentsmaintenacedetails"
                                   name="commentsmaintenacedetails"></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                           aria-hidden="true"></span> Save
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="ap_closed_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_closed_user_label">Maintenance Status</h4>
            </div>
            <?php echo form_open('', 'role="form" id="maintenace_status_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="maintenacemasterid" name="maintenacemasterid">
                <input type="hidden" id="vehicalemasteridstatus" name="vehicalemasteridstatus">
                <input type="hidden" id="maintenancedonebystatus" name="maintenancedonebystatus">

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <?php echo form_dropdown('statusmaintenace', array('' => 'Select Status', '1' => 'Not Started', '2' => 'On going', '3' => 'Closed'), '', 'class="form-control statusmaintenace select2" id="statusmaintenace"'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide datestatuschange" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                <span class="input-req" title="Required Field">
                     <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="stausdate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="stausdate" class="form-control">
                        </div>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row hide datestatuschange" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                        <textarea class="form-control" rows="3" id="commentstauts"
                                  name="commentstauts"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="save_status_maintenace()" class="btn btn-sm btn-primary" id="save_btn_status"><span
                            class="glyphicon glyphicon-floppy-disk"
                            aria-hidden="true"></span> Save
                </button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
     id="maintenace_spare_parts">
    <div class="modal-dialog modal-lg" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Spare Parts </h4>

                <form role="form" id="maintenace_Criteria_form" class="form-horizontal">
                    <input type="hidden" name="maintenanceCriteriaID" id="maintenanceCriteriaID">
                    <input type="hidden" name="maintenacemasteridadd" id="maintenacemasteridadd">
                    <input type="hidden" name="maintenacemasterdetailid" id="maintenacemasterdetailid">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed" id="addspareparts_add_table">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>UOM</th>
                                <th>Current Stock</th>
                                <th>Qty</th>
                                <th>Cost</th>
                                <th>Total</th>
                                <th>Description</th>
                                <th>Added Y/N</th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_spare_parts()" id="add_spare_parts_main"><i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="maintenace_table">
                            <!--<tr>
                                <td>
                                    <input type="text" class="form-control search input-mini f_search" name="search[]"
                                           id="f_search_1"
                                           placeholder="Item Code, Item Description"
                                           onkeydown="remove_item_all_description(event,this)"><input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                </td>
                                <td><?php /*echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" disabled  required'); */?></td>

                                <td>
                                    <input type="text" onfocus="this.select();" name="unitcost[]"  placeholder="0.00" class="form-control number input-mini unitcost" required="" autocomplete="off">
                                </td>

                                <td>
                                    <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off">
                                </td>
                                <td>
                                    <textarea class="form-control" rows="1" name="description[]"></textarea>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>-->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default"
                                type="button"> Close</button>
                        <button class="btn btn-primary" type="button" id="save_btn_changes"
                                onclick="save_spareparts_added();">Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var glArray = JSON.stringify(<?php echo json_encode($loadallcrewtype) ?>);
    var row = JSON.parse(glArray);

    var vehicalemasterid;
    var crewmember;
    var vehicalemaintenaceid;
    var crewname = 1;
    var search_id = 1;
    var tempTextFieldID;
    var clearid;
    var currency_decimal;
    var dr = <?php echo json_encode(form_dropdown('crewtype[]', $loadallcrewtype, '', 'class="form-control select2" name ="crewtype[]"'));?>;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_maintenance', '', 'Fleet Maintenance')


            $('.modal').on('hidden.bs.modal', function () {
                setTimeout(function () {
                    if ($('.modal').hasClass('in')) {
                        $('body').addClass('modal-open');
                    }
                }, 500);
            });

        });

        $('.requiredCheckbox').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
        });

    });
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
    p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    if (p_id) {
        vehicalemasterid = p_id;
        load_vehicale_master();
        get_crew_details();
        //   $('.addTableView').addClass('hide');
    }
    number_validation();
    $("#statusmaintenace").change(function () {

        if (this.value == 1) {
            $('.datestatuschange').addClass('hide');
        } else if (this.value == 2 || this.value == 3) {
            $('.datestatuschange').removeClass('hide')
        } else {
            $('.datestatuschange').addClass('hide')
        }
    });


    $("#maintenancedoneby").change(function () {

        if (this.value == 1) {
            $('.thirdpartydrop').addClass('hide')
            $('.warehouselocation').removeClass('hide')

        } else if (this.value == 2) {
            $('.thirdpartydrop').removeClass('hide')
            $('.warehouselocation').addClass('hide')
        } else {
            $('.thirdpartydrop').addClass('hide')
            $('.warehouselocation').addClass('hide')
        }
    });

    $('.modal').on('hidden.bs.modal', function () {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    });

    function loadmaintencaecode() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Fleet/fetch_maintenace_number'); ?>",
            success: function (data) {
                $("#documentsystemcode").val(data);
            }
        });
    }

    vehicalemasterid = null;
    vehicalemaintenaceid = null;
    tempTextFieldID = null;
    clearid = null;
    crewmember = null;
    $('.select2').select2();

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });

    function load_vehicale_master() {
        if (vehicalemasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Fleet/fetch_vehical_records'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {

                    if (!jQuery.isEmptyObject(data)) {
                        vehicalemasterid = data['vehicleMasterID'];
                        $('#vehiclemasterid').val(data['vehicleMasterID']);
                        $('#vehicalemasterid').val(data['vehicleMasterID']);
                        if (data['vehicleImage'] == ' ') {
                            $("#changeImg").attr("src", "<?php echo base_url('uploads/Fleet/no_image.jpg'); ?>");
                        } else {
                            $("#changeImg").attr("src",data['vehicaleIMG']);
                        }
                        $("#brand").html(data['brand_description']);
                        $("#titlecarname").html(data['titlevehicale']);
                        $("#titlecarnames").html(data['titlevehicalecreate']);
                        $("#model").html(data['model_description']);
                        $("#year").html(data['manufacturedYear']);
                        $("#bodytype").html(data['bodyType_description']);
                        $("#enginecapacity").html(data['engineCapacity']);
                        $("#colour").html(data['colour_description']);
                        $("#fueltype").html(data['fuel_type_description']);
                        $("#transmission").html(data['transmisson_description']);
                        $("#currentmilage").html(data['maximumpreviousreading']);
                        $("#initialmilage").html(data['initialMilage']);
                        $("#chessino").html(data['chessinovehicale']);


                        get_veicale_maitenance_detail_view();
                        get_crew_details();
                        get_maintenance_details();

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
    }

    function add_maintenance_crew() {


        $('#vehiclemaintenace_crew_add_form')[0].reset();
        $('#vehiclemaintenace_crew_add_form').bootstrapValidator('resetForm', true);
        $('#detail_vehiclemaintenace_crew_add_table tbody tr').not(':first').remove();
        $(".crewtype").val(null).trigger("change");
        $("#maintenace_crew").modal({backdrop: "static"});

    }

    function add_maintenance_detail() {
        var maintainedby = $('#maintenancedoneby').val();

        $('#vehiclemaintenace_add_form')[0].reset();
        $('#vehiclemaintenace_add_form').bootstrapValidator('resetForm', true);
        $('#detail_vehiclemaintenace_add_table tbody tr').not(':first').remove();
        crew_members();
        if(maintainedby == 1)
        {
            $('.Qty').prop('readonly',true)
            $('.unitcost').prop('readonly',true)
            $('.Qty').val('1');
            $('.unitcost').val('0');
            $('.unitcost').val('0');
            $('.totalamt').val('0');

        }else
        {
            $('.Qty').prop('readonly',false)
            $('.unitcost').prop('readonly',false)
        }

        $("#maintenace_detail_modal").modal({backdrop: "static"});
    }

    function get_veicale_maitenance_detail_view() {
        if (vehicalemasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Fleet/load_vehicale_maintenace_detail_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#vehicale_maintenace_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function vehiclemaintenace_model() {
        $('#main-tabs a:first').tab('show');
        $('#vehicale_master_form')[0].reset();
        $('#vehiclemaintenace_add_form')[0].reset();
        $('#vehiclemaintenace_crew_add_form')[0].reset();
        $('#vehicale_master_form').bootstrapValidator('resetForm', true);
        $('#vehiclemaintenace_add_form').bootstrapValidator('resetForm', true);
        $('#vehiclemaintenace_crew_add_form').bootstrapValidator('resetForm', true);
        $(".maintenancecompany").val(null).trigger("change");
        $(".maintenancedoneby").val(null).trigger("change");
        $(".warehouse").val(null).trigger("change");
        $(".segment").val(null).trigger("change");
        $(".glcode").val(null).trigger("change");
        $("#vehicalemaintenaceid").val('');
        $("#maintananceMasterIDcrew").val('');
        $("#maintananceMasterIDdetail").val('');
        $('#transactionCurrencyID').prop('disabled', true);
        $("#documentdate").prop('disabled', false);
        $("#maintenancecompany").prop('disabled', false);
        $("#maintenancetype").prop('disabled', false);
        $("#maintenancedatefrom").prop('disabled', false);
        $("#maintenancedateto").prop('disabled', false);
        $("#commentvehicalmainte").prop('disabled', false);
        $("#lastmaintenancedate").prop('disabled', false);
        $("#currentmeterreading").prop('disabled', false);
        $("#nextmaintenance").prop('disabled', false);
        $("#maintenancekm").prop('disabled', false);
        $("#maintenancedoneby").prop('disabled', false);
        $("#segment").prop('disabled', false);
        $('.thirdpartydrop').addClass('hide')
        $('.warehouselocation').addClass('hide')
        $("#glcode").prop('disabled', false);
        $("#supplierreferenceno,#warehouse").prop('disabled', false);
        $("#nextmaintenancedate").prop('disabled', false);
        $('#maintenacecrewadd').removeClass('hide');
        $('#addmaintenancedetail').removeClass('hide');
        $('.attachmentview').addClass('hide');
        $('#save_btn').removeClass('hide');
        nextmaintenacecriteria();
        nextmaintenacekm();
        loadmaintencaecode();
        $(".maintenancetype").val(null).trigger("change");
        $('.addTableView').addClass('hide');
        $('#save_btn').html('Save')
        $("#vehicale_maintenance").modal({backdrop: "static"});
    }


    function save_mainte_header() {
        $('#transactioncurrencyid').prop('disabled', false);
        $('#maintenancedoneby').prop('disabled', false);
        var data = $('#vehicale_master_form').serializeArray();
        data.push({'name': 'vehicalemasterid', 'value': vehicalemasterid});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_vehicalemaintenanceheader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    vehicalemaintenaceid = data[2];
                    $('#vehicalemaintenaceid').val(vehicalemaintenaceid);
                    $('#maintananceMasterIDcrew').val(vehicalemaintenaceid);
                    $('#maintananceMasterIDdetail').val(vehicalemaintenaceid);
                    $('.addTableView').removeClass('hide');
                    $('.attachmentview').removeClass('hide');
                    get_veicale_maitenance_detail_view();
                    get_crew_details();
                    $('#transactioncurrencyid').prop('disabled', true);
                    $('#maintenancedoneby').prop('disabled', true);
                    get_maintenance_details();
                    maintenance_detail_exist();
                    $('#save_btn').html('Update')
                } else {
                    $('#transactioncurrencyid').prop('disabled', true);
                    maintenance_detail_exist();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_more_maintenace() {
        $('select.select2').select2('destroy');
        var appendData = $('#detail_vehiclemaintenace_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        appendData.find('.Qty').val('1');
        appendData.find('.unitcost').val('0');
        appendData.find('.totalamt').val('0');

        $('#detail_vehiclemaintenace_add_table').append(appendData);

        var lenght = $('#detail_vehiclemaintenace_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    function add_more_maintenace_crew() {
        crewname += 1;

        $('select.select2').select2('destroy');
        var appendData = $('#detail_vehiclemaintenace_crew_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('.crewname').attr('id', 'crewname_' + crewname);
        appendData.find('.selectedemployee').attr('id', 'selectedemployee_' + crewname);
        appendData.find('.crewname').val('');
        appendData.find('.btn_class_more').attr('data-id', crewname);
        appendData.find('.btn_class_clear').attr('data-id', crewname);
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#detail_vehiclemaintenace_crew_add_table').append(appendData);
        $('#crewname_' + crewname).prop('readonly', false);
        $('#crewname_' + crewname).val('').trigger('input');
        $('#selectedemployee_' + crewname).val('').trigger('input');
        var lenght = $('#detail_vehiclemaintenace_crew_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    function save_details_maintenace_crew() {
        var data = $("#vehiclemaintenace_crew_add_form").serializeArray();
        $('#currency').prop('disabled', false);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_maintenance_crew_det'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    get_crew_details();
                    get_maintenance_details();
                    $('#maintenace_crew').modal('hide');
                    $('#currency').prop('disabled', true);
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

    function link_employee_model(obj) {
        tempTextFieldID = $(obj).attr('data-id');
        $('#employee_id').val('').change();
        $('#emp_model').modal('show');
    }

    function clearEmployee(obj) {
        clearid = $(obj).attr('data-id');
        $('#employee_id').val('').change();
        $('#crewname_' + clearid).val('').trigger('input');
        $('#selectedemployee_' + clearid).val('').trigger('input');
        $('#crewname_' + clearid).prop('readonly', false);
        EIdNo = null;
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id == '') {
            myAlert('e', 'Select A Crew Member');
        } else {
            EIdNo = employee_id;
            var empName = $("#employee_id option:selected").text();
            $('#crewname_' + tempTextFieldID).val($.trim(empName)).trigger('input');
            $('#selectedemployee_' + tempTextFieldID).val($.trim(employee_id)).trigger('input');
            $('#crewname_' + tempTextFieldID).prop('readonly', true);
            $('#emp_model').modal('hide');
        }
    }

    function load_vehicale_maintenace_edit(maintenanceMasterID, type) {

        $(".maintenancecompany").val(null).trigger("change");
        $("#vehicalemaintenaceid").val('');
        $("#maintenancekm").val('');
        $("#maintananceMasterIDcrew").val('');
        $("#maintananceMasterIDdetail").val('');
        $("#maintenancedateto").val('');
        $("#nextmaintenancedate").val('');
        $("#maintenancedatefrom").val('');
        $("#nextmaintenance").val('');
        $("#commentvehicalmainte").val('');
        $(".maintenancetype").val(null).trigger("change");

        if (type == 1) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'maintenanceMasterID': maintenanceMasterID},
                url: "<?php echo site_url('Fleet/fetch_vehicalemaintenace_header_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $("#documentdate").prop('disabled', true);
                    $("#maintenancecompany").prop('disabled', true);
                    $("#maintenancetype").prop('disabled', true);
                    $("#maintenancedatefrom").prop('disabled', true);
                    $("#maintenancedateto").prop('disabled', true);
                    $("#commentvehicalmainte").prop('disabled', true);
                    $("#lastmaintenancedate").prop('disabled', true);
                    $("#nextmaintenance").prop('disabled', true);
                    $("#segment").prop('disabled', true);
                    $("#maintenancekm").prop('disabled', true);
                    $("#nextmaintenancedate").prop('disabled', true);
                    $("#currentmeterreading").prop('disabled', true);
                    $("#maintenancedoneby").prop('disabled', true);
                    $("#glcode,#warehouse,#supplierreferenceno").prop('disabled', true);


                    $("#maintenancetype").val(data['maintenanceType']).change();
                    vehicalemaintenaceid = data['maintenanceMasterID'];
                    $("#maintananceMasterIDcrew").val(data['maintenanceMasterID']);
                    $("#maintananceMasterIDdetail").val(data['maintenanceMasterID']);
                    $("#vehicalemaintenaceid").val(data['maintenanceMasterID']);
                    $("#maintenancedatefrom").val(data['maintenanceDateFromcon']);
                    $("#documentdate").val(data['documentDatecon']);
                    $("#currentmeterreading").val(data['currentMeterReadingmastertbl']);
                    $("#maintenancedateto").val(data['maintenanceDateTocon']);
                    $("#nextmaintenancedate").val(data['nextMaintenanceDatecon']);
                    $("#maintenancekm").val(data['lastMaintenanceOnKM']);
                    $("#nextmaintenance").val(data['nextMaintenanceONKM']);
                    $("#commentvehicalmainte").val(data['comment']);
                    $("#lastmaintenancedate").val(data['lastMaintenanceDatecon']);
                    $("#documentsystemcode").val(data['maintenanceCode']);
                    $("#transactioncurrencyid").val(data['transactionCurrencyID']).change();
                    $("#maintenancedoneby").val(data['maintenanceBy']).change();

                    if(data['maintenanceBy']==2)
                    {
                        $('.thirdpartydrop').removeClass('hide')
                        $("#maintenancecompany").val(data['maintenanceCompanyID']).change();
                        $("#glcode").val(data['expenseGLAutoID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#supplierreferenceno').val(data['supplierDocRefNo']);

                    }else if(data['maintenanceBy']== 1) {
                        $('.warehouselocation').removeClass('hide');
                        $('.thirdpartydrop').addClass('hide');
                        $("#warehouse").val(data['warehouseAutoID']).change();

                    } else
                    {
                        $('.thirdpartydrop').addClass('hide')
                        $('.warehouselocation').addClass('hide');
                    }
                    $("#vehicale_maintenance").modal({backdrop: "static"});
                    $('.addTableView').removeClass('hide');
                    $('.attachmentview').removeClass('hide');
                    $('#maintenacecrewadd').addClass('hide');
                    $('#addmaintenancedetail').addClass('hide');
                    get_crew_details_view();
                    attachments_view();
                    get_maintenance_details_view();
                    maintenance_detail_exist();
                    $('#save_btn').addClass('hide');
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        } else {
            if (maintenanceMasterID) {
                swal({
                        title: "Are you sure?",
                        text: "You want to edit this record!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Edit"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'maintenanceMasterID': maintenanceMasterID},
                            url: "<?php echo site_url('Fleet/fetch_vehicalemaintenace_header_details'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                $("#documentdate,#warehouse").prop('disabled', false);
                                $("#maintenancecompany").prop('disabled', false);
                                $("#maintenancetype").prop('disabled', false);
                                $("#maintenancedatefrom").prop('disabled', false);
                                $("#glcode").prop('disabled', false);
                                $("#segment").prop('disabled', false);
                                $("#maintenancedateto").prop('disabled', false);
                                $("#commentvehicalmainte").prop('disabled', false);
                                $("#maintenancedoneby").prop('disabled', false);
                                $("#lastmaintenancedate").prop('disabled', false);
                                $("#nextmaintenance").prop('disabled', false);
                                $("#maintenancekm").prop('disabled', false);
                                $("#nextmaintenancedate").prop('disabled', false);
                                $("#currentmeterreading,#supplierreferenceno").prop('disabled', false);
                                $("#maintenancecompany").val(data['maintenanceCompanyID']).change();
                                $("#maintenancetype").val(data['maintenanceType']).change();
                                vehicalemaintenaceid = data['maintenanceMasterID'];
                                $("#maintananceMasterIDcrew").val(data['maintenanceMasterID']);
                                $("#maintananceMasterIDdetail").val(data['maintenanceMasterID']);
                                $("#vehicalemaintenaceid").val(data['maintenanceMasterID']);
                                $("#maintenancedatefrom").val(data['maintenanceDateFromcon']);
                                $("#documentdate").val(data['documentDatecon']);
                                $("#maintenancedateto").val(data['maintenanceDateTocon']);
                                $("#nextmaintenancedate").val(data['nextMaintenanceDatecon']);
                                $("#maintenancekm").val(data['lastMaintenanceOnKM']);
                                $("#nextmaintenance").val(data['nextMaintenanceONKM']);
                                $("#commentvehicalmainte").val(data['comment']);
                                $("#lastmaintenancedate").val(data['lastMaintenanceDatecon']);
                                $("#documentsystemcode").val(data['maintenanceCode']);
                                $("#currentmeterreading").val(data['currentMeterReadingmastertbl']);
                                $("#transactioncurrencyid").val(data['transactionCurrencyID']).change();
                                $("#vehicale_maintenance").modal({backdrop: "static"});
                                $('.addTableView').removeClass('hide');
                                $("#maintenancedoneby").val(data['maintenanceBy']).change();

                                if(data['maintenanceBy']==2)
                                {
                                    $('.thirdpartydrop').removeClass('hide')
                                    $("#maintenancecompany").val(data['maintenanceCompanyID']).change();
                                    $("#glcode").val(data['expenseGLAutoID']).change();
                                    $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                                    $('#supplierreferenceno').val(data['supplierDocRefNo']);

                                }else if(data['maintenanceBy']==1) {
                                    $('.warehouselocation').removeClass('hide');
                                    $('.thirdpartydrop').addClass('hide');
                                    $("#warehouse").val(data['warehouseAutoID']).change();

                                }
                                else
                                {
                                    $('.thirdpartydrop').addClass('hide')
                                    $('.warehouselocation').addClass('hide');
                                }
                                get_crew_details();
                                get_maintenance_details();
                                maintenance_detail_exist();
                                $('#maintenacecrewadd').removeClass('hide');
                                $('#addmaintenancedetail').removeClass('hide');
                                $('.attachmentview').removeClass('hide');
                                $('#save_btn').removeClass('hide');
                                $('#save_btn').html('Update');
                                stopLoad();
                                attachments_view();
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });
            }
        }


    }

    function load_vehicale_maintenace_status(maintenanceMasterID) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenanceMasterID': maintenanceMasterID},
            url: "<?php echo site_url('Fleet/fech_maintenace_status_details'); ?>",
            success: function (data) {
                if (data) {
                    $('#maintenacemasterid').val(data['maintenanceMasterID']);
                    $('#vehicalemasteridstatus').val(data['vehicleMasterID']);
                    $('#maintenancedonebystatus').val(data['maintenanceBy']);
                    $('#statusmaintenace').val(data['status']).change();
                    if (data['status'] == 2) {
                        $('.datestatuschange').removeClass('hide')
                        $('#stausdate').val(data['startedDatecon']);
                        $('#commentstauts').val(data['startingComment']);
                        $('#statusmaintenace').prop('disabled', false);
                        $('#stausdate').prop('readonly', false);
                        $('#commentstauts').prop('readonly', false);
                        $('#save_btn_status').removeClass('hide');

                    } else if (data['status'] == 3) {
                        $('.datestatuschange').removeClass('hide');
                        $('#statusmaintenace').prop('disabled', true);
                        $('#stausdate').prop('readonly', true);
                        $('#stausdate').val(data['closedDatecon']);
                        $('#commentstauts').val(data['closingComment']);
                        $('#commentstauts').prop('readonly', true);
                        $('#save_btn_status').addClass('hide');

                    } else {
                        $('.datestatuschange').addClass('hide');
                        $('#statusmaintenace').prop('disabled', false);
                        $('#stausdate').prop('readonly', false);
                        $('#commentstauts').prop('readonly', false);
                        $('#save_btn_status').removeClass('hide');
                    }
                    $('#ap_closed_user').modal('show');
                }
                ;
            }
        });

    }

    function get_crew_details() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/maintenace_crew_detail_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#crew_details_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function get_crew_details_view() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/maintenace_crew_detail_view_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#crew_details_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function delete_vehicale_crew(maintenanceCrewID) {
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
                    data: {'maintenanceCrewID': maintenanceCrewID},
                    url: "<?php echo site_url('Fleet/delete_maintenace_crew'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if (data[0] == 's') {
                            get_crew_details();
                            get_maintenance_details();
                            refreshNotifications(true);

                        }



                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_vmaintenace_details(maintenanceDetailID) {
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
                    data: {'maintenanceDetailID': maintenanceDetailID},
                    url: "<?php echo site_url('Fleet/delete_maintenace_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', 'Maintenace Detail Deleted Successfully');
                        get_maintenance_details();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_details_maintenace_details() {
        var data = $("#vehiclemaintenace_add_form").serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_maintenance_details_det'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    get_maintenance_details();
                    $('#maintenace_detail_modal').modal('hide');
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

    function crew_members() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {vehicalemaintenaceid: vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/fetch_crew_membersdropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_crew_member').html(data);
                    $('.select2').select2();
                    $('#crewmember').val(crewmember).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function get_maintenance_details() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/maintenace_detail_view_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#add_maintenance_details_view').html(data);
                    maintenance_detail_exist();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function get_maintenance_details_view() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/maintenace_detail_view_details_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#add_maintenance_details_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function update_is_doneyn_stauts(maintenanceDetailID, status) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenanceDetailID': maintenanceDetailID, 'status': status},
            url: "<?php echo site_url('Fleet/update_is_doneyn_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function maintenacedetailscommet(txtvalue, maintenanceDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'Comment': txtvalue, 'maintenanceDetailID': maintenanceDetailID},
            url: "<?php echo site_url('Fleet/update_doneyn_comment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }


    function maintenaceqtyupdate(txtvalue, maintenanceDetailID,maintenanceMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'qty': txtvalue, 'maintenanceDetailID': maintenanceDetailID,'maintenanceMasterID':maintenanceMasterID},
            url: "<?php echo site_url('Fleet/update_qty_maintenacedetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function maintenacecrewupdate(crewid, maintenanceDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'crewid': crewid, 'maintenanceDetailID': maintenanceDetailID},
            url: "<?php echo site_url('Fleet/update_crew_up_maintenacedetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function maintenaceunitcostupdate(txtvalue, maintenanceDetailID,maintenanceMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'unitcost': txtvalue, 'maintenanceDetailID': maintenanceDetailID,'maintenanceMasterID':maintenanceMasterID},
            url: "<?php echo site_url('Fleet/update_unitcost_maintenacedetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function maintenacetypedes(value, maintenanceDetailID) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenacetype': value, 'maintenanceDetailID': maintenanceDetailID},
            url: "<?php echo site_url('Fleet/update_maintenacetypedes_maintenacedetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                get_maintenance_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });

    }

    function vehicale_maintenace_emp_det(maintenanceMasterID) {
        $(".maintenancetype").val(null).trigger("change");
        if (maintenanceMasterID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {maintenanceMasterID: maintenanceMasterID},
                url: "<?php echo site_url('Fleet/fetch_maintenacedetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#maintenacetype').val(data['maintenanceCriteriadescription']);
                        $('#maintenacedoneby').val(data['crename']);
                        $('#commentsmaintenacedetails').val(data['comment']);
                        setTimeout(function () {
                            if (data['doneYN'] == 1) {
                                $('#maintenacedoneyes').iCheck('check');
                            } else if (data['doneYN'] == 0) {
                                $('#maintenacedoneno').iCheck('check');
                            }
                        }, 500);
                        $("#vehicale_maintenace_details_frm").modal({backdrop: "static"});

                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });


        }
    }

    function nextmaintenacecriteria() {
        if (vehicalemasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Fleet/maxmaintenacedetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#lastmaintenancedate').val(data['Maxdate']);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function nextmaintenacekm() {
        if (vehicalemasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Fleet/nextmaintenacekm'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#maintenancekm').val(data['maximumcurrentreading']);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function statuschangemaintenace() {
        swal({
                title: "Are you sure?",
                text: "You want to Change the Status!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {
                if (isConfirm) {

                } else {
                    $('#statusmaintenace').val(null).trigger('change');
                }
            });
    }

    function save_status_maintenace() {
        var maintenacetype = $('#maintenancedonebystatus').val();
        var statusmaintenace = $('#statusmaintenace').val();
        var data = $("#maintenace_status_form").serializeArray();

        if(maintenacetype== 2 && statusmaintenace == 3)
        {
            swal({
                    title: '',
                    text: "This Transaction Will Generate An Invoice," +
                    "Are you sure you want to continue ?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Fleet/save_maintenace_status'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1], data[2]);
                            if (data[0] == 's') {
                                get_veicale_maitenance_detail_view();
                                $('#ap_closed_user').modal('hide');
                                swal("Invoice Created !", data[2], "success");
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
        }
        else {
            swal({
                title: "Are you sure?",
                text: "You want to Change the Status!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Fleet/save_maintenace_status'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1], data[2]);
                            if (data[0] == 's') {
                                get_veicale_maitenance_detail_view();
                                $('#ap_closed_user').modal('hide');
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
        }






    }

    function calculatetotalamount(element) {
        var Qty = parseFloat($(element).closest('tr').find('.Qty').val());
        var unitcost = parseFloat($(element).closest('tr').find('.unitcost').val());
        $(element).closest('tr').find('.totalamt').val(((Qty * unitcost)))

    }
    function chkcurrentmeterreding(kmval) {

        var currentmeterreading = $('#currentmeterreading').val();
        if(currentmeterreading > kmval )
        {
            myAlert('w','Next maintenance km cannot be less than current meter reading');
            $('#nextmaintenance').val(' ');

        }
    }
    function attachments_view() {
        if (vehicalemaintenaceid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/attachments_vehicale_maintenace'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#attachments_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }
    function maintenance_detail_exist() {
        if(vehicalemaintenaceid)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'vehicalemaintenaceid': vehicalemaintenaceid},
                url: "<?php echo site_url('Fleet/maintenace_details_exist'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $("#maintenancedoneby").attr('disabled', 'disabled');

                    } else {
                        $("#maintenancedoneby").removeAttr('disabled');

                    }
                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }

    }

    function add_spare_parts(maintenanceCriteriaID,maintenanceMasterID,maintenanceDetailID) {

        fetch_detail(maintenanceCriteriaID,maintenanceDetailID);
        $('#maintenanceCriteriaID').val(maintenanceCriteriaID);
        $('#maintenacemasteridadd').val(maintenanceMasterID);
        $('#maintenacemasterdetailid').val(maintenanceDetailID);
        $('#maintenace_spare_parts').modal('show');
    }
    function add_spare_parts_status(maintenanceCriteriaID,maintenanceMasterID,maintenanceDetailID) {

        fetch_detail_status(maintenanceCriteriaID,maintenanceDetailID);
        $('#maintenanceCriteriaID').val(maintenanceCriteriaID);
        $('#maintenacemasteridadd').val(maintenanceMasterID);
        $('#maintenacemasterdetailid').val(maintenanceDetailID);
        $('#maintenace_spare_parts').modal('show');
    }
    function fetch_detail_status(maintenanceCriteriaID,maintenanceDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenacecritiria': maintenanceCriteriaID,'maintenanceDetailID':maintenanceDetailID},
            url: "<?php echo site_url('Fleet/fetch_maintenace_criteriadet'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#maintenace_table').empty();
                $('#save_btn_changes').addClass('hide');
                $('#add_spare_parts_main').addClass('hide');
                var maintenacetype =  $('#maintenancedoneby').val();

                if(data['statusaddedyn']['sparePartsAddedYN']==1)
                {
                    if (!jQuery.isEmptyObject(data['addsparedet'])) {
                        var x = 2;

                        $.each(data['addsparedet'], function (key, value) {

                            if(x == 2)
                            {
                                var deleterecordds = ' ';
                            } else
                            {
                                var deleterecordds = ' ';
                            }

                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                            var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)" readonly> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]" readonly>'


                            var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]" id="quantityRequested_'+key+'"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off" onkeyup="change_amount(this)" value="'+ value['qtyRequired']+'" readonly>'
                            var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  value="' + value['unitCost'] + '"   placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'

                            var description = ' <textarea class="form-control input-mini" rows="1" name="description[]" placeholder="..." readonly>' + value['comments'] + '</textarea>'
                            var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" id="currentstock_'+key+'"   placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';


                            if (value['selectedYN'] == 1)
                            {
                                var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" checked  disabled> <input type="hidden" name="isRequired[]" class="changeMandatory" value="1">';
                            }else
                            {
                                var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" disabled> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                            }






                            var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" id="total_'+key+'"   value="' + value['totalCost'] + '"  placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'

                            $('#maintenace_table').append('<tr><td>'+ itemsearch +'</td><td>'+ UOM +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');


                            fetch_related_uom_id( value['defaultUOMID'],value['uomID'],$('#uom_'+key));

                            fetch_spareparts_currentstock($('#currentstock_'+key),value['itemAutoID']);
                            change_amount($('#quantityRequested_'+key));
                            $('input').on('ifChanged', function(){
                                changeMandatory(this);
                            });
                            change_amount($('#quantityRequested_'+key));
                            initializeitemTypeahead(x);
                            x++;

                        });
                        $('.select2').select2();
                        search_id = x-1;
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });

                    }  else
                    {
                        var deleterecordds = ' ';

                        var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_1" placeholder="Item Id,Item Description" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" readonly>'

                        var uom = '<select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled> <option value=" ">Select UOM</option></select>';

                        var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';

                        var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" onkeyup="change_amount(this)" autocomplete="off" readonly>'

                        var description = '<textarea class="form-control" rows="1" name="description[]" readonly></textarea>'
                        var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" disabled> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                        var cost = ' <input type="text" onfocus="this.select();" name="cost[]"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                        var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'

                        $('#maintenace_table').append('<tr><td>'+itemsearch+'</td><td>'+ uom +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        initializeitemTypeahead(1);
                    }


                }else {
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var x = 2;
                        $.each(data['detail'], function (key, value) {

                            if(x == 2)
                            {
                                var deleterecordds = ' ';
                            } else
                            {
                                var deleterecordds = ' ';
                            }

                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                            var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)" readonly> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]" readonly>'


                            var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]" id="quantityRequested_'+key+'"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off" onkeyup="change_amount(this)" value="'+ value['qtyRequired']+'" readonly>'

                            var description = ' <textarea class="form-control input-mini" rows="1" name="description[]" placeholder="..." readonly>' + value['commentscriteria'] + '</textarea>'
                            var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" disabled> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';

                            var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                            var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" id="total_'+key+'"  placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'
                            var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" id="currentstock_'+key+'"   placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';

                            $('#maintenace_table').append('<tr><td>'+ itemsearch +'</td><td>'+ UOM +'</td><td>'+ currentstock +'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');


                            fetch_related_uom_id( value['defaultUOMID'],value['uomID'],$('#uom_'+key));
                            if(data['statusaddedyn']['sparePartsAddedYN']!=1){
                                fetch_spareparts_cost($('#cost_'+key),value['itemAutoID'])
                            }
                            fetch_spareparts_currentstock($('#currentstock_'+key),value['itemAutoID']);
                            $('input').on('ifChanged', function(){
                                changeMandatory(this);
                            });

                            change_amount($('#quantityRequested_'+key));
                            initializeitemTypeahead(x);
                            x++;

                        });
                        $('.select2').select2();
                        search_id = x-1;
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });

                    }  else
                    {
                        var deleterecordds = ' ';

                        var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_1" placeholder="Item Id,Item Description" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" readonly>'

                        var uom = '<select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled> <option value=" ">Select UOM</option></select>';
                        var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" onkeyup="change_amount(this)" autocomplete="off" readonly>'

                        var description = '<textarea class="form-control" rows="1" name="description[]" readonly></textarea>'
                        var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this)" disabled> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                        var cost = ' <input type="text" onfocus="this.select();" name="cost[]"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                        var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'

                        var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';



                        $('#maintenace_table').append('<tr><td>'+itemsearch+'</td><td>'+ uom +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        initializeitemTypeahead(1);
                    }

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });



    }

    function fetch_detail(maintenanceCriteriaID,maintenanceDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenacecritiria': maintenanceCriteriaID,'maintenanceDetailID':maintenanceDetailID},
            url: "<?php echo site_url('Fleet/fetch_maintenace_criteriadet'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#maintenace_table').empty();
                $('#save_btn_changes').removeClass('hide');
                $('#add_spare_parts_main').removeClass('hide');
                var maintenacetype =  $('#maintenancedoneby').val();

                if(data['statusaddedyn']['sparePartsAddedYN']==1)
                {
                    if (!jQuery.isEmptyObject(data['addsparedet'])) {
                        var x = 2;

                        $.each(data['addsparedet'], function (key, value) {

                            if(x == 2)
                            {
                                var deleterecordds = ' ';
                            } else
                            {
                                var deleterecordds = '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>';
                            }

                            if(value['mainCategory']!='Service')
                            {
                                var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  value="' + value['unitCost'] + '"   placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                            }else
                            {
                                var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  value="' + value['unitCost'] + '"   placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)">'
                            }


                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                            var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]">'


                            var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]" id="quantityRequested_'+key+'"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off" onkeyup="change_amount(this),checkCurrentStock(this)" value="'+ value['qtyRequired']+'">'


                            var description = ' <textarea class="form-control input-mini" rows="1" name="description[]" placeholder="...">' + value['comments'] + '</textarea>'

                     /*       if (value['selectedYN'] == 1)
                            {
                                var cheackyn =  '<select name="isRequired[]" class="form-control" tabindex="-1" aria-hidden="true"> <option value="1" selected>Yes</option> <option value="0">No</option></select>';
                            }else
                            {
                                var cheackyn =  '<select name="isRequired[]" class="form-control" tabindex="-1" aria-hidden="true"> <option value="1" >Yes</option> <option value="0" selected>No</option></select>';
                            }*/

                            if (value['selectedYN'] == 1)
                            {
                                 var cheackyn = '<input type="checkbox" class="requiredCheckbox" id="requiredCheckbox_'+key+'" data-value="req" onchange="changeMandatory(this),checkCurrentStockyn(this)" checked > <input type="hidden" name="isRequired[]" class="changeMandatory" value="1">';
                            }else
                            {
                                 var cheackyn = '<input type="checkbox" class="requiredCheckbox" id="requiredCheckbox_'+key+'" data-value="req" onchange="changeMandatory(this),checkCurrentStockyn(this)"> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                            }

                            var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" id="total_'+key+'"   value="' + value['totalCost'] + '"  placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'

                            var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" id="currentstock_'+key+'"   placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';

                            $('#maintenace_table').append('<tr><td>'+ itemsearch +'</td><td>'+ UOM +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');


                            fetch_related_uom_id( value['defaultUOMID'],value['uomID'],$('#uom_'+key));
                            fetch_spareparts_currentstock($('#currentstock_'+key),value['itemAutoID']);
                            change_amount($('#quantityRequested_'+key));
                            $('input').on('ifChanged', function(){
                                changeMandatory(this);
                                checkCurrentStockyn(this,value['mainCategory']);
                            });
                            number_validation();
                            initializeitemTypeahead(x);
                            x++;

                        });
                        $('.select2').select2();
                        search_id = x-1;
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });

                    }  else
                    {
                        var deleterecordds = ' ';

                        var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_1" placeholder="Item Id,Item Description" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">'

                        var uom = '<select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled> <option value=" ">Select UOM</option></select>';


                        var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" onkeyup="change_amount(this),checkCurrentStock(this)" autocomplete="off" >'

                        var description = '<textarea class="form-control" rows="1" name="description[]"></textarea>'
                        var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this),checkCurrentStockyn(this)"> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                        var cost = ' <input type="text" onfocus="this.select();" name="cost[]"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                        var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'

                        var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]"  placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';

                        $('#maintenace_table').append('<tr><td>'+itemsearch+'</td><td>'+ uom +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        initializeitemTypeahead(1);
                        number_validation();
                    }


                }else {
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var x = 2;
                        $.each(data['detail'], function (key, value) {

                            if(x == 2)
                            {
                                var deleterecordds = ' ';
                            } else
                            {
                                var deleterecordds = '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>';
                            }

                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                            var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]">'

                            if(value['mainCategory']!='Service')
                            {
                                var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                            }else
                            {
                                var cost = ' <input type="text" onfocus="this.select();" name="cost[]" id="cost_'+key+'"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)">'
                            }

                            var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]" id="quantityRequested_'+key+'"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off" onkeyup="change_amount(this),checkCurrentStock(this)" value="'+ value['qtyRequired']+'">'

                            var description = ' <textarea class="form-control input-mini" rows="1" name="description[]" placeholder="...">' + value['commentscriteria'] + '</textarea>'
                            var cheackyn = '<input type="checkbox" class="requiredCheckbox" id="requiredCheckbox_'+key+'"  data-value="req" onchange="changeMandatory(this),checkCurrentStockyn(this)"> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';

                            var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" id="total_'+key+'"  placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'
                            var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]" id="currentstock_'+key+'" placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';

                            $('#maintenace_table').append('<tr><td>'+ itemsearch +'</td><td>'+ UOM +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn + '</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');


                            fetch_related_uom_id( value['defaultUOMID'],value['uomID'],$('#uom_'+key));

                            if(data['statusaddedyn']['sparePartsAddedYN']!=1){
                                fetch_spareparts_cost($('#cost_'+key),value['itemAutoID'])
                            }
                            fetch_spareparts_currentstock($('#currentstock_'+key),value['itemAutoID']);
                            $('input').on('ifChanged', function(){
                                changeMandatory(this);
                                checkCurrentStockyn(this,value['mainCategory']);
                            });
                            number_validation();
                            change_amount($('#quantityRequested_'+key));
                            initializeitemTypeahead(x);
                            x++;

                        });
                        $('.select2').select2();
                        search_id = x-1;
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });

                    }  else
                    {
                        var deleterecordds = ' ';

                        var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_1" placeholder="Item Id,Item Description" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">'

                        var uom = '<select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled> <option value=" ">Select UOM</option></select>';
                        var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" onkeyup="change_amount(this),checkCurrentStock(this)" autocomplete="off" >'

                        var description = '<textarea class="form-control" rows="1" name="description[]"></textarea>'
                        var cheackyn = '<input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory(this),checkCurrentStockyn(this)"> <input type="hidden" name="isRequired[]" class="changeMandatory" value="0">';
                        var cost = ' <input type="text" onfocus="this.select();" name="cost[]"  placeholder="0.00" class="form-control number input-mini costcompanywac" required="" autocomplete="off"  onkeyup="change_amount(this)" readonly>'
                        var total = ' <input type="text" onfocus="this.select();" name="totalcost[]" placeholder="0.00" class="form-control number input-mini totalcost" required="" autocomplete="off" readonly>'


                        var currentstock = '<input type="text" onfocus="this.select();" name="currentstock[]"  placeholder="0.00" class="form-control number input-mini currentstock" required="" autocomplete="off" disabled>';


                        $('#maintenace_table').append('<tr><td>'+itemsearch+'</td><td>'+ uom +'</td><td>'+currentstock+'</td><td>'+ qty +'</td><td>'+ cost +'</td><td>'+total+'</td><td>'+ description +'</td><td class="requiredCheckbox">'+ cheackyn +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                        $('.requiredCheckbox').iCheck({
                            checkboxClass: 'icheckbox_minimal-blue'
                        });
                        number_validation();
                        initializeitemTypeahead(1);
                    }

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });



    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
    function add_more_spare_parts() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#addspareparts_add_table tbody tr:first').clone();

        appendData.find('.f_search').attr('id', 'f_search_' + search_id);

        appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val(0);
        appendData.find('.discount').val(0);
        appendData.find('.requiredCheckbox').html('<td align="center"><input type="checkbox" class="requiredCheckbox" data-value="req" onchange="changeMandatory()"><input type="hidden" name="isRequired[]" class="changeMandatory" value="0"></td>');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#addspareparts_add_table').append(appendData);
        var lenght = $('#addspareparts_add_table tbody tr').length - 1;
        $(".select2").select2();
        $('.requiredCheckbox').iCheck({ checkboxClass: 'icheckbox_minimal-blue' });
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');

        $('input').on('ifChanged', function(){
            changeMandatory(this);
            checkCurrentStockyn(this);
        });

        initializeitemTypeahead(search_id);
        number_validation();
    }
    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {

                $(element).closest('tr').find('.umoDropdown').empty()
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function initializeitemTypeahead(id) {

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Fleet/fetch_spareparts/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);


                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                fetch_spareparts_cost(this,suggestion.itemAutoID);
                fetch_spareparts_currentstock(this,suggestion.itemAutoID);
                change_amount(this);
                //checkCurrentStockyn(this,suggestion.mainCategory)
                if (suggestion.mainCategory == 'Service') {
                    $(this).closest('tr').find('.costcompanywac').prop('readonly', false);
                    $(this).closest('tr').find('.quantityRequested').removeAttr('onkeyup');
                   // $(this).closest('tr').find('.requiredCheckbox').removeAttr('onclick');
                    $(this).closest('tr').find('.currentstock').val('');
                } else {
                    $(this).closest('tr').find('.costcompanywac').prop('readonly', true);
                    $(this).closest('tr').find('.quantityRequested').attr('onkeyup','change_amount(this)');
                }
                $(this).closest('tr').css("background-color", 'white');
               /* if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }*/
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }


    function fetch_spareparts_cost(element,itemautoid) {
        var maintenanceCriteriaID = $('#maintenanceCriteriaID').val();
        var type = $('#maintenancedoneby').val();
        var qty = $(element).closest('tr').find('.quantityRequested').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenanceCriteriaID': maintenanceCriteriaID,'itemautoid':itemautoid},
            url: "<?php echo site_url('Fleet/fetch_cost_sprare_parts'); ?>",
            success: function (data) {
                if (data) {

                        $(element).closest('tr').find('.costcompanywac').val(data['companyLocalWacAmount']);
                        $(element).closest('tr').find('.totalcost').val(((parseFloat(qty) * parseFloat(data['companyLocalWacAmount'])).formatMoney(2, '.', ',')));


                }
            }
        });
    }

    function fetch_spareparts_currentstock(element,itemautoid) {
        var maintenanceCriteriaID = $('#maintenanceCriteriaID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenanceCriteriaID': maintenanceCriteriaID,'itemautoid':itemautoid},
            url: "<?php echo site_url('Fleet/fetch_cost_sprare_parts'); ?>",
            success: function (data) {
                if (data) {

                    $(element).closest('tr').find('.currentstock').val(data['currentStock']);


                }
            }
        });
    }

    function save_spareparts_added() {
        $('.umoDropdown').prop("disabled", false);
        var maintenanceCriteriaID = $('#maintenanceCriteriaID').val();
        var maintenacemasterdetailid = $('#maintenacemasterdetailid').val();
        var data = $('#maintenace_Criteria_form').serialize();
        /*$('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        })*/
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_spareparts_additional'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.umoDropdown').prop("disabled", true);
                    fetch_detail(maintenanceCriteriaID,maintenacemasterdetailid);
                    get_maintenance_details();
                }else
                {
                    $('.umoDropdown').prop("disabled", true);
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function remove_item_all_description(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }
    function change_amount(element) {
        var qty = $(element).closest('tr').find('.quantityRequested').val();
        var cost = $(element).closest('tr').find('.costcompanywac').val();
        $(element).closest('tr').find('.totalcost').val(((parseFloat(qty) * parseFloat(cost)).formatMoney(2, '.', ',')));

    }
    function changeMandatory(obj, str){
        var currentStock = $(obj).closest('tr').find('.currentstock').val();
        var quantityRequested = $(obj).closest('tr').find('.quantityRequested').val();
        var status = ($(obj).is(':checked')) ? 1 : 0;
        var str = $(obj).attr('data-value');
        $(obj).closest('tr').closest('tr').find('.changeMandatory').val(status);


    }
    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Selected item quantity is not sufficient');
            $(det).val(0);
            $(det).closest('tr').find('.totalcost').val(0);
        }
    }
    function checkCurrentStockyn(det,Category) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        var quantityRequested = $(det).closest('tr').find('.quantityRequested').val();

        if(Category !='Service')
        {
            if(quantityRequested > parseFloat(currentStock) )
            {
                setTimeout(function () {
                    $(det).iCheck('uncheck');

                }, 500);
                myAlert('w', 'Selected item quantity is not sufficient',4000);
               // $(det).closest('tr').find('.totalcost').val(0);
              //  $(det).closest('tr').find('.quantityRequested').val(0);
            }
        }

    }


</script>
