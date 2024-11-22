<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_report_template');
echo head_page($title, false);

/*echo head_page('Report Template', false);*/
$type_arr = fetch_report_type();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row table-responsive">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="opentemplatemastermodal()"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('config_create_template');?><!--Create Template-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="company_report_template_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_type');?><!--Type--></th>
            <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="company_report_template_modal" class=" modal fade bs-example-modal-lg"
           style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('config_report_template_master');?><!--Report Template Master--></h5>
            </div>
            <?php echo form_open('', 'role="form" id="company_report_template_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('common_type');?><!--Type--></label>
                        <?php echo form_dropdown('reportID', $type_arr,'', 'class="form-control select2" id="reportID" required'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary " onclick="save_reportTemplateMaster()"><i
                            class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="company_report_template_details_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="company_report_template_details_form"'); ?>
            <input type="hidden" name="companyReportTemplateID" id="companyReportTemplateID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_report_template_detail');?><!--Report Template Detail--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                <input type="text" name="detaildescription" id="detaildescription" class="form-control">
                            </div>
                            <div class="form-group col-sm-6">
                                <label><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                                <input type="number" name="sortOrder" id="sortOrder" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                            <button type="button" class="btn btn-sm btn-primary pull-right" onclick="save_reportTemplateDetail()"><i
                                    class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                            </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                            <?php echo $this->lang->line('common_save');?><!--Save-->
                           
                    </div>
                </div>


                <hr>
               

               
                <div class="row">
                    <div class="table-responsive">
                        <table id="company_report_template_details_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 80%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
                                <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="company_report_template_links_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="company_report_template_links_form"'); ?>
            <input type="hidden" name="detID" id="detID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_report_template_link');?><!--Report Template Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="row">
                            <div class="form-group col-sm-6" id="Gldescription">
                            </div>
                            <div class="form-group col-sm-6">
                                <label><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                                <input type="number" name="sortOrderLink" id="sortOrderLink" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-sm btn-primary pull-right" onclick="save_reportTemplateLink()"><i
                                class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table id="company_report_template_link_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 80%"><?php echo $this->lang->line('config_gl_description');?><!--GL Description--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></th>
                                <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/ReportTemplate/erp_report_template_master', 'Test', 'Report Template');
        });
        company_report_template_table();
    });

    function company_report_template_table() {
       $('#company_report_template_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('ReportTemplate/fetch_company_report_template_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "companyReportTemplateID"},
                {"mData": "description"},
                {"mData": "reportDescription"},
                {"mData": "edit"}
            ],
           "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'templateType', 'value':1});
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

    function opentemplatemastermodal() {
        //$('#companyReportTemplateID').val('');
        $('#company_report_template_form')[0].reset();
        $('#company_report_template_modal').modal('show');
    }


    function company_report_template_details_modal(companyReportTemplateID) {
        $('#companyReportTemplateID').val(companyReportTemplateID);
        $('#company_report_template_details_form')[0].reset();
        $('#company_report_template_details_modal').modal('show');
        company_report_template_details_table();
    }

    function delete_report_template_master(companyReportTemplateID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'companyReportTemplateID': companyReportTemplateID},
                    url: "<?php echo site_url('ReportTemplate/delete_report_template_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            company_report_template_table();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    }
                });
            });
    }

    function save_reportTemplateMaster(){
        var data = $("#company_report_template_form").serializeArray();
        data.push({'name':'templateType', 'value':1});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/save_reportTemplateMaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    company_report_template_table();
                    $('#company_report_template_modal').modal('hide');
                    $('#description').val('');
                    $('#reportID').val('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function setupTemplate(id, des){
        
        var master_page = 'system/ReportTemplate/erp_report_template_master.php';
        fetchPage('system/ReportTemplate/setup-template', id, 'Report Template Setup', '', des, master_page);
    }


    function get_configData(detID){
        $('#detID').val(detID);
        $('#company_report_template_links_form')[0].reset();
        $('#company_report_template_links_modal').modal('show');
        loadGlDrop();
        company_report_template_links_table();
    }

    function delete_reportTemplateDetail(detID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detID': detID},
                    url: "<?php echo site_url('ReportTemplate/delete_reportTemplateDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            company_report_template_details_table();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function company_report_template_links_table(){
        var Otable = $('#company_report_template_link_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('ReportTemplate/fetch_company_report_template_links_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "linkID"},
                {"mData": "GLDescription"},
                {"mData": "sortOrder"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "detID","value": $("#detID").val()});
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


    function save_reportTemplateLink(){
        var data = $("#company_report_template_links_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ReportTemplate/save_reportTemplateLink'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    company_report_template_links_table();
                    $('#glAutoID').val('');
                    $('#sortOrderLink').val('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function loadGlDrop(){
        var companyReportTemplateID=$('#companyReportTemplateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyReportTemplateID: companyReportTemplateID, All: 'true'},
            url: "<?php echo site_url('ReportTemplate/load_gl_drop'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#Gldescription').html(data);

                setTimeout(function(){
                    $('#glAutoID').select2();
                }, 400);
            }, error: function () {

            }
        });
    }

    function delete_report_tempalte_Link(linkID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'linkID': linkID},
                    url: "<?php echo site_url('ReportTemplate/delete_report_tempalte_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            company_report_template_links_table();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>