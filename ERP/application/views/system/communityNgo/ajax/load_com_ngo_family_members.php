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

    .actionicon {
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

    .headrowtitle {
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
</style>
<?php
  if (!empty($header)) { ?>
      <div class="table-responsive mailbox-messages">
          <b style="text-align: center;color: brown;font-size: 14px;"><?php echo $famName['FamilyName'] ?></b>
          <table class="table table-hover table-striped">
              <tbody>
              <tr class="task-cat-upcoming">
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Member/s</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Gender</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Date of Birth</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Relationship</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;width: 10%">Added Date</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: right">Action</td>
                  <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: right"></td>
              </tr>
              <?php
                $x = 1;
                $totalMem = 1;
                foreach ($header as $val) {
                    if($val['isMove']==1 ){ $moveStatus= '<span onclick="get_memMoved_history('.$val['Com_MasterID'].','.$val['FamMasterID'].', \'' . $val['CName_with_initials']. '\');" style="width:8px;height:8px;font-size: 0.73em;float: right;background-color: #00a5e6; display:inline-block;color: #00a5e6;" title="Moved To Another Family">m</span>'; } else{ $moveStatus=''; }
                    if($val['isActive'] ==1){ $activeState=''; } else{
                        if($val['DeactivatedFor']==2){ $INactReson='Migrate';} else{$INactReson='Death';}
                        $activeState='<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color:red; display:inline-block;color: red;" title="The Member Is Inactive :'.$INactReson.'">a</span>';}


                  ?>
                    <tr>
                        <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                        <td class="mailbox-star" width=""><?php echo $val['CName_with_initials'] ."&nbsp;". $moveStatus ."&nbsp;&nbsp;". $activeState ?></td>
                        <td class="mailbox-star" width=""><?php echo $val['name'] ?></td>
                        <td class="mailbox-star" width=""><?php echo $val['CDOB'] ?></td>
                        <td class="mailbox-star" width=""><?php echo $val['relationship'] ?></td>
                        <td class="mailbox-star"><?php echo $val['FamMemAddedDate'] ?></td>
                        <td class="mailbox-attachment taskaction_td"><span class="pull-right">
                                <?php if($val['isActive'] ==1){ ?>
                            <a onclick="edit_familyMember(<?php echo $val['FamDel_ID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                                <?php } ?>
                                &nbsp;
                            <a onclick="delete_familyMemDetails(<?php echo $val['FamDel_ID'] ?>,2)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                        </td>
                        <td class="mailbox-star">
                            <?php
                            $company_id = current_companyID();
                            $page = $this->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID = 530 AND companyID={$company_id}
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');
                            ?>
                            <span class="pull-right">
                            <a onclick="fetchPage('<?php echo $page; ?>','<?php echo $val['Com_MasterID'] ?>','Edit Member','1','<?php echo $val['FamMasterID'] ?>');"><span title="Edit Community" style="height:18px;width:20px;color: white;background-color:#51a251;text-align: center;" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                            </span>
                        </td>
                    </tr>
                  <?php

                    $totMem = $totalMem++;
                  $x++;
                }
              ?>
              </tbody>
              <tfoot >
              <tr>
                  <td style="" class="" colspan="8">Total Members : <?php echo $totMem; ?></td>
              </tr>
              </tfoot>
          </table><!-- /.table -->
      </div>
    <?php
  } else { ?>
      <br>
      <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
  }
?>

<div class="modal fade" id="mem_movedHistory_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="cmntModTitle">Member - <label style="font-size: 15px;font-weight: normal;" id="memDetail"></label></h4>
            </div>
            <div class="row modal-body">
                <label> &nbsp;&nbsp;&nbsp; <label class="glyphicon glyphicon-link"></label> Family Links</label>
                <div class="col-md-12" id="mem_movedId">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function get_memMoved_history(Com_MasterID,FamMasterID,CName_with_initials) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'Com_MasterID':Com_MasterID,'FamMasterID':FamMasterID},
            url: "<?php echo site_url('CommunityNgo/load_memberMovedHis'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#mem_movedHistory_modal').modal({backdrop: "static"});
                $('#memDetail').html(CName_with_initials);
                $('#mem_movedId').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
</script>