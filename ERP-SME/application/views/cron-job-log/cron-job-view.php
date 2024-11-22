<?php
$frm_date = date('Y-01-01');
$to_date = date('Y-m-d'); 
$paymentType_arr = payment_type([4,5]);
$inv_type = [ 1=> 'Subscription', 2=> 'Implementation', 0=>'Ad hoc' ];
?>
<style>
    .pay-input{
        float: left;
        margin-right: 10px;
    }

    .label-warning:hover{
        cursor: pointer;
    }

    .sub-container button.multiselect2.dropdown-toggle{
        padding: 0px;
    }

    .form-inline.editableform{
        padding-left: 10px;
        padding-right: 10px;
    }

    .frm-filtter-label{
        padding-right: 10px;
    }

    .input-group {        
        display: inline-flex;
    }

    .addon-date{
        padding: 3px 12px;
    }

    .fa-date-fitter {
        font-size: 12px;
        padding: 2px 3px;
        margin-left: -10px;
    }

    .date-input{
        width: 75px !important;
        font-size: 12px;
        padding: 4px 4px;
        height: 24px;
        border: 1px solid #d2d6de;
    }  

    .date-input:focus {
        border-color: #3c8dbc;
        box-shadow: none;
        outline: 0;
    }  

    .btn-group .input-group-addon{
        padding: 8px 4px;
        font-size: 12px;
        width: 35px;
    }

    .label-invoice {
        color: #3c8dbc;
        font-weight: bold;
        font-size: 11px;
    }

    .label-invoice:hover {
        cursor: pointer;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">            
            <?=form_open('', 'id="det_filter_form" name="det_filter_form" autocomplete="off" target="_blank"'); ?>
            <div class="box-header with-border">
                <h3 class="box-title">Cron Job Log</h3>
                <span class="">                                   
                    <div class="col-sm-3 pull-right sub-container">                          
                        <div class="input-group pull-right" id="">
                            <div class="input-group-addon addon-date"><i class="fa fa-calendar fa-date-fitter" aria-hidden="true"></i></div>
                            <input type="text" name="to_date" class="date-input input-small" value="<?=$to_date?>" id="to_date">    
                        </div>
                        <label class="frm-filtter-label pull-right" for="to_date"> To</label>
                                                
                        <div class="input-group pull-right" id="">
                            <div class="input-group-addon addon-date"><i class="fa fa-calendar fa-date-fitter" aria-hidden="true"></i></div>
                            <input type="text" name="frm_date" class="date-input input-small" value="<?=$frm_date?>" id="frm_date" >
                        </div>
                        <label class="frm-filtter-label pull-right" for="frm_date">From </label>
                    </div>                
                </span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="log_tb" class="<?=table_class()?>">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th style="min-width: 35%">Log</th>
                                    <th style="min-width: 35%">Query</th>
                                    <th style="min-width: 20%">Companies</th>                                    
                                    <th style="min-width: 8%">Date</th>                            
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?=form_close(); ?>
        </div>
    </div>
</section>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>


<script type="text/javascript">    
    let frm_date = $('#frm_date');
    let to_date = $('#to_date');  
 
    $(document).ready(function () {
        load_cron_log_data();

        setTimeout( () => {
            $('.date-input').datepicker({
                format: "yyyy-mm-dd",
                viewMode: "months",
                minViewMode: "days"
            }).on('changeDate', function (ev) {
                $(this).datepicker('hide');

                if( frm_date.val() > to_date.val() ){
                    swal("Error", "To date should be grater than from date", "error");                    
                    return false;
                }

                load_cron_log_data();   
            });
        }, 300);
    });

    function load_cron_log_data() {
        $('#log_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?=site_url('Dashboard/fetch_cron_log'); ?>",
            "aaSorting": [[0, 'DESC']],
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
            "initComplete": function() {
                //add a name to search box for excel download purpose (with out the input name we cannot get the value in POST)
                $('#payDet_tb_filter').find('input[type="search"]').attr('name', 'text-search');
            },
            "columnDefs": [
                
            ],
            "aoColumns": [                              
                {"mData": "id"},
                {"mData": "msg"},
                {"mData": "processed_qry"},
                {"mData": "com_name_list"},
                {"mData": "created_at"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {                
                aoData.push({'name': 'frm_date', 'value': frm_date.val()});
                aoData.push({'name': 'to_date', 'value': to_date.val()}); 

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
</script>    