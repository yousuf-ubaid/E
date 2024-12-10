<?php echo form_open('', 'role="form" id="reply_mail_from"'); ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Reply Mail</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <input type="hidden" name="uid" value="<?php echo $id; ?>">
        <div class="form-group">
            <label class="title">To</label>
            <input class="form-control" placeholder="To:" name="to" value="<?php echo $from; ?>">
        </div>
        <div class="form-group hide">
            <label class="title">CC</label>
            <input class="form-control" placeholder="CC:" value="<?php echo $cc; ?>">
        </div>
        <div class="form-group hide">
         <label class="title">BCC</label>
            <input class="form-control" placeholder="BCC:" value="<?php echo $bcc;  ?>">
        </div>
        <div class="form-group">
            <label class="title">Subject</label>
            <input class="form-control" placeholder="Subject:" name="Subject" value="<?php echo $subject; ?>">
        </div>

        <div class="form-group">
            <label class="title">Body</label>
            <textarea id="compose-textarea" name="compose-textarea"  class="form-control customerTypeDescription" style="height: 300px"></textarea>
        </div>
        </form>
    </div><!-- /.box-body -->
    <div class="box-footer">
        <div class="pull-right">
            <button class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
        </div>
        <button class="btn btn-default" type="button" onclick="discardReply()"><i class="fa fa-times"></i> Discard</button>
    </div><!-- /.box-footer -->
</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script>
    //$("#compose-textarea").wysihtml5();
    $(document).ready(function () {
    $('#reply_mail_from').bootstrapValidator({
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
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CrmMailBox/save_reply_mail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    setTimeout(function(){
                        fetchPage('system/crm/mail_box.php','Test','CRM');
                    }, 300);
                    $('.btn-primary').prop('disabled', false);
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
</script>