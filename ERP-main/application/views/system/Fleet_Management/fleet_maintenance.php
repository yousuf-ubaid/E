<?php

echo head_page('Maintenance', false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employeedrop = all_employee_drop();
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }
    .actionicon{
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .headrowtitle{
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">Date From</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="bookingdatefrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="bookingdatefrom" class="form-control"  value=""  >
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp</label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="bookingdateto"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="bookingdateto"  class="form-control" value="" >
            </div>
        </div>


        <div class="col-sm-3" style="margin-top: 26px;">
            <?php echo form_dropdown('employeesearch',$employeedrop, '', 'class="form-control select2" onchange="startMasterSearch()" id="employeesearch"'); ?>
        </div>
        <br>

    </div>
    <br>
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Enter Your Text Here"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
   <!-- <div class="col-md-2">
        <?php /*echo form_dropdown('documentstatus', array('' => 'Document Status', '1' => 'Draft', '2' => 'Confirmed', '3' => 'Approved'), '', 'class="form-control select2" onchange="startMasterSearch()" id="documentstatus"'); */?>
    </div>

    <div class="col-md-2">
        <?php /*echo form_dropdown('jpstatus', array('' => 'JP Status', '1' => 'Not Started', '2' => 'Started', '3' => 'Closed','4' => 'Cancelled','5' => 'On Hold'), '', 'class="form-control select2" onchange="startMasterSearch()" id="jpstatus"'); */?>
    </div>-->
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="ioubookingmasterview">
            <!-- /.table -->
        </div>

    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="vehicale_maintenance">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Usage</h4>
            </div>
            <?php echo form_open('', 'role="form" id="vehicale_maintenance_meter_form"'); ?>
                     <input type="hidden" id="vehiclemasterid" name="vehiclemasterid">
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">previous Usage</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="previousreading" id="previousreading" class="form-control" readonly >
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Current Usage</label>
                    </div>
                    <div class="form-group col-sm-6">

                            <div class="input-group">
                                 <div class="input-group-addon">KM/hrs</div>
                                 <input type="text" name="currentreading" id="currentreading" class="form-control number" onchange="changeamountreading(this.value)" >
                                </div>


                        </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Amount Usage</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="amountreading" id="amountreading" class="form-control" readonly>
                    </div>
                </div>
                <br>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </button>
                    <button type="button" class="btn btn-default" onclick="show_history()">Show History</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="update_vehicale_meter_reading()"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                            aria-hidden="true"></span> Update
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="vehicale_maintenance_history">
    <div class="modal-dialog" style="width: 73%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Usage History</h4>
            </div>
            <div class="modal-body">
               <div id="vehiclemaintenancemeterhis">

               </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </button>
            </div>
        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        number_validation();
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/Fleet_Management/fleet_maintenance', '', 'Vehicle Maintenance');
        });

        getiouvoucherbookingtable();
        Inputmask().mask(document.querySelectorAll("input"));
    });

    $('#searchTask').bind('input', function(){
        startMasterSearch();
    });
    number_validation();
    function getiouvoucherbookingtable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var status = $('#documentstatus').val();
        var jpstatus = $('#jpstatus').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask,'filtervalue':filtervalue,'status':status,'jpstatus':jpstatus},
            url: "<?php echo site_url('Fleet/vehiclemaintenance_masterview'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ioubookingmasterview').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getiouvoucherbookingtable();
    }
    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.donorsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#sorting_1').addClass('selected');
        getiouvoucherbookingtable();
    }
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    function meter_reading(vehiclemasterid) {
        $('#currentreading').val(' ');
        $('#amountreading').val(' ');
        $('#vehiclemasterid').val(' ');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'vehiclemasterid': vehiclemasterid},
            url: "<?php echo site_url('Fleet/fetch_vehicalemaintenace_meter_reading_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#previousreading').val(data['maximumpreviousreading']);
                $('#vehiclemasterid').val(vehiclemasterid);
                $("#vehicale_maintenance").modal({backdrop: "static"});
                $('#save_btn').html('Update');
                stopLoad();
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }
    function changeamountreading(currentreading) {
        var previousreading = $('#previousreading').val();
        var meterreading = currentreading - previousreading
       if(meterreading > 0)
       {
           $('#amountreading').val(meterreading);
       }else
       {
           $('#currentreading').val(' ');
           $('#amountreading').val(' ');
           myAlert('w', 'Current reading canot be less than previous reading');
       }
    }

    function update_vehicale_meter_reading() {
        var data = $('#vehicale_maintenance_meter_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_meter_reading'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getiouvoucherbookingtable();
                    $('#vehicale_maintenance').modal("hide");
                }
            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
    function show_history() {
        var vehicalemasterid = $('#vehiclemasterid').val();
        if (vehicalemasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'vehicalemasterid': vehicalemasterid},
                url: "<?php echo site_url('Fleet/load_vehicale_maintenace_meter_reading_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#vehiclemaintenancemeterhis').html(data);
                    $('#vehicale_maintenance_history').modal("show");
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }
</script>