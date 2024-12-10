<?php
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$contractor_arr = fetch_ngo_contractor();
?>
<?php /*echo '<pre>'; print_r($proposalid); echo '</pre>'; die();  */?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<?php echo form_open('', 'role="form" id="project_proposal_convert_to_project"'); ?>
<input type="hidden" name="proposalid" id="proposalid" value="<?php echo $proposalid ?>">
<input type="hidden" name="type" id="type">
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>PROJECT DETAILS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">

                <div class="form-group col-sm-2">
                    <label class="title">Project System Code</label>
                </div>
                <div class="form-group col-sm-3">

                      <input type="text" name="systemCode" id="systemCode" class="form-control"
                             placeholder="Project System Code" readonly>

                </div>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Project Name</label>
            </div>

            <div class="form-group col-sm-3">

                      <input type="text" name="projectname" id="projectname" class="form-control" readonly>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">
                <div class="form-group col-sm-2">
                    <label class="title">Created Date</label>
                </div>
                <div class="form-group col-sm-3">

                    <input type="text" name="createddate" id="createddate" class="form-control" readonly>

                </div>
            </div>
        </div>
        <hr>
        <div class="row proposalfromto" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">
                <div class="form-group col-sm-2">
                    <label class="title">Proposal Period From</label>
                </div>
                <div class="form-group col-sm-3">
                    <input type="text" name="proposalfrom" id="proposalfrom" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Proposal Period To</label>
            </div>
            <div class="form-group col-sm-3">
                <input type="text" name="proposalto" id="proposalto" class="form-control" readonly>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">
                <div class="form-group col-sm-2">
                    <label class="title">Project Period From</label>
                </div>
                <div class="form-group col-sm-3">
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input onchange="" type="text" name="projectfrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="projectfrom" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Project Period To</label>
            </div>
            <div class="form-group col-sm-3">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input onchange="" type="text" name="projectto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="projectto" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
           <div class="proposalcontra">
            <div class="" id="linkcontact_system">
                <div class="form-group col-sm-2">
                    <label class="title">Proposal Contractor</label>
                </div>
                <div class="form-group col-sm-3">

                      <input type="text" name="proposalcontractor" id="proposalcontractor" class="form-control"
                             readonly>

                </div>
            </div>
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Project Contractor</label>
            </div>
            <div class="form-group col-sm-3">
                          <!--  <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-contractor"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>

                            </span>-->
                    <?php echo form_dropdown('contractorID', $contractor_arr, '', 'class="form-control select2" id="contractorID"'); ?>
                    <th style="width: 40px;">

                    </th>
                </div>
            <div class="projectloadcls">
            <div class="form-group col-sm-2">
                <label class="title">Total Project Cost</label>
            </div>
            <div class="form-group col-sm-3">
                <input type="text" name="totalprojectcostproject" id="totalprojectcostproject" class="form-control number amount" onkeypress="return validateFloatKeyPress(this,event)">
            </div>
            </div>

        </div>
        <div class="proposalcostadd">
        <hr>
        <div class="row" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">

                <div class="form-group col-sm-2">
                    <label class="title">Total Proposal Cost</label>
                </div>
                <div class="form-group col-sm-3">
                    <input type="text" name="totalproposal" id="totalproposal" class="form-control" readonly>
                </div>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Total Project Cost</label>
                </div>
                <div class="form-group col-sm-3">
                    <input type="text" name="totalprojectcost" id="totalprojectcost" class="form-control number amount" onkeypress="return validateFloatKeyPress(this,event)">
                </div>


        </div>
        </div>
            <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        Project Description </h2>
                </header>
                <div class="row" style="margin-top: 10px;">

                            <div class="form-group col-sm-12" style="margin-top: 5px;">
                        <textarea class="form-control customerTypeDescription" rows="5" name="detailDescription"
                                  id="detailDescription"></textarea>
                        </div>
                </div>
            </div>
        </div>
            <div class="form-group col-sm-12">
                <div class="text-right m-t-xs">
                    <button type="button" class="btn btn-primary" id="save-btn-proposalupdate">Update</button>
                </div>
            </div>
        </div>
    </div>
</div>

