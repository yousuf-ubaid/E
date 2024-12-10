<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title =$this->lang->line('hrms_leave_management_leave_plan');
echo head_page($title, FALSE);
$empID = current_userID();

//

$leaveType = '';
$filter = '';

if(is_array($data_arr)){

    $leaveType = $data_arr['leaveType'];
    $filter = isset($data_arr['filterType']) ? $data_arr['filterType'] : '';
}

$leavePlanData = fetch_leavePlan($empID,$leaveType,$filter);

if( !empty($leavePlanData)) {
    $leavePlanData = array_group_by($leavePlanData, 'empID');

    $planArr = [];
    foreach ($leavePlanData as $row) {
        $parentID = $row[0]['id'];

        foreach ($row as $key => $emp) {
            /*if($key == 0 ){
                $emp1 = $emp;
                $emp1['id'] = $emp['id'].'01';
                $emp1['duration'] = 0;
                $emp1['parent'] = 0;
                array_push($planArr, $emp1);
            }*/
            // $emp['parent'] = $parentID;

            $emp['parent'] = ($key != 0) ? $parentID : 0;
            if ($key > 0) {
                $emp['text'] = $emp['documentCode'];
            }
            array_push($planArr, $emp);
        }
    }
    // echo '<pre>'; print_r($leavePlanData); echo '</pre>';
    $leavePlanData = $planArr;


}

// $status = array(
//     ''=> 'Select',
//     '1'=> 'Draft',
//     '2'=> 'Planned',
//     '3'=> 'Confirmed',
//     '4'=> 'Approved'
// );
?>


<script type="text/javascript" src="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.js'); ?>"></script>
<link href="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.css'); ?>" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url('plugins/dhtmlxGantt/codebase/ext/dhtmlxgantt_tooltip.js'); ?>"></script>


<style>
    .child_preview{
        box-sizing: border-box;
        margin-top: 2px;
        position: absolute;
        z-index: 1;
        color: white;
        text-align: center;
        font-size: 12px;
    }

    .gantt_task_line.task-collapsed{
        height: 4px;
        opacity: 0.25;
    }

    .gantt_task_line.gantt_project.task-collapsed .gantt_task_content{ display: none; }

    .gantt_row.task-parent{ font-weight: bold; }

    .gantt_task_line{ color: rgba(255, 255, 255, 0) !important; }

    .approved-cls{ background: #166123 !important; }

    .confirmed-cls{ background: #13f358 !important; }

    .draft-cls{ background: #61cde2 !important; }

    .plan-cls{ background: #fda70a !important; }

 
    /* .checkbox-inline input[type="checkbox"] {
        display: none;
    }

   
    .checkbox-inline {
        cursor: pointer;
        margin-right: 10px; 
    }

    .checkbox-inline span {
        display: inline-block;
        width: 20px; 
        height: 20px; 
        border-radius: 2px;
        border: 1px solid #ccc;
        vertical-align: middle;
        margin-right: 5px; 
    } */

    #filterApproved {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 3px solid darkgreen;
        border-radius: 3px;
        background-color: white;
        cursor: pointer;
    }

    #filterApproved:checked {
        background-color: darkgreen;
        border-color: darkgreen;
    }
    #filterConfirmed {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 3px solid #13f358;
        border-radius: 3px;
        background-color: white;
        cursor: pointer;
    }
    #filterConfirmed:checked {
        background-color: #13f358;
        border-color: #13f358;
    }
    #filterDraft {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 3px solid #61cde2;
        border-radius: 3px;
        background-color: white;
        cursor: pointer;
    }
    #filterDraft:checked {
        background-color: #61cde2;
        border-color: #61cde2;
    }
    #filterPlanned {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 3px solid #fda70a;
        border-radius: 3px;
        background-color: white;
        cursor: pointer;
    }
    #filterPlanned:checked {
        background-color: #fda70a;
        border-color: #fda70a;
    }
</style>

