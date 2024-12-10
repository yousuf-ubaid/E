<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$employee_arr = all_employee_drop();
$currency_arr = all_currency_new_drop();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
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
        z-index: 100;
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

<?php echo form_open('', 'role="form" id="usermaster_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('iou_user_detail') ?></h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_name') ?></label>
            </div>


                <div class="form-group col-sm-4">
                       <span class="input-req" title="<?php echo $this->lang->line('iou_required_field'); ?>">
                    <input type="text" name="employeeName" id="employeeName" class="form-control"
                           placeholder="Employee Name" autocomplete="off">
                       <span class="input-req-inner"></span>
                </div>
                <input type="hidden" name="userid" id="userid_edit">

        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_currency'); ?></label>
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field'); ?>">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID"  required'); ?>
                            <span class="input-req-inner"></span>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('iou_phone_number'); ?></label>
            </div>
            <div class="form-group col-sm-4">
                   <span class="input-req" title="<?php echo $this->lang->line('iou_required_field'); ?>">
                <input type="text" name="phonenumber" id="phonenumber" class="form-control"
                       placeholder="Phone Number" autocomplete="off">
                   <span class="input-req-inner"></span>
            </div>
        </div>


        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address'); ?></label>
            </div>
            <div class="form-group col-sm-4">
                   <span class="input-req" title="<?php echo $this->lang->line('iou_required_field'); ?>">
                            <textarea class="form-control"
                                      id="address"
                                      name="address"
                                      rows="2"></textarea>
                         <span class="input-req-inner"></span>
            </div>
        </div>



        <div class="row"  style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title"><?php echo $this->lang->line('iou_is_active'); ?></label>
            </div>
            <div class="form-group col-sm-4" style="padding-left: 0px;">
                <div class="skin-section extraColumns">
                    <label class="radio-inline">
                        <div class="skin-section extraColumnsgreen">
                            <label for="checkbox"><?php echo $this->lang->line('common_active'); ?>&nbsp;&nbsp;</label>
                            <input id="active" type="radio" data-caption="" class="columnSelected"
                                   name="active" value="1">
                        </div>
                    </label>
                    <label class="radio-inline">
                        <div class="skin-section extraColumnsgreen">
                            <label for="checkbox"><?php echo $this->lang->line('common_not_active'); ?>&nbsp;&nbsp;</label>
                            <input id="inactive" type="radio" data-caption="" class="columnSelected"
                                   name="active" value="0">
                        </div>
                    </label>
                </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="text-right m-t-xs">
        <div class="form-group col-sm-8" style="margin-top: 10px;">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save'); ?></button>
        </div>
    </div>
    </div>
</form>

<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel"><?php echo $this->lang->line('iou_link_user'); ?></h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label"><?php echo $this->lang->line('iou_user'); ?> </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $employee_arr, '', 'class="form-control select2" id="employee_id" onchange="fetchempcurrency(this.value)" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()"><?php echo $this->lang->line('iou_add_driver'); ?> </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            number_validation();
            fetchPage('system/iou/iou_user', '', '<?php echo $this->lang->line('iou_users'); ?>');
        });
        $('.select2').select2();

        userid = null;


        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            userid = p_id;
            load_iouuser_header();
        }


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        });


        $('#usermaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>',
            excluded: [':disabled'],
            fields: {
                employeeName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>'}}},
                phonenumber: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_phone_number_is_required');?>'}}},
                address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_address_is_required');?>'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Iou/save_user_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1],data[2]);
                    if (data[0] == 's') {
                        userid = data[2];
                        $('#userid_edit').val(userid);
                        fetchPage('system/iou/iou_user', '', '<?php echo $this->lang->line('iou_users'); ?>');
                    }


                },
                error: function () {
                    alert ('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        function clearEmployee() {
            $('#employee_id').val('').change();
            $('#employeeName').val('').trigger('input');
            $('#employeeName').prop('readonly', false);
            EIdNo = null;
        }
        function link_employee_model() {
            /*$('#employee_id').val('').change();*/
            $('#emp_model').modal('show');
        }
    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', '<?php echo $this->lang->line('iou_select_a_user'); ?>');
        } else {
            EIdNo = employee_id;
            var empName = $("#employee_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#employeeName').val($.trim(empName)).trigger('input');
            $('#employeeName').prop('readonly', true);
            $('#emp_model').modal('hide');
        }
    }

    function fetchempcurrency(empid) {
        if(empid)
        {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empid': empid},
                    url: "<?php echo site_url('Iou/fetch_iou_employee_currency'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#transactionCurrencyID').val(data['payCurrencyID']).change();
                        stopLoad();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }

    }
    function load_iouuser_header() {
        if (userid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'userid': userid},
                url: "<?php echo site_url('Iou/load_iou_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#userid_edit').val(userid);
                        $('#employeeName').val(data['userName']);
                        $('#transactionCurrencyID').val(data['currencyID']).change();
                        $('#phonenumber').val(data['PhoneNo']);
                        $('#address').val(data['Address']);
                        setTimeout(function () {
                            if (data['isActive'] == 1) {
                                $('#active').iCheck('check');
                            }else if(data['isActive'] == 0){
                                $('#inactive').iCheck('check');
                            } }, 500);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert ('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                    refreshNotifications(true);
                }
            });
        }
    }

</script>