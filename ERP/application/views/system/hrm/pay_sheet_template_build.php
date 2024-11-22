<!--Translation added by Naseek-->

<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_pay_sheet_template_builder');
echo head_page($title, false);


?>
    <style type="text/css">
        #detailDiv {
            margin: 2%;
            background: #f1f1f1 !important;
            padding: 2%;
            max-height: 150px;
            display: none;
        }

        .myInputGroup {
            margin-bottom: 1%;
        }

        .buttonSet {
            margin: 2%;
        }

        .addOnTxt {
            background: white !important;
        }

        .myPStyle {
            background-color: #f9f9f9;
            border: 1px solid #f3f0f0;
            font-size: 12px;
            line-height: 1.5;
            padding: 1% 3%;
            border-radius: 4px;
            color: #000;
        }

        .sortOrderTxt {
            height: 28px !important;
            width: 30px !important;
            padding: 2px !important;
            padding-right: 5px !important;
        }

        .addOnTxt {
            border-left: none;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <form class="form-horizontal" role="form" id="detailForm">
        <div class="m-b-md" id="wizardControl" style="margin-bottom: 2%">
            <a class="btn btn-wizard btn-primary" href="#step1" id="paySheetCreate" onclick="tabShow(this.id)"
               data-toggle="tab"><?php echo $this->lang->line('hrms_payroll_pay_sheet_create');?><!--Pay sheet Create--></a>
            <a class="btn btn-wizard btn-default" href="#step2" id="paySheetDesign" onclick="tabShow(this.id)"
               data-value="0" data-toggle="tab"><?php echo $this->lang->line('hrms_payroll_pay_sheet_design');?><!--Pay sheet design--></a>
        </div>

        <div class="row modal-body" style="margin-bottom: 1% !important;">
            <div class="col-sm-5 pull-left">
                <label class="col-sm-4 control-label" for="tCode"><?php echo $this->lang->line('hrms_payroll_template_code');?><!--Template Code--> </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control tmpCode" disabled>
                    <input type="hidden" name="tCode" id="tmpCode">
                </div>
            </div>

            <div class="col-sm-5 pull-right">
                <label class="col-sm-4 control-label" for="tName"><?php echo $this->lang->line('hrms_payroll_template_name');?><!--Template Name--> </label>
                <div class="col-sm-7">
                    <input type="text" name="tName" class="form-control tmpName confirmAction">
                </div>
            </div>
        </div>

        <div class="row modal-body " id="sort-btn-divs" style="margin-bottom: 1% !important;display: none">


            <button id="sort-btn-div" class="btn btn-default btn-sm pull-right "> <?php echo $this->lang->line('hrms_payroll_sort_order');?><!--Sort Order--></button>

        </div>


        <div class="modal-body stepBody" id="paySheetCreateTab" style="padding-top: 0px;">
            <div class="row">
                <div class="col-sm-5 pull-left">
                    <label class="col-sm-4 control-label" for="masterSelect"><?php echo $this->lang->line('hrms_payroll_detail_types');?><!--Detail Types--></label>
                    <div class="col-sm-5">
                        <select name="masterSelect" class="form-control confirmAction" id="masterSelect"
                                style="/*width: 120px*/">
                            <option></option>
                            <option value="H"><?php echo $this->lang->line('hrms_payroll_header');?><!--Header--></option>
                            <option value="A"><?php echo $this->lang->line('hrms_payroll_addition');?><!--Addition--></option>
                            <option value="D"><?php echo $this->lang->line('hrms_payroll_deduction');?><!--Deduction--></option>
                            <option value="G"><?php echo $this->lang->line('common_group');?><!--Group--></option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="button" onclick="addItems()" class="btn btn-primary btn-sm confirmAction">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
                        </button>
                    </div>
                </div>


            </div>

            <div class="row" id="detailDiv"></div>

            <div class="col-sm-12 table-responsive">

                <table class="table table-bordered" id="templateTB" style="margin-top: 1%">
                    <thead>
                    <tr>
                        <th style="width: 20%">
                            <?php echo $this->lang->line('hrms_payroll_header');?><!--Header-->
                        </th>
                        <th style="width: 20%">
                            <?php echo $this->lang->line('hrms_payroll_addition');?><!--Addition-->
                        </th>
                        <th style="width: 20%">
                            <?php echo $this->lang->line('hrms_payroll_deduction');?><!--Deduction-->
                        </th>
                        <th style="width: 20%">
                            <?php echo $this->lang->line('common_group');?><!--Group-->
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td id="H_TD"></td>
                        <td id="A_TD"></td>
                        <td id="D_TD"></td>
                        <td id="G_TD"></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="modal-body stepBody" id="paySheetDesignTab" style="display: none">
            <input type="hidden" id="isAlreadyLoaded" value="0">
            <div class="col-sm-12 table-responsive">
                <button type="button" onclick="sortOrderModal()" style="margin-bottom: 2px" id="sort-btn-div" class="btn btn-primary btn-sm pull-right "> <?php echo $this->lang->line('hrms_payroll_sort_order');?><!--Sort Order--></button>
                <table class="table table-bordered" id="templateViewTB" style="margin-top: 1%; display:none">
                    <thead>
                    <tr>
                        <th style="width: 25%" id="H_viewTH"> &nbsp; </th>
                        <th style="width: 20%" id="A_viewTH"> <?php echo $this->lang->line('hrms_payroll_addition');?><!--Addition--></th>
                        <th style="width: 20%" id="D_viewTH"> <?php echo $this->lang->line('hrms_payroll_deduction');?><!--Deduction--></th>
                        <th style="width: 20%" id="G_viewTH"> <?php echo $this->lang->line('common_group');?><!--Group--></th>
                        <th style="width: 10%"><?php echo $this->lang->line('hrms_payroll_net_salary');?><!-- Net Salary--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="viewTR">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> &nbsp; </td>
                    </tr>
                    </tbody>
                </table>
                <table class="table table-bordered" id="templateViewTB_shortOrder" style="margin-top: 1%">
                    <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> &nbsp; </td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="row buttonSet pull-right">
            <input type="hidden" name="isConform" id="isConform" value="0">
            <input type="hidden" id="H_count" value="0">
            <input type="hidden" id="A_count" value="0">
            <input type="hidden" id="D_count" value="0">
            <input type="hidden" id="G_count" value="0">
            <input type="hidden" name="templateID" id="templateID"
                   value="<?php echo $this->input->post('data_arr'); ?>">
            <button type="button" class="btn btn-primary btn-sm submitBtn confirmAction" data-value="0"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft-->
            </button>
            <button type="button" class="btn btn-primary btn-sm submitBtn confirmAction" data-value="1"><?php echo $this->lang->line('common_save_and_confirm');?><!--Save & Confirm-->
            </button>
        </div>
    </form>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="editDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_edit_template_detail');?><!--Edit Template Detail--></h4>
                </div>
                <form class="form-horizontal" role="form" id="captionUpdate_form">
                    <div class="modal-body">
                        <br>

                        <div class="form-group has-feedback">
                            <label class="col-sm-4 control-label" for="columnName"><?php echo $this->lang->line('hrms_payroll_column_name');?><!--Column Name--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <input type="text" name="columnName" id="columnName" class="form-control saveInputs"
                                       disabled>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label class="col-sm-4 control-label"
                                   for="captionName"><?php echo $this->lang->line('hrms_payroll_caption');?><!--Caption --><?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <input type="text" name="captionName" id="captionName" class="form-control saveInputs">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="captionUpdateID" id="thisID" value="">
                        <input type="hidden" name="captionUpdateTmpID"
                               value="<?php echo $this->input->post('data_arr'); ?>">
                        <input type="hidden" id="isTemplateConfirmed" value="">
                        <button type="button" class="btn btn-primary btn-sm" id="updateCaption"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shortingDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sma" role="document">
            <div class="modal-content">
                <form class="form-horizontal" role="form" id="sortOrder_form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="shortingTitle"></h4>
                    </div>
                    <div class="modal-body">
                        <div id="sortOrderBody" class="" style="min-height: 90px;"></div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="sortOrderUpdateTmpID"
                               value="<?php echo $this->input->post('data_arr'); ?>">
                        <input type="hidden" id="isTemplateConfirmedInSortOrder" value="">
                        <input type="hidden" name="updateColumn" id="updateColumn" value="">
                        <button type="button" class="btn btn-primary btn-sm" id="updateSortOrder"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sortOderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sma" role="document">
            <div class="modal-content">
                <form class="form-horizontal" role="form" id="sortOrder_form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="shortingTitle"><?php echo $this->lang->line('hrms_payroll_sort_order');?><!--Sort Order--></h4>
                    </div>
                    <div class="modal-body">
                        <div id="" class="" style="min-height: 90px;">
                            <table class="table table-bordered" id="sortOrderlistBody" style="margin-top: 1%">
                                </table>

                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var templateId = "<?php echo $this->input->post('data_arr'); ?>";
        $(document).ready(function () {


            if (templateId != 0 && templateId != null) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'templateId': templateId},
                    url: "<?php echo site_url('Template_paysheet/templateHeaderDetails'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('.tmpCode').val(data['documentCode']);
                        $('#tmpCode').val(data['documentCode']);
                        $('.tmpName').val(data['templateDescription']);
                        if (data['confirmedYN'] == 1) {
                            $('.confirmAction').attr('disabled', 'disabled');
                        }
                        $('#isTemplateConfirmed').val(data['confirmedYN']);
                        $('#isTemplateConfirmedInSortOrder').val(data['confirmedYN']);
                        get_templateDetails(templateId, data['confirmedYN']);

                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }

            $('.headerclose').click(function () {
                fetchPage('system/hrm/pay_sheet_template', templateId, 'HRMS');
            });

        });

        function get_templateDetails(templateId, confirmYN=0) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'templateId': templateId},
                url: "<?php echo site_url('Template_paysheet/templateDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();


                    var H_count = 0;
                    var A_count = 0;
                    var D_count = 0;
                    $.each(data, function (i, val) {
                        var tdAppend = '';
                        var count = 0;
                        var detailType = val['detailType'];
                        var tempFieldID = val['tempFieldID'];
                        var selectedColumn = "'" + detailType + "'";

                        switch (detailType) {
                            case 'H':
                                H_count++;
                                count = H_count;
                                break;

                            case 'A':
                                A_count++;
                                count = A_count;
                                break;

                            case 'D':
                                D_count++;
                                count = D_count;
                                break;
                        }

                        $('#' + detailType + '_count').val(count);


                        tdAppend += '<p class="cols-sm-4 myPStyle"><span class="cols-sm-2" id="captionName_' + tempFieldID + '">' + val['captionName'] + '</span>';
                        tdAppend += '<span class="cols-sm-2 pull-right"> <a onclick="edit_details(' + tempFieldID + ')"><span class="glyphicon glyphicon-pencil" style="color: #428dbc"></span></a>'; //#164f8a
                        if (confirmYN != 1) {
                            tdAppend += ' &nbsp;<span style="line-height: 2ch; color: #000000">|</span>&nbsp; ';
                            tdAppend += '<a onclick="delete_details(this, ' + tempFieldID + ', ' + selectedColumn + ')"><span class="glyphicon glyphicon-trash"  style="color: #dd4b39"></span></a></span>';
                        }
                        tdAppend += '<input type="hidden" name="postID[]" class="postID" value="' + tempFieldID + '" >';
                        tdAppend += '<input type="hidden" name="postDetailColumn[]" id="column_' + tempFieldID + '" value="' + val['columnName'] + '" >';
                        tdAppend += '<input type="hidden" name="postDetailCaption[]" class="postDetailCaption" id="caption_' + tempFieldID + '" value="' + val['captionName'] + '" >';
                        tdAppend += '<input type="hidden" name="postSortOrder[]" class="sortOrder" id="sortOrder_' + tempFieldID + '" value="' + val['sortOrder'] + '" >';
                        tdAppend += '<input type="hidden" name="postCatID[]"  value="' + val['catID'] + '" >';
                        tdAppend += '<input type="hidden" name="postType[]" class="postType" value="' + detailType + '" ></p>';

                        $('#' + detailType + '_TD').append(tdAppend);
                    });

                    $('#templateTB').hide().fadeIn(300);


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }


        $('#masterSelect').change(function () {
            var fieldType = $(this).val();
            var templateID = $('#templateID').val();
            var detailDiv = $('#detailDiv');
            detailDiv.hide();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'fieldType': fieldType, 'templateID': templateID},
                url: "<?php echo site_url('Template_paysheet/templateFields'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    var appendItem = '';
                    var details = '';
                    var caption = '';
                    var checked;
                    var orderAsc = 0;
                    if (data.length > 0) {
                        detailDiv.show();
                    }

                    $.each(data, function (elm, val) {

                        if (val['checked'] == 'Y') {
                            caption = val['captionName'];
                            checked = 'checked';
                        } else {
                            caption = val['caption'];
                            checked = '';
                        }

                        if (val['orderAsc'] == 100) {
                            orderAsc = parseInt(orderAsc);
                            orderAsc += 1;
                        } else {
                            orderAsc = val['orderAsc'];
                        }

                        details = 'data-value="' + val['id'] + '" data-caption="' + caption + '" data-sortOrder="' + orderAsc + '" data-cat="' + val['salaryCatID'] + '" ' + checked;

                        appendItem += '<div class="col-lg-3 myInputGroup"> <div class="input-group">';
                        appendItem += '<span class="input-group-addon"><input type="checkbox" class="itemCheckBox"  value="' + val['caption'] + '" ' + details + '></span>';
                        appendItem += '<input type="text" class="form-control col-xs-5 addOnTxt" value="' + caption + '" disabled> </div></div>';
                    });

                    detailDiv.html(appendItem);

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        function sortOrderModal(){
            $('#sortOderModal').modal('show');
            sortOrderModaldetails(templateId);
        }

        function sortOrderModaldetails(templateId){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'templateId': templateId},
                url: "<?php echo site_url('Template_paysheet/templateDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

$('#sortOrderlistBody').html('');
                    content='<thead>';
                    content +='<tr><td><?php echo $this->lang->line('common_field');?></td><td><?php echo $this->lang->line('hrms_payroll_field_type');?></td><td><?php echo $this->lang->line('hrms_payroll_sort_order');?></td></tr>';/*<!--Field-->*//*Field Type*//*Sort Order*/
                    content+='</thead><tbody>';
                    $.each(data, function (i, val) {
                        if(val['detailType']=='H'){
                            title='<?php echo $this->lang->line('hrms_payroll_header');?>';/*Header*/
                        }
                        if(val['detailType']=='A'){
                            title='<?php echo $this->lang->line('hrms_payroll_addition');?>';/*Addition*/
                        }
                        if(val['detailType']=='D'){
                            title='<?php echo $this->lang->line('hrms_payroll_deduction');?>';/*Deduction*/
                        }
                        if(val['detailType']=='G'){
                            title='<?php echo $this->lang->line('common_group');?>';/*Group*/
                        }
                        content += '<tr><td>' + val['captionName'] + '</td>';
                        content +='<td>'+title+'</td>';
                        content +='<td><input onchange="updatesortOrder('+val['tempFieldID']+',this.value)" style="text-align: right" type="number" name="sortOrder[]" value="' + val['sortOrder'] + '" id="sortOrder"></td></tr>';
                    });
                    content+='</tbody>';


                    $('#sortOrderlistBody').append(content);




                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });



        }

        function updatesortOrder(tempFieldID,sortOrder){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {tempFieldID:tempFieldID,sortOrder:sortOrder},
                url: "<?php echo site_url('Template_paysheet/update_template_sortOrder'); ?>",
                beforeSend: function () {
                   startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#sortOrder_'+tempFieldID).val(sortOrder);
                    $('#paySheetDesign').click();
                 /*   myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function () {
                            //fetchPage('system/hrm/pay_sheet_template','Test','Salary');

                        }, 200);

                    }*/
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                   stopLoad();
                }
            });

        }

        function addItems() {
            var selectedItem = $.trim($('#masterSelect').val());
            var selectedColumn = "'" + selectedItem + "'";
            if (selectedItem !== '') {
                var detailTD = $('#' + selectedItem + '_TD');
                var tdAppend = '';
                detailTD.text('');
                var count = 0;
                $('.itemCheckBox:checked').each(function () {
                    var id = $(this).attr('data-value');
                    var caption = $(this).attr('data-caption');
                    var sortOrder = $(this).attr('data-sortOrder');
                    var catID = $(this).attr('data-cat');
                    tdAppend += '<p class="cols-sm-4 myPStyle"><span class="cols-sm-2" id="captionName_' + id + '">' + caption + '</span>';
                    tdAppend += '<span class="cols-sm-2 pull-right"> <a onclick="edit_details(' + id + ')"><span class="glyphicon glyphicon-pencil" style="color: #428dbc"></span></a>';
                    tdAppend += ' &nbsp;<span style="line-height: 2ch; color: #000000">|</span>&nbsp; ';
                    tdAppend += '<a onclick="delete_details(this, ' + id + ', ' + selectedColumn + ')"><span class="glyphicon glyphicon-trash"  style="color: #dd4b39"></span></a></span>';
                    tdAppend += '<input type="hidden" name="postID[]" class="postID" value="' + id + '" >';
                    tdAppend += '<input type="hidden" name="postDetailColumn[]" id="column_' + id + '" value="' + $(this).val() + '" >';
                    tdAppend += '<input type="hidden" name="postDetailCaption[]" class="postDetailCaption" id="caption_' + id + '" value="' + caption + '" >';
                    tdAppend += '<input type="hidden" name="postSortOrder[]" class="sortOrder" id="sortOrder_' + id + '" value="' + sortOrder + '" >';
                    tdAppend += '<input type="hidden" name="postCatID[]"  value="' + catID + '" >';
                    tdAppend += '<input type="hidden" name="postType[]" class="postType" value="' + selectedItem + '" ></p>';
                    count++;
                });

                $('#' + selectedItem + '_count').val(count);
                detailTD.append(tdAppend);
                $('#isAlreadyLoaded').val(0);

                $('#templateTB').hide().fadeIn(300);
            }
            else {
                myAlert('e', 'please select a detail type.')
            }
        }


        $('.submitBtn').click(function () {
            var isConfirm = $(this).attr('data-value')
            $('#isConform').val(isConfirm);
            save();

            /*if (isConfirm == 1) {
                var hCount = $('#H_count').val();
                var aCount = $('#A_count').val();
                var dCount = $('#D_count').val();

                if (hCount > 0 && aCount > 0 && dCount > 0) {
                    getSaveConfirm();
                }
                else {
                    myAlert('e', 'Each detail type should have at least one record');
                }

            }
            else {
                save();
            }*/
        });

        function getSaveConfirm() {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    save();
                }
            );
        }

        function save() {
            var postDetails = $('#detailForm').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postDetails,
                url: "<?php echo site_url('Template_paysheet/templateDetailsSave'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function () {
                            //fetchPage('system/hrm/pay_sheet_template','Test','Salary');
                            templateLoad("<?php echo $this->input->post('data_arr'); ?>", '');
                        }, 200);

                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function edit_details(id) {
            $('#editDetailModal').modal({backdrop: "static"});
            $('#columnName').val($('#column_' + id).val());
            $('#captionName').val($('#caption_' + id).val());
            $('#thisID').val(id);
        }

        $('#updateCaption').click(function () {
            var updateTxt = $.trim($('#captionName').val());
            var id = $('#thisID').val();


            if (updateTxt != '') {
                $('#captionName_' + id).text(updateTxt);
                $('#caption_' + id).val(updateTxt);
                $('#isAlreadyLoaded').val(0);

                var isTemplateConfirmed = $('#isTemplateConfirmed').val();

                if (isTemplateConfirmed == 1) {
                    var postDetails = $('#captionUpdate_form').serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postDetails,
                        url: "<?php echo site_url('Template_paysheet/templateCaptionUpdate'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $('#editDetailModal').modal('hide');
                            }
                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                } else {
                    $('#editDetailModal').modal('hide');
                }


            } else {
                myAlert('e', '<?php echo $this->lang->line('hrms_payroll_please_fill_the_caption_field');?> ')/*Please fill the caption field*/
            }

        });

        $('#paySheetDesign').click(function () {
            var templateId = $('#templateID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'templateId': templateId},
                url: "<?php echo site_url('Template_paysheet/templateDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    var tdAppend = '<thead><tr>';
                    $.each(data, function (i, val) {
                        tdAppend += '<td>' + val['captionName'] + '</td>';
                    });
                    tdAppend += '</tr></thead>';
                    $('#templateViewTB_shortOrder').html(tdAppend);

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

            /*var isAlreadyLoaded = $('#isAlreadyLoaded').val();

            if (isAlreadyLoaded == 0) {
                $('.designTR').remove();
                $('#isAlreadyLoaded').val(1);
                $('#viewTR').hide();
                var H_count = 0;
                var A_count = 0;
                var D_count = 0;
                var G_count = 0;
                var H_viewTD = '';
                var A_viewTD = '';
                var D_viewTD = '';
                var G_viewTD = '';
                $('.myPStyle').each(function () {

                    var caption = $(this).find('.postDetailCaption').val();
                    var dType = $(this).find('.postType').val();
                    var count = 0;
                    switch (dType) {
                        case 'H':
                            H_count++;
                            count = H_count;
                            H_viewTD += '<td>' + caption + '</td>';
                            break;

                        case 'A':
                            A_count++;
                            count = A_count;
                            A_viewTD += '<td>' + caption + '</td>';
                            break;

                        case 'D':
                            D_count++;
                            count = D_count;
                            D_viewTD += '<td>' + caption + '</td>';
                            break;

                        case 'G':
                            G_count++;
                            count = G_count;
                            G_viewTD += '<td>' + caption + '</td>';
                            break;
                    }

                });

                if (H_count == 0) {
                    H_count = 1;
                    H_viewTD = '<td>&nbsp;</td>'
                }
                if (A_count == 0) {
                    A_count = 1;
                    A_viewTD = '<td>&nbsp;</td>'
                }
                if (D_count == 0) {
                    D_count = 1;
                    D_viewTD = '<td>&nbsp;</td>'
                }
                if (G_count == 0) {
                    G_count = 1;
                    G_viewTD = '<td>&nbsp;</td>'
                }


                $('#H_viewTH').attr('colspan', H_count);
                $('#A_viewTH').attr('colspan', A_count);
                $('#D_viewTH').attr('colspan', D_count);
                $('#G_viewTH').attr('colspan', G_count);

                var templateViewTB = $('#templateViewTB');
                templateViewTB.append('<tr class="designTR">' + H_viewTD + ' ' + A_viewTD + ' ' + D_viewTD + ' ' + G_viewTD + ' <td>&nbsp;</td></tr>');
            }*/
        });

        function sortDetails(dType) {
            var sortOrderBody = $('#sortOrderBody');
            var title = '';
            var appendDet = '';
            var caption = '';
            var postID = '';
            var sortOrder = '';

            switch (dType) {
                case 'H_TD':
                    title = 'Header';
                    break;
                case 'D_TD':
                    title = 'Deductions';
                    break;
                case 'A_TD':
                    title = 'Addition';
                    break;
            }

            $('#shortingDetailModal').modal({backdrop: "static"});
            $('#shortingTitle').text('Setup ' + title + ' Sort Order');
            $('#updateColumn').val(title + ' Fields');

            $('#' + dType).children('.myPStyle').each(function () {
                caption = $(this).find('.postDetailCaption').val();
                postID = $(this).find('.postID').val();
                sortOrder = $(this).find('.sortOrder').val();
                appendDet += '<div class="col-lg-4 myInputGroup"> <div class="input-group">';
                appendDet += '<span class="input-group-addon" style="min-width: 120px !important; text-align: left">' + caption + '</span>';
                appendDet += '<input type="hidden" name="sortOrderID[]" value="' + postID + '" >';
                appendDet += '<input type="text" name="sortOrder[]" class="form-control  sortOrderTxt number" value="' + sortOrder + '" data-id="' + postID + '"> </div></div>';
                appendDet += ' ';
            });

            sortOrderBody.html('');
            sortOrderBody.append(appendDet);

        }

        $('#updateSortOrder').click(function () {
            var errorCount = 0;
            $('.sortOrderTxt ').each(function () {
                var order = $.trim($(this).val());
                if (order != '') {
                    var orderID = $(this).attr('data-id');
                    $('#sortOrder_' + orderID).val(order);
                } else {
                    $(this).css('border-color', 'red');
                    errorCount++;
                }
            });

            if (errorCount == 0) {
                var isConfirmed = $('#isTemplateConfirmedInSortOrder').val();

                if (isConfirmed == 1) {
                    var postDetails = $('#sortOrder_form').serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postDetails,
                        url: "<?php echo site_url('Template_paysheet/templateSortOrderUpdate'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $('#shortingDetailModal').modal('hide');
                                setTimeout(function () {
                                    templateLoad("<?php echo $this->input->post('data_arr'); ?>", '');
                                }, 300);
                            }
                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
                else {
                    $('#shortingDetailModal').modal('hide');
                }
            } else {
                myAlert('e', 'Please fill all required fields');
            }
        });

        $(document).on('keypress', '.number', function (event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        });

        $(document).on('keyup', '.sortOrderTxt', function () {
            $(this).css('border-color', '#d2d6de');
        });

        function delete_details(id, row, column) {
            var captionName = $('#caption_' + row).val();

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
                    $(id).closest('.myPStyle').remove();
                    $('#isAlreadyLoaded').val(0);
                    var count = 0;
                    $('#' + column + '_TD').find('.myPStyle').each(function () {
                        count++;
                    });
                    $('#' + column + '_count').val(count);
                }
            );

        }

        function tabShow(tabShowBtn) {
            $('.stepBody').hide();
            wizardStyle(tabShowBtn);
            $('#' + tabShowBtn + 'Tab').show();
        }

        function wizardStyle(btnID) {
            var wizardBtn = $('.btn-wizard');
            wizardBtn.removeClass('btn-primary');
            wizardBtn.addClass('btn-default');
            wizardBtn.css('color', '#333');

            var btn = $('#' + btnID);
            btn.addClass('btn-primary');
            btn.css('color', '#fff');


            if (btnID == 'paySheetDesign') {

                $('#sort-btn-div').show();
            }
            else {
                $('#sort-btn-div').hide();

            }


        }


    </script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-06-19
 * Time: 2:51 PM
 */