<?php echo head_page($_POST['page_name'], false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="beneficiaryMaster_editView"></div>


<div class="modal fade " data-backdrop="static" id="modaluploadimages" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5><?php echo $this->lang->line('common_attachments'); ?> </h5><!--Attachments-->
        </div>
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: #F5F5F5">
            <?php echo form_open_multipart('', 'id="family_image_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                       name="empfamilydetailsID">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">Attachment </label><!--Attachment-->

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>

                    <div class="col-md-8">

                        <button type="button" class="btn btn-xs btn-primary" onclick="familyimage_uplode()"><span
                                class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>
                    </div>
                </div>
            </fieldset>
            </form>
        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default"
                    data-dismiss="modal"><?php echo $this->lang->line('common_cancel'); ?> </button><!--Cancel-->
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            benificiaryID = p_id;
            getBeneficiaryManagement_editView(benificiaryID);
        }

        $('.headerclose').click(function () {
            fetchPage('system/communityNgo/ngo_mo_communityBeneficiary', '', 'Beneficiary');
        });

    });

    function getBeneficiaryManagement_editView(benificiaryID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {benificiaryID: benificiaryID},
            url: "<?php echo site_url('CommunityNgo/load_comBeneficiaryManage_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#beneficiaryMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function beneficiary_edit_view_close() {

        fetchPage('system/crm/beneficiary_management', '', 'Beneficiary');

    }




</script>