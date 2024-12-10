<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<section class="content-header">
    <h1>
        Mailbox
    </h1>
</section>
<?php
$currentuserid = current_userID();
$companyid = current_companyID();
$mailbox = $this->db->query("SELECT emailConfigID,isDefault,successYN FROM `srp_erp_crm_emailconfiguration` Where companyID = '{$companyid}'  AND empID = '{$currentuserid}' AND isDefault = 1")->row_array();

?>
<?php if (!empty($mailbox) && $mailbox['successYN']!='0') { ?>
    <!-- Main content -->
    <section class="content">
        <div class="row mailboxview">


            <div class="col-md-3">
                <a href="#" onclick="compose_email()"
                   class="btn btn-primary btn-block margin-bottom compose">Compose</a>
                <a href="#" onclick="backtoinbox()" class="btn btn-primary btn-block margin-bottom hide backtoinbox">Back
                    To Inbox</a>


                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Folders</h3>
                        <div class="box-tools">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li class="active" id="inbox_mail"><a href="#" onclick="getInboxMails(1)"><i
                                        class="fa fa-inbox"></i> Inbox </a></li>
                            <li id="sent_mail"><a href="#" onclick="getsentMails(1)"><i class="fa fa-envelope-o"></i>
                                    Sent </a></li>
                            <!--  <li ><a href="#"><i class="fa fa-envelope-o"></i> Sent</a></li>-->
                            <!--       <li><a href="#"><i class="fa fa-file-text-o"></i> Drafts</a></li>
                                   <li><a href="#"><i class="fa fa-filter"></i> Junk </a>
                                   </li>
                                   <li><a href="#"><i class="fa fa-trash-o"></i> Trash</a></li>-->
                        </ul>
                    </div><!-- /.box-body -->
                </div><!-- /. box -->
            </div><!-- /.col -->
            <div class="col-md-9 inboxemail">
                <div id="mailBox">
                    <div class="box box-primary emailcompose">
                        <div class="box-header with-border emailcompose">
                            <h3 class="box-title">Inbox</h3>
                            <div class="box-tools pull-right">
                                <div class="has-feedback">
                                    <input type="text" class="form-control input-sm" placeholder="Search Mail">
                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                </div>
                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="box-body no-padding emailcompose">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                               <!-- <div class="btn-group">
                                    <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                </div>--><!-- /.btn-group -->
                                <button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            </div>
                            <div class="table-responsive mailbox-messages ">
                                <div id="mailbox-table"></div>
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                        <div class="box-footer no-padding emailcompose">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                              <!--  <div class="btn-group">
                                    <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                </div>--><!-- /.btn-group -->
                                <button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    <ul class="list-inline" id="pagination-ul">

                                    </ul>
                                </div><!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                </div>
                <div id="readMailBox" style="display: none">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Read Mail</h3>
                            <div class="box-tools pull-right">
                                <a href="#" onclick="closeMail()" class="btn btn-box-tool" data-toggle="tooltip"
                                   title="Close"><i
                                        class="fa fa-history"></i></a>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="mailbox-read-info">
                            <h3 id="subject"></h3>
                            <h5>From: <span id="from"></span><span class="mailbox-read-time pull-right"
                                                                   id="date"></span>
                            </h5>
                        </div><!-- /.mailbox-read-info -->
                        <div class="box-body no-padding">
                            <iframe id="ifr" src="about:blank" width="100%" height="500" style="border:none;">

                            </iframe>
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-right">
                                <button class="btn btn-default" onclick="getReplyMail()"><i class="fa fa-reply"></i>
                                    Reply
                                </button>
                                <button class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
                            </div>
                         <!--   <button class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button>-->
                            <button class="btn btn-default"><i class="fa fa-print"></i> Print</button>
                        </div><!-- /.box-footer -->
                    </div>
                </div><!-- /.col -->
                <div id="replyMail">

                </div>


            </div><!-- /.row -->


            <div class="col-md-9 hide sentemails">

                <div id="mailBoxsent">
                    <div class="box box-primary emailcompose">
                        <div class="box-header with-border emailcompose">
                            <h3 class="box-title">Sent</h3>
                            <div class="box-tools pull-right">
                                <div class="has-feedback">
                                    <input type="text" class="form-control input-sm" placeholder="Search Mail">
                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                </div>
                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="box-body no-padding emailcompose">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                               <!-- <div class="btn-group">
                                    <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                </div>--><!-- /.btn-group -->
                                <button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                            </div>
                            <div class="table-responsive mailbox-messages ">
                                <div id="mailbox-table-sentemails"></div>
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                        <div class="box-footer no-padding emailcompose">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                                </button>
                               <!-- <div class="btn-group">
                                    <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
                                </div>--><!-- /.btn-group -->
                                <button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                                <div class="pull-right">
                                    <ul class="list-inline" id="pagination-ul_sent">

                                    </ul>
                                </div><!-- /.pull-right -->
                            </div>
                        </div>
                    </div>
                </div>


                <div id="readMailBox_sent" style="display: none">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Read Mail</h3>
                            <div class="box-tools pull-right">
                                <a href="#" onclick="closeMail_sent()" class="btn btn-box-tool" data-toggle="tooltip"
                                   title="Close"><i
                                        class="fa fa-history"></i></a>
                            </div>
                        </div><!-- /.box-header -->
                       <!-- /.mailbox-read-info -->
                        <div class="box-body no-padding">
                            <iframe id="ifrsent" src="about:blank" width="100%" height="500" style="border:none;">

                            </iframe>
                        </div><!-- /.box-body -->
               <!-- /.box-footer -->
                    </div>
                </div>


            </div>


            <div class="col-md-9 compose_send">
                <div id="mailBox">

                </div>
                <div id="composemail">

                </div>
            </div>

    </section>
<?php } else if(($mailbox['isDefault']== '1')&&($mailbox['successYN'] == '0')){ ?>
    <br>
    <?php echo warning_message('Mail Box Configured Failed Please Reconfigure The Mailbox.'); ?>
    <button class="btn btn-default" onclick="load_mailboxconfigure()"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i>Re - Configure
    </button>
<?php }else { ?>
    <br>
    <?php echo warning_message('Mailbox not configured please configure the mailbox'); ?>
    <button class="btn btn-default" onclick="configure_mailbox()"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i>Configure
    </button>
<?php }?>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="mailboxconfigure">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Mail Box Configuration</h4>
            </div>
            <?php echo form_open('', 'role="form" id="email_configuration_form"'); ?>
            <div class="modal-body">
                <input type="hidden" class="form-control " id="emailconfigid" name="emailconfigid" required>
                <!--<input type="hidden" id="userGroupID" name="userGroupID">-->
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Email</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="usernameemail" name="usernameemail" required>

                    <!--<input type="text" name="partNumber" id="partNumber" class="form-control" placeholder="Part No" >-->
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Password</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="password" class="form-control password" value="***********" id="password"
                           name="password" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Account Type</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('accounttype', array('' => 'Select Account Type', '1' => 'IMAP', '2' => 'pop3'), '', 'class="form-control select2" id="accounttype" srequired'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Email Encryption</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('encrypto', array('' => 'Select Email Encryption', '1' => 'TLS', '2' => 'SSL'), '', 'class="form-control select2" id="encrypto" required'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Host</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <input type="text" class="form-control " id="host" name="host" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Port</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <input type="text" class="form-control " id="port" name="port" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" onclick="save_email_configuration()" class="btn btn-sm btn-primary"><span
                            class="glyphicon glyphicon-floppy-disk"
                            aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="mailboxcompose">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Compose New Message</h4>
            </div>
            <?php echo form_open('', 'role="form" id="email_configuration_form"'); ?>
            <div class="modal-body">

                <input type="hidden" id="emailconfigid" name="emailconfigid">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Email</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="usernameemail" name="usernameemail" required>
                    <!--<input type="text" name="partNumber" id="partNumber" class="form-control" placeholder="Part No" >-->
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Password</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <input type="password" class="form-control password" value="***********" id="password"
                           name="password" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Account Type</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('accounttype', array('' => 'Select Account Type', '1' => 'IMAP', '2' => 'pop3'), '', 'class="form-control select2" id="accounttype" srequired'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Email Encryption</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('encrypto', array('' => 'Select Email Encryption', '1' => 'TLS', '2' => 'SSL'), '', 'class="form-control select2" id="encrypto" required'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Host</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <input type="text" class="form-control " id="host" name="host" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Port</label>
                    </div>
                    <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <input type="text" class="form-control " id="port" name="port" required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" onclick="save_email_configuration()" class="btn btn-sm btn-primary"><span
                            class="glyphicon glyphicon-floppy-disk"
                            aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- /.content -->
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var per_page = 10;
    var message_uid;
    $(document).ready(function () {
        //getPagination();
        getInboxMails(1);
        $('.select2').select2();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        getInboxMails(data_pagination, uriSegment);
    }
    function sentemailpagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        getsentMails(data_pagination, uriSegment);
    }

    function getInboxMails(page, uriSegment = 0) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {pageID: page, folder: 'INBOX'},
            url: "<?php echo site_url('CrmMailbox/getInboxMails'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#inbox_mail').addClass('active');
                $('.sentemails').addClass('hide');
                $('.inboxemail').removeClass('hide');
                $('#sent_mail').removeClass('active');
                $('#mailBox').show();
                $('#readMailBox').hide();
                $('#mailbox-table').html(data.html);
                $('#pagination-ul').html(data.pagination);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function getsentMails(page, uriSegment = 0) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {pageID: page, folder: 'SENT'},
            url: "<?php echo site_url('CrmMailbox/getsenteMails'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {


                // $('#mailBox').show();
                $('#inbox_mail').removeClass('active');
                $('.sentemails').removeClass('hide');
                $('.inboxemail').addClass('hide');
                $('#sent_mail').addClass('active');
                $('#readMailBox_sent').hide();
                $('#mailBoxsent').show();
                $('#mailbox-table-sentemails').html(data.html);
                $('#pagination-ul_sent').html(data.pagination);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function readMails(uid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {uid: uid},
            url: "<?php echo site_url('CrmMailbox/getReadMailDetail'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#subject').text(data.subject);
                $('#from').text(data.from.name);
                $('#date').text(data.date);
                message_uid = uid;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {uid: uid},
            url: "<?php echo site_url('CrmMailbox/getReadMail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#mailBox').hide();
                $('#readMailBox').show();
                getIframeDocument(document.getElementById('ifr')).body.innerHTML = data;
                //$('#mailBox').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getIframeDocument(element) {
        var doc = element.contentDocument;
        if (doc == undefined || doc == null) {
            doc = element.contentWindow.document;
        }
        return doc;
    }

    function getReplyMail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {uid: message_uid},
            url: "<?php echo site_url('CrmMailbox/getReplyMail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#replyMail').show();
                $('#replyMail').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function closeMail() {
        $('#mailBox').toggle();
        $('#readMailBox').toggle();
        $('#replyMail').hide();
    }
    function closeMail_sent() {
        $('#mailBox').toggle();
        $('#readMailBox_sent').toggle();
        $('#mailBoxsent').show();

    }

    function discardReply() {
        $('#replyMail').html('');
        $('#replyMail').hide();
    }
    function configure_mailbox() {

       $('#email_configuration_form')[0].reset();
        $('#accounttype').val(null).trigger('change');
        $('#encrypto').val(null).trigger('change');
        $('#emailconfigid').val('');
        $('#mailboxconfigure').modal('show');

    }

    function load_mailboxconfigure()
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('CrmMailbox/load_mailbox_configuretion'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {


               $('#usernameemail').val(data['details']['displayUserName']);
                $('#password').val(data['passworddecode']);
                $('#accounttype').val(data['details']['accountTypeID']).change();
                $('#encrypto').val(data['details']['encryptoID']).change();
               $('#host').val(data['details']['host']);
               $('#port').val(data['details']['port']);
               $('#emailconfigid').val(data['details']['emailConfigID']);
                $('#mailboxconfigure').modal('show');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



    function save_email_configuration() {
        var data = $('#email_configuration_form').serializeArray();
        data.push({'name': 'encryptovalue', 'value': $('#encrypto option:selected').text()});
        data.push({'name': 'accounttypevalue', 'value': $('#accounttype option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CrmMailbox/save_mailbox_configurations'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {

                    setTimeout(function () {
                        fetchPage('system/crm/mail_box.php', 'Test', 'CRM')
                    }, 50);
                    getInboxMails(1);
                    $('#mailboxconfigure').modal('hide');
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function compose_email() {
        $('.compose').addClass('hide');
        $('.backtoinbox').removeClass('hide');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {uid: message_uid},
            url: "<?php echo site_url('CrmMailbox/composeMail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('.compose_send').removeClass('hide');
                $("#inbox_mail").removeClass('active');
                $('.emailcompose').addClass('hide');
                $('#composetitle').html('Back To Inbox');
                $('#composemail').show();
                $('#composemail').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function backtoinbox() {
        $('.emailcompose').removeClass('hide');
        $('.compose').removeClass('hide');
        $('.backtoinbox').addClass('hide');
        $('.compose_send').addClass('hide');
    }


    function readMails_sent(emailid) {
        /*$.ajax({
         async: true,
         type: 'post',
         dataType: 'json',
         data: {uid: uid},
         url: "<?php echo site_url('CrmMailbox/getReadMailDetail_sent'); ?>",
         beforeSend: function () {

         },
         success: function (data) {

         message_uid = uid;
         },
         error: function (jqXHR, textStatus, errorThrown) {
         myAlert('e', '<br>Message: ' + errorThrown);
         }
         });*/

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {emailid: emailid},
            url: "<?php echo site_url('CrmMailbox/getReadMailsent'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#subject_sent').text(data['emailSubject']);
                $('#from_sent').text(data['fromEmailAddress']);
                $('#date').text(data['createdDateTime']);
                $('#mailBoxsent').hide();
                $('#readMailBox_sent').show();
                /*   $('#ifrsent').html(data);*/
                getIframeDocument(document.getElementById('ifrsent')).body.innerHTML = data;
                //$('#mailBox').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>