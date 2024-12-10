<br>
<header class="head-title">
    <h2> Quantity Surveying</h2>
</header>

<div class="row" style="margin-top: 10px;">
    <?php echo form_open('', 'role="form" id="qs_comment_form"'); ?>
    <input type="hidden" name="headerID" id="headerID" value="<?= $headerID ?>">
    <div class="col-md-12">
        <textarea class="form-control quantitysurveying" rows="6" name="quantitysurveying" id="quantitysurveying"><?= $qscomment ?></textarea>
    </div>
    </form>
</div>
<br>
<div class="row pull-right">
    <div class="col-md-12">
        <button type="button" onclick="save_quantitysurveying();" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
            <!--Save-->
        </button>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <header class="head-title">
            <h2>QUANTITY SURVEYING ATTACHMENT</h2>
        </header>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="qsattachment"> </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    tinymce.init({
        selector: ".quantitysurveying",
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

    function save_quantitysurveying() {
        tinymce.triggerSave();
        var data = $('#qs_comment_form').serializeArray();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/update_qscomment'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1])
                if (data[0] == 's') {
                    show_quantity_surve()
                }

            },
            error: function() {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
            }
        });
    }
</script>