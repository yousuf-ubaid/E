<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->lang->line('communityngo_customerConfig'), false);
$customerCategory = party_category(1);
$gl_code_arr = supplier_gl_drop();
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_cusConfig()"><i
                    class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add'); ?><!-- Add -->
            </button>
        </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table id="cus_configTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 25%">Receivable Account</th>
                <th style="min-width: 25%">Category</th>
                <th style="min-width: 5%"></th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


    <div class="modal fade" id="config-modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title configmodal-title"
                        id="myModalLabel"><?php echo $this->lang->line('communityngo_customerConfig'); ?></h4>
                </div>
                <?php echo form_open('', 'role="form" class="form-horizontal" id="config_form"'); ?>
                <input type="hidden" name="ConfigAutoID" id="ConfigAutoID"/>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class=" control-label"
                                   for="receivableAutoID"> <?php echo $this->lang->line('communityngo_ReceivableAccount'); ?><?php required_mark(); ?></label>
                            <?php echo form_dropdown('receivableAutoID', $gl_code_arr, '', 'class="form-control select2" id="receivableAutoID" required'); ?>
                        </div>

                        <div class="col-sm-12">
                            <label class=" control-label"
                                   for="partyCategoryID"> <?php echo $this->lang->line('communityngo_Category'); ?><?php required_mark(); ?></label>
                            <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID" required'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var config_form = $('#config_form');
        var oTable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_customer_acc_setting', '', 'Customer Config');
            });

            $('.select2').select2();

            config_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    receivableAutoID: {validators: {notEmpty: {message: 'Receivable Account is required.'}}},
                    partyCategoryID: {validators: {notEmpty: {message: 'Category is required.'}}},
                },
            }).on('success.form.bv', function (e) {
                $('.submitBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var postData = $form.serialize();
                $.ajax({
                    type: 'post',
                    url: "<?php echo site_url('CommunityNgo/save_customer_config') ?>",
                    data: postData,
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            myAlert('e', data['message']);
                        }
                        else if (data['error'] == 0) {
                            oTable.draw();
                            myAlert('s', data['message']);
                        }
                        $('#config-modal').modal('hide');
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                    }
                });
            });
            cus_configTB();
        });

        function cus_configTB() {
            oTable = $('#cus_configTB').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_customer_config'); ?>",
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
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "ConfigAutoID"},
                    {"mData": "receivableAccount"},
                    {"mData": "categoryDescription"},
                    {"mData": "edit"}
                ],
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

        function new_cusConfig() {
            $('.configmodal-title').text('Customer Config');

            $("#ConfigAutoID").val('');
            $('#receivableAutoID').val('').change();
            $('#partyCategoryID').val('').change();
            config_form[0].reset();
            config_form.bootstrapValidator('resetForm', true);

            $('#config-modal').modal({backdrop: "static"});
        }

        function edit_customer_config(ConfigAutoID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ConfigAutoID': ConfigAutoID},
                url: "<?php echo site_url('CommunityNgo/edit_customer_config'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        $('#config-modal').modal({backdrop: "static"});
                        $('.configmodal-title').text('Edit | Customer Config');
                        $('#ConfigAutoID').val(ConfigAutoID);
                        $('#receivableAutoID').val(data['receivableAutoID']).change();
                        $('#partyCategoryID').val(data['partyCategoryID']).change();

                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }


        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });
    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 2/28/2018
 * Time: 12:31 PM
 */