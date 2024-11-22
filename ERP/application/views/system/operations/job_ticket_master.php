<?php
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$contractUIDTicket=$_POST['page_id'];
$this->load->helpers('operation');
$contractDetl=get_contract_detail($contractUIDTicket);
$get_calloff_arr=get_call_off_for_jobticket($contractUIDTicket);
$location_arr    = op_location_drop();
$get_job_eng    = job_eng_drop();
$emp_arr   = load_employee_drop_tkt();
$crew_grp_arr   = load_employee_crew_grp_tkt();
$asset_arr   = load_asset_unit_drop_tkt();
$current_date = current_format_date();
?>
<input type="hidden" id="contractUIDTicket" name="contractUID" value="<?php echo $_POST['page_id']; ?>">
<input type="hidden" id="defCompDecimal" name="defCompDecimal" value="<?php echo $this->common_data['company_data']['company_default_decimal']; ?>">
<div id="filter-panel" class="filter-panel" xmlns="http://www.w3.org/1999/html">
    <div class="row">
        <div class="form-group col-sm-4">
            <label> Customer :</label>
            <span> <?php echo $contractDetl['customerSystemCode'] ?>|<?php echo $contractDetl['customerName'] ?>|<?php echo $contractDetl['customerCountry'] ?></span>
        </div>
        <div class="form-group col-sm-4">
            <label> Contract No :</label>
            <span ><?php echo $contractDetl['ContractNumber'] ?></span>

        </div>
        <div class="form-group col-sm-4">
            <label> Department :</label>
            <span> <?php echo $contractDetl['Department'] ?></span>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label> Contract Start Date :</label>
            <span> <?php echo $contractDetl['ContStartDate'] ?></span>
        </div>
        <div class="form-group col-sm-4">
            <label> Contract End Date :</label>
            <span> <?php echo $contractDetl['ContEndDate'] ?></span>
        </div>
        <div class="form-group col-sm-4">
            <label> Contract Status :</label>
            <?php
            $status = '';
            if ($contractDetl['contractStatus'] == 3) {
                $status .= '<span class="label label-danger">Expired</span>';
            } else if ($contractDetl['contractStatus'] == 1) {
                $status .= '<span class="label label-success">Active</span>';
            } elseif ($contractDetl['contractStatus'] == 2) {
                $status .= '<span class="label label-warning">Pending</span>';
            } else {
                $status .= '-';
            }
            ?>
             <?php echo $status; ?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label> Contract Type :</label>
            <span> <?php echo $contractDetl['conType'] ?></span>
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
        <button type="button" class="btn btn-primary pull-right" onclick="openjobmodel()"><i class="fa fa-plus"></i> Add New Job</button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="ticket_master" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 7%">Job No</th>
            <th style="min-width: 10%">Job Created Date</th>
            <th style="min-width: 15%">Description</th>
            <th style="min-width: 7%">Location</th>
            <th style="min-width: 7%">Well No</th>
            <th style="min-width: 10%">Total</th>
            <th style="min-width: 9%">Confirmed</th>
            <th style="min-width: 9%">Approved</th>
            <th style="min-width: 15%">Action</th>
        </tr>
        </thead>
    </table>
</div>




