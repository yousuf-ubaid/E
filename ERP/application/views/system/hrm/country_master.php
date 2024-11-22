<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #country-master-tb td{  padding: 4px 10px; }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_country_master');
//echo head_page($title  , false);
$titleTab2 = 'Airport Destination';
$gl_arr = fetch_glcode_claim_category();
?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#approvelTab" data-toggle="tab" aria-expanded="true"><?php echo $title;?> </a>
        </li>
        <li class="">
            <a href="#cancellationAppTab" data-toggle="tab" aria-expanded="false"><?php echo $titleTab2;?></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="approvelTab">
            <div class="row">
                <div class="col-md-7 pull-right">
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openCountry_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="load_country" class="<?php echo table_class(); ?> hover">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="width: auto"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th style="width: auto"><?php echo $this->lang->line('common_Country');?><!--Country--></th>
                        <th style="width: 50px"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="tab-pane" id="cancellationAppTab">
            <div class="row">
                <div class="col-md-1 pull-right">
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openCountry_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
                </div>
                <div class=" pull-right">
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="opentravel_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add_travel_type');?><!--Add--> </button>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="load_destination" class="<?php echo table_class(); ?> hover">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="width: auto">Country<!--Country--></th>
                        <th style="width: auto">City<!--City--></th>
                        <th style="width: auto">Airport Destination<!--Airport Destination--></th>
                        <th style="width: 50px"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="country_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_add_country');?><!--Add Country--></h4>
            </div>

            <div class="modal-body" id="countryDiv"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_country()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="city_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add City<!--Add City--></h4>
            </div>
            <?php echo form_open('', 'role="form" id="city_form"'); ?>
            <div class="modal-body" id="countryDiv">
                <input type="hidden" name="countryID" id="countryID">
                <!-- <div class="row">
                    <label class="col-sm-2" for="">City Name</label>
                    <div class="col-sm-8">
                        <input type="text" name="city" id="city" style="width:100%;">
                    </div>
                </div> -->
                <table class="table table-bordered" id="sub-add-tb" style="margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th></th>
                            <th><button type="button" class="btn btn-primary btn-xs" onclick="add_more_sub()" ><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    <tbody id="field_tbody">
                        <tr>
                            <td><input type="text" class="form-control new-items" name="city[]" id="city" placeholder="Enter Name Here"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="addCity()">Add<!--Add--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!-- Travel Type Master -->
