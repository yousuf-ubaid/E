<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
global $typeDropdown;

$typeDropdown = group_structure_type();
$title = $this->lang->line('finance_group_structure');
echo head_page($title, false);

?>

<style>

    #cTable tbody tr.highlight td {
        background-color: #FFEE58;
    }

    /**
     * Framework starts from here ...
     * ------------------------------
     */
    .tree,
    .tree ul {
        margin: 0 0 0 1em; /* indentation */
        padding: 0;
        list-style: none;
        color: #cbd6cc;
        position: relative;
    }

    .tree ul {
        margin-left: .5em
    }

    /* (indentation/2) */

    .tree:before,
    .tree ul:before {
        content: "";
        display: block;
        width: 0;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        border-left: 1px solid;
    }

    .tree li {
        margin: 0;
        padding: 0 1.5em; /* indentation + .5em */
        line-height: 2em; /* default list item's `line-height` */
        font-weight: bold;
        position: relative;
        font-size: 11px
    }

    .tree li:before {
        content: "";
        display: block;
        width: 10px; /* same with indentation */
        height: 0;
        border-top: 1px solid;
        margin-top: -1px; /* border top width */
        position: absolute;
        top: 1em; /* (line-height/2) */
        left: 0;
    }

    .tree li:last-child:before {
        background: white; /* same with body background */
        height: auto;
        top: 1em; /* (line-height/2) */
        bottom: 0;
    }

    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 11px;
        background-color: #fbfbfb;
    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 11px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;
        /* color:#555;*/
    }


