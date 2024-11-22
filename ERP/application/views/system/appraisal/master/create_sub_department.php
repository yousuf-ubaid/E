<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


$title = $this->lang->line('appraisal_master_create_new_subdepartment_title');

$company_id = current_companyID();


?>
<style>
    .error-message {
        color: red;
    }
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body" style="display: block;">
                <div id="filter-panel" class="collapse filter-panel"></div>

                <div class="tab-content">
                    <div id="step1" class="tab-pane active">
                        <?php echo form_open('', 'role="form" id="invoice_form"'); ?>
                        <input type="hidden" id="supplierCreditPeriodhn" name="supplierCreditPeriodhn">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <!--                Department field -->
                                <label for="invoiceType">
                                    <?php echo $this->lang->line('appraisal_master_create_new_subdepartment_form_field_1'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <select id="departments" class="form-control select2">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <!--                Sub Department Description field -->
                                <label for="subDepartmentDescription">
                                    <?php echo $this->lang->line('appraisal_master_create_new_subdepartment_form_field_2'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                                <input type="text" id="subDepartmentDescription" class="form-control"/>
                                <div id="subDepartmentDescriptionError" class="error-message"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-right m-t-xs">
                            <button class="btn btn-primary" id="save_sub_department" type="button">
                                <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                        </div>
                        </form>
                    </div>
                    <div id="step2" class="tab-pane">

                    </div>

                </div>

                <script type="text/javascript">
                    app = {};
                    app.company_id = <?php echo current_companyID(); ?>;

                    $(document).ready(function () {
                        load_department_dropdown(app.company_id);
                    });

                    function load_department_dropdown(company_id) {
                        $.ajax({
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_departments'); ?>",
                            data: {company_id: company_id},
                            success: function (data) {
                                //id="departments"
                                var option_elements = "";
                                data.forEach(function (item, index) {
                                    option_elements += '<option value="' + item.DepartmentMasterID + '">' + item.DepartmentDes + '</option>';

                                });
                                $("#departments").html(option_elements);
                            }
                        });
                    }

                    $(document).on('click', '#save_sub_department', function () {

                        if (new_sub_department_form_validation()) {
                            var selected_department_id = $('#departments').find(":selected").val();
                            var sub_department_description = $("#subDepartmentDescription").val();
                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/add_sub_departments'); ?>",
                                data: {
                                    company_id: app.company_id,
                                    selected_department_id: selected_department_id,
                                    sub_department_description: sub_department_description
                                },
                                success: function (data) {
                                    }
                            });
                        }

                    });

                    function new_sub_department_form_validation() {
                        hide_error('subDepartmentDescriptionError');
                        var sub_department_description = $("#subDepartmentDescription").val();

                        var status = true;
                        if (sub_department_description == "") {
                            show_error('subDepartmentDescriptionError', 'Description is required.')
                            status = false;
                        }
                        return status;
                    }


                    $(document).on('click', '#new_sub_department_form_close', function () {
                        fetchPage('system/appraisal/master/sub_department', '', 'Sub Department');
                    })

                    function show_error(errorDivId, errorMessage) {
                        var divSelector = "#" + errorDivId;
                        $(divSelector).html(errorMessage);
                    }

                    function hide_error(errorDivId) {
                        var divSelector = "#" + errorDivId;
                        $(divSelector).html("");
                    }

                </script>

