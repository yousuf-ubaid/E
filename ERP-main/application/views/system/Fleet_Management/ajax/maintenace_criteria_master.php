<?php

echo head_page('Maintenance Criteria', false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');
$employeedrop = all_employee_drop();
?>
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }
    .actionicon{
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .headrowtitle{
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="openmaintenacecriteriamaster();"><i
                class="fa fa-plus"></i> Create Maintenance Criteria
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Enter Your Text Here"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
    <div class="col-md-2">
        <?php echo form_dropdown('maintenacestatus', array('' => 'Status', '1' => 'Active', '2' => 'In Active'), '', 'class="form-control" onchange="startMasterSearch()" id="maintenacestatus"'); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="maintenace_criteria_master"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="maintenacecriteria_model">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="maintenacecriteria_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="maintenacecriteriaid" name="maintenacecriteriaid">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <textarea class="form-control" id="criteriadescription" name="criteriadescription" rows="2" data-bv-field="address"></textarea>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">Active&nbsp;&nbsp;</label>
                                    <input id="active" type="radio" data-caption="" class="columnSelected"
                                           name="active" value="1">
                                </div>
                            </label>
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">In Active&nbsp;&nbsp;</label>
                                    <input id="inactive" type="radio" data-caption="" class="columnSelected"
                                           name="active" value="0">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" onclick="maintenacecriteria()"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
     id="spareparts_model">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Link Spare Parts </h4>

                <form role="form" id="maintenace_Criteria_form" class="form-horizontal">
                    <input type="hidden" name="maintenanceCriteriaID" id="maintenanceCriteriaID">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed" id="maintenace_Criteria_add_table">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Description</th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="maintenace_table">
                            <!--<tr>
                                <td>
                                    <input type="text" class="form-control search input-mini f_search" name="search[]"
                                           id="f_search_1"
                                           placeholder="Item Code, Item Description"
                                           onkeydown="remove_item_all_description(event,this)"><input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                </td>
                                <td><?php /*echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" disabled  required'); */?></td>
                                <td>
                                    <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off">
                                </td>
                                <td>
                                    <textarea class="form-control" rows="1" name="description[]"></textarea>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>-->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default"
                                type="button"> Close</button>
                        <button class="btn btn-primary" type="button"
                                onclick="savespareparts()">Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>






<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var selectedItemsSync = [];
    var search_id = 1;
    var oTable1 = [];
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/ajax/maintenace_criteria_master','','Maintenace Criteria');
        });
        maintenacecriteriatable();
        initializeitemTypeahead();
        Inputmask().mask(document.querySelectorAll("input"));
        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });
    function maintenacecriteriatable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var maintenacestatus = $('#maintenacestatus').val();


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask,'maintenacestatus':maintenacestatus},
            url: "<?php echo site_url('Fleet/load_vehicale_maintenace_criteria'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#maintenace_criteria_master').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        maintenacecriteriatable();
    }

    function openmaintenacecriteriamaster() {
        $('#usergroup-title').text('Add Maintenance Criteria');
        $('.extraColumnsgreen input').iCheck('uncheck');
        $('#maintenacecriteria_master_form')[0].reset();
        $('#maintenacecriteria_master_form').bootstrapValidator('resetForm', true);
        $('#maintenacecriteriaid').val('');
        $('#maintenacecriteria_model').modal('show');
    }

    function openmaintenacecriteriamasteredit(id) {
        $('#usergroup-title').text('Edit Maintenance Criteria');
        $('.extraColumnsgreen input').iCheck('uncheck');
        $('#maintenacecriteria_master_form')[0].reset();
        $('#maintenacecriteria_master_form').bootstrapValidator('resetForm', true);
        $('#maintenacecriteriaid').val('');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenanceCriteriaID': id},
            url: "<?php echo site_url('Fleet/fetchmaintenacecriteria'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                    $('#criteriadescription').val(data['maintenanceCriteria']);
                   setTimeout(function () {
                    if (data['status'] == 1) {
                        $('#active').iCheck('check');
                    }else if(data['status'] == 0){
                        $('#inactive').iCheck('check');
                    } }, 500);
                $('#maintenacecriteriaid').val(data['maintenanceCriteriaID']);
                stopLoad();
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });






        $('#maintenacecriteria_model').modal('show');
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.donorsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#maintenacestatus').val('');
        $('#sorting_1').addClass('selected');
        maintenacecriteriatable();
    }
    function delete_maintenace_criteria(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'userID': id},
                    url: "<?php echo site_url('Fleet/delete_maintenace_criteria'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            maintenacecriteriatable();
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function maintenacecriteria()
    {
        var data = $('#maintenacecriteria_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/maintenace_criteria'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    maintenacecriteriatable();
                    $('#maintenacecriteria_model').modal('hide');
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function linkspareparts(maintenanceCriteriaID) {
        selectedItemsSync = [];
        $('#maintenace_Criteria_form')[0].reset();
        $('#maintenace_Criteria_add_table tbody tr').not(':first').remove();
        $('#maintenanceCriteriaID').val(maintenanceCriteriaID);
        fetch_detail();
        $('.f_search').closest('tr').css("background-color",'white');
        $('.quantityRequested').closest('tr').css("background-color",'white');

        initializeitemTypeahead(1);
        $('#spareparts_model').modal('show');

    }
    function remove_item_all_description(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }
    function add_more() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#maintenace_Criteria_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        /*appendData.find('#expenseGlAutoID_1').attr('id', '')
         appendData.find('#liabilityGlAutoID_1').attr('id', '')
         appendData.find('#ifSlab_1').attr('id', '')*/
        appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val(0);
        appendData.find('.discount').val(0);
        ;
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#maintenace_Criteria_add_table').append(appendData);
        var lenght = $('#maintenace_Criteria_add_table tbody tr').length - 1;
        $(".select2").select2();
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function initializeitemTypeahead(id) {

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Fleet/fetch_spareparts/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                $(this).closest('tr').css("background-color", 'white');
              /*  if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }*/
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }
    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {

                $(element).closest('tr').find('.umoDropdown').empty()
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function savespareparts() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#maintenace_Criteria_form').serializeArray();

        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2 ');
            }
        });

        $('.quantityRequested').each(function () {
            if (this.value == '' || this.value == '0') {
                $(this).closest('tr').css("background-color", '#ffb2b2 ');
            }
        });

        $('.estimatedAmount').each(function () {
            if (this.value == '' || this.value == '0') {
                $(this).closest('tr').css("background-color", '#ffb2b2 ');
            }


        });

        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        })
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_spareparts'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('.umoDropdown').prop("disabled", true);
                    fetch_detail();
                    $('#spareparts_model').modal('hide');
                }else
                {
                    $('.umoDropdown').prop("disabled", true);
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function fetch_detail() {
        var maintenacecritiria = $('#maintenanceCriteriaID').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'maintenacecritiria': maintenacecritiria},
            url: "<?php echo site_url('Fleet/fetch_maintenace_criteriadet'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#maintenace_table').empty();
                if (!jQuery.isEmptyObject(data['detail'])) {
                    var x = 2;
                    $.each(data['detail'], function (key, value) {
                        if(x == 2)
                        {
                            var deleterecordds = ' ';
                        } else
                        {
                            var deleterecordds = '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>';
                        }

                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                        var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]">'


                        var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" autocomplete="off" onkeyup="change_qty(this)" value="'+ value['qtyRequired']+'">'

                        var description = ' <textarea class="form-control input-mini" rows="1" name="description[]" placeholder="...">' + value['commentscriteria'] + '</textarea>'


                        $('#maintenace_table').append('<tr><td>'+ itemsearch +'</td><td>'+ UOM +'</td><td>'+ qty +'</td><td>'+ description +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');


                        fetch_related_uom_id( value['defaultUOMID'],value['uomID'],$('#uom_'+key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id = x-1;
                }else
                {
                    var deleterecordds = ' ';

                    var itemsearch = '<input type="text" class="form-control search f_search" name="search[]" id="f_search_1" placeholder="Item Id,Item Description" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">'

                    var uom = '<select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled> <option value=" ">Select UOM</option></select>';
                    var qty = ' <input type="text" onfocus="this.select();" name="quantityRequested[]"  placeholder="0.00" class="form-control number input-mini quantityRequested" required="" onkeyup="change_qty(this)" autocomplete="off" >'

                    var description = '<textarea class="form-control" rows="1" name="description[]"></textarea>'

                    $('#maintenace_table').append('<tr><td>'+itemsearch+'</td><td>'+ uom +'</td><td>'+ qty +'</td><td>'+ description +'</td><td class="remove-td" style="vertical-align: middle;text-align: center">'+ deleterecordds +'</td></tr>');
                }
                initializeitemTypeahead(1);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function change_qty(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
    }
</script>