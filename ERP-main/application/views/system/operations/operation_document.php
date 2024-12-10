<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_others_master', $primaryLanguage);
echo head_page('Operation Documents' ,false);
$this->load->library('s3');
$CI =& get_instance();

$opDocuments = get_operationDocuments();
?>
<style type="text/css">
    .thumbnail{
        width:100px;
        height:120px;
        text-align:center;
        display:inline-block;
        margin:0 10px 10px 0;
        float: left;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDocument_modal_operation()" >
            <i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add'); ?> <!--Add-->
        </button>
    </div>
</div>
<hr>
<form class="form-horizontal">
    <div class="box-body" style="text-align: center; background: #ffffff;">
        <?php

        foreach($opDocuments as $doc){

            /*$file = base_url().'documents/hr_documents/'.$doc['documentFile'];
            $link=generate_encrypt_link_only($file);*/
            $file = $doc['documentFile'];
            $link = $empImage = $CI->s3->createPresignedRequest($file, '+1 hour');
            $linkStart = '<i class="fa fa-times-circle pull-right" aria-hidden="true" onclick="removeDocument('.$doc['id'].', \''.$doc['documentDescription'].'\')"></i>';
            $linkStart .= '<span class="glyphicon glyphicon-pencil pull-right" onclick="editDocument('.$doc['id'].', \''.$doc['documentDescription'].'\')"';
            $linkStart .=  'style="color:#3c8dbc;" data-original-title="Edit"></span>';
            $linkStart .=  '<a href="'.$link.'" target="_blank">';

            $linkEnd = '</a>';

            echo '<div class="thumbnail" >
                    '.$linkStart.'
                        <img class="" src="'.base_url().'images/doc1.ico" style="width:80px; height:65px; ">
                        <h6 style="margin: 2px;" class="text-muted text-center">'.$doc['documentDescription'].' </h6>
                        <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                        <h6 style="margin: 2px;" class="text-muted text-center"></h6>
                    '.$linkEnd.'
                </div>';
        }

        ?>
    </div>
</form>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_documents" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Documents</h4>
            </div>
            <form class="form-horizontal" id="add-documents-form" >
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_document');?><!--Document--></label>
                        <div class="col-sm-8">
                            <input type="text" name="documentName" id="documentName" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="doc_file" class="col-sm-4 control-label">
                            <?php echo $this->lang->line('emp_documents_file');?><!--File-->
                        </label>
                        <div class="col-sm-8">
                            <input type="file" name="doc_file" class="form-control" id="doc_file" placeholder="Brows Here">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('emp_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="max-width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_edit_document_description');?><!--Edit Document Description--></h4>
            </div>

            <form class="form-horizontal" id="edit-documents-form" >
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document" class="col-sm-4 control-label"><?php echo $this->lang->line('emp_documents_document');?><!--Document--></label>
                        <div class="col-sm-8">
                            <input type="text" name="documentName" id="edit-documentName" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateDocument()"><?php echo $this->lang->line('emp_update');?><!--Update--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/operations/operation_document','Test','Operations');
        });


        $('#add-documents-form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_name_is_required');?>.'}}},/*Document name is required*/
                doc_file: {
                    validators: {
                        file: {
                            maxSize: 5120 * 1024,   // 5 MB
                            message: '<?php echo $this->lang->line('common_the_selected_file_is_not_valid');?>'
                        },
                        notEmpty: {message: '<?php echo $this->lang->line('common_file_is_required');?>.'}/*File is required*/
                    }/*The selected file is not valid*/
                }
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();

            var formData = new FormData($("#add-documents-form")[0]);

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: '<?php echo site_url('Jobs/operation_document_save'); ?>',
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
                        $('#new_documents').modal('hide');
                        setTimeout(function () {
                            fetchPage('system/operations/operation_document','Test','Operation');
                        },400);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        });
    });

    function openDocument_modal_operation(){
        $('#setup-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_documents').modal({backdrop: "static"});
    }


    function editDocument(id, description){
        $('#hidden-id').val( id );
        $('#edit-documentName').val( $.trim(description) );


        $('#editModal').modal({backdrop: "static"});
    }

    function removeDocument(id, description){
        swal({
               title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Jobs/delete_operationDocument'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            setTimeout(function () {
                                fetchPage('system/operations/operation_document','Test','Operation');
                            },400);
                         }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function updateDocument(){
        var postData = $('#edit-documents-form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Jobs/edit_operationDocument'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');

                    setTimeout(function () {
                        fetchPage('system/operations/operation_document','Test','Operation');
                    },400);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }


</script>

<?php
