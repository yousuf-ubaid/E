<?php
/**
 * Created by PhpStorm.
 * Date: 2020-03-06
 * Time: 11:25 AM
 */
?>

<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #department-add-tb td{ padding: 2px; }
</style>

<?php
$this->load->helper('logistics');
$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//$title = $this->lang->line('hrms_others_master_department_master');
$title = "Service Types";
echo head_page($title  , false);
$document = load_logisticDocument();
//$item = fetch_item_details(0,false);
$item1 = fetch_item_details(1);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openServiceType_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_servicetype" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: 80%">Service Type</th>
            <th style="width: 15%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_servicetype"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Service Type</h4>
            </div>
            <form class="form-horizontal" id="add-servicetype_form" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Service Type</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control saveInputs" id="add_servicetype" name="add_servicetype">
                                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_servicetype()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Service Type</h4>
            </div>

            <div class="modal-body">
                <form role="form" id="editServicetype_form" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Service Type</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="serviceType" name="serviceType">
                                    <input type="hidden" id="edit_hidden-id" name="edit_hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateServicetype()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentSelection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Documents </h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Service Type</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="ds_serviceType" name="ds_serviceType" readonly>

                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal" id="documentSelection_form">
                        <div class="row">

                                <div class="form-group col-md-6">
                                    <label  class="col-sm-4 control-label" for="document">Document</label>
                                    <div class="col-sm-8">
                                    <?php echo form_dropdown('document', $document, '', 'class="form-control saveInputs new-itemss" id="document" '); ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-5">
                                    <label class="col-sm-8 control-label" for="isMandatory">Is Mandatory</label>
                                    <div class="col-sm-4">
                                    <input type="checkbox" name="isMandatory" class="requiredCheckbox" value="1" checked>
                                    </div>
                                </div>
                            <div class="form-group col-md-2">
                                <input class="col-sm-5 control-label" type="hidden" id="ds_hidden-id" name="ds_hidden-id" value="0" >
                                <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_add');?><!--Add--></button>
                            </div>
                        </div>
                    </form><br><br>

                    <table class="table table-bordered" id="document-tb">
                        <thead>
                        <tr>
                            <th style="width: 5%"> #</th>
                            <th style="width: 45%"> Document</th>
                            <th style="width: 40%">Is Mandatory</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                        </thead>
                        <tbody id="table_body">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>

        </div>
    </div>
</div>

<div class="modal fade in" id="modal_viewitem" role="dialog" aria-labelledby="myModalLabe2" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">Service Type Items</h4>
            </div>
            <form class="form-horizontal bv-form" id="model_form_servicetypeitem" novalidate="novalidate">
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" id="log_serviceID" value="">
                        <fieldset>
                            <button type="button" id="" name="" class="btn btn-primary" style="float:right;"
                                    onclick="load_servicetype_items();"><?php echo $this->lang->line('common_add_item');?>
                            </button> <!--Add item-->
                            <div class="form-group">
                                <div class="col-md-4">
                                </div>
                            </div>
                        </fieldset>
                    </form>

                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                        <tr>
                            <!--q<td>#</td>-->
                            <td>Item System Code</td>
                            <td>Secondary Item Code</td>                
                            <td><?php echo $this->lang->line('common_description');?></td><!--Description-->
                            <td>&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody id="tableBody_servicetypeItem"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_servicetype" role="dialog" aria-labelledby="myModalLabe3" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">Items Search</h4><!--Items Search-->
            </div>
            <div class="modal-body">
                <form class="form-inline">
                    <div>Search Item: <input id="keyword_itemDescription" name="itemID" type="text" placeholder="search" onkeyup="search_item();">
                        &nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="clearmaste_search()"  class="btn btn-default"><?php echo $this->lang->line('common_clear');?>
                        </button><!--Clear-->
                    </div>
                </form>
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                    <tr>
                        <td>Item System Code</td>
                        <td>Item Secondary Code</td>
                        <td><?php echo $this->lang->line('common_description');?></td><!--Description-->
                        <td></td>
                    </tr>
                    </thead>
                    <tbody id="tableBody_servicetype"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
        </div>

    </div>
