<?php echo head_page('Goods Received Note', true);
$this->load->helper('buyback_helper');
$farmer = load_all_farms();
$date_format_policy = date_format_policy();
$location_arr = load_all_locations();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<form id="grn_filter_frm">
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="grnDatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="grnDatefrom" class="form-control"  value=""  >
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="grnDateto"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="grnDateto"  class="form-control" value="" >
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="area">Area</label><br>
            <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID" onchange="load_locationBase_sub_location(this), startMasterSearch()"'); ?>
        </div>
        <div class="form-group col-sm-2">
            <label for="area">Sub Area</label><br>
            <div id="div_load_subloacations">
                <select name="subLocationID" class="form-control" id="subLocationID">

                </select>
            </div>
        </div>
        <div class="col-sm-2" style="">
            <label for="area">Farmer</label><br>
            <?php echo form_dropdown('farmername',$farmer, '', 'class="form-control select2" onchange="startMasterSearch()" id="farmername"'); ?>
        </div>
    </div>
    <br>
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-3 text-center">
        &nbsp;
    </div>
    <div class="col-md-4 text-right">
        <button type="button" class="btn btn-primary"
                onclick="fetchPage('system/buyback/create_good_receipt_note',null,'Add New Goods Received Note','BUYBACK');"><i
                class="fa fa-plus"></i> New Goods Received Note
        </button>
        <a href="#" type="button" class="btn btn-success btn-sm pull-right" style="margin-left: 2px;" onclick="excel_Export()">
            <i class="fa fa-file-excel-o"></i> Excel
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-4" style="padding-left: 4%;">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchTask" type="text" class="form-control input-sm"
                                   placeholder="Search Goods Received Note" id="searchTask">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <?php echo form_dropdown('grn_status', array('' => 'Status', '1' => 'Draft', '2' => 'Confirmed', '3' => 'Approved', '4' => 'Deleted'), '', 'class="form-control" onchange="startMasterSearch()" id="grn_status"'); ?>
                </div>
                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-12">
                    <div id="DispatchNoteMaster_view"></div>
                </div>
            </div>
            <div class="col-xs-12" style="padding-right: 5px;">
                <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                    <p id="filterDisplay"></p>
                    <nav>
                        <ul class="list-inline" id="pagination-ul"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<div class="modal fade" id="grnView_SubItem_model" style="z-index: 1000000001;" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="grn_SubItemTitle">Sub Item Configuration</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="grnView_SubItem_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var per_page = 10;
    var Otable;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/buyback/good_received_note', '', 'Good Receipt Note');
        });
        get_goodReceiptNoteManagement_tableView();
    });
    Inputmask().mask(document.querySelectorAll("input"));

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        get_goodReceiptNoteManagement_tableView(data_pagination, uriSegment);
    }

    function get_goodReceiptNoteManagement_tableView(pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#grn_filter_frm').serializeArray();
        data.push({'name': 'pageID', 'value': pageID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_goodReceiptNoteManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#DispatchNoteMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_GoodReceiptnote(id) {
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
                    data: {'grnAutoID': id},
                    url: "<?php echo site_url('Buyback/delete_goodReceipt_note_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        get_goodReceiptNoteManagement_tableView();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function reOpen_grn(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to re open!",
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
                    data : {'grnAutoID':id},
                    url :"<?php echo site_url('Buyback/re_open_grn'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        get_goodReceiptNoteManagement_tableView();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        get_goodReceiptNoteManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.farmsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#grn_status').val('');
        $('#grnDatefrom').val('');
        $('#grnDateto').val('');
        $('#locationID').val('').change();
        $('#subLocationID').val('').change();
        $('#farmername').val(null).trigger("change");
        $('#sorting_1').addClass('selected');
        get_goodReceiptNoteManagement_tableView();
    }

    function excel_Export() {
        var form = document.getElementById('grn_filter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/export_grn_excel'); ?>';
        form.submit();
    }

    function referback_GoodReceiptnote(grnAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'grnAutoID': grnAutoID},
                    url: "<?php echo site_url('Buyback/referback_GoodReceiptnote'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            get_goodReceiptNoteManagement_tableView();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    function load_locationBase_sub_location(){
        var locationid = $('#locationID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {locationid: locationid},
            url: "<?php echo site_url('Buyback/fetch_dispatch_filterSubLocation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_subloacations').html(data);
                startMasterSearch();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_grn_subItems(id){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/load_sub_item_grn_view"); ?>',
            dataType: 'html',
            data: {grnDetailsID: id, type: 'View'},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#grnView_SubItem_view').html(data);
                $("#grnView_SubItem_model").modal('show');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#dispatchSubItem_view').html(xhr.responseText);
            }
        });
    }

</script>