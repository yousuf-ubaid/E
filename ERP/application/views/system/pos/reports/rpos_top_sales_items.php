<?php echo head_page('<i class="fa fa-bar-chart"></i> Top Sales Items', false);

// $outlets = get_active_outletInfo();
$outlets = get_active_outletInfo_with_status();

$primaryLanguage = getPrimaryLanguage();
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

?>
<form id="frm_top_sales_items" method="post" class="form-inline" role="form">
    <div class="form-group">
        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outletPromotions" onchange="" multiple>
            <?php
            foreach ($outlets as $outlet) {
                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-' . $outlet['wareHouseLocation'] . ' - ' . $outlet['isActive'] . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label class="" for="">Date From</label>
        <input type="text" class="form-control input-sm date_pic" id="start_date" name="start_date"
               value="<?php echo date('d-m-Y 00:00:00') ?>" style="width: 130px;"/>
    </div>

    <div class="form-group">
        <label class="" for="">To Date</label>
        <input type="text" class="form-control input-sm date_pic" id="end_date" name="end_date"
               value="<?php echo date('d-m-Y 23:59:59') ?>" style="width: 130px;"/>
    </div>

    <button type="button" onclick="top_sales_report()" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report-->
    </button>
</form>
<hr>
<div id="pos_modalBody_delivery_person_report">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.headerclose').click(function () {
            fetchPage('system/pos/reports/rpos_top_sales_items.php', 'RPOS', 'Top Sales Items');
        });

        $('.date_pic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        });

        $("#outletPromotions").multiselect2({
            enableCaseInsensitiveFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true,
            maxHeight: 400
        });
        $("#outletPromotions").multiselect2('selectAll', false);
        $("#outletPromotions").multiselect2('updateButtonText');

    });

    function top_sales_report() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/get_top_sales_items'); ?>", //LoadDiscountReport
            data: $("#frm_top_sales_items").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_delivery_person_report").modal('show');
                $("#title_promotion_or_order").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');

                startLoadPos();
                $("#pos_modalBody_delivery_person_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_delivery_person_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>