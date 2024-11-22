<?php


?>

<link href=" <?php echo base_url('plugins/jsGantt/jsgantt.css'); ?>" rel="stylesheet" type="text/css"/>
<script src=" <?php echo base_url('plugins/jsGantt/jsgantt.js'); ?>" type="text/javascript"></script>

<div style="position:relative" class="gantt" id="GanttChartDIV">


</div>

<script type="text/javascript">

    $(document).ready(function () {
        setTimeout(function () {
            get_gantchart();
        }, 100);

    });

    function get_gantchart()
    {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    headerID: <?php echo $headerID?>

                },
                url: "<?php echo site_url('Boq/getallchart'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#GanttChartDIV').html('');
                    var g = new JSGantt.GanttChart(document.getElementById('GanttChartDIV'), 'day');
                    if (g.getDivId() != null && data != '') {
                        g.setCaptionType('Complete');
                        g.setQuarterColWidth(36);
                        g.setDateTaskDisplayFormat('day dd month yyyy');
                        g.setDayMajorDateDisplayFormat('mon yyyy - Week ww');
                        g.setWeekMinorDateDisplayFormat('dd mon');
                        g.setShowTaskInfoLink(1);
                        g.setShowEndWeekDate(0);
                        g.setUseSingleCell(10000);
                        g.setFormatArr('Day', 'Week', 'Month', 'Quarter');
                        for (i = 0; i < data.length; i++) {
                            group = 0;
                            relationship = '';
                            if(data[i].masterID==0){
                                group = 1;
                            }
                            employee= data[i].ename2;
                            /*if(data[i].count!=1){
                               employee= data[i].ename2+' etc.';
                            }*/
                            if(data[i].relatedtaskID!=''){
                                relationship = data[i].relationship

                            }

                            g.AddTaskItem(new JSGantt.TaskItem(data[i].projectPlannningID/*pid*/, data[i].description/*name*/, data[i].startDate/*startDate*/, data[i].endDate/*endDate*/, data[i].bgColor/*css*/, ''/*link*/, 'gmilestone'/*milestrone*/, employee /*resourceName*/, data[i].percentage/* completion percent*/, group/*group task*/, data[i].masterID/*parentID*/, 1 /*pOpen*/, relationship/*pDepend*/, data[i].note, data[i].note, g));
                        }
                        /*  g.AddTaskItem(new JSGantt.TaskItem(11, 'Chart Object', '2016-02-20', '2016-02-20', 'gmilestone', '', 1, 'Shlomy', 100, 0, 1, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(12, 'Task Objects', '', '', 'ggroupblack', '', 0, 'Shlomy', 40, 1, 1, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(121, 'Constructor Proc', '2016-02-21', '2016-03-09', 'gtaskblue', '', 0, 'Brian T.', 60, 0, 12, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(122, 'Task Variables', '2016-03-06', '2016-03-11', 'gtaskred', '', 0, 'Brian', 60, 0, 12, 1, 121, '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(123, 'Task by Minute/Hour', '2016-03-09', '2016-03-14 12:00', 'gtaskyellow', '', 0, 'Ilan', 60, 0, 12, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(124, 'Task Functions', '2016-03-09', '2016-03-29', 'gtaskred', '', 0, 'Anyone', 60, 0, 12, 1, '123FF', 'This is a caption', null, g));
                         g.AddTaskItem(new JSGantt.TaskItem(2, 'Create HTML Shell', '2016-03-24', '2016-03-24', 'gtaskyellow', '', 0, 'Brian', 20, 0, 0, 1, 122, '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(3, 'Code Javascript', '', '', 'ggroupblack', '', 0, 'Brian', 0, 1, 0, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(31, 'Define Variables', '2016-02-25', '2016-03-17', 'gtaskpurple', '', 0, 'Brian', 30, 0, 3, 1, '', 'Caption 1', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(32, 'Calculate Chart Size', '2016-03-15', '2016-03-24', 'gtaskgreen', '', 0, 'Shlomy', 40, 0, 3, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(33, 'Draw Task Items', '', '', 'ggroupblack', '', 0, 'Someone', 40, 2, 3, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(332, 'Task Label Table', '2016-03-06', '2016-03-09', 'gtaskblue', '', 0, 'Brian', 60, 0, 33, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(333, 'Task Scrolling Grid', '2016-03-11', '2016-03-20', 'gtaskblue', '', 0, 'Brian', 0, 0, 33, 1, '332', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(34, 'Draw Task Bars', '', '', 'ggroupblack', '', 0, 'Anybody', 60, 1, 3, 0, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(341, 'Loop each Task', '2016-03-26', '2016-04-11', 'gtaskred', '', 0, 'Brian', 60, 0, 34, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(342, 'Calculate Start/Stop', '2016-04-12', '2016-05-18', 'gtaskpink', '', 0, 'Brian', 60, 0, 34, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(343, 'Draw Task Div', '2016-05-13', '2016-05-17', 'gtaskred', '', 0, 'Brian', 60, 0, 34, 1, '', '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(344, 'Draw Completion Div', '2016-05-17', '2016-06-04', 'gtaskred', '', 0, 'Brian', 60, 0, 34, 1, "342,343", '', '', g));
                         g.AddTaskItem(new JSGantt.TaskItem(35, 'Make Updates', '2016-07-17', '2017-09-04', 'gtaskpurple', '', 0, 'Brian', 30, 0, 3, 1, '333', '', '', g));
                         g.Draw();*/
                        g.Draw();
                    }
                }, error: function () {

                }
            });
    }



</script>