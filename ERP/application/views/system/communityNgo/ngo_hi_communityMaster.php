<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_members');
echo head_page($title, true);
$this->load->helper('community_ngo_helper');

$date_format_policy = date_format_policy();
$member_arr = all_member_drop_for_community();
$division_arr = load_division_for_member();
$area_arr = load_region_fo_members();
$gender_arr = load_gender();
$status_arr = array('1' => 'Active', '0' => 'Inactive');

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

?>

    <style>
        fieldset {
            border: 1px solid silver;
            border-radius: 5px;
            padding: 1%;
            padding-bottom: 15px;
            margin: 10px 15px;
        }

        legend {
            width: auto;
            border-bottom: none;
            margin: 0px 10px;
            font-size: 20px;
            font-weight: 500
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel">
        <form id="filterForm">
            <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>"/>
            <div class="row">
                <fieldset>
                    <legend><?php echo $this->lang->line('emp_employee_columns'); ?><!--Columns--></legend>

                    <div class="form-group col-sm-12">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label for="GS_Division"><?php echo $this->lang->line('communityngo_GS_Division'); ?>
                                    <!--GS_division--></label><br>
                                <?php echo form_dropdown('GS_Division[]', $division_arr, '', 'class="form-control" id="GS_Division" onchange="fetch_all_member_details(\'GS_Division\')" multiple="multiple"'); ?>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="RegionID"><?php echo $this->lang->line('communityngo_region'); ?>
                                    <!--Area--></label><br>
                                <?php echo form_dropdown('RegionID[]', $area_arr, '', 'class="form-control" id="RegionID" onchange="fetch_all_member_details(\'RegionID\')" multiple="multiple"'); ?>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="GenderID"><?php echo $this->lang->line('communityngo_gender'); ?>
                                    <!--Gender--></label><br>
                                <?php echo form_dropdown('GenderID[]', $gender_arr, '', 'class="form-control" id="GenderID" onchange="fetch_all_member_details(\'GenderID\')" multiple="multiple"'); ?>
                            </div>

                            <div class="form-group col-sm-3 col-xs-6" id="memberdrp">
                                <label
                                    for="Com_MasterID"><?php echo $this->lang->line('communityngo_member_name_with_int'); ?>
                                    <!--Name--></label><br>
                                <?php echo form_dropdown('Com_MasterID[]', $member_arr, '', 'class="form-control" id="Com_MasterID" onchange="fetch_all_member_details(\'Com_MasterID\')" multiple="multiple"'); ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label
                                    for="isActive"><?php echo $this->lang->line('communityngo_com_member_header_Status'); ?>
                                    <!--Status--></label><br>
                                <?php echo form_dropdown('isActive[]', $status_arr, '', 'class="form-control" id="isActive" onchange="fetch_all_member_details(\'isActive\')" multiple="multiple"'); ?>
                            </div>

                            <div class="form-group col-sm-3 col-xs-6"></div>
                            <div class="form-group col-sm-3 col-xs-6"></div>

                            <div class="form-group col-sm-3 col-xs-6">
                                <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()"
                                        style="margin-top: 7%;">
                                    <i class="fa fa-paint-brush"></i>
                                    <?php echo $this->lang->line('common_clear'); ?><!--Clear-->
                                </button>
                            </div>
                        </div>

                    </div>

                </fieldset>
            </div>

        </form>
    </div>


    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td style="background-color: white">
                        <span class="glyphicon glyphicon-stop"
                              style="color:#8bc34a; font-size:15px;"></span> <?php echo $this->lang->line('communityngo_Active'); ?>
                    </td><!--Active-->
                    <td style="background-color: white">
                        <span class="glyphicon glyphicon-stop"
                              style="color:rgba(255, 72, 49, 0.96); font-size:15px;"></span> <?php echo $this->lang->line('communityngo_Inactive'); ?>
                    </td><!--Inactive-->
                </tr>
            </table>
        </div>
        <div class="col-md-4 text-center">
            &nbsp;
        </div>
        <div class="col-md-3 pull-right">
            <a href="#" type="button" class="btn btn-success btn-sm pull-right" onclick="excel_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>

            <?php
            $company_id = current_companyID();
            $page = $this->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID = 530 AND companyID={$company_id}
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');
            ?>


            <button type="button" class="btn btn-primary pull-right" style="margin-right: 2px;"
                    onclick="fetchPage('<?php echo $page; ?>',null,'<?php echo $this->lang->line('communityngo_add_new_member'); ?>','CRM');">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('communityngo_add_new'); ?>
            </button>
        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table id="memberTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 10px">#</th>
                <th style="width: 30px"></th>
                <th style="width: 120px;"><?php echo $this->lang->line('communityngo_MemberCode'); ?></th>
                <th style="width: 220px;">
                    <?php echo $this->lang->line('communityngo_member_name_with_int'); ?><!--Employee Name--></th>
                <th style="width: 70px"><?php echo $this->lang->line('communityngo_nic'); ?><!--NIC--></th>
                <th style="width: 70px"><?php echo $this->lang->line('communityngo_gender'); ?><!--Gender--></th>
                <th style="width: 85px"><?php echo $this->lang->line('communityngo_TP_Mobile'); ?><!--Mobile--></th>
                <th style="width: 150px"><?php echo $this->lang->line('communityngo_region'); ?><!--Region--></th>
                <th style="width: 150px">
                    <?php echo $this->lang->line('communityngo_GS_Division'); ?><!--GS Division--></th>
                <th style="width: 75px">
                    <?php echo $this->lang->line('communityngo_com_member_header_Status'); ?><!--Status--></th>
                <th style="width: 150px"></th>
            </tr>
            </thead>
        </table>
    </div>

    <script type="text/javascript">


        $(document).ready(function () {

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_communityMaster', '', '<?php $this->lang->line('communityngo_members'); ?>');
            });


            $('#GS_Division').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            $('#RegionID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            $('#GenderID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            $('#isActive').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            $('#Com_MasterID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            $('.select2').select2();

            fetch_all_member_details();
        });

        function fetch_all_member_details(name) {

            var Com_MasterID = $('#Com_MasterID').val();
            var GenderID = $('#GenderID').val();
            var RegionID = $('#RegionID').val();
            var GS_Division = $('#GS_Division').val();
            var isActive = $('#isActive').val();

            $('#memberTB').DataTable({

                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_all_member_details'); ?>",
                "aaSorting": [[2, 'desc']],
                "aoColumnDefs": [{"bSortable": false, "aTargets": [1, 7]}],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if (parseInt(oSettings.aoData[x]._aData['Com_MasterID']) == selectedRowID) {
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "Com_MasterID"},
                    {"mData": "image"},
                    {"mData": "MemberCode"},
                    {"mData": "CName_with_initials"},
                    {"mData": "CNIC_No"},
                    {"mData": "Gender"},
                    {"mData": "PrimaryNumber"},
                    {"mData": "Region"},
                    {"mData": "GS_Division"},
                    {"mData": "status"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "GenderID", "value": GenderID});
                    aoData.push({"name": "GS_Division", "value": GS_Division});
                    aoData.push({"name": "RegionID", "value": RegionID});
                    aoData.push({"name": "Com_MasterID", "value": Com_MasterID});
                    aoData.push({"name": "isActive", "value": isActive});
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

        function loadEmployees() {
            var Com_MasterID = $('#Com_MasterID').val();
            var GenderID = $('#GenderID').val();
            var RegionID = $('#RegionID').val();
            var GS_Division = $('#GS_Division').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'Com_MasterID': Com_MasterID,
                    'GenderID': GenderID,
                    'RegionID': RegionID,
                    'GS_Division': RegionID
                },
                url: '<?php echo site_url("CommunityNgo/loadEmployees"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#memberdrp').html(data);

                    $('#Com_MasterID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });

                    $('#GS_Division').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });

                    $('#GenderID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });

                    $('#RegionID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });


                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }


        function clear_all_filters() {
            $('#isActive').val("");
            $('#Com_MasterID').multiselect2('deselectAll', false);
            $('#Com_MasterID').multiselect2('updateButtonText');
            $('#GS_Division').multiselect2('deselectAll', false);
            $('#GS_Division').multiselect2('updateButtonText');
            $('#RegionID').multiselect2('deselectAll', false);
            $('#RegionID').multiselect2('updateButtonText');
            $('#GenderID').multiselect2('deselectAll', false);
            $('#GenderID').multiselect2('updateButtonText');

            fetch_all_member_details();
        }

        function callOTable(name) {
            fetch_all_member_details(name);
        }


        function excel_Export() {
            var form = document.getElementById('filterForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#filterForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/export_excel'); ?>';
            form.submit();
        }

        function delete_communityMembers(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'Com_MasterID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_community_members'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            loadEmployees();
                            fetch_all_member_details();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }
                            else if (data['error'] == 0) {
                                myAlert('s', data['message']);
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-30
 * Time: 4:12 PM
 */