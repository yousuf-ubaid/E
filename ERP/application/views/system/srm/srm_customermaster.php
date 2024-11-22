<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('srm_customer_master');
echo head_page($title, false);
/*echo head_page('Customer Master', false);*/

$this->load->helper('srm_helper');
$customer_arr = all_srm_customer_drop();
$currncy_arr = all_srm_supplie_Currency_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
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

    .alpha-box{
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
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td style="background-color: white">
                    <span class="label label-success">&nbsp;</span>&nbsp;<?php echo $this->lang->line('common_active');?>
                </td><!--Active-->
                <td style="background-color: white">
                    <span class="label label-danger">&nbsp;</span>&nbsp;<?php echo $this->lang->line('srm_inactive');?>
                </td><!--Inactive-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary size-sm pull-right"
                onclick="fetchPage('system/srm/customer/srm_create_customer',null,'<?php echo $this->lang->line('srm_add_new_customer');?>','SRM');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('srm_new_customer');?><!--Add New Customer-->
        </button><!--New Customer-->
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-4" style="margin-left: 2%;">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchTask" type="text" class="form-control input-sm"
                                   placeholder="<?php echo $this->lang->line('srm_search_customers');?>"
                                   id="searchTask" onkeypress="startMasterSearch()"><!--Search Customers-->
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <?php echo form_dropdown('status', array('-1' =>  $this->lang->line('srm_select')/*'Select'*/, '1' =>  $this->lang->line('common_active')/*'Active'*/, '0' =>  $this->lang->line('srm_not_active')/*'Not Active'*/), '', 'class="form-control" id="filter_status" onchange="startMasterSearch()"'); ?>
                </div>
                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-11">
                    <div id="outputCustomerMasterTbl"></div>
                </div>
                <div class="col-sm-1">
                    <ul class="alpha-box">
                        <li><a href="#" class="customersorting selected" id="sorting_1" onclick="load_customer_filter('#',1)">#</a></li>
                        <li><a href="#" class="customersorting" id="sorting_2" onclick="load_customer_filter('A',2)">A</a></li>
                        <li><a href="#" class="customersorting" id="sorting_3" onclick="load_customer_filter('B',3)">B</a></li>
                        <li><a href="#" class="customersorting" id="sorting_4" onclick="load_customer_filter('C',4)">C</a></li>
                        <li><a href="#" class="customersorting" id="sorting_5" onclick="load_customer_filter('D',5)">D</a></li>
                        <li><a href="#" class="customersorting" id="sorting_6" onclick="load_customer_filter('E',6)">E</a></li>
                        <li><a href="#" class="customersorting" id="sorting_7" onclick="load_customer_filter('F',7)">F</a></li>
                        <li><a href="#" class="customersorting" id="sorting_8" onclick="load_customer_filter('G',8)">G</a></li>
                        <li><a href="#" class="customersorting" id="sorting_9" onclick="load_customer_filter('H',9)">H</a></li>
                        <li><a href="#" class="customersorting" id="sorting_10" onclick="load_customer_filter('I',10)">I</a></li>
                        <li><a href="#" class="customersorting" id="sorting_11" onclick="load_customer_filter('J',11)">J</a></li>
                        <li><a href="#" class="customersorting" id="sorting_12" onclick="load_customer_filter('K',12)">K</a></li>
                        <li><a href="#" class="customersorting" id="sorting_13" onclick="load_customer_filter('L',13)">L</a></li>
                        <li><a href="#" class="customersorting" id="sorting_14" onclick="load_customer_filter('M',14)">M</a></li>
                        <li><a href="#" class="customersorting" id="sorting_15" onclick="load_customer_filter('N',15)">N</a></li>
                        <li><a href="#" class="customersorting" id="sorting_16" onclick="load_customer_filter('O',16)">O</a></li>
                        <li><a href="#" class="customersorting" id="sorting_17" onclick="load_customer_filter('P',17)">P</a></li>
                        <li><a href="#" class="customersorting" id="sorting_18" onclick="load_customer_filter('Q',18)">Q</a></li>
                        <li><a href="#" class="customersorting" id="sorting_19" onclick="load_customer_filter('R',19)">R</a></li>
                        <li><a href="#" class="customersorting" id="sorting_20" onclick="load_customer_filter('S',20)">S</a></li>
                        <li><a href="#" class="customersorting" id="sorting_21" onclick="load_customer_filter('T',21)">T</a></li>
                        <li><a href="#" class="customersorting" id="sorting_22" onclick="load_customer_filter('U',22)">U</a></li>
                        <li><a href="#" class="customersorting" id="sorting_23" onclick="load_customer_filter('V',23)">V</a></li>
                        <li><a href="#" class="customersorting" id="sorting_24" onclick="load_customer_filter('W',24)">W</a></li>
                        <li><a href="#" class="customersorting" id="sorting_25" onclick="load_customer_filter('X',25)">X</a></li>
                        <li><a href="#" class="customersorting" id="sorting_26" onclick="load_customer_filter('Y',26)">Y</a></li>
                        <li><a href="#" class="customersorting" id="sorting_27" onclick="load_customer_filter('Z',27)">Z</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customermaster', '', 'Supplier Master');
        });
        load_customer_filter('#', 1);
        //load_customerMasterTable();

    });

    function load_customerMasterTable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var status = $('#filter_status').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchTask': searchTask,'filtervalue':filtervalue,status:status},
            url: "<?php echo site_url('srm_master/fetch_customer_all'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#outputCustomerMasterTbl').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_customer(id) {
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
                    data: {'customerID': id},
                    url: "<?php echo site_url('srm_master/delete_customer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        load_customerMasterTable();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        load_customerMasterTable();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.customersorting').removeClass('selected');
        $('#searchTask').val('');
        $('#filter_status').val(-1);
        $('#sorting_1').addClass('selected');
        load_customerMasterTable();
    }

    function load_customer_filter(value, id){
        $('.customersorting').removeClass('selected');
        $('#sorting_'+ id).addClass('selected');
        if(value != '#'){
            $('#search_cancel').removeClass('hide');
        }
        load_customerMasterTable(value)
    }

    function updatefavouritescustomers(){

    }

</script>