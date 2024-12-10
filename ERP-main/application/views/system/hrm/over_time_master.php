



<!--Translation added by Naseek-->
<?php



$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_over_time_master');
echo head_page($title, false);

$masterCat = systemOT_drop();
$salaryCat = system_salary_cat_drop('OT');
?>
<style type="text/css">

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <!--<table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Active
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> In-Active
                </td>
            </tr>
            </tbody>
        </table>-->
    </div>
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openOTCat_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_OTCats" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_attendance_master_category');?><!--Master Category--></th>
            <th style="width: auto"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_otCat"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_new_ot_category');?><!--New OT Category--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="overTimeCat_Form"'); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="description"  id="description" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="masterCat"><?php echo $this->lang->line('hrms_attendance_master_category');?><!--Master category--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('masterCat', $masterCat, '', 'class="form-control saveInputs select2" id="masterCat" required'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="masterCat"><?php echo $this->lang->line('hrms_attendance_salary_category');?><!--Salary Category--> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('salaryCategoryID', $salaryCat, '', 'class="form-control saveInputs select2" id="salaryCategoryID" required'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="editID" name="editID">
                <button type="submit" class="btn btn-primary btn-sm" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<script>
    var overTimeCat_Form = $('#overTimeCat_Form');
    $('.select2').select2();

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/over_time_master','Test','HRMS');
        });

        load_OTCats();

        overTimeCat_Form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
                masterCat: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_master_category_is_required');?>.'}}},/*Master category is required*/
                salaryCategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_salary_category_is_required');?>.'}}}/*Salary Category is required*/
            }
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var requestUrl = $form.attr('action');
            var postData = $form.serializeArray();


            $.ajax({
                type: 'post',
                url: requestUrl,
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_otCat').modal('hide');
                        load_OTCats( data[2] );
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });


        });

    });

    function load_OTCats(selectedRowID=null){
        $('#load_OTCats').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_OTCat'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['ID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "ID"},
                {"mData": "description"},
                {"mData": "catDescription"},
                {"mData": "salaryDescription"},
                {"mData": "edit"}
            ],
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

    function openOTCat_modal(){
        $('#masterCat').val('').change();
        overTimeCat_Form[0].reset();
        overTimeCat_Form.bootstrapValidator('resetForm', true);
        overTimeCat_Form.attr('action', '<?php echo site_url('Employee/saveOTCat'); ?>');

        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_new_ot_category');?>');
        $('#new_otCat').modal({backdrop: "static"});
    }


    function edit_OTCat( obj ){
        overTimeCat_Form[0].reset();
        overTimeCat_Form.bootstrapValidator('resetForm', true);
        overTimeCat_Form.attr('action', '<?php echo site_url('Employee/editOTCat'); ?>');

        var details = getTableRowData(obj);

        $('#description').val( $.trim(details.description ) );
        $('#editID').val( $.trim(details.ID ) );
        $('#masterCat').val(details.OTMasterID).change();
        $('#salaryCategoryID').val(details.salaryCategoryID).change();


        $('#myModalLabel').text('<?php echo $this->lang->line('hrms_attendance_edit_ot_category');?>');


        overTimeCat_Form.bootstrapValidator('resetField', 'masterCat');

        $('#new_otCat').modal({backdrop: "static"});
    }

    function delete_OTCat(obj){
        var details = getTableRowData(obj);
        swal(
            {
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
                    url :"<?php echo site_url('Employee/deleteOTCat'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hiddenID':details.ID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's'){ load_OTCats(); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function getTableRowData(obj){
        var table = $('#load_OTCats').DataTable();
        var thisRow = $(obj);
        return table.row(thisRow.parents('tr')).data();
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>



<?php
