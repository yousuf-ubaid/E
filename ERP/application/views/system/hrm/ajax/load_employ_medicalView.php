<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//echo fetch_account_review(false, true, $approval);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

?>

<div class="row" style="">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <button class="btn btn-primary btn-sm" id="save_medical" onclick="load_medical_edit_details(1)" type="button">Add New
            </button>
        </div>
    </div>
</div>
<hr>
    <div class="table-responsive">
        <table id="medical_view_table" style="width: 100%" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr text-left' style="min-width: 5%"><strong>#</strong></th>
                <th class='theadtr text-left' style="min-width: 20%"><strong>Employee Code</strong></th>
                <th class='theadtr text-left' style="min-width: 20%"><strong>Name</strong></th>
                <th class='theadtr text-left' style="min-width: 20%"><strong>Start Date</strong></th>
                <th class='theadtr text-left' style="min-width: 20%"><strong>To Date</strong></th>
                <th class='theadtr text-center' style="min-width: 15%"><strong>Action</strong></th>
            </tr>
            </thead>
            <tbody id="medical_view_table_body">
            <?php 
        
            $x = 1;
            if (!empty($information)) {
                foreach ($information as $val) {
                        echo '<tr>';
                            echo '<td>' . $x . '</td>';
                            echo '<td>' . $val['empCode'] . '</td>';
                            echo '<td>' . $val['empName'] . '</td>';
                            echo '<td>' . $val['fromDate']. '</td>';
                            echo '<td>' . $val['toDate']. '</td>'; 
                                $status = '<a onclick="load_medical_edit_details(2,' . $val['id'] . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                                $status .= '<a target="_blank" onclick="view_medical_details(' . $val['id'] . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                                $status .= '<a target="_blank" href="' . site_url('Employee/load_medical_details_print/') . '/' . $val['id'] . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> &nbsp;&nbsp;| &nbsp;&nbsp;';
                                $status .= '<a onclick="delete_medical_details(' . $val['id'] . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                            echo '<td class="text-right">'. $status .'</td>';                    
                        echo '</tr>';
                        
                    $x++;
                }
            } else {
                $norec=$this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="6" class="text-center"><b>'.$norec.'<!--No Records Found--></b></td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>


<script>
    
    function load_medical_edit_details(viewType, medicalInformationID = null){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, 'medicalInformationID': medicalInformationID, 'viewType' : viewType},
            url: '<?php echo site_url("Employee/load_medical_edit_details"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#medicine_tab').html('');
                $('#medicine_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function view_medical_details(medicalInformationID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, 'medicalInformationID': medicalInformationID, 'viewType' : 3},
            url: '<?php echo site_url("Employee/load_medical_edit_details"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#medicine_tab').html('');
                $('#medicine_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function delete_medical_details(medicalInformationID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'medicalInformationID': medicalInformationID},
                    url: "<?php echo site_url('Employee/delete_medical_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', 'Medical record deleted successfully');
                        fetch_medical_details();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>