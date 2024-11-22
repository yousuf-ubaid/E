<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_ot_summary');
echo head_page($title, false);
$attendanceCycle=getPolicyValues('HACDAY','All');
?>

<div class="row">
  <div class="col-md-12">

    <input type="hidden" value="<?php echo $attendanceCycle ?>" id="attendanceCycle">
    <div class="col-md-12">
        <label for=""  class="col-md-1 control-label"><?php echo $this->lang->line('common_month');?></label>
        <div class="input-group">
        <input type="month" class="form-control" id="monthDate" name="monthDate" onchange="checkpolicy()">
        </div>
        <br>
    </div>

    <label for="inputCodforn" class="col-md-1 control-label"><?php echo $this->lang->line('common_from');?></label>
    <div class="col-md-2">
        <div class="input-group datepic">
            <input type="date" class="form-control" id="fromDate" name="fromDate">
        </div>
    </div>

    <label for="inputCodforn" class="col-md-1 control-label"><?php echo $this->lang->line('common_to');?></label>
    <div class="col-md-2">
        <div class="input-group datepic">
            <input type="date" class="form-control" id="toDate" name="toDate">
        </div>
    </div>

   <div class="row">
      <button style="margin-top: 5px" type="button" onclick="getSummary()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_load');?></button>
   </div>

  </div>
</div>
<hr>


<div aria-hidden="true" id="ot_summary_body" style="display: none;">
    <div class="row">
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 remove-margin">
            <div class="row">
                <div class="col-md-12">
                    <div class="" style="width: 100%">
                    <ul class="nav nav-tabs list" role="tablist">
                        <li role="presentation" class="master-nav-li">
                            <a href="#employment_tab" aria-controls="employment_tab" role="tab" data-toggle="tab" onclick="fetchOtSummary()">
                                <?php echo $this->lang->line('common_ot_summary'); ?>
                            </a>
                        </li>
                        <li role="presentation" class="master-nav-li">
                            <a href="#employment_tab" aria-controls="employment_tab" role="tab" data-toggle="tab" onclick="fetchOtSummaryDateWise()">
                                <?php echo $this->lang->line('common_ot_summary_day_wise'); ?>
                            </a>
                        </li>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- OT Summary Table -->
    <div aria-hidden="true" id="ot_detail_modal" style="display: none;">
    <div class="table-responsive" model="daiplay:hide">
        <table id="ot_summary_table" class="<?php echo table_class() ?>">
            <thead >
            <tr id="segmentCol">
                <th style="min-width: 4%"  rowspan="3">#</th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_employee_name'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_secondary_code'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('commom_employee_code'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_segment'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_amount'); ?></th>
            </tr>

            <tr id="segmendate">
            </tr>

            <tr id="segmenamt">
            </tr>
            
            </thead>
            <tbody id="table_body">
            </tbody>
        </table>
    </div>
    </div>

    <!-- OT Summary Date Wise -->
    <div aria-hidden="true" id="ot_date_wise_modal" style="display: none;">
    <div class="table-responsive" model="daiplay:hide">
        <table id="ot_summary_date_table" class="<?php echo table_class() ?>">
            <thead >
            <tr id="monthTr">
                <th style="min-width: 4%"  rowspan="3">#</th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_employee_name'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_secondary_code'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_amount'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_hours'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_rate'); ?></th>
                <th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_equivalent_hrs'); ?></th>
            </tr>

            <tr id="dateTr">
            </tr>
            
            </thead>
            <tbody id="date_wise_table_body">
            </tbody>
        </table>
    </div>
    </div>
