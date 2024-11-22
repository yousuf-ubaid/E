<section class="content">
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" id="tmpCompanyID" value="0">
            <div id="div_loadCompanyAdminUsers" class="ajaxContainer"></div>
            <div id="mainContainer" class="ajaxContainer">
                <table id="company_table" class="table table-bordered table-striped table-condensed">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 10%">Module</th>
                        <th style="min-width: 15%">&nbsp;</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if ($modules) {
                        $i = 1;
                        foreach ($modules as $value) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $value['addonDescription'] ?></td>

                                <td>
                                    <a onclick="modaldetailopen(<?php echo $value['navigationMenuID'] ?>)"><span
                                                class="glyphicon glyphicon-pencil"></span></a>
                                    <a onclick="attachment_modal(<?php echo $value['navigationMenuID'] ?>,'Attachment','Attachment')"><span
                                                title="" rel="tooltip" class="glyphicon glyphicon-paperclip"
                                                data-original-title="Attachment"></span></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>
<script type="text/javascript">
    $(document).ready(function () {
        //company_table();
        /*$('#description').wysihtml5({
         toolbar: {
         "font-styles": true, // Font styling, e.g. h1, h2, etc.
         "emphasis": true, // Italics, bold, etc.
         "lists": true, // (Un)ordered lists, e.g. Bullets, Numbers.
         "html": false, // Button which allows you to edit the generated HTML.
         "link": false, // Button to insert a link.
         "image": false, // Button to insert an image.
         "color": false, // Button to change color of font
         "blockquote": true, // Blockquote
         }
         });*/

        tinymce.init({
            selector: "textarea",
            height: 300,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

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
    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload'); ?>",
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
                    attachment_modal($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val());
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function attachment_modal(documentSystemCode, document_name, documentID) {
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID},
                // beforeSend: function () {
                //     check_session_status();
                //     //startLoad();
                // },
                success: function (data) {
                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "");
                    $('#attachment_modal_body').empty();
                    if (!jQuery.isEmptyObject(data)) {
                        for (var i = 0; i < data.length; i++) {
                            type = '<i class="color fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>';
                            if (data[i]['fileType'] == '.xlsx') {
                                type = '<i class="color fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.xls') {
                                type = '<i class="color fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.xlsxm') {
                                type = '<i class="color fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.doc') {
                                type = '<i class="color fa fa-file-word-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.docx') {
                                type = '<i class="color fa fa-file-word-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.ppt') {
                                type = '<i class="color fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.pptx') {
                                type = '<i class="color fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.jpg') {
                                type = '<i class="color fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.jpeg') {
                                type = '<i class="color fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.gif') {
                                type = '<i class="color fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.png') {
                                type = '<i class="color fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
                            } else if (data[i]['fileType'] == '.txt') {
                                type = '<i class="color fa fa-file-text-o fa-2x" aria-hidden="true"></i>';
                            }

                            $('#attachment_modal_body').append('<tr><td>' + (i + 1) + '</td><td>' + data[i]['file'] + '</td><td>' + data[i]['description'] + '</td><td class="text-center">' + type + '</td><td class="text-center"><a target="_blank" href="<?php echo addon_path() ?>' + data[i]['file'] + '" ><i class="fa fa-download fa-2x" aria-hidden="true"></i></a></td></tr>');
                            //<td class="text-center"><a onclick="delete_attachment('+data[i]['attachmentID']+',\''+data[i]['myFileName']+'\','+data[i]['documentSystemCode']+',\''+data[i]['document_name']+'\',\''+data[i]['documentID']+'\')" ><i class="fa fa-trash-o fa-2x" style="color:rgb(209, 91, 71);" aria-hidden="true"></i></a></td>
                        }
                    } else {
                        $('#attachment_modal_body').append('<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>');
                    }
                    $("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }


    function modaldetailopen(navID) {
        $('#navigationMenuID').val(navID);
        //$('iframe').contents().find('.wysihtml5-editor').html('');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/getModuleDetail'); ?>",
            data: {navID: navID,companyid:$('#companyID').val()},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('iframe').contents().find('.wysihtml5-editor').html(data[0]);
                tinyMCE.activeEditor.setContent("");
                if (data[0] != null) {
                    tinyMCE.activeEditor.setContent(data[0]);
                }
                stopLoad();
                $('#moduleDetail').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function submitupdate() {
        tinymce.triggerSave();
        var companyid=$('#companyID').val();
        $('#companyidhn').val(companyid);
        var formData = new FormData($("#form_nav")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Dashboard/update_moduleDescirption'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                console.log(data);
                if (data['error'] == 0) {

                    myAlert('s', data['message']);

                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

</script>

