<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_bta_bank_reconcilation_summary');
echo head_page($title, false);

/*echo head_page('Bank Reconciliation Summary', false);*/
$page_id=trim($this->input->post('page_id'));
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<style>
    .datepicker {
        z-index: 0 !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>

            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">


        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="bankrecheader('<?php echo $page_id ?>');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('treasury_bta_new_bank_reconcilation');?><!--New Bank Reconciliation--> </button>
        <!--       <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_new',<?php /*echo $page_id */?>,'Add Bank Reconciliation','Bank Reconciliation');"><i class="fa fa-plus"></i> New Bank Reconciliation </button>-->
    </div>
</div><hr>

<?php $CI=get_instance();
$companyID=current_companyID();
$data=$CI->db->query("SELECT c.systemAccountCode, c.GLSecondaryCode, c.GLDescription, c.isBank, c.bankName, c.bankBranch, c.bankSwiftCode, c.bankAccountNumber, c.bankCurrencyID, c.bankCurrencyCode, b.bankCurrencyDecimalPlaces As jdecimal, GLAutoID, SUM(IF(transactionType = 2, - 1 * COALESCE(bankCurrencyAmount, 0), 0))+ SUM(IF(transactionType = 1, COALESCE(bankCurrencyAmount, 0), 0)) AS SumbankAmount FROM `srp_erp_chartofaccounts` AS `c` LEFT JOIN `srp_erp_bankledger` AS `b` ON `c`.`GLAutoID` = `b`.`bankGLAutoID` WHERE `c`.`isBank` = 1 AND `c`.`companyID` = {$companyID}  AND GLAutoID={$page_id} GROUP BY `c`.`systemAccountCode`, `c`.`GLSecondaryCode`, `c`.`GLDescription`, `c`.`isBank`, `c`.`bankName`, `c`.`bankBranch`, `c`.`bankSwiftCode`, `c`.`bankAccountNumber`, `c`.`bankCurrencyID`, `c`.`bankCurrencyCode` ")->row_array();

?>


<div class="row">
    <div class="col-sm-12" id="">
        <table style="" class=" table-condendsed">

            <tr>
                <td style=""><strong><?php echo $this->lang->line('common_bank');?><!--Bank--></strong></td>
                <td style="">: <?php echo $data['bankName']?></td>
                <td style=""><strong><?php echo $this->lang->line('common_branch');?><!--Branch--></strong></td>
                <td style="">: <?php echo $data['bankBranch']?></td>
                <td style=""><strong><?php echo $this->lang->line('common_currency');?><!--Currency--></strong></td>
                <td style="">: <?php echo $data['bankCurrencyCode']?></td>
            </tr>
            <tr>
                <td style=""><strong><?php echo $this->lang->line('treasury_common_account_no');?><!--Accound No--></strong></td>
                <td style="">: <?php echo $data['bankAccountNumber']?></td>
                <td style=""><strong><?php echo $this->lang->line('treasury_ap_br_un_book_balance');?><!--Book Balance--></strong></td>
                <td style="">: <?php echo number_format($data['SumbankAmount'],$data['jdecimal'])?></td>
                <td ></td>
                <td ></td>
            </tr>
            </table>
        <hr>
        </div>

    <div class="col-sm-12" id="">

        <div class="table-responsive">
            <table id="load_generated_summary_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('treasury_common_brc_code');?><!--BRC Code--></th>
                    <th><?php echo $this->lang->line('common_month');?><!--Month--></th>
                    <th><?php echo $this->lang->line('common_year');?><!--Year--></th>
                    <th><?php echo $this->lang->line('treasury_common_as_of');?><!--As Of--></th>
                    <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th><?php echo $this->lang->line('treasury_common_created_by');?><!--Created By--></th>
                    <th><?php echo $this->lang->line('treasury_bta_confirmed_yn');?><!--Confirmed YN--></th>
                    <th><?php echo $this->lang->line('treasury_bta_approved_yn');?><!--Approved YN--></th>
                    <th></th>

                </tr>
                </thead>
            </table>
            <br>
            <button type="button" id=""  class="btn btn-default pull-right btn-md" onclick="fetchPage('system/bank_rec/erp_bank_reconciliation','',' Bank Reconciliation','Bank Reconciliation');"> <?php echo $this->lang->line('common_previous');?><!--Previous--> </button>
        </div>

    </div>


</div>

<div class="modal fade" id="bankrecModal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('treasury_bta_create_bank_reconcilation');?><!--Create Bank Reconciliation--> <span id=""></span> </h4>
            </div>

            <form role="form" id="bankrecForm" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">


                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('treasury_common_as_of');?><!--As of --></label>
                            <div class="col-sm-6">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="bankRecAsOf" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="sk"
                                           class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label class="col-sm-4 control-label">Month</label>
                            <div class="col-sm-6">
                                <div class="input-group ">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" readonly name="month" value="<?php /*echo date('Y-m'); */?>" class="form-control  monthFields" required="">
                                </div>
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                            <div class="col-sm-6">
                                <textarea rows="2" type="text" name="description" class="form-control" id=""></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    $('.headerclose').click(function(){
        fetchPage('system/bank_rec/erp_bank_reconciliation','','Bank Reconciliation');
    });
    $(document).ready(function () {


        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            var thisDate = $(this).attr('id');
            var date=  $(this).val().split('-');
            var newmonth=date[0]+'-'+date[1];
            $('.monthFields').val(newmonth);
            $(this).datepicker('hide');
            $('#bankrecForm').bootstrapValidator('revalidateField', thisDate);
        });

        $('.monthFields').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        }).on('changeDate', function(ev){
            var thisDate = $(this).attr('id');
            $(this).datepicker('hide');
            $('#bankrecForm').bootstrapValidator('revalidateField', thisDate);
        });

        bankGLAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        bank_rec_summary();

        $('#bankrecForm').bootstrapValidator({
            live            : 'enabled',
            message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
            excluded        : [':disabled'],
            fields          : {
                bankRecAsOf     : {validators : {notEmpty:{message:'<?php echo $this->lang->line('treasury_common_as_of_date_is_required');?>.'}}},/*As of date is required*/
                /*  month      : {validators : {notEmpty:{message:'Month is required.'}}},*/
                description  : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/

            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name' : 'bankGLAutoID', 'value' : bankGLAutoID });

            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Bank_rec/save_bank_rec_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){


                    refreshNotifications(true);
                    stopLoad();
                    $('#bankrecModal').modal('hide');
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        bank_rec_summary();

                    }
                },error : function(){
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


    });

    function bankrecheader(){
        $('#bankrecModal').modal({backdrop:"static"});
    }





    function bank_rec_summary() {
        $('#load_generated_summary_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/fetch_bank_rec_summary'); ?>",
            "aaSorting": [[3, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

            },
            "aoColumns": [
                {"mData": "bankRecAsOf"},
                {"mData": "bankRecPrimaryCode"},
                {"mData": "month"},
                {"mData": "year"},
                {"mData": "bankRecAsOf"},
                {"mData": "description"},
                {"mData": "createdBy"},
                {"mData": "confirm"},
                {"mData": "approvedYN"},
                {"mData": "edit"}

            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({ "name": "bankGLAutoID","value": bankGLAutoID});
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

    function referback_bankrec(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
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
                    data : {'bankRecAutoID':id},
                    url :"<?php echo site_url('Bank_rec/referback_grv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            bank_rec_summary();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_bankrec(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_common_you_want_to_delete');?>",/*You want to Delete!*/
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
                    data : {'bankRecAutoID':id},
                    url :"<?php echo site_url('Bank_rec/delete_bankrec'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            bank_rec_summary();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>