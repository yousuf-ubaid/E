<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('project_management', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_common_project');
$segment_arr = fetch_segment(true, false);
echo head_page($title, true);
$prType = ['1'=> 'Internal','2'=> 'External'];
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-2">
            <label for="crTypes[]">Customer Type </label><br>
            <?php echo form_dropdown('crTypes[]', $prType, '', 'class="form-control" id="crTypes" multiple="multiple"'); ?>
        </div>

        <div class="form-group col-sm-2">
            <label for="segment[]"><?php echo $this->lang->line('common_segment');?> </label><br>
            <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-2">
            <label >&nbsp;</label><br/>
            <button type="button" class="btn btn-primary pull-right" onclick="clear_all_filters()" style=" ">
                <i class="fa fa-times-circle-o"></i>
            </button>
            <button type="button" class="btn btn-primary pull-right" onclick="Otable.draw();" style="margin-right: 10px;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12 text-center">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/pm/erp_boq_estimation_add_new','','Project')" ><i
                    class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New-->
        </button>
    </div>
</div>

<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs mainpanel">
        <li class="active">
            <a class="buybackTab" onclick="" id="" data-id="0" href="#step1" data-toggle="tab" aria-expanded="true"><span><i class="fa fa-tasks" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>&nbsp;Projects</span></a>
        </li>
        <li class="">
            <a class="buybackTab" onclick="" id="" data-id="0" href="#step2" data-toggle="tab" aria-expanded="true"><span><i class="fa fa-tasks" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>&nbsp;Archived Projects</span></a>
        </li>
    </ul>
</div>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="table-responsive">
            <table id="table_boq" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style=""><?php echo $this->lang->line('promana_common_id');?><!--ID--></th>
                    <th style=""><?php echo $this->lang->line('promana_pm_project_code');?><!--Project Code--></th>
                    <th style="">Project</th>
                    <th style="">Project Name</th>
                    <th style=""><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></th>

                    <th style="width: 55px;"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                    <th style="width: 55px;"><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
                    <th style="width: 55px;"><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
                    <th style=""><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                    <th style="">Segment</th>
                    <th style="width: 30px;"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                    <th style="width: 30px"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                    <th style="width: 86px"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div class="table-responsive">
            <table id="table_boq_archieve" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style=""><?php echo $this->lang->line('promana_common_id');?><!--ID--></th>
                    <th style=""><?php echo $this->lang->line('promana_pm_project_code');?><!--Project Code--></th>
                    <th style="">Project</th>
                    <th style="">Project Name</th>
                    <th style=""><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></th>

                    <th style="width: 55px;"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                    <th style="width: 55px;"><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
                    <th style="width: 55px;"><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
                    <th style=""><?php echo $this->lang->line('common_comment');?><!--Comment--></th>
                    <th style="width: 30px;"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                    <th style="width: 30px"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                    <th style="width: 86px"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">

    var Otable;
    var Otable_archive;

    $('#crTypes, #segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/pm/boq','','Project');
        });
    });

    window.Otable=  $('#table_boq').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Boq/fetch_Boq_headertable'); ?>",
        "aaSorting": [[1, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var
                tmp_i = oSettings._iDisplayStart;
            var
                iLen = oSettings.aiDisplay.length;
            var
                x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }

        },
        "aoColumns": [
            {"mData": "headerID"},
            {"mData": "projectCode"},
            {"mData": "projectName"},
            {"mData": "projectDescription"},
            {"mData": "customerName"},

            {"mData": "createdDateTime"},
            {"mData": "projectDateFrom"},
            {"mData": "projectDateTo"},
            {"mData": "comment"},
            {"mData": "segDes"},
            {"mData": "confirmedYN"},
            {"mData": "approvedYN"},
            {"mData": "action"}
        ],
        "columnDefs": [{"searchable": false, "targets": [0,1,2,3,4,5,6,7,8,9,10,11]}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            
            aoData.push({ "name": "segmentID","value": $('#segmentID').val()});
            aoData.push({ "name": "crTypes","value": $('#crTypes').val()});
            aoData.push({ "name": "archive_status","value": 0});
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        }
    });

    window.Otable_archive=  $('#table_boq_archieve').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Boq/fetch_Boq_headertable'); ?>",
        "aaSorting": [[1, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var
                tmp_i = oSettings._iDisplayStart;
            var
                iLen = oSettings.aiDisplay.length;
            var
                x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }

        },
        "aoColumns": [
            {"mData": "headerID"},
            {"mData": "projectCode"},
            {"mData": "projectName"},
            {"mData": "projectDescription"},
            {"mData": "customerName"},

            {"mData": "createdDateTime"},
            {"mData": "projectDateFrom"},
            {"mData": "projectDateTo"},
            {"mData": "comment"},
            {"mData": "confirmedYN"},
            {"mData": "approvedYN"},
            {"mData": "action"}
        ],
        "columnDefs": [{"searchable": false, "targets": [0,1,2,3,4,5,6,7,8,9,10]}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({ "name": "archive_status","value": 1});
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        }
    });

    function deleteBoqHeader(headerID){
        if (headerID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*Your want to delete this record*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it');?>",/*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'headerID':headerID},
                        url: "<?php echo site_url('Boq/deleteBoqHeader'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> <?php echo $this->lang->line('promana_common_please_wait_untill_page_load');?><!--Please wait until page load!--> </h4>",
                            });
                        },
                        success: function (data) {
                            HoldOn.close();
                           myAlert(data[0],data[1]);
                            Otable.ajax.reload();
                        }, error: function () {
                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        };
    }

    function clone_projet(headerID)
    {
        if (headerID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to Clone this Project",/*Your want to delete this record*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",/*Yes, delete it!*/
                    cancelButtonText: "No"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'headerID':headerID},
                        url: "<?php echo site_url('Boq/clone_project'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> <?php echo $this->lang->line('promana_common_please_wait_untill_page_load');?><!--Please wait until page load!--> </h4>",
                            });
                        },
                        success: function (data) {
                            HoldOn.close();
                            myAlert(data[0],data[1]);
                            Otable.ajax.reload();
                        }, error: function () {
                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        };
    }

    function archive_projet(headerID)
    {
        if (headerID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to Archive This Project",/*Your want to delete this record*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",/*Yes, delete it!*/
                    cancelButtonText: "No"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'headerID':headerID},
                        url: "<?php echo site_url('Boq/archive_projet'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> <?php echo $this->lang->line('promana_common_please_wait_untill_page_load');?><!--Please wait until page load!--> </h4>",
                            });
                        },
                        success: function (data) {
                            HoldOn.close();
                            myAlert(data[0],data[1]);
                            Otable.ajax.reload();
                            Otable_archive.ajax.reload();
                        }, error: function () {
                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        };
    }

    function referback_project(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'headerID':id},
                    url :"<?php echo site_url('Boq/referback_project'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.ajax.reload();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters(){
        $('#crTypes, #segmentID').val([]).multiselect2('refresh');
        Otable.draw();
    }
</script>
