<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>
<div class="row" id="show_add_files_button">
    <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>Attachments</h4></div>

</div>
<br>
<div class="row " id="add_attachemnt_show">
    <?php echo form_open_multipart('', 'id="logistic_attachment_form" class="form-inline"'); ?>


    <div class="col-sm-12" style="margin-left: 3%">
        <div class="col-sm-4">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="document_ID" class="title">Document</label>
                </div>
                <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <select class="filters select2 form-control" required name="document_ID" id="document_ID">
                            <?php
                            if(!empty($documentdrop))
                            {
                                foreach ($documentdrop as $valdocdrop) {
                                    echo '<option value="' . $valdocdrop['docID'] . '">' . $valdocdrop['description'] . '</option>';
                                }
                            }else {
                                echo '<option value=" ">Select Document</option>';
                            }
                            ?>
                            </select>

                            <span class="input-req-inner"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="Description..." style="width: 240%;">

                <input type="hidden" class="form-control" id="jobID" name="jobID" value="<?php echo $jobID; ?>">
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
            <button type="button" class="btn btn-default" onclick="logistic_document_uplode()"><span
                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>

        </div>
    </div>

    <?php echo form_close(); ?>
</div>
<br>
<div class="row " id="attachemnt_tbl">
    <div class="col-sm-12" style="margin-left: 3%">
    <div class="table-responsive" style="width: 80%">
        <table class="table table-striped table-condensed table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                <th>Action</th><!--Type-->

            </tr>
            </thead>
            <tbody id="job_request_attachment" class="no-padding">
            <tr class="danger">
                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
            </tr>
            </tbody>
        </table>
    </div>
    </div>
</div>
<script type="text/javascript">
    $('.select2').select2();

</script>