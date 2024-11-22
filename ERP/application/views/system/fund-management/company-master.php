<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_title');
echo head_page($title, false);


$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
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
        border: 1px solid #89aedc99;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
        border-bottom: 1px solid #89aedc99;
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

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-5"> </div>
    <div class="col-md-4 text-center"> </div>
    <div class="col-md-3 text-right"> </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-4" style=" ">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="<?php echo $this->lang->line('fn_man_search_company');?>" id="searchTask" >
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <span class="tipped-top hide" id="search_cancel">
                            <a id="cancelSearch" href="#" onclick="clearSearchFilter()">
                                <img src="<?php echo base_url("images/crm/cancel-search.gif") ?>">
                            </a>
                        </span>
                    </div>
                    <div class="col-sm-3" style=" ">
                        <?php echo form_dropdown('doc_status', document_status_drop(), '', 'class="form-control select2" id="doc_status" onchange="company_data_table_view()"'); ?>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary pull-right" onclick="edit_company_data(null)">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
                        </button><!--Add New Contact-->
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-11">
                    <div id="table_view"></div>
                </div>
                <div class="col-sm-1">
                    <ul class="alpha-box">
                        <li><a href="#" class="contact_sorting selected" id="sorting_1" onclick="filter('#',1)">#</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_2" onclick="filter('A',2)">A</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_3" onclick="filter('B',3)">B</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_4" onclick="filter('C',4)">C</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_5" onclick="filter('D',5)">D</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_6" onclick="filter('E',6)">E</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_7" onclick="filter('F',7)">F</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_8" onclick="filter('G',8)">G</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_9" onclick="filter('H',9)">H</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_10" onclick="filter('I',10)">I</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_11" onclick="filter('J',11)">J</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_12" onclick="filter('K',12)">K</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_13" onclick="filter('L',13)">L</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_14" onclick="filter('M',14)">M</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_15" onclick="filter('N',15)">N</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_16" onclick="filter('O',16)">O</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_17" onclick="filter('P',17)">P</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_18" onclick="filter('Q',18)">Q</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_19" onclick="filter('R',19)">R</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_20" onclick="filter('S',20)">S</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_21" onclick="filter('T',21)">T</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_22" onclick="filter('U',22)">U</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_23" onclick="filter('V',23)">V</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_24" onclick="filter('W',24)">W</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_25" onclick="filter('X',25)">X</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_26" onclick="filter('Y',26)">Y</a></li>
                        <li><a href="#" class="contact_sorting" id="sorting_27" onclick="filter('Z',27)">Z</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/company-master', '', 'Contact');
        });

        filter('#', 1);
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function company_data_table_view(filtervalue) {
        var searchTask = $('#searchTask').val();
        var doc_status = $('#doc_status').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchTask': searchTask,'filterValue':filtervalue, 'doc_status':doc_status},
            url: "<?php echo site_url('Fund_management/load_company_data_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#table_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_contact(id) {
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
                    data: {'contactID': id},
                    url: "<?php echo site_url('Fund_management/delete_contact_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        company_data_table_view();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        company_data_table_view();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.contact_sorting').removeClass('selected');
        $('#searchTask').val('');
        $('#sorting_1').addClass('selected');
        company_data_table_view();
    }

    function filter(value, id){
        $('.contact_sorting').removeClass('selected');
        $('#sorting_'+ id).addClass('selected');
        if(value != '#'){
            $('#search_cancel').removeClass('hide');
        }
        company_data_table_view(value);
    }

    function edit_company_data(id){
        fetchPage('system/fund-management/create-company', id, '','FM')
    }

    function get_document_status_more_details(sysType, documentSystemCode, statusType){
        var title_str = '<?php echo $this->lang->line('common_document');?>';

        switch (statusType){
            case 'pending': title_str = '<?php echo $this->lang->line('fn_man_pending');?>'+' '+title_str; break;
            case 'elapse': title_str = '<?php echo $this->lang->line('fn_man_expire_expire');?>'+' '+title_str; break;
            case 'expiry': title_str = '<?php echo $this->lang->line('fn_man_expiry_remain');?>'+' '+title_str; break;
            default : title_str = 'Not a  valid selection';
        }

        $('#document_status_more_details-title').text(title_str);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'sysType': sysType, 'documentSystemCode':documentSystemCode, 'statusType':statusType},
            url: "<?php echo site_url('Fund_management/get_document_status_more_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#document_status_more_details-model').modal('show');
                $('#document_status_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>


<?php
