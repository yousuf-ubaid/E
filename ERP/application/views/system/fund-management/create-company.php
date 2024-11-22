<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$page_id = trim($this->input->post('page_id'));
$tittle = ($page_id == null)? $this->lang->line('fn_man_add_new_company'): $this->lang->line('fn_man_edit_company');

echo head_page($tittle, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$countries_arr = fetch_emp_countries();
$designation_arr = getDesignationDrop(true);
$currency_arr = all_currency_new_drop();
$industry_arr = industryTypes_drop();
$incomeStatement_arr = statementTemplate_drop(5);
$balanceSheet_arr = statementTemplate_drop(6);

?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #060606
        }

        span.input-req-inner {
            width: 20px;
            height: 40px;
            position: absolute;
            overflow: hidden;
            display: block;
            right: 4px;
            top: -15px;
            -webkit-transform: rotate(135deg);
            -ms-transform: rotate(135deg);
            transform: rotate(135deg);
        }

        span.input-req-inner:before {
            font-size: 20px;
            content: "*";
            top: 15px;
            right: 1px;
            color: #fff;
            position: absolute;
            z-index: 2;
            cursor: default;
        }

        span.input-req-inner:after {
            content: '';
            width: 35px;
            height: 35px;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
            background: #f45640;
            position: absolute;
            top: 7px;
            right: -29px;
        }

        .search_cancel {
            background-color: #f3f3f3;
            border: solid 1px #dcdcdc;
            vertical-align: middle;
            padding: 3px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="m-b-md" id="wizardControl2">
        <a class="btn btn-primary" href="#step1" data-toggle="tab">
            <?php echo $this->lang->line('fn_man_step_one').' - '.$this->lang->line('fn_man_master_data');?>
        </a>
        <a class="btn btn-default btn-wizard disabled" href="#step2" data-toggle="tab" onclick="get_share_holders_details()">
            <?php echo $this->lang->line('fn_man_step_two').' - '. $this->lang->line('fn_man_shareHolder');?>
        </a>
        <a class="btn btn-default btn-wizard disabled" href="#step3" data-toggle="tab" onclick="get_attachments_details()">
            <?php echo $this->lang->line('fn_man_step_three').' - '. $this->lang->line('common_attachments');?>
        </a>
        <a class="btn btn-default btn-wizard disabled" href="#step4" data-toggle="tab">
            <?php echo $this->lang->line('fn_man_step_four').' - '. $this->lang->line('fn_man_users');?>
        </a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="frm_company" autocomplete="off"'); ?>
                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2><?php echo $this->lang->line('fn_man_basic_data');?></h2><!--BASIC DETAILS-->
                        </header>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1"> &nbsp; </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('fn_man_company_name');?><!--Company Name--></label>
                            </div>
                            <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field">
                                   <input type="text" name="company_name" id="company_name" class="form-control"
                                     placeholder="<?php echo $this->lang->line('fn_man_company_name');?>" required>
                                   <span class="input-req-inner"></span>
                                </span>
                                <input type="hidden" name="editID" id="edit-id">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">&nbsp;</div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_comment');?></label><!--Comment-->
                            </div>
                            <div class="form-group col-sm-4">
                                <textarea class="form-control" id="comment" name="comment" rows="2" ></textarea>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">&nbsp;</div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_industry_type');?></label>
                            </div>
                            <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field">
                                   <?php echo form_dropdown('industryID', $industry_arr, '', 'class="form-control select2" id="industryID"'); ?>
                                   <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">&nbsp;</div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_currency');?></label>
                            </div>
                            <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('com_currencyID', $currency_arr, '', 'class="form-control select2" id="com_currencyID"'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2><?php echo $this->lang->line('fn_man_contact_details');?></h2><!--CONTACT DETAILS-->
                        </header>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_email');?> </label><!--Email-->
                            </div>
                            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="email" id="email"
                                         class="form-control" placeholder="<?php echo $this->lang->line('common_email');?>"  required>
                                <span class="input-req-inner"></span>
                            </span>
                            </div><!--Email-->
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_telephone');?> </label><!--Telephone-->
                            </div>
                            <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field">
                                    <input type="text" name="telephone" id="telephone" class="form-control"
                                       placeholder="<?php echo $this->lang->line('common_telephone');?>"><!--Telephone-->
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="text" name="fax" id="fax" class="form-control" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_web');?></label><!--Web site-->
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="text" name="web_site" id="web_site" class="form-control" placeholder="<?php echo $this->lang->line('common_web');?>"><!--Web site-->
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2 style="text-transform: uppercase"><?php echo $this->lang->line('common_address');?></h2>
                        </header>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_address');?> </label><!--Address-->
                            </div>
                            <div class="form-group col-sm-4">
                                <span class="input-req" title="Required Field"><textarea class="form-control" id="address" name="address" rows="2"></textarea><span class="input-req-inner"></span></span>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('fn_man_postal_code');?></label><!--Postal Code-->
                            </div>
                            <div class="form-group col-sm-4">
                                <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="<?php echo $this->lang->line('fn_man_postal_code');?>"><!--Postal Code-->
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('common_Country');?></label><!--Country-->
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2 style="text-transform: uppercase"><?php echo $this->lang->line('common_template');?></h2>
                        </header>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('fn_man_income_statement');?><!--Income Statements--></label>
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('incomeStatement', $incomeStatement_arr, '', 'class="form-control select2" id="incomeStatement"'); ?>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title"><?php echo $this->lang->line('fn_man_balance_sheet');?><!--Balance sheet--></label>
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('balanceSheet', $balanceSheet_arr, '', 'class="form-control select2" id="balanceSheet"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="form-group col-sm-1" style="margin-top: 10px;">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="text-right m-t-xs">
                            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>

        <div id="step2" class="tab-pane">
            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-primary pull-right" onclick="add_share_holders()" style="margin-right: 15px; margin-bottom: 10px;">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
                    </button>
                </div>

                <div class="col-sm-12">
                    <div class="" id="share_holders_details"></div>
                </div>
            </div>
        </div>

        <div id="step3" class="tab-pane">
            <div class="row">
                <div class="col11-sm-12">
                    <div class="" id="attach_details"></div>
                </div>
            </div>
        </div>

        <div id="step4" class="tab-pane">
            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-primary pull-right" onclick="add_users()" style="margin-right: 15px; margin-bottom: 10px;">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
                    </button>
                </div>

                <div class="col-sm-12">
                    <div class="" id="user_details"></div>
                </div>
            </div>
        </div>
    </div>


<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

<script type="text/javascript">
    var fm_ID = <?php echo json_encode($page_id); ?>;
    var comCurrencyID = null;

    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/fund-management/company-master', '', 'FM');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        var request_url = "<?php echo site_url('Fund_management/save_company_details'); ?>";
        if (fm_ID != '') {
            $('.btn-wizard').removeClass('disabled');
            get_company_basic_details();
            request_url = "<?php echo site_url('Fund_management/update_company_details'); ?>";
        }


        $('#frm_company').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                company_name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('fn_man_company_name_is_required');?>.'}}},
                com_currencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},
                industryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('fn_man_industry_is_required');?>.'}}},
                email: {validators: {notEmpty: {message: '<?php echo $this->lang->line('fn_man_email_is_required');?>.'}}},
                telephone: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_telephone_is_required');?>.'}}},
                address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('fn_man_address_is_required');?>.'}}}
            },
        })
           .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: request_url,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        comCurrencyID = $('#com_currencyID').val();
                        if(data['id'] != 0){
                            edit_company_data( data['id'] );
                        }

                    } else {
                        $('.btn-primary').removeAttr('disabled');
                    }

                },
                error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        });

    });

    function get_company_basic_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': fm_ID},
            url: "<?php echo site_url('Fund_management/get_company_basic_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (!jQuery.isEmptyObject(data)) {
                    $('#edit-id').val(fm_ID);
                    $('#prefix').val(data['prefix']);
                    $('#company_name').val(data['company_name']);
                    $('#comment').val(data['com_comment']);
                    $('#email').val(data['email_id']);
                    $('#telephone').val(data['tel_no']);
                    $('#fax').val(data['fax_no']);
                    $('#web_site').val(data['web_site']);
                    $('#address').val(data['address']);
                    $('#postal_code').val(data['postal_code']);
                    $('#countryID').val(data['countryID']).change();
                    $('#industryID').val(data['industryTypesID']).change();
                    $('#com_currencyID').val(data['currencyID']).change();
                    if(data['incomeStatementID'] != 0){
                        $('#incomeStatement').val(data['incomeStatementID']).change();
                    }
                    if(data['balanceSheet'] != 0){
                        $('#balanceSheet').val(data['balanceSheet']).change();
                    }

                    comCurrencyID = data['currencyID'];

                    get_user_details();

                }
                else{
                    myAlert('e', 'Master record not found.<br/>Please refresh the page and try again.');
                }

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*Start User*/
    function add_users(){
        $('#newUser-model').modal('show');
        $('#add_user_form')[0].reset();
        $('#user-frm-btn').attr('onclick', 'save_user_data()');
        $('#user-modal-title').text('<?php echo $this->lang->line('fn_man_add_user');?>');
        $('#designationID').val('').change();
        $('.credential-conf').show();
    }

    function edit_user(uID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'uID': uID},
            url: "<?php echo site_url('Fund_management/get_user_basic_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#add_user_form')[0].reset();
                $('#user-frm-btn').attr('onclick', 'update_user_data()');
                $('#edit_userID').val(uID);
                $('#e_name').val(data['contactName']);
                $('#designationID').val(data['designationID']).change();
                $('#telNo').val(data['telNo']);
                $('#emailID').val(data['email']);

                $('#user-modal-title').text('<?php echo $this->lang->line('fn_man_edit_user');?>');
                $('#newUser-model').modal('show');
                //$('#user_name').val(data['userName']);
                $('.credential-conf').hide();

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function save_user_data(){
        var postData = $('#add_user_form').serializeArray();
        postData.push({name:'masterID', value:fm_ID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/save_user_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    get_user_details();
                    $('#newUser-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function update_user_data(){
        var postData = $('#add_user_form').serializeArray();
        postData.push({name:'masterID', value:fm_ID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/update_user_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    get_user_details();
                    $('#newUser-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_user_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'id': fm_ID},
            url: "<?php echo site_url('Fund_management/get_company_user_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#user_details').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    /*End User*/

    /*Start share holders*/
    function add_share_holders(){
        $('#shareHolder-model').modal('show');
        $('#share_holder_form')[0].reset();
        $('#currencyID').val(comCurrencyID).change();
        $('#share-holder-frm-btn').attr('onclick', 'save_share_holder_data()');
        $('#share-holder-modal-title').text('<?php echo $this->lang->line('fn_man_add_share_holder');?>');
    }

    function save_share_holder_data(){
        var postData = $('#share_holder_form').serializeArray();
        postData.push({name:'masterID', value:fm_ID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/save_share_holder_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    get_share_holders_details();
                    $('#shareHolder-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function get_share_holders_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'id': fm_ID},
            url: "<?php echo site_url('Fund_management/get_company_share_holder_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#share_holders_details').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function edit_share(shareID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'shareID': shareID},
            url: "<?php echo site_url('Fund_management/get_share_holder_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#share_holder_form')[0].reset();
                $('#share-holder-frm-btn').attr('onclick', 'update_share_data()');
                $('#edit_shareID').val(shareID);
                $('#holderName').val(data['holderName']);
                $('#percent').val(data['sharePercentage']);
                $('#amount').val(data['shareAmount']);
                $('#currencyID').val(comCurrencyID).change();

                $('#share-holder-modal-title').text('<?php echo $this->lang->line('fn_man_edit_share_holder');?>');
                $('#shareHolder-model').modal('show');

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function update_share_data(){
        var postData = $('#share_holder_form').serializeArray();
        postData.push({name:'masterID', value:fm_ID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/update_share_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    get_share_holders_details();
                    $('#shareHolder-model').modal('hide');
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    /*End share holders*/


    /*Start attachments*/
    function get_attachments_details(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'documentSystemCode': fm_ID, 'systemDocumentID': 'FMC'},
            url: "<?php echo site_url('Fund_management/get_attachment_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attach_details').html(data);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    /*End attachments*/

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

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
    });

</script>

<div id="newUser-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="user-modal-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="add_user_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="e_name"><?php echo $this->lang->line('common_name');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="e_name" id="e_name" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="designationID"><?php echo $this->lang->line('common_designation');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('designationID', $designation_arr, '', 'class="form-control select2" id="designationID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="telNo"><?php echo $this->lang->line('common_telephone');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="telNo" id="telNo" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="emailID"><?php echo $this->lang->line('common_email');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="emailID" id="emailID" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row credential-conf">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="user_name"><?php echo $this->lang->line('common_user_name');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="user_name" id="user_name" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row credential-conf">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="password"><?php echo $this->lang->line('common_password');?></label>
                            <div class="col-sm-6">
                                <input type="password" name="password" id="password" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="edit_userID" id="edit_userID" value="0">
                    <button class="btn btn-primary" type="button" id="user-frm-btn"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div id="shareHolder-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="share-holder-modal-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="share_holder_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="holderName"><?php echo $this->lang->line('common_name');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="holderName" id="holderName" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="percent"><?php echo $this->lang->line('fn_man_percent');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="percent" id="percent" class="form-control number" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="currencyID"><?php echo $this->lang->line('common_currency');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" disabled'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="amount"><?php echo $this->lang->line('fn_man_amount');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="amount" id="amount" class="form-control number" value="" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="edit_shareID" id="edit_shareID" value="0">
                    <button class="btn btn-primary" type="button" id="share-holder-frm-btn"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
