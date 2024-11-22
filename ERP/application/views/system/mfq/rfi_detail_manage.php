<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_rfi_detial');
echo head_page($title, false);
$segment = fetch_mfq_segment(true);
$companyID = current_companyID();
$stageList = job_stage_selection();

$items = $this->db->where('companyID',$companyID)->from('srp_erp_itemmaster')->get()->result_array();
$itemList = array();

foreach($items as $item){
    $itemList[$item['itemAutoID']] = $item['itemSystemCode'].' - '.$item['itemName'];
}

$rfiID = $data_arr;

$this->db->where('rfiID',$rfiID);
$rfiDetail = $this->db->from('srp_erp_mfq_jobrfimaster')->get()->row_array();


?>

<style>
    .rfiType{
        background-color: #f3dada;
        padding: 10px;
        border-radius: 5px;
        margin:10px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-sm-12">

    <input type="hidden" name="workProcessID" id="workProcessID" value="<?php echo $rfiDetail['workProcessID'] ?>" >
    <input type="hidden" name="rfiID" id="rfiID" value="<?php echo $rfiDetail['rfiID'] ?>" >
<!-- 
    <div class="alert alert-success"><p>RFI is Submitted</p></div> -->

    <div class="row" style="">
            <div class="form-group col-sm-3">
                <label class="title"><?php echo 'Select Type' ?></label>
            </div>
    </div>

    <div class="row" style="margin-bottom:5%">
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType" name="selectType" value="1" <?php echo ($rfiDetail['rfiType'] == 1) ? 'checked' : '' ?> > &nbsp; Stage Inspection
        </div>
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="2" <?php echo ($rfiDetail['rfiType'] == 2) ? 'checked' : '' ?> > &nbsp; Before Client Inspection 
        </div>
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="3" <?php echo ($rfiDetail['rfiType'] == 3) ? 'checked' : '' ?> > &nbsp; Final Inspection / Functional Test
        </div>
   
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="4" <?php echo ($rfiDetail['rfiType'] == 4) ? 'checked' : '' ?> > &nbsp; Parts Inspection
        </div>
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="5" <?php echo ($rfiDetail['rfiType'] == 5) ? 'checked' : '' ?> > &nbsp; Client Inspection
        </div>
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="6" <?php echo ($rfiDetail['rfiType'] == 6) ? 'checked' : '' ?> > &nbsp; Load Out Inspection
        </div>
        <div class="form-group rfiType col-sm-3">
            <input type="radio" class="selectType"  name="selectType" value="7" <?php echo ($rfiDetail['rfiType'] == 7) ? 'checked' : '' ?> > &nbsp; Other
        </div>
   
    </div>

    <div class="row" style="margin-bottom:5%">
        <div <?php echo ($rfiDetail['rfiType'] == 7) ? 'class="hide"' : '' ?> id="div_stage">
            <div class="form-group col-sm-3">
                <label class="title"><?php echo 'Select Stage' ?></label>
            </div>
            <div class="form-group col-sm-9">
                <?php echo form_dropdown('stageID', $stageList,$rfiDetail['stageID'], 'class="form-control select2"  id="stageID" onchange="change_value_master(this)"'); ?>
            </div>
        </div>
        <div <?php echo ($rfiDetail['rfiType'] == 7) ? '' : 'class="hide"' ?> id="div_stage_comment">
            <div class="form-group col-sm-3">
                <label class="title"><?php echo 'Stage Comment' ?></label>
            </div>
            <div class="form-group col-sm-9">
                <input type="text" class="form-control " id="stageComment" name="stageComment" value="<?php echo $rfiDetail['stageComment'] ?>" onchange="change_value_master(this,'stageComment')">
            </div>
        </div>
    </div>

    <div class="row" style="margin-bottom:5%">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'RFI No' ?></label>
            <input type="text" class="form-control " id="rfiNumber" name="rfiNumber" value="<?php echo $rfiDetail['rfiNumber'] ?>">
        </div>

        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Requested Date' ?></label>
            <input type="datetime" class="form-control" id="requestedDate" name="requestedDate" value="<?php echo $rfiDetail['requestedDate'] ?>">
        </div>
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Prepaired By' ?></label>
            <input type="datetime" class="form-control" id="created_by" name="created_by" value="<?php echo $rfiDetail['created_by'] ?>">
        </div>
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Status' ?></label>
            <input type="datetime" class="form-control" id="status" name="status" required>
        </div>
    </div>
    <div class="row" style="margin-bottom:5%">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Job ID' ?></label>
           
        </div>
        <div class="form-group col-sm-9">
            <input type="text" class="form-control " id="jobDescription" name="jobDescription" value="<?php echo $rfiDetail['jobDescription'] ?>">
        </div>
    </div>

    <div class="row" style="margin-bottom:5%">
        <div class="form-group col-sm-3">
            <label class="">Equipment Serial</label>
                <span class="input-req"
                    title="Required Field">
                    <input type="text" class="form-control"  name="equpSerial" id="equpSerial" value="<?php echo $rfiDetail['equipmentSerial'] ?>"> 
                
            </div>
            <div class="form-group col-sm-3">
                <label class="">Drawing Ref</label>
                <span class="input-req"
                    title="Required Field">
                    <input type="text" class="form-control"  name="drawingRef" id="drawingRef" value="<?php echo $rfiDetail['drawingRef'] ?>"> 
                    <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-3">
                <label class="">Quantity</label>
                <span class="input-req"
                    title="Required Field">
                    <input type="text" class="form-control"  name="rfiQty" id="rfiQty" value="<?php echo $rfiDetail['quantity'] ?>"> 
                    <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-3">
                <label class="">Client/TPI</label>
                <span class="input-req"
                    title="Required Field">
                    <input type="text" class="form-control"  name="clientTpi" id="clientTpi" value="<?php echo $rfiDetail['clientTpi'] ?>"> 
                    <span class="input-req-inner"></span></span>
            </div>
                            
        </div>
    </div>

    <label class="title"><?php echo 'Items for Inspection' ?></label> <hr>
    
    

    <table class="table table-striped table-condesed mfqTable" id="RFItbl">
        <thead>
        <tr>
            <th>#</th>
            <th>Item For Inspection</th>
            <th class="text-left">Item Status</th>
            <th>Remarks</th>
            <th>Inspected By</th>
            <th><?php if($rfiDetail['status'] == 'Open') { ?>
                <button class="btn btn-primary" onclick="add_detail()"><i class="fa fa-plus"></i>  Add </button> <?php } ?></th>
        </tr>
        </thead>
        <tbody id="table_body_ci_detail">
      
        </tbody>
    </table>

    <br><br>
    <div class="row <?php echo ($rfiDetail['status'] != 'Close') ? 'hide': '' ?>" style="margin-bottom:5%">
        <div class="col-sm-8">
            <div class="text-center" style="background-color:#b1ddad;">
                <label class="title"><?php echo 'Satisfactory and Released For' ?></label> <hr>
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2" name="inspectedResult" value="1"  <?php echo ($rfiDetail['responseType'] == 1 || is_null($rfiDetail['responseType']) ) ? 'checked' : '' ?> > &nbsp; Blast and Prime
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="2"  <?php echo ($rfiDetail['responseType'] == 2) ? 'checked' : '' ?> > &nbsp; Next Stage
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="3"  <?php echo ($rfiDetail['responseType'] == 3) ? 'checked' : '' ?> > &nbsp; Client Inspection
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="4"  <?php echo ($rfiDetail['responseType'] == 4) ? 'checked' : '' ?> > &nbsp; Further Coating
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="5"  <?php echo ($rfiDetail['responseType'] == 5) ? 'checked' : '' ?> > &nbsp; Test Sequence
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="6"  <?php echo ($rfiDetail['responseType'] == 6) ? 'checked' : '' ?> > &nbsp; Packaging & Delivery
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="7" <?php echo ($rfiDetail['responseType'] == 7) ? 'checked' : '' ?> > &nbsp; Fire Proofing
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="8"  <?php echo ($rfiDetail['responseType'] == 8) ? 'checked' : '' ?> > &nbsp; Site Installation
            </div>
            <div class="col-sm-4">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="9"  <?php echo ($rfiDetail['responseType'] == 9) ? 'checked' : '' ?> > &nbsp; Load Out
            </div>
        </div>
        <div class="col-sm-4">
            <div class="text-center" style="background-color:#e3a3a3;">
                <label class="title"><?php echo 'Not Satisfactory On Hold' ?></label> <hr>
            </div>
            <div class="col-sm-12">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="10"  <?php echo ($rfiDetail['responseType'] == 10) ? 'checked' : '' ?> > &nbsp; Repair Required
            </div>
            <div class="col-sm-12">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="11"  <?php echo ($rfiDetail['responseType'] == 11) ? 'checked' : '' ?> > &nbsp; Further Work/s Required
            </div>
            <div class="col-sm-12">  
                <input type="radio" class="selectType2"  name="inspectedResult" value="12"  <?php echo ($rfiDetail['responseType'] == 12) ? 'checked' : '' ?> > &nbsp; Site remarks
            </div>
        </div>
    </div>
   
    <div class="row <?php echo ($rfiDetail['status'] != 'Close') ? 'hide': '' ?>" style="margin-bottom:5%">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo 'Remarks' ?></label>
           
        </div>
        <div class="form-group col-sm-9">
            <textarea class="form-control" id="remarks" onChange="add_rfi_remarks()"><?php echo $rfiDetail['remarks'] ?></textarea>
        </div>
    </div>

    <?php if($rfiDetail['status'] == 'Open') { ?>
        <div class="row">
            <button class='btn btn-primary' id="btnEnter" onclick="close_rfi_request()">Submit RFI</button>
        </div>

    <?php } ?>

</div>


<div class="modal fade" id="addItemModal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Item</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        
                    <div class="row" style="margin-bottom:5%">
                        <div class="form-group col-sm-3">
                            <label class="title"><?php echo 'Item' ?></label>
                        
                        </div>
                        <div class="form-group col-sm-9">
                            <?php echo form_dropdown('ItemAutoID', $itemList, '', 'class="form-control select2"  id="ItemAutoID"'); ?>
                        </div>
                    </div>

                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="proccedToItemAdd()">Add</button>
            </div>
        </div>
    </div>
</div>


<script>    
    $('.select2').select2();
    load_detail_table();
    var workProcessID = $('#workProcessID').val();

    $('.headerclose').attr('onclick',"fetchPage('system/mfq/mfq_job_create',"+workProcessID+",'Edit Job','MFQ')");

    function add_detail(){

        $("#addItemModal").modal('show');

    }

    function load_detail_table(){
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();
        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'workProcessID': workProcessID,'rfiID':rfiID},
            url: "<?php echo site_url('MFQ_Job/get_detail_table'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);

                $('#table_body_ci_detail').empty();
                $('#table_body_ci_detail').html(data);
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });


    }

    function proccedToItemAdd(){
        var ItemAutoID = $('#ItemAutoID').val();
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();

        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'workProcessID': workProcessID,'rfiID':rfiID,'ItemAutoID':ItemAutoID},
            url: "<?php echo site_url('MFQ_Job/add_item_Detail'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                load_detail_table();
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function deleteRfi(id){
       
            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo 'Are you sure you want to delete';?>",/*You want to confirm this!*/
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
                    // dataType: 'html',
                    data: {'id':id},
                    url: "<?php echo site_url('MFQ_Job/delete_rfi_detail'); ?>",
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        load_detail_table();
                        
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            }
        );


    }

    $('.selectType').click(function () {

        var iVal = $(this).val();
        var name = $(this).attr('name');
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();

        if(iVal == 7){
            $('#div_stage').addClass('hide');
            $('#div_stage_comment').removeClass('hide');
        }else{
            $('#div_stage').removeClass('hide');
            $('#div_stage_comment').addClass('hide');
        }
        
        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'val':iVal,'selectType': 1,'rfiID':rfiID,'workProcessID':workProcessID},
            url: "<?php echo site_url('MFQ_Job/change_value_detail'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                load_detail_table();
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    });


    $('.selectType2').click(function () {

        var iVal = $(this).val();
        var name = $(this).attr('name');
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();

        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'val':iVal,'selectType': 2,'rfiID':rfiID,'workProcessID':workProcessID},
            url: "<?php echo site_url('MFQ_Job/change_value_detail'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    });

    function add_rfi_remarks(ev){
        
        var iVal = $('#remarks').val();
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();

        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'val':iVal,'remarks': 1,'rfiID':rfiID,'workProcessID':workProcessID},
            url: "<?php echo site_url('MFQ_Job/change_value_detail'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function close_rfi_request(){
         var rfiID = $('#rfiID').val();

         swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo 'Are you sure you want to submit this';?>",/*You want to confirm this!*/
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
                    // dataType: 'html',
                    data: {'rfiID':rfiID,'status':'Submit'},
                    url: "<?php echo site_url('MFQ_Job/change_value_detail'); ?>",
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }
    

    function change_value_master(ev,type='stage'){
        var val = $(ev).val();
  
        var workProcessID = $('#workProcessID').val();
        var rfiID = $('#rfiID').val();
        var dataJson = {};
        if(type == 'stage'){
            dataJson = {'stage':val,'rfiID':rfiID,'workProcessID':workProcessID};
        }else if(type == 'stageComment'){
            dataJson = {'stageComment':val,'rfiID':rfiID,'workProcessID':workProcessID};
        }

        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: dataJson,
            url: "<?php echo site_url('MFQ_Job/change_value_detail'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


</script>