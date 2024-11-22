<?php echo head_page('Beneficiary Types', false);
$date_format_policy = date_format_policy();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="add_beneficiaryTypes()"><i class="fa fa-plus"></i> Beneficiary Type
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="BeneficiaryTypes_view"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--modal report-->
<div class="modal fade" id="BeneficiaryTypesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Beneficiary Types</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="beneficiaryTypess_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <input type="text" name="Description" id="Description" class="form-control"
                               placeholder="Description">
                        <input type="hidden" class="form-control" name="beneficiaryTypeID"
                               id="beneficiaryTypeID">
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="save_beneficiaryTypes()">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/beneficiary_types','','Beneficiary Types');
        });
        getBeneficiaryTypesManagement_tableView();

    });

    function getBeneficiaryTypesManagement_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('OperationNgo/load_beneficiaryTypess_Master_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#BeneficiaryTypes_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function delete_beneficiaryTypes(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'beneficiaryTypeID': id},
                    url: "<?php echo site_url('OperationNgo/delete_beneficiaryTypes'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getBeneficiaryTypesManagement_tableView();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_beneficiaryTypes() {
        var description = $('#Description').val();
        var beneficiaryTypeID = $('#beneficiaryTypeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {description:description, beneficiaryTypeID:beneficiaryTypeID},
            url: "<?php echo site_url('OperationNgo/save_beneficiaryTypes_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getBeneficiaryTypesManagement_tableView();
                    $('#BeneficiaryTypesModal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_beneficiaryTypes(beneficiaryTypeID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'beneficiaryTypeID': beneficiaryTypeID},
            url: "<?php echo site_url('OperationNgo/load_beneficiaryTypes_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#Description').val(data['description']);
                    $('#beneficiaryTypeID').val(data['beneficiaryTypeID']);
                    $("#BeneficiaryTypesModal").modal({backdrop: "static"});
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_beneficiaryTypes(){
        $('#Description').val('');
        $('#beneficiaryTypeID').val('');
        $("#BeneficiaryTypesModal").modal({backdrop: "static"});
    }


</script>