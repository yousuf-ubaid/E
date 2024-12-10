<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_investment_types');
echo head_page($title, false);

$gl_code_arr = company_PL_account_drop();
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
                                           placeholder="<?php echo $this->lang->line('fn_man_search_investment');?>" id="searchTask" >
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
                        <div class="col-sm-7">
                            <button type="button" class="btn btn-primary pull-right" onclick="add_type()" style="margin-right: 5px; margin-bottom: 10px;">
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

<script>

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/investment-types', '', 'Investment Types');
        });

        filter('#', 1);
    });

    $('#gl_code').select2();

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });

    function add_type(){
        $('#inv-type-model').modal('show');
        $('#inv_type_form')[0].reset();
        $('#gl_code').val('').change();
        $('#inv-type-frm-btn').attr('onclick', 'save_investment_type()');
        $('#inv-type-modal-title').text('<?php echo $this->lang->line('fn_man_new_investment_type');?>');
    }

    function save_investment_type(){
        var postData = $('#inv_type_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/save_investment_type'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    filter('#', 1);
                    $('#inv-type-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function edit_investment(invTypID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invTypID': invTypID},
            url: "<?php echo site_url('Fund_management/get_investment_type_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#inv_type_form')[0].reset();
                $('#inv-type-frm-btn').attr('onclick', 'update_investment_type()');
                $('#edit_invID').val(invTypID);
                $('#description').val(data['description']);
                $('#gl_code').val(data['glCode']).change();

                $('#inv-type-modal-title').text('<?php echo $this->lang->line('fn_man_edit_share_holder');?>');
                $('#inv-type-model').modal('show');

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function update_investment_type(){
        var postData = $('#inv_type_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/update_investment_type'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    filter('#', 1);
                    $('#inv-type-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function filter(value, id){
        $('.contact_sorting').removeClass('selected');
        $('#sorting_'+ id).addClass('selected');
        if(value != '#'){
            $('#search_cancel').removeClass('hide');
        }
        investment_data_table_view(value);
    }

    function investment_data_table_view(filtervalue) {
        var searchTask = $('#searchTask').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchTask': searchTask,'filterValue':filtervalue},
            url: "<?php echo site_url('Fund_management/load_investment_type_view'); ?>",
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

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.contact_sorting').removeClass('selected');
        $('#searchTask').val('');
        $('#sorting_1').addClass('selected');
        investment_data_table_view();
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        investment_data_table_view();
    }
</script>



<div id="inv-type-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="inv-type-modal-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="inv_type_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="description" id="description" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="gl_code"><?php echo $this->lang->line('common_gl_code');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('gl_code', $gl_code_arr, '', 'class="form-control select2" id="gl_code"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="edit_invID" id="edit_invID" value="0">
                    <button class="btn btn-primary" type="button" id="inv-type-frm-btn"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
