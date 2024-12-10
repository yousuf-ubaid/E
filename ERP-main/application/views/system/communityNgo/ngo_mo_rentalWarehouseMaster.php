<?php

$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_rentalWarehouse');
echo head_page($title  , false);

$this->load->helper('community_ngo_helper');
$rentWHdrop = comNgoWarehouseBinFilter();
?>
    <style type="text/css">
        .saveInputs{ height: 25px; font-size: 11px }
        #setup-edit-tb td{ padding: 2px; }
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
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openDocument_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
        </div>
    </div><hr>
    <div class="row" style="margin-top: 2%;">
        <div class="col-sm-4" style="margin-left: 2%;">

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
        <div class="col-sm-7">
            <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mailbox-messages" id="load_rentSetup">
                <!-- /.table -->
            </div>

        </div>
    </div>


<?php echo footer_page('Right foot','Left foot',false); ?>


    <div class="modal fade" id="com_rentWareHmod" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeRentWH();"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_addRentalWHitem');?><!--Add Warehouse Item--></h4>
                </div>
                <form class="form-horizontal" id="add-rentItems_form" >
                    <div class="row modal-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-8" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_rentalWHitem');?><!--Warehouse Item--> <?php required_mark(); ?></label>

                    <?php echo form_dropdown('rentWareHid', $rentWHdrop, '', 'class="form-control select2" id="rentWareHid" '); ?>

                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;text-align: center;">
                                <label>Is Default</label>
                                <br>
                                <input type="checkbox" name="isWhDef" id="isWhDef" class="" onchange="isitWHDef();" value="0">
                                <input class="form-control" type="number" name="isWHdefault" id="isWHdefault" value="0" style="display: none ;">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_rentalWHmaster()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="closeRentWH();"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRentalSetModel" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="max-width:750px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_edit_rentalSet');?><!--Edit Rental Setup--></h4>
                </div>

                <form class="form-horizontal" id="edit-documents_form" >
                    <div class="modal-body">
                        <table class="table table-bordered" id="setup-edit-tb">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('communityngo_rentalWHitem');?><!--Warehouse Item--></th>
                                <th style="width: 70px">Is Default</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('editrentWareHid', $rentWHdrop, '', 'class="form-control" id="editrentWareHid" '); ?>
                                </td>
                                <td align="center">
                                    <input type="checkbox" id="isVowel2b"  name="isVowel" onchange="isitWHvowel(this);" checked>
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <input class="form-control" type="number" name="vowel" id="vowel2" value="1" style="display: none ;">
                                    </div>
                                </td>
                                </tr>
                            </tbody>
                        </table>
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
                fetchPage('system/communityNgo/ngo_mo_rentalWarehouseMaster','Test','Rental Warehouse');
            });
            getRentalWHTable();

            $('.requiredCheckbox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

        });

        $('#searchRentType').bind('input', function(){
            startRentalSearch();
        });

        function getRentalWHTable(filtervalue) {
            var searchRentType = $('#searchRentType').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'q': searchRentType ,'filtervalue':filtervalue},
                url: "<?php echo site_url('CommunityNgo/fetch_rentWHSetup'); ?>",
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
            getNgoFamilyMasterTable();
        }

        function clearSearchFilter() {
            $('#search_cancel').addClass('hide');
            $('#searchRentType').val('');
            getNgoFamilyMasterTable();
        }


        function openDocument_modal(){

            $('.saveInputs').val('');
            $('.saveInputs').change();
            $('#com_rentWareHmod').modal({backdrop: "static"});
        }

        function isitWHDef(){

            var f=document.getElementById('isWhDef').checked;

            if(f==true) {

                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/defWHControl",
                    data: {},
                    success: function (data) {

                        if (data) {

                        bootbox.alert("Already the warehouse is as default So if you make this default, the current default warehouse \n will be not default !");

                        } else {

                        }
                        document.getElementById('isWHdefault').value = 1;
                    }
                });

            }if(f==false){
                document.getElementById('isWHdefault').value = 0;
            }
        }

        function save_rentalWHmaster(){

            var postData = $('#add-rentItems_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveRentalWH_master'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){

                        $('#rentWareHid').val('').change();

                        $('#com_rentWareHmod').modal('hide');
                        getRentalWHTable();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })

        }


        function isitWHvowel(y){
            var yid= y.id;
            var vid= yid.replace(/[^\d.]/g, '');
            //  alert(vid);
            var f=document.getElementById('isVowel'+vid+'b').checked;

            if(f==true) {

                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/defWHControl",
                    data: {},
                    success: function (data) {

                        if (data) {

                            bootbox.alert("Already the warehouse is as default So if you make this default, the current default warehouse \n will be not default !");

                        } else {

                        }
                        document.getElementById('vowel' + vid).value = 1;
                    }
                });

            }if(f==false){
                document.getElementById('vowel' + vid).value = 0;
            }
        }

        function edit_rentalWHSetup(id, thisTR){

            $('#hidden-id').val( $.trim(id) );

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/fetchEdit_RentWh'); ?>",
                data: {'id': id},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,

                success: function (data) {
                    $('#editRentalSetModel').modal('show');

                    $('#editrentWareHid').val(data.editrentWareHid);
                  //  $("#editrentWareHid").select2("val", (data.editrentWareHid));

                    if((data.vowel) == '1'){
                        document.getElementById('isVowel2b').checked=true;
                        document.getElementById('vowel2').value='1';
                    }else{
                        document.getElementById('isVowel2b').checked=false;
                        document.getElementById('vowel2').value='0';
                    }

                }
            });

        }

        function closeRentWH() {

            $('#rentWareHid').val('').change();

            $('#com_rentWareHmod').modal({backdrop: "static"});

        }

        function delete_rentalWH(id, description){
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
                        url :"<?php echo site_url('CommunityNgo/delete_rentalWH'); ?>",
                        type : 'post',
                        dataType : 'json',
                        data : {'hidden-id':id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if( data[0] == 's'){ getRentalWHTable() }
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
                url: '<?php echo site_url('CommunityNgo/edit_rentalWH'); ?>',
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
                        getRentalWHTable($('#hidden-id').val());
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

    </script>

<?php
