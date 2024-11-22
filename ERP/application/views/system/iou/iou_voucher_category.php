<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo head_page( $this->lang->line('iou_category') , false);
$date_format_policy = date_format_policy();
$gl_code =fetch_glcode_claim_category();
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
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="add_iou_catergory()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('iou_category'); ?>
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="iou_catergory_view"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ioucategorymodal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('iou_category'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="iou_category_form"'); ?>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('common_description'); ?></label>
                    </div>
                    <div class="form-group col-sm-8">
                        <input type="text" name="Description" id="Description" class="form-control"
                               placeholder="Description">
                        <input type="hidden" class="form-control" name="expenseClaimCategoriesAutoID" id="expenseClaimCategoriesAutoID">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('common_gl_code'); ?></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('glcode', $gl_code, '', 'class="form-control select2" id="glcode"'); ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Maximum Limit</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="maxValue" id="maxValue" class="form-control">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Validity period</label>
                    </div>
                    <div class="form-group col-sm-3">
                        <input type="text" name="validity_dperiod" id="validity_dperiod" class="form-control" placeholder="Days">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Is Deductable</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin skin-square">
                            <div class="skin-section" id="extraColumns">
                                <input id="is_deductable" type="checkbox"
                                       data-caption="" class="columnSelected" name="is_deductable" value="1">
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title"><?php echo $this->lang->line('iou_is_fuel_usage'); ?></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="skin skin-square">
                            <div class="skin-section" id="extraColumns">
                                <input id="isfueluage" type="checkbox"
                                       data-caption="" class="columnSelected" name="isfueluage" value="1">
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="save_iou_category()" id="save_btn"><?php echo $this->lang->line('common_save'); ?></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var Type = 3;
    $(document).ready(function () {
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/iou/iou_category', '', '<?php echo $this->lang->line('iou_category'); ?>');
        });
        getioucategory_tableView();

    });

    function getioucategory_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'type': Type},
            url: "<?php echo site_url('Iou/iou_categorymaster_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_catergory_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function delete_iou_category(id) {
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
                    data: {'expenseClaimCategoriesAutoID': id},
                    url: "<?php echo site_url('Iou/delete_ioucategory'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', '<?php echo $this->lang->line('iou_category_deleted_successfully'); ?>');
                        getioucategory_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_iou_category() {

        var data = $('#iou_category_form').serializeArray();
        data.push({'name': 'gldes', 'value': $('#glcode option:selected').text()});

        data.push({'name': 'is_deductable', 'value': $('#is_deductable').prop('checked') ? 1 : 0});
        data.push({'name': 'type', 'value': Type});
       // data.push({'name': 'isfueluage', 'value': $('#isfueluage').prop('checked') ? 1 : 0});
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_iou_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if (data['status'] == true) {
                    getioucategory_tableView();
                    $('#ioucategorymodal').modal('hide');
                }
                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_iou_catergory(expenseClaimCategoriesAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'expenseClaimCategoriesAutoID': expenseClaimCategoriesAutoID},
            url: "<?php echo site_url('Iou/iou_categoryheader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                    $("#ioucategorymodal").modal({backdrop: "static"});
                    $('#maxValue').val(data.expenseclaimcategories['maxLimit']);
                    $('#validity_dperiod').val(data.expenseclaimcategories['validityPeriod']);
                    $('#Description').val(data.expenseclaimcategories['claimcategoriesDescription']);
                    $('#expenseClaimCategoriesAutoID').val(data.expenseclaimcategories['expenseClaimCategoriesAutoID']);
                    $('#glcode').val(data.expenseclaimcategories['glAutoID']).trigger("change");
                    if (data.expenseclaimcategories['isDeductable'] == 1) {
                        $('#is_deductable').iCheck('check');
                    } else {
                        $('#is_deductable').iCheck('uncheck');
                    }
                    if (data.expenseclaimcategories['fuelUsageYN'] == 1) {
                        $('#isfueluage').iCheck('check');
                    } else {
                        $('#isfueluage').iCheck('uncheck');
                    }
                    
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again') ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_iou_catergory(){
        $('#maxValue').val('');
        $('#validity_dperiod').val('');
        $('#Description').val('');
        $('#glcode').val(null).trigger("change");
        $('#expenseClaimCategoriesAutoID').val('');
        $('#is_deductable').iCheck('uncheck');
        $('#isfueluage').iCheck('uncheck');
        $("#ioucategorymodal").modal({backdrop: "static"});
    }


</script>