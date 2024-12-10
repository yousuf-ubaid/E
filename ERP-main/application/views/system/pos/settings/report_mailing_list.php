<?php
$employees = get_employeeList();

$employees_drop = array();
$employees_js = array();
if (!empty($employees)) {

    $employees_drop[''] = 'Please select';
    foreach ($employees as $s) {
        $employees_drop[trim($s['employeeID'] ?? '')] = $s['name'];

        /*JS get Email and show */
        $employees_js[$s['employeeID']]['id'] = $s['employeeID'];
        $employees_js[$s['employeeID']]['name'] = $s['name'];
        $employees_js[$s['employeeID']]['email'] = $s['email'];
    }
}
$schedulerList = get_schedulerList();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div class="box box-primary">
    <div class="box-body">
        <h4 style="font-size:16px; font-weight: 800;">
            <i class="fa fa-list" aria-hidden="true"></i>
            Mailing List


            <button class="btn  btn-primary pull-right" type="button" id="btn_user" style="margin-bottom: 9px;">
                <i class="fa fa-user-plus" aria-hidden="true"></i> Add User
            </button>
            <span class="pull-right"> &nbsp;</span>
            <button class="hide btn  btn-default pull-right" onclick="toggleFilter()" type="button" id="btn_user" style="margin-bottom: 9px;">
                <i class="fa fa-filter" aria-hidden="true"></i> Filter
            </button>
        </h4>
        <div class="container-wifi-filter" style="display: none;">
            <div class="row">
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <i class="fa fa-filter text-purple" aria-hidden="true"></i>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label>Filter </label>
                </div>
                <div class="col-xs-8 col-sm-6 col-md-5 col-lg-5">
                    <?php echo form_dropdown('listID', $schedulerList, '1', ' type="button" id="listID" class="form-control select2"') ?>
                </div>
            </div>
            <hr>
        </div>

        <table class="<?php echo table_class() ?>" id="tbl_mailing_list" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th>name</th>
                <th>Email</th>
                <th>Mail Process</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    function toggleFilter(){
        $(".container-wifi-filter").toggle('slow');
    }


    var DT_MailingList;
    $(document).ready(function () {
        $('.select2').select2();
        $("#btn_user").click(function () {
            add_mailing_list_modal();
        });

        $("#btn_save_mailing_list").click(function () {
            save_mailing_list();
        });

        DT_MailingList = $('#tbl_mailing_list').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": false,
            "sAjaxSource": "<?php echo site_url('Pos_batchProcess/LoadMailingList'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "name"},
                {"mData": "email"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'listID', 'value': $("#listID").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        $("#listID").change(function () {
            DT_MailingList.ajax.reload();
        })
    });

    function add_mailing_list_modal() {
        $("#add_mailingList").modal('show');
        $("#frm_mailing_list")[0].reset();
        $("#employeeID_from").val('').change();
        $("#email").val('');

    }

    function save_mailing_list() {
        var data = $("#frm_mailing_list").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_batchProcess/save_mailing_list'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['status'], data['message']);
                if (data['status'] == 's') {
                    DT_MailingList.ajax.reload();
                    $("#mailing_list_add_modal").modal('hide');
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in loading currency denominations.')
            }
        });
    }

    function delete_mailing_list(id) {
        swal({
                title: "Are you sure",
                text: "You want to Delete this record?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos_batchProcess/delete_mailing_list'); ?>",
                    data: {id: id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data['status'], data['message']);
                        if (data['status'] == 's') {
                            DT_MailingList.ajax.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', jqXHR + ' : ' + textStatus + ' : ' + errorThrown);
                    }
                });
            });
    }

    function get_email() {
        var id = $("#employeeID_from").val();
        var list = <?php echo json_encode($employees_js) ?>;
        if (list[id] != 'undefined' && id > 0) {
            $("#email").val(list[id].email);
        }
    }


</script>

<div class="modal pddLess" data-backdrop="static" id="add_mailingList" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"> Add Mailing List </h4></div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_mailing_list">
                    <input type="hidden" id="id" name="id">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Employee</label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('employeeID', $employees_drop, '', ' type="button" id="employeeID_from" class="form-control select2" onchange="get_email()"') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Email Address </label>
                            <div class="col-md-6">
                                <input id="email" name="email" type="email"
                                       placeholder="somebody@somewhere.com"
                                       class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Scheduler Report </label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('batchlist_id', $schedulerList, '', ' type="button" id="batchlist_id_from" class="form-control select2"') ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-4">
                                <button type="button" id="btn_save_mailing_list" class="btn btn-primary">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Add
                                </button>
                            </div>
                        </div>

                    </fieldset>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>