<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$activitiessoci= $this->lang->line('emp_master_edu_activities_socities');
$veri=$this->lang->line('emp_master_edu_verified');
$notveri=$this->lang->line('emp_master_edu_not_verified');
$gppa=$this->lang->line('emp_GPA');

$degree = fetch_degree();
$date_format_policy = date_format_policy();

$subTab = $this->input->post('subTab');
$isFrom = $this->input->post('isFrom');

if($isFrom == 'profile'){
    echo '<style> .isVerified-div{ display: none; } </style>';
}
?>

<style>
    .btn-circle{
        padding: 0px 6px;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        line-height: 25px;

    }

    .verified{
        color: darkgreen;
    }

    .not-verified{
        color: darkred;
    }

    #education-tab .nav-tabs-custom>.nav-tabs>li.active{
        border-top: 0px !important;
    }
</style>



<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" id="education-tab" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="qualifications_li active" data-value="academicTab">
            <a href="#academicTab" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('emp_master_edu_academic');?></a><!--Academic-->
        </li>
        <li class="qualifications_li" data-value="certificationTab">
            <a href="#certificationTab" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('emp_master_edu_certification');?></a><!--Certification-->
        </li>
        <li class="pull-right" style="padding: 4px 4px;">
            <div class="pull-right">
                <a onclick="open_add_modal()" data-toggle="modal" class="btn size-sm btn-primary-new">
                    <i class="fa fa-plus"> </i> <?php echo $this->lang->line('common_add');?><!--Add-->
                </a>
            </div>
        </li>
    </ul>

    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21); padding-top: 0px;">
        <div class="tab-pane disabled active" id="academicTab">
            <div class="" style="" id="">
            <?php


            //echo '<pre>'; print_r($details); echo '</pre>';

            foreach($details as $key=>$det){
            if($key > 0){ echo '<hr>'; }
            $verified = ($det['hrVerified'] == 1)?'<span class="verified">'.$veri.'<!--Verified--></span>' : '<span class="not-verified">'.$notveri.'<!--Not Verified--></span>';
            $currentlyReading = ($det['currentlyReadingYN'] == 1)?'Reading' : '';
            echo '
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td>
                            <div><h4>'.$det['school'].'</h4> </div>
                            <div>'.$det['degreeDescription'].', '.$det['fieldOfStudy'].', '.$currentlyReading.'</div>
                            <div>
                                <small>'.$det['dateFromStr'].' - '.$det['dateToStr'].'</small>
                            </div>
                            <br>
                            <small> '.$det['description'].' </small>
                        </td>
                        <td>
                            <span class="pull-right">
                                <button id="edit" title="" rel="tooltip" class="btn btn-circle btn-mini btn-primary" onclick="editAcademic(\''.$det['id'].'\')"
                                        data-original-title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span class="pull-right" style="margin-top: 28px; margin-right: -25px;">
                                <button id="edit" title="" rel="tooltip" class="btn btn-circle btn-mini btn-danger" onclick="deleteAcademic(\''.$det['id'].'\')"
                                        data-original-title="Edit"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small>'.$activitiessoci.'<!--Activities and Societies--> :  '.$det['activitiesSocieties'].'</small>
                        </td>
                        <td class="pull-right" style="font-weight: bolder">
                            <span class="notActive" style="text-align: center;">&nbsp; '.$verified.'</span>
                        </td>
                    </tr>
                    </tbody>
                </table>';
            }
            ?>
            </div>
        </div>
        <div class="tab-pane disabled" id="certificationTab">
            <div class="tab-pane" id="two" style="">
            <?php
            foreach($certification_data as $key_ce=>$row){
            if($key_ce > 0){ echo '<hr>'; }

            $id = $row['certificateID'];
            $description = $row['Description'];
            $gpa = $row['GPA'];
            $institution = $row['Institution'];
            $awardedDate = $row['awardedDateStr'];
            $isVerified = $row['hrVerified'];
            $verified = ($isVerified == 1)?'<span class="verified">'.$veri.'<!--Verified--></span>' : '<span class="not-verified">'.$notveri.'<!--Not Verified--></span>';
            $function = 'editQualification(\''.$id.'\', \''.$description.'\', \''.$gpa.'\', \''.$institution.'\', \''.$awardedDate.'\', \''.$isVerified.'\')';


            echo '
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td>
                            <div><h4>'.$description.'</h4> </div>
                            <div>'.$gppa.'<!--GPA--> : '.$gpa.'</div>
                            <div>
                                <small>'.$awardedDate.'</small>
                            </div>
                            <br>
                            <small> '.$institution.' </small>
                        </td>
                        <td>
                            <span class="pull-right">
                                <button id="edit" title="" rel="tooltip" class="btn btn-circle btn-mini btn-primary" onclick="'.$function.'"
                                        data-original-title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span class="pull-right" style="margin-top: 28px; margin-right: -25px;">
                                <button id="edit" title="" rel="tooltip" class="btn btn-circle btn-mini btn-danger" onclick="delete_Qualification(\''.$id.'\')"
                                        data-original-title="Edit"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="pull-right" style="font-weight: bolder">
                            <span class="notActive" style="text-align: center;">&nbsp; '.$verified.'</span>
                        </td>
                    </tr>
                    </tbody>
                </table>';
            }
            ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="academic_modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form role="form" id="academic_form" class="form-horizontal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="academic_modal-title"></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="school"><?php echo $this->lang->line('emp_school');?><!--School--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="school" name="school">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="degree"><?php echo $this->lang->line('emp_degree');?><!--Degree--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('degree', $degree, '', 'class="form-control select2" id="degree" '); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="fieldOfStudy"><?php echo $this->lang->line('emp_master_field_of_study');?><!--Field Of Study--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="fieldOfStudy" name="fieldOfStudy">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="grade"><?php echo $this->lang->line('emp_grade');?><!--Grade--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="grade" name="grade">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="dateFrom"><?php echo $this->lang->line('emp_master_date_from');?><!--Date From--></label>
                            <div class="col-sm-6">
                                <div class="input-group datePic"">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateFrom" value="" id="dateFrom" class="form-control"
                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="dateTo"><?php echo $this->lang->line('emp_master_date_to');?><!--Date To--></label>
                            <div class="col-sm-6">
                                <div class="input-group datePic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateTo" value="" id="dateTo" class="form-control"
                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="currentlyReadingYN"><?php echo $this->lang->line('emp_master_currently_reading');?><!--Currently Reading--></label>
                            <div class="col-sm-6">
                                <input type="checkbox" name="currentlyReadingYN" id="currentlyReadingYN" value="1" style="margin-top: 12px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="description" name="description">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="acc_society"><?php echo $this->lang->line('emp_master_edu_activities_socities');?><!--Activities and Societies--></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="acc_society" name="acc_society"></textarea>
                            </div>
                        </div>
                        <div class="form-group isVerified-div">
                            <label class="col-sm-4 control-label" for="isVerified"><?php echo $this->lang->line('emp_master_is_verified');?><!--Is verified--></label>
                            <div class="col-sm-6">
                                <input type="checkbox" name="isVerified" id="isVerified" value="1" style="margin-top: 12px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="hidden-id-acc" name="hidden-id-acc" value="0">
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn-acc" onclick="saveAcademic()">
                    <?php echo $this->lang->line('emp_save');?><!--Save-->
                </button>
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="update-btn-acc" onclick="updateAcademic()">
                    <?php echo $this->lang->line('emp_update');?><!--Update-->
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
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
                                <label class="col-sm-4 control-label" for="certification">
                                <?php echo $this->lang->line('emp_certification');?><!--Certification--> <?php required_mark(); ?></label>
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
                                    <div class="input-group datePic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="awardedDate" value="" id="awardedDate" class="form-control"
                                         data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group isVerified-div">
                            <label class="col-sm-4 control-label" for="isVerified"><?php echo $this->lang->line('emp_master_is_verified');?><!--Is verified--></label>
                            <div class="col-sm-6">
                                <input type="checkbox" name="isVerified" id="isVerified-certificate" value="1" style="margin-top: 12px">
                            </div>
                        </div>
                        </div>
                    </div>
            </form>
            <div class="modal-footer">
                <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="save-btn" onclick="saveQualification()">
                <?php echo $this->lang->line('emp_save');?><!--Save--></button>
                <button type="button" class="btn btn-primary btn-sm actionBtn" id="update-btn" onclick="updateQualification()">
                <?php echo $this->lang->line('emp_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    var subTab = '<?php echo $subTab; ?>';
    var isFrom = '<?php echo $isFrom; ?>';
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datePic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    function open_add_modal(){
        var tabName = $('.qualifications_li.active').attr('data-value');

        switch (tabName){
            case 'academicTab':
            add_academic();
            break;

            case 'certificationTab':
            add_qualification();
            break;

            default:
            add_academic();
        }
    }

    function add_academic(){
        $('#academic_form').trigger("reset");
        $('.actionBtn').hide();
        $('#save-btn-acc').show();
        $('#academic_modal-title').text('<?php echo $this->lang->line('emp_master_new_acadamic');?>');/*New Academic*/
        $('#academic_modal').modal({backdrop: "static"});
    }

    function saveAcademic(){
        var postData = $('#academic_form').serializeArray();
        var empID = $('#updateID').val();
        /**** In employee master updateID is in personal tab *****/
        /**** In Profile updateID is in qualification_tab *****/

        postData.push({'name':'empID', 'value':empID});
        postData.push({'name':'isFrom', 'value':isFrom});

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/saveAcademic'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#academic_modal').modal('hide');

                    setTimeout(function(){
                        fetch_qualification();
                    }, 300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function updateAcademic(){
        var postData = $('#academic_form').serializeArray();
        var empID = $('#updateID').val();
        postData.push({'name':'empID', 'value':empID});
        postData.push({'name':'isFrom', 'value':isFrom});

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/updateAcademic'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#academic_modal').modal('hide');

                    setTimeout(function(){
                        fetch_qualification();
                    }, 300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function editAcademic(id){
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/getAcademicData'); ?>',
            data: {id:id},
            dataType: 'JSON',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();

                $('#academic_form').trigger("reset");

                $('#school').val(data['school']);
                $('#degree').val(data['degree']);
                $('#fieldOfStudy').val(data['fieldOfStudy']);
                $('#grade').val(data['grade']);
                $('#dateFrom').val(data['dateFromStr']);
                $('#dateTo').val(data['dateToStr']);
                $('#description').val(data['description']);
                $('#acc_society').val(data['activitiesSocieties']);
                var currentlyReadingYN = data['currentlyReadingYN'];
                $('#currentlyReadingYN').prop('checked', (currentlyReadingYN == 1));
                var hrVerified = data['hrVerified'];
                $('#isVerified').prop('checked', (hrVerified == 1));
                $('#hidden-id-acc').val(id);

                $('.actionBtn').hide();
                $('#update-btn-acc').show();
                $('#academic_modal-title').text('<?php echo $this->lang->line('emp_master_edit_acadamic_details');?>');/*Edit Academic Details*/
                $('#academic_modal').modal({backdrop: "static"});
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function deleteAcademic(id){
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
                    url :"<?php echo site_url('Employee/deleteAcademic'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ fetch_qualification(); }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function add_qualification(){
        $('#qualification_form').trigger("reset");
        $('.actionBtn').hide();
        $('#save-btn').show();
        $('#addModal').modal({backdrop: "static"});
        $('#qualificationModal-title').text('<?php echo $this->lang->line('emp_qualification');?>');
    }

    function saveQualification(){
        var postData = $('#qualification_form').serializeArray();
        var empID = $('#updateID').val();
        postData.push({'name':'empID', 'value':empID});
        postData.push({'name':'isFrom', 'value':isFrom});
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
                    setTimeout(function(){
                        fetch_qualification('certificationTab');
                    }, 300);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })
    }

    function editQualification(id, certificate, gpa, institute, awardDate, isVerified){

        $('.actionBtn').hide();
        $('#update-btn').show();

        $('#qualificationModal-title').text('<?php echo $this->lang->line('emp_edit_qualification');?>');
        $('#addModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#certification').val( certificate );
        $('#GPA').val( gpa );
        $('#institution').val( institute );
        $('#awardedDate').val( awardDate );
        $('#isVerified-certificate').prop('checked', (isVerified == 1));

    }

    function delete_Qualification(id){
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
                        if( data[0] == 's'){
                            setTimeout(function(){
                                fetch_qualification('certificationTab');
                            }, 300);
                        }
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
                    setTimeout(function(){
                        fetch_qualification('certificationTab');
                    }, 300);
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

    if(subTab != ''){
        $('[href=#'+subTab+']').tab('show');
    }

</script>




<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-06
 * Time: 2:04 PM
 */