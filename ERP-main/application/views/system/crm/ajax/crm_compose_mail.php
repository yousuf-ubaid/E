<link rel="stylesheet" href="<?php echo base_url('plugins/multipleattachment/fileinput.css'); ?>">
<style type="text/css">
    .thumbnail {
        width: 70px;
        height: 100px;
        text-align: center;
        display: inline-block;
        margin: 0 10px 10px 0;
        float: left;
    }
    .btn-filemultiup {
        position: relative;
        overflow: hidden;
    }

    .multipleattachmentbtn {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }


</style>
<?php echo form_open_multipart('', 'role="form" id="compose_mail_from"'); ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Compose New Message</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <input type="hidden" name="uid" value="<?php echo $id; ?>">
        <div class="form-group">
            <label class="title">To</label>
            <input class="form-control" placeholder="To:" name="to" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="title">CC</label>
            <input class="form-control" placeholder="CC:" name="cc" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="title">BCC</label>
            <input class="form-control" placeholder="BCC:" name="Bcc" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="title">Subject</label>
            <input class="form-control" placeholder="Subject:" name="Subject" autocomplete="off">
        </div>

        <div class="form-group">
            <label class="title">Body</label>
            <textarea id="compose-textarea" name="compose-textarea"  class="form-control customerTypeDescription" style="height: 300px"></textarea>
        </div>
        <br>
        <!--<div class="form-group col-sm-5">
            <input type="file" name="upload[]" multiple>
        </div>-->
        <div class="row" >
            <div class="form-group col-sm-6 files" id="files3">
                        <span class="btn btn-default btn-filemultiup">
                               Attachment  <input type="file" name="files3" class="multipleattachmentbtn" multiple/>
                             </span>
                <br/>
                <ul class="fileList"></ul>
            </div>
        </div>
      <!--  <div class="form-group col-sm-5 files" id="files1">
            <h2>Files 1</h2>
                <span class="btn btn-default btn-file">
                    Browse  <input type="file" name="photo[]" multiple />
                </span>
            <br />
            <ul class="fileList"></ul>
        </div>-->


        </form>
        <div class="form-group col-sm-12">
            <div id="status"></div>
            <div id="photos" class="row"></div>
        </div>



    </div>


    <!-- /.box-body -->
    <div class="box-footer">
        <div class="pull-right">
            <button class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
        </div>
        <button class="btn btn-default" type="button" onclick="discardReply()"><i class="fa fa-times"></i> Discard</button>
    </div><!-- /.box-footer -->



</div>

<div class="row">
    <div class="col-md-5">

    </div>

</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/multipleattachment/fileinput.min.js'); ?>"></script>
<script>
    var storedFiles = [];
    //$("#compose-textarea").wysihtml5();
    $(document).ready(function () {

        $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
            var fileIdCounter = 0;

            this.closest(".files").change(function (evt) {
                var output = [];

                for (var i = 0; i < evt.target.files.length; i++) {
                    fileIdCounter++;
                    var file = evt.target.files[i];
                    var fileId = sectionIdentifier + fileIdCounter;

                    filesToUpload.push({
                        id: fileId,
                        file: file
                    });

                    var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

                    output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
                };

                $(this).children(".fileList")
                    .append(output.join(""));

                //reset the input to null - nice little chrome bug!
                evt.target.value = null;
            });

            $(this).on("click", ".removeFile", function (e) {
                e.preventDefault();

                var fileId = $(this).parent().children("a").data("fileid");

                // loop through the files array and check if the name of that file matches FileName
                // and get the index of the match
                for (var i = 0; i < filesToUpload.length; ++i) {
                    if (filesToUpload[i].id === fileId)
                        filesToUpload.splice(i, 1);
                }

                $(this).parent().remove();
            });

            this.clear = function () {
                for (var i = 0; i < filesToUpload.length; ++i) {
                    if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                        filesToUpload.splice(i, 1);
                }

                $(this).children(".fileList").empty();
            }

            return this;
        };


        var filesToUpload = [];
        var files3Uploader = $("#files3").fileUploader(filesToUpload, "files3");
        $('#compose_mail_from').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                to: {validators: {notEmpty: {message: 'To is required'}}},/*To is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            var data = new FormData($("#compose_mail_from")[0]);

            for (var i = 0, len = filesToUpload.length; i < len; i++) {
                data.append("upload[]", filesToUpload[i].file);
            }
           /* $.each($(".email_attachment"), function(i, obj) {
                $.each(obj.files,function(i,file){
                var push =  data.append('photo[]', file);
                    array_push($a);
                });
            });*/
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CrmMailBox/compose_email'); ?>",
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
                        $('.btn-primary').prop('disabled', false);
                        setTimeout(function(){
                            fetchPage('system/crm/mail_box.php','Test','CRM');
                        }, 300);
                        $("#compose-textarea").wysihtml5();
                        files3Uploader.clear();
                    } else {
                        $('.btn-primary').prop('disabled', true);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        tinymce.init({
            selector: ".customerTypeDescription",
            height: 200,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],

            templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }]
        });

    });

    /*function readFile(input) {
        $("#status").html('Processing...');
        counter = input.files.length;
        for(x = 0; x<counter; x++){
            if (input.files && input.files[x]) {

                var reader = new FileReader();

                reader.onload = function (e) {
                    $("#photos").append('<div class="col-md-3 col-sm-3 col-xs-3"><i class="fa fa-times-circle " aria-hidden="true" style="color: red;" onclick="delete_image(e)"></i><img src="<?php echo base_url("images/crm/emailattachment.png")?>" class="img-thumbnail"> </div>');
                };

                reader.readAsDataURL(input.files[x]);
            }
        }
        if(counter == x){$("#status").html('');}

    }*/
    $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
        var fileIdCounter = 0;

        this.closest(".files").change(function (evt) {
            var output = [];

            for (var i = 0; i < evt.target.files.length; i++) {
                fileIdCounter++;
                var file = evt.target.files[i];
                var fileId = sectionIdentifier + fileIdCounter;

                filesToUpload.push({
                    id: fileId,
                    file: file
                });

                var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

                output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
            };

            $(this).children(".fileList")
                .append(output.join(""));

            //reset the input to null - nice little chrome bug!
            evt.target.value = null;
        });

        $(this).on("click", ".removeFile", function (e) {
            e.preventDefault();

            var fileId = $(this).parent().children("a").data("fileid");

            // loop through the files array and check if the name of that file matches FileName
            // and get the index of the match
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id === fileId)
                    filesToUpload.splice(i, 1);
            }

            $(this).parent().remove();
        });

        this.clear = function () {
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                    filesToUpload.splice(i, 1);
            }

            $(this).children(".fileList").empty();
        }

        return this;
    };

    (function () {
        var filesToUpload = [];
        var files1Uploader = $("#files1").fileUploader(filesToUpload, "files1");


    })()



</script>