</form>
<div class="modal fade" id="add-contractor-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Contractor</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Contractor</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="contractor_name" name="contractor_name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-contractor">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


<br>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
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
        load_proposal_project_details();
        $(".select2").select2();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

    });

    function load_proposal_project_details() {
        var proposalid = $('#proposalid').val()
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {proposalid: proposalid},
            url: "<?php echo site_url('OperationNgo/fetch_converted_proposal_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#type').val(data['type']);
                if(data['type']==1 || data['type']=='')
                {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#systemCode').val(data['projectdocumentsyscode']);
                        $('#projectname').val(data['projectName']);
                        $('#proposalfrom').val(data['proposalstartdate']);
                        $('#proposalto').val(data['proposalendadate']);
                        $('#projectfrom').val(data['projectstartdate']);
                        $('#projectto').val(data['projectenddate']);;
                        $('#proposalcontractor').val(data['proposalcontractor']);
                        //  $('#detailDescription').val(data['prodescription']);
                        $('#createddate').val(data['proposalconverteddate']);
                        $('#contractorID').val(data['contractor']).change();
                        $('#totalproposal').val(data['totalestimated']);
                        $('#totalprojectcost').val(data['totalProjectValue']);
                        setTimeout(function () {
                            tinyMCE.get("detailDescription").setContent(data['prodescription']);
                            tinyMCE.get("detailDescription").setMode('readonly');
                        }, 150);
                        $('.projectloadcls').addClass('hide');
                    }
                }else
                {
                    $('#createddate').val(data['proposalconverteddate']);
                    $('#systemCode').val(data['projectdocumentsyscode']);
                    $('#projectname').val(data['projectName']);
                    $('#projectfrom').val(data['projectstartdate']);
                    $('#projectto').val(data['projectenddate']);;
                    $('#proposalcontractor').val(data['proposalcontractor']);
                    $('.proposalfromto').addClass('hide');
                    $('.proposalcontra').addClass('hide');
                    $('.proposalcostadd').addClass('hide');
                    $('#contractorID').val(data['contractor']).change();
                    $('#totalprojectcostproject').val(data['totalProjectValue']);
                    setTimeout(function () {
                        tinyMCE.get("detailDescription").setContent(data['prodescription']);
                    }, 150);
                }


            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    $('#add-contractor').click(function () {
        $('#contractor_name').val('');
        $('#add-contractor-modal').modal({backdrop: 'static'});
    });
    $('#save-btn-contractor').click(function (e) {
        e.preventDefault();
        var contractorName = $('#contractor_name').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractorName': contractorName},
            url: '<?php echo site_url("OperationNgo/save_ngo_contractor"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var contractor_drop = $('#contractorID');
                if (data[0] == 's') {
                    contractor_drop.append('<option value="' + data[2] + '">' + contractorName + '</option>');
                    contractor_drop.val(data[2]);
                    $('#add-contractor-modal').modal('hide');

                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    $('#project_proposal_convert_to_project').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            projectfrom: {validators: {notEmpty: {message: 'Project from date is required.'}}},
            totalproposalcost: {validators: {notEmpty: {message: 'Total proposal cost is required.'}}},
            projectto: {validators: {notEmpty: {message: 'Project to date is required.'}}},
            contractorID: {validators: {notEmpty: {message: 'Project Contractor is required.'}}},
        },
    });
    $('#save-btn-proposalupdate').click(function (e) {
        e.preventDefault();
        tinymce.triggerSave();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#project_proposal_convert_to_project").serialize(),
            url: '<?php echo site_url("OperationNgo/save_converted_project_details"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_proposal_project_details();
                    $('[href=#step2]').removeClass('btn-default');
                    $('[href=#step2]').addClass('btn-primary');
                    $('[href=#step2]').tab('show');
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
    });

    $('.next').click(function () {
        var nextId = $(this).parents('.tab-pane').next().attr("id");
        $('[href=#' + nextId + ']').tab('show');
    });

    $('.prev').click(function () {
        var prevId = $(this).parents('.tab-pane').prev().attr("id");
        $('[href=#' + prevId + ']').tab('show');
    });
    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');

        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }


</script>