<?php echo head_page('Add-on Category Master', false);
$this->load->helper('buyback_helper');
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="addonCategory_Add_Model()"><i
                class="fa fa-plus"></i> New Add-on Category
        </button>
    </div>
</div>
<hr style="margin-top: 5px;margin-bottom: 5px;">
<div id="">
    <div class="table-responsive">
        <table id="addonCategory_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 12%">Description</th>
                <th style="min-width: 12%">GL Code</th>
                <th style="min-width: 3%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div id="addonCategory-add-model" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 50%">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Add-on Category</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="addonCategory-add-form" class="form-horizontal"'); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">Description</label>

                            <div class="col-sm-6">
                                <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="description" class="form-control">
                                    <span class="input-req-inner"></span>
                                     <input type="hidden" name="category_id" id="edit_category_id" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="selectbasic">GL Code</label>

                            <div class="col-sm-6">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('GLAutoID', buyback_all_gl_codes(), '', 'class="form-control select2" id="GLAutoID"'); ?>
                                <span class="input-req-inner"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            <button id="singlebutton" type="submit" name="singlebutton"
                                    class="btn btn-primary btn-xs pull-right" style="margin-right: 2%;">Submit
                            </button>
                        </div>
                    </div>
                </div>
                </form>
                <div class="modal-footer">
                </div>
            </div>

        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/addon_master', 'Test', 'Add-on Category Master');
        });
        bom_table();
        $('.select2').select2();

        $('#addonCategory-add-form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                GLAutoID: {validators: {notEmpty: {message: 'GL AutoID is required.'}}}
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
                url: "<?php echo site_url('Buyback/save_addon_categoryMaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        bom_table();
                        $('#addonCategory-add-model').modal('hide');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function bom_table() {
        oTable = $('#addonCategory_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_addonCategory_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "category_id"},
                {"mData": "description"},
                {"mData": "GLAutoID"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2], "orderable": false}, {"searchable": false, "targets": [0]}],
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

    function addonCategory_Add_Model() {
        $('#addonCategory-add-form')[0].reset();
        $('#addonCategory-add-form').bootstrapValidator('resetForm', true);
        $("#addonCategory-add-model").modal({backdrop: "static"});
    }

    function edit_addonCategoryMaster(id) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'category_id': id},
                    url: "<?php echo site_url('Buyback/fetch_addonCategory_detail'); ?>",
                    beforeSend: function () {
                        $("#addonCategory-add-model").modal('show');
                        startLoad();
                    },
                    success: function (data) {
                        $('#edit_category_id').val(data['category_id']);
                        $('#description').val(data['description']);
                        $('#GLAutoID').val(data['GLAutoID']).change();
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }


</script>