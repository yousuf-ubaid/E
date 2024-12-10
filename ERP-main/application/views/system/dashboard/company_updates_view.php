<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$title=$this->lang->line('dashboard_company_updates');
echo head_page($title,false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-9 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="CompanyUpdatesModal()" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create'); ?><!--Create Purchasing Address--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="company_updates_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_title'); ?><!--Title--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_description'); ?><!--Com Updates Description--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_expiry_date'); ?><!--Expiry date--></th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="CompanyUpdatesModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="companyUpdatesHeader">Add Company Update</h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="companyUpdatesForm"'); ?>
                <input type="hidden" class="form-control" id="id" name="id" value="">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="title"><?php echo $this->lang->line('dashboard_company_updates_add_title'); ?></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="<?php echo $this->lang->line('dashboard_company_updates_add_title'); ?>">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="expiry"><?php echo $this->lang->line('dashboard_company_updates_add_expiry'); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="expiryDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                value="<?php echo $current_date; ?>" id="expiryDate" class="form-control docdt" placeholder="<?php echo $this->lang->line('dashboard_company_updates_add_expiry'); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="description"><?php echo $this->lang->line('dashboard_company_updates_add_description'); ?></label>
                        <textarea  class="form-control" id="description" name="description" rows="3" placeholder="<?php echo $this->lang->line('dashboard_company_updates_add_description'); ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function() {
            fetchPage('system/company_updates_view', 'Test', 'Company Updates');
        });

        let date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy + ' hh:mm A',
        });
        
        fetchCompanyUpdates();

        $('#companyUpdatesForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                title: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('dashboard_company_updates_title_is_required'); ?>.'
                        }
                    }
                },
                description: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('dashboard_company_updates_description_is_required'); ?>.'
                        }
                    }
                },
                expiryDate: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('dashboard_company_updates_expirydate_is_required'); ?>.'
                        }
                    }
                },
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            let $form = $(e.target);
            let bv = $form.data('bootstrapValidator');
            let data = $form.serializeArray();

            let id = $('#id').val();
            data.push({ name: 'id', value: id });

            let url = id ? "<?php echo site_url('CompanyUpdates/update'); ?>" : "<?php echo site_url('CompanyUpdates/create'); ?>";

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
                        $("#CompanyUpdatesModal").modal("hide");
                        fetchCompanyUpdates()
                    }
                }, 
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function fetchCompanyUpdates() {
        $('#company_updates_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('CompanyUpdates/loadCompanyUpdates'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "title"},
                {"mData": "description"},
                {"mData": "expiryDate"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    
    function editCompanyUpdatesModal(id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('CompanyUpdates/getById'); ?>",
            success: function (data) {
                data = data['data'];
                openCompanyUpdatesModal();
                $('#companyUpdatesHeader').text('Edit Company Update');
                $('#id').val(id);
                $('#title').val(data.title);
                $('#description').val(data.description);
                $('#expiryDate').val(data.expiryDate);
            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again')?>.');
            }
        });
    }

    function openCompanyUpdatesModal() {
        $('#companyUpdatesForm').bootstrapValidator('resetForm', true);
        $("#CompanyUpdatesModal").modal("show");
    }

    function CompanyUpdatesModal(){
        $('#companyUpdatesHeader').text('Add Company Update');
        $('#id').val('');
        openCompanyUpdatesModal()
    }

    function deleteUpdate(id) {
        if (id) {
            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            }, function () {
                $.ajax({
                    type: 'POST',
                    url: "<?php echo site_url('CompanyUpdates/delete'); ?>",
                    data: { id: id },
                    success: function (response) {
                        const data = JSON.parse(response);
                        myAlert(data.status === 's' ? 's' : 'e', 'Message: ' + data.message);
                        if (data.status === 's') {
                            fetchCompanyUpdates()
                        }
                    },
                    error: function () {
                        myAlert('e', 'Message: An error occurred while deleting the update.');
                    }
                });
            });
        }
    }
</script>