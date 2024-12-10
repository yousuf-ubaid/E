<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1"  data-toggle="tab" aria-expanded="false">Send Email</a></li>
        <li class=""><a href="#tab_2" onclick="load_mail_history()"  data-toggle="tab" aria-expanded="true">History</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="emailContent">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <div class="append_data_nw">
                    <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                        <div class="col-sm-1">
                            <strong>TO : </strong>
                        </div>
                        <div class="col-sm-8">
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $customerEmail?>"
                                   placeholder="example@example.com" style="margin-left: -10px">
                        </div>
                        <div class="col-sm-1 remove-tdnw">
                        </div>
                    </div>
                </div>
                <div class="append_data_nw">
                    <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                        <div class="col-sm-1">
                            <strong>CC :</strong>
                        </div>
                        <div class="col-sm-8">
                            <input type="email" name="ccemail" id="ccemail" class="form-control"
                                   placeholder="example@example.com" style="margin-left: -10px">
                        </div>
                        <div class="col-sm-1 remove-tdnw">
                        </div>
                    </div>
                </div>

                <br>
                <div class="append_data_nw">
                    <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                        <div class="col-sm-1">

                        </div>
                        <div class="col-sm-8">
                            <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                                &nbsp <strong>Invoice Attachments</strong>
                                <br><br>
                                <table class="table table-striped table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>File Name</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody class="no-padding">
                                    <?php if(!empty($attachmentdescription)) {
                                        $X = 1;
                                        foreach ($attachmentdescription as $val) { ?>
                                            <tr>
                                                <td><?php echo $X ?></td>
                                                <td> <?php echo $val['filename'] ?></td>
                                                <td><?php echo $val['description'] ?> </td>
                                                <td>
                                                    <div class="skin skin-square">
                                                        <div class="skin-section" id="extraColumns">
                                                            <input id="attachmentID" type="checkbox"
                                                                   data-caption="" class="columnSelected" name="attachmentID[]" value="<?php echo $val['attachmentID']?>">
                                                            <label for="checkbox">
                                                                &nbsp;
                                                            </label>
                                                        </div>
                                                    </div>



                                                </td>
                                            </tr>
                                            <?php
                                            $X++;

                                        }
                                    }else
                                    {
                                        echo '   <tr class="danger">
                                                    <td colspan="5" class="text-center">No Attachment Found</td>
                                                </tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="SendQuotationMail()">Send Email</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <div class="tab-pane " id="tab_2">
            <div class="modal-body">
                <table id="mailhistory" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="">#</th>
                        <th style="">documentID</th>
                        <th style="">Sent By</th>
                        <th style="">Sent to Email</th>
                        <th style="">Sent Date time</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
</script>