<?php
// $primaryLanguage = getPrimaryLanguage();
// $this->lang->load('school_studentmaster', $primaryLanguage);
// $this->lang->load('common', $primaryLanguage);

echo head_page('Student Master', true);

$this->load->helper('sm_school');

$student_arr = all_students_drop(false);
$student_code = fetch_student_code();
$admitted_year = fetch_admitted_year();
$grade = fetch_grade();
$group = fetch_group();
$category = fetch_category();
$sponsor = all_sponsor();
$status = all_status();
$required = all_required();
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
    <div class="col-md-6">&nbsp;</div>
    <div class="col-md-6">
        <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/school/masters/student_create_envoy','','HRMS', '', '', '<?php echo $page_url; ?>')" class="btn btn-success-new size-sm pull-left">
            <i class="fa fa-plus"></i> Add
        </button>
        <button class="btn btn-info size-sm"><i class="fa fa-graduation-cap"></i> Manage SN</button>
        <button class="btn btn-info size-sm"><i class="fa fa-user"></i> Change student Syllabus</button>
        <button class="btn btn-info size-sm">Medium Setup</button>
        <button class="btn btn-default size-sm"><i class="fa fa-files-o"></i></button>
        <button class="btn btn-success-new size-sm" onclick="excelDownload()">
            <i class="fa fa-file-excel-o"></i>
        </button>
        <button class="btn btn-info size-sm"><i class="fa fa-bars"></i></button>
    </div>
</div>
<hr>

