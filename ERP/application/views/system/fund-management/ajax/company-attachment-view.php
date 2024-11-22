<style>
    #ul-list li{
        list-style: none;
        padding-right: 25px;
        margin-bottom: 15px;
        float: left;
    }

    .post-doc:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .post-doc {
        display: flex;
        width: 300px;
        height: 110px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        border-top: 1px solid #ddd;
    }

    .post-doc-left {
        width: 90px;
        position: relative;
    }

    .post-doc-right {
        width: 70%;
        background-color: #FEFEFE;
        color: #484855;
    }

    .post-doc-right_body {
        line-height: 2;
        padding: 4px;
    }

    .post-doc-right_footer {
        justify-content: space-between;
        padding: 4px;
    }

    .post-doc-right_footer_btn {
        font-size: 12px;
        margin-right: 2px;
    }

    .required-img {
        width: 10px;
        height: 10px;
    }

    .label-danger2{ background-color: #c75f53 !important; }

    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }
</style>

<ul id="ul-list">
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$logo = 'images/fm_doc_logo.png';
$submitted =  $this->lang->line('common_submited');
$not_sub = $this->lang->line('common_not_submited');
$documentSystemCode = $this->input->post('documentSystemCode');

// If ($documentSystemCode == FM_Company ) => srp_erp_fm_companymaster.id
// If ($documentSystemCode == Investment ) => srp_erp_fm_master.id

if(!empty($attachData)){
    foreach($attachData as $doc){

        $attachmentID = $doc['attachmentID'];
        $description = $doc['description'];
        $docSysID = $doc['docSetupID'];
        $expiryDate = $doc['docExpiryDate'];
        $expiryDateDis = ($expiryDate != null)? convert_date_format($expiryDate): ' - ';
        $remainingDays = '';

        if($expiryDate != null){
            $today = date('Y-m-d');
            $date1 = new DateTime($expiryDate);
            $date2 = new DateTime($today);
            $diff = $date2->diff($date1)->format("%a");
            $remainingDays = intval($diff);

            if($today < $expiryDate){
                $remainingDays = 'Remaining Days :  <b> &nbsp; &nbsp; '.$remainingDays.' </b>';

            }else{
                $remainingDays = '<span class="label label-danger2">Elapsed Days : &nbsp; <b> '.$remainingDays.' </b></span>';
            }
        }

        $reqImg = ($doc['isMandatory'] == 1)? '<img class="required-img" src="'.base_url().'images/required.png"/>' : '';

        $data = '';

        if(!empty($doc['myFileName'])){
            $file = base_url().'documents/'.$doc['myFileName'];
            $link = generate_encrypt_link_only($file);
            $data = '<span class="label label-success" style="width: 100px;">'.$submitted.'</span>';
            $data .= '<a class="btn btn-default btn-xs pull-right" target="blank" href="'.$link.'" >';
            $data .= '<i class="fa fa-download" aria-hidden="true" title="Download"></i></a>';
            $data .= '<button class="btn btn-default btn-xs pull-right" onclick="edit_upload_attachment('.$attachmentID.', '.$docSysID.', \''.$description.'\', \''.$expiryDateDis.'\')">';
            $data .= '<i class="fa fa-pencil" aria-hidden="true" title="Edit"></i></button>';
            $data .= '<br/>Expiry Date : <b>'.$expiryDateDis.'</b><br/>'.$remainingDays;

        }
        else{
            $data = '<span class="label label-danger2" style="width: 100px;">'.$not_sub.'</span>';
            $data .= '<button class="btn btn-default btn-xs pull-right" onclick="open_upload_attachment_modal('.$docSysID.', \''.$description.'\')">';
            $data .= '<i class="fa fa-cloud-upload" aria-hidden="true" title="Upload"></i>';
            $data .= '</button>';
        }


        echo '<li >
                  <div class="post-doc">
                        <div class="post-doc-left">
                            <img src="'.base_url().$logo.'" alt="Avatar" style="width:100%; padding-top: 4px;">
                        </div>
                        <div class="post-doc-right">
                            <div class="post-doc-right_body">
                                <b style="font-size: 14px ">'.$description.' &nbsp; '.$reqImg.'</b>
                            </div>
                            <div class="post-doc-right_footer">'.$data.'</div>
                        </div>
                  </div>
              </li>';
    }
}
else{
    echo '<div class="search-no-results">'. $this->lang->line('fn_man_there_no_attachment_setup_to_diplay').'</div>';
}

?>
</ul>

<div class="modal fade" id="attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_add_attachments');?></h4>
            </div>

            <?php echo form_open('','role="form" class="form-horizontal" id="upload_form" '); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('common_document');?><!--Document--></label>
                        <div class="col-sm-6">
                            <input type="text" name="" class="form-control" id="docDes" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="doc_file" class="col-sm-4 control-label"><?php echo $this->lang->line('common_file');?><!--File--></label>
                        <div class="col-sm-6">
                            <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="expireDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="expireDate" value=""
                                       class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="docSysID" class="form-control" id="docSysID" value="">
                    <input type="hidden" name="documentSystemCode" value="<?php echo $documentSystemCode ?>">
                    <button type="button" class="btn btn-primary btn-sm" onclick="upload_attachment()"><?php echo $this->lang->line('common_Upload');?></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_upload_attachment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_edit_attachments');?></h4>
            </div>

            <?php echo form_open('','role="form" class="form-horizontal" id="edit_upload_form" '); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('common_document');?><!--Document--></label>
                    <div class="col-sm-6">
                        <input type="text" name="" class="form-control" id="edit_docDes" value="" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expireDate" class="col-sm-4 control-label"><?php echo $this->lang->line('common_expire_date');?><!--Expire Date--></label>
                    <div class="col-sm-6">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="edit_expiryDate" value="" id="edit_expiryDate"
                                   class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="attachID" class="form-control" id="edit_attachID" value="">
                <input type="hidden" name="docSysID" class="form-control" id="edit_docSysID" value="">
                <input type="hidden" name="documentSystemCode" value="<?php echo $documentSystemCode ?>">
                <button type="button" class="btn btn-primary btn-sm" onclick="update_attachmentDetails()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });


    function open_upload_attachment_modal(docSysID, des) {
        $('#attachment_modal').modal('show');

        $('#docDes').val(des);
        $('#docSysID').val(docSysID);
    }

    function edit_upload_attachment(attachID, docSysID, des, exDate) {
        $('#edit_upload_attachment').modal('show');

        $('#edit_docDes').val(des);
        $('#edit_attachID').val(attachID);
        $('#edit_docSysID').val(docSysID);
        $('#edit_expiryDate').val(exDate);
    }

    function update_attachmentDetails(){
        var formData =  $("#edit_upload_form").serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Fund_management/update_attachmentDetails'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    $('#edit_upload_attachment').modal('hide');
                    setTimeout(function () { get_attachments_details(); },400);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function upload_attachment(){
        var formData = new FormData($("#upload_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: '<?php echo site_url('Fund_management/document_upload'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's'){
                    $('#attachment_modal').modal('hide');
                    setTimeout(function () { get_attachments_details(); },400);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>

<?php
