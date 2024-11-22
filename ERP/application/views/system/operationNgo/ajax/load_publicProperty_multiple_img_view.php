<style type="text/css">
    .thumbnail {
        width: 120px;
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
                <h3 class="box-title">Images</h3>
            </div>
            <form class="form-horizontal">
                <input type="hidden" name="publicPropertyBeneID" id="hn_publicPropertyBeneID" value="<?php echo $publicPropertyBeneID; ?>">
                <div class="box-body" style="text-align: center; background: #ffffff;">
                    <?php
                    if (!empty($docDet)) {
                        foreach ($docDet as $doc) {
                            if($doc['isSelectedforPP'] == 1){
                                $status = "checked";
                            }else{
                                $status = "";
                            }
                            if ($doc['beneficiaryImage'] != '') {
                                $ppimage = get_all_operationngo_images($doc['beneficiaryImage'],'uploads/ngo/beneficiaryImage/','pubPropImg');

                                $file = base_url() . 'uploads/ngo/beneficiaryImage/' . $doc['beneficiaryImage'];
                                $isSubmitted = '<span class="label label-success" style="width: 100px;">Submitted</span>';
                                $linkStart = '<i class="fa fa-times-circle pull-right" aria-hidden="true" style="color: red;" onclick="delete_publicProperty_document(' . $doc['beneficiaryImageID'] . ', \'' . $doc['beneficiaryImage'] . '\')"></i>
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
                                <img class="" src="' .$ppimage . '" style="width:100px; height:85px; ">

                            <div class="skin skin-square" style="margin-top: 5%;">
                                <div class="skin-section extraColumns"><input id="isActive_'.$doc['beneficiaryImageID'].'" type="checkbox" '.$status.'
                                                                              data-caption="" class="columnSelected"
                                                                              name="isClosed" value="'.$doc['beneficiaryImageID'].'"><label for="checkbox">&nbsp;</label></div>
                            </div>
                            ' . $linkEnd . '
                        </div>';
                        }
                    }else{
                        echo '<span style="text-align: center;font-size: 15px;font-weight: 800;">No Images Found </span>';
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
                <h3 class="box-title">Choose / Capture</h3>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="empDoc_form" '); ?>
            <input type="hidden" name="publicPropertyBeneID" value="<?php echo $publicPropertyBeneID; ?>">

            <div class="box-body" style="background: #ffffff;">
                <div class="form-group">
                    <label for="doc_file" class="col-sm-4 control-label">Image</label>

                    <div class="col-sm-8">
                        <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here" accept="image/*;capture=camera">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm pull-right">Upload</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('.select2').select2();

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#empDoc_form').bootstrapValidator({
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
            var formData = new FormData($("#empDoc_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: '<?php echo site_url('OperationNgo/upload_publicProperty_multiple_image'); ?>',
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
                            fetch_beneficiary_imageView();
                        }, 400);
                    }else {
                        $('.btn-primary').removeAttr('disabled');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        });
    });

    $('input').on('ifChecked', function (event){
        update_pp_imageStatus(this.value,1);
    });
    $('input').on('ifUnchecked', function (event) {
        update_pp_imageStatus(this.value,0);
    });


    function delete_publicProperty_document(beneficiaryImageID, description) {
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
                    url: "<?php echo site_url('OperationNgo/delete_publicProperty_multiple_image'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'beneficiaryImageID': beneficiaryImageID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function () {
                                fetch_beneficiary_imageView();
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

    function update_pp_imageStatus(beneficiaryImageID, status) {
        var publicPropertyBeneID = $('#hn_publicPropertyBeneID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {publicPropertyBeneID: publicPropertyBeneID,beneficiaryImageID:beneficiaryImageID,status:status},
            url: "<?php echo site_url('OperationNgo/update_publicProperty_multiple_image'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_beneficiary_imageView();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

</script>

<?php