</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <button onclick="createGroup()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('finance_create_a_group')?><!--Create a Group-->
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <div id="loadstructurePage">

            <table id="cTable" class="table " style="width: 100%">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('common_company')?></th>
                    <th style="text-align: left"><?php echo $this->lang->line('common_type')?></th>

                    <th>
                        <div class="pull-right">
                            <button onclick="pullcompanies()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('finance_add_companies')?>
                        </div>
                    </th>

                </tr>
                </thead>
                <tbody>
                <?php $group = $this->db->query("SELECT companyGroupID,description,groupCode,reportingTo,masterID FROM srp_erp_companygroupmaster ORDER BY reportingTo ASC")->result_array();
                $companies = $this->db->query("SELECT
	companyGroupID,company_code,company_name,srp_erp_companygroupdetails.companyID,typeID,companyGroupDetailID,description
FROM
	srp_erp_companygroupdetails
	LEFT JOIN srp_erp_groupstructuretype ON typeID=groupStructureTypeID
	INNER JOIN srp_erp_company ON srp_erp_companygroupdetails.companyID = srp_erp_company.company_id")->result_array();
                function buildTree(array $elements, $parentId = 0, $i = 0, array $companies, $typeDropdown)
                {
                    $branch = array();
                    if ($parentId != 0) {
                        $i += 50;

                    }

                    foreach ($elements as $element) {
                        if ($element['reportingTo'] == $parentId) {
                            ?>
                            <tr class="subheader ">
                                <td style="padding-left: <?php echo $i ?>px"><i class="fa fa-minus"
                                                                                aria-hidden="true"></i> <?php echo $element['groupCode'] ?>
                                    | <?php echo $element['description'] ?></td>
                                <td></td>
                                <td style="text-align: right">&nbsp; <span style="cursor: pointer"
                                                                           onclick="editGroup(<?php echo $element['companyGroupID'] ?>,'<?php echo $element["groupCode"] ?>','<?php echo $element["description"] ?>',<?php echo $element['masterID']?>)"><i
                                                style="color: #0d6aad" class="fa fa-edit"></i></span</td>
                            </tr>


                            <?php

                            $keys = array_keys(array_column($companies, 'companyGroupID'), $element['companyGroupID']);
                            $company = array_map(function ($k) use ($companies) {
                                return $companies[$k];
                            }, $keys);

                            if (!empty($company)) {
                                foreach ($company as $c) {
                                    ?>

                                    <tr class="">


                                        <td style="padding-left: 200px"><?php echo $c['company_code'] ?>
                                            | <?php echo $c['company_name'] ?></td>
                                        <td style="width: 100px">  <?php echo form_dropdown('typeID', $typeDropdown, $c['typeID'], 'class=" select2" onchange="updatefield(this.value,' . $c['companyGroupDetailID'] . ')" id="companyID" required"'); ?>  </td>
                                        <td style="width: 100px;text-align: right"><span style="cursor: pointer"
                                                                                         onclick="modalOpen(<?php echo $c['companyID'] ?>)"><i
                                                        style="color: #0d6aad" class="fa fa-plus"></i></span> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                                            <span style="cursor: pointer"
                                                  onclick="modalview(<?php echo $c['companyID'] ?>)"><i
                                                        style="color: #0d6aad" class="fa fa-eye"></i></span></td>


                                    </tr>


                                    <?php
                                }
                            }
                            ?>
                            <?php

                            $children = buildTree($elements, $element['companyGroupID'], $i, $companies, $typeDropdown);
                            if ($children) {
                                $element['children'] = $children;

                                ?>

                                <?php
                            }
                            ?>

                            <?php

                            $branch[] = $element;
                        }
                    }

                    return $branch;
                }

                $i = 0;

                $tree = buildTree($group, 0, $i, $companies, $typeDropdown);


                ?>


                </tbody>
            </table>


        </div>
    </div>
</div>
</div>

<div class="modal fade" id="groupStructure" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"></h3>
            </div>
            <form role="form" id="groupStructureForm" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('config_share_holder_name')?><!--Share Holder Name--> </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="shareholderName" name="shareholderName">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('config_share_holding')?><!--Share Holding--> %</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" id="percentage" name="percentage">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date_from')?><!--Date From--> </label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateFrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="dateFrom"
                                       class="dateFrom form-control" value="">
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_active')?><!--Active--> </label>
                        <div class="col-sm-6">

                            <input type="checkbox" value="1" class="" id="isActive" name="isActive">


                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" onclick="save_update()" class="btn btn-primary btn-sm saveBtn submitBtn"
                            data-value="0"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="groupStructureView" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"></h3>
            </div>

            <div class="modal-body" id="htmlView">


            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="pullCompanies" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"></h3>
            </div>
            <form role="form" id="pullCompaniesForm" class="form-horizontal">
                <div class="modal-body" id="loadCompanyform">


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" onclick="savepullcompanies()" class="btn btn-primary btn-sm saveBtn submitBtn"
                            data-value="0"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="createGroup" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('finance_create_group'); ?><!--Create Group--></h3>
            </div>
            <form role="form" id="createGroupForm" class="form-horizontal">
                <div class="modal-body" id="loadcreateGroup">


                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_group_code'); ?><!--Group Code--> </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="groupCode" name="groupCode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_group_description'); ?><!--Group Description--> </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="xxGroupdescription" name="description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_parent'); ?><!--Parent--> </label>
                        <div class="col-sm-6" id="masterTo">

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('finance_group_to'); ?><!--Group To--> </label>
                        <div class="col-sm-6" id="reportingTo">

                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" onclick="submitGroup()" class="btn btn-primary btn-sm saveBtn submitBtn"
                            data-value="0"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    var company_ID;
    var companyGroup_ID;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.dateFrom').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
    });
    $('#cTable').on('click', 'tr', function (e) {
        $('#cTable').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#cTable tr').each(function () {
            $(this).removeClass('highlight');
        });
        if (searchtext !== '') {
            $('#cTable tr').each(function () {
                if ($(this).find('td').text().toLowerCase().indexOf(searchtext.toLowerCase()) == -1) {

                    $(this).removeClass('highlight');
                }
                else {
                    $(this).addClass('highlight');
                }
            });
        }
    }

    function save_update() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#groupStructureForm").serialize() + "&companyID=" + company_ID,
            url: '<?php echo site_url('Group_structure/submit_groupStructure'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#groupStructure').modal('hide');
                    $("#groupStructureForm")[0].reset();
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function modalOpen(companyID) {
        company_ID = companyID;
        $('#groupStructure').modal('show');
    }


    function modalview(companyID) {
        $('#groupStructureView').modal('show');
        company_ID = companyID;
        loadPageView();
    }


    function loadPageView() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: company_ID},
            url: '<?php echo site_url('Group_structure/get_groupStructure'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#htmlView').html(data);


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function updatefield(thesValue, companyGroupDetailID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {companyGroupDetailID: companyGroupDetailID, value: thesValue},
            url: '<?php echo site_url('Group_structure/groupStructureDetail_update_field'); ?>',
            beforeSend: function () {

            },
            success: function (data) {


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function update_field(thes, groupstructureID) {
        if ($(thes).prop('checked') == true) {
            value = 1
        }
        else {
            value = 0;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {groupstructureID: groupstructureID, value: value},
            url: '<?php echo site_url('Group_structure/groupStructure_update_field'); ?>',
            beforeSend: function () {

            },
            success: function (data) {


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function deleteshareholding(groupstructureID) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_common_you_want_to_delete_this_record');?>", /*You want to delete this record!*/
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
                    data: {'groupstructureID': groupstructureID},
                    url: "<?php echo site_url('Group_structure/deleteshareholding'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        myAlert(data[0][1]);
                        loadPageView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function pullcompanies() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',

            url: '<?php echo site_url('Group_structure/loadCompanyForm'); ?>',
            beforeSend: function () {

            },
            success: function (data) {


                $('#pullCompanies').modal('show');
                $('#loadCompanyform').html(data);

                $("#xcompanyID").multiselect2({
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    maxHeight: 200,
                    numberDisplayed: 2,
                    buttonWidth: '180px'
                });


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });


    }

    function savepullcompanies() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#pullCompaniesForm").serialize(),
            url: '<?php echo site_url('Group_structure/submit_groupStructurepullcompany'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#pullCompanies').modal('hide');
                    $("#pullCompaniesForm")[0].reset();

                    loadstructurePage()

                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function loadstructurePage() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',

            url: '<?php echo site_url('Group_structure/loadstructurePage'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#loadstructurePage').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function createGroup() {
        companyGroup_ID = null;
        $('#createGroup').modal('show');


        loadparentTo();
        setTimeout(function(){  loadreportingTo($('#masterID').val()); }, 300);

    }

    function loadreportingTo(thisvalue) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {parent: thisvalue},
            url: '<?php echo site_url('Group_structure/GroupStructurereportingTo'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#reportingTo').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function loadparentTo() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',

            url: '<?php echo site_url('Group_structure/GroupStructuremasterTo'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#masterTo').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function submitGroup() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#createGroupForm").serialize() + "&companyGroupID=" + companyGroup_ID,
            url: '<?php echo site_url('Group_structure/submit_create_group'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#createGroup').modal('hide');
                    $("#createGroupForm")[0].reset();
                    companyGroup_ID = null;
                    loadstructurePage();
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function editGroup(companyGroupID, groupCode, description,masterID) {
        companyGroup_ID = companyGroupID;
        $('#groupCode').val(groupCode);
        $('#xxGroupdescription').val(description);
        $('#createGroup').modal('show');
        loadparentTo();

        editReportingTo(masterID);

    }




    function editReportingTo(masterID) {


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'companyGroup_ID': companyGroup_ID,parent:masterID},
            url: '<?php echo site_url('Group_structure/GroupStructurereportingTo'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#reportingTo').html(data);
                $('#masterID').val(masterID);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });


    }

</script>