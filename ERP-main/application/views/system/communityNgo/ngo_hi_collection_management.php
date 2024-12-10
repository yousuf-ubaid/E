<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('communityngo_collection_header');
echo head_page($title, false);
$this->load->helper('community_ngo_helper');
$gl_code_arr_income = income_gl_drop();
$gl_code_arr_receivable = receivable_gl_drop();
$gender_arr = load_gender_for_collection();
$collectionType_arr = load_collectionType();
?>

    <style>
        .form-group .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }
    </style>

    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-md-3 text-right">
            <button type="button" onclick="collection_master_model()" class="btn btn-primary pull-right standedbtn"><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
            </button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="collection_entry_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th><?php echo $this->lang->line('communityngo_Description'); ?><!--Details--></th>
                <th><?php echo $this->lang->line('communityngo_collection_types'); ?><!--Details--></th>
                <th><?php echo $this->lang->line('communityngo_gender'); ?></th>
                <th><?php echo $this->lang->line('communityngo_collection_Account'); ?><!--Details--></th>
                <th><?php echo $this->lang->line('communityngo_ReceivableAccount'); ?><!--Details--></th>
                <th style="min-width: 13%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
        </table>
    </div>


    <div aria-hidden="true" role="dialog" id="collection_master_model" class="modal fade" style="display: none;">
        <div class="modal-dialog" style="width: 1100px;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('communityngo_collection_header'); ?></h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="collection_master_form">
                        <input type="hidden" id="CollectionMasterID" name="CollectionMasterID">
                        <table class="table table-bordered responsive" id="collection_master_add_table"
                               style="width: 100%">
                            <thead>
                            <tr>
                                <th style="width: 200px"><?php echo $this->lang->line('communityngo_Description'); ?><?php required_mark(); ?></th>
                                <th style="width: 180px"><?php echo $this->lang->line('communityngo_collection_types'); ?><?php required_mark(); ?></th>
                                <th style="width: 180px"><?php echo $this->lang->line('communityngo_gender'); ?></th>
                                <th><?php echo $this->lang->line('communityngo_collection_Account'); ?><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('communityngo_ReceivableAccount'); ?><?php required_mark(); ?></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                                </th>
                            </tr>

                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div
                                        class="form-group">
                                        <input type="text" name="CollectionDes[]"
                                               placeholder="<?php echo $this->lang->line('communityngo_Description'); ?>"
                                               value="" id="CollectionDes" class="form-control" required>
                                    </div>
                                </td>
                                <td>
                                    <div
                                        class="form-group">
                                        <select id="CollectionTypeID" class="form-control select2"
                                                name="CollectionTypeID[]"
                                                data-placeholder="<?php echo $this->lang->line('communityngo_collection_types'); ?>"
                                                required>
                                            <option value=""></option>
                                            <?php
                                            if (!empty($collectionType_arr)) {
                                                foreach ($collectionType_arr as $val) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $val['TypeID'] ?>"><?php echo $val['Description'] ?></option>
                                                    <?php

                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div
                                        class="form-group"><?php echo form_dropdown('genderID[]', $gender_arr, '', 'class="form-control select2" id="genderID" required'); ?></div>
                                </td>

                                <td>
                                    <div
                                        class="form-group"><?php echo form_dropdown('revenueGLAutoID[]', $gl_code_arr_income, '', 'class="form-control select2" id="revenueGLAutoID" required'); ?></div>
                                </td>
                                <td>
                                    <div
                                        class="form-group"><?php echo form_dropdown('receivableAutoID[]', $gl_code_arr_receivable, '', 'class="form-control select2" id="receivableAutoID" required'); ?></div>
                                </td>
                                <td class="remove-td" style="vertical-align: middle; text-align: center;"></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="save_collection_setup()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save Changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <script type="text/javascript">
        var CollectionMasterID;
        var CollectionType;
        var Otable;

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_collection_management', '', 'Collection Setup');
            });
            $('.select2').select2();

            load_collection_entries();
        });

        function get_collectionType(TypeID) {
            CollectionType = TypeID;
        }

        function load_collection_entries() {
            Otable = $('#collection_entry_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_collection_entry'); ?>",
                "aaSorting": [[1, 'desc']],
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
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');
                },
                "aoColumns": [
                    {"mData": "CollectionMasterID"},
                    {"mData": "CollectionDes"},
                    {"mData": "Description"},
                    {"mData": "genName"},
                    {"mData": "revenueAccount"},
                    {"mData": "receivableAccount"},
                    {"mData": "action"}
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

        function delete_collection_entry(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'CollectionMasterID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_collection_entry'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            Otable.draw();
                            myAlert(data[0], data[1]);
                            stopLoad();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function collection_master_model() {

            $('#CollectionMasterID').val('');
            $('#CollectionDes').val('');
            $('#CollectionTypeID').val('').change();
            $('#genderID').val('').change();
            $('#revenueGLAutoID').val('').change();
            $('#receivableAutoID').val('').change();
            $('#collection_master_form')[0].reset();
            $("#collection_master_model").modal({backdrop: "static"});
            $('#collection_master_add_table tbody tr').not(':first').remove();

        }

        function add_more() {
            $('select.select2').select2('destroy');
            var appendData = $('#collection_master_add_table tbody tr:first').clone();

            appendData.find('input,select').val('');

            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
            $('#collection_master_add_table').append(appendData);
            var lenght = $('#collection_master_add_table tbody tr').length - 1;
            $(".select2").select2();
        }

        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });


        function save_collection_setup() {
            var $form = $('#collection_master_form');
            var data = $form.serializeArray();

            $('select[name="revenueGLAutoID[]"] option:selected').each(function () {
                data.push({'name': 'CashGL_des[]', 'value': $(this).text()})
            });

            $('select[name="receivableAutoID[]"] option:selected').each(function () {
                data.push({'name': 'receivableGL_des[]', 'value': $(this).text()})
            });

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_collection_setup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#collection_master_form')[0].reset();
                        $("#CollectionTypeID").select2("");
                        $("#revenueGLAutoID").select2("");
                        $("#receivableAutoID").select2("");
                        $("#genderID").select2("");

                        $('#collection_master_model').modal('hide');

                        load_collection_entries();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function edit_collection_entry(id, value) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'CollectionMasterID': id},
                        url: "<?php echo site_url('CommunityNgo/edit_collection_entry'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            $('#collection_master_form')[0].reset();
                            $('#CollectionMasterID').val(data['CollectionMasterID']);

                            $('#CollectionDes').val(data['CollectionDes']);
                            $('#CollectionTypeID').val(data['CollectionTypeID']).change();
                            $('#genderID').val(data['genderID']).change();
                            $('#revenueGLAutoID').val(data['revenueGLAutoID']).change();
                            $('#receivableAutoID').val(data['receivableAutoID']).change();

                            $("#collection_master_model").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }

        function fetch_accrual_debit_account(id, value) {
            $('#AccrualDebitAccount').val(id).change();
        }


    </script>


<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 2/20/2018
 * Time: 1:53 PM
 */