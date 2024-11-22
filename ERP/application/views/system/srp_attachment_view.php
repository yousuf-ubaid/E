<?php echo head_page('Add On Master', false);

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/dist/css/jasny-bootstrap.min.css'); ?>" />
<div class="row">
    <div class="col-md-8">
        <?php echo form_open_multipart('','id="image_uplode_form" class="form-inline"'); ?>
        <div class="form-group">
            <label for="attachmentDescription">Description</label>
            <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="Description...">
            <input type="hidden" class="form-control" value="PR"  id="documentID" name="documentID">
            <input type="hidden" class="form-control" value="1"  id="documentSystemCode" name="documentSystemCode">
        </div>
        <div class="form-group">
            <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="margin-top: 10px;">
                <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span><span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span><input type="file" name="document_file" id="document_file" ></span>
                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </div>
        </div>
        <button type="button" class="btn btn-default" onclick="document_uplode()" ><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>

        </form>
    </div>
    <div class="col-md-4">
        <table class="<?php echo table_class(); ?>"><tbody id="detail_table_attachment"><tr class="danger"><td class="text-center">&nbsp;</td><td> No Attachments Found </td></tr></tbody></table>
    </div>
</div>






<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    $( document ).ready(function() {
        load_attachment();

    });


    function load_attachment(){

            $.ajax({
                async : false,
                type : 'post',
                dataType : 'json',
                data : {'RequestID':1,'code':'PR'},
                url :"<?php echo site_url('Upload/load_attachment'); ?>",

                success : function(data){
                    $('#detail_table_attachment').empty();
                    if(!jQuery.isEmptyObject(data['attachments'])){
                        x=1;
                        $.each(data['attachments'], function (key, value) {
                            $('#detail_table_attachment').append('<tr><td><a target="_blank" href="'+data['uploads_url']+'/'+value['myFileName']+'">' + x +'. &nbsp;<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>&nbsp;&nbsp;'+ value['attachmentDescription'] + '</a><span class="pull-right"><a onclick="delete_attachment('+ value['attachmentID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></span></td></tr>');
                            x++;
                        });
                    }else{
                        $('#detail_table_attachment').append('<tr class="danger"><td class="text-center"><b>Attachment Not Available</b></td></tr>');
                    }
                },error : function(){
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });

    }

    function delete_attachment(id){
        if (id) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async : false,
                        type : 'post',
                        dataType : 'json',
                        data : {'attachmentID':id},
                        url :"<?php echo site_url('Upload/delete_attachment'); ?>",
                        success : function(data){
                            refreshNotifications(true);
                            load_attachment();
                        },error : function(){
                            swal("Cancelled", "Your  file is safe :)", "error");
                        }
                    });
                });
        }
    }



    function document_uplode(){
       // $('#documentSystemCode').val(purchaseRequestID);
        var formData = new FormData($("#image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Upload/do_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                $('#image_uplode_form')[0].reset();
                fetchPage('system/srp_attachment_view','Test','Attachment');
                //load_attachment();
            },
            error: function (data) {
                HoldOn.close();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }


</script>