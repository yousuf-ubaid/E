<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$title = '<i class="fa fa-bar-chart"></i> Item Usage Report';
$locations = load_pos_location_drop();
// $outlets = get_active_outletInfo();
$outlets = get_active_outletInfo_with_status();

echo head_page($title, false);
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
        min-height: 200px;
        margin-top: 10px;
    }
</style>
<form id="form_item_usage" method="post" class="form-inline" role="form">
    <input type="hidden" id="item_usage_outletID" name="outletID" value="0"/>
    <label for="" class="col-sm-2"><?php echo $this->lang->line('Filters'); ?><!--Filters--> </label>

    <div class="form-group">
        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outlet_item_usage" multiple>
            <?php
            foreach ($outlets as $outlet) {
                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . trim($outlet['wareHouseDescription'] ?? '') . ' - ' . $outlet['isActive'] . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--></label>

        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;">
        <?php echo $this->lang->line('common_to'); ?><!--to-->
        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2">
    </div>

    <button type="submit" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
</form>

<div id="rpos_modal_body_item_usage" class="reportContainer">
    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
        Report
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function (e) {

        $("#outletID_f").multiselect2('selectAll', false);
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });
        $("#form_item_usage").submit(function (e) {
            item_usage_ajax();
            return false;
        });
        $("#outlet_item_usage").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outlet_item_usage").multiselect2('selectAll', false);
        $("#outlet_item_usage").multiselect2('updateButtonText');
    });

    function item_usage_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/rpos_item_usage_report'); ?>",
            data: $("#form_item_usage").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_modal_body_item_usage").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#rpos_modal_body_item_usage").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>