</div>


 <script type="text/javascript">

    function checkpolicy()
     {
        var selectedMonth =$('#monthDate').val();
        var attendanceCycle=$("#attendanceCycle").val();
        
        if (!attendanceCycle || attendanceCycle == 0) {
            var startDate = new Date(selectedMonth + "-01");
            var endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0); 
            var sDate=formatDate(startDate);
            var eDate=formatDate(endDate);
            $('#fromDate').val(sDate);
            $('#toDate').val(eDate);
        } else {
            var endDate = new Date(selectedMonth + "-" + attendanceCycle);
            var startDate = new Date(endDate);
            startDate.setMonth(startDate.getMonth() - 1); 
            var sDate=formatDate(startDate);
            var eDate=formatDate(endDate);
            $('#fromDate').val(sDate);
            $('#toDate').val(eDate);
        }
    }

    function formatDate(date) {
        var year = date.getFullYear();
        var month = ("0" + (date.getMonth() + 1)).slice(-2); 
        var day = ("0" + date.getDate()).slice(-2);
        return year + "-" + month + "-" + day;
    }

    function getSummary() {
        $('#ot_detail_modal').hide();

        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var fromDateObj = new Date(fromDate); 
        var toDateObj = new Date(toDate);  

        if(fromDate =='' || toDate ==''){
        myAlert('e', 'Date can not be empty');
        return;
        }
        else if (fromDateObj >= toDateObj) {
        myAlert('e', '"From Date" should be older than "To date"');
        return;
        }
        
        $.ajax({
            url: '<?php echo site_url('Report/getOtSummary') ?>',
            type: 'POST',
            dataType: 'json',
            data: {'fromDate': fromDate, 'toDate': toDate},
            success: function(data) {

            
            if(data !='' ){
                $('#ot_detail_modal').show();

                $('#segmentCol').empty();
                $('#segmendate').empty();
                $('#segmenamt').empty();
                $('#segmentCol').append('<th style="min-width: 4%" rowspan="3">#</th>');
                $('#segmentCol').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_employee_name'); ?></th>');
                $('#segmentCol').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('commom_employee_code'); ?></th>');
                $('#segmentCol').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_secondary_code'); ?></th>');
                $('#segmentCol').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_segment'); ?></th>');
                $('#segmentCol').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_amount'); ?></th>');

                var segments = [];
                var segmentHeaders = [];

                if (Array.isArray(data)) {
                    data.forEach(function(row, index) {
                        var segmentDescription = row['description'];
                        segments.push(segmentDescription);

                        $('#segmentCol').append('<th style="min-width: 15%" colspan="2">' + segmentDescription + '</th>');
                        $('#segmendate').append('<th colspan="2">' + fromDate + ' - ' + toDate + '</th>');
                        $('#segmenamt').append('<th>Amt</th><th>Hrs</th>');
                    });
                }
                $('#ot_summary_body').show();
                getOtDetails(fromDate, toDate, segments);
            }
            else{
                myAlert('e','No data found');
                $('#ot_summary_body').hide();
            }

                
            },
            error: function() {
            myAlert('e','Error occurred while fetching data.');
            }
        });
    }

    function getOtDetails(fromDate, toDate, segments) {
        $.ajax({
            url: '<?php echo site_url('Report/getOtDetails') ?>',
            type: 'POST',
            dataType: 'json',
            data: {'fromDate': fromDate, 'toDate': toDate},
            success: function(data) {
                $('#table_body').empty();
                var x=1;

                if (Array.isArray(data)) {
                    var employeeData = {};

                    data.forEach(function(row) {
                        var empID = row['empID'];
                        var description = row['description'];
                        

                        if (!employeeData[empID]) {
                            employeeData[empID] = {
                                empID: empID,
                                Ename2: row['Ename2'],
                                EmpSecondaryCode: row['EmpSecondaryCode'],
                                ECode: row['ECode'],
                                segment:row['description'],
                                amount: row['totalPaymentOT'],
                                totals: {}
                            };

                            segments.forEach(function(segment) {
                                employeeData[empID].totals[segment] = {
                                    totalPaymentOT: 0,
                                    totalOTHours: 0
                                };
                            });
                        }

                        if (employeeData[empID].totals[description]) {
                            employeeData[empID].totals[description].totalPaymentOT += parseFloat(row['totalPaymentOT']);
                            employeeData[empID].totals[description].totalOTHours += parseFloat(row['totalOTHours']);
                        }
                    });

                    Object.values(employeeData).forEach(function(emp) {
                        var tablerow = '<tr>' +
                            '<td style="min-width: 15%">' + x+ '</td>' +
                            '<td style="min-width: 15%">' + emp.Ename2 + '</td>' +
                            '<td style="min-width: 15%">' + emp.ECode + '</td>'+
                            '<td style="min-width: 15%">' + emp.EmpSecondaryCode + '</td>'+
                            '<td style="min-width: 15%">' + emp.segment + '</td>'+
                            '<td style="min-width: 15%; text-align:right;">' + emp.amount + '</td>' ;

                        segments.forEach(function(segment) {
                            if (emp.totals[segment]) {
                                tablerow += '<td style="min-width: 15%; text-align:right;">' + (emp.totals[segment].totalPaymentOT || '0') + '</td>' +
                                    '<td style="min-width: 15%; text-align:right;">' + (emp.totals[segment].totalOTHours || '0') + '</td>';
                            } else {
                                tablerow += '<td style="min-width: 15%"></td>' +
                                            '<td style="min-width: 15%"></td>';
                            }
                        });

                        tablerow += '</tr>';
                        $('#table_body').append(tablerow);
                        x++;
                    });
                }
                otDateWise(fromDate, toDate);
                fetchOtSummary();
            },
            error: function() {
            myAlert('e','Error occurred while fetching data.');
            }
        });
    }

    function fetchOtSummary(){
        $('#ot_detail_modal').show();
        $('#ot_date_wise_modal').hide();
    }

    function fetchOtSummaryDateWise(){
        $('#ot_detail_modal').hide();
        $('#ot_date_wise_modal').show();
    }

    function otDateWise(fromDate, toDate) {
        $.ajax({
            url: '<?php echo site_url('Report/getOtDetailsDateWise') ?>',
            type: 'POST',
            dataType: 'json',
            data: {'fromDate': fromDate, 'toDate': toDate},
            success: function(data) {
                $('#date_wise_table_body').empty();
                if (!data || data.length === 0) {
                    var row = '<td colspan="5">No data found yet :(</td>';
                    $("#date_wise_table_body").append(row);
                    return;
                }

                var fromDateObj = new Date(fromDate);
                var toDateObj = new Date(toDate);

                $('#monthTr').empty();
                $('#dateTr').empty();

                $('#monthTr').append('<th style="min-width: 4%" rowspan="3">#</th>');
                $('#monthTr').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_employee_name'); ?></th>');
                $('#monthTr').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_secondary_code'); ?></th>');
                // $('#monthTr').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_amount'); ?></th>');
                // $('#monthTr').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_total_hours'); ?></th>');
                $('#monthTr').append('<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_ot_type') ?></th>'); 

                var currentMonth = fromDateObj.getMonth();
                var currentYear = fromDateObj.getFullYear();
                var dayCount = 0;
                var monthHtml = '';
                var dateHtml = '';
                var dateHeaders = [];
                var x=1;

                while (fromDateObj <= toDateObj) {
                    var monthName = fromDateObj.toLocaleString('default', { month: 'long' });
                    var year = fromDateObj.getFullYear();

                    if (currentMonth === fromDateObj.getMonth() && currentYear === year) {
                        dayCount++;
                        dateHtml += '<th>' + fromDateObj.getDate() + '</th>';
                        dateHeaders.push(fromDateObj.toISOString().split('T')[0]);
                    } else {
                        var prevMonthName = new Date(fromDateObj.getFullYear(), fromDateObj.getMonth() - 1).toLocaleString('default', { month: 'long' });
                        var prevYear = fromDateObj.getMonth() === 0 ? fromDateObj.getFullYear() - 1 : fromDateObj.getFullYear();
                        monthHtml += '<th colspan="' + dayCount + '" style="min-width: 15%">' + prevMonthName + ' ' + prevYear + '</th>';

                        currentMonth = fromDateObj.getMonth();
                        currentYear = year;
                        dayCount = 1;

                        dateHtml += '<th>' + fromDateObj.getDate() + '</th>';
                        dateHeaders.push(fromDateObj.toISOString().split('T')[0]);
                    }

                    fromDateObj.setDate(fromDateObj.getDate() + 1);
                }

                if (dayCount > 0) {
                    monthHtml += '<th colspan="' + dayCount + '" style="min-width: 15%">' + monthName + ' ' + currentYear + '</th>';
                }
                monthHtml += '<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_equivalent_hrs') ?></th>';
                monthHtml += '<th style="min-width: 10%" rowspan="3">Total Hours</th>';
                monthHtml += '<th style="min-width: 10%" rowspan="3">Total Amount</th>';
                monthHtml += '<th style="min-width: 15%" rowspan="3"><?php echo $this->lang->line('common_rate') ?></th>';

                $('#monthTr').append(monthHtml);
                $('#dateTr').append(dateHtml);

                var empData = {};

                data.forEach(function (item) {
                    if (!empData[item.empID]) {
                        empData[item.empID] = {
                            empID: item.empID,
                            Ename2: item.Ename2,
                            ECode:item.ECode,
                            totalPaymentOT: 0,
                            totalOTHours:item.totalOTHours,
                            normalDayOT: {},
                            weekendOT: {},
                            holidayOT: {}
                        };
                    }

                    if (item.totalNormalDayOT > 0) {
                        empData[item.empID].normalDayOT[item.attendanceDate] = item.totalNormalDayOT;
                    }
                    if (item.totalWeekendOT > 0) {
                        empData[item.empID].weekendOT[item.attendanceDate] = item.totalWeekendOT;
                    }
                    if (item.totalHolidayOT > 0) {
                        empData[item.empID].holidayOT[item.attendanceDate] = item.totalHolidayOT;
                    }
                    empData[item.empID].totalPaymentOT += parseFloat(item.totalPaymentOT);
                });

                Object.keys(empData).forEach(function (empID) {
                    var emp = empData[empID];
                    var totalOTHours = 0; 
                    var normalEquivalent = 0;
                    var weekendEquivalent = 0;

                    dateHeaders.forEach(function (date) {
                        var normalOT = emp.normalDayOT[date] || 0;
                        var weekendOT = emp.weekendOT[date] || 0;
                        var holidayOT = emp.holidayOT[date] || 0;

                        totalOTHours += normalOT + weekendOT + holidayOT;
                        normalEquivalent += normalOT * 1.25;
                        weekendEquivalent += weekendOT * 1.5;
                    });

                   var  rate=totalOTHours/emp.totalPaymentOT;


                    //  Normal Day OT
                    var rowHtml = '<tr>';
                    rowHtml += '<td rowspan="3">' +x+ '</td>';  
                    rowHtml += '<td rowspan="3">' + emp.Ename2 + '</td>';
                    rowHtml += '<td rowspan="3">' + emp.ECode + '</td>';
                   
                    rowHtml += '<td><?php echo $this->lang->line('common_normal_ot') ?></td>';
                    dateHeaders.forEach(function (date) {
                        rowHtml += '<td>' + (emp.normalDayOT[date] || '') + '</td>';
                    });
                    rowHtml += '<td>' + normalEquivalent.toFixed(2)  + '</td>';
                    rowHtml += '<td rowspan="3" style="text-align:right">' +  totalOTHours.toFixed(2) + '</td>';
                    rowHtml += '<td rowspan="3" style="text-align:right">' + emp.totalPaymentOT.toFixed(2) + '</td>';
                    rowHtml += '<td rowspan="3" style="text-align:right">' + rate.toFixed(2) + '</td>';
                    rowHtml += '</tr>';
                    $('#date_wise_table_body').append(rowHtml);

                    // Weekend OT
                    rowHtml = '<tr>';
                    rowHtml += '<td><?php echo $this->lang->line('common_weekend_ot') ?></td>';  
                    dateHeaders.forEach(function (date) {
                        rowHtml += '<td>' + (emp.weekendOT[date] || '') + '</td>';
                    });
                    rowHtml += '<td>' + weekendEquivalent.toFixed(2)  + '</td>';
                    rowHtml += '</tr>';
                    $('#date_wise_table_body').append(rowHtml);

                    // Holiday OT
                    rowHtml = '<tr>';
                    rowHtml += '<td><?php echo $this->lang->line('common_holiday_ot') ?></td>';
                    dateHeaders.forEach(function (date) {
                        rowHtml += '<td>' + (emp.holidayOT[date] || '') + '</td>';
                    });             
                    rowHtml += '<td>-</td>';       
                    rowHtml += '</tr>';
                    
                    $('#date_wise_table_body').append(rowHtml);
                    x++;
                });
            },
            error: function() {
                myAlert('e', 'Error occurred while fetching data.');
            }
        });
    }


</script>
