<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<style>
    .customPad {
        padding: 3px 0px;
    }

    /*.al {
        text-align: left !important;
    }*/

    .ar {
        text-align: right !important;
    }

    .alin {
        text-align: center !important;
    }

    .filter_uc {
        display: inline-block;
        margin: 0 10px;
    }
</style>


<div class="box">
    <div class="box-header with-border" id="box-header-with-border">
        <i class="fa fa-arrow-left back"></i> <h3 class="box-title" id="box-header-title">Succession Plan Report</h3>
        <div class="box-tools pull-right">
            <button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button id="" class="btn btn-box-tool headerclose navdisabl" type="button"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding: 25px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="filter_uc">
                    <label class="" for="">Year</label>
                    <div style="    display: inline-block;">
                    <input class="form-control" type="text" id="segment_year"/>
                    </div>
                </div>
                <div class="filter_uc">
                    <label class="" for="">Segment</label>
                    <select class=" filters" multiple required name="segment_dropdown[]" id="segment_dropdown"
                            onchange="loadEmp()">

                    </select>
                </div>
                <div class="filter_uc">
                    <label class="" for="">Employee</label>
                    <div id="emp_dropdown_div" style="display: inline-block;">
                        <select class=" filters" multiple required name="emp_dropdown[]" id="emp_dropdown">
                        </select>
                    </div>
                </div>
                <div class="filter_uc">
                    <input class="btn btn-primary" type="button" value="Generate Report" onclick="get_sp_report()">
                </div>
            </div>

        </div>
        <hr>
        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2">
<!--                <button type="button" id="btn_print_report" class="btn btn-default btn-xs" onclick="print_sp_report()">-->
<!--                    <i class="fa fa-print"></i> Print </button>-->

                <button type="button" class="btn btn-default btn-xs" onclick="download_pdf()">
                    <i class="fa fa-file-pdf-o"></i> PDF<!--Print--> </button>

                <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Succession_Plan_Report.xls" onclick="var file = tableToExcel('report_table', 'Succession Plan Report'); $(this).attr('href', file);">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                </a>
            </div>
        </div>
        <div class="row">
            <div id="report_div">
                <div style="text-align: center;font-size: 17px;font-weight: 700;">Succession Plan Report</div>
                <table class="table table-bordered table-striped table-condensed table-row-select" id="report_table">
                    <thead id="thead">

                    </thead>
                    <tbody id="tbody">

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<div id="editor"></div>
<script type="text/javascript" src="/gs_sme/plugins/printJS/jQuery.print.js"></script>
<script>

    $(".headerclose").click(function () {
        fetchPage('system/hrm/succession_planning','0','HRMS');
    });

    $(".back").click(function () {
        fetchPage('system/hrm/succession_planning','0','HRMS');
    });

    app.pagestate = 0;

    $(document).ready(function () {

        $('#segment_year').datepicker({
            minViewMode: 2,
            format: 'yyyy'
        });

        let today = new Date();
        let this_year = today.getFullYear();
        $('#segment_year').val(this_year);

        $('#segment_year').keypress(function (e) {
            e.preventDefault();
        });

        let segment_id = localStorage.getItem('segment_id');
        get_sp_report_headers();
        load_segment_dropdown();
        $("#segment_dropdown").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Segment',
            includeSelectAllOption: true,
            maxHeight: 400
        });

        $("#segment_dropdown").multiselect2('selectAll', false);
        $("#segment_dropdown").multiselect2('updateButtonText');
        $("#segment_dropdown").trigger('change');

        $("#emp_dropdown").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });




    });

    function get_sp_report() {
        let segment_id = $('#segment_dropdown').val();
        let emp_id = $('#emp_dropdown').val();
        let year = $('#segment_year').val();

        if(segment_id==null || emp_id==null){
            myAlert('w', 'Please set all filters');
        }else{
            $.ajax({
                type: 'post',
                dataType: 'html',
                data: {'segment_id': segment_id, 'emp_id': emp_id,'year':year},
                url: '<?php echo site_url('Employee/get_sp_report') ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#tbody").html(data);
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }

    }

    function download_pdf() {
        var doc = new jsPDF('l');
        doc.autoTable({ html: '#report_table' });
        doc.save('Succession_Plan_Report.pdf');
    }

    function get_sp_report_headers() {
        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {},
            url: '<?php echo site_url('Employee/get_sp_report_headers') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#thead").html(data);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_segment_dropdown() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {},
            url: '<?php echo site_url('Employee/get_segments_by_company') ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let options = "";
                data.map(function (item, index) {
                    options += '<option value="' + item.segmentID + '" onchange="">' + item.description + '</option>';
                });
                $("#segment_dropdown").html(options);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function loadEmp() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Employee/get_emp_by_segment_list'); ?>",
            data: {segmentID: $('#segment_dropdown').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                $("#emp_dropdown_div").html(data);
                $("#emp_dropdown").multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: 'Search Cashier',
                    includeSelectAllOption: true,
                    maxHeight: 400
                });

                $("#emp_dropdown").multiselect2('selectAll', false);
                $("#emp_dropdown").multiselect2('updateButtonText');

                if(app.pagestate==0){
                    get_sp_report();
                    app.pagestate=1;
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function print_sp_report() {
        $.print("#report_div");
    }
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.4/jspdf.plugin.autotable.min.js"></script>
