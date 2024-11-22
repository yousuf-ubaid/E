<?php
$this->load->helper('buyback_helper');
$farmer_arr = load_all_farms(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="salesReturnManagement_div" xmlns="http://www.w3.org/1999/html">
    <?php
    echo head_page('Return', true);

    /*echo head_page('Sales Return', true);*/ ?>

    <div id="filter-panel" class="collapse filter-panel">
        <div class="row col-sm-12" style="padding-left: 3%; margin-bottom: 8px; ">
            <div class="form-group col-sm-2">
                <label for="datefrom"><?php echo $this->lang->line('common_date');?> <?php echo $this->lang->line('common_from');?> </label><br>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="IncidateDateFrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           onchange="startMasterSearch()" value="" id="IncidateDateFrom"
                           class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="dateto">&nbsp&nbsp<?php echo $this->lang->line('common_to');?> &nbsp&nbsp</label><!--To--><br>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           onchange="startMasterSearch()" value="" id="IncidateDateTo"
                           class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="farmerName">Farmer Name </label><br><!--Customer Name-->
                <div>
                    <?php echo form_dropdown('farmer[]', $farmer_arr, '', 'multiple  class="form-control select2" id="farmer" onchange="startMasterSearch()"'); ?>
                </div>
            </div>
            <div class="form-group col-sm-1">
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4" style="padding-left: 4%;">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Search Dispatch Note"
                           id="searchTask">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>

            </div>
        </div>
        <div class="form-group col-sm-2">
            <?php echo form_dropdown('status', array('all' => 'Status', '1' => 'Draft', '2' => 'Confirmed', '3' =>'Approved', '4' =>'Deleted'), '', 'class="form-control" id="status" onchange="startMasterSearch()"'); ?>
        </div>
        <div class="col-sm-1">
            <div class="hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clear_all_filters()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
        <div class="col-md-2 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/buyback/create_buyback_return',null,'Add New Return','BSR');"><i
                    class="fa fa-plus"></i> Create Return
            </button><!--Create Sales Return-->
        </div>
    </div>
    <hr>

    <div id="returnReportView"></div>
    <div class="col-xs-12" style="padding-right: 5px;">
        <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
            <p id="filterDisplay"></p>

            <nav>
                <ul class="list-inline" id="pagination-ul">

                </ul>
            </nav>
        </div>
    </div>
   <!-- <div class="table-responsive">
        <table id="buyback_return_table" class="<?php /*echo table_class(); */?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%">Code</th>
                <th>Farm</th>
                <th style="min-width: 15%">Warehouse</th>
                <th style="min-width: 10%">Date</th>
                <th style="min-width: 5%">Confirmed </th>
                <th style="min-width: 5%">Approved</th>
                <th style="width:120px;">Action </th>
            </tr>
            </thead>
        </table>
    </div>-->
    <?php echo footer_page('Right foot', 'Left foot', false); ?>
</div>

<div id="salesReturnCreateNew_div">
</div>

<script type="text/javascript">
    /**

     function hideAllDiv() {
        $("#salesReturnCreateNew_div").hide();
        $("#salesReturnManagement_div").hide();
    }
     function createNewSalesReturn(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'salesReturnID': id},
            url: "<?php echo site_url('Inventory/createNewSalesReturn'); ?>",
            beforeSend: function () {
                startLoad();
                hideAllDiv();
            },
            success: function (data) {
                stopLoad();
                $("#salesReturnManagement_div").show();
                $("#salesReturnManagement_div").html(data);

            }, error: function () {
                $("#salesReturnManagement_div").html('<div class="alert alert-danger">An error has occured</div>');
            }
        });
    }*/
    var per_page = 10;
    var grvAutoID;
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            /* fetchPage('system/inventory/stock_return_management', 'Test', 'Purchase Return');*/
            fetchPage('system/buyback/buyback_return', '', 'Return ')
        });
        grvAutoID = null;
        number_validation();
     //   buyback_return_table();
        get_return_tableView();

        $('#farmer').multiselect2({
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#farmer").multiselect2('selectAll', false);
        $("#farmer").multiselect2('updateButtonText');

        Inputmask().mask(document.querySelectorAll("input"));

        $('#searchTask').bind('input', function () {
            startMasterSearch();
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (e) {
            get_return_tableView();
        });

    });

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        get_return_tableView();
    }

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        get_return_tableView(data_pagination, uriSegment);
    }

    function get_return_tableView(pageID,uriSegment = 0) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {datefrom: $("#IncidateDateFrom").val(),
                dateto: $("#IncidateDateTo").val(),
                status: $("#status").val(),
                searchTask:  $('#searchTask').val(),
                farmer: $("#farmer").val(),
                'pageID':pageID
            },
            url: "<?php echo site_url('Buyback/load_buyback_return_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#returnReportView').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function buyback_return_table(selectedID=null) {

        Otable = $('#buyback_return_table').DataTable({

            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_buyback_return_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['salesReturnAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "returnAutoID"},
                {"mData": "documentSystemCode"},
                {"mData": "description"},
                {"mData": "returnwarehouse"},
                /*{"mData": "transactionCurrency"},*/
                {"mData": "documentDate"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "wareHouseLocation"}
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [7], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [8]
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "farmer", "value": $("#farmer").val()});
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function delete_item(id, value) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'returnAutoID': id},
                    url: "<?php echo site_url('Buyback/delete_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        get_return_tableView();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#search_cancel').addClass('hide');
        $('#searchTask').val('');
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#farmer').multiselect2('selectAll', false);
        $('#farmer').multiselect2('updateButtonText');
        get_return_tableView();
    }

    function reOpen_contract(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'returnAutoID':id},
                    url :"<?php echo site_url('Buyback/re_open_buyback'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        get_return_tableView();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_buyback_return(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",/*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'returnAutoID': id},
                    url: "<?php echo site_url('Buyback/referback_buyback_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            get_return_tableView();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>