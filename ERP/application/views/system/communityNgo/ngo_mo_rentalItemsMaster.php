<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_rentalItemMAS');
echo head_page($title  , false);

$this->load->helper('community_ngo_helper');
$periodtype_rent = all_periodType_drop();
$revenue_gl_rent = all_revenue_gl_drop();
$uomRnt_arr = all_umo_new_drop();

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

?>
    <style type="text/css">
        .saveInputs{ height: 25px; font-size: 11px }
        .number{ width: 90px !important;}
        .icheckbox_minimal-blue{ margin-top: 3px; }
        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single{
            height: 25px;
            padding: 1px 5px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow{ height: 25px !important;}
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-info btn-sm " onclick="rent_stockUpdate()" ><i class="fa fa-cubes"></i>&nbsp;Stock Update</button>
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDocument_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="row" style="margin-top: 2%;">
        <form method="post" name="rentSetFillForm" id="rentSetFillForm" class="">

            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

        <div class="col-sm-3" style="margin-left: 2%;">
            <select class="form-control select2" style="" id="itemTypeId" name="itemTypeId" onchange="getNgoRentalMasterTable();">
                <option value=""><?php echo $this->lang->line('communityngo_selRenItemType'); ?><!--Select Item Type--></option>
                <option value="">All</option>
                <option value="1">Products / Goods</option>
                <option value="2">Fixed Assets</option>

            </select>
        </div>
        <div class="col-sm-3">

            <div class="col-sm-12">
                <div class="box-tools">
                    <div class="has-feedback">
                        <input name="searchRentType" type="text" class="form-control input-sm"
                               placeholder=""
                               id="searchRentType" onkeypress="startRentalSearch()"><!--Search by leader--><!--leader-->
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
        <div class="col-sm-4" style="float: right;">

            <button href="#" type="button" class="btn btn-success btn-sm pull-right" onclick="excelrentSet_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </button>
            <button href="#" type="button" style="margin-right: 2px;" class="btn btn-danger btn-sm pull-right" onclick="generate_rentSetPdf()">
                <i class="fa fa-file-pdf-o"></i> PDF
            </button>
        </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mailbox-messages" id="load_rentSetup">
                <!-- /.table -->
            </div>

        </div>
    </div>


<?php echo footer_page('Right foot','Left foot',false); ?>


    <div class="modal fade" id="new_documents" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAddRent();"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_add_rentalSet');?><!--Add Rental Items--></h4>
                </div>
                <form class="form-horizontal" id="add-rentItems_form" >
                    <div class="row modal-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_renItemType');?><!--Item Type--> <?php required_mark(); ?></label>
                                <select id="rentTypeID" class="form-control select2" onchange="get_rentItems();"
                                        data-placeholder="Select Item Type"
                                        name="rentTypeID">
                                    <option value=""></option>
                                    <option value="1">Products / Goods</option>
                                    <option value="2">Fixed Assets</option>

                                </select>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_Description');?><!--Description--> <?php required_mark(); ?></label>
                                <select id="descriptionID" class="form-control select2" name="descriptionID" onchange="get_rentDetails();">
                                    <option>Select Description</option>
                                    <option></option>

                                </select>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_collection_PeriodType'); ?> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('rentPeriodID', $periodtype_rent, '', 'class="form-control select2" id="rentPeriodID" required '); ?>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_unit_of_measure');?><!--Unit of Measure--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('defUnitOfMeasureID', $uomRnt_arr, 'Each', 'class="form-control" id="defUnitOfMeasureID" required'); ?>

                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_rentPrice');?><!--Rental Price--> <?php required_mark(); ?></label>

                                <div class="input-group">
                                    <div
                                        class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                                    <input type="text" step="any" class="form-control" id="rentalPrice"
                                           name="rentalPrice" value="0">
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_CurrentStock');?><!--Current Stock--> <?php required_mark(); ?></label>
                                <input type="text" class="form-control" id="currentStck" name="currentStck">

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_revenueGlCode');?><!--Revenue GL Code --> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('revanueRentGLID', $revenue_gl_rent, '', 'class="form-control select2" id="revanueRentGLID" '); ?>

                            </div>

                            <div class="form-group col-sm-2" style="margin-right: 3px;">
                                <label>Is Active</label>
                                <br>
                                <input type="checkbox" name="isRequired" class="requiredCheckbox" value="1" checked>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_rentalItems()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="closeAddRent();"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRentalSetModel" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_edit_rentalSet');?><!--Edit Rental Setup--></h4>
                </div>

                <form class="form-horizontal" id="edit-documents_form" >
                    <div class="modal-body">

                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_renItemType');?><!--Item Type--> <?php required_mark(); ?></label>
                                <select id="editrentTypeID" class="form-control select2" data-placeholder="Select Item Type" name="editrentTypeID" disabled>
                                    <option value=""></option>
                                    <option value="1">Products / Goods</option>
                                    <option value="2">Fixed Assets</option>

                                </select>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_Description');?><!--Description--> <?php required_mark(); ?></label>
                                <input type="text" name="edit_descriptionID" id="edit_descriptionID" class="form-control saveInputs new-items" />
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_collection_PeriodType'); ?></label>
                                <?php echo form_dropdown('edit_rentPeriodID', $periodtype_rent, '', 'class="form-control select2" id="edit_rentPeriodID" required '); ?>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_unit_of_measure');?><!--Unit of Measure--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('edit_defUnitOfMeasureID', $uomRnt_arr, '', 'class="form-control select2" id="edit_defUnitOfMeasureID" required'); ?>

                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_rentPrice');?><!--Rental Price--> <?php required_mark(); ?></label>

                                <div class="input-group">
                                    <div
                                        class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                                    <input type="text" step="any" class="form-control" id="edit_rentalPrice"
                                           name="edit_rentalPrice" value="0">
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_CurrentStock');?><!--Current Stock--></label>
                                <input type="text" class="form-control" id="edit_currentStck" name="edit_currentStck">

                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_revenueGlCode');?><!--Revenue GL Code --></label>
                                <?php echo form_dropdown('edit_revanueRentGLID', $revenue_gl_rent, '', 'class="form-control select2" id="edit_revanueRentGLID" required'); ?>

                            </div>

                            <div class="form-group col-sm-2" style="margin-right: 3px;">
                                <label>Is Active</label>
                                <br>
                                <input type="checkbox" id="isVowel2b"  name="isVowel" onchange="isitvowel(this);" checked>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <input class="form-control" type="number" name="vowel" id="vowel2" value="1"
                                           style="display: none ;">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                        <button type="button" class="btn btn-primary btn-sm" onclick="update_rentalItem()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        $('.select2').select2();

        $(document).ready(function() {
            $('.headerclose').click(function(){
                fetchPage('system/communityNgo/ngo_mo_rentalItemsMaster','Test','Rental Item');
            });
            getNgoRentalMasterTable();

            $('.requiredCheckbox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

        });

        $('#searchRentType').bind('input', function(){
            startRentalSearch();
        });

        function getNgoRentalMasterTable() {

            var searchRentType = $('#searchRentType').val();
            var itemTypeId =document.getElementById('itemTypeId').value;

            if(itemTypeId=='' && searchRentType ==''){
                $('#search_cancel').addClass('hide');

            }
            else{
                $('#search_cancel').removeClass('hide');

            }

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'searchRentType': searchRentType,'itemTypeId':itemTypeId},
                url: "<?php echo site_url('CommunityNgo/fetch_rentItemSetups'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_rentSetup').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function startRentalSearch() {
            $('#search_cancel').removeClass('hide');
            getNgoRentalMasterTable();
        }

        function clearSearchFilter() {
            $('#search_cancel').addClass('hide');
            $('#searchRentType').val('');
            $('#itemTypeId').val('').change();
            getNgoRentalMasterTable();
        }

        function get_rentItems() {
            var rentTypeID=document.getElementById('rentTypeID').value;

            $('#descriptionID').val('').change();
            $('#revanueRentGLID').val('').change();
            $('#rentPeriodID').val('').change();
            $('#defUnitOfMeasureID').val('').change();
            $('#rentalPrice').val('').change();
            $('#currentStck').val('').change();

                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/get_rentTypeDrop",
                    data: {rentTypeID: rentTypeID},
                    success: function (data) {

                        $('#descriptionID').html(data);
                    }
                });
        }

        function get_rentDetails() {
            var rentTypeID=document.getElementById('rentTypeID').value;
            var descriptionID=document.getElementById('descriptionID').value;

            $.ajax({
                type: "POST",
                url: "CommunityNgo/get_rentDetails",
                data: {rentTypeID: rentTypeID,descriptionID:descriptionID},
                success: function (data) {

                    $('#revanueRentGLID').html(data);
                }
            });

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/get_rentOtrDetails'); ?>",
                data: {rentTypeID: rentTypeID,descriptionID:descriptionID},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (data) {

                    $('#currentStck').val(data.currentStck).change();
                    $('#defUnitOfMeasureID').val(data.defUnitOfMeasureID).change();

                }
            });
        }

        function openDocument_modal(){

            $('.saveInputs').val('');
            $('.saveInputs').change();
            $('#new_documents').modal({backdrop: "static"});
        }

        function save_rentalItems(){

                var postData = $('#add-rentItems_form').serialize();

                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('CommunityNgo/saveRentalItm_master'); ?>',
                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success :function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){

                            $('#rentTypeID').val('').change();
                            $('#descriptionID').val('').change();
                            $('#revanueRentGLID').val('').change();
                            $('#rentPeriodID').val('').change();
                            $('#defUnitOfMeasureID').val('').change();
                            $('#rentalPrice').val('').change();
                           // $('#minimumRntQty').val('').change();
                           // $('#maximunRntQty').val('').change();
                            $('#currentStck').val('').change();

                            $('#new_documents').modal('hide');
                            getNgoRentalMasterTable();
                        }

                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                })

        }


        function isitvowel(y){
            var yid= y.id;
            var vid= yid.replace(/[^\d.]/g, '');
            //  alert(vid);
            var f=document.getElementById('isVowel'+vid+'b').checked;

            if(f==true) {
                document.getElementById('vowel' + vid).value = 1;
            }if(f==false){
                document.getElementById('vowel' + vid).value = 0;
            }
        }

        function edit_rentalSetup(rentalItemID, thisTR){

            $('#hidden-id').val($.trim(rentalItemID));

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/fetchEdit_Item'); ?>",
                data: {'rentalItemID': rentalItemID},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,

                success: function (data) {
                    $('#editRentalSetModel').modal('show');

                    $("#editrentTypeID").select2("val", (data.editrentTypeID));
                    $("#edit_descriptionID" ).val(data.edit_descriptionID);
                    $("#edit_rentPeriodID").select2("val", (data.edit_rentPeriodID));
                  //  $("#edit_defUnitOfMeasureID").select2("val", (data.edit_defUnitOfMeasureID));
                    $('#edit_defUnitOfMeasureID').val(data.edit_defUnitOfMeasureID).change();
                    $('#edit_revanueRentGLID').val(data.edit_revanueRentGLID).change();
                   // $("#edit_revanueRentGLID").select2("val", (data.edit_revanueRentGLID));
                    $("#edit_rentalPrice" ).val(data.edit_rentalPrice);
                    $("#edit_currentStck" ).val(data.edit_currentStck);

                    if((data.rentalStatus) == '1'){
                        document.getElementById('isVowel2b').checked=true;
                        document.getElementById('vowel2').value='1';
                    }else{
                        document.getElementById('isVowel2b').checked=false;
                        document.getElementById('vowel2').value='0';
                    }

                }
            });

        }
        
        function closeAddRent() {

            $('#rentTypeID').val('').change();
            $('#descriptionID').val('').change();
            $('#revanueRentGLID').val('').change();
            $('#rentPeriodID').val('').change();
            $('#defUnitOfMeasureID').val('').change();
            $('#rentalPrice').val('').change();
            $('#minimumRntQty').val('').change();
            $('#maximunRntQty').val('').change();
            $('#currentStck').val('').change();
            $('#new_documents').modal({backdrop: "static"});
            
        }
        
        function rent_stockUpdate() {

                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "You want to update the rent items ! ",
                        type:"success",
                        showCancelButton: true,
                        confirmButtonColor: "#5cb85c",
                        confirmButtonText: "<?php echo $this->lang->line('common_update');?>",/*Upadte*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {},
                            url: "<?php echo site_url('CommunityNgo/update_rentStockDel'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                refreshNotifications(true);
                                stopLoad();
                                myAlert('s', 'Updated Successfully');
                                getNgoRentalMasterTable();

                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
        }

        function delete_rentalItm(id, description){
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
                        url :"<?php echo site_url('CommunityNgo/delete_rentalItm'); ?>",
                        type : 'post',
                        dataType : 'json',
                        data : {'hidden-id':id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if( data[0] == 's'){ getNgoRentalMasterTable() }
                        },error : function(){
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }


        function update_rentalItem(){
            var postData = $('#edit-documents_form').serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_rentalItm'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);


                    if(data[0] == 's'){
                        $('#editRentalSetModel').modal('hide');
                        getNgoRentalMasterTable($('#hidden-id').val());
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })

        }

        $(document).on('keypress', '.number',function (event) {
            var amount = $(this).val();
            if(amount.indexOf('.') > -1) {
                if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault();
                }
            }
            else {
                if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                    event.preventDefault();
                }
            }

        });

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });


        function excelrentSet_Export() {
            var form = document.getElementById('rentSetFillForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#rentSetFillForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/exportRentSet_excel'); ?>';
            form.submit();
        }

        function generate_rentSetPdf() {

            var form = document.getElementById('rentSetFillForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#rentSetFillForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/exportRentItems_pdf'); ?>';
            form.submit();


        }

    </script>

<?php
