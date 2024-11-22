<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('crm_quotation');

echo head_page($title, false);

/*echo head_page('Quotation', false);*/
$this->load->helper('crm_helper');
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
//$status_arr_filter = all_quotation_status(false);
$status_arr_filter = array('' =>  $this->lang->line('common_status')/*'Status'*/,0 => 'Open', 1 => 'Submitted to Customer'/*'Confirmed'*/,2=>'Accepted by Customer',3=>'Rejected by Customer',4=>'Quotation / Contract / SO Generated');
$customerDrop = all_customer_drop(true);

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<style>
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }

    .bordertype {
        border-left: 3px solid #daa520;
    }
    .bordertypePRO {
        border-left: 3px solid #f7f4f4;
    }



</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary "
                onclick="fetchPage('system/crm/create_new_quotation',null,'<?php echo $this->lang->line('crm_add_new_quotation')?>','CRM');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('crm_quotation');?> </h4><!--Quotation-->
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row" style="margin-top: 2%;">
                <div class="col-sm-4" style="margin-left: 2%;">
                    <div class="col-sm-2">
                        <div class="mailbox-controls">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">&nbsp;<label
                                        for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchTask" type="text" class="form-control input-sm"
                                       placeholder="Search Quotation"
                                       id="searchTask" onkeypress="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="col-sm-3">
                        <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="filter_statusID"  onchange="startMasterSearch()"'); ?>
                    </div>
                    <div class="col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                    </div>
                </div>
            </div>
            <br>
            <div id="quotationMaster_view"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('crm_quotation');?> </h4><!--Quotation-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
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


<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     id="qutsogeneratemodel" >
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0 none;min-height: 1px;padding-bottom: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <div class="row">
                    <div class="col-sm-8">
                        <h4 class="modal-title bordertype">&nbsp;<strong style="font-family: tahoma;font-weight: 900;font-size: 108%;">Estimate</strong></h4><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance : <lablel id="dateofissue_qut">00.00.0000</lablel></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydate_qut">00.00.0000</label></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Reference Number : <label id="referencenumber_qut">00.00.0000</strong></h6>
                        <h6 class="modal-title bordertype">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Narration : <label id="narration_qut">00.00.0000</strong></h6>
                    </div>
                    <div class="col-sm-4">
                        <h4 class="modal-title bordertypePRO">&nbsp;&nbsp;<strong style="font-weight: 300;font-size: 127%;color: #908f8f;"><lablel id="quotationcode_qut">AAA-000000</lablel></strong></h4><!--Quotation-->
                        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma;font-size: 88%;color: #aba6a6;">Date of Issuance :  <lablel id="dateofissueright_qut">00.00.0000</lablel></strong></h6><!--Quotation-->
                        <h6 class="modal-title bordertypePRO">&nbsp;&nbsp;&nbsp;<strong style="font-family: tahoma; font-size: 88%;color: #3e3e3e;font-weight: 700;">Open Till : <label id="expiarydateright_qut">00.00.0000</strong></h6>



                    </div>
                </div>
            </div>
            <?php echo form_open('', 'role="form" id="qut_so_master_form"'); ?>
            <input type="hidden" id="qutID" name="qutID">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="qutationview"></div>
                    </div>
                </div>
            </div>
            <!--    <div class="modal-footer">
                    <button type="button" onclick="save_qut_so();" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Generate
                    </button>
                </div>-->
                </form>
            </div>
        </div>
    </div>

