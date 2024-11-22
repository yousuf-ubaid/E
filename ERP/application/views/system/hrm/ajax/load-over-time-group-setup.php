
<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_over_time_group_master_setup');
echo head_page($title, false);

$master_data = $this->input->post('data_arr');
$masterCat = masterOT_drop(1);
$expenseGL = expenseGL_drop();
$detail_arr = get_OT_groupMasterDet($master_data['groupID']);
$salary_categories_arr = salary_categories(array('A', 'D'));


?>

    <style type="text/css">
        .saveInputs {
            height: 25px;
            font-size: 11px
        }

        #otCat-add-tb td {
            padding: 2px;
        }

        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            height: 25px;
            padding: 0px 5px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 18px !important;
        }

        #groupData-div {
            height: 600px;
            border: 1px solid #cbd6cc;
        }

        @media (max-width: 767px) {
            #groupData-div {
                border: 0px
            }
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> : </label>
                <div class="col-sm-6">
                    <input type="text" name="description"  id="description" class="form-control" value="<?php echo $master_data['description'] ?>">
                </div>
            </div>
        </div>
        <div class="col-md-8 col-xs-6 pull-right">
            <!--<button type="button" class="btn btn-primary btn-sm pull-right"
                    onclick="open_categoryModel()"><i class="fa fa-plus-square"></i>&nbsp; Add
            </button>-->
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <div id="groupData-div">
            <?php echo form_open('', 'role="form" class="form-horizontal" id="groupDet_from"'); ?>
            <input type="hidden" name="groupID" value="<?php echo $master_data['groupID'] ?>"/>
            <table id="OT-setup-tb" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 15px">#</th>
                    <th style="width: 150px"><?php echo $this->lang->line('hrms_attendance_over_time_description');?><!--OT Description--></th>
                    <!--<th style="width: 250px">GL Code</th>-->
                    <th style="width: auto"><?php echo $this->lang->line('hrms_attendance_formula');?><!--Formula--></th>
                    <th style="width: 80px">
                        <button type="button" class="btn btn-primary btn-xs pull-right"
                                onclick="open_categoryModel()"><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add-->
                        </button>
                    </th>
                </tr>
                </thead>
                <?php
                $operand_arr = array('+', '*', '/', '-', '(', ')');
                if (!empty($detail_arr)) {
                    $expenseGL_arr = expenseGL_drop('asResult');

                    ?>

                    <tbody>
                        <?php foreach ($detail_arr as $key => $row) {
                        $classTitle = explode(' ', $row['description']);
                            $lastInputType = 2;
                        $formulaText = '';
                        $formula = trim($row['formula'] ?? '');


                        if (!empty($formula) && $formula != null) {

                            $formula_arr = explode('|', $formula); // break the formula

                            foreach ($formula_arr as $formula_row) {

                                if (trim($formula_row) != '') {
                                    if (in_array($formula_row, $operand_arr)) { //validate is a operand
                                        $formulaText .= $formula_row;
                                    } else {
                                        $elementType = $formula_row[0];
                                        if ($elementType == '_') {
                                            /*** Number ***/
                                            $numArr = explode('_', $formula_row);
                                            $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                                            $formulaText .= $num.' ';

                                        }
                                        else if ($elementType == '#') {
                                            /*** Salary category ***/
                                            $catArr = explode('#', $formula_row);
                                            $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                                            $new_array = array_map(function ($k) use ($salary_categories_arr) {
                                                return $salary_categories_arr[$k];
                                            }, $keys);

                                            $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                                            $formulaText .= $salaryDescription.' ';
                                        }
                                    }
                                }

                            }
                        }

                        $action_pra = $row['overTimeID'] . ', \'' . $row['description'] . '\', \'' . $classTitle[0] . '\', \''.$formulaText.'\', \''.$formula.'\'';
                        $action_pra .= ', \''.$lastInputType.'\'';
                        ?>
                        <tr>
                            <td></td>
                            <td>
                                <input name="OTDescription[]" class="form-control saveInputs"
                                       value="<?php echo $row['description'] ?>" readonly>
                                <input type="hidden" name="OT_ID[]" class="ot_ID" value="<?php echo $row['overTimeID'] ?>">
                                <input type="hidden" name="groupDetID[]" value="<?php echo $row['groupDetailID'] ?>">
                            </td>
                            <!--<td>
                                <?php /*echo form_dropdown('glCode[]', $expenseGL_arr, $row['glCode'], 'class="form-control saveInputs select2" id="masterCat" required'); */?>
                            </td>-->
                            <td style="vertical-align: middle;">
                                <span class="<?php echo $classTitle[0] . '_' . $row['overTimeID'] ?>"><?php echo $formulaText; ?></span>
                                <input type="hidden" name="formulaOriginal[]" class="formulaOriginal" value="<?php echo $formula; ?>">
                            </td>
                            <td align="right" style="vertical-align: middle;">
                                <i class="fa fa-superscript" aria-hidden="true" onclick="open_formulaModal(<?php echo $action_pra ?>)" title="formula"
                                   style="color:#3c8dbc;"></i>&nbsp; | &nbsp;&nbsp;
                                <span class="glyphicon glyphicon-trash traceIcon" onclick="delete_groupSetup(<?php echo $row['groupDetailID'] ?>, this)"
                                      style="color:rgb(209, 91, 71);"></span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
            <?php echo form_close(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 5px">
            <div class="col-md-10 hidden-xs">&nbsp;</div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_groupDetails()"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="OTCategories" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_over_time_categories');?><!--Over Time Categories--></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            if (!empty($masterCat)) {
                                foreach ($masterCat as $key => $row) { ?>
                                    <div class="form-group col-sm-6 col-xs-6">
                                        <div class="input-group">
                                        <span class="input-group-addon">
                                            <input type="checkbox" value="<?php echo $row['ID']; ?>"
                                                   data-value="<?php echo $row['description']; ?>"
                                                   class="cat-checkbox"/>
                                        </span>
                                            <input type="text" class="form-control"
                                                   value="<?php echo $row['description']; ?>" readonly/>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="add_OT_cat()"><?php echo $this->lang->line('common_add');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formulaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_formula_builder');?><!--Formula Builder--> <span id="formula-title"></span></h4>
            </div>

            <form method="post" name="frm_formulaBuilderMaster" id="frm_formulaBuilderMaster">
                <div class="modal-body" id="" style="min-height: 100px;">

                    <input type="text" class="hide" id="formulaOriginal" name="formulaOriginal"/>
                    <input type="text" class="hide" id="last-insert-type" name="last-insert-type" value=""/>
                    <input type="text" class="hide" id="editing_row" name="editing_row"/>
                    <div id="formulaOriginal_div" style="display: none"></div>
                    <div id="formulaCode_div"></div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-xs-1 control-label" for="category"><!--Category--></label>
                            <div class="col-xs-2">
                            </div>
                            <span class="col-xs-9 pull-right"></span>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 5px">
                        <div class="col-xs-3">
                            <div class="well well-sm" id="formula-build" style="border-radius: 0;margin: 0; min-height: 185px;">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#field" data-toggle="tab"><?php echo $this->lang->line('hrms_attendance_fields');?><!--Fields--></a></li>
                                    <li><a href="#math" data-toggle="tab"><?php echo $this->lang->line('hrms_attendance_math');?><!--Math--></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="field">
                                        <?php
                                        $type = '';
                                        foreach ($salary_categories_arr as $payGroupDetail) {
                                            $type .= '<a class="btn btn-sm btn-default"';
                                            $type .= 'onclick="appendFormula(\'' . $payGroupDetail['salaryDescription'] . '\',' . $payGroupDetail['salaryCategoryID'] . ',1)"';
                                            $type .= ' href="#"><strong>' . $payGroupDetail['salaryDescription'] . '</strong></a>';
                                        }

                                        echo $type;
                                        ?>
                                    </div>
                                    <div class="tab-pane" id="math">
                                        <div style="padding-bottom: 5px">
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('+','+',2)"
                                               href="#">+</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('*','*',2)"
                                               href="#">*</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('/','/',2)"
                                               href="#">/</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('-','-',2)"
                                               href="#">-</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('(','(',2)"
                                               href="#">(</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula(')',')',2)"
                                               href="#">)</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('.','.',0)"
                                               href="#">.</a><!-- Use as a number for the logic-->
                                        </div>
                                        <div style="">
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('1','1',0)"
                                               href="#">1</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('2','2',0)"
                                               href="#">2</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('3','3',0)"
                                               href="#">3</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('4','4',0)"
                                               href="#">4</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('5','5',0)"
                                               href="#">5</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('6','6',0)"
                                               href="#">6</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('7','7',0)"
                                               href="#">7</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('8','8',0)"
                                               href="#">8</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('9','9',0)"
                                               href="#">9</a>
                                            <a class="btn btn-sm btn-default"
                                               onclick="appendFormula('10','10',0)" href="#">10</a>
                                            <a class="btn btn-sm btn-default" onclick="appendFormula('0','0',0)"
                                               href="#">0</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-9">
                            <div class=""  style="background-color: white;border-radius: 0;padding: 1px;font-size: 25px">
                                <textarea readonly style="width: 100%" rows="5" name="formulaText" id="formula"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">

                <button class="btn btn-primary btn-sm" rel="tooltip" type="button" title="Add Detail"
                        id="btn_add_formulaDetail" onclick="saveFormula()">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_attendance_add_formula');?><!--Add Formula-->
                </button>
                <button class="btn btn-primary btn-sm" rel="tooltip" type="button" title="Clear"
                        onclick="removeFormula()" id="clear"> <?php echo $this->lang->line('common_clear');?><!--Clear-->
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script>
    var setupTB = $('#OT-setup-tb');
    $('.select2').select2();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/over_time_group_master', 'Test', 'HRMS');
        });

    });

    function open_categoryModel() {
        $('#OTCategories').modal('show');
       // $('.cat-checkbox').prop('checked', false);
    }

    function add_OT_cat() {
        var alreadyExist = [];
        var existItems = [];
        $('.ot_ID').each(function(){
            alreadyExist.push( $(this).val() );
        });

        $('.cat-checkbox:checked').each(function () {
            var masterCat = $(this);
            var selectedCatID = masterCat.val();
            var selectedCatDes = masterCat.attr('data-value');

            if( $.inArray(selectedCatID, alreadyExist) !== -1 ){
                existItems.push( selectedCatDes );
            }
            else{

                var selectedCatDes_arr = selectedCatDes.split(' ');
                var onClick_fn = 'open_formulaModal(' + selectedCatID + ', \'' + selectedCatDes + '\', \'' + selectedCatDes_arr[0] + '\', \'\', \'\', \'\')';

                var appData = '<tr> <td> </td>';
                appData += '<td> <input name="OTDescription[]" class="form-control saveInputs" value="' + selectedCatDes + '" readonly>';
                appData += '<input type="hidden" name="OT_ID[]" class="ot_ID" value="' + selectedCatID + '">';
                appData += '<input type="hidden" name="groupDetID[]" value=""> </td>';
                /*appData += '<td>' + glDrop_make() + '</td>';*/
                appData += '<td style="vertical-align: middle;">';
                appData += '<span class="'+selectedCatDes_arr[0]+'_' +selectedCatID+'"></span>';
                appData += '<input type="hidden" name="formulaOriginal[]" class="formulaOriginal" value="">';
                appData += '</td>';
                appData += '<td align="right" style="vertical-align: middle;">';
                appData += '<i class="fa fa-superscript" aria-hidden="true" onclick="'+onClick_fn+'" title="formula" style="color:#3c8dbc;"></i>';
                appData += '&nbsp;&nbsp; | &nbsp;&nbsp;<span class="glyphicon glyphicon-trash" onclick="delete_groupSetup( \'\', this)" ';
                appData += 'style="color:rgb(209, 91, 71);"></span>';
                appData += '</td>';
                appData += '</tr>';

                setupTB.append(appData);
            }

        });

        if( existItems.length > 0 ){
            myAlert('w',  '<?php echo $this->lang->line('common_following_items_already_exist');?> [ '+ existItems.join( ' | ' ) + ' ] ' );/*Following items already exist*/
        }

        $('.select2').select2();

    }

    function open_formulaModal(OT_ID, catDescription, classTitle, formulaText, formulaOriginal, lastInsertType) {

        $('#editing_row').val(classTitle + '_' + OT_ID);
        $('#formula-title').text(' - ' + catDescription);
        $('#formula').val(formulaText);
        $('#formulaOriginal').val(formulaOriginal);
        $('#last-insert-type').val(lastInsertType);
        $('#formulaModal').modal('show');
    }

    function save_groupDetails() {
        var postData = $('#groupDet_from').serializeArray();
        postData.push({'name':'description', 'value': $('#description').val()});
        var requestUrl = '<?php echo site_url('Employee/save_OTGroupDet'); ?>';

        $.ajax({
            type: 'post',
            url: requestUrl,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    setTimeout(function(){
                        fetchPage('system/hrm/ajax/load-over-time-group-setup', null, 'HRMS', null, data[2]);
                    }, 300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function this_PageRefresh(){
        var arr = JSON.stringify(<?php echo json_encode($master_data); ?>);
        arr = JSON.parse(arr);
        var details = {};
        details.groupID = arr['groupID'];
        details.description = arr['description'];

        setTimeout(function(){
            fetchPage('system/hrm/ajax/load-over-time-group-setup', null, 'HRMS', null, details);
        }, 300);

    }

    function appendFormula(symbol, code, isFiled) {
        /**************************************
         *  isFiled  0 => number
         *           1 => catType
         *           2 => operand
       /***************************************/
        var last_insert_type = $('#last-insert-type');
        var lastInsertType = last_insert_type.val();
        var formula = $("#formula");
        var formulaOriginal = $("#formulaOriginal");
        var content = formula.val();
        var content2 = formulaOriginal.val();

        if (isFiled == 0) {
            if( lastInsertType != 0 ){
                content2 += '_' + code + '_';
            }
            else{
                content2 = content2.replace(/_([^_]*)$/,'$1');
                content2 += code+ '_';
            }
        }

        else if (isFiled == 1) {
            content2 += '#'+code;
        }
        else if (isFiled == 2) { content2 += '|' + code + '|'; }

        var newContent = (isFiled == 0) ? content +''+symbol : content +' '+ symbol;
        var newContent2 = content2;


        formula.val(newContent);
        formulaOriginal.val(newContent2);
        formula.focus();

        last_insert_type.val(isFiled);
        $("#formulaOriginal_div").text(newContent2);
    }

    function saveFormula() {
        var formulaData = $("#frm_formulaBuilderMaster").serializeArray();
        var formulaOriginal = formulaData[0].value;
        var editing_rowClass = formulaData[2].value;
        var formulaText = formulaData[3].value;
        var editing_row = $('.' + editing_rowClass);

        editing_row.text(formulaText);
        editing_row.closest('tr').find('.formulaOriginal').val(formulaOriginal);


        $('#formulaModal').modal('hide');

    }

    function removeFormula(){
        $('#formula').val('');
        $('#formulaOriginal').val('');
        $('#last-insert-type').val('');
    }

    function delete_groupSetup(groupDet_ID, obj) {

        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {

                if( groupDet_ID == '' ){
                    $(obj).closest('tr').remove();
                }
                else{
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Employee/delete_OTGroupDetail'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'groupDet_ID': groupDet_ID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);

                            if (data[0] == 's') {
                                this_PageRefresh();
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }

            }
        );
    }

    function glDrop_make() {
        var declarationCombo = JSON.stringify(<?php echo json_encode($expenseGL); ?>);

        var row = JSON.parse(declarationCombo);
        var drop = '<select name="glCode[]" class="form-control select2 saveInputs"><option value=""></option>';

        $.each(row, function (i, obj) {
            drop += '<option value="' + obj.GLAutoID + '" >' + obj.GLSecondaryCode + ' | ' + obj.GLDescription + '</option>';
        });

        drop += '</select>';

        return drop;

    }

</script>

<?php
/**
 * Created by PhpStorm.
 * Date: 1/29/2017
 * Time: 3:54 PM
 */