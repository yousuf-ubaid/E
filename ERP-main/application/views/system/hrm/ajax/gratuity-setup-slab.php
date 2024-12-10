<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_gratuity_setup');
$masterID = $this->input->post('page_id');
echo head_page($title, false);

$lastEndRange = 0.001;
$slabDetails = get_gratuity_slabDetails($masterID);
$salary_categories = salary_categories(array('A', 'D'));
?>

<style>
    #slab_detail_form .control-label{
        text-align: left !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="add_slabDetail()"><i
                class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="gratuity_setup_table" class="<?php echo table_class(); ?>">
        <tr>
            <td style="font-weight: 700; text-align: center;">#</td>
            <td style="font-weight: 700; text-align: center;"><?php echo $this->lang->line('common_title');?></td>
            <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_start_range');?></td>
            <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_end_range');?></td>
            <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_formula');?></td>
            <td style="font-weight: 700; text-align: center; width:80px"><?php echo $this->lang->line('common_action');?></td>
        </tr>
        <?php
        $i = 1;
        $decodeUrl = site_url('Employee/formulaDecode/GRATUITY-SLAB');
        $count_slabDetails = count($slabDetails);
        if (!empty($slabDetails)) {
            foreach ($slabDetails as $val) {
                $detailID = $val['id'];
                $formula = $val['formulaString'];
                $descriptionDet = '( '.$val['startYear'].' - '. $val['endYear'] .' )';
                $encode = payGroup_formulaBuilder_to_sql('encode', $formula, $salary_categories, null);
                $inputStr = '<a onclick="formulaModalOpen(\''.$descriptionDet.'\', \''.$detailID.'\', \''.$decodeUrl.'\', \'row_'.$i.'\')">';
                $inputStr .= '<span title="Formula" rel="tooltip" class="fa fa-superscript"></a>';
                ?>
                <tr>
                    <td style="width:15px" ><?php echo $i; ?></td>
                    <td style="width:120px" >
                        <a href="#" data-type="text" data-placement="right" data-title="Edit slab title"
                           data-pk="<?php echo $detailID; ?>" class="slab_title_edit" data-value="<?php echo $val['slabTitle']; ?>">
                            <?php echo $val['slabTitle']; ?>
                        </a>
                    </td>
                    <td style="text-align: right; width:90px"><?php echo $val['startYear'] ?> Y</td>
                    <td style="text-align: right; width:90px"><?php echo $val['endYear'] ?> Y</td>
                    <td style="text-align: left" id="row_<?php echo $i;?>"><?php echo $encode[0];?></td>
                    <td style="text-align: right; width:55px">
                        <?php
                        echo $inputStr;
                        if( $i == $count_slabDetails){?>
                            &nbsp;&nbsp; | &nbsp;&nbsp;
                            <a onclick="delete_item(<?php echo $detailID; ?>,<?php echo $masterID; ?>,<?php echo $val['startYear']; ?>);">
                                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span></a>
                        <?php }?>
                    </td>
                </tr>
                <?php
                $lastEndRange = $val['endYear'] + 0.001;
                $i++;
            }
        } else { ?>
            <tr>
                <td colspan="6" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?><!--No records Found--></td>
            </tr>
            <?php
        }
        ?>
    </table>
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
                        <label for="start_range" class="col-sm-5 control-label">
                            <?php echo $this->lang->line('common_title');?>
                        </label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="slab_title" id="slab_title">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_range" class="col-sm-5 control-label">
                            <?php echo $this->lang->line('hrms_payroll_start_range');?>
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control number number-text" name="start_range" id="start_range" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_range" class="col-sm-5 control-label">
                            <?php echo $this->lang->line('hrms_payroll_end_range');?>
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control number number-text" name="end_range" id="end_range">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="masterID" value="<?php echo $masterID;?>">
                    <button type="button" class="btn btn-primary" onclick="save_gratuity_slabs()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$items = [
    'MA_MD' => false,
    'balancePay' => false,
    'SSO' => false,
    'payGroup' => false,
    'only_salCat_payGroup' => false,
    'isForGratuity' => true,
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var oTable = null;
    var urlSave = '<?php echo site_url('Employee/saveFormula_gratuity/GRATUITY-SLAB') ?>';
    var masterID = '<?php echo $masterID; ?>';
    var lastEndRange = '<?php echo $lastEndRange; ?>';
    var isPaySheetGroup = 0;
    $(".number-text").numeric();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/gratuity-setup-master', masterID, 'HRMS');
        });
    });


    function add_slabDetail() {
        $("#start_range").val(lastEndRange);
        $("#end_range").val('');
        $("#slabDetail_Modal").modal({backdrop: "static"});
    }

    function save_gratuity_slabs() {
        var postData = $('#slab_detail_form').serialize();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/save_gratuity_slabs'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#slabDetail_Modal').modal('hide');
                    loadSlabDetail();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function delete_item(id, masterID) {
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
                    url: "<?php echo site_url('Employee/delete_gratuity_slab_detail'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id, 'masterID':masterID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            loadSlabDetail();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
    }

    $('.slab_title_edit').editable({
        url: '<?php echo site_url('Employee/ajax_update_gratuity_slab_title') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function loadSlabDetail() {
        setTimeout(function(){
            fetchPage('system/hrm/ajax/gratuity-setup-slab', masterID, 'HRMS', '');
        },400);
    }
</script>


<?php
