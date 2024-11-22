<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo head_page($this->lang->line('treasury_cheque_register'), false);

/*echo head_page('Loan Management',false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class=" form-group col-md-4">
        <label for=""><?php echo $this->lang->line('common_bank'); ?> </label>
        <?php echo form_dropdown('bankGLAutoIDfilter', company_bank_account_drop(0,1), '', 'class="form-control select2" onchange="cheque_register_table()" id="bankGLAutoIDfilter" '); ?>
    </div>
    <div class="col-md-5 text-center">

    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="open_checque_register_modal()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="cheque_register_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_description'); ?> </th>
            <th style="min-width: 20%"><?php echo $this->lang->line('treasury_common_bank_name'); ?> </th>
            <th style="min-width: 10%"><?php echo $this->lang->line('treasury_start_cheque_no'); ?> </th>
            <th style="min-width: 10%"><?php echo $this->lang->line('treasury_no_of_cheques'); ?> </th>
            <th style="min-width: 10%"><?php echo $this->lang->line('treasury_end_cheque_no'); ?> </th>
            <th style="min-width: 10%"><?php echo $this->lang->line('treasury_unused_cheques'); ?> </th>
            <th style="width: 5%"><?php echo $this->lang->line('common_action'); ?> </th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div aria-hidden="true" role="dialog"  id="cheque_register_modal" class=" modal fade bs-example-modal-lg" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="categoryHead"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="cheque_register_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="chequeRegisterID" name="chequeRegisterID">
                    <input type="hidden" id="frstZeros" name="frstZeros">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?></label>
                        <textarea class="form-control readonleenable" rows="3" name="description" id="description" ></textarea>
                       <!-- <input type="text" name="description" id="description" class="form-control readonleenable">-->
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('treasury_start_cheque_no'); ?> <?php required_mark(); ?></label>
                        <input type="number" name="startChequeNo" onkeyup="update_end_cheque()" id="startChequeNo" class="form-control readonleenable">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('treasury_no_of_cheques'); ?> <?php required_mark(); ?></label>
                        <input type="number" name="noofcheques" onkeyup="update_end_cheque()" id="noofcheques" class="form-control readonleenable">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="PVbankCode"><?php echo $this->lang->line('common_bank'); ?> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('bankGLAutoID', company_bank_account_drop(0,1), '', 'class="form-control select2 readonleenable" id="bankGLAutoID" '); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php echo $this->lang->line('treasury_end_cheque_no'); ?> </label>
                        <input type="number" name="endChequeNo" id="endChequeNo" class="form-control readonleenable" readonly>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary savebtn_cheque hide" onclick="saveChequeRegister()"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog"  id="cheque_register_detail_modal" class=" modal fade bs-example-modal-lg" style="display: none;">
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('treasury_cheque_register_detail'); ?></h5>
            </div>
            <?php echo form_open('', 'role="form" id="cheque_register_detail_form"'); ?>
            <div class="modal-body">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('treasury_common_cheque_no'); ?></th>
                            <th><?php echo $this->lang->line('common_status'); ?></th>
                            <th><?php echo $this->lang->line('treasury_pv_code'); ?></th>
                            <th><?php echo $this->lang->line('common_canceled'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="chequeRegisterDtblbody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/cheque_register_management','','<?php echo $this->lang->line('treasury_cheque_register')?>');
        });
      cheque_register_table();
        $('.select2').select2();
    });

    function cheque_register_table(){
        var Otable = $('#cheque_register_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/cheque_register_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "chequeRegisterID"},
                {"mData": "description"},
                {"mData": "bankname"},
                {"mData": "startChequeNo"},
                {"mData": "noofcheques"},
                {"mData": "endChequeNo"},
                {"mData": "Totcount"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({ "name": "bankGLAutoID","value": $("#bankGLAutoIDfilter").val()});
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

   function open_checque_register_modal(){
        $('#chequeRegisterID').val('');
        $('#description').val('');
        $('#frstZeros').val('');
        $('#startChequeNo').val('');
        $('#noofcheques').val('');
        $('#endChequeNo').val('');
        $('#bankGLAutoID').val('').change();
        $('#categoryHead').html('<?php echo $this->lang->line('treasury_add_new_cheque_register')?>');
        $('#cheque_register_form')[0].reset();
       $('.readonleenable').prop('disabled', false)
        $('.savebtn_cheque').removeClass('hide');
        $('#cheque_register_modal').modal('show');
    }

    function saveChequeRegister(){
        var data = $("#cheque_register_form").serializeArray();

        if($('#startChequeNo').val()<0){
            myAlert('w', '<?php echo $this->lang->line('treasury_start_cheque_no_cannot_be_less_than_zero')?>');
            return false;
        }

        if($('#noofcheques').val()<0){
            myAlert('w', '<?php echo $this->lang->line('treasury_no_of_cheque_cannot_be_less_than_zero')?>');
            return false;
        }

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data: data,
            url :"<?php echo site_url('Bank_rec/saveChequeRegister'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    cheque_register_table();
                    $('#cheque_register_modal').modal('hide');
                    $('#cheque_register_form')[0].reset();
                    $('#chequeRegisterID').val('');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function cheque_register_detail_modal(chequeRegisterID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'chequeRegisterID':chequeRegisterID},
            url :"<?php echo site_url('Bank_rec/cheque_register_detail_modal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){   
                stopLoad(); 
                $('#chequeRegisterDtblbody').empty(); 
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#chequeRegisterDtblbody').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data, function (key, value) {
                        var status ="Not Used";

                        if(value['status']==1){
                            status ="Used";
                        }else if(value['status']==2){
                            status ="Cancelled";
                        }else{
                             status ="Not Used";
                        }

                        if(value['documentID']=='PV'){
                            var pvcode='<a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'PV\','+value['documentMasterAutoID']+')">'+value['documentID']+' | '  +value['chequeNo']+ '</a> <span class="text-green">Payment Voucher</span>';
                        } else if(value['documentID']=='BT') {
                            var pvcode='<a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'BT\','+value['documentMasterAutoID']+')">'+value['documentID']+' | '+ value['chequeNo']+ '</a> <span class="text-green">Bank Transfer </span>';
                        } else {
                            pvcode='';
                        }
           
    
                        var checked="checked";
                        if(value['status']==0){
                            checked="";
                        }

                        var checkbx='<input type="checkbox" onchange="uodatechequeStatus(' + value['chequeRegisterDetailID'] + ')" id="status_' + value['chequeRegisterDetailID'] + '" name="status"  value="1" ' + checked + '>';
                        if(value['status']==1){
                             checkbx='';
                        }

                        $('#chequeRegisterDtblbody').append('<tr><td>' + x + '</td><td>' + value['chequeNo'] + '</td><td >' + status + '</td><td>' + pvcode + '</td><td class="text-right">'+checkbx+'</td></tr>');
                        x++;
                    });
                }
                $('#cheque_register_detail_modal').modal('show');
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function uodatechequeStatus(chequeRegisterDetailID){
        var chkval=0;
        if ($('#status_'+chequeRegisterDetailID).is(':checked')) {
             chkval=2;
        }
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_you_want_to_update_this_record')?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_update')?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'chequeRegisterDetailID':chequeRegisterDetailID,'chkval':chkval},
                    url :"<?php echo site_url('Bank_rec/uodatechequeStatus'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                    },error : function(){
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    }

    function update_end_cheque(){
        var noofcheques=$('#noofcheques').val();
        var startChequeNo=$('#startChequeNo').val();
        var digit = startChequeNo.toString()[0];
        var pre = '';
        if(digit==0){


            var d = startChequeNo.toString().length;
            var x=0;
            var max=0;
            if(startChequeNo.toString()[0]==0){
                for (i = d; i >= 1; i--) {
                    var valu=startChequeNo.toString()[x];
                    if(max==0){
                        if(valu==0){
                            pre += '0';
                        }else{
                            max=1;
                        }
                    }
                    x++
                }
            }else{
                 pre = '';
                 x=0;
                 max=0;
            }
            var endchqno=parseFloat(startChequeNo)+parseFloat(noofcheques)-1;
            $('#endChequeNo').val(pre+endchqno);
            $('#frstZeros').val(pre);
        }else{
            $('#endChequeNo').val(parseFloat(startChequeNo)+parseFloat(noofcheques)-1);
        }

    }
    function cheque_register_master_modal(chequeRegisterID)
    {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'chequeRegisterID':chequeRegisterID},
            url :"<?php echo site_url('Bank_rec/fetch_cheque_regdetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                $('#categoryHead').html('Edit Cheque Register');
                ((data['isexist']>0)?  $('.readonleenable').prop('disabled', true):$('.readonleenable').prop('disabled', false));
                ((data['isexist']>0)?   $('.savebtn_cheque').addClass('hide'):  $('.savebtn_cheque').removeClass('hide'));
                $('#chequeRegisterID').val(data['detail']['chequeRegisterID']);
                $('#description').val(data['detail']['description']);
                $('#startChequeNo').val(data['detail']['startChequeNo']);
                $('#noofcheques').val(data['detail']['noofcheques']);
                $('#endChequeNo').val(data['detail']['endChequeNo']);
                $('#bankGLAutoID').val(data['detail']['bankGLAutoID']).change();




                $('#cheque_register_modal').modal('show');
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

</script>