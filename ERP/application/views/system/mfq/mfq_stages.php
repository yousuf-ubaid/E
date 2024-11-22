<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #stage-add-tb td{ padding: 2px; }
    #weigth-add-tb td{padding:11px}
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage); 
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_stages');
echo head_page($title  , false);


?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-7 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openStage_modal()" ><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('common_add');?><!--Add--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="load_stages" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_stage');?><!--Stage--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_DefaultType');?><!--DefaultType--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_weightage');?><!--weightage--></th>
            <th style="width: 60px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="new_stage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_add_stages');?><!--Add Stage--></h4>
            </div>
            <form class="form-horizontal" id="add-stage_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="stage-add-tb">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_stage');?><!--Stage--></th>
                                <th><?php echo $this->lang->line('common_DefaultType');?><!--DefaultType--></th>
                                <th><?php echo $this->lang->line('common_weightage');?><!--DefaultType--></th>
                                <th>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more()" ><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="description[]" id="description" class="form-control saveInputs new-items">
                            </td>
                            <td>
                                <select name="default_type[]" class="form-control">
                                    <option value="" >Select</option>
                                    <option value="PR">PR</option>
                                    <option value="PO">PO</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="WeiAge[]" id="WeiAge" class="form-control numeric" value="0" onkeypress="return validateFloatKeyPress(this,event)">
                            </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_stage()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="cancelstages()"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="new_weightage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelweigthage()"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_checklist');?><!--Add weightage--></h4>
            </div>
            <form class="form-horizontal" id="add-stage_form" >
                <div class="modal-body">
                    <table class="table table-bordered" id="weigth-add-tb">
                    <input type="hidden" id="WID" class="form-control">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_checklist_description');?><!--weightage--></th>
                                <th>
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_weight()" ><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" name="weightage[]" id="weightage" class="form-control">

                            </td>

                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_weitage()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="cancelweigthage()"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var stage_tb = $('#stage-add-tb');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/mfq/mfq_stages');
        });
       
    });
    load_stages();
    function load_stages(selectedRowID=null){
        var Otable = $('#load_stages').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_stage'); ?>",
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
                {"mData": "stage_name"},
                {"mData": "stage_name"},
                {"mData": "DefaultType"},
                {"mData": "weightage"},
                {"mData": "delete"}
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


    function openStage_modal(){
        cancelstages();
        $('#stage-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('.saveInputs').val('');
        $('#new_stage').modal({backdrop: "static"});
    }

    function save_stage(){
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });
        if (errorCount == 0) {
            var descriptions = document.getElementsByName('description[]');
            var defaultTypes = document.getElementsByName('default_type[]');
            var weiAges = document.getElementsByName('WeiAge[]');

            var data = [];

            for (var i = 0; i < descriptions.length; i++) {
                var description = descriptions[i].value.trim() !== '' ? descriptions[i].value : null;
                var weightage = weiAges[i].value.trim() !== '' ? weiAges[i].value : null;
                var defaultType = defaultTypes[i].value.trim() !== '' ? defaultTypes[i].value : null;
                var defaultTypeMapped = defaultType === 'PR' ? 1 : defaultType === 'PO' ? 2 : null;

                data.push({ description: description, default_type: defaultTypeMapped,weightage:weightage });
            }

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('MFQ_CustomerInquiry/savestage'); ?>',
                data: { stages: data },
              //  dataType: 'json',
                beforeSend: function() {
                    startLoad();
                },
                success: function(response) {
                    stopLoad();
                    var message = response.message;
                    myAlert('s',data);
                    load_stages();
                    $('#new_stage').modal('hide');
                    cancelstages();
                },
                error: function() {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
                }
            });
        } else {
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>'); 
        }
    }

   

    function delete_stage(id){
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
                    // async : true,
                    url :"<?php echo site_url('MFQ_CustomerInquiry/deleteStages'); ?>",
                    type : 'post',
                   // dataType : 'json',
                    data : {'stageid':id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert('s', data);  
                        load_stages();
                        $('#new_stage').modal('hide');
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
        var appendData = '<tr>';
        appendData += '<td><input type="text" name="description[]" class="form-control saveInputs new-items" /></td>';
        appendData += '<td><select name="default_type[]" class="form-control">';
        appendData += '<option value="" disabled selected>Select</option>';
        appendData += '<option value="PR">PR</option>';
        appendData += '<option value="PO">PO</option>';
        appendData += '</select></td>';
        appendData += '<td><input type="text" name="WeiAge[]" id="WeiAge" class="form-control saveInputs numeric" value="0" onkeypress="return validateFloatKeyPress(this,event)"></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td>';
        appendData += '</tr>';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';
        stage_tb.append(appendData);
    }

    function validateFloatKeyPress(el, evt) {

        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }

        if(number.length>1 && charCode == 46){
            return false;
        }


        var dotPos = el.value.indexOf(".");

        return true;
    }


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function addweihtage(id){
    $.ajax({
            async : true,
            url :"<?php echo site_url('MFQ_CustomerInquiry/weightage'); ?>",
            type : 'post',
            dataType : 'json',
            data : {'stageid':id},
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                cancelstages();
                cancelweigthage();
                $('#WID').val(id);
                // foreach(data as row){
                //     $('#').val(id);
                // 

                $.each(data, function(i, item) {

                    var tr = '<tr><td>'+item.checklistDescription+'</td><td><a class="btn btn-danger"><i class="fa fa-trash"></i></a></td></tr>';
                    $('#weigth-add-tb').append(tr);
               
                });
                // Need to set the records
                $('#new_weightage').modal('show');
                
               
            },error : function(){
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }
    function add_weight() {
        var appendrow = '<tr><td><input type="text" name="weightage[]" class="form-control  new-items" /></td>';
        appendrow += '<td align="center" style="vertical-align: middle">';
        appendrow += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';
        $('#weigth-add-tb tbody').append(appendrow);
    }

    function save_weitage() {
        var stageid = $('#WID').val();
        var weightages = []; 
        $('input[name="weightage[]"]').each(function() {
            weightages.push($(this).val());
        });

        $.ajax({
            async: true,
            url: "<?php echo site_url('MFQ_CustomerInquiry/saveweightage'); ?>",
            type: 'post',
            dataType: 'text',
            data: {'stageid': stageid, 'weightages': weightages},
            beforeSend: function () {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert('s', data);
                $('#new_weightage').modal('hide');
                cancelweigthage();
            },
            error: function(xhr, status, error) {
                stopLoad();
                myAlert('e', 'Error while saving weightage');
            }
        });
    }

    function cancelweigthage(){
        $('#new_weightage input[type="text"]').val('');
        $('#weigth-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
    }

    function cancelstages(){
        $('#new_stage input[type="text"]').val('');
        $('#new_stage select').val('');
    }



</script>


<?php
