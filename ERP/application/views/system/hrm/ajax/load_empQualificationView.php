<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="row table-responsive">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openQualification_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add');?><!--Add--> </button>
    </div>
</div>
<div class="table-responsive" style="margin-top: 1%;">
    <table id="load_qualification" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('emp_certification');?><!--Certification--></th>
            <th style="width: auto"><?php echo $this->lang->line('emp_GPA');?><!--GPA--></th>
            <th style="width: auto"><?php echo $this->lang->line('emp_institution');?><!--Institute--></th>
            <th style="width: auto"><?php echo $this->lang->line('emp_award_date');?><!--Award Date--></th>
            <th style="width: 30px"></th>
        </tr>
        </thead>
    </table>
</div>




<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="qualificationModal-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="qualificationModal-title"></h4>
            </div>

            <form role="form" id="qualification_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="certification"><?php echo $this->lang->line('emp_certification');?><!--Certification--> <?php required_mark(); ?></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="certification" name="certification">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="GPA"><?php echo $this->lang->line('emp_GPA');?><!--GPA--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="GPA" name="GPA">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="institution"><?php echo $this->lang->line('emp_institution');?><!--Institution--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="institution" name="institution">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="awardedDate"><?php echo $this->lang->line('emp_award_date');?><!--Awarded Date--></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="awardedDate" value="" id="awardedDate" class="form-control dateFields" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
            <div class="modal-footer">
                <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn" onclick="saveQualification()"><?php echo $this->lang->line('emp_save');?><!--Save--></button>
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="update-btn" onclick="updateQualification()"><?php echo $this->lang->line('emp_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var country_master_tb = $('#country-master-tb');

    $(document).ready(function() {
        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');
        });
        load_qualification();
    });

    function load_qualification(){
        var Otable = $('#load_qualification').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_qualification'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                if(fromHiarachy==1){
                    Otable.column( 5 ).visible( false );
                }
            },
            "aoColumns": [
                {"mData": "certificateID"},
                {"mData": "Description"},
                {"mData": "GPA"},
                {"mData": "Institution"},
                {"mData": "AwardedDate"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                var empID = $('#updateID').val();
                aoData.push({'name':'empID', 'value': empID});
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

    function openQualification_modal(){
        $('#qualification_form').trigger("reset");
        $('.actionBtn').hide();
        $('#save-btn').show();
        $('#qualificationModal-title').text('<?php echo $this->lang->line('emp_qualification');?>');
        $('#addModal').modal({backdrop: "static"});
    }

    function saveQualification(){
        var postData = $('#qualification_form').serializeArray();
        var empID = $('#updateID').val();
        postData.push({'name':'empID', 'value':empID});
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/saveQualification'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#addModal').modal('hide');
                    load_qualification();
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    $(document).on('click', '.editIcon', function(){
        var id = $(this).attr('data-id');
        var thisRow = $(this).closest('tr');

        var certificate = $.trim(thisRow.find('td:eq(1)').html());
        var gpa = $.trim(thisRow.find('td:eq(2)').html());
        var institute = $.trim(thisRow.find('td:eq(3)').html());
        var awardDate = $.trim(thisRow.find('td:eq(4)').html());

        $('.actionBtn').hide();
        $('#update-btn').show();

        $('#qualificationModal-title').text('<?php echo $this->lang->line('emp_edit_qualification');?>');
        $('#addModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#certification').val( certificate );
        $('#GPA').val( gpa );
        $('#institution').val( institute );
        $('#awardedDate').val( awardDate );

    });


    function delete_Qualification(id, description){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/deleteQualification'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_qualification() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function updateQualification(){
        var postData = $('#qualification_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/editQualification'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#addModal').modal('hide');
                    load_qualification();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
    }

</script>




<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-06
 * Time: 2:04 PM
 */