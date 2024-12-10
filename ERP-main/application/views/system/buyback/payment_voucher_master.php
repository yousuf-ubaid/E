<?php echo head_page('Vouchers', true);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$farmer = load_all_farms();
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
<form id="paymentvoucherfilter_frm">
    <div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode">Date From</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="voucherDatefrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="voucherDatefrom" class="form-control"  value=""  >
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="voucherDateto"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="voucherDateto"  class="form-control" value="" >
                </div>
            </div>

            <div class="col-sm-2">
                <label for="area">Farmer</label><br>
                <?php echo form_dropdown('farmer',$farmer, '', 'class="form-control select2" onchange="startMasterSearch()" id="farmer"'); ?>
            </div>


            <div class="col-sm-2">
                <label for="vouchertype">Voucher Type</label>
                <?php echo form_dropdown('vouchertype', array('' => 'Select Voucher Type', '1' => 'Payment Voucher', '2' => 'Receipt Voucher', '3' => 'Settlement', '4' => 'Journal Entry'), '', 'class="form-control" id="vouchertype" onchange="startMasterSearch()"'); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 text-right">
            <a href="#" type="button" class="btn btn-success btn-sm pull-right" style="margin-left: 2px" onclick="excel_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
            <button type="button" class="btn btn-primary"
                    onclick="fetchPage('system/buyback/create_payment_voucher',null,'Add New Voucher','BUYBACK');"><i
                        class="fa fa-plus"></i> New Voucher
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-4" style="margin-left: 2%;">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Voucher"
                                       id="searchTask">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2" style="margin-left: 1%;">
                        <?php echo form_dropdown('status', array('' => 'Select Status', '1' => 'Confirmed', '2' => 'Not Confirmed', '3' => 'Approved', '4' => 'Deleted'), '', 'class="form-control"  id="status" onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
</form>
<br>

<div class="row">
    <div class="col-sm-12">
        <div id="PaymentVoucherMaster_view"></div>
    </div>
</div>
<div class="col-xs-12" style="padding-right: 5px;">
    <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
        <p id="filterDisplay"></p>

        <nav>
            <ul class="list-inline" id="pagination-ul">

            </ul>
        </nav>
    </div>
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
        $('.headerclose').click(function () {
            fetchPage('system/buyback/payment_voucher_master', '', 'Payment Voucher');
        });
        getPaymentVoucherManagement_tableView();
    });
    Inputmask().mask(document.querySelectorAll("input"));
    $('.select2').select2();

    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        getPaymentVoucherManagement_tableView(data_pagination, uriSegment);
    }

    function getPaymentVoucherManagement_tableView(pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var data = $('#paymentvoucherfilter_frm').serializeArray();
        data.push({'name': 'pageID', 'value': pageID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/load_paymentVoucherManagement_view'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#PaymentVoucherMaster_view').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function excel_Export() {
     /*   var form = document.getElementById('paymentvoucherfilter_frm');
        form.target = '_blank';
      form.method = 'post';
      form.post = $('#paymentvoucherfilter_frm').serializeArray();
        form.submit();
*/
        var form = document.getElementById('paymentvoucherfilter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/export_paymentVoucher_excel'); ?>';
        form.submit();
    }


    function delete_paymentVoucher(id) {
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
                    data: {'pvMasterAutoID': id},
                    url: "<?php echo site_url('Buyback/delete_paymentVoucher_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        getPaymentVoucherManagement_tableView();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function reOpen_paymentVoucher(id){
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
                    data : {'pvMasterAutoID':id},
                    url :"<?php echo site_url('Buyback/reOpen_paymentVoucher'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        getPaymentVoucherManagement_tableView();
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
        getPaymentVoucherManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.farmsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#voucherDatefrom').val('');
        $('#voucherDateto').val('');
        $('#vouchertype').val('');
        $('#status').val('');
        $("#farmer").val(null).trigger("change");

        $('#sorting_1').addClass('selected');
        getPaymentVoucherManagement_tableView();
    }

    function referback_paymentVoucher(pvMasterAutoID) {
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
                    data: {'pvMasterAutoID': pvMasterAutoID},
                    url: "<?php echo site_url('Buyback/referback_paymentVoucher'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            getPaymentVoucherManagement_tableView();
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

</script>