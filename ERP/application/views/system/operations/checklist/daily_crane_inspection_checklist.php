<?php
$result1= array_slice($details ?? [],0,7);
$result2= array_slice($details ?? [],7,7);
$result3= array_slice($details ?? [],14,7);


$html = false;
$comment = '';

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_time = date('H:i:s');

$default_date = date('Y-m-d H:i:s',strtotime($current_date));
$default_time = date('H:i:s',strtotime($current_time));

$crew = get_crew_list_for_checlist_contract($job_details['contract_po_id']);

if($page_type =='html'){
    $html_img = '';
    $html_style = '';
    $html_radio = '';

}else{
    $html_img = '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAhvL-Nr_RGSeRVENLybjH_3eqB7h71xy5xw&usqp=CAU" width="20"/>';
    $html_style = 'type="hidden"';
    $html_radio = '<input type="radio" />';
 
}

?>
<div class="row">
    <div class="col-md-12" style="background: #fff;padding: 25px;">

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table>
                        <colgroup>
                            <col style="width: 20%; height:40px" />
                            <col style="width: 60%; height:40px" />
                            <col style="width: 20%; height:40px" />
                        </colgroup>
                        <tr>
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                                <img alt="Logo" style="height: 75px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td width="40%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 18px">
                                            RAY INTERNATIONAL OIL & GAS
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 15px">
                                            Daily Crane Inspection Checklist
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="20%" valign="middle" height="40">
                                <table>                                    
                                    <tr>
                                        <th style="text-align:right;font-size: 12px">
                                            RAY-OG-FRM-412(1.0)
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <form role="form" id="data_check_list_form_fish" class="form-horizontal">
        <input type="hidden" name="header_id" value="<?php echo isset($header_record['id'])? $header_record['id'] : '' ?>">
            <input type="hidden" name="checklist_id" value="<?php echo isset($header_record['master_id'])? $header_record['master_id'] : '' ?>">
        <table>
            <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Date:</td>
                            <td valign="middle" height="40">
                                <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="w-100" name="crane_date" value="<?php echo isset($header_record['crane_date'])? $header_record['crane_date'] : $current_date ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="crane_date" class="form-control" required>
                                </div>
                            </td>
                            <td valign="middle" height="40">Crane Reg No #:</td>
                            <td valign="middle" height="40">
                            <input type="text" value="<?php echo isset($header_record['crane_reg'])? $header_record['crane_reg'] : '' ?>" class="input_style_1" name="crane_reg" value="" id="crane_reg" />
                            </td>
                        </tr>
                        <tr>
                            <td valign="middle" height="40">Crane Operator Name</td>
                            <td valign="middle" height="40" colspan="3">
                            <?php echo form_dropdown('crane_operator', $crew, $header_record['crane_operator'], 'class="form-control select2" id="field_id" required onchange=""'); ?>
                            </td>
                        </tr>   
                    </table>
                </div>
            </div>
        </div>
        
        <table>
            <tr><td height="25px" style="height:35px;font-size:14px !important">Check all items as indicated. Inspect and indicate as Pass = P; Fail = F; or Not Applicable = N/A</td></tr>
        </table>

        <div class="row">
            <div class="col-md-4" style="padding-right: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                    <?php
                        foreach ($result1 as $val1) {
                            if(!empty($result1)){  
                                $comment = isset($response_list[$val1['id']]['comments']) ? $response_list[$val1['id']]['comments'] : ''; 
                                ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val1['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="<?php echo base_url() . 'images/icons/daily_crane_inspection/' . $val1['image_path'];?>" height="60"/></td>
                            <td style="text-align:center">
                                <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val1['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                        } ?>    
                        
                    </table>
                </div>
            </div>    
            <div class="col-md-4" style="padding-right: 5px;padding-left: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                        <?php
                        foreach ($result2 as $val2) {
                            if(!empty($result2)){  
                                $comment = isset($response_list[$val2['id']]['comments']) ? $response_list[$val2['id']]['comments'] : ''; 
                                ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="<?php echo base_url() . 'images/icons/daily_crane_inspection/' . $val2['image_path'];?>" height="60"/></td>
                            <td style="text-align:center">
                                <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val2['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                        } ?>   
                        
                        
                    </table>
                </div>
            </div>  
            <div class="col-md-4" style="padding-left: 5px;">        
                <div class="table-responsive" style="background:#fff;">    
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th style="text-align:left">Item to be Checked</th>
                            <th style="text-align:center">Picture</th>
                            <th style="text-align:center">Condition</th>
                        </tr>
                        <?php
                        foreach ($result3 as $val3) {
                            if(!empty($result3)){  
                                $comment = isset($response_list[$val3['id']]['comments']) ? $response_list[$val3['id']]['comments'] : ''; 
                                ?>                        
                        <tr>
                            <td style="text-align:left"><?php echo $val3['qtn_name']; ?></td>
                            <td style="text-align:center"><img src="<?php echo base_url() . 'images/icons/daily_crane_inspection/' . $val3['image_path'];?>" height="60"/></td>
                            <td style="text-align:center">
                                <?php if($page_type =='html') { ?>
                                        <input type="text" class="input_style_1" name="comment_<?php echo $val3['id'] ?>" value="<?php echo $comment ?>" id="comment" />
                                    <?php }else{ ?>
                                        <?php echo $comment ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }
                        } ?>   
                        
                    </table>
                </div>
            </div>          
        </div>
       
        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1">
                        <colgroup>
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                            <col style="width: 25%; height:20px" />
                        </colgroup>
                        <?php if($page_type =='html') { ?>
                            <tr>
                                <th colspan="4" valign="middle" height="40">Comments:</th>
                            </tr>
                            <tr>
                                <td colspan="4" valign="middle" height="40"><input type="text" value="<?php echo isset($header_record['comment'])? $header_record['comment'] : '' ?>" class="input_style_1" name="comment_crane" id="comment_crane" /></td>
                            </tr>
                            <tr>
                                <td valign="bottom" height="40">Crane Operator signature:</td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="inspection_signature" id="inspection_signature" value="<?php echo isset($header_record['inspection_signature'])? $header_record['inspection_signature'] : '' ?>" /></td>
                                <td valign="bottom" height="40">Rig Manager or Supervisor Signature: </td>
                                <td valign="bottom" height="40"><input type="text" class="input_style_1" name="report_review_signature" id="report_review_signature"  value="<?php echo isset($header_record['report_review_signature'])? $header_record['report_review_signature'] : '' ?>" /></td>
                            </tr>
                        <?php }else{ ?>
                            <tr>
                                <th colspan="4" valign="middle" height="40">Comments:</th>
                            </tr>
                            <tr>
                                <td colspan="4" valign="middle" height="40"><?php echo isset($header_record['comment'])? $header_record['comment'] : '' ?></td>
                            </tr>
                            <tr>
                                <td valign="bottom" height="40">Crane Operator signature:</td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['inspection_signature'])? $header_record['inspection_signature'] : '' ?></td>
                                <td valign="bottom" height="40">Rig Manager or Supervisor Signature: </td>
                                <td valign="bottom" height="40"><?php echo isset($header_record['report_review_signature'])? $header_record['report_review_signature'] : '' ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>


        <div class="row">
                        <div class="col-12 col-md-10">  
                                <label for="financeyear">Comment</label>
                                     <div>
                                            <textarea class="form-control" name="ChecklistComment" id="ChecklistComment"><?php echo $header_record['checklist_comment'] ?>  </textarea>
                                     </div>
                        </div>
        </div>
        

        
        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>

        <div class="row">
            <div class="col-12 col-md-12">  
                <?php if($header_record['is_confirmed']==1){ ?>
                    <!-- <button type="button" class="btn btn-primary-new size-sm float-right" onclick="save_checklist(1)"><i class="fa fa-save"></i> Edit</button> -->
                <?php } else { ?>
                <button type="button" class="btn btn-primary-new size-sm float-left" onclick="save_checklist()"><i class="fa fa-save"></i> Save Draft</button>  
                <button type="button" class="btn btn-primary-new size-sm float-right" onclick="save_checklist(1)"><i class="fa fa-save"></i> Submit</button>
                <?php } ?>
            </div>
        </div>
        </form>

        <div class="row">
            <div class="form-group col-md-6 pt-20">
                <label for="bob_number">Add Attachment</label>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="checklist_attachment_com" class="form-inline"'); ?>
                                    
                        <div class="col-sm-12">
                                <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                    value="header_checklist">
                                <input type="hidden" class="form-control" id="checklist_header_id"
                                    name="checklist_header_id" value="<?php echo isset($header_record['id'])? $header_record['id'] : '' ?>">
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
                                            type="file" name="document_file_bob" id="document_file_bob"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_checklist_header()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    
                </div>

                <div id="show_attachments_checklist"></div>

            </div>
        </div>
    </div>
</div>

<script>
        $(document).ready(function () {               
            op_checklist_attachments_com();
        });    

        var csrf_token = '<?php echo $this->security->get_csrf_hash() ?>';

        $('#crane_date').datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(ev){
        });

        $('.select2').select2();

        function save_checklist(confirmedYN = null){

            var data = $('#data_check_list_form_fish').serializeArray();

            data.push({'name': 'confirmYN', 'value': confirmedYN});
            data.push({'name': 'csrf_token', 'value': csrf_token});

            swal({
                    title: "Are you sure?",
                    text: "You want to save this ",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes!"
                },
            function () {

                $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Jobs/save_checklist_response'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        $("#checklist_view_modal_response").modal('hide');
                        load_pre_job_checklist();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            
            })
        }

        function document_uplode_checklist_header() {
            var formData = new FormData($("#checklist_attachment_com")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Jobs/checklist_attachement_upload'); ?>",
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
                       // $('#add_attachemnt_show').addClass('hide');
                        $('#remove_id').click();
                        //$('#opportunityattachmentDescription').val('');
                        op_checklist_attachments_com();
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function op_checklist_attachments_com() {
            var checklist_header_id = $('#checklist_header_id').val();
       
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {checklist_header_id: checklist_header_id},
                url: "<?php echo site_url('Jobs/load_checklist_attachment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#show_attachments_checklist').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_job_attachment(id, fileName) {
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
                        data: {'id': id, 'fileName': fileName,'csrf_token':csrf_token},
                        url: "<?php echo site_url('Jobs/delete_job_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                op_job_attachments();
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
</script>
