<?php
echo head_page('Check List', true);
$customer_arr = all_customer_drop(true);
$customer_drp = all_customer_drop();
$customer_arr_masterlevel = array('' => 'Select Customer');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$contract_type = all_contarct_types();
$gl_code_arr    = fetch_all_gl_codes();
$uom_arr    = all_umo_new_drop();
$location_arr    = op_location_drop();
$segment_arr    = fetch_segment(True);
?>
                          
<div class="table-responsive">
    <table id="checklist_master_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 7%">#</th>
            <th style="width: 66%">Name</th>
            <th style="width: 20%">Status</th>
            <th style="width: 7%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="checklist_view_modal_common" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="checklist_view_modal">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default-new size-lg" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<script type="text/javascript">
    var Otable;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operations/masters/crew_master', '', 'Crew Group');
        });
        checklist_master_table();
    });


    function checklist_master_table() {
        Otable = $('#checklist_master_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Operation/fetch_checklist_master_table'); ?>",
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
                {"mData": "id"},
                {"mData": "name"},
                {"mData": "status"},
                {"mData": "actionchk"}
            ],
            "columnDefs": [
                {
                        "targets": 0, // your case first column
                        "className": "text-center",
                        "width": "7%"
                },
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

    function load_checklist_rig_crew_inspection(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_rig_crew_inspection"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_wellsite(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_wellsite"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_field_ticket(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_field_ticket"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_daily_equipment_inspection(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_daily_equipment_inspection"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_rig_turn_over(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_rig_turn_over"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_drilling_service_rig_inspection(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_drilling_service_rig_inspection"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_system_inspection(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_system_inspection"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_well_program_steps(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_well_program_steps"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_supervisors_overview(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_supervisors_overview"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_well_program(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_well_program"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_pipe_tally(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_pipe_tally"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_power_swivel(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_power_swivel"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_fishing(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_fishing"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_rig(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_rig"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_daily_drillers(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_daily_drillers"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_daily_assistant_driller(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_daily_assistant_driller"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_crane_inspection(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_crane_inspection"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_forklift(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_forklift"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_rig_move(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_rig_move"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function load_checklist_tool_box(id,companyID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_tool_box"); ?>',
            dataType: 'html',
            data: {'id': id,'companyID': companyID},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }


    function openChecklistTemp(id,companyID) {
        if(id == 3){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_fishing(id,companyID);
        } else if(id == 4){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_rig(id,companyID);
        } else if(id == 2){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_daily_assistant_driller(id,companyID);
        } else if(id == 5){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_crane_inspection(id,companyID);
        } else if(id == 6){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_forklift(id,companyID);
        } else if(id == 7){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_rig_move(id,companyID);
        } else if(id == 8){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_tool_box(id,companyID);
        } else if(id == 9){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_power_swivel(id,companyID);
        } else if(id == 10){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_pipe_tally(id,companyID);
        } else if(id == 11){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_well_program(id,companyID);
        } else if(id == 12){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_supervisors_overview(id,companyID);
        } else if(id == 13){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_well_program_steps(id,companyID);
        } else if(id == 14){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_system_inspection(id,companyID);
        } else if(id == 15){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_daily_equipment_inspection(id,companyID);
        } else if(id == 16){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_rig_turn_over(id,companyID);
        } else if(id == 18){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_rig_crew_inspection(id,companyID);
        } else if(id == 19){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_wellsite(id,companyID);
        } else if(id == 20){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_field_ticket(id,companyID);
        } else{
            $('#checklist_view_modal_common').modal('show');
            load_checklist_daily_drillers(id,companyID);
        }
        
    }

    function updateChecklistActive(id){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': id},
            url: "<?php echo site_url('Operation/updateChecklistActive'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    
    }


</script>