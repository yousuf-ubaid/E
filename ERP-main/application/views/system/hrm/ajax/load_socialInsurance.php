<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="row">
    <div class="col-md-12">
        <div style="margin: auto -15px">
            <div class="col-md-6">
                <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                    <tr>
                        <td width="85px">SSO No</td>
                        <td class="bgWhite" colspan="2">
                            <a href="#" data-type="text" data-placement="right" data-title="Edit SSO No"
                               data-pk="<?php echo $empID; ?>" id="sso_xEditable" data-value="<?php echo $ssoNo; ?>">
                                <?php echo $ssoNo; ?>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="$('#socialInsraunce').modal('show')"><i
                        class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('emp_add');?><!--Add-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive" style="margin: 15px -15px">
    <table id="socialInsuranceTb" class="<?php echo table_class(); ?>" style="">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th><?php echo $this->lang->line('emp_description');?><!--Description--></th>
            <th><?php echo $this->lang->line('emp_social_insurance_no');?><!--Social Insurance No--></th>
            <th>%</th>
            <th style="width: 35px"><?php echo $this->lang->line('emp_action');?><!--Action--></th>
        </tr>
        </thead>

    </table>
</div>


<div class="modal fade" id="socialInsraunce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('emp_social_insurance_add_new');?><!--Add Social Insurance--></h4>
            </div>

            <form role="form" id="socialForm" class="form-horizontal">
                <div class="modal-body">
                    <div class="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"
                                       for="socialInsuranceMasterID"><?php echo $this->lang->line('emp_social_insurance');?><!--Social Insurance--> <?php required_mark(); ?></label>
                                <select name="socialInsuranceMasterID" id="socialInsuranceMasterID"
                                        class="form-control select2">
                                    <option value=""><?php echo $this->lang->line('emp_select_a_social_insurance');?> </option><!--Select a Social Insurance-->
                                    <?php
                                    foreach ($si as $s) {
                                        ?>
                                        <option
                                            value="<?php echo $s['socialInsuranceID'] ?>_<?php echo $s['type'] ?>"><?php echo $s['Description'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label"
                                       for="socialInsuranceNumber"><?php echo $this->lang->line('emp_social_insurance_no');?><!--Social Insurance No--> <?php required_mark(); ?></label>
                                <input type="text" name="socialInsuranceNumber" value="" id="socialInsuranceNumber"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="editID" name="editID" value="0">
                    <button type="submit" class="btn btn-primary btn-sm actionBtn" id="si-btn"><?php echo $this->lang->line('emp_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var socialForm = $('#socialForm');
    $(document).ready(function () {
        socialForm.bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                socialInsuranceMasterID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_social_insurance_is_required');?>.'}}},/*Social Inusrance is required*/
                socialInsuranceNumber: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_social_no_is_required');?>.'}}}/*Social Insurance No is required*/
            }
        }).on('success.form.bv', function (e) {
            /*$('.si-btn').prop('disabled', false);*/
            e.preventDefault();
            var $form = $(e.target);
            var postData = $form.serializeArray();
            var urlReq = $form.attr('action');

            var empID = '<?php echo $empID; ?>';
            postData.push({'name': 'empID', 'value': empID});

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/save_social_insurance'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        load_socialInsurance();
                        socialForm[0].reset();
                        $('#socialInsraunce').modal('hide');
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

        });
    });

    load_socialInsurance();

    $('#sso_xEditable').editable({
        url: '<?php echo site_url('Employee/ajax_update_ssoNo') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                /*if( data[0] == 's'){
                    var description_xEditable = $('#description_xEditable');
                    setTimeout(function (){
                        description_xEditable.attr('data-pk', description_xEditable.html());
                        description = $.trim(description_xEditable.html());
                    },400);

                }else{
                    var oldVal = $('#description_xEditable').data('pk');
                    setTimeout(function (){
                        $('#description_xEditable').editable('setValue', oldVal );
                    },300);
                }*/
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });

    function load_socialInsurance() {
        var soc = $('#socialInsuranceTb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_socialInsurance'); ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                if (oSettings.bSorted || oSettings.bFiltered) {

                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                }
                if(fromHiarachy ==0){
                    $('.socialNumber').editable({
                        params: function (params) {
                            params.empId = '<?php echo $empID; ?>';
                            return params;
                        }
                    })
                }
                if(fromHiarachy==1){
                    soc.column( 4 ).visible( false );
                }

            },
            "aoColumns": [
                {"mData": "socialInsuranceDetailID"},
                {"mData": "Description"},
                {"mData": "socialNumber"},
                {"mData": "contribution"},
                {"mData": "delete"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                var empID = '<?php echo $empID; ?>';
                aoData.push({'name': 'empID', 'value': empID});
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

    function delete_si(socialInsuranceDetailID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'socialInsuranceDetailID': socialInsuranceDetailID},
                    url: "<?php echo site_url('Employee/delete_si'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        load_socialInsurance();
                        myAlert(data[0], data[1]);
                        stopLoad()
                    }, error: function () {
                        stopLoad()
                        swal("Cancelled", "Your record is safe :)", "error");
                    }
                });
            });
    }

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
    }
</script>