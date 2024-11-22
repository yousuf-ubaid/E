<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$status_arr = all_opportunities_status();
$reason_arr = all_crm_critirias();
$reason_arr['-1'] = ('Other');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

<div id="filter-panel" class="collapse filter-panel"></div>
<div id="opportunityMaster_editView"></div>

<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Opportunity State</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="editopportunitystate_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control select2" id="statusID" onchange="statuscheack(this.value)"  required'); ?>
                    </div>
                </div>

                <div class="row closedatehideshow hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Closed Date</label><!--Due Date-->
                    </div>
                    <div class="form-group col-sm-6">

                             <div class="input-group dateDatepic">
                                 <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                 <input type="text" name="closedate"
                                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                        value="<?php echo $current_date; ?>" id="closedate"
                                        class="form-control">
                             </div>

                    </div>
                </div>
                <div class="row closedatehideshow hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Criteria</label><!--Reason-->
                    </div>
                    <div class="form-group col-sm-6">

                                <?php echo form_dropdown('reason', $reason_arr, '', 'class="form-control select2" onchange="reasoncheack(this.value)" id="reason"'); ?>

                    </div>
                </div>
                <div class="row showotherreson hide" style="margin-top: 10px;">

                    <div class="form-group col-sm-3">
                        <label class="title">Remarks</label><!--Opportunity Name-->
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="otherreson" id="otherreson" class="form-control" placeholder="Remarks">
                        <input type="hidden" class="form-control" name="opportunityID"
                               id="opportunityID">
                    </div>
                </div>


            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="opportunity_state()">Update</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="srm_rfq_modelView_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0 none;min-height: 1px;padding-bottom: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="modal-title bordertype">&nbsp;<strong style="font-family: tahoma;font-weight: 900;font-size: 108%;">Estimate</strong></h4><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance : <lablel id="dateofissue">00.00.0000</lablel></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydate">00.00.0000</label></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Reference Number : <label id="referencenumber">00.00.0000</strong></h6>
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Narration : <label id="narration">00.00.0000</strong></h6>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="modal-title bordertypePRO">&nbsp;&nbsp;<strong style="font-weight: 300;font-size: 127%;color: #908f8f;"><lablel id="quotationcode">AAA-000000</lablel></strong></h4><!--Quotation-->
                        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance :  <lablel id="dateofissueright">00.00.0000</lablel></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydateright">00.00.0000</strong></h6>



                    </div>
                </div>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content_new"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        pagenew = '<?php  echo $_POST['policy_id']; ?>';
        if (p_id) {
            opportunityID = p_id;
            page = pagenew;
            getOpportunityManagement_editView(opportunityID,page);
        }
        pageRedirection = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';
        masterID = '<?php if((isset($_POST['policy_id'])) && !empty($_POST['policy_id'])){ echo $_POST['policy_id']; } ?>';

        if (pageRedirection == 'Project') {
            $('.headerclose').click(function () {
                fetchPage('system/crm/project_edit_view', masterID, 'View Project');
            });
        } else if (pageRedirection == 'Contact') {
            $('.headerclose').click(function () {
                fetchPage('system/crm/contact_edit_view', masterID, 'View Contact','opportunitiecontact');
            });
        }else if(pageRedirection == 'dashbardopp')
        {
            $('.headerclose').click(function () {
            fetchPage('system/crm/dashboard', '', 'Dashboard');
            });
        }

        else {
            $('.headerclose').click(function () {
                fetchPage('system/crm/opportunities_management', '', 'Opportunities');
            });
        }

        /*        if (masterID != '') {
         $('.headerclose').click(function () {
         fetchPage('system/crm/organization_edit_view', masterID, 'View Organizations');
         });
         } */

    });

    function opportunity_state() {
        var statusID = $('#statusID').val();
        var reason = $('#reason').val();
        var otherreson = $('#otherreson').val();
        var opportunityID = p_id;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {statusID: statusID, 'reason': reason, opportunityID: opportunityID,'otherreson':otherreson},
            url: "<?php echo site_url('CrmLead/opportunity_update_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#statusModal').modal('hide');
                    setTimeout(function () {
                        fetchPage('system/crm/opportunities_edit_view', p_id, 'View Opportunity', 'CRM');
                    }, 400);

                } else {
                    $('.btn-wizard').removeClass('disabled');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getOpportunityManagement_editView(opportunityID,page) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {opportunityID: opportunityID,'page':page},
            url: "<?php echo site_url('CrmLead/load_opportunityManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#opportunityMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function opportunity_edit_view_close() {

        fetchPage('system/crm/opportunity_management', '', 'Opportunities');

    }
    function statuscheack(statusid)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid},
            url: "<?php echo site_url('CrmLead/crm_opportunity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['isexist'] == 1)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to close this Opportunity!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }else if(data['isexist'] == 3)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to change the status as lost!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }

                else if(data['isexist'] == 2)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to Covert this Opportunity to Project!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }

                else
                {
                    $('.closedatehideshow').addClass('hide');
                    $('.showotherreson').addClass('hide');
                    $("#reason").val(null).trigger("change");
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function reasoncheack(val) {
        if(val == -1)
        {
            $('.showotherreson').removeClass('hide');
        }else
        {
            $('.showotherreson').addClass('hide');
        }
    }
    function view_quotation_printModel_new_view(quotationAutoID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {quotationAutoID: quotationAutoID, html: html,'type':1},
            url: "<?php echo site_url('crm/quotation_print_view_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content_new').html(data['view']);
                $('#dateofissue').html(data['master']['quotationDate']);
                $('#expiarydate').html(data['master']['quotationExpDate']);
                $('#dateofissueright').html(data['master']['quotationDate']);
                $('#expiarydateright').html(data['master']['quotationExpDate']);
                $('#quotationcode').html(data['master']['quotationCode']);
                if(data['master']['referenceNo'])
                {
                    $('#referencenumber').html(data['master']['referenceNo']);
                }else
                {
                    $('#referencenumber').html('-');
                }
                if(data['master']['quotationNarration'])
                {
                    $('#narration').html(data['master']['quotationNarration']);
                }else
                {
                    $('#narration').html('-');
                }



                $("#srm_rfq_modelView_new").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

</script>