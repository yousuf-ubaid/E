<?php $modules = $extra['module'];
$companies = fetch_all_companies();
?>

<style>
    #mainContainer{
        min-height: 700px
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Modules</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="Type">Company </label>
                        <?php echo form_dropdown('companyID', $companies, '', 'class="form-control"  id="companyID" required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="company_module_view">

                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div id="moduleDetail" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 80%">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Module Detail</h4>
                </div>
                <div class="modal-body">
                    <form id="form_nav" class="form-horizontal">
                        <input type="hidden" id="navigationMenuID" name="navigationMenuID">
                        <input type="hidden" id="companyidhn" name="companyid">
                        <!-- Textarea -->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="textarea">Description</label>
                            <div class="col-md-9">
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="singlebutton"></label>
                            <div class="col-md-6">
                                <button onclick="submitupdate()" id="submit" type="button"
                                        class="btn btn-primary btn-xs">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="attachment_modal_label">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">&nbsp;</div>
                        <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="attachment_uplode_form" class="form-inline"'); ?>
                                <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription" placeholder="Description...">
                                <input type="hidden" class="form-control" id="documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="documentID" name="documentID">
                                <input type="hidden" class="form-control" id="document_name" name="document_name">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                              class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                              class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                              class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                          aria-hidden="true"></span></span><span
                                              class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                             aria-hidden="true"></span></span><input
                                              type="file" name="document_file" id="document_file"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                                </form></span>
                        </div>
                    </div>
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="attachment_modal_body" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center">No Attachment Found</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        $('#companyID').change(function () {
            get_company_module_view();
        });

    });
    function get_company_module_view() {
        $.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: "<?php echo site_url('Dashboard/showAllmodules'); ?>",
            data: {companyid: $('#companyID').val()},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#company_module_view').html(data);
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No Database Selected :)", "error");
            }
        });
        return false;
    }
</script>