<div class="modal fade" id="access_denied" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="title_generate_exceed"></h4>
            </div>
            <div class="modal-body">
                <h6 class="modal-title" id="myModalLabel" style="color: #000000;font-size: 13px;">Customer Name : <label id="customername">..</label></h6>
                <h6 class="modal-title" id="myModalLabel" style="color: red;font-size: 13px;">You cannot process this document.</h6>
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/crm/quotation_management', '', 'Quotation');
        });
        getQuotationManagement_tableView();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.dropdown-toggle').dropdown()

    });

    function getQuotationManagement_tableView() {
        var searchQuotation = $('#searchQuotation').val();
        var status = $('#filter_statusID').val();
        var type = $('#filter_typeID').val();
        var assignee = $('#filter_assigneesID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchQuotation': searchQuotation, status: status, type: type, assignee: assignee,crmedittype:1},
            url: "<?php echo site_url('crm/load_quotationManagement_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#quotationMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getQuotationManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#filter_typeID').val('');
        $('#filter_statusID').val('');
        $('#filter_assigneesID').val('');
        $('#searchQuotation').val('');
        $('#searchTask').val('');
        getQuotationManagement_tableView();
    }

    function delete_crm_quotation(id) {
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
                    data: {'quotationAutoID': id},
                    url: "<?php echo site_url('Crm/delete_crm_quotation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        getQuotationManagement_tableView();
                        myAlert('s', 'Quotation Deleted Successfully');
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function view_quotation_printModel(quotationAutoID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {quotationAutoID: quotationAutoID, html: html},
            url: "<?php echo site_url('crm/quotation_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
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

    function reOpen_contract_qut(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'QuotationAutoID':id},
                    url :"<?php echo site_url('Crm/re_open_quotation_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        getQuotationManagement_tableView();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function generate_qut_so(id)
    {
        $("#typeID").val(null).trigger("change");
        $("#qutID").val('');
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'QuotationAutoID':id,'Genqutso':1,html: 'html','type':1},
            url :"<?php echo site_url('Crm/details_generate_qu_so'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){

                if(data['isexist']==1)
                {
                    $('#access_denied_body').empty();
                    $('#customername').html(data['detail_fetch']['detail']['organizationcus']['customerName']);
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail_fetch']['detail'])) {
                        $('#access_denied_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    }
                    else {
                        $.each(data['detail_fetch']['detail'], function (key, value) {
                            $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['productName'] + '</td></tr>');
                            x++;
                        });
                    }
                    $('#title_generate_exceed').text(data['detail_fetch']['master']['quotationCode']);
                    $('#access_denied').modal('show');
                }else if(data['detail_fetch']['isexist']==2)
                {
                    $('#access_denied_body').empty();
                    $('#customername').html('Customer not linked');
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail_fetch']['detail'])) {
                        $('#access_denied_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    }
                    else {
                        $.each(data['detail_fetch']['detail'], function (key, value) {
                            $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['productName'] + '</td></tr>');
                            x++;
                        });
                    }
                    $('#title_generate_exceed').text(data['detail_fetch']['master']['quotationCode']);
                    $('#access_denied').modal('show');
                } else
                {
                    $('#dateofissue_qut').html(data['master']['quotationDate']);
                    $('#expiarydate_qut').html(data['master']['quotationExpDate']);
                    $('#dateofissueright_qut').html(data['master']['quotationDate']);
                    $('#expiarydateright_qut').html(data['master']['quotationExpDate']);
                    $('#quotationcode_qut').html(data['master']['quotationCode']);
                    if(data['master']['referenceNo'])
                    {
                        $('#referencenumber_qut').html(data['master']['referenceNo']);
                    }else
                    {
                        $('#referencenumber_qut').html('-');
                    }
                    if(data['master']['quotationNarration'])
                    {
                        $('#narration_qut').html(data['master']['quotationNarration']);
                    }else
                    {
                        $('#narration_qut').html('-');
                    }
                    $('#qutationview').html(data['view']);
                    $('#qutsogeneratemodel').modal('show');
                    //$('#title_generate').text(data['master']['quotationCode']);
                    $('#qutID').val(id);
                }

                stopLoad();
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
                stopLoad();
            }
        });


    }
    function save_qut_so() {
        var data = $('#qut_so_master_form').serializeArray();
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : data,
            url :"<?php echo site_url('Crm/generate_so_qut'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#qutsogeneratemodel').modal('hide');
                    getQuotationManagement_tableView();
                }
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }


</script>
