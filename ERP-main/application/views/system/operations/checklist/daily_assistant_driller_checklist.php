<?php
$result1= array_slice($details,0,15);
$result2= array_slice($details,15);
//print_r($result1);
//print_r($header_record);
$html = false;
$comment = '';

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_time = date('H:i:s');

$default_date = date('Y-m-d H:i:s',strtotime($current_date));
$default_time = date('H:i:s',strtotime($current_time));

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
                            <td width="60%" valign="middle" height="40">
                                <table>
                                    <tr>
                                        <th style="text-align:center;font-size: 18px">
                                            DAILY ASSISTANT DRILLER CHECKLIST
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;font-size: 14px">
                                            RAY-OG-FRM-413 (4.0)
                                        </th>
                                    </tr>
                                </table>        
                            </td>
                            <td width="20%" valign="middle" height="40" style="font-size: 16px !important;text-align:left">
                               &nbsp;
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <form role="form" id="data_check_list_form" class="form-horizontal">

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
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                            <col style="width: 10%; height:40px" />
                            <col style="width: 23%; height:40px" />
                        </colgroup>
                        <tr>
                            <td valign="middle" height="40">Rig #</td>
                            <td valign="middle" height="40"><?php echo $header_record['rig_hoist_name'] ?></td>
                            <td valign="middle" height="40">Well #</td>
                            <td valign="middle" height="40"><?php echo $header_record['well_name'] ?></td>
                            <td valign="middle" height="40">Date</td>
                            <td valign="middle" height="40">
                                <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" class="w-100" name="date_inspection" value="<?php echo isset($header_record['date_inspection'])? $header_record['date_inspection'] : $current_date ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="date_inspection" class="form-control" required>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <table>
            <tr><td height="25px" style="height:25px">&nbsp;</td></tr>
        </table>
        <div class="row">
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                        <?php
                            $i=1;
                            if(!empty($result1)){
                            foreach ($result1 as $val1) {
                                $checked_id = isset($response_list[$val1['id']]['status']) ? $response_list[$val1['id']]['status'] : '';
                        ?>
                            <tr>
                                <td style="text-align:center"><?php echo $i; ?></td>
                                <td><?php echo $val1['qtn_name']; ?></td>
                                <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val1['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val1['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                                <td style="text-align:center"><input type="radio" value="3" name="radio_<?php echo $val1['id'] ?>" <?php echo ($checked_id == '3') ? "checked ": '';echo $html_style  ?> id="flexRadioDefault3"/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                            </tr>
                        <?php 
                            $i++;
                            }
                            } 
                        ?>
                        
                    </table>
                </div>
            </div>
            <div class="col-md-6">        
                <div class="table-responsive" style="background:#fff;">
                    <table class="table table-bordered-1 table_check_list table-striped">
                        <tr>
                            <th rowspan="2" style="text-align:center;background:#eee;">No.</th>
                            <th rowspan="2" style="background:#eee;">Inspection Details</th>
                            <th colspan="3" scope="colgroup" style="text-align:center;background:#eee;">Finding</th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center;background:#eee;">Yes</th>
                            <th scope="col" style="text-align:center;background:#eee;">No</th>
                            <th scope="col" style="text-align:center;background:#eee;">N/A</th>
                        </tr>
                    
                        <?php
                        $i=16;
                            if(!empty($result2)){
                            foreach ($result2 as $val2) {
                                $checked_id = isset($response_list[$val2['id']]['status']) ? $response_list[$val2['id']]['status'] : '';
                        ?>
                        <tr>
                            <td style="text-align:center"><?php echo $i; ?></td>
                            <td><?php echo $val2['qtn_name']; ?></td>
                            <td style="text-align:center"><input type="radio" value="1" name="radio_<?php echo $val2['id'] ?>" <?php echo ($checked_id == '1') ? "checked ": '';echo $html_style ?> id="flexRadioDefault1"/><?php echo ($checked_id == '1') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" value="2" name="radio_<?php echo $val2['id'] ?>" <?php echo ($checked_id == '2') ? "checked ": '';echo $html_style ?> id="flexRadioDefault2"/><?php echo ($checked_id == '2') ? "$html_img": "$html_radio" ?></td>
                            <td style="text-align:center"><input type="radio" value="3" name="radio_<?php echo $val2['id'] ?>" <?php echo ($checked_id == '3') ? "checked ": '';echo $html_style ?> id="flexRadioDefault3"/><?php echo ($checked_id == '3') ? "$html_img": "$html_radio" ?></td>
                        </tr>
                        <?php $i++;
                            }
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
                        <?php if($page_type =='html') { ?>
                            <tr>
                                <td>Assistant Driller’s Name </td>
                                <td width="20%"><input type="text" class="input_style_1" name="driller_name" id="driller_name" value="<?php echo isset($header_record['driller_name'])? $header_record['driller_name'] : '' ?>" /></td>
                                <td>Assistant Driller’s Signature </td>
                                <td width="20%"><input type="text" class="input_style_1" name="driller_signature" id="driller_signature" value="<?php echo isset($header_record['driller_signature'])? $header_record['driller_signature'] : '' ?>" /></td>
                            </tr>
                            <tr>
                                <td>Rig Manager / Toolpusher Name </td>
                                <td width="20%"><input type="text" class="input_style_1" name="rig_manager_name" id="rig_manager_name" value="<?php echo isset($header_record['rig_manager_name'])? $header_record['rig_manager_name'] : '' ?>" /></td>
                                <td>Rig Manager / Toolpusher Signature </td>
                                <td width="20%"><input type="text" class="input_style_1" name="rig_manager_signature" id="rig_manager_signature" value="<?php echo isset($header_record['rig_manager_signature'])? $header_record['rig_manager_signature'] : '' ?>" /></td>
                            </tr>

                        <?php }else{ ?>
                            <tr>
                            <td>Assistant Driller’s Name </td>
                            <td width="20%"><?php echo isset($header_record['driller_name'])? $header_record['driller_name'] : '' ?></td>
                            <td>Assistant Driller’s Signature </td>
                            <td width="20%"><?php echo isset($header_record['driller_signature'])? $header_record['driller_signature'] : '' ?></td>
                            </tr>
                            <tr>
                                <td>Rig Manager / Toolpusher Name </td>
                                <td width="20%"><?php echo isset($header_record['rig_manager_name'])? $header_record['rig_manager_name'] : '' ?></td>
                                <td>Rig Manager / Toolpusher Signature </td>
                                <td width="20%"><?php echo isset($header_record['rig_manager_signature'])? $header_record['rig_manager_signature'] : '' ?></td>
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

        var csrf_token = '<?php echo $this->security->get_csrf_hash() ?>';

        $('#date_inspection').datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(ev){
        });

        function save_checklist(confirmedYN = null){

            var data = $('#data_check_list_form').serializeArray();

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
</script>
