<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>

<div class="row">
    <?php echo form_open_multipart('', 'id="changerequests_attachment_form" class="form-inline"'); ?>
    <div class="col-sm-12" style="margin-left: 3%">
        <div class="col-sm-4">
            <div class="form-group">
                <input type="text" class="form-control" id="procrattachmentDescription"
                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                <input type="hidden" class="form-control" id="procr_documentid" name="documentID" value="PROCR">
                <input type="hidden" class="form-control" id="procr_document_name" name="document_name"
                       value="CHANGE REQUESTS ATTACHMENTS">
                <input type="hidden" class="form-control" id="procr_documentSystemCode" name="documentSystemCode" value="<?php echo $headerID ?>">
            </div>
        </div>
        <div class="col-sm-4" style="margin-top: -8px;">
            <div class="form-group">
                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                     style="margin-top: 8px;">
                    <div class="form-control" data-trigger="fileinput">
                        <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                        <span class="fileinput-filename"></span></div>
                    <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new"><span class="glyphicon glyphicon-plus"   aria-hidden="true"></span></span>
                        <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat"  aria-hidden="true"></span></span>
                        <input   type="file" name="document_file" id="document_file"></span>
                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                      aria-hidden="true"></span></a>
                </div>
            </div>
            <button type="button" class="btn btn-default" onclick="document_uplode_chreq()"><span
                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>

        </div>
    </div>

    <?php echo form_close(); ?>
</div>
<br>
<div class="row " id="attachemnt_tbl">
    <div class="col-sm-8" style="margin-left: 3%">
        <div class="table-responsive" style="width: 80%">
            <table class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                    <th style="text-align: center"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                    <th>Action</th><!--Type-->

                </tr>
                </thead>
                <tbody class="no-padding">
                <?php
                if(!empty($attachment))
                {
                $x=1;
                foreach ($attachment as $val){
                    $uploadtype = $this->config->item('ftp_image_uplod_local');
                    if($uploadtype ==3)
                    { 
                        $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');

                    }else{
                        $link = base_url().$val['myFileName'];
                    }

                 
                    ?>
                    <tr>
                        <td><?php echo $x?></td>
                        <td><?php echo $val['attachmentDescription']?></td>
                        <td><?php echo $val['myFileName'] ?></td>
                        <td class="text-center">
                    <a target="_blank" href="<?php echo $link?>" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_eoi_attachment('<?php echo $val['attachmentID']?> . ','<?php echo $val['myFileName']?>','<?php echo $val['documentSubID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td>

                    </tr>

                <?php
                $x++;
                }
                }else {?>
                <tr class="danger">
                <td colspan="4" class="text-center">No Attachment Found</td><!--No Attachment Found-->
                </tr>
                <?php } ?>


                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function document_uplode_chreq()
    {
        var formData = new FormData($("#changerequests_attachment_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
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
                    $('#procrattachmentDescription').val('');
                    changereq_attachment_view();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
</script>