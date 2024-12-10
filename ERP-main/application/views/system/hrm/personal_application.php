<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Personal Action';

echo head_page($title, true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date'); ?><!--Date--></label><br>
            <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                   class="input-small">
            <label for="supplierPrimaryCode">&nbsp;<?php echo $this->lang->line('common_to'); ?>&nbsp;<!--To-->&nbsp;&nbsp;</label>
            <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                   size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                   class="input-small">
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status'); ?><!--Status--></label><br>

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => 'All', '1' => 'Draft', '2' => 'Confirmed', '3' => 'Approved'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i>
                <?php echo $this->lang->line('common_clear'); ?><!-- Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved--></td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_confirmed'); ?><!--Not Confirmed -->/
                    <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span>
                    <?php echo $this->lang->line('common_refer_back'); ?><!--Refer-back--></td>
                <!--<td><span class="label label-info">&nbsp;</span> Closed </td>-->
            </tr>
        </table>
    </div>
    <div class="col-md-2 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">

        <!--Add persional action-->
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/hrm/personal_application_new', null, 'Add Personal Action', 'PAA');">
            <i class="fa fa-plus"></i>
                Add Personal Action
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="personal_action_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 4%">#</th>
            <th style="min-width: 20%">Document Code</th>
            <th style="min-width: 15%">Document Date</th>
            <th style="min-width: 15%">Employee  Name</th>
            <th style="min-width: 15%">Action Type</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 15%">Action</th>
        </tr>
        </thead>
    </table>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="approvel_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('profile_approval'); ?><!--Approval--></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="approval_table" class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('profile_approval_level'); ?><!--Approval Level--></th>
                            <th><?php echo $this->lang->line('common_confirmed_by'); ?><!--Document Confirmed By--></th>
                            <th><?php echo $this->lang->line('common_company_id'); ?><!--Company ID--></th>
                            <th><?php echo $this->lang->line('common_date'); ?><!--Document Date--></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>




<!-- approval user model -->
<div class="modal fade" id="paa_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label"><?php echo $this->lang->line('profile_approval_users'); ?><!--Approval user--></h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('profile_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code">...</dd>
                    <dt><?php echo $this->lang->line('common_date'); ?><!--Document Date--></dt>
                    <dd id="c_document_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_date'); ?><!--Confirmed Date--></dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('common_approved_date'); ?><!--Approved Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th>Comments<!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_confirm'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/personal_application', 'Test', 'Personal Action');
        });
        personal_action_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });


    function personal_action_table(selectedID=null) {
        Otable = $('#personal_action_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_personal_action_table'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['expenseClaimMasterAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "documentCode"},
                {"mData": "documentDate"},
                {"mData": "Ename2"},
                {"mData": "actionType"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "documentDate"}
            ],
            "columnDefs": [
                {"targets": [0], "orderable": false}, 
                //{"visible": false,"searchable": true,"targets": [ 6, 7, 8]}, 
                {"targets": [8], "visible": false,"searchable": false}, 
                {"searchable": false, "targets": [0]}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                //aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id},
                    url: "<?php echo site_url('Employee/delete_personal_action'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }






    function procu(id) {
        $("#approvel_model").modal("show");
        approvalview(id);
    }

    function approvalview(id) {
        var Otable = $('#approval_table').DataTable({
            "Processing": true,
            "ServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Procurement/load_approvel'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'asc']],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [
                {"mData": "approvalLevelID"},
                {"mData": "empname"},
                {"mData": "companyID"},
                {"mData": "documentDate"}
            ],
            "columnDefs": [{
                "targets": [],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "porderid", "value": id});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    //ok
    function referback_PAA(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back'); ?>", /*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*Cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id},
                    url: "<?php echo site_url('Employee/referback_persional_action'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }


    function fetch_approval_user_modal_pa(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Employee/fetch_approval_user_modal_pa'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Approval user');
                $('#ap_user_body').html('');
                x = 1;

                if (jQuery.isEmptyObject(data)) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="3" class="text-center"><b>No Records Found</b></td></tr>');
                } else {

                    $.each(data['approved'], function(i, item) {
                        comment = ' - ';
                        approvedDate = ' - ';
                        if (item['approvedComments']) {
                            comment = item['approvedComments'];
                        }
                        if (item['approvedDate']) {
                            approvedDate = item['approvedDate'];
                        }
                        bePlanVar = (item['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>'  +  item['Ename2'] + '</td><td class="text-center"> Level '  + item['approvalLevelID'] + '</td><td class="text-center">  ' + approvedDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                     x++;
                    });
                    

                }

                $("#paa_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code").html(data['documentCode']);
                $("#c_document_date").html(data['documentDate']);
                $("#c_confirmed_date").html(data['confirmedDate']);
                $("#c_conformed_by").html(data['confirmedByName']);
                stopLoad();
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                // swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

   
    // function edit_model(documentID, id){
    //     var iD = id;
    //     fetchPage("system/hrm/personal_application_new", iD ,"Edit Persional Action","PAA");
    // }
</script>
