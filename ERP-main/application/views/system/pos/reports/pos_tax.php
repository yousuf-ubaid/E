<?php
echo head_page('<i class="fa fa-archive"></i> Tax Detail Report', false);
// $locations = load_pos_location_drop();
$locations = load_pos_location_drop_with_status();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';print_r($locations);echo '<pre>';*/
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
    }
    .btn-excel:focus,.btn-excel:hover{
        color: white;
    }
    .btn-excel:hover{
       background-color: #2ea363!important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="col-md-2">
    <div class="form-group">
        <label class="col-md-4 control-label">Outlet</label>
        <div class="col-md-12">
            <select class="filters" multiple required name="outletID_f[]" id="outletID_f" onchange="loadCashier()">
                <?php
                foreach ($locations as $loc) {
                    echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . ' - ' . $loc['isActive'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="col-md-2">
    <div class="form-group">
        <label class="col-md-4 control-label"><?php echo $this->lang->line('common_from'); ?></label>
        <input autocomplete="off" type="text" required class="form-control input-sm startdateDatepic" name="filterFrom"
               id="filterFrom"
               value="<?php echo date('Y-01-01'); ?>">
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label class="col-md-4 control-label"><?php echo $this->lang->line('common_to'); ?></label>
        <input autocomplete="off" type="text" required class="form-control input-sm startdateDatepic"
               value="<?php echo date('Y-m-d') ?>" placeholder="To" name="filterTo" id="filterTo">
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label class="col-md-4 control-label">&nbsp;</label>
        <button class="form-control btn btn-primary" onclick="load_tax_details()">Generate Report</button>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
      <label>&nbsp;</label>
        <a href="" class="form-control btn btn-excel btn-sm" id="btn-excel" download="Tax_Detail_Report.xls"
           onclick="var file = tableToExcel('tax_details_table', 'Tax Detail Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
    </div>
</div>
</div>

<div style="padding: 10px;">
<hr>
<div id="tax_table_div" class="table-responsive" style="display: none;">
    <table id="tax_details_table" class="table table-bordered table-hover table-condensed">
        <thead>
        <tr></tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>

<div id="empty_table_message" class="reportContainer" style="min-height: 200px;overflow: auto;">
    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the
        Generate
        Report
    </div>
</div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>

    app = {};
    app.tax_details_table = null;
    $(document).ready(function () {

        $("#outletID_f").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "YYYY-MM-DD",
            sideBySide: false,
            widgetPositioning: {},
            keyBinds: {
                enter: function () {
                    this.hide();
                    load_tax_details();
                }
            }
        }).on('dp.change', function (ev) {
        });

        generate_table_header();

        // load_tax_details();
    });


    function generate_table_header() {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('Pos_restaurant/tax_detail_report_columns'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var table_header = '<th>#</th><th>Bill No</th><th>Outlet</th>';
                data.forEach(function (item, index) {
                    table_header += '<th>' + item.taxShortCode + '</th>';
                });
                table_header += '<th>Total</th>';
                $("#tax_details_table thead tr").html(table_header);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function load_tax_details() {
        $("#tax_table_div").show();
        $("#empty_table_message").hide();
        var start_date = $("#filterFrom").val();
        var end_date = $("#filterTo").val();
        var outlet_list = $('#outletID_f').val();
        outlet_list = outlet_list.join(",");
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {start_date: start_date, end_date: end_date, outlet_list: outlet_list},
            url: "<?php echo site_url('Pos_restaurant/tax_detail_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $("#tax_details_table tbody").html("");
                var all_total = 0;
                var column_total = [];
                var seq = 1;
                data.forEach(function (item, index) {
                    var row = [];
                    //converting object to array.
                    var item2 = Object.keys(item).map(function (key) {
                        //column_total[key]=0;
                        return {"index": key, "value": item[key]};
                    });
                    var bill_no = "";
                    var row_tax_total = 0;
                    row.unshift(seq);
                    seq++;
                    item2.forEach(function (item, index) {
                        if (item.index == 'bill_no') {
                            row.splice(1, 0, item.value);
                        } else if (item.index == 'outlet') {
                            row.splice(2, 0, item.value);
                        } else {

                            let item_value = parseFloat(item.value);
                            item_value = item_value.toFixed(2);
                            row.push(item_value);
                            row_tax_total += parseFloat(item_value);

                            //separately calculate and store column wise total
                            let existing_column_total = column_total[index];
                            if (isNaN(existing_column_total)) {
                                existing_column_total = 0;
                            }
                            existing_column_total = parseFloat(existing_column_total).toFixed(2);
                            column_total[index] = parseFloat(existing_column_total) + parseFloat(item_value);
                        }

                    });
                    row.push(row_tax_total.toFixed(2));
                    var table_row_html = "<tr>";
                    row.forEach(function (item, index) {
                        if(index>2){
                            table_row_html += '<td style="text-align: right;">' + item + '</td>';
                        }else{
                            table_row_html += '<td>' + item + '</td>';
                        }
                    });
                    table_row_html += "</tr>";
                    $("#tax_details_table tbody").append(table_row_html);
                    all_total += parseFloat(row_tax_total);
                });
                var table_row_html = "<tr><th>Total</th><th></th><th></th>";
                column_total.forEach(function(item,index){
                        table_row_html += '<th style="text-align: right;">'+item.toFixed(2)+'</th>';
                });
                table_row_html += '<th style="text-align: right;">'+all_total.toFixed(2)+'</th>';
                table_row_html+="</tr>";
                $("#tax_details_table tfoot").html("");
                $("#tax_details_table tfoot").append(table_row_html);

                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>



