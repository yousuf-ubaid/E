<?php
$primaryLanguage = getPrimaryLanguage();
echo head_page('Audit Log', true);
$date_format_policy = date_format_policy();
$employee_arr = all_employees_drop(false);
?>
<div id="filter-panel" class="collapse filter-panel">
    <?php echo form_open('', 'role="form" id="audit_log_filter_form"'); ?>
        <div class="row">
        <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_employee_name');?> </label> <br><!--Customer Name-->
                <?php echo form_dropdown('employee[]', $employee_arr, '', 'class="form-control" id="employee" onchange="Otable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="filter_date"> <?php echo $this->lang->line('common_date_from');?> </label> <br><!--date from-->
                <input type="text" name="filter_date_from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="filter_date_from"
                           class="input-small">
            </div>
            <div class="form-group col-sm-3">
                <label for="filter_date"> <?php echo $this->lang->line('common_date_to');?> </label> <br><!--date to -->
                <input type="text" name="filter_date_to" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="filter_date_to"
                           class="input-small">
            </div>
            
            <div class="form-group col-sm-3">
             
                <button type="button" class="btn btn-primary pull-right" style="margin-left:5px;margin-top:10px;"
                        onclick="clear_all_filters()" ><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
                </button>
                <!-- <div class="col-md-5 text-right" style="margin-top:10px;"> -->
                        
                   <!--  </div> -->
            </div>
        </div>
    </form>
</div>

<div class="row">
        <div class="col-md-5">
            
        </div>
        
        <div class="col-md-7 text-right">
            <a href="#" type="button" class="btn btn-excel btn-success-new size-sm " style="margin-left: 2px" onclick="excel_export_audit_log()">
                <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?><!--Excel-->
            </a>
        </div>
 </div><hr>
    
<div class="table-responsive">
    <table id="audit_log_tbl" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 2%">#</th>
            <th style="width: 13%;">Emp Short Code</th><!--Code-->
            <th style="width: 25%;">Emp Name<!--Details-->
            <th style="width: 30%;">Document</th><!--Type-->
            <th style="width: 10%">Date</th><!--Confirmed-->
        </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('#employee').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

            });
            Inputmask().mask(document.querySelectorAll("input"));

        audit_log_table();
    });

    function audit_log_table(selectedID=null){
        Otable = $('#audit_log_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "lengthMenu": [[500, 1000, 2000], [500, 1000, 2000, "All"]],
            "sAjaxSource": "<?php echo site_url('Company/fetch_audit_log'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {

                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if( parseInt(oSettings.aoData[x]._aData['contractAutoID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');

                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');

            },
            "aoColumns": [
                {"mData": "auditlogID"},
                {"mData": "Ecode"},
                {"mData": "Ename2"},
                {"mData": "documentID"},
                {"mData": "createdDateTime"}


            ],
            "columnDefs": [{"targets": [4], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "date_from", "value": $("#filter_date_from").val()});
                aoData.push({"name": "date_to", "value": $("#filter_date_to").val()});
                aoData.push({"name": "employee", "value": $("#employee").val()});
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

    function clear_all_filters(){
        $('#filter_date_from').val("");
        $('#filter_date_to').val("");
        $('#employee').multiselect2('deselectAll', false);
        $('#employee').multiselect2('updateButtonText');
        Otable.draw();
    }

    function excel_export_audit_log() {
        var form = document.getElementById('audit_log_filter_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#audit_log_filter_form').serializeArray();
        form.action = '<?php echo site_url('Company/export_excel_audit_log'); ?>';
        form.submit();
    }
</script>