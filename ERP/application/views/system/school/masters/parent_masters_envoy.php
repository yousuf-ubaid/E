<?php echo head_page('Parent Master', true);

$this->load->helper('sm_school');

$country = fetch_country();
$area = fetch_area();
$status = all_status();
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

<div class="row">
    <div class="col-md-9">&nbsp;</div>
    <div class="col-md-3">
        <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/school/masters/parent_create_envoy','','HRMS', '', '', '<?php echo $page_url; ?>')" class="btn btn-success-new size-sm pull-left">
            <i class="fa fa-plus"></i> Add
        </button>
        <button class="btn btn-info size-sm">
            <i class="fa fa-users"></i> Parent Code
        </button>
        <button class="btn btn-success-new size-sm" onclick="excelDownload()">
            <i class="fa fa-file-excel-o"></i>
        </button>
        <button class="btn btn-info size-sm"><i class="fa fa-print"></i></button>
    </div>
</div>
<hr>

<div>
    <div class="row">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('PM_Status'); ?>Status:</label><br>
            <?php echo form_dropdown('status[]', $status, '', 'class="form-control" id="status"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('PM_Country'); ?>Country:</label><br>
            <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('PM_Area'); ?>Area:</label><br>
            <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area"'); ?>
        </div>
        <div class="form-group col-sm-1">
            <i class="fa fa-search btn btn-info pull-right" aria-hidden="true"></i>
        </div>
    </div>
</div>
<br>
<hr><br>
<div>
    <div class="table-responsive">
        <table id="ParenttTB_envoy" class="<?php echo table_class(); ?>">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th style="width: auto">Parent&nbsp;Code</th>
                    <th style="width: auto;">Contact&nbsp;Person</th>
                    <th style="width: auto;">Contact&nbsp;Person (Other)</th>
                    <th style="width: auto">Resident/Labour/NIC/Civil No</th>
                    <th style="width: auto">Email</th>
                    <th style="width: auto">Contact&nbsp;Number/s</th>
                    <th style="width: auto">Area</th>
                    <th style="width: 40px"></th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var Otable;

    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/school/masters/parent_masters_envoy', 'Test', 'HRMS');
        });

        $('#status').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        $('#country').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        $('#area').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        fetch_parent();

});

function fetch_parent() {
    Otable = $('#parentM_envoy').DataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "scrollX": true,
        "sAjaxSource": "<?php echo site_url('school/parent/fetch_parents'); ?>",
        "aaSorting": [
            [2, 'desc']
        ],
        "fnInitComplete": function() {

        },
        "fnDrawCallback": function(oSettings) {
            $("[rel=tooltip]").tooltip();
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i;
                (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }

        },
        "aoColumns": [
            {"mData": "parentID"},
            {"mData": "Contact_Name"},
            {"mData": "otherName"},
            {"mData": "NIC"},
            {"mData": "Contact_Person_email"},
            {"mData": "area"},
            {"mData": "action"}
        ],
        "columnDefs": [{
            "targets": [6],
            "orderable": false
        }, {
            "visible": false,
            "searchable": true,
            "targets": [7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
        }, {
            "searchable": false,
            "targets": [0, 2, 4, 5, 6]
        }],

        "fnServerData": function(sSource, aoData, fnCallback) {

            aoData.push({
                "name": "status",
                "value": $("#status").val()
            });
            aoData.push({
                "name": "country",
                "value": $("#country").val()
            });
            aoData.push({
                "name": "area",
                "value": $("#area").val()
            });
        }
    });
}

function delete_parent(parentID) {
    swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
            /*Are you sure?*/
            text: "<?php echo $this->lang->line('parentmasters_you_want_to_delete_this_parent'); ?>",
            /*You want to delete this customer!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
            /*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
        },
        function() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'parentAutoID': parentID
                },
                url: "<?php echo site_url('school/parent/delete_parent'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    refreshNotifications(true);
                    Otable.draw();
                    ODeltable.draw();
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
}

function excelDownload() {
    var form = document.getElementById('filterForm');
    form.target = '_blank';
    form.method = 'post';
    form.post = $('#filterForm').serializeArray();
    form.action = '<?php echo site_url('school/parent/export_excel_parentmaster'); ?>';
    form.submit();
}

function clear_all_filters() {
    $('#status').multiselect2('deselectAll', false);
    $('#status').multiselect2('updateButtonText');
    $('#country').multiselect2('deselectAll', false);
    $('#country').multiselect2('updateButtonText');
    $('#area').multiselect2('deselectAll', false);
    $('#area').multiselect2('updateButtonText');
    Otable.draw();
}
</script>