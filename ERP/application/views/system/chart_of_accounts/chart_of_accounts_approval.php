<?php echo head_page('Chart Of Accounts Approval',false); ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> Confirmed /
                        Approved
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                        / Not Approved
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> Refer-back
                    </td>
                </tr>
            </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0'=>'Pending','1'=>'Approved','2'=>'Reject'), '','class="form-control" id="approvedYN" required onchange="chart_of_acconts_table()"'); ?>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="chart_of_acconts_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">COA Code</th>
                <th style="min-width: 30%">Narration</th>
                <th style="min-width: 20%">Customer Name</th>
                <th style="min-width: 5%">Level</th>
                <th style="min-width: 5%">Approved</th>
                <th style="min-width: 10%">Action</th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="pv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Chart Of Accounts Approval</h4>
        </div>
        <form class="form-horizontal" id="coa_approval_form">
        <div class="modal-body">
            <div id="conform_body"></div><hr>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                <div class="col-sm-4">
                    <?php echo form_dropdown('status', array('1'=>'Approved','2'=>'Reject'), '','class="form-control" id="status" required'); ?>
                    <input type="hidden" name="Level" id="Level">
                    <input type="hidden" name="receiptVoucherAutoId" id="receiptVoucherAutoId">
                    <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Comments</label>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/chart_of_accounts/chart_of_accounts_approval','','Chart Of Accounts Approval');
    });
    chart_of_acconts_table();
    $('#coa_approval_form').bootstrapValidator({
        live            : 'enabled',
        message         : 'This value is not valid.',
        excluded        : [':disabled'],
        fields          : {
            status                  : {validators : {notEmpty:{message:'Purchase Order Status is required.'}}},
            Level                   : {validators : {notEmpty:{message:'Level Order Status is required.'}}},
            comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
            receiptVoucherAutoId    : {validators : {notEmpty:{message:'Payment Voucher ID is required.'}}},
            documentApprovedID      : {validators : {notEmpty:{message:'Document Approved ID is required.'}}}
        },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Chart_of_acconts/save_rv_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){ 
                    refreshNotifications(true);
                    $("#pv_modal").modal('hide');
                    stopLoad();
                    chart_of_acconts_table();     
                },error : function(){
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });   
    });
});

function chart_of_acconts_table(){
    var Otable = $('#chart_of_acconts_table').DataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Chart_of_acconts/fetch_chart_of_acconts_approval'); ?>",
        "aaSorting": [[1, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            if (oSettings.bSorted || oSettings.bFiltered) {
                for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                }
            }
        },
        "aoColumns": [
            {"mData": "accountID"},
            {"mData": "systemAccountCode"},
            {"mData": "AccountDescription"},
            {"mData": "masterAccountDescription"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"}
            //{"mData": "edit"},
        ],
        //"columnDefs": [{"targets": [2], "orderable": false}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({ "name": "approvedYN","value": $("#approvedYN :checked").val()});
            //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

function fetch_approval(receiptVoucherAutoId,documentApprovedID,Level){
    if (receiptVoucherAutoId) {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {'receiptVoucherAutoId':receiptVoucherAutoId,'html':true},
            url :"<?php echo site_url('Chart_of_acconts/load_rv_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                $('#receiptVoucherAutoId').val(receiptVoucherAutoId);
                $('#documentApprovedID').val(documentApprovedID);
                $('#Level').val(Level);
                $("#pv_modal").modal({backdrop: "static"});
                $('#conform_body').html(data);
                $('#comments').val('');
                stopLoad();
                refreshNotifications(true);   
            },error : function(){
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }
}


</script>