<div class="modal fade" id="travel_type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog  modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_travel_type_master');?><!--Add Travel Type--></h4>
            </div>

            <div class="modal-body" >

                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-3 control-label "> <?php echo $this->lang->line('common_travel_type');?></label>
                        <div class="col-sm-6">
                        <input type="text" id="travelType" name="travelType" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:10px;">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"> <?php echo $this->lang->line('common_gl_code');?><!--GL Code--></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('glAutoID', $gl_arr, '', 'class="form-control select2" id="glAutoID"'); ?>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="margin-top:20px;">
                    <table id="travel_type_table" class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                        <th style="min-width: 10%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_travel_type');?></th>
                            <th><?php echo $this->lang->line('common_gl_code');?></th>
                            <th><?php echo $this->lang->line('common_action');?></th>
                        </tr>
                        </thead>
                        <tbody id="travel_tbody">

                        </tbody>
                    </table>
                </div>
            

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_travel_type()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var is_loaded = $('#is-loaded-country-master-tb');
    var country_master_tb = $('#country-master-tb');
    var oTable;
    $('#glAutoID').select2();

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/country_master','Test','HRMS');
        });
        load_country();
        fetch_airport_destination();
    });

    function load_country(){
        $('#load_country').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_country'); ?>",
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


                    /*if( parseInt(oSettings.aoData[x]._aData['payrollMasterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }*/

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "countryID"},
                {"mData": "countryShortCode"},
                {"mData": "CountryDes"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,3]}],
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

    function load_masterCountry(){
        oTable = country_master_tb.DataTable({
            "scrollY": "150px",
            "scrollCollapse": true,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bPaginate": false,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_allCountry'); ?>",
            "aaSorting": [[2, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html('<div align="right">'+ (i + 1) +'</divi>');
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "countryID"},
                {"mData": "countryShortCode"},
                {"mData": "CountryDes"},
                {"mData": "edit"}
            ],
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

        if( is_loaded.val() == 0 ){
            is_loaded.val(1);
            setTimeout(function(){
                oTable.ajax.reload();
            }, 200);

        }
    }

    function fetch_airport_destination(){
        $('#load_destination').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_airport_destination'); ?>",
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


                    /*if( parseInt(oSettings.aoData[x]._aData['payrollMasterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }*/

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "destinationID"},
                {"mData": "Country"},
                {"mData": "City"},
                {"mData": "airportName"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,3]}],
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

    function opentravel_modal(){
        $('#travel_type').modal({backdrop: "static"});
        $('#glAutoID').select2({
            dropdownParent: $('#travel_type')
        });
        getTravelType();
    }

    function getTravelType(){
        $.ajax({
            url:'<?php echo site_url('Employee/getTravelType') ?>',
            beforeSend: function () {
                startLoad();
            },
            success:function(data){
                stopLoad();
                var travel = JSON.parse(data); 
                var x=1;
                $('#travel_tbody').empty();
                travel.forEach(function(type){  
                    var glcode=type.systemAccountCode?type.systemAccountCode:'<center>-</center>';
                    row='<tr>'+
                    '<td>'+x+'</td>'+
                    '<td>'+type.tripType+'</td>'+
                    '<td>'+glcode+'</td>'+
                    '<td>'+ 
                    '<a onclick="deleteTravel(' + type.id + ')">' +
                    '<span  class="glyphicon glyphicon-trash " style="color:#d15b47;" data-original-title="Delete">'+
                    '</span></a>'+
                    '</td>'+
                    '</tr>'
                    $('#travel_tbody').append(row);
                    x++; 
                });
              
            },
            error:function(){
                stopLoad();
            }
        });
    }

    function save_travel_type(){
        var travelType=$('#travelType').val();
        var glcode=$('#glAutoID').val();

        $.ajax({
            url:"<?php echo site_url('Employee/saveTravelType') ?>",
            type : 'post',
            dataType : 'json',
            data:{
                'travelType':travelType,
                'glcode':glcode
            },
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                $('#travelType').val('');
                $('#glAutoID').val('');
                myAlert(data[0], data[1]);
                if( data[0] == 's'){ getTravelType() }
            },error : function(){
                stopLoad();
                myAlert('e', 'error');
            }

        });
    }

    function deleteTravel(id){
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
                    url :"<?php echo site_url('Employee/deleteTravel'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ getTravelType() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    /*function openCountry_modal(){
        $('#country_modal').modal({backdrop: "static"});
        load_masterCountry();
    }*/
    function openCountry_modal(){
        $('#country_modal').modal({backdrop: "static"});
        var countryDiv = $('#countryDiv');

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                url: '<?php echo site_url('Employee/fetch_allCountry'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    setTimeout(function(){
                        stopLoad();
                        countryDiv.html(data);
                    }, 300);

                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
    }

    function deleteCountry(id, description){
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
                    url :"<?php echo site_url('Employee/deleteCountry'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_country() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    // function openCityModel(countryID, cityName){
    //     $('#countryID').val(countryID);
    //     $('#city').val(cityName);
    //     $("#city_modal").modal({backdrop: "static"});
    // }

    // function addCity(){
    //     var countryID = $('#countryID').val();
    //     var city = $('#city').val();
    //     $.ajax({
    //         async : true,
    //         url :"<?php echo site_url('Employee/addCity'); ?>",
    //         type : 'post',
    //         dataType : 'json',
    //         data : {'countryID':countryID, 'city': city},
    //         beforeSend: function () {
    //             startLoad();
    //         },
    //         success : function(data){
    //             stopLoad();
    //             myAlert(data[0], data[1]);
    //             if( data[0] == 's'){
    //                 $("#city_modal").modal('hide');
    //                 load_country() 
    //             }
    //         },error : function(){
    //             stopLoad();
    //             myAlert('e', 'error');
    //         }
    //     });
    // }
    //...........................................................................................................

    function add_more_sub(){
        var appendData = '<tr class="tb-tr"><td><input type="text" name="city[]" id="city" class="form-control new-items" placeholder="Enter Field Here" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" onclick="remove_field(this)" style="color:rgb(209, 91, 71);"></span></td></tr>';

        $('#sub-add-tb').append(appendData);
    }

    //called when open the model. if records already exists then all will be assigned here as append............
    function openCityModel(countryID, cityName)  {
        $('#countryID').val(countryID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'countryID': countryID},
            url: "<?php echo site_url('Employee/fetchCity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#field_tbody').html('');

                if (!jQuery.isEmptyObject(data)) {  
                        var appendData1 = '';
                            appendData1 = '<tr class="tb-tr"><td><input type="text" value="" name="city[]" id="city" class="form-control new-items" placeholder="Enter Field Here" /></td>';
                        $('#field_tbody').append(appendData1);
                    
                    $.each(data, function (i, v) {
                        var appendData = '';
                        appendData = '<tr class="tb-tr"><td><input type="text" value="'+ v.cityName	 + '" name="city_exist[]" id="city_exist" class="form-control new-items" placeholder="Enter Field Here" /><input type="hidden" value="'+ v.cityID + '" name="city_exist_ID[]" id="city_exist_ID" class="form-control new-items" /></td>';
                        appendData += '<td align="center" style="vertical-align: middle">';
                        appendData += '<a onclick="deleteCity(this,' + v.cityID	 + ')"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                        $('#sub-add-tb').append(appendData);
                        
                        });
                }

                $('#city_modal').modal({ backdrop: "static" });

                stopLoad();
                refreshNotifications(true);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again') ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    //called when save fields
    function addCity() {
        var data = $('#city_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/addCity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[2] == true){
                    swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                        text: "Do you want to continue",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Continue"
                    },
                    function () {
                        var data = $('#city_form').serializeArray();
                        data.push({ "name": "continue","value": 1});
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('Employee/addCity'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] = 's') {
                                    $('#city_modal').modal('hide');
                                }
                            }, error: function () {
                                myAlert('e','common_an_error_occurred_on_save_city_Please_try_again');
                            }
                        });
                    });
                }
                else{
                    if (data[0] = 's') {
                        $('#city_modal').modal('hide');
                    }
                }
            },
            error: function (/*jqXHR, textStatus, errorThrown*/) {
                myAlert('e','common_an_error_occurred_on_save_city_Please_try_again');
                //myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }




    //called when delete not empty fields
    function deleteCity(obj, cityID){
        if(cityID){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'cityID': cityID},
                        url: "<?php echo site_url('Employee/deleteCity'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if(data == true){
                                myAlert('s', 'Field Deleted Successfully');
                            }
                            $(obj).closest('tr').remove();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        else{
            $('.remove-tr').closest('tr').remove();
        }
        
    };

    //called when remove emty field
    function remove_field(obj){
        $(obj).closest('tr').remove();
    }

    //..........................................................................................................
    
    function deleteAirportDestination(destinationID){
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
                    url :"<?php echo site_url('Employee/deleteAirportDestination'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'destinationID-id': destinationID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            fetch_airport_destination() 
                        }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
</script>



<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-03
 * Time: 5:27 PM
 */