<div aria-hidden="true" role="dialog"  id="job_master_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Job</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="job_form"'); ?>
                <input type="hidden" id="contractUIDjob" name="contractUID" value="<?php echo $_POST['page_id']; ?>">
                <input type="hidden" id="ticketidAtuto" name="ticketidAtuto">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Calloff Base Y/N <?php required_mark(); ?></label>
                        <select name="callOffBase" id="callOffBase" onchange="check_calloffYN()" class="form-control">
                            <option selected  value="1">Yes</option>
                            <option  value="2">No</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Client <?php required_mark(); ?></label>
                        <select name="clientID" id="clientID" class="form-control">
                            <option selected value="<?php echo $contractDetl['clientID'] ?>"><?php echo $contractDetl['customerName'] ?></option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Contract Ref No <?php required_mark(); ?></label>
                        <input type="text" class="form-control" value="<?php echo $contractDetl['ContractNumber'] ?>" id="contractRefNo" name="contractRefNo" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="supplierPrimaryCode">Network Ref #/PO No <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="jobNetworkNo" name="jobNetworkNo">
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Location <?php required_mark(); ?></label>
                        <?php  echo form_dropdown('primaryUnitAssetID', $location_arr,'','class="form-control select2" id="primaryUnitAssetID"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Well No (Line No) <?php required_mark(); ?></label>
                        <!--<input type="text" class="form-control " id="wellNo1" name="wellNo1" readonly>-->
                        <input type="text" class="form-control " id="wellNo" name="wellNo" >
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Supervisor <?php required_mark(); ?></label>
                        <?php echo form_dropdown('EngID', $get_job_eng, '', 'class="form-control select2" id="EngID" '); ?>
                    </div>
                    <div class="form-group col-sm-4 calof">
                        <label>Call Offs <?php required_mark(); ?></label>
                        <?php echo form_dropdown('calloffID', $get_calloff_arr, '', 'class="form-control select2" id="calloffID" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Job Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control " id="comments" name="comments">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Department <?php required_mark(); ?></label>
                        <select name="serviceLine" id="serviceLine" class="form-control">
                            <option selected value="<?php echo $contractDetl['ServiceLineCode'] ?>"><?php echo $contractDetl['Department'] ?></option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Operation Log Y/N <?php required_mark(); ?></label>
                        <select name="operationLog" id="operationLog" class="form-control">
                            <option selected value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="contractmastesavebtn" onclick="save_job()">Save</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>



<div aria-hidden="true" role="dialog"  id="job_master_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Edit Job</h5>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="jobTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="jobDetailtab" data-toggle="tab" href="#jobdetails" role="tab" aria-controls="home" aria-selected="false">Job Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#serviceprodcut" role="tab" aria-controls="profile" aria-selected="false">Service/Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#txtfinance" role="tab" onclick="load_txt_invoice()" aria-controls="profile" aria-selected="false">Finance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#txtmi" role="tab" aria-controls="profile" aria-selected="false">Material Issues</a>
                    </li>
                </ul>
                <div class="tab-content" id="TabContent">
                    <div class="tab-pane fade" id="jobdetails" role="tabpanel" aria-labelledby="home-tab">
                        <?php echo form_open('', 'role="form" id="job_edit_form"'); ?>
                        <input type="hidden" id="ticketidAtutoEdit" name="ticketidAtuto">
                        <input type="hidden" id="isConfirmendEdit" name="isConfirmendEdit">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Calloff Base Y/N <?php required_mark(); ?></label>
                                <select name="callOffBase" id="callOffBaseEdit" onchange="check_calloffYN_edit(),update_jobDetail(this,'callOffBase')"  class="form-control isConfirmend">
                                    <option value="1">Yes</option>
                                    <option selected value="2">No</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Supervisor <?php required_mark(); ?></label>
                                <?php echo form_dropdown('EngID', $get_job_eng, '', 'class="form-control select2 isConfirmend" onchange="update_jobDetail(this,\'EngID\')" id="EngIDedit" '); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Location <?php required_mark(); ?></label>
                                <?php  echo form_dropdown('primaryUnitAssetID', $location_arr,'','class="form-control select2 isConfirmend" onchange="update_jobDetail(this,\'primaryUnitAssetID\')" id="primaryUnitAssetIDedit"'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Well No (Line No) <?php required_mark(); ?></label>
                                <!--<input type="text" class="form-control " id="wellNo1" name="wellNo1" readonly>-->
                                <input type="text" class="form-control isConfirmend" onchange="update_jobDetail(this,'wellNo')" id="wellNoedit" name="wellNo" >
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Job Description <?php required_mark(); ?></label>
                                <input type="text" class="form-control isConfirmend" onchange="update_jobDetail(this,'comments')" id="commentsedit" name="comments">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Department <?php required_mark(); ?></label>
                                <select name="serviceLine" id="serviceLineedit" class="form-control isConfirmend">
                                    <option selected value="<?php echo $contractDetl['ServiceLineCode'] ?>"><?php echo $contractDetl['Department'] ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4 calof">
                                <label>Call Offs <?php required_mark(); ?></label>
                                <?php echo form_dropdown('calloffID', $get_calloff_arr, '', 'class="form-control select2 isConfirmend" onchange="update_jobDetail(this,\'calloffID\')" id="calloffIDedit" '); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Length </label>
                                <input type="text" class="form-control  isConfirmend" id="lengthedit" onchange="update_jobDetail(this,'length')" readonly name="length">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Percentage Comp.% </label>
                                <input type="text" class="form-control  isConfirmend" id="percentageCompletionedit" name="percentageCompletion" onchange="update_jobDetail(this,'percentageCompletion')">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Submission Date </label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="submissionDate" onchange="update_jobDetail(this,'submissionDate')" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="submissionDateedit" class="form-control isConfirmend">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Batch No </label>
                                <input type="text" class="form-control  isConfirmend" id="batchNoedit" onchange="update_jobDetail(this,'batchNo')" name="batchNo">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Estimated Service Value </label>
                                <input type="text" class="form-control  isConfirmend" id="estimatedServiceValueedit" onchange="update_jobDetail(this,'estimatedServiceValue')" name="estimatedServiceValue">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Estimated Product Value </label>
                                <input type="text" class="form-control  isConfirmend" id="estimatedProductValueedit" onchange="update_jobDetail(this,'estimatedProductValue')" name="estimatedProductValue">
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Rev. Year </label>
                                <input type="text" class="form-control  isConfirmend" id="revenueYearedit"  name="revenueYear" readonly>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Rev. Month </label>
                                <input type="text" class="form-control  isConfirmend" id="revenueMonthedit"  name="revenueMonth" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Operation Log Y/N <?php required_mark(); ?></label>
                                <select name="operationLog" id="operationLogEdit" class="form-control isConfirmend" onchange="update_jobDetail(this,'operationLog')">
                                    <option selected value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                        </div>

                        </form>

                        <!--<button class="btn btn-primary" id="contractmastesavebtn" onclick="update_job()">Save</button>-->
                    </div>
                    <div class="tab-pane fade" id="serviceprodcut" role="tabpanel" aria-labelledby="profile-tab">
                        <h4>Product</h4>
                        <div class="table-responsive">
                            <table id="product_table" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 2%">Date</th>
                                    <th style="min-width: 15%">Client Ref</th>
                                    <th style="min-width: 10%">Description</th>
                                    <th style="min-width: 10%">Comments</th>
                                    <th style="min-width: 10%">Unit</th>
                                    <th style="min-width: 15%">Unit Rate</th>
                                    <th style="min-width: 7%">%per</th>
                                    <th style="min-width: 5%">Qty</th>
                                    <th style="min-width: 9%">Dis/Unit</th>
                                    <th style="min-width: 9%">Amount</th>
                                    <th style="min-width: 9%"><button class="btn btn-primary btn-xs isConfirmend" onclick="openproductModel()"><i class="fa fa-plus"></i></button></th>
                                </tr>
                                </thead>
                                <tbody id="product_table_body">

                                </tbody>
                            </table>
                        </div>

                        <br>

                        <h4>Service</h4>
                        <div class="table-responsive">
                            <table id="service_table" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 2%">Date</th>
                                    <th style="min-width: 15%">Client Ref</th>
                                    <th style="min-width: 10%">Description</th>
                                    <th style="min-width: 10%">Comments</th>
                                    <th style="min-width: 10%">Unit</th>
                                    <th style="min-width: 15%">Unit Rate</th>
                                    <th style="min-width: 7%">%per</th>
                                    <th style="min-width: 5%">Qty</th>
                                    <th style="min-width: 9%">Dis/Unit</th>
                                    <th style="min-width: 9%">Amount</th>
                                    <th style="min-width: 9%"><button class="btn btn-primary btn-xs isConfirmend" onclick="openServiceModel()"><i class="fa fa-plus"></i></button></th>
                                </tr>
                                </thead>
                                <tbody id="service_table_body">

                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-9">
                            </div>
                            <div class="col-md-3 pull-right">
                                <input type="hidden" id="producttothn" name="producttothn">
                                <input type="hidden" id="servicetothn" name="servicetothn">
                                <table>
                                    <tbody>
                                    <tr>
                                        <th style="width:40%;">Product Total</th>
                                        <td>:</td>
                                        <td id="producttot">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th style="width:40%;">Service Total</th>
                                        <td>:</td>
                                        <td id="servicetot">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th style="width:40%;">Grand Total</th>
                                        <td>:</td>
                                        <td id="grandtot">&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="tab-pane fade" id="txtfinance" role="tabpanel" aria-labelledby="home-tab">
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table id="service_table" class="<?php echo table_class(); ?>">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 2%">#</th>
                                            <th style="min-width: 15%">Invoice No</th>
                                            <th style="min-width: 10%">Invoice Posted Date</th>

                                        </tr>
                                        </thead>
                                        <tbody id="op_tkt_invoice_table_body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="txtmi" role="tabpanel" aria-labelledby="home-tab">

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>



<div aria-hidden="true" role="dialog"  id="job_service_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Service</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="service_add_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Client Ref</th>
                            <th style="min-width: 20%">Description</th>
                            <th style="min-width: 15%">Amount</th>
                            <th style="min-width: 10%">Units</th>
                            <th style="min-width: 10%">Currency</th>
                            <!--<th style="min-width: 7%">%Last YN</th>-->
                            <th style="min-width: 9%">&nbsp;</th>
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


<div aria-hidden="true" role="dialog"  id="job_product_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Product</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="product_add_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Client Ref</th>
                            <th style="min-width: 20%">Description</th>
                            <th style="min-width: 15%">Amount</th>
                            <th style="min-width: 10%">Units</th>
                            <th style="min-width: 10%">Currency</th>
                            <th style="min-width: 7%">Last YN</th>
                            <th style="min-width: 9%">&nbsp;</th>
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




<div aria-hidden="true" role="dialog"  id="operation_log_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Operation System Log</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="operation_log_ticketid">
                    <div class="col-md-12" >
                        <table class=" table-bordered">
                            <tbody>
                            <tr>
                                <th ><strong>PO Number</strong></th>
                                <td id="poNumberhd"></td>
                                <th ><strong>Well Number</strong></th>
                                <td id="wellNohd"></td>
                                <th ><strong>Length</strong></th>
                                <td id="lengthhd"></td>
                                <th ><strong>Job Starting Date</strong></th>
                                <td id="Timedatejobstrahd"></td>
                            </tr>
                            <tr>
                                <th><strong>Job Number</strong></th>
                                <td id="ticketNohd"></td>
                                <th><strong>Location</strong></th>
                                <td id="fieldNamehd"> </td>
                                <th><strong>RDX</strong></th>
                                <td id="RDXhd"></td>
                                <th><strong>Job End Date</strong></th>
                                <td id="Timedatejobendhd"></td>
                            </tr>
                            <tr>
                                <th><strong>Client</strong></th>
                                <td id="CustomerNamehd"></td>
                                <th><strong>Drawing Number</strong></th>
                                <td id="drawingNohd"></td>
                                <th><strong>Total Joints</strong></th>
                                <td id="jointshd"></td>
                                <th><strong>Expected End Date</strong></th>
                                <td><div class="input-group datepicshd">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="expectedEndDate" value="" id="expectedEndDatehd" class="form-control">
                                    </div></td>
                            </tr>
                            <tr>
                                <th><strong>Activity</strong></th>
                                <td colspan="3"><input type="text" onchange="updatehdFields('activityhd');" class="form-control" value=""  name="activity" id="activityhd" ></td>
                                <th colspan="2"><strong>Product/Service</strong></th>

                                <td><select name="productId" id="productIdhd" onchange="updatehdFields('productIdhd');" class="form-control searchbox">
                                        <option value="">Select Product/Service</option>
                                    </select></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <br>
                <br>
                <div class="row">
                    <div class="form-group col-sm-5">

                    </div>
                    <div class="form-group col-sm-6">
                        <?php  echo form_dropdown('crewEmpID', $emp_arr,'','class="form-control select2" id="crewEmpID"'); ?>
                    </div>
                    <div class="form-group col-sm-1">
                        <button class="btn btn-primary btn-xs isConfirmend" type="button" onclick="addCrewRow()"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="operation_log_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">Date</th>
                            <th style="min-width: 20%">Crew</th>
                            <th style="min-width: 15%">Joints</th>
                            <th style="min-width: 10%">TIE IN</th>
                            <th style="min-width: 10%">Stragiht</th>
                            <th style="min-width: 10%">Completion (meters)</th>
                            <th style="min-width: 10%">Remarks</th>
                            <th style="min-width: 10%">Joints %</th>
                            <th style="min-width: 5%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="operation_log_body">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="op_crew_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 35%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Crew</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="crewticketidAtuto" name="ticketidAtuto">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-5">

                        </div>
                        <div class="form-group col-md-6">
                            <?php  echo form_dropdown('ticketCrewEmp', $crew_grp_arr,'','class="form-control select2" id="ticketCrewEmp"'); ?>
                        </div>
                        <div class="form-group col-md-1">
                            <button class="btn btn-primary btn-xs" type="button" onclick="check_employee_attach_expired()"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table id="op_crew_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Employee Name</th>
                                <th style="min-width: 5%"><button class="btn btn-primary btn-xs" type="button" onclick="CrewHistory()">History</button></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="op_crew_history_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Crew History</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="op_crew_history_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Employee Name</th>
                                <th style="min-width: 5%">Description</th>
                                <th style="min-width: 5%">Action Done By</th>
                                <th style="min-width: 5%">Action Date</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog"  id="op_unit_history_modal" class="modal fade" style="z-index: 10000;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Asset Unit History</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="op_asset_unit_history_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Asset Unit</th>
                                <th style="min-width: 5%">Description</th>
                                <th style="min-width: 5%">Action Done By</th>
                                <th style="min-width: 5%">Action Date</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>




<div aria-hidden="true" role="dialog"  id="op_unit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Asset Units</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="unitticketidAtuto" name="ticketidAtuto">

                <div class="row">
                    <div class="form-group col-sm-1">

                    </div>
                    <div class="form-group col-sm-6">
                        <?php  echo form_dropdown('faID', $asset_arr,'','class="form-control select2" id="faID"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="unitComment"  name="Comment">
                    </div>
                    <div class="form-group col-sm-1">
                        <button class="btn btn-primary btn-xs" type="button" onclick="check_asset_attach_expired()"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table id="op_asset_unit_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Asset Unit</th>
                                <th style="min-width: 10%">Comment</th>
                                <th style="min-width: 5%"><button class="btn btn-primary btn-xs" type="button" onclick="assetUnitHistory()">History</button></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>






<div aria-hidden="true" role="dialog"  id="opdoc_expiry_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Following employees has documents which has been expired or nearing to expire</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table id="opdoc_expiry_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Employee</th>
                                <th style="min-width: 10%">Document</th>
                                <th style="min-width: 10%">Description</th>
                                <th style="min-width: 10%">Days Remaining</th>
                            </tr>
                            </thead>
                            <tbody id="opdoc_expiry_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="addCrewop()" class="btn btn-primary" type="button">Add</button>
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    var OtableServce;
    var OtableProduct;
    var OtableCrew;
    var OtableCrewHis;
    var OtableAssetunit;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operations/contractmaster', '', 'Contract');
        });
        load_job_ticket_table();
        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            update_jobDetail(this,'submissionDate')
        });

        $('.datepics').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            loadproductserviceTable()
        });

        $('.datepicshd').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            updatehdFields('expectedEndDatehd')
        });
    });


    function openjobmodel() {
        $('#job_master_modal').modal('show');
        $('#job_form')[0].reset();
        $('#primaryUnitAssetID').val('').change();
        $('#calloffID').val('').change();
    }

    function save_job() {
        var $form = $('#job_form');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Operation/save_job_master'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#job_master_modal').modal('hide');
                    $('#ticketidAtuto').val('');
                    Otable.draw()
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function check_calloffYN() {
        var callOffBase=$('#callOffBase').val();
        if(callOffBase==1){
            $('.calof').show()
        }else{
            $('.calof').hide()
        }
    }

    function check_calloffYN_edit() {
        var callOffBase=$('#callOffBaseEdit').val();
        if(callOffBase==1){
            $('.calof').show()
        }else{
            $('.calof').hide()
        }
    }
    
    function load_job_ticket_table(selectedID=null) {
        Otable = $('#ticket_master').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_job_ticket_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['ticketidAtuto']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "ticketidAtuto"},
                {"mData": "ticketNo"},
                {"mData": "createdDate"},
                {"mData": "comments"},
                {"mData": "fieldName"},
                {"mData": "wellNo"},
                {"mData": "total_prod_servc"},
                {"mData": "confirmed"},
                {"mData": "ticketApproved"},
                {"mData": "job_ticket_action"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractUID", "value": $("#contractUIDjob").val()});
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

    function openEditjobticket(ticketidAtuto) {
        $('#job_master_edit_modal').modal('show');
        $('#jobDetailtab').click();
        load_ticket_edit(ticketidAtuto);
        loadproductserviceTable();
    }

    function deletejobticket(ticketidAtuto) {
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
                    data: {'ticketidAtuto': ticketidAtuto},
                    url: "<?php echo site_url('Operation/deletejobticket'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        stopLoad();
                        Otable.draw()
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_ticket_edit(ticketidAtuto) {
        $('#ticketidAtutoEdit').val(ticketidAtuto);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto},
            url: "<?php echo site_url('Operation/load_ticket_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#callOffBaseEdit').val(data['callOffBase']);
                $('#operationLogEdit').val(data['operationLog']);
                $('#EngIDedit').val(data['EngID']).change();
                $('#primaryUnitAssetIDedit').val(data['primaryUnitAssetID']).change();
                $('#wellNoedit').val(data['wellNo']);
                $('#commentsedit').val(data['comments']);
                $('#serviceLineedit').val(data['serviceLine']);
                $('#calloffIDedit').val(data['calloffID']).change();
                $('#lengthedit').val(data['completionMeters']);
                $('#percentageCompletionedit').val(data['percentageCompletion']);
                $('#submissionDateedit').val(data['submissionDat']);
                $('#batchNoedit').val(data['batchNo']);
                $('#estimatedServiceValueedit').val(data['estimatedServiceValue']);
                $('#estimatedProductValueedit').val(data['estimatedProductValue']);
                $('#revenueYearedit').val(data['revenueYear']);
                $('#revenueMonthedit').val(data['revenueMonth']);
                $('#isConfirmendEdit').val(data['confirmedYN']);
                if(data['confirmedYN']==1){
                    $('.isConfirmend').attr('disabled',true);
                }else{
                    $('.isConfirmend').attr('disabled',false);
                }
                check_calloffYN_edit()
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function update_jobDetail(dt,fieldName) {
        var vlu=$(dt).val();
        if(fieldName=='submissionDate'){
            vlu= $('#submissionDateedit').val();
        }
       var ticketidAtuto= $('#ticketidAtutoEdit').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto,'valu': vlu,'fieldName': fieldName},
            url: "<?php echo site_url('Operation/update_jobDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                Otable.draw();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function loadproductserviceTable() {
        seriviceLoad();
        productLoad();
        setTimeout(function(){  loadGrandTot(); }, 500);

    }

    function seriviceLoad() {

       var contractUID = $('#contractUIDTicket').val();
       var ticketidAtuto = $('#ticketidAtutoEdit').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractUID':contractUID,'ticketidAtuto':ticketidAtuto,'typeid':2},
            url: "<?php echo site_url('Operation/load_ticket_service'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#service_table_body').empty();
                x = 1;
                var sertot=0;
                if (jQuery.isEmptyObject(data)) {
                    $('#service_table_body').append('<tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    $('#servicetot').html(parseFloat(sertot).toFixed(defCompDecimal));
                    $('#servicetothn').val(sertot);
                }else{
                    $.each(data, function (key, value) {
                        $('#service_table_body').append('<tr><td><input type="text" name="addedDate" id="addedDate_' + value['TicketproductID'] + '" class="form-control addedDate isConfirmend" value="' + value['addedDat'] + '"></td><td>' + value['clientReference'] + '</td><td>' + value['Description'] + '</td><td><input type="text" name="comments" class="form-control isConfirmend" value="' + value['comments'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'comments\',this)"></td><td>' + value['UnitShortCode'] + '</td><td>' + value['UnitRate'] + '</td><td><input type="text" name="percentage" class="form-control isConfirmend" value="' + value['percentage'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'percentage\',this)"></td> <td><input type="text" name="Qty" class="form-control isConfirmend" value="' + value['Qty'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'Qty\',this)"></td> <td><input type="text" name="discount isConfirmend" class="form-control" value="' + value['discount'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'discount\',this)"></td> <td>' + value['TotalCharges'] + '</td> <td class="text-right"> <button onclick="delete_op_product_service(' + value['TicketproductID'] + ');" class="isConfirmend"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash isConfirmend"></span></button></td></tr>');
                        x++;
                        sertot=parseFloat(sertot)+parseFloat(value['TotalCharges']);
                    });
                    $('#servicetot').html(parseFloat(sertot).toFixed(defCompDecimal));
                    $('#servicetothn').val(sertot);
                    //$('.startedDated').datetimepicker();
                    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
                    $('.addedDate').datetimepicker({
                        useCurrent: false,
                        format: date_format_policy,
                    }).on('dp.change', function (ev) {
                        update_op_product_service(0,'addedDate',this)
                    });
                }

                setTimeout(function(){
                    if($('#isConfirmendEdit').val()==1){
                    $('.isConfirmend').attr('disabled',true);
                    }else{
                        $('.isConfirmend').attr('disabled',false);
                    }
                }, 200);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function productLoad() {
        var contractUID = $('#contractUIDTicket').val();
        var ticketidAtuto = $('#ticketidAtutoEdit').val();
        var defCompDecimal = $('#defCompDecimal').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractUID':contractUID,'ticketidAtuto':ticketidAtuto,'typeid':1},
            url: "<?php echo site_url('Operation/load_ticket_service'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#product_table_body').empty();
                y = 1;
                var prdtot=0;
                if (jQuery.isEmptyObject(defCompDecimal)) {
                    defCompDecimal=3;
                }
                if (jQuery.isEmptyObject(data)) {
                    $('#product_table_body').append('<tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                    $('#producttot').html(parseFloat(prdtot).toFixed(defCompDecimal));
                    $('#producttothn').val(prdtot);
                }else{
                    $.each(data, function (key, value) {
                        $('#product_table_body').append('<tr><td><input type="text" name="addedDate" id="addedDate_' + value['TicketproductID'] + '" class="form-control addedDate isConfirmend" value="' + value['addedDat'] + '"></td><td>' + value['clientReference'] + '</td><td>' + value['Description'] + '</td><td><input type="text" name="comments" class="form-control isConfirmend" value="' + value['comments'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'comments\',this)"></td><td>' + value['UnitShortCode'] + '</td><td>' + value['UnitRate'] + '</td><td><input type="text" name="percentage" class="form-control isConfirmend" value="' + parseFloat(value['percentage']).toFixed(2) + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'percentage\',this)"></td> <td><input type="text" name="Qty" class="form-control isConfirmend" value="' + value['Qty'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'Qty\',this)"></td> <td><input type="text" name="discount" class="form-control isConfirmend" value="' + value['discount'] + '" onchange="update_op_product_service(' + value['TicketproductID'] + ',\'discount\',this)"></td> <td>' + parseFloat(value['TotalCharges']).toFixed(defCompDecimal) + '</td> <td class="text-right"> <button onclick="delete_op_product_service(' + value['TicketproductID'] + ');" class="isConfirmend"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></button></td></tr>');
                        y++;
                        prdtot=parseFloat(prdtot)+parseFloat(value['TotalCharges']);
                    });
                    $('#producttot').html(parseFloat(prdtot).toFixed(defCompDecimal));
                    $('#producttothn').val(prdtot);
                    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
                    $('.addedDate').datetimepicker({
                        useCurrent: false,
                        format: date_format_policy,
                    }).on('dp.change', function (ev) {
                        update_op_product_service(0,'addedDate',this)
                    });
                }

                setTimeout(function(){
                    if($('#isConfirmendEdit').val()==1){
                        $('.isConfirmend').attr('disabled',true);
                    }else{
                        $('.isConfirmend').attr('disabled',false);
                    }
                }, 200);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function openServiceModel() {
        $('#job_service_modal').modal('show');
        load_service_add_table();
    }

    function openproductModel() {
        $('#job_product_modal').modal('show');
        load_product_add_table();
    }


    function load_service_add_table(selectedID=null) {
        OtableServce = $('#service_add_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_service_add_table'); ?>",
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
                {"mData": "ItemDescrip"},
                {"mData": "standardRate"},
                {"mData": "UnitDes"},
                {"mData": "CurrencyCode"},
                {"mData": "service_action_add"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractUID", "value": $("#contractUIDjob").val()});
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


    function load_product_add_table(selectedID=null) {
        OtableProduct = $('#product_add_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_product_add_table'); ?>",
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
                {"mData": "ItemDescrip"},
                {"mData": "standardRate"},
                {"mData": "UnitDes"},
                {"mData": "CurrencyCode"},
                {"mData": "service_action_lastYN"},
                {"mData": "service_action_add"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractUID", "value": $("#contractUIDjob").val()});
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

    function open_operation_log(ticketidAtuto,contractUID) {
        $('#operation_log_modal').modal('show');
        $('#operation_log_ticketid').val(ticketidAtuto);
        load_contract_detail_drop(ticketidAtuto,contractUID);
        load_operationlogbody(ticketidAtuto);
    }

    function load_contract_detail_drop(ticketidAtuto,contractUID) {
        $('#productIdhd option').remove();
        $('#productIdhd').val("");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/loard_contract_detail_drop"); ?>',
            dataType: 'json',
            data: {'contractUID': contractUID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#productIdhd').empty();
                    var mySelect = $('#productIdhd');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['ContractDetailID']).html(text['ItemDescrip']));
                    });
                }
                load_operation_system_log_hd(ticketidAtuto,contractUID);
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function load_operation_system_log_hd(ticketidAtuto,contractUID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_operation_system_log_hd"); ?>',
            dataType: 'json',
            data: {'contractUID': contractUID,'ticketidAtuto': ticketidAtuto},
            async: false,
            success: function (data) {
                $('#poNumberhd').html(data['poNumber']);
                $('#wellNohd').html(data['wellNo']);
                $('#lengthhd').html(data['length']);
                $('#Timedatejobstrahd').html(data['Timedatejobstra']);
                $('#ticketNohd').html(data['ticketNo']);
                $('#fieldNamehd').html(data['fieldName']);
                $('#RDXhd').html(data['RDX']);
                $('#Timedatejobendhd').html(data['Timedatejobend']);
                $('#CustomerNamehd').html(data['customerName']);
                $('#drawingNohd').html(data['drawingNo']);
                $('#jointshd').html(data['joints']);
                $('#expectedEndDatehd').val(data['expectedEndDate']);
                $('#activityhd').val(data['activity']);
                $('#productIdhd').val(data['productId']);
                if (jQuery.isEmptyObject(data['productId'])) {
                    $('#productIdhd').attr('disabled',false);
                }else{
                    $('#productIdhd').attr('disabled',true);
                }
                $('#isConfirmendEdit').val(data['confirmedYN']);

                if(data['confirmedYN']==1){
                    $('.isConfirmend').attr('disabled',true);
                }else{
                    $('.isConfirmend').attr('disabled',false);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function updatehdFields(fieldID) {
        var fieldname = document.getElementById(fieldID).getAttribute("name");
        var valu=$('#'+fieldID).val();
        var ticketidAtuto=$('#operation_log_ticketid').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/updatehdFields"); ?>',
            dataType: 'json',
            data: {'fieldname': fieldname,'ticketidAtuto': ticketidAtuto,'valu': valu},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_operationlogbody(ticketidAtuto) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_operationlogbody"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#operation_log_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#operation_log_body').append('<tr class="danger"><td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                }else{
                    $.each(data, function (key, value) {
                        var dlt='';
                        if(value['isUsed']==0){
                            var dlt='<a onclick="delete_oplog(' + value['opStatusReportID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash isConfirmend"></span></a>';
                        }
                        $('#operation_log_body').append('<tr><td>' + x + '</td><td><input type="text" name="startedDated" id="startedDated_' + value['opStatusReportID'] + '" class="form-control startedDated isConfirmend" value="' + value['startedDat'] + '"></td><td>' + value['empnam'] + '</td><td><input type="text" name="joints" class="form-control isConfirmend" value="' + value['joints'] + '" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'joints\',this)"></td><td><input type="text" name="tieIn" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'tieIn\',this)" class="form-control isConfirmend" value="' + value['tieIn'] + '"></td><td><input type="text" name="straight" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'straight\',this)" class="form-control isConfirmend" value="' + value['straight'] + '"></td><td><input type="text" name="completionMeters" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'completionMeters\',this)" class="form-control isConfirmend" value="' + value['completionMeters'] + '"></td><td><input type="text" name="remarks" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'remarks\',this)" class="form-control isConfirmend" value="' + value['remarks'] + '"></td><td><input type="text" name="jointPercentage" onchange="update_op_system_log_detail(' + value['opStatusReportID'] + ',\'jointPercentage\',this)" class="form-control isConfirmend" value="' + value['jointPercentage'] + '"></td><td class="text-right">'+dlt+'</td></tr>');
                        x++;
                    });

                    //$('.startedDated').datetimepicker();
                    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
                    $('.startedDated').datetimepicker({
                        useCurrent: false,
                        format: date_format_policy,
                    }).on('dp.change', function (ev) {
                        update_op_system_log_detail(0,'startedDated',this)
                    });


                    setTimeout(function(){
                        if($('#isConfirmendEdit').val()==1){
                            $('.isConfirmend').attr('disabled',true);
                        }else{
                            $('.isConfirmend').attr('disabled',false);
                        }
                    }, 200);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addCrewRow() {
        var crewEmpID=$('#crewEmpID').val();
        var ticketidAtuto=$('#operation_log_ticketid').val();
        if (jQuery.isEmptyObject(crewEmpID)) {
            myAlert('e','Select Crew')
        }else{
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Operation/addCrewRow"); ?>',
                dataType: 'json',
                data: {'ticketidAtuto': ticketidAtuto,'empID': crewEmpID},
                async: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(data[0]=='s'){
                        load_operationlogbody(ticketidAtuto);
                    }else{
                        myAlert(data[0],data[1])
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function delete_oplog(opStatusReportID) {
        var ticketidAtuto=$('#operation_log_ticketid').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/delete_oplog"); ?>',
            dataType: 'json',
            data: {'opStatusReportID': opStatusReportID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                load_operationlogbody(ticketidAtuto);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function update_op_system_log_detail(opStatusReportID,fieldname,ths) {
        var valu=$(ths).val();
        var ticketidAtuto=$('#operation_log_ticketid').val();

        if(fieldname=="startedDated"){
            var startedDatedID=$(ths).attr('id')
            var res = startedDatedID.split("_");
            opStatusReportID=res[1];

        }
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/update_op_system_log_detail"); ?>',
            dataType: 'json',
            data: {'opStatusReportID': opStatusReportID,'valu': valu,'fieldname': fieldname},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    load_operationlogbody(ticketidAtuto);
                }else{
                    myAlert(data[0],data[1])
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addServiceProduct(ContractDetailID,typeId) {
        var contractUID = $('#contractUIDTicket').val();
        var ticketidAtuto = $('#ticketidAtutoEdit').val();
        var lastYN=0;

        if(typeId==1){
            var lastYN = document.getElementById("lastYNProd_"+ContractDetailID).checked;
            if(lastYN==false){
                lastYN=0;
            }else{
                lastYN=1;
            }
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/save_product_service_detail"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto,'contractUID': contractUID,'ContractDetailID': ContractDetailID,'typeId':typeId, 'lastYN':lastYN},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='e'){
                    myAlert(data[0],data[1]);
                }
                loadproductserviceTable();
                Otable.draw();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function update_op_product_service(TicketproductID,fieldname,ths) {
        var valu=$(ths).val();
        var ticketidAtuto=$('#ticketidAtutoEdit').val();

        if(fieldname=="addedDate"){
            var addedDateID=$(ths).attr('id')
            var res = addedDateID.split("_");
            TicketproductID=res[1];
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/update_op_product_service"); ?>',
            dataType: 'json',
            data: {'TicketproductID': TicketproductID,'valu': valu,'fieldname': fieldname},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    loadproductserviceTable();
                    Otable.draw();
                }else{
                    myAlert(data[0],data[1])
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function delete_op_product_service(TicketproductID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/delete_op_product_service"); ?>',
            dataType: 'json',
            data: {'TicketproductID': TicketproductID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                loadproductserviceTable();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addCrew(ticketidAtuto) {
        $('#op_crew_modal').modal('show');
        $('#crewticketidAtuto').val(ticketidAtuto);
        load_op_crew_table(ticketidAtuto)
    }

    function check_employee_attach_expired() {
        var ticketidAtuto=$('#crewticketidAtuto').val();
        var ticketCrewEmp=$('#ticketCrewEmp').val();

        if (jQuery.isEmptyObject(ticketCrewEmp)) {
            myAlert('e','Select Crew Group');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/check_employee_attach_expired"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto,'groupID': ticketCrewEmp},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    addCrewop();
                }else{

                    if(data[2]==0){
                        myAlert(data[0],data[1]);
                    }else if(data[2]==2){
                        var x=1;
                        $('#opdoc_expiry_body').empty();
                        $.each(data[3], function (key, value) {
                            var dlt='';
                            $('#opdoc_expiry_body').append('<tr><td>' + x + '</td><td>' + value['empdtl'] + '</td><td>' + value['DocDescription'] + '</td><td>' + value['descp'] + '</td><td>' + value['datdiff'] + '</td></tr>');
                            x++;
                        });

                        $('#opdoc_expiry_modal').modal('show');

                    }else{
                        addCrewop();
                    }


                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function addCrewop() {
        var ticketidAtuto=$('#crewticketidAtuto').val();
        var ticketCrewEmp=$('#ticketCrewEmp').val();
        $('#opdoc_expiry_modal').modal('hide');
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/addCrewop"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto,'ticketCrewEmp': ticketCrewEmp},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    OtableCrew.draw();
                    $('#ticketCrewEmp').val('').change();
                }else{
                    myAlert(data[0],data[1])
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_op_crew_table(selectedID=null) {
        var ticketidAtuto=$('#crewticketidAtuto').val();
        OtableCrew = $('#op_crew_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_op_crew_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['ticketCrewID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');



                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
                api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="3"><b>&nbsp;<i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;  '+group+'</b></td></tr>'
                        );
                        last = group;
                    }
                });


            },
            "aoColumns": [
                {"mData": "groupName"},
                {"mData": "crewname"},
                {"mData": "opCrewAction"}
            ],
            "columnDefs": [{"orderable": false,"targets": [1,2] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "ticketidAtuto", "value": ticketidAtuto});
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

    function deleteOpCrew(ticketCrewID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/deleteOpCrew"); ?>',
            dataType: 'json',
            data: {'ticketCrewID': ticketCrewID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                OtableCrew.draw();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function CrewHistory(selectedID=null) {
        var ticketidAtuto=$('#crewticketidAtuto').val();
        $('#op_crew_history_modal').modal('show');

        OtableCrewHis = $('#op_crew_history_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_op_crew_history_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['ticketCrewHistoryID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "ticketCrewHistoryID"},
                {"mData": "crewname"},
                {"mData": "description"},
                {"mData": "crewcreated"},
                {"mData": "createdDate"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "ticketidAtuto", "value": ticketidAtuto});
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

    function addOpUnit(ticketidAtuto) {
        $('#op_unit_modal').modal('show');
        $('#unitticketidAtuto').val(ticketidAtuto);
        load_op_asset_unit_table(ticketidAtuto)
    }

    function check_asset_attach_expired() {
        var faID = $('#faID').val();

        if (jQuery.isEmptyObject(faID)) {
            myAlert('e','Select Asset');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/check_asset_attach_expired"); ?>',
            dataType: 'json',
            data: {'faID': faID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    addAssetunitop();
                }else{

                    swal({
                            title: data[1],
                            text: "Do you want to add this Asset",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Confirm",
                            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                        },
                        function () {
                            addAssetunitop();
                        });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addAssetunitop() {
        var faID = $('#faID').val();
        var ticketidAtuto = $('#unitticketidAtuto').val();
        var unitComment = $('#unitComment').val();


        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/addAssetunitop"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto,'faID': faID,'Comment': unitComment},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='s'){
                    OtableAssetunit.draw();
                    $('#faID').val('').change();
                    $('#unitComment').val('');
                }else{
                    myAlert(data[0],data[1])
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_op_asset_unit_table(selectedID=null) {
        var ticketidAtuto=$('#unitticketidAtuto').val();
        OtableAssetunit = $('#op_asset_unit_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_op_asset_unit_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['unitMoreID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "unitMoreID"},
                {"mData": "assetDescription"},
                {"mData": "Comment"},
                {"mData": "opAssetAction"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "ticketidAtuto", "value": ticketidAtuto});
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

    function deleteOpAssetUnit(unitMoreID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/deleteOpAssetUnit"); ?>',
            dataType: 'json',
            data: {'unitMoreID': unitMoreID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                OtableAssetunit.draw();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function assetUnitHistory(selectedID=null) {
        var ticketidAtuto=$('#unitticketidAtuto').val();
        $('#op_unit_history_modal').modal('show');

        OtableCrewHis = $('#op_asset_unit_history_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/load_op_asset_history_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['ticketCrewHistoryID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "ticketCrewHistoryID"},
                {"mData": "assetDescription"},
                {"mData": "description"},
                {"mData": "crewcreated"},
                {"mData": "createdDate"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "ticketidAtuto", "value": ticketidAtuto});
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


    function confirmOpJobTicket(ticketidAtuto,contractUID) {
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
                    data: {'ticketidAtuto': ticketidAtuto},
                    url: "<?php echo site_url('Operation/confirm_opticket'); ?>",
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

    function referbackjob(id) {
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
                    data: {'ticketidAtuto': id},
                    url: "<?php echo site_url('Operation/referback_job_ticket'); ?>",
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

    function load_txt_invoice() {
        var ticketidAtuto=$('#ticketidAtutoEdit').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_txt_invoice"); ?>',
            dataType: 'json',
            data: {'ticketidAtuto': ticketidAtuto},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#op_tkt_invoice_table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#op_tkt_invoice_table_body').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                }else{
                    $.each(data, function (key, value) {
                        var dlt='';

                        $('#op_tkt_invoice_table_body').append('<tr><td>' + x + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['invoiceDate'] + '</td></tr>');
                        x++;
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function confirmProformaInvoice(ticketidAtuto) {
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
                    data: {'ticketidAtuto': ticketidAtuto},
                    url: "<?php echo site_url('Operation/confirmProformaInvoice'); ?>",
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

    function loadGrandTot() {
        $('#grandtot').html();
        var srvtot=$('#producttothn').val();
        var prodtt=$('#servicetothn').val();
        var defCompDecimal = $('#defCompDecimal').val();
        if (jQuery.isEmptyObject(defCompDecimal)) {
            defCompDecimal=3;
        }
        var tot=parseFloat(prodtt)+parseFloat(srvtot);
        $('#grandtot').html(parseFloat(tot).toFixed(defCompDecimal));
    }

</script>