<style type="text/css">
    .thumbnail {
        width: 100px;
        height: 140px;
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

    <div class="col-md-7">
        <div class="box box-default" style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
            <div class="box-header with-border">
                <h3 class="box-title">Documents</h3>
            </div>
            <form class="form-horizontal">
                <div class="box-body" style="text-align: center; background: #ffffff;">
                    <?php
                    if (!empty($docDet)) {


                        foreach ($docDet as $doc) {
                            $reqImg = ($doc['isMandatory'] == 1) ? '<img class="required-img" src="' . base_url() . 'images/required.png"/>' : '';

                            if ($doc['FileName'] != '') {
                                $ppimage = get_all_operationngo_images($doc['FileName'],'documents/ngo/','pubPropDoc');

                              //  $file = base_url() . 'documents/ngo/' . $doc['FileName'];
                                $isSubmitted = '<span class="label label-success" style="width: 100px;">Submitted</span>';
                                $linkStart = '<i class="fa fa-times-circle pull-right" aria-hidden="true" style="color: red;" onclick="delete_publicProperty_document(' . $doc['DocDesFormID'] . ', \'' . $doc['DocDescription'] . '\')"></i>
                                      <a href="' . $ppimage . '" target="_blank">';
                                $linkEnd = '</a>';
                            } else {
                                $file = base_url() . 'images/doc1.ico';
                                $isSubmitted = '<span class="label label-danger" style="width: 100px;">Not Submitted</span>';
                                $linkStart = '';
                                $linkEnd = '';
                            }


                            echo '<div class="thumbnail" >

                            ' . $linkStart . '
                                <img class="" src="' . base_url() . 'images/doc1.ico" style="width:80px; height:65px; ">
                                <h6 style="margin: 2px;" class="text-muted text-center">' . $doc['DocDescription'] . ' ' . $reqImg . '</h6>
                                <h6 style="margin: 2px;" class="text-muted text-center">' . $isSubmitted . '</h6>
                                <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                            ' . $linkEnd . '
                        </div>';
                        }
                    }else{
                        echo '<span style="text-align: center;font-size: 15px;font-weight: 800;">No Documents Found </span>';
                    }

                    ?>
                    <!--<div class="thumbnail" >
                        <a href="<?php /*echo base_url(); */ ?>images/doc1.ico" target="_blank">
                            <img class=" " src="<?php /*echo base_url(); */ ?>images/doc1.ico" style="width:100px; height:65px; ">
                            <h6 style="margin: 2px;" class="text-muted text-center">Employee passport <img class="required-img" src="<?php /*echo base_url(); */ ?>images/required.png"/> </h6>
                            <h6 style="margin: 2px;" class="text-muted text-center"><span class="label label-danger" style="width: 100px;">Not Submitted</span></h6>
                            <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                        </a>
                    </div>-->
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-5">
        <div class="box box-default" style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
            <div class="box-header with-border">
                <h3 class="box-title">Add New</h3>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="empDoc_form_document" '); ?>
            <input type="hidden" name="publicPropertyBeneID" value="<?php echo $publicPropertyBeneID; ?>">
            <input type="hidden" name="projectID" value="<?php echo $projectID; ?>">

            <div class="box-body" style="background: #ffffff;">
                <div class="form-group">
                    <label for="document" class="col-sm-4 control-label">Document</label>

                    <div class="col-sm-8">
                        <?php
                        $companyID = $this->common_data['company_data']['company_id'];
                        $division = $this->db->query("SELECT ddm.DocDesID as DocDesID,ddm.DocDescription as docMaster,dds.DocDesSetupID as DocDesSetupID FROM srp_erp_ngo_documentdescriptionsetup dds INNER JOIN
                        srp_erp_ngo_documentdescriptionmaster ddm ON ddm.DocDesID =dds.DocDesID WHERE dds.companyID = {$companyID} AND projectID = {$projectID}")->result_array();
                        $data_arr = array('' => 'Select a Document');
                        if (!empty($division)) {
                            foreach ($division as $row) {
                                $data_arr[trim($row['DocDesID'] ?? '')] = trim($row['docMaster'] ?? '');
                            }
                        }
                        echo form_dropdown('document', $data_arr, '', 'class="form-control select2" id="document"');
                        ?>
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
                <button type="submit" class="btn btn-primary btn-sm pull-right">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('.select2').select2();

        $('#empDoc_form_document').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                document: {validators: {notEmpty: {message: 'Document is required.'}}},
                doc_file: {
                    validators: {
                        file: {
                            maxSize: 5120 * 1024,   // 5 MB
                            message: 'he selected file is not valid.'/*The selected file is not valid*/
                        },
                        notEmpty: {message: 'File is required.'}/*File is required*/
                    }
                }
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var formData = new FormData($("#empDoc_form_document")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: '<?php echo site_url('OperationNgo/save_publicProperty_document'); ?>',
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
                            fetch_ngo_document();
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

    function delete_publicProperty_document(DocDesFormID, description) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('OperationNgo/delete_publicProperty_document'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'DocDesFormID': DocDesFormID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function () {
                                fetch_ngo_document();
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




<?php
