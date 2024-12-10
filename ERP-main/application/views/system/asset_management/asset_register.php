<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_register');
echo head_page($title, false);
/*
echo head_page('Asset Register', false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$companyId = current_companyID();

$Categories = fa_asset_category(3, false);//Fa Cateogoirs
$location = fetch_all_location();//Location
$segment_arr = fetch_segment(true, false); // Segment


/*Report Extra Fileds*/
$this->db->select("fieldName,caption,isDefault,textAlign,isMandatory,isCalculate");
$this->db->from("srp_erp_reporttemplate rt");
$this->db->join('srp_erp_reporttemplatefields rf', 'rt.reportID = rf.reportID', 'INNER');
$this->db->where("rt.documentCode", 'FA_AR');
$this->db->where("rf.isVisible", 1);
$this->db->order_by("rf.sortOrder ASC");
$columns = $this->db->get()->result_array();
/*print_r($columns);
exit;*/
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <div class="row">
            <div class="col-md-3">
                <label for=""><?php echo $this->lang->line('assetmanagement_date_as_of');?><!--Date As of--> <span title="required field"
                                               style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                <div class=" input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateAsOf" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="dateAsOf"
                           class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class=""><?php echo $this->lang->line('assetmanagement_filter_item_by_cat');?><!--Filter By Item Category--></label>
                <div>
                    <?php echo form_dropdown('FaCategories[]', $Categories, '', 'id="FaCategories" class="form-control"  multiple="multiple"'); ?> </div>
            </div>
            <div class="col-md-3">
                <label class=""><?php echo $this->lang->line('common_filter_by_location')?><!--Filter By Location--></label>
                <div>
                    <?php echo form_dropdown('locationID[]', $location, '', 'id="locationID" class="form-control"  multiple="multiple"');  ?>
                </div>
            </div>
            <div class="col-md-3">
                <label class=""><?php echo $this->lang->line('common_filter_by_segment')?><!--Filter By Segment--></label>
                <div>
                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'id="segment" class="form-control"  multiple="multiple"');  ?>
                </div>
            </div>
            <hr>

        </div>
        <div class="row">
            <fieldset class="scheduler-border" style="margin-top: 10px">
                <legend class="scheduler-border"><?php echo $this->lang->line('assetmanagement_extra_columns');?><!--Extra Columns--></legend>
                <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                    <table class="<?php echo table_class(); ?>" id="extraColumns">
                        <?php
                        if (!empty($columns)) {
                            $i = 1;
                            foreach ($columns as $val) {
                                $checked = "";
                                if ($val["isDefault"] == 1) {
                                    $checked = "checked";
                                }
                                if ($val["isMandatory"] == 0) {
                                    ?>
                                    <tr>
                                        <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                        <td>
                                            <div class="skin skin-square">
                                                <div class="skin-section">
                                                    <input tabindex="<?php echo $i; ?>" id="checkbox<?php echo $i; ?>"
                                                           type="checkbox"
                                                           data-caption="<?php echo $val["caption"] ?>"
                                                           class="columnSelected" name="fieldName"
                                                           value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                    <label for="checkbox<?php echo $i; ?>">
                                                        &nbsp;
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    ?>
                                    <tr class="hide">
                                        <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                        <td>
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?php echo $i; ?>" type="checkbox"
                                                       data-caption="<?php echo $val["caption"] ?>"
                                                       class="columnSelected" name="fieldName"
                                                       value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                <label for="checkbox<?php echo $i; ?>">
                                                    &nbsp;
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $i++;
                            }
                        } ?>
                    </table>
                </div>
                <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">
                    <?php echo $this->lang->line('assetmanagement_put_a_cheack_mark');?> <!--Put a check mark next to each column that you want to appear in the report-->
                </div>
            </fieldset>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button class="btn btn-flat btn-primary pull-right" onclick="assetRegisterMainView(this)"><?php echo $this->lang->line('common_generate');?><!--Generate-->
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/asset_management/asset_register','','Asset Register');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('#FaCategories').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            allSelectedText: 'All Selected',
            buttonWidth: '100%'
        });
        $("#FaCategories").multiselect2('selectAll', false);
        $("#FaCategories").multiselect2('updateButtonText');

        $('#locationID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100%',
            maxHeight: '30px'
        });
        $("#locationID").multiselect2('selectAll', false);
        $("#locationID").multiselect2('updateButtonText');

        $('#segment').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100%',
            maxHeight: '30px'
        });
        $("#segment").multiselect2('selectAll', false);
        $("#segment").multiselect2('updateButtonText');

        /*$('#subCategory').multiselect2({
         enableCaseInsensitiveFiltering: true,
         includeSelectAllOption: true,
         allSelectedText: 'All Selected',
         buttonWidth: '100%'
         });*/

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function assetRegisterMainView(item) {
        var subCategory = $('#subCategory').val();
        var FaCategories = $('#FaCategories').val();
        var locationID = $('#locationID').val();
        var segment = $('#segment').val();

        var dateAsOf = $('#dateAsOf').val();
        var currencyType = $('#currencyType').val();

        if (dateAsOf == '') {
            notification("Date is required");
            return false;
        }

        if ($.isEmptyObject(FaCategories)) {
            notification("Item Main Category is Required.");
            return false;
        }

        /*if ($.isEmptyObject(subCategory)) {
         notification("Item Sub Category is Required.");
         return false;
         }*/

        /*var fieldNameChk = [];
         var i = 0;
         $("input[name=fieldName]:checked").each(function () {
         fieldNameChk[i] = $(this).val()
         i++;
         //            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
         //            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
         });*/

        var fieldName = $("input[name=fieldName]:checked").val();
        var arr = {
            'dateAsOf': dateAsOf,
            'mainCategory': FaCategories,
            'locationID': locationID,
            'segment': segment,
            'currencyType': currencyType,
            'fieldName': fieldName
        };

        fetchPage('system/asset_management/asset_register_main', '', 'Asset Register', '', arr);
    }


    function getSubCategory(faCategories) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/getSubCategoryJson'); ?>",
            data: {masterCategory: faCategories},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                $("#subCategory").multiselect2('dataprovider', data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
</script>