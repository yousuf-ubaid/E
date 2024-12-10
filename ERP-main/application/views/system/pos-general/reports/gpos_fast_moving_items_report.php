<?php echo head_page('<i class="fa fa-bar-chart"></i> Fast Moving Items Report', false);
$locations = load_pos_location_drop();
$currentUserWarehouseID = current_warehouseID();
$get_discountType = get_specialCustomers_drop(array(2, 3));


$outlets = get_gpos_location();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        text-align: left !important;
    }

    .ar {
        text-align: right !important;
    }

    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
        min-height: 200px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


?>
<form id="frm_fastmvitmReport" method="post" class="form-group" role="form">
    <input type="hidden" id="promoOrder_outletID" name="outletID" value="0"/>
    <div class="row">
        <div class="form-group col-sm-3">
            <label><?php echo $this->lang->line('common_item'); ?><!--Item--></label>
            <?php echo form_dropdown('items[]', fetch_item_dropdown(false,true), '', 'class="form-control select2" id="items"  multiple="" style="z-index: 0;"'); ?>
        </div>

        <div class="form-group col-sm-3">
            <label class="" for="">Currency</label>
            <select class="form-control input-sm" name="currency" id="currency">
                <option value="companyLocalAmount">Local Currency</option>
                <option value="companyReportingAmount" selected>Reporting Currency</option>
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label class="" for="">Date From</label>
            <input type="text" class="form-control input-sm startdateDatepic" id="startdate"
                   name="startdate" value="<?php echo date('d-m-Y 00:00:00') ?>"/>
        </div>

        <div class="form-group col-sm-2">
            <label class="" for="">To :</label>
            <input type="text" class="form-control input-sm startdateDatepic" id="enddate" name="enddate"
                   value="<?php echo date('d-m-Y 23:59:59') ?>"/>
        </div>

        <div class="form-group col-sm-2">
            <div>&nbsp;</div>
            <button type="button" onclick="load_gpos_fastmovingitm_Report()" class="btn btn-primary btn-sm" style="margin-top: 2%;">
                <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report-->
            </button>
        </div>



    </div>




</form>
<hr>
<div id="rpos_modalBody_fast_moving_itm_report">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
            }
        });
        $('.select2').select2();
        $("#items").select2({
            tags: true,
            containerCssClass : "items-input"
        });
    });

    $('.items-input').on('keyup', '.select2-search__field', function (e) {
        load_items_dropdown(e.target.value);
    });

    var currentRequest = null;
    function load_items_dropdown(skey) {
        let selected = $("#items").val();
        currentRequest = $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Pos/load_items_dropdown'); ?>",
            data: {skey:skey,selected:selected},
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
            success: function (data) {
                var Name = "";
                var ID = "";
                data.items.forEach(function (item, index) {
                    Name = item.seconeryItemCode + ' | ' + item.itemName;
                    ID = item.itemAutoID;
                    $("#items").append("<option value='"+ID+"'>"+Name+"</option>");
                    [].slice.call(items.options)
                        .map(function(a){
                            if(this[a.innerText]){
                                items.removeChild(a);
                            } else {
                                this[a.innerText]=1;
                            }
                        },{});
                });
            }
        });
    }

    function load_gpos_fastmovingitm_Report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_general_report/LoadFastMovingReport'); ?>",
            data: $("#frm_fastmvitmReport").serialize(),
            cache: false,
            beforeSend: function () {
                startLoadPos();
                $("#rpos_modalBody_fast_moving_itm_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#rpos_modalBody_fast_moving_itm_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



</script>