<div class="row" style="margin-bottom: 3px">
    <div class="col-xs-4 col-sm-2">
        <label for="leaveType">
            <?php echo $this->lang->line('hrms_leave_management_please_select__a_type'); ?><!--Leave Type--></label>
    </div>
    <div class="col-xs-7 col-sm-4" id="leaveTypeDropDown">
        <?php echo form_dropdown('leaveType', leavemaster_dropdown(false,false), $leaveType, 'id="leaveType" class="form-control select2" onchange="change_leave_type(this)"'); ?>
    </div>

    <!-- <div class="col-xs-4 col-sm-2 text-right">
        <label for="status">Status</label>
    </div>
    <div class="col-xs-7 col-sm-4" id="statusDropDown">
        <?php //echo form_dropdown('status', $status, '', 'id="status" class="form-control select2" onchange="change_status_type(this)"'); ?>
    </div> -->
</div>


<div class="row">
    <div class="panel-body">
      
        <div class="well" style="padding: 10px; margin-bottom: 0px">
            <label for="scale1" class="radio-inline"><input type="radio" id="scale1" name="scale" value="1" /><strong><?php echo $this->lang->line('hrms_leave_management_day_scale');?></strong></label><!--Day scale-->
            <label for="scale2" class="radio-inline"><input type="radio" id="scale2" name="scale" value="2" checked/><strong><?php echo $this->lang->line('hrms_leave_management_week_scale');?><strong></strong></label><!--Week scale-->
            <label for="scale3" class="radio-inline"><input type="radio" id="scale3" name="scale" value="3" /><strong><?php echo $this->lang->line('hrms_leave_management_month_scale');?></strong></label><!--Month scale-->
            <button onclick="print_leave_plan_report()">Print PDF</button>
            <div class="pull-right">
                <span>
                    <input type="checkbox" class="filterCheckbox" id="filterApproved" name="filterApproved" onchange="filterLeaves(1)" value="approved" <?php echo (is_array($filter) && in_array('approved',$filter)) ? 'checked' : ''?> >
                    <label for="filterApproved" style="padding-bottom:10px;"><strong><?php echo $this->lang->line('common_approved'); ?></strong></label>
                </span>&nbsp;&nbsp;
                <span>
                    <input type="checkbox" class="filterCheckbox" id="filterConfirmed" name="filterConfirmed" onchange="filterLeaves(2)" value="confirmed" <?php echo (is_array($filter) && in_array('confirmed',$filter)) ? 'checked' : ''?>>
                    <label for="filterConfirmed" style="padding-bottom:10px;"><strong><?php echo $this->lang->line('common_confirmed'); ?></strong></label>
                </span>&nbsp;&nbsp;
                <span>
                    <input type="checkbox" class="filterCheckbox" id="filterDraft" name="filterDraft" onchange="filterLeaves(3)" value="draft" <?php echo (is_array($filter) && in_array('draft',$filter)) ? 'checked' : ''?>>
                    <label for="filterDraft" style="padding-bottom:10px;"><strong><?php echo $this->lang->line('common_draft'); ?></strong></label>
                </span>&nbsp;&nbsp;
                <span>
                    <input type="checkbox" class="filterCheckbox" id="filterPlanned" name="filterPlanned" onchange="filterLeaves(4)" value="planned" <?php echo (is_array($filter) && in_array('planned',$filter)) ? 'checked' : ''?>> 
                    <label for="filterPlanned" style="padding-bottom:10px;"><strong><?php echo $this->lang->line('hrms_leave_management_planned'); ?></strong></label>
                </span>&nbsp;&nbsp;
            </div>


        </div>
    </div>
    <div class="panel-body" style="height: 420px;">
        <div id="leave-plan-gant" style="width:100%; height:100%;"></div>
    </div>
</div>

