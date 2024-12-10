<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('mis');
    $this->load->library('sequence');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Sales Data Manual Posting', false);
    
    $doc_id = $this->sequence->sequence_generator('MIS/');
    $date_formated = date('Y-m-d',strtotime(current_date()));

    $report_id = $data_arr;

    if($report_id){
        $report_details = get_mis_report_details($report_id);
        $report_details['date'] = date('Y-m-d',strtotime($report_details['date']));
    }

    $type2_arr = array('1'=>"Income",'2'=>"Expense");

 

?>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="report_posting">

            <input type="hidden" name="report_id" id="report_id" value="<?php echo $report_id ?>" />

            <table class="<?php echo table_class() ?> text-left">
                <tr>
                    <td class="text-right"> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Doc ID</label>

                        <div class="col-sm-8">

                            <div class="col-sm-8">
                                <?php if(!isset($posting_detials)) { ?>
                                    <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" disabled />
                                <?php } else { ?>
                                    <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $posting_detials['doc_id'] ?>" />
                                <?php } ?>
                            </div>
                               
                        </div>
                    </td>

                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Report Type</label><!--Status-->

                        <div class="col-sm-4">
                            <?php echo form_dropdown('mis_report_type', array(''=>'Select report type','1' =>'PL','2' =>'BS','3' =>'OTH'/*'Approved'*/),isset($report_details['type']) ? $report_details['type'] : '' , 'class="form-control" id="mis_report_type" ' ); ?>
                        </div>
                    </td><!--Approved-->
                </tr>
                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Date</label><!--Status-->

                        <div class="col-sm-8">
                            <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo isset($report_details['date']) ? $report_details['date'] : ''  ?>" id="date" <?php if(!empty($report_details['date'])) { ?>  disabled <?php } ?> class="input-small form-control">
                        </div>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Report Name</label><!--Status-->

                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="report_name" id="report_name" value="<?php echo isset($report_details['report_name']) ? $report_details['report_name'] : '' ?>"  <?php if(!empty($report_details['report_name'])) { ?>  disabled <?php } ?> required/>
                        </div>
                    </td>
                   
                </tr>
                <tr>
                    <td>
                    <?php if(!isset($report_details['report_id'])) { ?>
                        <label for="inputEmail3" class="col-sm-4 control-label"></label><!--Status-->
                            <button class="btn btn-success" type="submit" id="proceed_report"> <i class="fa fa-plus"></i> &nbsp Create </button>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        </form>
    </div>
    
</div>
<hr>


<div class="modal fade" id="add_report_row" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Report Row';?></h4>
            </div>
    
            <form class="form-horizontal" id="sales_column_mapping_form">
                <div class="modal-body">
                   
                    <input type="hidden" name="config_id" id="config_id" value="1" />
                    <input type="hidden" name="type" id="type" value="add" />

                    <div class="col-sm-12">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                
                                <hr>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Report Master Type</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('header_type1', array('' =>  'Please Select', '1' =>'Header', '2' =>'Total','3' =>'Group Total','4' =>'Group Group Total'), '', 'class="form-control" id="header_type1" required'); ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Header Type</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('header_type2', $type2_arr, '', 'class="form-control" id="header_type2" required'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Category ID</label><!--Status-->
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="cat_id" id="cat_id"  required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Category Description</label><!--Status-->
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="cat_description" id="cat_description" required/>
                                    </div>
                                </div>

                                <hr>


                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>

                                        
        </div>
    </div>
</div>

<div class="modal fade" id="edit_report_row" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Report Row';?></h4>
            </div>

            <form class="form-horizontal" id="sales_column_mapping_form_edit">
    
                <div id="edit_config_row"></div>
            
            </form>
                                        
        </div>
    </div>
</div>

<div class="modal fade" id="add_chart_accounts_groups" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="add_config_area">
           
                                        
        </div>
    </div>
</div>

<hr>


<div class="table-responsive" id="report_table" <?php if(empty($report_id)) { ?> style="display:none" <?php } ?> >
    <div class="text-right" style="padding:10px 0px;">
        <!--  -->
        <button class="btn btn-success" data-toggle="modal" data-target="#add_report_row"  onclick="add_table_row()"><i class="fa fa-plus"></i></button>
    </div>
    <table id="config_mapping" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Type</th><!--Code-->
            <th style="min-width: 10%">Type 2</th><!--Code-->
            <th style="min-width: 10%">CatID</th><!--Code-->
            <th style="min-width: 10%">Cat Description</th><!--Code-->
            <th style="min-width: 10%">Sort Order</th><!--Code-->
            <th style="min-width: 10%">Added GL Accounts</th><!--Code-->
            <th style="min-width: 10%">Group</th><!--Code-->
        </tr>
        </thead>
        <tbody>
          
            
        </tbody>
    </table>
</div>
<hr>


<script type="text/javascript">

    mis_config_table();

    $('#proceed_report').click(() => {

        var report_id = $('#report_id').val();
        var doc_id = $('#doc_id').val();
        var date = $('#date').val();
        var report_type = $('#report_type').val();

        $('#report_posting').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
        }).on('success.form.bv', function (e) {

            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name':'doc_id', 'value':$('#doc_id').val() });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Mis/add_report_posting'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);

                        $('#report_id').val(data['id']);
                        $('#report_table').css('display','block');
                        mis_config_table();
                     
                    },
                    error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });

    })

    ///////////////////////////////////////////////////

    function add_table_row(){
       
    }

    ///////////////////////////////////////////////////

    function mis_config_table() {

        var Otable = $('#config_mapping').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Mis/fetch_mis_report_rows'); ?>",
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
            "createdRow": function( row, data, dataIndex){
                if( data.header_type1 ==  `Total`){
                    $(row).css('background','#d9f8b9');
                } else if(data.header_type1 ==  `Group Total`){
                    $(row).css('background','#ffe6e6');
                }else if(data.header_type1 ==  `Group Group Total`){
                    $(row).css('background','#a2ccf6');
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "header_type1"},
                {"mData": "header_type2"},
                {"mData": "cat_id"},
                {"mData": "cat_description"},
                {"mData": "sort_order"},
                {"mData": "count"},
                {"mData": "view"},
                // {"mData": "mapping_type"},
                // {"mData": "control_acc"},
                // {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({ "name": "report_id","value": $("#report_id").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "order": [6]
        });
    }

    /////////////////////////////////////////////////

    $('#sales_column_mapping_form').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
    }).on('success.form.bv', function (e) {

            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name':'config_id', 'value':$('#config_id').val() });
            data.push({'name':'report_id', 'value':$('#report_id').val() });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Mis/add_config_field_setting'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                       
                        refreshNotifications(true);
                        $('#sales_column_mapping_form').trigger('reset');
                        $('#add_report_row').modal('toggle');
                        mis_config_table();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });

    /////////////////////////////////////////////////////
    
    $('#sales_column_mapping_form_edit').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
    }).on('success.form.bv', function (e) {

            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name':'config_id', 'value':$('#config_id').val() });
            // data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});

            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Mis/add_config_field_setting'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        $('#edit_report_row').modal('toggle');
                        mis_config_table();
                    },
                    error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });

        });

    //////////////////////////////////////////////////////

    function add_config_records(id) {
        
        var report_type = $('#mis_report_type').val();

        $.ajax({
            async: true,
            type: 'post',
            data: {'config_row_id': id,'report_type': report_type},
            url: "<?php echo site_url('Mis/load_config_settings'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#add_config_area').empty();
                $('#add_config_area').html(data);
                $('#add_chart_accounts_groups').modal('toggle');
                
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    /////////////////////////////////////////////////////

    function edit_config_record(id) {
        
        var report_id = $('#report_edit').val();

        $.ajax({
            async: true,
            type: 'post',
            data: {'config_row_id': id, 'report_id': report_id},
            url: "<?php echo site_url('Mis/edit_config_row'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#edit_config_row').empty();
                $('#edit_config_row').html(data);
                $('#edit_report_row').modal('toggle');
                
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    /////////////////////////////////////////////////////

    function delete_config_records(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this');?>",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'config_row_id': id},
                    url: "<?php echo site_url('Mis/delete_config_row'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        mis_config_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
       
    }

    
</script>