<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#projectplanning" data-toggle="tab" onclick="loadTaskData('<?php echo $headerID?>')">Project Planning</a></li>
        <li><a href="#projectplanningreview" data-toggle="tab" onclick="getchart()">Project Planning Review</a></li>
          <li><a href="#RFI" data-toggle="tab" onclick="load_RFI_table()" >Request For Information</a></li> <!--onclick="requestForInformation('<?php echo $headerID?>')"-->
    
      
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="projectplanning">
        <div class="row ">
            <div class="col-md-12">
                <div class="text-right m-t-xs editview">
                    <button onclick="addMasterTask()" type="button" class="btn btn-sm btn-primary">
                        Add <span
                            class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>

                </div>
            </div>

            <div class="col-md-12">
                <div id="loadtaskData"></div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="projectplanningreview">
        <div class="row ">
            <div class="col-md-12">
                <div id="gantchartview"></div>
            </div>
        </div>

    </div>

    <div class="tab-pane" id="RFI">
        <div class="row ">
            <div class="col-md-12" style="margin-bottom: 10px;">
                <button class="btn btn-primary btn-sm pull-right" onclick="openRFIDoc(0)"> New RFI </button>
            </div>

            <div class="col-md-12">
                <div class="row table-responsive" >
                    <table id="rfi_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th>RFI NO</th>
                            <th>Created By</th>
                            <th>Created Date</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <!--<div id="RFI-container"></div>-->
            </div>
        </div>

    </div>   
</div>

<div class="modal fade" id="RFI_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">REQUEST FOR INFORMATION</h4>
            </div>

            <div class="modal-body" id="RFI-container">

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary next" onclick="save_project_rfi();">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let rfi_tb = null;
    $(document).ready(function () {
        loadTaskData('<?php echo $headerID?>')
    });

    function load_RFI_table(){
        rfi_tb = $('#rfi_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_RFI_docs'); ?>",
            "aaSorting": [[2, 'desc']],
            "columnDefs": [
                { "targets": [0,2,3,4], "orderable": false }
            ],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                let tmp_i   = oSettings._iDisplayStart;
                let iLen    = oSettings.aiDisplay.length;
                let x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "headerID"},
                {"mData": "documentCode"},
                {"mData": "createdUserName"},
                {"mData": "createdDate"},
                {"mData": "action"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'project', 'value': '<?=$headerID?>'});
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

    function openRFIDoc(id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: { project:<?=$headerID?>, headerID: id},
            url: "<?php echo site_url('Boq/load_project_RFI'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $('#RFI_modal').modal('show');
                $('#RFI-container').html(data);
            }, error: function () {

            }
        });
    }

    function save_project_rfi() {
        let data = $('#rfi_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_tem_repdetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                rfi_tb.ajax.reload();
                if(data[0]=='s') {
                    $('#tempmasterID').val(data[2]);
                }
                stopLoad();
                myAlert(data[0],data[1],data[2]);
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "", "error");
            }
        });
    }
    function generateReportPdf_rfi() {
        var form = document.getElementById('rfi_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_rfi_rec_pdf'); ?>';
        form.submit();
    }
</script>
