<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #828282;
        font-weight: bold;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }
</style>
<?php
if (!empty($header)) {
if($header['isClosed'] == 1){
?>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
    <button type="button"  class="btn btn-primary pull-right"
                    onclick="check_warning()">
                <span title="" rel="tooltip" class="glyphicon" data-original-title="Edit"></span>
                Edit
            </button>
    </div>
</div>
<br>
<?php
} else { ?>
<div class="row">
    <div class="col-md-5">
        &nbsp;
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="check_edit_approval()"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span> Edit
        </button>
    </div>
</div>
<br>

<?php
}
?>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>CAMPAIGN DETAILS</h2>
            </header>
        </div>
    </div>
    <table class="property-table">
        <tbody>
        <tr>
            <td class="ralign"><span class="title">Document Code</span></td>
            <td><span class="tddata"><?php echo $header['documentSystemCodecamp']; ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Campaign Name</span></td>
            <td><span class="tddata"><?php echo $header['name']; ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Category</span></td>

            <td><span class="label"
                      style="background-color:<?php echo $header['backGroundColor'] ?>; color: <?php echo $header['textColor'] ?>; font-size: 11px;"><?php echo $header['categoryDescription'] ?></span>
            </td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Objective</span></td>
            <td><span class="tddata"><?php echo $header['objective']; ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Status</span></td>
            <td><span class="label" style="background-color:#9e9e9e; color:#ffffff; font-size: 11px;"><?php echo $header['statusDescription'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Start Date</span></td>
            <td><span class="tddata"><?php echo $header['startDate'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">End Date</span></td>
            <td><span class="tddata"><?php echo $header['endDate'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Assigned To</span></td>
            <td><span class="tddata"><?php
                    if (!empty($taskAssignee)) {
                        foreach ($taskAssignee as $row) {
                            echo $row['Ename2'] . " ,";
                        }
                    }
                    ?></span>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
   <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>CAMPAIGN ATTENDEES</h2>
            </header>
        </div>
    </div>
    <table class="property-table" style="width: 75% !important;" cellspacing="5" cellpadding="5">
        <tbody>
        <?php
                    if (!empty($taskAttendees)) {
                    $i=1;
                        foreach ($taskAttendees as $more) {
                        if( $i == 1){
                        echo '<tr>';
                        }
                        ?>
                            <td style="padding-left: 7%; padding-top: 1%;"><span class="tddata"><?php echo $more['firstName']." ".$more['lastName']; ?></span></td>
                            <?php if($more['isAttended'] == 1){  ?>
                                <td style="padding-top: 1%;"><span class="label" style="background-color:#8bc34a; color:#ffffff; font-size: 11px;">YES</span></td>
                            <?php
                             } else { ?>
                              <td style="padding-top: 1%;"><span class="label" style="background-color:#f1754e; color:#ffffff; font-size: 11px;">NO</span></td>
                              <?php  } ?>

                            <?php
                        if( $i == 3){
                        echo '</tr>';
                        $i = 0;
                        }
                            $i++;
                        }
                    }
                    ?>
        </tbody>
    </table>
    <br>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>CAMPAIGN DESCRIPTION</h2>
            </header>
        </div>
    </div>
    <table class="property-table">
        <tbody>
        <tr>
            <td style="padding-left: 5%;"><span class="tddata"><?php echo $header['description'] ?></span></td>
        </tr>
        </tbody>
    </table>
    <br>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>RECORD DETAILS</h2>
            </header>
        </div>
    </div>
    <table class="property-table">
        <tbody>
        <tr>
            <td class="ralign"><span class="title">Date Completed</span></td>
            <td><span class="tddata"><?php echo $header['completedDate'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Completed By</span></td>
            <td><span class="tddata"><?php echo $header['completedBy'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Last Updated</span></td>
            <td><span class="tddata"><?php echo $header['updateDate'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Created By</span></td>
            <td><span class="tddata"><?php echo $header['createdbY'] ?></span></td>
        </tr>
        </tbody>
    </table>
    <br>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>CAMPAIGN COMMENTS</h2>
            </header>
        </div>
    </div>
    <?php echo form_open('', 'role="form" id="campaign_view_comment_form"'); ?>
    <div class="row">
        <div class="col-md-12">
            <textarea class="form-control" rows="4" name="campaigncomment" id="campaigncomment"><?php echo  $header['comment']; ?></textarea>
            <input type="hidden" name="campaignID" value="<?php echo $header['campaignID']; ?>" >
        </div>
    </div>
    <br>
    <div class="row">
        <div class="form-group col-sm-6">
            <button class="btn btn-primary" type="submit">Add</button>
            <button class="btn btn-danger" type="button" onclick="campaign_attendees_close()">Close</button>
        </div>
        <div class="form-group col-sm-6" style="margin-top: 10px;">
            &nbsp
        </div>
    </div>
    </form>
    <?php
}
?>


<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $("#campaigncomment").wysihtml5();

        $('#campaign_view_comment_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //campaign_name: {validators: {notEmpty: {message: 'Campaign Name is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Crm/update_campaign_edit_view_comment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        $('.btn-primary').prop('disabled', false);
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });
    function check_edit_approval() {
        if (<?php echo $this->common_data['current_userID']?>==<?php if (!empty($superadmn)) {echo $superadmn['isadmin'];} else {echo 000;}  ?>) {
            fetchPage('system/crm/create_new_campaign',<?php echo $header['campaignID'] ?>,'Edit Campaign','CRM');
        } else if (<?php echo $this->common_data['current_userID']?>==<?php echo $header['crtduser'] ?>) {
            fetchPage('system/crm/create_new_campaign',<?php echo $header['campaignID'] ?>,'Edit Campaign','CRM');
        } else if (<?php echo $tskass ?> == 1) {
            fetchPage('system/crm/create_new_campaign',<?php echo $header['campaignID'] ?>,'Edit Campaign','CRM');
        } else {
            myAlert('w', 'You do not have permission to edit this campaign')
        }
    }

    function check_warning(){
            
                myAlert('w','You canot Edit this Campaign. This Campaign has been closed')
            
                            }
</script>


