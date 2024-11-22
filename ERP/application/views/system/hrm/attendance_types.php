<!--Translation added by Naseek-->


<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_attendance_types');
echo head_page($title  , false);

$system_attendanceTypes = system_attendanceTypes();

?>

<style type="text/css">
    .myInputGroup{ margin-bottom: 1%; }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openAttendanceType_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="attendanceTypeTB" class="<?php echo table_class(); ?> hover">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_attendance_present_type');?><!--Present Type--></th>
            <th style="width: 30px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="attendanceType_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_add_attendance_type');?><!--Add Attendance Type--></h4>
            </div>

            <div class="modal-body">
                <form id="presentType_form">
                    <div class="row">
                    <?php
                    foreach($system_attendanceTypes as $key=>$type){
                        $appendItem ='';
                        $appendItem .= '<div class="col-lg-4 myInputGroup"> <div class="input-group">';
                        $appendItem .= '<span class="input-group-addon">';
                        $appendItem .= '<input type="checkbox" name="attType[]" class="presentTypeCheckBox"  value="'.$type['PresentTypeID'].'"></span>';
                        $appendItem .= '<input type="text" class="form-control col-xs-5 addOnTxt" value="'.$type['PresentTypeDes'].'" disabled> </div></div>';
                        echo $appendItem;
                    }
                    ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_presentTypes()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var is_loaded = $('#is-loaded-country-master-tb');
    var country_master_tb = $('#country-master-tb');
    var oTable;

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/attendance_types','Test','HRMS');
        });
        load_attendanceTypeTB();
    });

    function load_attendanceTypeTB(){
        $('#attendanceTypeTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_attendanceType'); ?>",
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
                {"mData": "AttPresentTypeID"},
                {"mData": "PresentTypeDes"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
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

    function openAttendanceType_modal(){
        $('#attendanceType_modal').modal({backdrop: "static"});
    }

    function save_presentTypes(){
        var count = 0;
        $('.presentTypeCheckBox').each( function(){
           if( $(this).is(':checked') == true ){
               count = 1;
               return false;
           }
        });

        if(count > 0){
            var postData = $('#presentType_form').serializeArray();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/savePresentType'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        countryArray = [];
                        $('#attendanceType_modal').modal('hide');
                        setTimeout(function(){
                            fetchPage('system/hrm/attendance_types','Test','HRMS');
                        }, 300);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('hrms_attendance_please_select_at_least_one_present_type');?>');/*Please select at least one present type*/
        }
    }

    function delete_attendanceTypes(id, description){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_attendanceTypes'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ fetchPage('system/hrm/attendance_types','Test','HRMS'); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
</script>



<?php
