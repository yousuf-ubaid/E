<?php $this->load->helper('operation_ngo_helper'); ?>
<style type="text/css">
    .thumbnail {
        width: 70px;
        height: 100px;
        text-align: center;
        display: inline-block;
        margin: 0 10px 10px 0;
        float: left;
    }

    .required-img {
        width: 10px;
        height: 10px;
    }
</style>
<div class="row">
    <div class="col-md-5">
        <div class="box box-default" style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
            <div class="box-header with-border">
                <h3 class="box-title">Add New Attachment</h3>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="Doc_form_attachment" '); ?>
            <input type="hidden" name="ngoProposalID" value="<?php echo $ngoProposalID; ?>">

            <div class="box-body" style="background: #ffffff;">
                <div class="form-group">
                    <label for="document" class="col-sm-4 control-label">Description</label>

                    <div class="col-sm-8">
                        <textarea class="form-control" id="document" name="document" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="doc_file" class="col-sm-4 control-label">File</label>

                    <div class="col-sm-8">
                        <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <input type="hidden" name="test" id="test" value="">
                <button type="submit" class="btn btn-primary btn-sm pull-right">Upload</button>
            </div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="box box-default" style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
            <div class="box-header with-border">
                <h3 class="box-title">Project Proposal Attachments</h3>
            </div>
            <form class="form-horizontal">
                <div class="box-body" style="text-align: center; background: #ffffff;">
                    <?php
                    if (!empty($docDet)) {
                        foreach ($docDet as $doc) {
                            if ($doc['myFileName'] != '') {
                                $file = get_all_operationngo_images($doc['myFileName'],'uploads/ngo/attachments/','projectProposalAtt');
                         //       $file = base_url() . 'uploads/ngo/attachments/' . $doc['myFileName'];
                                $linkStart = '<i class="fa fa-times-circle pull-right" aria-hidden="true" style="color: red;" onclick="delete_attachment(' . $doc['attachmentID'] . ', \'' . $doc['myFileName'] . '\')"></i>
                                      <a href="' . $file . '" target="_blank">';
                                $linkEnd = '</a>';
                            } else {
                                $file = base_url() . 'images/doc1.ico';
                                $linkStart = '';
                                $linkEnd = '';
                            }

                            echo '<div class="thumbnail" >

                            ' . $linkStart . '
                                <img class="" src="' . base_url() . 'images/doc1.ico" style="width:50px; height:45; ">
                                <h6 style="margin: 2px;" class="text-muted text-center">' . $doc['attachmentDescription'] . '</h6>
                                <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                            ' . $linkEnd . '
                        </div>';
                        }
                    } else {
                        echo '<span style="text-align: center;font-size: 15px;font-weight: 800;">No Attachments Found </span>';
                    }

                    ?>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('#Doc_form_attachment').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                document: {validators: {notEmpty: {message: 'Attachment is required.'}}},
                doc_file: {
                    validators: {
                        file: {
                            maxSize: 5120 * 1024,  //5 MB
                            message: 'The Attachment size should be less than 5 MB.'
                        },
                        notEmpty: {message: 'File is required.'}
                    }
                }
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var formData = new FormData($("#Doc_form_attachment")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: '<?php echo site_url('OperationNgo/save_project_proposal_attachments'); ?>',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_project_proposal_attachments();
                        }, 400);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        });
    });


    function delete_attachment(attachmentid, FileName) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this Attachment!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('OperationNgo/delete_project_proposal_attachment'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': attachmentid, 'myFileName': FileName},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function () {
                                fetch_project_proposal_attachments();
                            }, 400);
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

</script>



