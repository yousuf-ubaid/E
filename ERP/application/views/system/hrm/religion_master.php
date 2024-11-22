<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #religion-add-tb td{ padding: 2px; }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_others_master_religion_master');
echo head_page($title  , false);


?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openReligion_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_religions" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_religion');?><!--Religion--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_religion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_add_religion');?><!--Add Religion--></h4>
            </div>
            <form class="form-horizontal" id="add-religion_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="religion-add-tb">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_religion');?><!--Religion--></th>
                                <th>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="description[]" class="form-control saveInputs new-items" />
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_religion()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_others_master_edit_religion_description');?><!--Edit Religion Description--></h4>
            </div>

            <form role="form" id="editReligion_form" class="form-horizontal">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="religionDes" name="religionDes">
                            <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    </div>
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="updateReligion()"><?php echo $this->lang->line('common_update');?><!--Update--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var religion_tb = $('#religion-add-tb');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/religion_master','Test','HRMS');
        });
        load_religions();
    });

    function load_religions(selectedRowID=null){
        var Otable = $('#load_religions').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_religion'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                /*if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                        if( parseInt(oSettings.aoData[i]._aData['RId']) == selectedRowID ){
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


                    if( parseInt(oSettings.aoData[x]._aData['RId']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "RId"},
                {"mData": "Religion"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,2]}],
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

    function openReligion_modal(){
        $('#religion-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_religion').modal({backdrop: "static"});
    }

    function save_religion(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });

        if(errorCount == 0){
            var postData = $('#add-religion_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('Employee/saveReligion'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#new_religion').modal('hide');
                        load_religions();
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

    function edit_religion(id, des){
        $('#editModal').modal({backdrop: "static"});
        $('#hidden-id').val( $.trim(id) );
        $('#religionDes').val( $.trim(des) );
    }

    function delete_religion(id, description){
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
                    url :"<?php echo site_url('Employee/deleteReligion'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_religions() }
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
        var appendData = '<tr><td><input type="text" name="description[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        religion_tb.append(appendData);
    }

    function updateReligion(){
        var postData = $('#editReligion_form').serialize();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/editReligion'); ?>',
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
                    load_religions( $('#hidden-id').val() );
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
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-03
 * Time: 11:25 AM
 */