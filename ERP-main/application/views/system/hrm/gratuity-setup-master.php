<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_gratuity_master');
echo head_page($title, false);

$expenseGL = expenseGL_drop(1);
$provisionGL = liabilityGL_drop(1);
$addTitle = $this->lang->line('common_add').' - '.$title;
$updateTitle = $this->lang->line('common_update').' - '.$title;

?>
<style type="text/css">

</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="add_items()"><i
                class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="gratuity_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 10px">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_expense_gl_code');?></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_provision_gl_code');?></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_payroll_formula');?></th>
            <th style="width: 68px"></th>
        </tr>
        </thead>
    </table>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<div class="modal fade" id="gratuity_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="" role="document">
        <div class="modal-content">
            <form class="" id="gratuity-master-form" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-title-gratuity"></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="masterID" id="masterID" value="">
                    <div class="row" style="margin-left: 50px">
                        <div class="form-group col-sm-5">
                            <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <input type="text" class="form-control" id="gratuity_description" name="gratuity_description" />
                        </div>
                    </div>
                    <div class="row" style="margin-left: 50px">
                        <div class="form-group col-sm-5">
                            <label><?php echo $this->lang->line('hrms_payroll_expense_gl_code');?></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <?php echo form_dropdown('expenseGL', $expenseGL,'','class="form-control select2" id="expenseGL" '); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 50px">
                        <div class="form-group col-sm-5">
                            <label><?php echo $this->lang->line('hrms_payroll_provision_gl_code');?></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <?php echo form_dropdown('provisionGL', $provisionGL,'','class="form-control select2" id="provisionGL" '); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save-btn-gratuity"><?php echo $this->lang->line('common_save');?><!--Save--></button>
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
    'only_salCat_payGroup' => false
];
$data['items'] = $items;
$this->load->view('system/hrm/formula-modal-view', $data);

?>

<script type="text/javascript">
    var pgId = '<?php echo $this->input->post('page_id'); ?>';
    var oTable = null;
    var urlSave = '<?php echo site_url('Employee/saveFormula_gratuity/GRATUITY') ?>';
    var isPaySheetGroup = 0;
    var addTitle = '<?php echo $addTitle; ?>';
    var updateTitle = '<?php echo $updateTitle; ?>';
    $(".number-text").numeric();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/gratuity-setup-master', '', 'HRMS');
        });

        load_gratuity_table(pgId);

        $('.select2').select2();
    });

    function load_gratuity_table(selectedRowID=null) {
        oTable = $('#gratuity_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_gratuity'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if (parseInt(oSettings.aoData[x]._aData['gratuityID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "gratuityID"},
                {"mData": "gratuityDescription"},
                {"mData": "exGL"},
                {"mData": "prGL"},
                {"mData": "formula"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function add_items() {
        $('#save-btn-gratuity').attr('onclick', 'create_gratuity()');
        $('#modal-title-gratuity').text(addTitle);
        $('#masterID,#gratuity_description').val('');
        $('#expenseGL,#provisionGL').val('').trigger('change');
        $('#gratuity_modal').modal({backdrop: "static"});
    }

    function create_gratuity() {
        var postData = $('#gratuity-master-form').serialize();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/create_gratuity'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#gratuity_modal').modal('hide');
                    load_gratuity_table();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function edit_gratuity_master(id, des, exGL, prGL) {
        $('#save-btn-gratuity').attr('onclick', 'update_gratuity()');
        $('#modal-title-gratuity').text(updateTitle);
        $('#masterID').val(id);
        $('#gratuity_description').val(des);
        $('#expenseGL').val(exGL).trigger('change');
        $('#provisionGL').val(prGL).trigger('change');
        $('#gratuity_modal').modal({backdrop: "static"});
    }

    function update_gratuity() {
        var postData = $('#gratuity-master-form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/update_gratuity_master'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    $('#gratuity_modal').modal('hide');
                    load_gratuity_table($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })

    }

    function delete_gratuity_master(id) {
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
                    url: "<?php echo site_url('Employee/delete_gratuity_master'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_gratuity_table()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
    }

    function load_gratuity_details(id){
        fetchPage('system/hrm/ajax/gratuity-setup-slab', id, 'HRMS');
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<?php
