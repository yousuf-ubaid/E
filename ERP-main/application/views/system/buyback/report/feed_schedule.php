<?php echo head_page($_POST["page_name"], True);
$this->load->helper('buyback_helper');
$batchMasterID_arr = load_buyBack_batches_report();
$farms_arr = load_all_farms();
$date_format_policy = date_format_policy();
$location_arr = load_all_locations();
$field_Officer = buyback_farm_fieldOfficers_drop();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
$mortality_causes_arr = load_buyBack_mortality_Causes();
$uom_arr = array('' => 'Select UOM');
$warehouse_arr = all_delivery_location_drop();
$warehouse_arr_default = default_delivery_location_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>

<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>
<form id="dispatch_filter_frm">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="row" style="padding-left: 17px">
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">Date From</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="feedscheduleDatefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="feedscheduleDatefrom" class="form-control"  value=""  >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="feedscheduleDateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="feedscheduleDateto"  class="form-control" value="" >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="area">Area</label><br>
                <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID" onchange="startMasterSearch()"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="area">Sub Area</label><br>
                <?php echo form_dropdown('subLocationID', array(" " => "Select Sub Area"), '', 'class="form-control select2" id="subLocationID" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="farmer">Farmer </label><br>
                <?php echo form_dropdown('farmer', $farms_arr, '', 'class="form-control select2" id="farmer" onchange="startMasterSearch()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="fieldofficer">Field Officer</label><br>
                <?php echo form_dropdown('fieldofficer', $field_Officer, '', 'class="form-control select2" id="fieldofficer" onchange="startMasterSearch()"'); ?>
            </div>

        </div>
        <div class="row" style="padding-left: 17px; margin-top: 3px; margin-bottom: 3px;">


        </div>
        <div class="row" style="padding-left: 17px; margin-top: 3px; margin-bottom: 3px;">
            <div class="col-sm-4" style="margin-top: 2%">
                <div class="box-tools">
                    <div class="has-feedback">
                        <input name="searchTask" type="text" class="form-control input-sm"
                               placeholder="Search Farmer or Batch"
                               id="searchTask" onkeypress="startMasterSearch()">
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-2" style="margin-top: 2%">
                <?php echo form_dropdown('feed_status', array(''=>'Status','0' => 'Active', '1' => 'Closed'), '0', 'class="form-control" onchange="startMasterSearch()" id="feed_status"'); ?>
            </div>
            <div class="col-sm-2">
                <label for="supplierPrimaryCode">Column</label>
                <div class="input-group">
                    <?php echo form_dropdown('columnDrop[]', array('CHICKS' => 'LIVE STOCK', 'AGE' => 'AGE', 'FEED UP TO' => 'FEED UP TO', 'NEXT FEED' => 'NEXT FEED', 'FEED VALUE' => 'FEED VALUE'), '0', 'class="form-control" multiple="" onchange="" id="columnDrop"'); ?>

                </div>
               <!-- <label class="title"> Column &nbsp;&nbsp; </label>-->
                <!--Main Category-->
                <?php // echo form_dropdown('columnDrop[]', array('CHICKS' => 'LIVE STOCK', 'AGE' => 'AGE', 'FEED UP TO' => 'FEED UP TO', 'NEXT FEED' => 'NEXT FEED', 'FEED VALUE' => 'FEED VALUE'), '0', 'class="form-control" multiple="" onchange="" id="columnDrop"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode">Batch Age</label>
                <div class="input-group">
                    <input class="age text-center" id="ageFrom" name="ageFrom" placeholder="&nbsp;&nbsp;From" onchange="startMasterSearch()" style="width: 65px;">
                    <label class="">&nbsp; - &nbsp;</label>
                    <input class="age text-center" id="ageTo" name="ageTo" placeholder="&nbsp;&nbsp;&nbsp;To" onchange="startMasterSearch()" style="width: 65px;">
                </div>
            </div>
            <div class="col-sm-1 hide" id="search_cancel" style="margin-top: 2%">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div id="BatchMaster_view" style="margin: 1%"></div>
            </div>
        </div>
    </div>
</form>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
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
<div class="modal fade" id="buyback_production_report_modal" tabindex="2" role="dialog"
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
                <div id="productionReportDrilldown"></div>
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
<div class="modal fade" id="buyback_dispatch_note_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Dispatch Note<span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="dispatchNoteReportDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="mortality_add_new_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <b>Add New Mortality</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="80" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="mortality_add_item_form" class="form-horizontal">
                    <input type="hidden" name="batchID" id="batchID">
                    <input type="hidden" name="currentbirds" id="currentbirds">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="font-size: 16px; font-weight: 700;">Current Birds : <label id="currenct"> </label></div>

                        </div>
                    </div>

                    <table class="table table-bordered table-condensed no-color" id="mortality_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Mortality Cause <?php required_mark(); ?></th>
                            <th style="width: 150px;">No of Birds <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost<?php required_mark(); ?></th>
                            <th style="width: 150px;">Total Amount<?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_mortality()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('causeID[]', $mortality_causes_arr, '', 'class="form-control mortalityCausesDropdown"  required'); ?></td>
                            <td><input type="text" name="noOfBirds[]" id="noOfBirds" onfocus="this.select();"
                                       class="form-control number noOfBirds" onkeyup="greaterthantest(this); calculateTotalCost_mortality(this)" required></td>
                            <td><input type="text" name="unitCost[]" id="unitCost" onfocus="this.select();"
                                       class="form-control number unitCost" required disabled></td>
                            <td><input type="text" name="totalCost[]" id="totalCost" onfocus="this.select();"
                                       class="form-control number totalCost" required></td>
                            <td><textarea class="form-control" rows="1" name="comment[]" placeholder="Remarks..."
                                ></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_mortality_birds()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="goodReceiptNote_add_item_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg modal_resize" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" ><b>Add New GRN</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="100" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="goodReceiptNote_add_item_form" class="form-horizontal">
                    <div class="row">
                        <div class=" col-sm-3" style="margin-bottom: 10px ">
                            <label for="">Select Warehouse :</label>
                            <?php echo form_dropdown('wareHouseAutoID', $warehouse_arr, $warehouse_arr_default, 'class="form-control select2" id="wareHouseAutoID" required'); ?>
                        </div>
                    </div>
                    <input type="hidden" name="grn_batchID" id="grn_batchID" class="grn_batchID">
                    <table class="table table-bordered table-condensed no-color" id="goodReceiptNote_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th id="qtyChicksShow" style="width: 150px;">Qty Live Stock<?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th id="mortalityShow" style="width: 100px;">Mortality <?php required_mark(); ?></th>
                            <th id="returnShow" style="width: 100px;">Return <?php required_mark(); ?></th>
                            <th id="balanceShow" style="width: 100px;">Balance <?php required_mark(); ?></th>
                            <th colspan="3" style="width: 200px;">Qty Received</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_grn()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search"
                                       name="search[]"
                                       placeholder="Item ID, Item Description..." id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td id="valueqtyChicksShow"><input type="text" name="qtybirds[]" onfocus="this.select();"
                                                               class="form-control number qtybirds_grn" required readonly></td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required disabled'); ?></td>

                            <td id="valuemortalityShow"><input type="text" name="mortality[]" onfocus="this.select();"
                                                               class="form-control number mortality_grn" required readonly></td>

                            <td id="valuereturnShow"><input type="text" name="return[]" onfocus="this.select();"
                                                            class="form-control number return_grn" required readonly></td>

                            <td id="valuebalanceShow"><input type="text" name="balance[]" class="form-control number balance_grn" readonly></td>

                            <td><input type="text" name="noofbirds[]" onfocus="this.select();"
                                       class="form-control number noofbirds"  onkeyup="birdvalidation(this)" placeholder="No of Birds"></td>
                            <td><input type="text" name="kgweight[]" onfocus="this.select();"
                                       class="form-control number kgweight" placeholder="KG"></td>
                            <td><input type="text" name="Amount[]" onfocus="this.select();"
                                       class="form-control number Amount" placeholder="Amount"></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_GoodReceiptNote_item()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="dispatchNote_add_item_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" ><b>Add New Dispatch</b> &nbsp;&nbsp; <input id="farmerBatchName_dispatch" name="farmerBatchName" size="100" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="dispatchNote_add_item_form" class="form-horizontal">
                    <input class="hidden" id="chickTotal_dispatch">
                    <input class="hidden" id="batchID_dispatch" name="batchID_dispatch">
                    <div class="col-sm-3">
                        <div class="" style="margin-bottom: 10px ">
                            <label for="">Select Warehouse :</label>
                            <?php echo form_dropdown('wareHouseAutoID_dispatch', $warehouse_arr, $warehouse_arr_default, 'class="form-control select2" id="wareHouseAutoID_dispatch" required'); ?>
                        </div>
                    </div>
                    <div class="col-sm-9 text-right" style="font-size: 16px; font-weight: 700;"><br><br><label id="currenct_dispatch"></label></div>
                    <table class="table table-bordered table-condensed no-color" id="dispatchNote_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th style="width: 150px;">Current Stock</th>
                            <th style="width: 150px;">Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost <span class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_dispatch()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search_dispatch f_search_dispatch"
                                       name="search[]"
                                       placeholder="Item ID, Item Description..." id="f_search_dispatch_1">
                                <input type="hidden" class="form-control itemAutoID_dispatch" name="itemAutoID[]">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <td><input type="text" name="currentstock[]"
                                       class="form-control currentstockadditem_dispatch number" id="currentstockadditem" readonly></td>
                            <td><input type="text" name="quantityRequested[]" onfocus="this.select();"
                                       onkeyup="validatetb_row(this)"
                                       class="form-control quantityRequested_dispatch number" id="quantityrequestedadditem" required></td>
                            <td><input type="text" name="estimatedAmount[]" placeholder="0.00"
                                       onchange="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number estimatedAmount_dispatch"></td>
                            <td><textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment..."
                                ></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_dispatchNote_item_form()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_grn_id = 1;
    var search_dispatch_id = 1;
    $('body').addClass('sidebar-collapse');
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/report/feed_schedule', '', 'Feed Schedule');
        });
        initializeitemTypeaheadGRN(search_grn_id);
        initializeitemTypeahead_DP(search_dispatch_id);
        getBatchManagement_tableView();
        $('.select2').select2();
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    /*Dispatch Transaction creation related functions*/
    function new_dispatch_feedSchecule(chickTotal,batchID) {
        $('.umoDropdown').attr('disabled', true);
        $('#dispatchNote_add_item_form')[0].reset();
        $('#currenct_dispatch').text('Current Birds : ' + chickTotal);
        $('#chickTotal_dispatch').val(chickTotal);
        $('#batchID_dispatch').val(batchID);
        // $('#farmerBatchName').val(data['farmer']);
        farmBatchName(batchID);
        $('#dispatchNote_detail_add_table tbody tr').not(':first').remove();
        $("#dispatchNote_add_item_modal").modal({backdrop: "static"});
        $('.f_search_dispatch').closest('tr').css("background-color", 'white');
        $('.currentstockadditem_dispatch').closest('tr').css("background-color", 'white');
    }
    function add_more_dispatch() {
        search_dispatch_id += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#dispatchNote_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search_dispatch').attr('id', 'f_search_dispatch_' + search_dispatch_id);
        appendData.find('.f_search_dispatch').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.umoDropdown ,.item_text').empty();
        appendData.find('.umoDropdown').prop('disabled', false);
        appendData.find('.umoDropdown').removeClass('uom_disabled');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#dispatchNote_detail_add_table').append(appendData);
        var lenght = $('#dispatchNote_detail_add_table tbody tr').length - 1;
        $('#f_search_dispatch_' + search_dispatch_id).closest('tr').css("background-color", 'white');

        //$(".select2").select2();
        initializeitemTypeahead_DP(search_dispatch_id);
        number_validation();
    }
    function initializeitemTypeahead_DP(id) {
        $('#f_search_dispatch_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode/',
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#f_search_dispatch_' + id).closest('tr').find('.itemAutoID_dispatch').val(suggestion.itemAutoID);
                }, 200);
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/// Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                validate_dispatch_rawmaterials(this, suggestion.itemAutoID, id);
                $(this).closest('tr').find('.estimatedAmount_dispatch').val(suggestion.companyLocalSellingPrice);
                $(this).closest('tr').find('.currentstockadditem_dispatch').val(suggestion.currentStock);
                $(this).closest('tr').css("background-color", 'white');
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
            }
        });
    }
    function validate_dispatch_rawmaterials(element, itemAutoID, id){
      var chickTotal = $('#chickTotal_dispatch').val();
      if(chickTotal > 0){
          $.ajax({
              async: true,
              type: 'post',
              dataType: 'json',
              data: {itemAutoID: itemAutoID},
              url: "<?php echo site_url('Buyback/validate_dispatch_rawmaterials'); ?>",
              success: function (data) {
                  if (data['buybackItemType'] == 1) {
                      myAlert('w','Raw materials are dispatched already!');
                      $(element).closest('tr').find('.itemAutoID_dispatch').val('');
                      $(element).closest('tr').find('#f_search_dispatch_' + id).val('');
                      $(element).closest('tr').find('.estimatedAmount_dispatch').val('');
                      $(element).closest('tr').find('.umoDropdown').val('Select UOM');
                      $(element).closest('tr').find('.currentstockadditem_dispatch').val('');
                      $(element).closest('tr').css("background-color", 'white');
                  }
              }
          });
      }
    }
    function validatetb_row(det) {
        var currentStock= $(det).closest('tr').find('.currentstockadditem_dispatch').val();
        if(det.value > parseFloat(currentStock))
        {
            myAlert('w','Quantity should be less than or equal to current stock');
            $(det).val('');
        }

        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
    function save_dispatchNote_item_form() {
        $('.umoDropdown').attr('disabled', false);
        var data = $("#dispatchNote_add_item_form").serializeArray();
        data.push({'name': 'delivery_location', 'value': $('#wareHouseAutoID_dispatch option:selected').text()});
        $('#dispatchNote_add_item_form' + ' select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });

        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $('.quantityRequested').each(function () {
            if (this.value == '' || this.value == 0) {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_dispatchNote_feed_schedule'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.uom_disabled').prop('disabled', true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#dispatchNote_add_item_modal').modal('hide');
                }
            }, error: function () {
                $('.uom_disabled').prop('disabled', true);
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    /*GRN transaction creation related functions*/
    function grn_new_feedSchedule(batchID){
        if (batchID) {
            $('#goodReceiptNote_add_item_form')[0].reset();
            $('#wareHouseAutoID').val('').change();
            $('.grn_batchID').val(batchID);
            $('#goodReceiptNote_table tbody tr').not(':first').remove();
            $("#goodReceiptNote_add_item_modal").modal({backdrop: "static"});
        }
    }
    function initializeitemTypeaheadGRN(id) {
        $('#f_search_' + id ).autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode_grn/',
            onSelect: function (suggestion) {
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/// Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                fetch_goodReciptNote_batch_chicks(this);
                fetch_goodReciptNote_batch_mortality(this);
                fetch_return_qty_chicks(this);
                fetch_balancechicks(this);
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
            }
        });
    }
    function fetch_goodReciptNote_batch_chicks(element) {
        var batchID = $('.grn_batchID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_chicks'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.qtybirds_grn').val(data['chicksTotal']);
                }
            }
        });
    }
    function fetch_goodReciptNote_batch_mortality(element) {
        var batchID = $('.grn_batchID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_mortality'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.mortality_grn').val(data['mortalityTotal']);
                }
            }
        });
    }
    function fetch_return_qty_chicks(element) {
        var batchID = $('.grn_batchID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/returnqtychicks'); ?>",
            success: function (data) {
                if (data) {

                    $(element).closest('tr').find('.return_grn').val(data['qtynew']);
                }
            }
        });
    }
    function fetch_balancechicks(element) {
        var batchID = $('.grn_batchID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_balance_chicks'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.balance_grn').val(data);
                }
            }
        });
    }
    function fetch_related_uom_id_b(masterUnitID, select_value, element, isSubItemExist) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('Dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        if (isSubItemExist == 1) {
                            $(element).closest('tr').find('.umoDropdown').prop('disabled', true);
                            $(element).closest('tr').find('.umoDropdown').addClass('uom_disabled', true);
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function birdvalidation(det) {
        var currentbalncebirds = $(det).closest('tr').find('.balance_grn').val();
        if(det.value > parseFloat(currentbalncebirds)){
            myAlert('w','No of birds should be less than or equal to Balance Birds');
            $(det).val('');
        }
    }
    function clearitemAutoID(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }
    function add_more_grn() {
        search_grn_id += 1;
        //$('.select2').select2('destroy');
        var appendData = $('#goodReceiptNote_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_grn_id);
        appendData.find('.f_search').attr('id', 'f_search_' + search_grn_id);
        appendData.find('.f_search').val('');
        appendData.find('.itemAutoID').val('');
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.umoDropdown').prop('disabled', false);
        appendData.find('.umoDropdown').removeClass('uom_disabled');
        appendData.find('.noofbirds').val('');
        appendData.find('.kgweight').val('');
        appendData.find('.Amount').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#goodReceiptNote_table').append(appendData);
        var lenght = $('#goodReceiptNote_table tbody tr').length - 1;
        //$(".select2").select2();
        initializeitemTypeaheadGRN(search_grn_id);
        number_validation();
    }
    function save_GoodReceiptNote_item() {
        $('.umoDropdown').attr('disabled', false);
        var data = $("#goodReceiptNote_add_item_form").serializeArray();
        data.push({'name': 'delivery_location', 'value': $('#wareHouseAutoID option:selected').text()});
        $('#goodReceiptNote_add_item_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_goodReceiptNote_feedSchedule'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.uom_disabled').prop('disabled', true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#goodReceiptNote_add_item_modal').modal('hide');
                    $('.umoDropdown').attr('disabled', true);
                }
            }, error: function () {
                $('.uom_disabled').prop('disabled', true);
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    
    /*Mortality transaction creation*/
    function mortality_new_modal_feedSchedule(chickCount, batchID) {
            // loadbirdcurrent(mortalityAutoID,batchMasterID);
            $('#batchID').val(batchID);
            $('#currentbirds').val(chickCount);
            $('#currenct').html(chickCount);
            farmBatchName(batchID);
            fetchBirdUnitCost(batchID);
            $('#mortality_add_item_form')[0].reset();
            $('#mortality_detail_add_table tbody tr').not(':first').remove();
            $("#mortality_add_new_modal").modal({backdrop: "static"});
    }
    function farmBatchName(batchMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchMasterID},
            url: "<?php echo site_url('Buyback/fetchFarmBatch_grn'); ?>",
            success: function (data)
            {
                if(data){
                    $('#farmerBatchName').val(data);
                    $('#farmerBatchName_dispatch').val(data);
                }
            }
        });
    }
    function fetchBirdUnitCost(batchMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchMasterID},
            url: "<?php echo site_url('Buyback/fetchBirdUnitCost'); ?>",
            success: function (data)
            {
                if(data){
                    $('#unitCost').val(data);
                }
            }
        });
    }
    function add_more_mortality() {
        var appendData = $('#mortality_detail_add_table tbody tr:first').clone();
        // appendData.find('input').val('');
        appendData.find('.noOfBirds').val('');
        appendData.find('.totalCost').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mortality_detail_add_table').append(appendData);
        var lenght = $('#mortality_detail_add_table tbody tr').length - 1;
        number_validation();
    }
    function save_mortality_birds() {
        var data = $("#mortality_add_item_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_feedSchedule_mortality'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mortalityDetailID = null;
                    $('#mortality_add_new_modal').modal('hide');
                }
                setTimeout(function () {
                    getBatchManagement_tableView();
                }, 500);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function greaterthantest(det) {
        var currentbirds =$('#currentbirds').val();
        if(det.value > parseFloat(currentbirds)){
            myAlert('w','Birds shoud not be greater than the current birds');
            $(det).val('');
        }
    }
    function calculateTotalCost_mortality(element) {
        var Qty = parseFloat($(element).closest('tr').find('.noOfBirds').val());
        var unitcost = parseFloat($(element).closest('tr').find('.unitCost').val());
        $(element).closest('tr').find('.totalCost').val(((Qty * unitcost)).toFixed(2))
    }
    
    
    
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
    $('#columnDrop').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 190,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#columnDrop").multiselect2('selectAll', false);
    $("#columnDrop").multiselect2('updateButtonText');

    $("#columnDrop").change(function () {
        if ((this.value)) {
            getBatchManagement_tableView(this.value);
            return false;
        }
    });

    function getBatchManagement_tableView() {
        var searchTask = $('#searchTask').val();
        var data = $('#dispatch_filter_frm').serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Buyback/load_feed_schedule_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#BatchMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getBatchManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#searchTask').val('');
        $('#farmer').val('').change();
        $('#locationID').val('').change();
        $('#fieldofficer').val('').change();
        $('#subLocationID').val('').change();
        $('#feedscheduleDatefrom').val('');
        $('#feedscheduleDateto').val('');
        $('#ageFrom').val('');
        $('#ageTo').val('');
        $('#feed_status').val('').change();
        getBatchManagement_tableView();
    }

    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });

    /*call report content*/
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

    function generateProductionReport(batchMasterID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {batchMasterID: batchMasterID,typecostYN:'1'},
            url: '<?php echo site_url('Buyback/buyback_production_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#productionReportDrilldown").html(data);
                $('#buyback_production_report_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function generateDispatchNoteReport(dispatchAutoID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {dispatchAutoID: dispatchAutoID, html: 'html'},
            url: '<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#dispatchNoteReportDrilldown").html(data);
                $('#buyback_dispatch_note_modal').modal("show");
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    $("#locationID").change(function () {
        get_buyback_subArea($(this).val())
    });
    function get_buyback_subArea(locationID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {locationID: locationID},
            url: "<?php echo site_url('Buyback/fetch_buyback_subArea'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#subLocationID').empty();
                var mySelect = $('#subLocationID');
                mySelect.append($('<option></option>').val("").html("Select"));
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, text) {
                        mySelect.append($('<option></option>').val(text['locationID']).html(text['description']));
                    });
                }
                if(subLocationID){
                    mySelect.val(subLocationID).change();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });
    Inputmask().mask(document.querySelectorAll("input"));

    function generateReportPdf() {
       var form = document.getElementById('dispatch_filter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/get_buy_back_feedSchedule_rpt_pdf'); ?>';
        form.submit();
    }

    </script>