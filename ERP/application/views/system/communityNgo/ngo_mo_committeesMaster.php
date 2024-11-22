<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #Committee-add-tb td{ padding: 2px; }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_CommitteesMaster');
echo head_page($title  , false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_active');?><!--Active-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_in_active');?><!--In-Active-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right CA_Alter_btn" onclick="openCommittee_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_Committees" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('communityngo_Committee');?><!--Committee--></th>
            <th style="width: 60px"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="width:90px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_Committee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_AddCommitteeMas');?><!--Add Committee--></h4>
            </div>
            <form class="form-horizontal" id="add-Committee_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="Committee-add-tb">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('communityngo_Committee');?><!--Committee--></th>
                            <th>
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="Committee[]" class="form-control saveInputs new-items" />
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_Committee()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_EditCommitteeMas');?><!--Edit Committee Description--></h4>
            </div>

            <div class="modal-body">
                <form role="form" id="editCommittee_form" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="CommitteeDes" name="CommitteeDes">
                                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                   <input type="radio" name="status" value="1" id="active" class="status" checked="checked">
                                                </span>
                                                <input type="text" class="form-control" value="<?php echo $this->lang->line('common_active');?>" disabled><!--Active-->
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                   <input type="radio" name="status" value="0" id="in-active" class="status" >
                                                </span>
                                                <input type="text" class="form-control" value="<?php echo $this->lang->line('common_in_active');?>" disabled><!--In-Active-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateCommittee()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Committee_tb = $('#Committee-add-tb');

    $(document).ready(function() {

        //control_staff_access(0, 'system/communityNgo/ngo_mo_committeesMaster', 0);

        load_Committees();
        $('.headerclose').click(function(){
            fetchPage('system/communityNgo/ngo_mo_committeesMaster','Test','Community Ngo');
        });
    });

    function load_Committees(selectedRowID=null){
        var Otable = $('#load_Committees').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_CommitteeMas'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                /*if (oSettings.bSorted || oSettings.bFiltered) {
                 for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                 $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                 if( parseInt(oSettings.aoData[i]._aData['CommitteeID']) == selectedRowID ){
                 var thisRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
                 $(thisRow).addClass('dataTable_selectedTr');
                 }
                 }
                 }*/


                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['CommitteeID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "CommitteeID"},
                {"mData": "CommitteeDes"},
                {"mData": "status"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function openCommittee_modal(){
        $('#Committee-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_Committee').modal({backdrop: "static"});
    }

    function save_Committee(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#add-Committee_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveCommitteeMas'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_Committee').modal('hide');
                        load_Committees();
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })
        }
        else{
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');/*Please fill all fields*/
        }
    }

    function editCommitteeMas(id, des, status){
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#CommitteeDes').val( $.trim(des) );
        $('.status').prop('checked', false);

        if( status == 1 ){
            $('#active').prop('checked', true);
        }else{
            $('#in-active').prop('checked', true);
        }
    }

    function deleteCommitteeMas(id, description){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('CommunityNgo/deleteCommitteeMas'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_Committees() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });

    function add_more(){
        var appendData = '<tr><td><input type="text" name="Committee[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        Committee_tb.append(appendData);
    }

    function updateCommittee(){
        var postData = $('#editCommittee_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('CommunityNgo/editCommitteeMas'); ?>',
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success :function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#editModal').modal('hide');
                    load_Committees($('#hidden-id').val());
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        })

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>

<?php
