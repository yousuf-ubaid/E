<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_uploads');
echo head_page($title, false);

$this->load->helper('community_ngo_helper');
$upGSDivision = load_divisionForUploads();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$current_user = current_user();

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
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

    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">


    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row">
        <div class="col-md-8 text-center">
        </div>
        <div class="col-md-4 text-right">
            <button type="button" class="btn btn-default btn-sm" style="margin-right: 2px;" onclick="fetchPage('system/communityNgo/ngo_mo_user_uploads_management','','User Uploads Management','NGO')"" ><i class="fa fa-tasks"></i>&nbsp; <?php echo $this->lang->line('communityngo_user_upload_mng');?><!--user upload--> </button>

            <button type="button" class="btn btn-primary btn-sm pull-right" style="float:right;margin-right: 2px;" onclick="openUploadData_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('communityngo_upload_new');?><!--Add--> </button>
        </div>
    </div>
    <br>
    <div class="row">

        <form method="post" name="comUploadForm" id="comUploadForm" class="">

            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

            <div class="col-md-12">

                <div class="form-group col-sm-4">
                    <label></label>
                    <div class="input-group" style="width:100% !important; height:28px !important;">
                        <button type="button" class="btn btn-default btn-xs btn-block btn-flat" id="daterange-btn" style="background-color: white; height:28px !important;">
                            <span id="spanLid"></span>
                            <i class="fa fa-caret-down pull-right" style="margin-top: 3px;"></i>
                        </button>
                    </div>
                    <div id="receiptDateDiv" style="display:none;">
                        <input type="text" name="dateUFrom" id="dateUFrom" value="">
                        <input type="text" name="dateUTo" id="dateUTo" value="">
                    </div>
                </div>
                <div class="form-group col-sm-2">

                        <label></label>
                    <select id="ComUpload1Type" class="form-control select2"
                            data-placeholder="Select Upload Type"
                            name="ComUpload1Type" onchange="get_uploads_delByDt();">
                        <option value=""></option>
                        <option value="0">All</option>
                        <option value="1">Video</option>
                        <option value="2">Audio</option>

                    </select>
                </div>
                <div class="form-group col-sm-2">

                    <label></label>
                    <select id="ComUpload1Submited" class="form-control select2"
                            data-placeholder="Select Upload Status"
                            name="ComUpload1Submited" onchange="get_uploads_delByDt();">
                        <option value=""></option>
                        <option value="0">All</option>
                        <option value="1">Expired</option>
                        <option value="2">Not Expired</option>
                        ?>
                    </select>
                </div>
                <div class="form-group col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearUpSearchFltr()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>

                </div>
            </div>

        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive mailbox-messages" id="timelineComUpload">
                <!-- /.table -->
            </div>

        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="newUpload_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeUploadMod();"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_upload_add');?><!--Add--></h4>
                </div>
                <form class="form-horizontal" id="add_comUpload_form" >
                    <input class="form-control" type="text" name="editComUploadID" id="editComUploadID" value="" style="display:none ;">

                    <div class="row modal-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_upload_type');?><!--Upload Type--> <?php required_mark(); ?></label>
                                <select id="ComUploadType" class="form-control select2" data-placeholder="Select Upload Type" name="ComUploadType">
                                    <option value=""></option>
                                    <option value="1">Vedio</option>
                                    <option value="2">Audio</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_upload_subject');?><!--Subject--> <?php required_mark(); ?></label>
                                <input type="text" class="form-control" id="ComUploadSubject" name="ComUploadSubject">

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_upload_url');?><!--Url--> <?php required_mark(); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon" onclick="paste_uploadUrl();"><i class="fa fa-paste"></i></div>
                                    <input type="text" class="form-control no-copy-paste" id="ComUpload_url"
                                           name="ComUpload_url">
                                </div>
                            </div>
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_uploader_name');?><!--Uploader Name--> <?php required_mark(); ?></label>
                                <input type="text" class="form-control" value="<?php echo $current_user ?>" id="ComUploader" name="ComUploader">
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_upload_publish');?><!--Publish--> <?php required_mark(); ?></label>
                    <div class="input-group datepic_ComUploadDate1">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                       <input onchange="$('#UploadPublishedDate').val(this.value);" type="text" name="UploadPublishedDate"
                              data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date ?>" id="UploadPublishedDate" class="form-control">
                    </div>
                            </div>
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_upload_expiredate');?><!--expiar--> <?php required_mark(); ?></label>
                    <div class="input-group datepic_ComUploadDate2">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input onchange="$('#ComUploadExpireDate').val(this.value);" type="text" name="ComUploadExpireDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="" id="ComUploadExpireDate" class="form-control">
                    </div>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_Description');?><!--Description --> <?php required_mark(); ?></label>
                                <textarea class="form-control" rows="2" id="ComUploadDescription" name="ComUploadDescription"></textarea>
                            </div>
                            <div class="form-group col-sm-6" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_GS_Division');?><!--GS Division --> <?php required_mark(); ?></label>
                                <div id="uploadGS_div">
                                <select id="uploadGSDiviID" name="uploadGSDiviID[]" class="form-control select2 uploadGSDiviID" data-placeholder="Select GS Division" multiple>
                                    <option value=""></option>
                                    <?php foreach ($upGSDivision as $upVal) { ?>
                                        <option value="<?php echo $upVal['stateID']; ?>"><?php echo $upVal['Description']; ?></option>
                                    <?php } ?>
                                </select>
                               </div>
                            </div>
                            </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-2" style="margin-right: 3px;">
                                <label>Is Submit</label>
                                <br>
                                <input type="checkbox" id="isRequired" name="isRequired" class="form-control isSubmitCheckbox" value="1" checked>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="saveCom_uploadData()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="closeUploadMod();"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $('.select2').select2();

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_communityUploads','','Community Uploads');
            });

            $('.isSubmitCheckbox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            var start=moment();
            var end =moment();

            function cb(start, end) {
                document.getElementById('dateUFrom').value = start.format('YYYY-MM-DD');
                document.getElementById('dateUTo').value = end.format('YYYY-MM-DD');
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

                get_uploads_delByDt();
            }

            $('#daterange-btn').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

          

        });

        function com_uploadEdit(ComUploadID) {

            $('.isSubmitCheckbox').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            $.ajax({
                // Send with POST mean you must retrieve it in server side with POST
                type: 'POST',
                // change this to the url of your controller
                url:"CommunityNgo/edit_comUpload",
                // Set the data that you are submitting
                data:{'ComUploadID':ComUploadID},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success:function(data){
                    $('#newUpload_modal').modal('show');

                    $('#editComUploadID').val(data['ComUploadID']);

                    $('#ComUploadType').val(data['ComUploadType']).change();
                    $('#ComUploadSubject').val(data['ComUploadSubject']).change();
                    $('#ComUpload_url').val(data['ComUpload_url']).change();

                    $('#UploadPublishedDate').val(data['UploadPublishedDate']).change();
                    $('#ComUploadExpireDate').val(data['ComUploadExpireDate']).change();

                    $('#ComUploadDescription').val(data['ComUploadDescription']).change();
                    $('#ComUploader').val(data['ComUploader']).change();

                    if(data['ComUploadSubmited'] == '0'){
                        $('#isRequired').iCheck('uncheck');
                    }else{
                        $('#isRequired').iCheck('check');
                    }



                }
            });

            $.ajax({
                // Send with POST mean you must retrieve it in server side with POST
                type: 'POST',
                // change this to the url of your controller
                url:"CommunityNgo/editGS_comUpload",
                // Set the data that you are submitting
                data:{'ComUploadID':ComUploadID},
                success:function(data){
                    $('#uploadGS_div').html(data);
                  //  $('#uploadGSDiviID').val(data['uploadGSDiviID']).change();
                    $('.uploadGSDiviID').select2();
                    $('.uploadGSDiviID').on("select2:unselecting", function (e) {
                        var DropDownID = $(this).attr('id');
                        var OptionID = (e.params.args.data.id);
                        var student = $('#'+DropDownID+' option[value="'+OptionID+'"]').text();
                    })
                }
            });

        }

        function com_uploadDelete(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
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
                        data: {'ComUploadID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_comUpload'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            //refreshNotifications(true);
                            stopLoad();
                            get_uploads_delByDt();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }
                            else if (data['error'] == 0) {
                                myAlert('s', data['message']);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function clearUpSearchFltr() {

            $('#ComUpload1Submited').val('').change();
            $('#ComUpload1Type').val('').change();
            $('#search_cancel').addClass('hide');
            get_uploads_delByDt();
        }

        function openUploadData_modal(){

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic_ComUploadDate1').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });
            $('.datepic_ComUploadDate2').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            $('#newUpload_modal').modal({backdrop: "static"});
        }

        function saveCom_uploadData(){

            var postData = $('#add_comUpload_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveUploadData_masCom'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){

                        $('#ComUploadType').val('').change();
                        $('#ComUploadSubject').val('').change();
                        $('#ComUpload_url').val('').change();

                        $('#ComUploadDescription').val('').change();
                        $('#ComUploader').val('<?php echo $current_user ?>').change();
                        $('#UploadPublishedDate').val('<?php echo $current_date ?>').change();
                        $('#ComUploadExpireDate').val('').change();
                        $('#uploadGSDiviID').val('').change();

                        $('#newUpload_modal').modal('hide');
                        get_uploads_delByDt();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })

        }

        function closeUploadMod() {

            $('#ComUploadType').val('').change();
            $('#ComUploadSubject').val('').change();
            $('#ComUpload_url').val('').change();
            $('#ComUploadDescription').val('').change();
            $('#ComUploader').val('<?php echo $current_user ?>').change();
            $('#UploadPublishedDate').val('<?php echo $current_date ?>').change();
            $('#ComUploadExpireDate').val('').change();
            $('#uploadGSDiviID').val('').change();

            $('#newUpload_modal').modal({backdrop: "static"});

        }

        function get_uploads_delByDt() {

            var dateUFrom =document.getElementById('dateUFrom').value;
            var dateUTo=document.getElementById('dateUTo').value;
            var ComUpload1Type = document.getElementById('ComUpload1Type').value;
            var ComUploadSubmited = document.getElementById('ComUpload1Submited').value;

            if(ComUpload1Type || ComUploadSubmited){
                $('#search_cancel').removeClass('hide');
            }
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                url: "<?php echo site_url('CommunityNgo/get_com_uploadsInfo'); ?>",
                data: {'dateUFrom':dateUFrom,'dateUTo':dateUTo,'ComUploadType':ComUpload1Type,'ComUploadSubmited':ComUploadSubmited},
                success: function (data) {

                    $('#timelineComUpload').html(data);

                }
            });

        }

        function paste_uploadUrl() {

        }

    </script>
<?php
