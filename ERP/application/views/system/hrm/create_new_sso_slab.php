<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$masterID = trim($this->input->post('page_id'));
$description = trim($this->input->post('data_arr'));
$lastEndRange = 0;
$slabDetails = get_sso_slabDetails($masterID);
$salary_categories = salary_categories(array('A', 'D'));
$pay_groups = get_payGroup(2);
$title = $this->lang->line('hrms_payroll_slab_master');
$dPlace = $this->common_data['company_data']['company_default_decimal'];
echo head_page($title, false);
?>

<style>
    .declarationTable td:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable th:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable tbody td:not(:first-child):not(:last-child):hover {
        cursor: pointer !important;
        background-color: #DEDEDE;
    }

    #slab_detail_form .control-label{
        text-align: left !important;
    }
</style>

<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td width="85px"><?php echo $this->lang->line('common_description');?> : <!--Description--></td>
                    <td class="bgWhite" colspan="2">
                        <!--<a href="#" data-type="text"
                           data-placement="bottom"
                           data-url="<?php /*echo site_url('Employee/ajax_update_ssoSlabDescription?masterID='.$masterID) */?>"
                           data-pk="<?php /*echo $description; */?>"
                           data-name="name"
                           data-title="Name"
                           class="xeditable"
                           data-value="<?php /*echo $description; */?>">
                            <?php /*echo $description*/?>
                        </a>-->
                        <a href="#" data-type="text"
                           data-placement="bottom"
                           data-title="Edit Description"
                           data-pk="<?php echo $description?>"
                           id="description_xEditable"
                           data-value="<?php echo $description; ?>">
                            <?php echo $description?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>

<h4>
    <?php echo $this->lang->line('hrms_payroll_slab_detail');?><!--Slab Detail-->
    <button type="button" class="btn btn-primary pull-right"
            onclick="add_slabDetail()"><i
            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_detail');?><!--Add Detail-->
    </button>
</h4>
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td style="font-weight: 700; text-align: center; width:25px">#</td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_start_range_amount');?><!--Range Start Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_end_range_amount');?><!--Range End Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_formula');?><!--Formula--></td>
                    <td style="font-weight: 700; text-align: center; width:80px"><?php echo $this->lang->line('common_action');?><!--Action--></td>
                </tr>
                <?php
                $i = 1;
                $decodeUrl = site_url('Employee/formulaDecode/isSSO_slab');
                $count_slabDetails = count($slabDetails);
                if (!empty($slabDetails)) {
                    foreach ($slabDetails as $val) {
                        $detailID = $val['ssoSlabDetailID'];
                        $formula = $val['formulaString'];
                        $descriptionDet = '( '.number_format($val['startRangeAmount'], $dPlace).' - '. number_format($val['endRangeAmount'], $dPlace) .' )';
                        $encode = payGroup_formulaBuilder_to_sql('encode', $formula, $salary_categories, $pay_groups);
                        $inputStr = '<a onclick="formulaModalOpen(\''.$descriptionDet.'\', \''.$detailID.'\', \''.$decodeUrl.'\', \'row_'.$i.'\')">';
                        $inputStr .= '<span title="Formula" rel="tooltip" class="fa fa-superscript"></a>';
                        ?>
                        <tr>
                            <td ><?php echo $i; ?></td>
                            <td style="text-align: right"><?php echo number_format($val['startRangeAmount'], $dPlace) ?></td>
                            <td style="text-align: right"><?php echo number_format($val['endRangeAmount'], $dPlace) ?></td>
                            <td id="row_<?php echo $i;?>"><?php echo $encode[0];?></td>
                            <td style="text-align: right">
                                <?php
                                echo $inputStr;
                                if( $i == $count_slabDetails){?>
                                &nbsp;&nbsp; | &nbsp;&nbsp;
                                <a onclick="delete_item(<?php echo $detailID; ?>,<?php echo $val['ssoSlabMasterID']; ?>,<?php echo $val['startRangeAmount']; ?>);">
                                    <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span></a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php
                        $lastEndRange = $val['endRangeAmount'] + 1;
                        $i++;
                    }
                } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?><!--No records Found--></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="slabDetail_Modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="slab_detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_slab_detail');?><!--Slab Detail--></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_amount" class="col-sm-7 control-label">
                            <?php echo $this->lang->line('hrms_payroll_start_range_amount');?><!--Start Range Amount-->
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="start_amount" id="start_amount" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_amount" class="col-sm-7 control-label">
                            <?php echo $this->lang->line('hrms_payroll_end_range_amount');?><!--End Range Amount-->
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control number" name="end_amount" id="end_amount">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="masterID" value="<?php echo $masterID;?>">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
$items = [
    'MA_MD' => false,
    'balancePay' => true,
    'SSO' => false,
    'payGroup' => false,
    'only_salCat_payGroup' => true
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var p_id = <?php echo json_encode($masterID); ?>;
    var description = <?php echo json_encode($description); ?>;
    var lastEndRange = <?php echo json_encode( number_format($lastEndRange, $dPlace)); ?>;
    var urlSave = '<?php echo site_url('Employee/save_ssoSlabsFormula') ?>';
    var isPaySheetGroup = 0;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/SSO_slab_master', p_id, 'Slab Master');
        });

        $("[rel=tooltip]").tooltip();

        $('#slab_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                start_amount: {validators: {notEmpty: {message: 'Start Range is required.'}}},
                end_amount: {validators: {notEmpty: {message: 'End Range  is required.'}}}
            },
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_sso_slabs_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#slabDetail_Modal").modal('hide');
                        LoadSlabDetail();
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#description_xEditable').editable({
            url: '<?php echo site_url('Employee/ajax_update_ssoSlabDescription?masterID='.$masterID) ?>',
            send: 'always',
            ajaxOptions: {
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if( data[0] == 's'){
                        var description_xEditable = $('#description_xEditable');
                        setTimeout(function (){
                            description_xEditable.attr('data-pk', description_xEditable.html());
                            description = $.trim(description_xEditable.html());
                        },400);

                    }else{
                        var oldVal = $('#description_xEditable').data('pk');
                        setTimeout(function (){
                            $('#description_xEditable').editable('setValue', oldVal );
                        },300);
                    }
                },
                error: function (xhr) {
                    myAlert('e', xhr.responseText);
                }
            }
        });
    });

    function add_slabDetail() {
        $('#slab_detail_form').bootstrapValidator('resetForm', true);
        $("#start_amount").val(lastEndRange);
        $("#slabDetail_Modal").modal({backdrop: "static"});
    }

    function delete_item(detailID, masterID, strRange) {
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
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID, 'masterID':masterID, 'strRange':strRange},
                    url: "<?php echo site_url('Employee/delete_ssoSlabDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            LoadSlabDetail();
                        }
                        stopLoad();

                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Please try again');
                    }
                });
            });
    }

    function LoadSlabDetail() {
        setTimeout(function(){
            fetchPage('system/hrm/create_new_sso_slab', p_id,'HRMS', '', description);
        },400);
    }

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
</script>

<?php
