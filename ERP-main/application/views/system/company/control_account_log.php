<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_control_accounts');
$all_control_account_drop = all_control_account_drop(false);
echo head_page($title, true);

/*echo head_page('Company Configuration',false); */?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel">

    </div>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary btn-wizard size-sm " href="#step1" data-toggle="tab"><?php echo $this->lang->line('config_control_accounts');?><!--Control Account--></a>
        <a class="btn btn-default btn-wizard size-sm" href="#step2" onclick="control_account_log()" data-toggle="tab">Control Account Log<!--Control Account Log--></a>

    </div>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <div id="" class=" " style="margin-top:10px;">
                <div class="row">
                <?php echo form_open('', 'role="form" id="controlaccount_filter_form"'); ?>
                    <!-- <div class="form-group col-sm-3">
                        <label for="supplierPrimaryCode"> <?php //echo $this->lang->line('config_control_account');?></label><br>
                        <?php //echo form_dropdown('controlAccounDrop[]', $all_control_account_drop, '', 'class="form-control" id="controlAccount_filter" onchange="control_account_log()" multiple="multiple"'); ?>
                    </div> -->
                </form>
                    <!-- <div class="col-md-12 text-right" style="margin-top:10px;">
                        <a href="#" type="button" class="btn btn-excel " style="margin-right:20px;" onclick="excel_export_conrtol_account()">
                            <i class="fa fa-file-excel-o"></i> <?php //echo $this->lang->line('common_excel');?>
                        </a>
                    </div> -->
                </div>
            </div>
            <div class="table-responsive" style="margin-top:10px;">
                <table id="control_account" class="<?php echo table_class(); ?>">
                <thead>
                    
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 10%">Control Account Type</th>
                        <th style="min-width: 13%"><?php echo $this->lang->line('config_gl_system_code');?><!--GL System Code--></th>
                        <th style="min-width: 12%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                        <th style="min-width: 30%"><?php echo $this->lang->line('config_gl_description');?><!--GL Description--></th>
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div id="step2" class="tab-pane">

            <div id="" class=" " style="margin-top:10px;">
                <div class="row">
                <?php echo form_open('', 'role="form" id="controlaccountlog_filter_form"'); ?>
                    <div class="form-group col-sm-3">
                        <label for="supplierPrimaryCode"> <?php echo $this->lang->line('config_control_account');?><!--Control Account--></label><br>
                        <?php echo form_dropdown('controlAccounDrop[]', $all_control_account_drop, '', 'class="form-control" id="controlAccount_filter" onchange="control_account_log()" multiple="multiple"'); ?>
                    </div>
                </form>
                    <div class="form-group col-sm-4"><br>
                        <button type="button" class="btn btn-primary-new size-sm "
                                onclick="clear_all_filters()"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
                        </button>
                    </div>
                    <div class="col-md-5 text-right" style="margin-top:10px;">
                        <a href="#" type="button" class="btn btn-excel  btn-success-new size-sm " style="margin-right:20px;" onclick="excel_export_conrtol_account_log()">
                            <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?><!--Excel-->
                        </a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="table-responsive" style="margin-top:10px;">
                <table id="control_account_log" class="<?php echo table_class(); ?>">
                <thead>
                    
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 10%">Control Account Type</th>
                        <th style="min-width: 13%"><?php echo $this->lang->line('config_gl_system_code');?><!--GL System Code--></th>
                        <th style="min-width: 12%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                        <th style="min-width: 30%"><?php echo $this->lang->line('config_gl_description');?><!--GL Description--></th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th  style="min-width: 15%">Created User</th>
                        <th  style="min-width: 30%"><?php echo $this->lang->line('common_created_date');?><!--Created Date--></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
            });

            $('#controlAccount_filter').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            control_account();
            control_account_log();
        });

        function control_account(){
            var Otable = $('#control_account').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Company/fetch_control_account'); ?>",
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
                    $(".switch-chk").bootstrapSwitch();
                    $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_matching_records_found'); ?>')
                    $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                    $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
                },
                "aoColumns": [
                    {"mData": "controlAccountsAutoID"},
                    {"mData": "controlAccountType"},
                    //{"mData": "controlAccountDescription"},
                    {"mData": "systemAccountCode"},
                    {"mData": "GLSecondaryCode"},
                    {"mData": "GLDescription"},
                    {"mData": "edit"}
                ],
                "columnDefs": [
                    {"targets": [0,5], "searchable": false}],
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
       

        function control_account_log(){
            var COtable = $('#control_account_log').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Company/fetch_control_account_log'); ?>",
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
                    {"mData": "controlAccountLogAutoID"},
                    {"mData": "controlAccountType"},
                    //{"mData": "controlAccountDescription"},
                    {"mData": "systemAccountCode"},
                    {"mData": "GLSecondaryCode"},
                    {"mData": "GLDescription"},
                    {"mData": "status"},
                    {"mData": "createdUserName"},
                    {"mData": "createdDateTime"} 
                ],
                "columnDefs": [
                    {"targets": [0], "searchable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "controlAccounDrop", "value": $("#controlAccount_filter").val()});
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

        function changeControlAccoutnStatus(obj, id, GLAutoID) {
            var msg, postStatus;
           
            if ($(obj).prop('checked')) {
                msg = "<?php echo $this->lang->line('config_you_want_to_open_this_control_account');?>";
                postStatus = 1;
                
            } else {
                msg = "<?php echo $this->lang->line('config_you_want_to_close_this_control_account');?>"
                postStatus = 0;
            }
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: msg,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            url: "<?php echo site_url('Company/statusChangeControlAccount'); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {'controlAccountsAutoID': id, 'GLAutoID': GLAutoID, 'status': postStatus},
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] != 's') {

                                    var thisChk = $('#status' + id);
                                    var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                    var changeFn = thisChk.attr('onchange');

                                    thisChk.removeAttr('onchange');
                                    thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', 'error');

                                var thisChk = $('#status' + id);
                                var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                                var changeFn = thisChk.attr('onchange');

                                thisChk.removeAttr('onchange');
                                thisChk.prop('checked', changeStatus).change().attr('onchange', changeFn);

                            }
                        });
                    }
                    else {
                        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
                        $('#status_' + id).prop('checked', changeStatus).change();
                    }
                }
            );
        }

        function clear_all_filters(){
        $('#controlAccount_filter').multiselect2('deselectAll', false);
        $('#controlAccount_filter').multiselect2('updateButtonText');

        control_account_log();
    }

    function excel_export_conrtol_account_log() {
            var form = document.getElementById('controlaccountlog_filter_form');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#controlaccountlog_filter_form').serializeArray();
            form.action = '<?php echo site_url('Company/export_excel_controlaccountlog'); ?>';
            form.submit();
    }
    function excel_export_conrtol_account() {
            var form = document.getElementById('controlaccount_filter_form');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#controlaccount_filter_form').serializeArray();
            form.action = '<?php echo site_url('Company/export_excel_controlaccount'); ?>';
            form.submit();
    }
    </script>