</div>

<script type="text/javascript">
    var servicetype_tb = $('#servicetype-add-tb');
    var document_tb = $('#document-tb');
var servID;
var descr;
    $(document).ready(function() {
         servID= null;
         descr= null;
        //$('.select2').select2();
       
        load_servicetype();
        $('.headerclose').click(function(){
            fetchPage('system/logistics/service_types','Test','Service Type');
        });

        $('#documentSelection_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                document  : {validators: {notEmpty: {message: 'Document is required.'}}},/*UOM is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'servID', 'value': servID });
           //print_r(data);
            $.ajax({

                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Logistics/save_mandatorydocument'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if(data){
                        //alert(servID);
                        fetch_mandatorydocument(servID,descr);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });


        $('.requiredCheckbox').iCheck({
            checkboxClass: 'icheckbox_minimal-blue'
        });
    });

    function load_servicetype(selectedRowID=null){
        var Otable = $('#load_servicetype').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Logistics/fetch_servicetype'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if( parseInt(oSettings.aoData[x]._aData['serviceID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "serviceID"},
                {"mData": "serviceType"},
                //{"mData": "itemDescription"},
               // {"mData": "status"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2], "orderable": false},{"visible":true,"searchable": false,"targets": [0,2] }],
           // "columnDefs": [{"searchable": false, "targets": [0,2]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function openServiceType_modal(){
        //$('#department-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('.new-items').val(null).trigger("change");
        $('#new_servicetype').modal({backdrop: "static"});
       //  $("#tableBody_subpplierItem").empty();
       // load_items_details();
    }


    function fetch_mandatorydocument(id,desc){
        servID = id;
        descr = desc;
        //alert(servID);
//alert(servID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'servID': servID},
            url: "<?php echo site_url('Logistics/fetch_document_detail_table'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {

                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#table_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                }else {

                    $.each(data['detail'], function (key, value) {
                        //var stat = mandatoryStatus(value['isMandatory']);
                        if(value['isMandatory'] == 1){
                              var status = '<span class="label label-success">Yes</span>'
                        }else{
                            var status = '<span class="label label-danger">No</span>'
                        }
                        //alert(value['serviceDocumentID']);
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['description'] +' </td><td align="center"> '+ status + '</td><td><a onclick="delete_mandatoryDocument('+value['serviceDocumentID']+')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a></td></tr>');
                        x++;
                    });

                }
                SelectMandatoryDocument(servID,desc);

                //$('#add_conversion_form')[0].reset();
                //$('#add_conversion_form').bootstrapValidator('resetForm', true);

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function SelectMandatoryDocument(id,des){
        //$('#department-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('#ds_hidden-id').val( $.trim(id) );
        $('#ds_serviceType').val( $.trim(des) );
        $('#document').val('');
        $('#documentSelection').modal({backdrop: "static"});

    }
    function save_servicetype(){


            var postData = $('#add-servicetype_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Logistics/save_servicetype'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_servicetype').modal('hide');
                        load_servicetype();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        
    }

    function save_document(){
        var errorCount=0;
        $('#document').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });



        if(errorCount == 0){
            var postData = $('#add-servicetype_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Logistics/save_servicetype'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_servicetype').modal('hide');
                        load_servicetype();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');/*Please fill all fields*/
        }
    }

    function edit_servicetype(id, des){
        $('#editModal').modal({backdrop: "static"});
        $('#edit_hidden-id').val( $.trim(id) );
        $('#serviceType').val( $.trim(des) );
       // $('#item').val( $.trim(itemAutoID) ).change();
    }

    function delete_servicetype(id, description){
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
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Logistics/delete_servicetype'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_servicetype() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });




    function updateServicetype(){
        var postData = $('#editServicetype_form').serialize();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Logistics/editServicetype'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_servicetype($('#edit_hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function delete_mandatoryDocument(id){
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
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Logistics/delete_mandatoryDocument'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'serviceDocumentID':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ fetch_mandatorydocument(servID,descr); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }


    function itemDrop_make(){
        var comboBox = JSON.stringify(<?php echo json_encode($item1) ?>);
        var row = JSON.parse(comboBox);

        var drop = '<select name="item[]" class="form-control saveInputs new-items select2" >';
        drop += '<option value="">Select item</option>';<!--Select Document-->

        $.each(row, function(i, obj){
            drop += '<option value="'+obj.itemAutoID+'" >'+obj.itemDescription+'</option>';
        });

        drop += '<select>';

        return drop;
    }

    function load_servicetype_items_details(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'serviceID': id},
            url: "<?php echo site_url('Logistics/load_servicetype_items_details'); ?>",
            beforeSend: function () {
                startLoad();
                $("#modal_viewitem").modal('show');
                $("#tableBody_servicetypeItem").empty();
            },
            success: function (data) {
                stopLoad();
                $("#log_serviceID").val(id);
                if (jQuery.isEmptyObject(data)) {
                    
                    $('#tableBody_servicetypeItem').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (val, text) {
                            /*text['itemDescription']*/
                        var del = '<i class="fa fa-trash text-red" onclick="delete_servicetype_item(' + text['serviceItemID'] + ')">&nbsp;</i>';
                    

                        $("#tableBody_servicetypeItem").append('<tr id="row_' + text['serviceItemID'] + '"><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td style="text-align: center">' + del + '</td></tr>');
                    });
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_servicetype_item(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'serviceItemID': id},
                    url: "<?php echo site_url('Logistics/delete_servicetype_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data["error"] == 1) {
                            myAlert('e', data["message"]);
                        } else if (data['error'] == 0) {
                            $('#row_' + id).hide();
                            myAlert('s', data["message"]);
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_servicetype_items(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {serviceID: $("#log_serviceID").val()},
            url: "<?php echo site_url('Logistics/load_servicetype_items'); ?>",
            beforeSend: function () {
                startLoad();
                $("#modal_servicetype").modal('show');
                $("#tableBody_servicetype").empty();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data)) {
                    alert("no records");
                    $('#tableBody_servicetype').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (val, text) {
                        if (text['serviceItemID'] > 0) {
                            $("#tableBody_servicetype").append('<tr><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td >' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-default" id="save_itm_btn" type="submit" disabled onclick="save_servicetypeItem(' + text['itemAutoID'] + ');">Added</button></td></tr>');
                        } else { 
                            $("#tableBody_servicetype").append('<tr><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td >' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-primary" id="save_itm_btn" type="submit" onclick="save_servicetypeItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('common_add');?></button></td></tr>');<!--Add-->
                        } 
                    });
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function search_item() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'serviceID': $("#log_serviceID").val(), 'keyword': $("#keyword_itemDescription").val()},
            url: "<?php echo site_url('Logistics/load_servicetype_items'); ?>",
            beforeSend: function () {
                //startLoad();
                //$("#modal_itemmaster").modal('show')
                $("#tableBody_servicetype").empty();
            },
            success: function (data) {
                //stopLoad();
                if (jQuery.isEmptyObject(data)) {
                    $('#tableBody_servicetype').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                } else {
                     $.each(data, function (val, text) {
                        if (text['serviceItemID'] > 0) {
                            $("#tableBody_servicetype").append('<tr><td >' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-default" id="save_itm_btn" type="submit" disabled onclick="save_servicetypeItem(' + text['itemAutoID'] + ');">Added</td></tr>');
                        } else {
                            $("#tableBody_servicetype").append('<tr><td >' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-primary" id="save_itm_btn" type="submit" onclick="save_servicetypeItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('common_add');?></button></td></tr>');<!--Add-->
                        }
                    });
                }
               
            }, error: function (jqXHR, textStatus, errorThrown) {
                //stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function clearmaste_search(){
        $('#keyword_itemDescription').val('');
        search_item();
    }

    function save_servicetypeItem(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID, 'serviceID': $('#log_serviceID').val()},
            url: "<?php echo site_url('Logistics/save_servicetypeItem'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                load_servicetype_items();
                if (data["error"] == 1) {
                    myAlert('e', data["message"]);
                } else if (data['error'] == 0) {
                    load_servicetype_items_details(data['code']);
                    myAlert('s', data["message"]);
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>



