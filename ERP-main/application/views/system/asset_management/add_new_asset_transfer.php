<?php
$primaryLanguage = getPrimaryLanguage();/*Language*/
$this->lang->load('assetmanagementnew', $primaryLanguage);/*Language*/
$this->lang->load('common', $primaryLanguage);/*Language*/
$fetch_all_location = fetch_all_location();
$emp = get_employee_current_company();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$title = $this->lang->line('assetmanagement_add_new_asset_transfer');
echo head_page($_POST['page_name'], false);

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps mb25">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('assetmanagement_asset_transfer'); ?><!--Asset Transfer --></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard disabled" href="#step2" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('assetmanagement_asset_transfer_details'); ?><!--Details--></span>
        </a>
    </div>
</div>

<div class="tab-content">
    <!-- Step 1: Asset Transfer -->
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <?php echo form_open('', 'role="form" id="assetTransferForm"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="documentDate">
                    <?php echo $this->lang->line('common_document_date'); ?><!--Document Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control docdt"
                           placeholder="<?php echo $this->lang->line('common_document_date'); ?>" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="locationFromId">
                    <?php echo $this->lang->line('assetmanagement_location_from'); ?><!--Transfer From--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('locationFromID', $fetch_all_location, '', 'class="form-control select2" id="locationFromId"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="locationToId">
                    <?php echo $this->lang->line('assetmanagement_location_to'); ?><!--Transfer To--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('locationToID', $fetch_all_location, '', 'class="form-control select2" id="locationToId"'); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="requestedEmpId">
                    <?php echo $this->lang->line('assetmanagement_requested_by'); ?><!--Requested By--></label>
                <?php echo form_dropdown('requestedEmpID', $emp, '', 'class="form-control select2" id="requestedEmpId"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="issuedEmpId">
                    <?php echo $this->lang->line('assetmanagement_issued_by'); ?><!--Issued By--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('issuedEmpID', $emp, '', 'class="form-control select2" id="issuedEmpId" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="narration"><?php echo $this->lang->line('common_narration'); ?><!--Narration--></label>
                <textarea class="form-control" name="narration" id="narration" rows="2"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="button" id="assetTransferSave" onclick="save('draft')">
                        <?php echo $this->lang->line('common_save_and_next'); ?><!--Next--></button>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>

    <!-- Step 2: Asset Transfer Details -->
    <div id="step2" class="tab-pane" style="box-shadow: none;">
        <div class="row">
            <div class="col-md-8">
                <h4><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('common_item_details'); ?><!--Item Detail--> </h4>
                <h4></h4>
            </div>
            <button type="button" onclick="assetTransferDetailModal()" class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
            </button>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 25%" class="text-left">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>

                <th style="min-width: 10%" class="text-left">
                    <?php echo $this->lang->line('common_comments'); ?><!--Comments-->
                </th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="table_body">
            <tr class="danger">
                <td colspan="5" class="text-center"><b>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
        </table>

        <table style="width: 30%;margin-top: 50px">
            <tbody>
                <tr>
                    <td width="40%"><?php echo $this->lang->line('common_confirmed_by');?> :</td>
                    <td id="confirmedBy"></td>
                </tr>
                <tr>
                    <td width="40%"><?php echo $this->lang->line('common_confirmed_date');?> : </td>
                    <td id="confirmedDate"></td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-success" type="button" id="assetSave" onclick="confirm('confirmed')">
                        <?php echo $this->lang->line('common_confirm'); ?><!--Save-->
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="asset_transfer_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('common_add_item_details'); ?></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="asset_transfer_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="asset_transfer_detail_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('common_item_code'); ?><?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_comment'); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearfaID(event,this)" class="form-control f_search"
                                       name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>..."
                                       id="f_search_1">
                                <input type="hidden" class="form-control faID" name="faID[]">
                            </td>
                            <td>
                                <textarea class="form-control comment" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('common_comment'); ?>..."></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?></button>
                <button class="btn btn-primary" type="button" onclick="saveAssetTransferDetails()">
                    <?php echo $this->lang->line('common_save_change'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>

    let search_id = 1;
    let faID;
    let transferID;
    let location;

    $(document).ready(function () {

        $('.select2').select2();

        let date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        $('.headerclose').click(function () {
            fetchPage('system/asset_management/asset_transfer', null, '<?php echo $this->lang->line("assetmanagement_asset_transfer"); ?>');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            const nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            const prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        initializeitemTypeahead(1);
        $(document).on('click', '.remove-tr', deleteItemRow);

        transferID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (transferID) {
            getAsset();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
    });

    function confirm(status) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                save(status);
            });
    }

    function save(status) {
        $('#assetTransferForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                locationFrom: {
                    validators: {
                        notEmpty: {
                            message: "<?php echo $this->lang->line('assetmanagement_location_from_required'); ?>"
                        }
                    }
                },
                locationTo: {
                    validators: {
                        notEmpty: {
                            message: "<?php echo $this->lang->line('assetmanagement_location_to_required'); ?>"
                        }
                    }
                },
                issuedBy: {
                    validators: {
                        notEmpty: {
                            message: "<?php echo $this->lang->line('assetmanagement_issued_by_required'); ?>"
                        }
                    }
                },
                transferDate: {
                    validators: {
                        notEmpty: {
                            message: "<?php echo $this->lang->line('assetmanagement_transfer_date_required'); ?>"
                        }
                    }
                }
            }
        });

        $('#assetTransferForm').data('bootstrapValidator').validate();
        if ($('#assetTransferForm').data('bootstrapValidator').isValid()) {

            let data = $('#assetTransferForm').serializeArray();

            $('#assetTransferForm').find(':disabled').each(function() {
                data.push({ name: this.name, value: $(this).val() });
            });

            if (transferID) {
                data.push({ name: 'id', value: transferID });
            }

            data.push({ name: 'status', value: status });

            let url = transferID ? "<?php echo site_url('AssetTransfer/update'); ?>" : "<?php echo site_url('AssetTransfer/create'); ?>";

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: url,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data.status === 's' ? 's' : 'e', 'Message: ' + data.message);
                    if (data.status === 's') {
                        $('.btn-wizard').removeClass('disabled');
                        if (!transferID) {
                            transferID = data.data.id;
                        }
                        if(status === 'draft'){
                            $('.step-wiz[href="#step1"]').addClass('completed');
                            $('[href=#step2]').tab('show');
                        }

                        if(status === 'confirmed'){
                            getAsset();
                        }
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function assetTransferDetailModal() {
        $('.f_search').typeahead('destroy');
        $('#asset_transfer_detail_form')[0].reset();
        $('#asset_transfer_detail_table tbody tr').not(':first').remove();
        $('.f_search').typeahead('val', '');
        $('.faID').val('');
        $('#f_search_1').closest('tr').css("background-color", 'white');
        initializeitemTypeahead(1);
        $("#asset_transfer_detail_modal").modal({
            backdrop: "static"
        });
    }

    function initializeitemTypeahead(id) {
        Inputmask().mask(document.querySelectorAll("input"));
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>AssetTransfer/fetchAssets?&location=' + $('#locationFromId').val(),
            onSelect: function (suggestion) {
                let cont = true;
                $('.faID').each(function () {
                    if (this.value && this.value == suggestion.faID) {
                        $('#f_search_' + id).val('');
                        $('#f_search_' + id).closest('tr').find('.faID').val('');
                        myAlert('w', 'Selected asset is already selected');
                        cont = false;
                    }
                });

                if (cont) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.faID').val(suggestion.faID);
                    }, 200);
                    $(this).closest('tr').css("background-color", 'white');
                }
            }
        });
        $(".tt-dropdown-menu").css("top", "");
        $('#f_search_' + id).off('focus.autocomplete');
    }

    function clearfaID(e, ths) {
        let keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
        } else {
            $(ths).closest('tr').find('.faID').val('');
        }
    }

    function add_more() {
        let newRowId = $('#asset_transfer_detail_table tbody tr').length + 1;
        let appendData = $('#asset_transfer_detail_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + newRowId).val('');
        appendData.find('.faID').val('');
        appendData.find('.comments').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);" onclick="deleteItemRow(this)"></span>');

        $('#asset_transfer_detail_table tbody').append(appendData);
        initializeitemTypeahead(newRowId);
    }

    function deleteItemRow() {
        $(this).closest('tr').remove();
    }

    function saveAssetTransferDetails() {
        let data = $('#asset_transfer_detail_form').serializeArray();

        data.push({
            'name': 'id',
            'value': transferID
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('AssetTransfer/addDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data.status, data.message);
                if (data.status === 's') {
                    getAsset();
                    $('#locationFromId').prop('disabled', true);
                    $('#asset_transfer_detail_modal').modal('hide');
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function getAsset() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': transferID},
            url: "<?php echo site_url('AssetTransfer/getById'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                data = data.data;
                $('#locationFromId').val(data.locationFromID).change();
                $('#locationToId').val(data.locationToID).change();
                $('#requestedEmpId').val(data.requestedEmpID).change();
                $('#issuedEmpId').val(data.issuedEmpID).change();
                $('#narration').val(data.narration);
                $('#documentDate').val(data.documentDate);
                $('#confirmedBy').html(data.confirmedByName);
                $('#confirmedDate').html(data.confirmedDate);

                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data.detail)) {
                    $('#locationFromId').prop('disabled', false);
                    $('#table_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                } else {
                    $('#locationFromId').prop('disabled', true);
                    $.each(data.detail, function (key, value) {
                        let faCode = value.faCode;
                        let faDescription = value.faDescription;
                        let comment = value.comment;

                        let deleteIcon = '<a onclick="deleteAsset(' + value.id + ')"><span class="glyphicon glyphicon-trash" title="Delete" style="color:rgb(209, 91, 71);"></span></a>';

                        $('#table_body').append('<tr>' +
                            '<td>' + x + '</td>' +
                            '<td>' + faCode + '</td>' +
                            '<td>' + faDescription + '</td>' +
                            '<td>' + comment + '</td>' +
                            '<td class="text-center">' + deleteIcon + '</td>' +
                            '</tr>');
                        x++;
                    });
                }

                if(data.confirmedYN){
                    $('#assetTransferForm').find('input, select, textarea, button').prop('disabled', true);
                    $('#step2').find('input, select, textarea, button, a').prop('disabled', true).css({
                        'pointer-events': 'none',
                        'color': '#ccc',
                        'text-decoration': 'none'
                    });
                }
                stopLoad();
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function deleteAsset(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': transferID, 'detailId': id},
                    url: "<?php echo site_url('AssetTransfer/removeDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data.status, data.message);
                        setTimeout(function () {
                            getAsset();
                        }, 300);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

</script>