<div id="filter-panel">

    <?php echo form_open('', 'role="form" id="itemmaster_filter_form"'); ?>
    <div class="row">
        <div class="form-group col-sm-3">
            <div class="form-group col-sm-12">
                <label><?php echo $this->lang->line('SM_Academic_Year'); ?>Academic Year:</label>
                <?php echo form_dropdown('admitted_year[]', $admitted_year, '', 'class="form-control" id="admitted_year" onchange="LoadYear()"'); ?>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('SM_Class'); ?>Class:</label>
            <div class="input-group" style="width: 100%; ">
                <?php echo form_dropdown('grade[]', $grade, '', 'class="form-control" id="grade" onchange="LoadGrade()"'); ?>
                <span class="input-group-btn" style="width:0px;"></span>
                <?php echo form_dropdown('group[]', $group, '', 'class="form-control" id="group" onchange="LoadGroup()"'); ?>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="SM_Category"><?php echo $this->lang->line('SM_Category'); ?>Category:</label><br>
            <?php echo form_dropdown('category[]', $category, '', 'class="form-control" id="category" onchange="LoadCategory()"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label>
                <?php echo $this->lang->line('SM_Sort_By'); ?>Sort By:</label>
            <?php echo form_dropdown('student_code[]', $student_code, '', 'class="form-control" id="student_code" onchange="LoadStuCode()"'); ?>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            <div class="form-group col-sm-6">
                <label for="SM_Required"><?php echo $this->lang->line('SM_Required'); ?>Select Required:</label>
                <?php echo form_dropdown('required[]', $required, '', 'class="form-control" id="required" onchange="LoadRequired()"'); ?>
            </div>
            <div class="form-group col-sm-6">
                <label for="SM_Status"><?php echo $this->lang->line('SM_Status'); ?>Status:</label>
                <?php echo form_dropdown('status[]', $status, '', 'class="form-control" id="status" onchange="LoadStatus()"'); ?>
            </div>
        </div>
        <div class="form-group col-sm-5">
            <label for="SM_Sponsor"><?php echo $this->lang->line('SM_Sponsor'); ?>Sponsor/s:</label><br>
            <?php echo form_dropdown('sponsor[]', $sponsor, '', 'class="form-control" id="sponsor" onchange="LoadSponsor()"'); ?>
        </div>
        <div class="form-group col-sm-1">
            <i class="fa fa-search btn btn-info" aria-hidden="true"></i>
        </div>
        <div class="form-group col-sm-3">
            <label>
                <?php echo $this->lang->line('SM_Search_By'); ?>Search by Student Code:</label>
            <div class="input-group" style="width: 100%; ">
                <?php echo form_dropdown('student_code[]', $student_code, '', 'class="form-control" id="student_code" onchange="LoadStuCode()"'); ?>
                <span class="input-group-btn" style="width:0px;"></span>
                <i class="fa fa-search btn btn-info" aria-hidden="true"></i>
                <span class="input-group-btn" style="width:0px;"></span>
                <a id="cancelSearch" href="#" onclick="clearSearchFilter()">
                    <img src="<?php echo base_url("images/crm/cancel-search.gif") ?>">
                </a>
            </div>
            
        </div>
    </div>
    </form>
    
</div>
<br>
<hr><br>
<div id="studentmaster" style="margin-top:20px;">
    <div class="table-responsive">
        <table id="studentM_envoy" class="<?php echo table_class(); ?>">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th style="width: auto"><?php echo $this->lang->line('Stu_Code'); ?>Student&nbsp;Code</th>
                    <th style="width: auto;"><?php echo $this->lang->line('Stu_Name'); ?>Name</th>
                    <th style="width: auto"><?php echo $this->lang->line('Stu_date_of_birth'); ?>Date of Birth</th>
                    <th style="width: auto"><?php echo $this->lang->line('Stu_present_class'); ?>Class</th>
                    <th style="width: auto"><?php echo $this->lang->line('Contact_Person_surname'); ?>Contact Person</th>
                    <th style="width: 40px"><?php echo $this->lang->line('common_action'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    var Otable;

    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/school/masters/student_masters_envoy', 'Test', 'HRMS');
        });


        fetch_Student();

        $('#student_code').change(function() {
            Otable.draw();
        });
        $('#admitted_year').change(function() {
            Otable.draw();
        });
        $('#grade').change(function() {
            Otable.draw();
        });
        $('#group').change(function() {
            Otable.draw();
        });
        $('#category').change(function() {
            Otable.draw();
        });
        $('#sponsor').change(function() {
            Otable.draw();
        });
        $('#status').change(function() {
            Otable.draw();
        });
        $('#required').change(function() {
            Otable.draw();
        });

    });

    function LoadYear() {
        $('#admitted_year').val("");
        $('#admitted_year option').remove();
        load_year();
        Otable.draw();
    }
    function LoadGrade() {
        $('#grade').val("");
        $('#grade option').remove();
        load_grade();
        Otable.draw();
    }
    function LoadGroup() {
        $('#group').val("");
        $('#group option').remove();
        load_group();
        Otable.draw();
    }
    function LoadCategory() {
        $('#category').val("");
        $('#category option').remove();
        load_category();
        Otable.draw();
    }
    function LoadSponsor() {
        $('#sponsor').val("");
        $('#sponsor option').remove();
        load_sponsor();
        Otable.draw();
    }
    function LoadStuCode() {
        $('#student_code').val("");
        $('#student_code option').remove();
        load_student_code();
        Otable.draw();
    }
    function LoadStatus() {
        $('#status').val("");
        $('#status option').remove();
        load_status();
        Otable.draw();
    }
    function LoadRequired() {
        $('#required').val("");
        $('#required option').remove();
        load_required();
        Otable.draw();
    }


    function fetch_Student() {
        Otable = $('#studentM_envoy').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "scrollX": true,
            "sAjaxSource": "<?php echo site_url('SM_School/fetch_student'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "stuID"},
                {"mData": "student_code"},
                {"mData": "name"},
                {"mData": "dob"},
                {"mData": "class"},
                {"mData": "first_name"},
                {"mData": "action"}
            ],
            "columnDefs": [
                    {"targets": [0], "searchable": false},{"targets": [2, 3], "orderable": false}
                   
            ],

            "fnServerData": function(sSource, aoData, fnCallback) {

                aoData.push({
                    "name": "student_code",
                    "value": $("#student_code").val()
                });
                aoData.push({
                    "name": "admitted_year",
                    "value": $("#admitted_year").val()
                });
                aoData.push({
                    "name": "grade",
                    "value": $("#grade").val()
                });
                aoData.push({
                    "name": "group",
                    "value": $("#group").val()
                });
                aoData.push({
                    "name": "status",
                    "value": $("#status").val()
                });
                aoData.push({
                    "name": "category",
                    "value": $("#category").val()
                });
                aoData.push({
                    "name": "sponsor",
                    "value": $("#sponsor").val()
                });
                aoData.push({
                    "name": "required",
                    "value": $("#required").val()
                });
                aoData.push({
                    "name": "deletedYN",
                    "value": 0
                });
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

    function delete_student(stuID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('studentmasters_you_want_to_delete_this_student'); ?>",
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
                        'studentAutoID': stuID
                    },
                    url: "<?php echo site_url('school/Student/delete_student'); ?>",
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

    function load_year(select_val) {
        $('#admitted_year').val("");
        $('#admitted_year option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_year"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#admitted_year').empty();
                    var mySelect = $('#admitted_year');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_student_code(select_val) {
        $('#student_code').val("");
        $('#student_code option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_student_code"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#student_code').empty();
                    var mySelect = $('#student_code');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_grade(select_val) {
        $('#grade').val("");
        $('#grade option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_grade"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#grade').empty();
                    var mySelect = $('#grade');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_group(select_val) {
        $('#group').val("");
        $('#group option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_group"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#group').empty();
                    var mySelect = $('#group');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_category(select_val) {
        $('#category').val("");
        $('#category option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_category"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#category').empty();
                    var mySelect = $('#category');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_sponsor(select_val) {
        $('#sponsor').val("");
        $('#sponsor option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_sponsor"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#sponsor').empty();
                    var mySelect = $('#sponsor');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_status(select_val) {
        $('#status').val("");
        $('#status option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_status"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#status').empty();
                    var mySelect = $('#status');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_required(select_val) {
        $('#required').val("");
        $('#required option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_required"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#required').empty();
                    var mySelect = $('#required');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function excelDownload() {
        var form = document.getElementById('filterForm');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#filterForm').serializeArray();
        form.action = '<?php echo site_url('school/Student/export_excel_studentmaster'); ?>';
        form.submit();
    }

    function clearSearchFilter() {
        $('#student_code').val("");
        $('#student_code').remove();
        $('#admitted_year').val("");
        $('#admitted_year').remove();
        $('#grade').val("");
        $('#grade').remove();
        $('#group').val("");
        $('#group').remove();
        $('#category').val("");
        $('#category').remove();
        $('#required').val("");
        $('#required').remove();
        $('#status').val("");
        $('#status').remove();
        $('#sponsor').val("");
        $('#sponsor').remove();

        $('#student_code').append($('<option>', {value: '', text: 'Student Code'}));
        $('#admitted_year').append($('<option>', {value: '', text: 'Select Year'}));
        $('#grade').append($('<option>', {value: '', text: 'Select Grade'}));
        $('#group').append($('<option>', {value: '', text: 'Group'}));
        $('#category').append($('<option>', {value: '', text: 'Category'}));
        $('#required').append($('<option>', {value: '', text: 'Select'}));
        $('#status').append($('<option>', {value: '', text: 'Select Status'}));
        $('#sponsor').append($('<option>', {value: '', text: 'Select Sponsor'}));
        Otable.draw();
    }
</script>