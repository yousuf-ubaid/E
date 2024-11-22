<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('hrms_others_master', $primaryLanguage);
echo head_page($this->input->post('page_name'), false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="table-responsive" style="padding: 0px !important;">
        <?php echo form_open('', 'role="form" class="" id="attendanceReview_form" autocomplete="off"'); ?>
        <div >
         <div id="loaduserGroupdropdown"></div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="row">
   
    <div class="col-sm-12 form-inline">
        <div class="form-group hide" id="post_data">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-success-new size-sm"
                    onclick="post_validated_excel_data()"> Post Data<!--Search-->
            </button>
        </div>

        <div class="form-group hide" id="validate_data">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary-new size-sm"
                    onclick="validated_excel_data()"> Validate Data
            </button>
        </div>

        <div class="form-group hide" id="error_view">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary-new size-sm"
                    onclick="view_validated_errors()"> View Errors
            </button>
        </div>
     
    </div>
   
    
</div>



<?php

echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="item_part_number_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="itemPartNumberModelHeader">Errors</h3>
            </div>
            
                <div class="modal-body p-5">
                  
                    <div class="row">
                        <div class="table-responsive">
                            <table id="partNumber_table" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%">Excel LineID</th>
                                        <th style="min-width: 20%">Temp Coloumn Name</th>
                                        <th style="min-width: 11%">Error Message</th>
                                        
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                  
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close')?><!--Close--></button>
                        <!-- <button onclick="save_partNumber_details()" class="btn btn-primary">Save</button> -->
                    </div>
            
        </div>
    </div>
</div>

<script type="text/javascript">
     var empBankTbl = $('#empBankTB');
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/migration/load_applicable_document','','Load Document');
        });
        fetch_header_migration_details();
      //  fetch_migration_submission();
        fetch_excel_upload_migration_details();
    });

    function fetch_excel_upload_migration_details(){

       // var formData = new FormData($("#excelUpload_form")[0]);
       p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

       if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/fetch_excel_upload_migration_details'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                   // $('#loaduserGroupdropdown').html(data);
                   $('#loaduserGroupdropdown').html(data);

                }, error: function () {

                }
            });

       }
        
    }

    function fetch_header_migration_details(){
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/fetch_header_migration_details'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if(data.isValidated==0){
                        $('#post_data').addClass('hide');
                        $('#validate_data').removeClass('hide');
                    }else{
                        $('#post_data').removeClass('hide');
                       $('#validate_data').addClass('hide');
                    }

                }, error: function () {

                }
            });

        }
        
    }

    function validated_excel_data(){
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/validated_excel_data'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    if(data.length==0){
                        myAlert('s', "validate successfully");
                        fetchPage('system/migration/load_applicable_document','','Load Document');
                    }else{
                        // $.each(data, function (i, v) {
                        //     myAlert('e', v.Errormessage + ' At Line Number '+ v.excelLineID+ ' And Column Name -: '+ v.tempColoumnName);

                        // });
                        myAlert('e', "validation fail Please view errors");
                        $('#error_view').removeClass('hide');
                    }

                }, error: function () {

                }
            });

        }
        
    }

    function view_validated_errors(){
     
        $('#item_part_number_model').modal('show');
        fetch_view_validated_errors();
    }

    function fetch_view_validated_errors() {
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#partNumber_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('MigrationDocument/fetch_view_validated_errors'); ?>",
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
                {"mData": "autoID"},
                {"mData": "excelLineID"},
                {"mData": "tempColoumnName"},
                {"mData": "Errormessage"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "migrationHeaderMasterID", "value": p_id});
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

    function post_validated_excel_data(){
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if(p_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {migrationHeaderMasterID: p_id},
                url: "<?php echo site_url('MigrationDocument/post_validated_excel_data'); ?>",
                beforeSend: function () {

                },
                success: function (data) { 
                   $.each(data, function (i, v) {
                        //Employee Master
                        if(v.invoiceType=='EM'){
                            var header_post_url = v.header_post_url;
                            var details_arr = v.emp_details;
                          
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: v,
                                url: header_post_url,
                                beforeSend: function () {

                                },
                                success: function (data1) {
                                    refreshNotifications(true);
                                    myAlert(data1[0], data1[1]);
                                    if (data1[0]=='s') {
                                        var empAutoID = data1[2];
                                        
                                        $.each(details_arr, function (i, val) {

                                            var url_d=val.details_url;
                                            val['empID']=empAutoID;
                                            val['updateID']=empAutoID;

                                            if(val['details_url']=='Employee/save_employmentData_envoy'){
                                                url_d=val.details_url+'/?empID='+empAutoID;
                                            }

                                            if(val['details_url']=='Employee/save_attendanceData'){
                                                url_d=val.details_url+'/?empID='+empAutoID;
                                            }

                                            $.ajax({
                                                async: true,
                                                type: 'post',
                                                dataType: 'json',
                                                data: val,
                                                url: url_d,
                                                beforeSend: function () {

                                                },
                                                success: function (data2) {
                                                    refreshNotifications(true);

                                                }, error: function () {

                                                }
                                            });
                                        });

                                    }

                                }, error: function () {

                                }
                            });

                        }
                        //Customer Master
                        else if(v.invoiceType=='CM'){

                            var header_post_url = v.header_post_url;
                            var details_arr = v.cus_details;
                          
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: v,
                                url: header_post_url,
                                beforeSend: function () {

                                },
                                success: function () {
                                    refreshNotifications(true);
                                },
                                error: function () {

                                }
                            });

                        }
                        else{
                            var header_post_url = v.header_post_url;
                            var detail_post_url = v.detail_post_url;
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: v,
                                url: header_post_url,
                                beforeSend: function () {

                                },
                                success: function (data1) {
                                    refreshNotifications(true);

                                    if (data1['status']) {
                                        InvoiceAutoID = data1['last_id'];

                                        if(v.invoiceType=='DirectIncome'){
                                            v.invoice_details['invoiceAutoID']=InvoiceAutoID;
                                        }
                                        if(v.invoiceType=='StandardExpense'){
                                            v.invoice_details['InvoiceAutoID']=InvoiceAutoID;
                                        }

                                        send_details(v.invoice_details,detail_post_url);

                                    }

                                }, error: function () {

                                }
                            });

                        }
                        
                       
                    });

                }, error: function () {

                }
            });

       }
    }

    function send_details(v,url1){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: v,
            url: url1,
            beforeSend: function () {

            },
            success: function (data2) {
                refreshNotifications(true);

            }, error: function () {

            }
        });
    }

    /////////////////////////////////////////////////

</script>