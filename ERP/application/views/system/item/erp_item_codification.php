<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
echo head_page('Item Codification', false);
$uom_arr = all_umo_new_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$stock_adjustment = stock_adjustment_control_drop();
/*echo head_page('Item Master', false);*/
$main_category_arr = all_main_category_drop();
$usergroupcompanywiseallow = getPolicyValuesgroup('ITM','All');
?>
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

    .fc {
        height: 22px !important;
        width: 100% !important;
        display: inline !important;
        margin: 0px !important;
    }

    .arrowDown {
        vertical-align: sub;
        color: rgb(75, 138, 175);
        font-size: 13px;
    }

    .applytoAll {
        display: none;
        vertical-align: top;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<ul class="nav nav-tabs" id="jobTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link" id="codificationtab" data-toggle="tab" href="#codificatnTb" role="tab" aria-controls="home" aria-selected="false">Codification</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="codificatnsetuptab" data-toggle="tab" href="#codificatnstuptb" role="tab" aria-controls="profile" aria-selected="false">Setup</a>
    </li>
</ul>

<div class="tab-content" id="TabContent">
    <div class="tab-pane fade" id="codificatnTb" role="tabpanel" aria-labelledby="home-tab">
        <br>
        <div class="row">
            <div class="col-md-6">

            </div>

            <div class="col-md-6">
                <button type="button" class="btn btn-success pull-right"
                        onclick="openitmCodificatnModal()"><i class="fa fa-plus"></i>
                    Add New
                </button>
            </div>
        </div>
        <br>

        <hr>
        <div class="table-responsive">
            <table id="item_codification_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th>Description</th>
                    <th>Value Type</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="item_codification_table_body">

                </tbody>
            </table>

        </div>
    </div>

    <div class="tab-pane fade" id="codificatnstuptb" role="tabpanel" aria-labelledby="home-tab">
        <br>
        <div class="row">
            <div class="col-md-6">

            </div>

            <div class="col-md-6">
                <button type="button" class="btn btn-success pull-right"
                        onclick="opensetupModal()"><i class="fa fa-plus"></i>
                    Add Setup
                </button>
            </div>
        </div>
        <br>

        <hr>
        <div class="table-responsive">
            <table id="codificatn_setup_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 85%">Description</th>
                    <th style="min-width: 5%">No of elements</th>
                    <th style="min-width: 5%">Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog"  id="codificatn_master_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Codification Master</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="attribute_form"'); ?>

                <input type="hidden" id="attributeID" name="attributeID">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control"  id="attributeDescription" name="attributeDescription">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Value Type <?php required_mark(); ?></label>
                        <select name="valueType" id="valueType" class="form-control">
                            <option value="0">Text</option>
                            <option value="1">Numeric</option>
                        </select>
                    </div>
                </div>


                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"  onclick="save_attribute()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>




<div aria-hidden="true" role="dialog"  id="codificatn_master_sub_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Sub Codification Master</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="subattribute_form"'); ?>

                <input type="hidden" id="attributeIDsub" name="attributeID">
                <input type="hidden" id="levl" name="levl">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control"  id="attributeDescriptionsub" name="attributeDescription">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Value Type <?php required_mark(); ?></label>
                        <select name="valueType" id="valueTypesub" class="form-control">
                            <option value="0">Text</option>
                            <option value="1">Numeric</option>
                        </select>
                    </div>
                </div>


                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"  onclick="save_attribute_sub()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="codificatn_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Codification Details &nbsp;&nbsp;&nbsp;- <span id="codificatndtllbl" style="font-weight: bold;"></span></h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="attribute_dtl_form"'); ?>
                <input type="hidden" id="attributeIDdtl" name="attributeID">
                <input type="hidden" id="attributeDetailID" name="attributeDetailID">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label>Code <?php required_mark(); ?></label>
                        <input type="text" class="form-control"  id="detailDescription" name="detailDescription">
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control"  id="comment" name="comment">
                    </div>
                    <div class="form-group col-sm-3 asnto">
                        <label>Assigned To <?php required_mark(); ?></label>
                        <select name="masterID" id="masterIDDtl" class="form-control">
                            <option value="">Please Select</option>

                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label style="color: white;">add button</label>
                        <button type="button" class="btn btn-sm btn-success pull-right"
                                onclick="save_attribute_detail()"><i class="fa fa-plus"></i>
                            Add Detail
                        </button>
                    </div>
                </div>
                </form>
                <br>
                <div class="table-responsive">
                    <table id="codification_detail_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 12%">Code</th>
                            <th style="min-width: 12%">Description</th>
                            <th style="min-width: 12%">Master</th>
                            <th style="min-width: 5%">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="codificatn_setup_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Setup</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="setup_form"'); ?>
                <input type="hidden" id="codificationSetupID" name="codificationSetupID">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control"  id="descriptionSetup" name="description">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>No of elements <?php required_mark(); ?></label>
                        <input type="number" class="form-control"  id="noOfElement" name="noOfElement">
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary"  onclick="save_setup()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="setup_detail_modal" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Setup</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="setupDetailID" name="setupDetailID">
                <div id="setupdetaildv">

                </div>
            </div>
            <div class="modal-footer">
                <!--<button class="btn btn-primary"  onclick="save_setup_detail()">Save</button>-->
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog"  id="setup_assign_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Assign to category</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="setup_assign_form"'); ?>
                <input type="hidden" id="codificationSetupIDAssign" name="codificationSetupID">
                <div class="row">

                    <div class="form-group col-sm-3">
                        <label>Category</label>
                        <?php  echo form_dropdown('mainCategoryID', $main_category_arr,'','class="form-control" onchange="load_subcategory()" id="mainCategoryID"'); ?>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Sub Category</label>
                        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox">
                            <option value="">Select Category </option>

                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label style="color: white;">add button</label>
                        <button type="button" class="btn btn-sm btn-success pull-right"
                                onclick="save_assigned_setup_detail()"><i class="fa fa-plus"></i>
                            Add Detail
                        </button>
                    </div>
                </div>
                </form>
                <br>
                <div class="table-responsive">
                    <table id="setup_assign_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 12%">Setup</th>
                            <th style="min-width: 12%">Category</th>
                            <th style="min-width: 12%">Sub Category</th>
                            <th style="min-width: 5%">Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    var Otabled;
    var Otablestup;
    var Otableasncat;
    $(document).ready(function () {
        $('#codificationtab').click();
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_codification', '', 'Item Codification');
        });
        //load_item_codification_table();
        item_codification_table_body();
        load_codification_setup_table();
        $('.select2').select2();
    });

    function load_item_codification_table(selectedID=null) {
        Otable = $('#item_codification_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Codification/load_item_codification_table'); ?>",
            "aaSorting": [[0, 'DESC']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['attributeID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "attributeID"},
                {"mData": "attributeDescription"},
                {"mData": "valtyp"},
                {"mData": "codificatn_action"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "contractUID", "value": $("#contractUIDjob").val()});
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

    function load_codification_setup_table(selectedID=null) {
        Otablestup = $('#codificatn_setup_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Codification/load_codification_setup_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['codificationSetupID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "codificationSetupID"},
                {"mData": "description"},
                {"mData": "noOfElement"},
                {"mData": "setup_action"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({"name": "contractUID", "value": $("#contractUIDjob").val()});
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
    
    function openitmCodificatnModal() {
        $('#attributeID').val('');
        $('#attributeDescription').val('');
        $('#codificatn_master_modal').modal('show');
    }

    function save_attribute() {
        var $form = $('#attribute_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Codification/save_attribute'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#codificatn_master_modal').modal('hide');
                    item_codification_table_body()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addSubAttribute(attributeID,levl) {
        $('#attributeIDsub').val(attributeID);
        $('#levl').val(levl);
        $('#attributeDescriptionsub').val('');
        $('#codificatn_master_sub_modal').modal('show');
    }
    
    function save_attribute_sub() {
        var $form = $('#subattribute_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Codification/save_attribute_sub'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#codificatn_master_sub_modal').modal('hide');
                    item_codification_table_body()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addAttrDetail(attributeID,masterID,attributeDescription) {
        $('#masterIDDtl option').remove();
        $('#masterIDDtl').val("");
        $('#comment').val("");
        $('#detailDescription').val("");
        $('#attributeIDdtl').val(attributeID);
        $('#codificatndtllbl').html(attributeDescription);
        if(masterID==0){
            $('.asnto').addClass('hidden');
        }else{
            $('.asnto').removeClass('hidden');
            load_assignto_drop(masterID)
        }
        $('#codificatn_detail_modal').modal('show');
        load_codification_detail_table(attributeID,null);
    }

    function load_codification_detail_table(attributeID,selectedID=null) {
        Otabled = $('#codification_detail_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Codification/load_codification_detail_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['attributeID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "attributeID"},
                {"mData": "detailDescription"},
                {"mData": "comment"},
                {"mData": "mastrdtl"},
                {"mData": "codificatn_action"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "attributeID", "value": attributeID});
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

    function load_assignto_drop(masterID) {
        $('#masterIDDtl option').remove();
        $('#masterIDDtl').val("");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Codification/load_assignto_drop"); ?>',
            dataType: 'json',
            data: {'attributeID': masterID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#masterIDDtl').empty();
                    var mySelect = $('#masterIDDtl');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['attributeDetailID']).html(text['detailDescription']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    
    function save_attribute_detail() {
        var $form = $('#attribute_dtl_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Codification/save_attribute_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#attributeDetailID').val("");
                    $('#masterIDDtl').val("");
                    $('#comment').val("");
                    $('#detailDescription').val("");
                    Otabled.draw()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function opensetupModal() {
        $('#codificationSetupID').val();
        $('#descriptionSetup').val('');
        $('#noOfElement').val('');
        $('#codificatn_setup_modal').modal('show');
    }

    function save_setup() {
        var $form = $('#setup_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Codification/save_setup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#codificationSetupID').val();
                    $('#descriptionSetup').val('');
                    $('#noOfElement').val('');
                    $('#codificatn_setup_modal').modal('hide');
                    Otablestup.draw()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function openSetupDetail(codificationSetupID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Codification/load_setup_detail'); ?>",
            data: {codificationSetupID: codificationSetupID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#setupdetaildv").html('');
                $("#setupdetaildv").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
        $('#setup_detail_modal').modal('show');
    }

    function update_setup_details(setupDetailID,field) {
        var valu=$('#'+field+'_'+setupDetailID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {setupDetailID: setupDetailID,fieldnam:field,valu:valu},
            url: "<?php echo site_url('Codification/update_setup_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function confirmSetup(codificationSetupID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "You want to confirm",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'codificationSetupID': codificationSetupID},
                    url: "<?php echo site_url('Codification/confirmSetup'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if (data[0] == 's') {
                            Otablestup.draw()
                        }

                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            });
    }

    function assign_to_category(codificationSetupID) {
        $('#mainCategoryID').val('').change();
        $('#codificationSetupIDAssign').val(codificationSetupID);
        load_asn_cat_table();
        $('#setup_assign_modal').modal('show');
    }


    function load_subcategory(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        var codificationSetupID=$('#codificationSetupIDAssign').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Codification/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid,'codificationSetupID': codificationSetupID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function save_assigned_setup_detail() {
        var $form = $('#setup_assign_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Codification/save_assigned_setup_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#mainCategoryID').val('').change();
                    Otableasncat.draw()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_asn_cat_table(selectedID=null) {
        var codificationSetupID=$('#codificationSetupIDAssign').val();
        Otableasncat = $('#setup_assign_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Codification/load_asn_cat_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['itemCategoryID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "itemCategoryID"},
                {"mData": "codsetup"},
                {"mData": "mstrcategory"},
                {"mData": "subcategory"},
                {"mData": "setup_asn_action"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "codificationSetupID", "value": codificationSetupID});
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

    function delete_cat_asn(itemCategoryID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemCategoryID': itemCategoryID},
                    url: "<?php echo site_url('Codification/delete_cat_asn'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        if(data[0]=='s'){
                            Otableasncat.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    
    function item_codification_table_body() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Codification/item_codification_table_body'); ?>",
            data: {codificationSetupID: 0},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#item_codification_table_body").html('');
                $("#item_codification_table_body").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function editAttributeDetail(attributeDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'attributeDetailID': attributeDetailID},
            url: "<?php echo site_url('Codification/editAttributeDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attributeDetailID').val(attributeDetailID);
                $('#detailDescription').val(data['detailDescription']);
                $('#comment').val(data['comment']);
                $('#masterIDDtl').val(data['masterID']);

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

</script>