<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs" id="main-tabs">
       
        <li class="active"><a href="#prhandover" data-toggle="tab" onclick="fetch_projecthandover('<?php echo $headerID?>')">Project Hand Over</a></li>
        <li><a href="#projectsummary" data-toggle="tab" onclick="projectsummary('<?php echo $headerID?>')">Completion Certificate</a></li>
        <li><a href="#retentiondetails" data-toggle="tab" onclick="fetch_project_retention_details(<?php echo $boqmaster['projectID']?>)">Retention Details</a></li>
        <li><a href="#invoicedetails" data-toggle="tab" onclick="fetch_project_invoice_details(<?php echo $boqmaster['projectID']?>)">InvoiceDetails</a></li>
        <li><a href="#maintenancewarranty" data-toggle="tab" onclick="fetch_project_maintenancewarranty()">Maintenance / Warranty</a></li>
        <li><a href="#handingover" onclick="" data-toggle="tab">Project Closure</a></li>
    </ul>
</div>



<br>
<div class="tab-content">
    <div class="tab-pane" id="handingover">
        <input type="hidden" id="headerID" name="headerID" >
        <header class="head-title">
            <h2>Lessons Learnt</h2>
        </header>
        <?php echo form_open('', 'role="form" id="lessonslearned_form"'); ?>
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
    <textarea class="form-control customerTypeDescription" rows="5" name="lessonslearned" id="lessonslearned">
        <?php echo $boqmaster['lessonslearned']?>
    </textarea>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="form-group col-sm-12">
                <button class="btn btn-primary pull-right" type="button" onclick="save_lessonslearnt(<?php echo $headerID?>)">save</button>
            </div>
        </div>
        </form>
        <br>
        <header class="head-title">
            <h2>Template References</h2>
        </header>

        <div class="row">
            <div class="col-md-12">
                <div id="project_closureattachment"> </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="completioncertificate">

    </div>
    <div class="tab-pane" id="retentiondetails">
        <div class="col-md-12">
            <div id="project_retentiondetails"> </div>
        </div>
    </div>

    <div class="tab-pane" id="invoicedetails">
        <div class="col-md-12">
            <div id="project_invoicedetails"> </div>
        </div>
    </div>

    <div class="tab-pane" id="projectsummary">
        <div class="row ">
            <div class="col-md-12">
                <div id="projectsummary_view"></div>
            </div>
        </div>

    </div>

     <div class="tab-pane active" id="prhandover">
        <div class="row ">
            <div class="col-md-12">
                <div id="projecthandover"></div>
            </div>
        </div>

    </div>
    <div class="tab-pane" id="maintenancewarranty">
        <div class="row ">
            <div class="col-md-12">
                <div id="maintenancewarrantyattachment"></div>
            </div>
        </div>

    </div>


    
</div>


<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">

    $(document).ready(function() {
        fetch_project_closureattachment();
        fetch_project_maintenancewarranty();
        fetch_projecthandover('<?php echo $headerID?>');
    });
    function fetch_no_ofheads(designationID) {
        var headerID = $('#headerID').val();
        var boq_detailID =$('#boq_detailID').val();
        if(designationID)
        {
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'headerID':headerID,'boq_detailID':boq_detailID,'designationID':designationID},
                    url: "<?php echo site_url('Boq/fetch_noofheads'); ?>",
                    beforeSend: function () {
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#noofavailableheads').val((data['empcount']-data['requirednoofheads']));
                            $('#noofrequiredheads').val('');
                        }
                    }
                });
        }else
        {
            $('#noofavailableheads').val('');
        }

    }
    function validate_no_ofheads(noofeq) {

        var noofavailableheads =  $('#noofavailableheads').val();
        if(parseFloat(noofavailableheads) < parseFloat(noofeq))
        {
            myAlert('w','No Of Required Heads greater than No Of Available Heads');
        }
    }

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
    function fetch_project_closureattachment()
    {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {headerID: headerID},
            url: "<?php echo site_url('Boq/fetch_project_closureattachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_closureattachment').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    function document_uplode_clos()
    {
        var formData = new FormData($("#clo_attachment_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
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
                    $('#cloattachmentDescription').val('');
                    fetch_project_closureattachment();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
    function fetch_project_invoice_details(projectID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectID: projectID},
            url: "<?php echo site_url('Boq/fetch_project_invoices'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_invoicedetails').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function fetch_project_retention_details(projectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectID: projectID},
            url: "<?php echo site_url('Boq/fetch_project_retention'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_retentiondetails').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function generateReportPdf_prosum() {
        var form = document.getElementById('project_summary');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_prosum_pdf'); ?>';
        form.submit();
    }
    function save_projectsummary() {
        var data = $('#project_summary').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_tem_repdetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1],data[2])
                if(data[0]=='s')
                {
                    projectsummary('<?php echo $headerID?>');
                    $('#tempmasterID').val(data[2]);
                }


            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
</script>