<script>
    //const checkboxes = document.querySelectorAll('.checkbox-inline input[type="checkbox"]');
    
    // checkboxes.forEach((checkbox) => {
    //     checkbox.addEventListener('click', function() {
    //         // Uncheck all other checkboxes
    //         checkboxes.forEach((cb) => {
    //             if (cb !== this) {
    //                 cb.checked = false;
    //             }
    //         });
    //     });
    // });
    $(document).ready(function(){

        var filterArr = {};

        $('.headerclose').click(function(){
            fetchPage('system/hrm/report/profile-leave-plan','Test','HRMS');
        });

        // $('#filterApproved, #filterConfirmed, #filterDraft, #filterPlanned').change(function() {
        //     applyFilters();
        // });

        loadGant();

         // Bind click event to the print button
        $('#printGanttBtn').click(function() {
            printGantt();
        });
    });

    
    function filterLeaves(val){

        var selectedValues = [];

        $('.filterCheckbox:checked').each(function() {
            selectedValues.push($(this).val());
        });

        var leaveType = $('#leaveType').val();

        var leave_arr = {'leaveType':leaveType,'filterType':selectedValues};

        fetchPage('system/hrm/report/profile-leave-plan','Test','HRMS','',leave_arr);

    }

  

    function print_leave_plan_report(header_id){       
        header_id = 115;
        
        var leaveType = $('#leaveType').val();

        var selectedValues = [];
        $('.filterCheckbox:checked').each(function() {
            selectedValues.push($(this).val());
        });

        var filter = encodeURIComponent(JSON.stringify(selectedValues));
        
        // window.open("<?php //echo site_url('Jobs/load_daily_job_report_print') ?>");
        window.open("<?php echo site_url('Employee/load_leave_plan_report') ?>"+'/'+header_id+'/'+leaveType+'/'+filter+'/Leave Plan');
    }

    function change_leave_type(ev){

        var leaveType = $(ev).val();

        var selectedValues = [];

        $('.filterCheckbox:checked').each(function() {
            selectedValues.push($(this).val());
        });


        var leave_arr = {'leaveType':leaveType,'filterType':selectedValues};

        fetchPage('system/hrm/report/profile-leave-plan','Test','HRMS','',leave_arr);

    }

    function printGantt() {
        var printContents = document.getElementById('leave-plan-gant').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function applyFilters() {
        // Get the filter values
        var approved = $('#filterApproved').is(':checked');
        var confirmed = $('#filterConfirmed').is(':checked');
        var draft = $('#filterDraft').is(':checked');
        var planned = $('#filterPlanned').is(':checked');

        // Get the leave plan data
        var leavePlanData = <?php echo json_encode($leavePlanData); ?>;

        // If no filters are selected, show all data
        if (!approved && !confirmed && !draft && !planned) {
            gantt.clearAll();
            gantt.parse({"data": leavePlanData});
            return;
        }

        // Filter the data based on the selected filters
        var filteredData = leavePlanData.filter(function(task) {

            return (approved && task.approvedYN == 1) ||
                (confirmed && task.confirmedYN == 1 && task.approvedYN == 0) ||
                (draft && task.applicationType == 1 && task.approvedYN == 0) ||
                (planned && task.applicationType == 2);
        });


        // Update the Gantt chart with the filtered data
        gantt.clearAll();
        gantt.parse({"data": filteredData});
        return;
    }


    function setScaleConfig(value) {
        switch (value) {
            case "1":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.subscales = [{unit: "day", step: 1, date: "%d, %M"}];
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 60;
                gantt.templates.date_scale = null;
                break;
            case "2":
                var weekScaleTemplate = function (date) {
                    var dateToStr = gantt.date.date_to_str("%d %M, %Y");
                    var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                    return dateToStr(date) + " - " + dateToStr(endDate);
                };
                gantt.config.scale_unit = "year";
                gantt.config.date_scale = "%Y";
                gantt.config.step = 1;
                gantt.config.subscales = [
                    {unit: "week", step: 1, date: "%d, %M"}
                ];
                gantt.config.scale_height = 50;
                gantt.config.min_column_width = 60;
                break;
            case "3":
                gantt.config.scale_unit = "year";
                gantt.config.step = 1;
                gantt.config.date_scale = "%Y";
                gantt.config.min_column_width = 50;

                gantt.config.scale_height = 50;
                gantt.templates.date_scale = null;


                gantt.config.subscales = [
                    {unit: "month", step: 1, date: "%M"}
                ];
                break;
        }
    }
    function loadGant() {
    setTimeout(function () {
        gantt.config.readonly = true;
        gantt.config.row_height = 24;
        gantt.config.scale_height = 50;
        gantt.config.details_on_dblclick = false;

        gantt.templates.tooltip_text = function (start, end, task) {
            var applicationType = parseInt(task.applicationType);
            var statusText = '';
            switch (applicationType){
                case 1:
                    if(parseInt(task.approvedYN) == 1){ statusText = "<div style='text-align: left'><b><?php echo $this->lang->line('common_status');?>:</b><?php echo $this->lang->line('common_approved');?>  </div>"; }/*Approved*/
                    else if(parseInt(task.confirmedYN) == 1){ statusText = "<div style='text-align: left'><b><?php echo $this->lang->line('common_status');?>:</b><?php echo $this->lang->line('common_confirmed');?>  </div>"; }/*Confirmed*/
                    else{ statusText = "<div style='text-align: left'><b><?php echo $this->lang->line('common_status');?>:</b><?php echo $this->lang->line('common_draft');?> </div>";}/*Draft*/
                    break;

                case 2:
                    statusText = "<div style='text-align: left'><b><?php echo $this->lang->line('common_status');?>:</b><?php echo $this->lang->line('hrms_leave_management_planned');?>  </div>";/*Planned*/
                    break;

                default:
                    statusText = "<div style='text-align: left'><b><?php echo $this->lang->line('common_status');?>:</b> <?php echo $this->lang->line('common_draft');?> </div>";/*Draft*/
            }
            return "<div style='text-align: left;margin-bottom: 0px'><b><?php echo $this->lang->line('common_document');?>:</b> " + task.documentCode + "</div>" +
                "<div style='text-align: left'><b><?php echo $this->lang->line('common_start_date');?>:</b> " + gantt.templates.tooltip_date_format(start) + "</div>" +
                "<div style='text-align: left'><b><?php echo $this->lang->line('common_end_date');?>:</b> " + task.endDate2 + "</div>" +
                "<div style='text-align: left'><b><?php echo $this->lang->line('common_type');?>:</b> " + task.typeText + "</div>" +
                ""+ statusText +
                "<div style='text-align: left'><b><?php echo $this->lang->line('common_comments');?>:</b> " + task.levComment + "</div>";
        };

        gantt.templates.task_text = function(start, end, task){
            return " ";
        };

        gantt.templates.task_class=function(start, end, task){
            return "child_preview";
        };

        gantt.templates.grid_folder = function(item) {
            var icon;
            if(item.assigned){
                icon = (item.$open ? "openAssigned.gif" : "closedAssigned.gif")
            }
            else{
                icon = (item.$open ? "open.gif" : "closed.gif")
            }
            return "<div class='gantt_tree_icon'><span class='glyphicon glyphicon-user'></span></div>";
        };

        gantt.config.columns = [
            {name: "text", label: "Employee", align: "left", tree: true, width: 200}
        ];

        setScaleConfig('2');
        gantt.init("leave-plan-gant");

        function createBox(sizes, class_name){
            var box = document.createElement('div');
            box.style.cssText = [
                "height:" + sizes.height + "px",
                "line-height:" + sizes.height + "px",
                "width:" + sizes.width + "px",
                "top:" + sizes.top + 'px',
                "left:" + sizes.left + "px",
                "position:absolute"
            ].join(";");
            box.className = class_name;
            return box;
        }

        gantt.templates.grid_row_class = gantt.templates.task_class=function(start, end, task){
            var css = [];
            if(gantt.hasChild(task.id)){
                //css.push("task-parent");
            }
            if (!task.$open && gantt.hasChild(task.id)) {
                //css.push("task-collapsed");
            }

            return css.join(" ");
        };

        gantt.addTaskLayer(function show_hidden(task) {
            if (!task.$open && gantt.hasChild(task.id)) {
                var sub_height = gantt.config.row_height - 5,
                    el = document.createElement('div'),
                    sizes = gantt.getTaskPosition(task);

                var sub_tasks = gantt.getChildren(task.id);
                var child_el;

                for (var i = 0; i < sub_tasks.length; i++){
                    var child = gantt.getTask(sub_tasks[i]);
                    var child_sizes = gantt.getTaskPosition(child);
                    var applicationType = parseInt(child.applicationType);

                    var subTaskColor = '';
                    switch (applicationType){
                        case 1:
                            if(parseInt(child.approvedYN) == 1){ subTaskColor = 'approved-cls'; }
                            else if(parseInt(child.confirmedYN) == 1){ subTaskColor = 'confirmed-cls'; }
                            else{ subTaskColor = 'draft-cls';}
                            break;

                        case 2:
                            subTaskColor = 'plan-cls';
                            break;

                        default:
                            subTaskColor = 'draft-cls';
                    }

                    child_el = createBox({
                        height: sub_height,
                        top:sizes.top,
                        left:child_sizes.left,
                        width: child_sizes.width
                    }, "child_preview gantt_task_line "+subTaskColor);
                    child_el.innerHTML =  child.text;
                    el.appendChild(child_el);
                }
                return el;
            }
            return false;
        });

        function printGantt() {
            gantt.exportToPDF({
                name: "gantt.pdf",
                header: "Leave Plan",
                footer: "Generated by DHTMLX Gantt"
            });
        }

        var func = function (e) {
            e = e || window.event;
            var el = e.target || e.srcElement;
            var value = el.value;
            setScaleConfig(value);
            gantt.render();
        };

        var els = document.getElementsByName("scale");
        for (var i = 0; i < els.length; i++) {
            els[i].onclick = func;
        }

        // Function to filter tasks based on selected checkboxes
        // function applyFilters() {
        //     var filterApproved = document.getElementById('filterApproved').checked;
        //     var filterConfirmed = document.getElementById('filterConfirmed').checked;
        //     var filterDraft = document.getElementById('filterDraft').checked;
        //     var filterPlanned = document.getElementById('filterPlanned').checked;

        //     gantt.eachTask(function (task) {
        //         var applicationType = parseInt(task.applicationType);

        //         // Determine if the task should be shown based on selected filters
        //         var showTask = true;
        //         if (filterApproved && parseInt(task.approvedYN) !== 1) {
        //             showTask = false;
        //         }
        //         if (filterConfirmed && parseInt(task.confirmedYN) !== 1) {
        //             showTask = false;
        //         }
        //         if (filterDraft && parseInt(task.approvedYN) === 1) {
        //             showTask = false;
        //         }
        //         if (filterPlanned && applicationType !== 2) {
        //             showTask = false;
        //         }

        //         if (showTask) {
        //             gantt.showTask(task.id);
        //         } else {
        //             gantt.hideTask(task.id);
        //         }
        //     });

        //     gantt.render();
        // }

        //Apply filters on checkbox change
        // document.getElementById('filterApproved').addEventListener('change', applyFilters);
        // document.getElementById('filterConfirmed').addEventListener('change', applyFilters);
        // document.getElementById('filterDraft').addEventListener('change', applyFilters);
        // document.getElementById('filterPlanned').addEventListener('change', applyFilters);

        // Initially apply filters to show all tasks
        // applyFilters();

        // Ensure the JSON data is passed correctly

     
        var demo_tasks = { "data": <?php echo json_encode($leavePlanData); ?> };

        gantt.parse(demo_tasks);
        
    }, 100);
    }
</script>

<?php
