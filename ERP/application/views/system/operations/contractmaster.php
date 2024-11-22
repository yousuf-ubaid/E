<?php
echo head_page('Contract', true);
$customer_arr = all_customer_drop(true);
$customer_drp = all_customer_drop();
$customer_arr_masterlevel = array('' => 'Select Customer');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$contract_type = all_contarct_types();
$gl_code_arr    = fetch_all_gl_codes();
$uom_arr    = all_umo_new_drop();
$location_arr    = op_location_drop();
$segment_arr    = fetch_segment(True);
?>
<div id="filter-panel" class="filter-panel" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></label><br>
            <?php echo form_dropdown('customerCode', $customer_arr, '', 'class="form-control select2" id="customerCode" onchange="Otable.draw()"'); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="opencontractmodel()"><i class="fa fa-plus"></i> Create Contract </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="contract_master" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 10%">Contract No</th>
            <th style="min-width: 15%">Customer Name</th>
            <th style="min-width: 10%">Department</th>
            <th style="min-width: 10%">Contract Type</th>
            <th style="min-width: 10%">Contract Start Date</th>
            <th style="min-width: 10%">Contract End Date</th>
            <th style="min-width: 7%">Contract Status</th>
            <th style="min-width: 5%">Confirmed</th>
            <th style="min-width: 5%">Approved</th>
            <th style="min-width: 9%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog"  id="contract_master_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Contract</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="contract_master_form"'); ?>
                <input type="hidden" id="contractUID" name="contractUID">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Contract Type <?php required_mark(); ?></label>
                        <?php echo form_dropdown('contractType', $contract_type, '', 'class="form-control select2" onchange="" id="contractType" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Customer Name <?php required_mark(); ?></label>
                        <?php echo form_dropdown('clientID', $customer_drp, '', 'class="form-control select2" id="clientID"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Contract/Quotation Ref # <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="ContractNumber" name="ContractNumber">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Department <?php required_mark(); ?></label>
                        <?php echo form_dropdown('ServiceLineCode', $segment_arr, '', 'class="form-control select2" onchange="" id="ServiceLineCode" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Contract Start Date <?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="ContStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="ContStartDate" class="form-control">
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="">Contract End Date <?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="ContEndDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="ContEndDate" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Contract Status <?php required_mark(); ?></label>
                        <select class="form-control" id="contractStatus" name="contractStatus">
                            <option value="1">Active</option>
                            <option value="2">Pending</option>
                            <option value="3">Expired</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Contract Currency ID <?php required_mark(); ?></label>
                        <?php echo form_dropdown('ContCurrencyID', $currency_arr, '', 'class="form-control select2" id="ContCurrencyID" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Contract Value <?php required_mark(); ?></label>
                        <input type="number" step="any" class="form-control " id="contValue" name="contValue">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="receivableAccount">Product GL Code <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('productGLCode', $gl_code_arr,'','class="form-control select2" id="productGLCode"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="receivableAccount">Service GL Code <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('serviceGLCode', $gl_code_arr,'','class="form-control select2" id="serviceGLCode"'); ?>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="contractmastesavebtn" onclick="save_contract()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="contract_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Contract Detail</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <button class="btn btn-primary pull-right" onclick="add_contract_detail_modal()">Add Contract Detail</button>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="contract_detail" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 2%">#</th>
                            <th style="min-width: 15%">Client Ref</th>
                            <th style="min-width: 15%">Our Ref</th>
                            <th style="min-width: 15%">Client Item Description</th>
                            <th style="min-width: 15%">Type</th>
                            <th style="min-width: 15%">Unit</th>
                            <th style="min-width: 7%">Currency</th>
                            <th style="min-width: 7%">Product Rate</th>
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

<div aria-hidden="true" role="dialog"  id="contract_detail_add_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Contract Detail</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="contract_detail_form"'); ?>
                <input type="hidden" id="contractUIDhn" name="contractUID">
                <input type="hidden" id="productGLCodehn" name="productGLCode">
                <input type="hidden" id="serviceGLCodehn" name="serviceGLCode">
                <input type="hidden" id="ContractDetailID" name="ContractDetailID">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Client Reference <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="ClientRef" name="ClientRef">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Company Reference <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="OurRef" name="OurRef">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Item Description <?php required_mark(); ?></label>
                        <textarea name="ItemDescrip" class="form-control" id="ItemDescrip"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Type <?php required_mark(); ?></label>
                        <select class="form-control" id="TypeID" name="TypeID" onchange="selectGLCode(this)">
                            <option value="1">Product</option>
                            <option value="2">Service</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="receivableAccount">Unit Of Measure <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('UnitID', $uom_arr,'','class="form-control select2" id="UnitID"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Currency <?php required_mark(); ?></label>
                        <?php echo form_dropdown('RateCurrencyID', $currency_arr, '', 'class="form-control select2" disabled id="RateCurrencyID" '); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Product Rate <?php required_mark(); ?></label>
                        <input type="number" step="any" class="form-control " id="standardRate" name="standardRate">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="receivableAccount">GL Code <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('GLCode', $gl_code_arr,'','class="form-control select2" id="GLCode"'); ?>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary " onclick="save_contract_details()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>

<!--call off start-->
<div aria-hidden="true" role="dialog"  id="calloff_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Call Offs</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <button class="btn btn-primary pull-right" onclick="add_call_offs_modal()">Create New CallOffs</button>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="call_off_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 2%">#</th>
                            <th style="min-width: 15%">Description</th>
                            <th style="min-width: 15%">Created Date</th>
                            <th style="min-width: 15%">Expiry Date</th>
                            <th style="min-width: 15%">Location</th>
                            <th style="min-width: 15%">RDX</th>
                            <th style="min-width: 7%">Length</th>
                            <th style="min-width: 7%">Joints</th>
                            <th style="min-width: 7%">Well No</th>
                            <th style="min-width: 7%">Drawing No</th>
                            <th style="min-width: 7%">Completion</th>
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



<div aria-hidden="true" role="dialog"  id="calloff_add_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Call Off Detail</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="call_off_form"'); ?>
                <input type="hidden" id="contractUIDcalloff" name="contractUID">
                <input type="hidden" id="calloffID" name="calloffID">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="descriptioncalloff" name="description">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="receivableAccount">Unit Of Measure <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('unitOfMeasure', $uom_arr,'','class="form-control select2" id="unitOfMeasure"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Created Date <?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="createdDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="createdDate" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">Expired Date <?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="expiryDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="expiryDate" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Length <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="length" name="length">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Drawing No <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="drawingNo" name="drawingNo">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Location <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('location', $location_arr,'','class="form-control select2" id="location"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Well No <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="WellNo" name="WellNo">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Joints <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="joints" name="joints">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>RDX <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="RDX" name="RDX">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Product/Service <?php required_mark(); ?></label>
                        <select name="productId" id="productId" class="form-control searchbox">
                            <option value="">Select Product/Service</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Is Hold </label>
                        <select class="form-control" id="isHold" name="isHold" >
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary " onclick="save_calloff()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    var OtableD;
    var calloftbl;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operations/contractmaster', '', 'Contract');
        });
        contract_master_table();
        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#cn_form').bootstrapValidator('revalidateField', 'cnDate');
        });
    });


    function contract_master_table(selectedID=null) {
        Otable = $('#contract_master').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/fetch_contract_master_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['contractUID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "contractUID"},
                {"mData": "ContractNumber"},
                {"mData": "customerName"},
                {"mData": "Department"},
                {"mData": "conType"},
                {"mData": "ContStartDate"},
                {"mData": "ContEndDate"},
                {"mData": "contractStatuss"},
                {"mData": "confirmed"},
                {"mData": "contractApproved"},
                {"mData": "edit"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
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

    function opencontractmodel() {
        $('#contract_master_form')[0].reset();
        $('#contractUID').val('');
        $('#contractType').val('').change();
        $('#clientID').val('').change();
        $('#ServiceLineCode').val('').change();
        $('#ContCurrencyID').val('').change();
        $('#productGLCode').val('').change();
        $('#serviceGLCode').val('').change();
        $('#contract_master_modal').modal('show');
    }

    function save_contract() {
        var $form = $('#contract_master_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Operation/save_contract_master'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#contract_master_modal').modal('hide');
                    $('#contractUID').val('');
                    Otable.draw()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function openEditContract(contractUID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractUID': contractUID},
            url: "<?php echo site_url('Operation/get_contract_master_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['DetailExsist']==1) {
                    $('#contractmastesavebtn').attr("disabled",true);
                }else{
                    $('#contractmastesavebtn').attr("disabled",false);
                }
                $('#contractUID').val(contractUID);
                $('#contractType').val(data['contractType']).change();
                $('#clientID').val(data['clientID']).change();
                $('#ContractNumber').val(data['ContractNumber']);
                $('#ServiceLineCode').val(data['ServiceLineCode']).change();
                $('#ContStartDate').val(data['ContStartDate']);
                $('#ContEndDate').val(data['ContEndDate']);
                $('#contractStatus').val(data['contractStatus']);
                $('#ContCurrencyID').val(data['ContCurrencyID']).change();
                $('#productGLCode').val(data['productGLCode']).change();
                $('#serviceGLCode').val(data['serviceGLCode']).change();
                $('#contValue').val(data['contValue']);
                $('#contract_master_modal').modal('show');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function openOPDetail(contractUID,productGLCode,serviceGLCode) {
        loard_contract_detail_tbl(contractUID);
        $('#contractUIDhn').val(contractUID);
        $('#productGLCodehn').val(productGLCode);
        $('#serviceGLCodehn').val(serviceGLCode);
        $('#GLCode').val(productGLCode).change();
        $('#contract_detail_modal').modal('show');
    }

    function loard_contract_detail_tbl(contractUID,selectedID=null) {
        OtableD = $('#contract_detail').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/fetch_contract_detail_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['ContractDetailID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "ContractDetailID"},
                {"mData": "ClientRef"},
                {"mData": "OurRef"},
                {"mData": "ItemDescrip"},
                {"mData": "contractDType"},
                {"mData": "UnitDes"},
                {"mData": "CurrencyCode"},
                {"mData": "standardRate"},
                {"mData": "editConDetail"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractUID", "value": contractUID});
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

    function add_contract_detail_modal() {
        $('#ContractDetailID').val('');
        $('#UnitID').val('').change();
        $('#RateCurrencyID').val('').change();
        $('#contract_detail_form')[0].reset();
        selectGLCode();
        select_master_currency();
        $('#contract_detail_add_modal').modal('show');
    }
    
    function selectGLCode() {
        if($('#TypeID').val()==1){
            $('#GLCode').val($('#productGLCodehn').val()).change();
        }else if($('#TypeID').val()==2){
            $('#GLCode').val($('#serviceGLCodehn').val()).change();
        }
    }

    function save_contract_details() {
        $('#RateCurrencyID').attr('disabled',false);
        var $form = $('#contract_detail_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Operation/save_contract_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#RateCurrencyID').attr('disabled',true);
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#contract_detail_add_modal').modal('hide');
                    Otable.draw();
                    OtableD.draw();
                }
            }, error: function () {
                $('#RateCurrencyID').attr('disabled',true);
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function openEditContractDetail(ContractDetailID) {
        $('#ContractDetailID').val(ContractDetailID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'ContractDetailID': ContractDetailID},
            url: "<?php echo site_url('Operation/get_contract_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#ClientRef').val(data['ClientRef']);
                $('#OurRef').val(data['OurRef']);
                $('#ItemDescrip').val(data['ItemDescrip']);
                $('#TypeID').val(data['TypeID']);
                $('#UnitID').val(data['UnitID']).change();
                $('#RateCurrencyID').val(data['RateCurrencyID']).change();
                $('#ContCurrencyID').val(data['ContCurrencyID']).change();
                $('#standardRate').val(data['standardRate']);
                $('#GLCode').val(data['GLCode']).change();
                $('#contract_detail_add_modal').modal('show');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function deleteContractDetail(ContractDetailID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'ContractDetailID': ContractDetailID},
                    url: "<?php echo site_url('Operation/delete_contrct_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        OtableD.draw()
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function confirmOpContract(contractUID) {
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
                    data: {'contractUID': contractUID},
                    url: "<?php echo site_url('Operation/confirm_opcontrct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            myAlert('e', data['message']);
                        } else if (data['error'] == 2) {
                            myAlert('w', data['message']);
                        }
                        else {
                            Otable.draw()
                            myAlert('s', data['message']);
                            refreshNotifications(true);
                        }

                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function openOPcalloff(contractUID,productGLCode,serviceGLCode) {
        load_calloff_tbl(contractUID);
        load_contract_detail_drop(contractUID);
        $('#contractUIDcalloff').val(contractUID);
        $('#calloff_modal').modal('show');
    }

    function load_calloff_tbl(contractUID,selectedID=null) {
        calloftbl = $('#call_off_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_calloff_tbl'); ?>",
            "aaSorting": [[2, 'desc']],
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
                    if (parseInt(oSettings.aoData[x]._aData['calloffID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "calloffID"},
                {"mData": "description"},
                {"mData": "createdDate"},
                {"mData": "expiryDate"},
                {"mData": "fieldName"},
                {"mData": "RDX"},
                {"mData": "length"},
                {"mData": "joints"},
                {"mData": "WellNo"},
                {"mData": "drawingNo"},
                {"mData": "calloffprogress"},
                {"mData": "callofactn"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractUID", "value": contractUID});
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

    function load_contract_detail_drop(contractUID) {
        $('#productId option').remove();
        $('#productId').val("");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/loard_contract_detail_drop"); ?>',
            dataType: 'json',
            data: {'contractUID': contractUID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#productId').empty();
                    var mySelect = $('#productId');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['ContractDetailID']).html(text['ItemDescrip']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function add_call_offs_modal() {
        $('#calloff_add_modal').modal('show');
        $('#calloffID').val('');
        $('#unitOfMeasure').val('').change();
        $('#location').val('').change();
        $('#productId').val('');
        $('#call_off_form')[0].reset();
    }


    function save_calloff() {
        var $form = $('#call_off_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Operation/save_calloff'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#calloff_add_modal').modal('hide');
                    calloftbl.draw();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function openEditcalloff(calloffID) {
        $('#calloffID').val(calloffID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'calloffID': calloffID},
            url: "<?php echo site_url('Operation/get_call_off_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#descriptioncalloff').val(data['description']);
                $('#productId').val(data['productId']);
                $('#location').val(data['location']).change();
                $('#length').val(data['length']);
                $('#RDX').val(data['RDX']);
                $('#WellNo').val(data['WellNo']);
                $('#drawingNo').val(data['drawingNo']);
                $('#joints').val(data['joints']);
                $('#unitOfMeasure').val(data['unitOfMeasure']).change();
                $('#isHold').val(data['isHold']).change();
                $('#createdDate').val(data['createdDate']);
                $('#expiryDate').val(data['expiryDate']);
                $('#calloff_add_modal').modal('show');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function deleteCallOff(calloffID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'calloffID': calloffID},
                    url: "<?php echo site_url('Operation/deleteCallOff'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        calloftbl.draw()
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_op_contract(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'contractUID': id},
                    url: "<?php echo site_url('Operation/referback_contract_op'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function select_master_currency() {
        var contractUIDhn=$('#contractUIDhn').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractUID': contractUIDhn},
            url: "<?php echo site_url('Operation/select_master_currency'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#RateCurrencyID').val(data['ContCurrencyID']).change();
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
</script>