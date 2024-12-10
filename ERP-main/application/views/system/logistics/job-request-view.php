<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>

<div class="" id="">
    <table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
        <tbody>
        <tr>
            <td style="width: 110px;">Document Code </td>
            <td class="bgWhite" style="width:35%" ><?php echo $jobrequestDetail['Documentcode'] ?></td>
            <td style="width: 110px;">Customer Name</td>
            <td colspan="2" class="bgWhite"><?php echo $jobrequestDetail['CustomerName'] ?></td>
        </tr>
        <tr>
            <td>Reference No</td>
            <td class="bgWhite"><?php echo $jobrequestDetail['BLLogisticRefNo'] ?></td>
            <td>Container No</td>
            <td class="bgWhite" colspan="2"><?php echo $jobrequestDetail['containerNo'] ?></td>
        </tr>
        <tr>
            <td>Shipping Line</td>
            <td class="bgWhite"><?php echo $jobrequestDetail['shippingLine'] ?></td>
            <td>Type of Service</td>
            <td class="bgWhite" colspan="2"><?php echo $jobrequestDetail['serviceType'] ?></td>
        </tr>
        <tr>
            <td>Arrival Date</td>
            <td class="bgWhite"><?php echo $jobrequestDetail['arrivalDate'] ?></td>
            <td>Booking No</td>
            <td class="bgWhite" colspan="2"><?php echo $jobrequestDetail['bookingNumber'] ?></td>
        </tr>
        <tr>
            <td>Bayan System Status</td>
            <td class="bgWhite"><?php echo $jobrequestDetail['statusDescription'] ?></td>
            <td>Encode By</td>
            <td class="bgWhite" colspan="2"><?php echo $jobrequestDetail['Ename2'] ?></td>
        </tr>
        <tr>
            <td>Remainder in Days</td>
            <td class="bgWhite"><?php echo $jobrequestDetail['reminderInDays'] ?></td>
            <td>House Reference No</td>
            <td class="bgWhite" colspan="2"><?php echo $jobrequestDetail['internalRefNo'] ?></td>
        </tr>
        </tbody>
    </table>
    <br>
    <div>
    <div class="row " id="add_attachemnt_show">
        <?php echo form_open_multipart('', 'id="logistic_attachment_form_2" class="form-inline"'); ?>
        <div class="col-sm-12" style="margin-left: 3%">
            <div class="col-sm-4">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="document_ID" class="title">Document</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <select class="filters select2 form-control" required name="document_ID" id="document_ID">
                            <?php
                            if(!empty($documentdrop))
                            {
                                foreach ($documentdrop as $valdocdrop) {
                                    echo '<option value="' . $valdocdrop['docID'] . '">' . $valdocdrop['description'] . '</option>';
                                }
                            }else {
                                echo '<option value=" ">Select Document</option>';
                            }
                            ?>
                            </select>

                            <span class="input-req-inner"></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="Description..." style="width: 240%;">

                    <input type="hidden" class="form-control" id="jobID" name="jobID" value="<?php echo $jobID; ?>">
                </div>
            </div>
            <div class="col-sm-4" style="margin-top: -8px;">
                <div class="form-group">
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                         style="margin-top: 8px;">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                            <span class="fileinput-filename"></span></div>
                        <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new"><span class="glyphicon glyphicon-plus"   aria-hidden="true"></span></span>
                        <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat"  aria-hidden="true"></span></span>
                        <input   type="file" name="document_file" id="document_file"></span>
                        <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                           data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                          aria-hidden="true"></span></a>
                    </div>
                </div>
                <button type="button" class="btn btn-default" onclick="logistic_document_uplode_2()"><span
                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>

            </div>
        </div>

        <?php echo form_close(); ?>
    </div>

    <table class="table table-striped table-condensed table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
            <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
            <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="job_request_attachment_2" class="no-padding">
        <tr class="danger">
            <?php /*
            if(!empty($attachmentDetails))
            {
                $x=1;
                foreach ($attachmentDetails as $val) {
                echo '<tr><td id=" '.$val['attachmentID'].' ">' . $x . '</td><td>'. $val['myFileName'] .'</td><td>'. $val['attachmentDescription'] .'</td><td>'. $val['description'] .'</td>
                         <td class="text-center"><a target="_blank" href="' . $x . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_job_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
                $x++;
                }
            }else {
           // echo '<option value=" ">Select Document</option>';
            echo "<tr><td colspan=\"5\" class=\"text-center\">No Attachment Found </td></tr>";
            }
 */
            ?>

        </tr>
        </tbody>
    </table>
    </div>
</div>




<script type="text/javascript">
    $('.select2').select2();

</script>