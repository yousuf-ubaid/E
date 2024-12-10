<?php
echo head_page('<i class="fa fa-truck"></i>  Upcoming Orders', false);
//$locations = load_pos_location_drop();
$locations = load_pos_location_drop_with_status();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$date_format_policy = date_format_policy();
$newpolicy_date = strtoupper($date_format_policy);
$from_date = current_format_date();
$to_date = current_format_date();
$to_date .= " 11:59 PM";

$sort_by_drop = ['deliveryDate'=>'Date', 'posCustomerAutoID'=>'Customer'];
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">

<style>
    .content{ padding: 15px; }

    .btn-generate{
        color : #FFF;
        background-color : #005b8a;
        border : 0px solid #007FFF;
        font-weight : bold;
    }

    .btn-generate.active, .btn-generate.focus, .btn-generate:active, .btn-generate:focus, .btn-generate:hover {
        color: #FFF;
        background-color: #2080b1;
    }

    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-weight: bold !important;
        font-size: 14px;
        color: #6a6c6f;
    }
</style>
<div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">
        <?php echo form_open('', ' id="report_form" method="post" class="form-group" role="form" autocomplete="off"'); ?>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Filter</legend>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group col-sm-2">
                        <label class="" for="date_from">From </label>
                        <div class="input-group startdateDatepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="date_from" value="<?=$from_date?>"
                                   id="date_from" class="form-control dateFields frm_input"
                                   style="z-index 999999 !important">
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="date_to" for="">To </label>
                        <div class="input-group duedateDatepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="date_to" value="<?=$to_date?>"
                                   id="date_to" class="form-control dateFields frm_input" style="z-index: 100">
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="">Customer </label>
                        <br>
                        <?php echo form_dropdown('customers[]', get_all_pos_customers(true, true), '', 'class="form-control select2" id="customers"  multiple="" style="z-index: 0;"'); ?>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="" for="outletID_f">Outlet</label>
                        <br>
                        <select  name="outletID[]" id="outletID" class="form-control" multiple >
                            <?php
                            $locations = load_pos_location_drop();
                            foreach ($locations as $loc) {
                                echo '<option value="' .$loc['wareHouseAutoID']. '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-'. $loc['wareHouseLocation']. ' - ' . $outlet['isActive'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="">Sort By </label>
                        <br>
                        <?php echo form_dropdown('sort_by', $sort_by_drop, '', ' id="sort_by"  class="form-control"'); ?>
                    </div>
                </div>
                <div class="col-sm-12">
                <div class="form-group col-sm-2">
                    <label for="">Type </label>
                    <br>
                    <select name="type_by" id="type_by" class="form-control">
                        <option value="1" selected>Detail Wise</option>
                        <option value="2">Summary</option>
                    </select>
                </div>

                <div class="form-group col-sm-1" style="border: 0px solid">
                    <label for="">&nbsp; </label><br/>
                    <button type="button" class="btn btn-default btn-sm btn-generate" onclick="load_data()" > Generate </button>
                </div>
                 </div>
            </div>
        </fieldset>
        <?php echo form_close(); ?>
    </div>
</div>

<div id="ajax_response" style="width: 570px; margin-left: 20%;"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/pos/reports/upcoming_orders', '', 'POS');
        });
        //$('#sort_by').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        Inputmask().mask(document.querySelectorAll("input"));

        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "<?php echo $newpolicy_date ?> hh:mm A",
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            //$('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
            //$(this).datetimepicker('hide');
        });

        $('.duedateDatepic').datetimepicker({
            showTodayButton: true,
            format: "<?php echo $newpolicy_date ?> hh:mm A",
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            //$('#task_header_form').bootstrapValidator('revalidateField', 'duedate');
            //$(this).datetimepicker('hide');
        });

        $('#date_to').val('<?=$to_date?>');

        $('.select2').select2();

        $("#outletID").multiselect2({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search',
            includeSelectAllOption: true,
            buttonWidth: '160px',
            maxHeight: '30px'
        });

        $("#outletID").multiselect2('selectAll', false);
        $("#outletID").multiselect2('updateButtonText');

        load_data();
    });

    function load_data(){
        var post_data = $('#report_form').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_delivery/upcoming_orders'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
                $("#ajax_response").html('');
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){
                    $("#ajax_response").html(data['view']);
                }
                else{
                    myAlert('e', data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', ''+errorThrown);
            }
        });
    }

    function print_orders(){
        $("#print_content").print( );
    }
</script>
<?php
