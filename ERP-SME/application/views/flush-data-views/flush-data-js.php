<script type="text/javascript">
    var company_id = '<?=$companyID?>';
    var header_tbl = flush_tbl = errLog_tbl = null;
    var current_obj = null;
    var $det_content = $('.det-content');
    var $flushDet_dialog = $('#flushDet-dialog');
    var $log_content = $('#log-content');

    $(document).ready(function () {
        get_flush_headers();
    });

    function get_flush_headers() {
        header_tbl = $('#headers_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?=site_url('Flush_data/fetch_flush_headers'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [                
                {"targets": [ 3, 4 ], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "createdDate"},
                {"mData": "flushStatus_lable"},
                {"mData": "action"}               
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'company_id', 'value':  company_id});

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

    function save_flushHeader(e){     
        e.preventDefault();
        let post_data = $('#frm-flushHeader').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?=site_url('Flush_data/save_flush_header'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                ajax_toaster(data);

                if(data[0] == 's'){
                    reset_flushHeader_frm();
                    header_tbl.ajax.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }    

    function edit_flushHeader(obj){
        let det = get_dataTable_det('headers_table', obj);
        
        $('#flushHeader-frm-title').text(' Update Flush Header ');
        $('#flush_id').val(det.id);
        $('#description').val(det.description);
        $('#frm-flushHeader').hide().fadeIn('slow');
    }

    function view_det(obj){
        let det = get_dataTable_det('headers_table', obj);

        $('#flushViewID').val( det.id );
        $('#flushHeaders').text( det.description );
        get_flushable_modules( det.id )
        
        $det_content.removeClass( 'col-sm-6 col-sm-12' ).addClass( 'col-sm-6' );

        $('#flushDet-modal').modal('show');        

        errorLog_toggle();
        
    }

    function reset_flushHeader_frm(){
        $('#frm-flushHeader')[0].reset();
        $('#flush_id').val('');
        $('#flushHeader-frm-title').text(' New Flush Header ');
        $('#frm-flushHeader').hide().fadeIn('slow');
    }

    function delete_master(obj){        
        let det = get_dataTable_det('headers_table', obj);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?=site_url('Flush_data/delete_flush_header'); ?>",
            data: {'id': det.id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                ajax_toaster(data);                

                if(data[0] == 's'){                    
                    header_tbl.ajax.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
        
    }

    function get_flushable_modules(masterID) {
        flush_tbl = $('#flush_modules_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?=site_url('Flush_data/fetch_flush_modules'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [                
                {"targets": [ 2, 3 ], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "sortOrder"},
                {"mData": "moduleDes"},                
                {"mData": "flushStatus_lable"},
                {"mData": "action"}               
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'masterID', 'value':  masterID});

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

    function flush_startLoad(){   
        HoldOn.open({
            theme: 'sk-bounce',
            message: '<div id="loding-cls"> Processing, Please wait </div><div id="dynamic-content"></div>',
            content: '', 
            textColor: 'white'
        });

        setTimeout(() => {
            $('#holdon-content, #holdon-message').css('top', '15%');
        });
    }
    
    let dynamic_content = null;
    let current_flushID = null;
    let modules_arr = null;    
    function initialize_flush(obj){
        current_obj = obj;
        let det = get_dataTable_det('headers_table', obj);
        current_flushID = det.id;

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?=site_url('Flush_data/initialize_flush');?>",
            data: { 'id': current_flushID },
            cache: false,
            beforeSend: function () {  
                flush_startLoad();              
            },
            success: function (data) {
                if(data[0] == 's'){
                    modules_arr = data['module'];                    
                    dynamic_content = $('#dynamic-content');

                    console.log('Flush start : '+ new Date )
                    flush(0);
                }
                else{
                    stopLoad();
                    ajax_toaster(data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }     

    function flush(index){                
        let obj = modules_arr[ index ];        
        let str = '<br/>'+ obj.moduleDes +' <span id="flusModStatus_'+index+'"><b class="flush-loadin-cls">processing..</b></span>';
        dynamic_content.append( str );

        $.ajax({
            type: 'POST',            
            dataType: 'json',
            url: "<?=site_url('Flush_data/flush');?>",
            data: { 
                'company_id': company_id, 'masterID': current_flushID, 'moduleID': obj['moduleID']
            },
            cache: false,            
            success: function (data) {                                
                if(data[0] == 's'){
                    $('#flusModStatus_'+index).html('<i class="fa fa-check text-success"></i>');                    
                }
                else{
                    $('#flusModStatus_'+index).html('<i class="fa fa-close text-danger"></i>');                
                }         
 
                continue_flush( index );
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#flusModStatus_'+index).html('<i class="fa fa-close text-danger"></i>');                
                continue_flush(index); 
            }
        });  
    }

    function continue_flush(i){
        let nxtIndex = parseInt(i) + 1;
        if( modules_arr.length > nxtIndex){
            flush( nxtIndex );
        }
        else{    
            console.log('Flush end : '+ new Date )     
            setTimeout(() => {
                stopLoad();
                header_tbl.ajax.reload();
                view_det(current_obj);
            }, 3000);
        }
    }

    function view_error_log(obj){    
        let det = get_dataTable_det('flush_modules_table', obj);
        $det_content.removeClass( 'col-sm-6 col-sm-12' ).addClass( 'col-sm-12' );        
        $('#log-module-title').html( ' Error Log : <b>'+ det.moduleDes +'</b> ');

        errorLog_toggle();        
        
        get_error_log(det.masterID, det.moduleID);
    }

    
    function errorLog_toggle(){
        let addCls = ( $det_content.hasClass('col-sm-12') )? 'col-sm-6': 'col-sm-12';
        let remCls = ( $det_content.hasClass('col-sm-12') )? 'col-sm-12': 'col-sm-6';
        
        $det_content.addClass( addCls ).removeClass( remCls );

        $log_content.hide();
        $flushDet_dialog.removeAttr('style');
        if(addCls == 'col-sm-6'){
            $flushDet_dialog.css('width', '95%');            
            $log_content.delay(500).fadeIn();             
        }
    }

    function get_error_log(masterID, moduleID) {
        errLog_tbl = $('#errLog_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?=site_url('Flush_data/fetch_error_log'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [                
                {"targets": [ 2 ], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id_str"},
                {"mData": "created_at"},
                {"mData": "error_msg"},
                {"mData": "processed_qry"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'masterID', 'value':  masterID});
                aoData.push({'name': 'moduleID', 'value':  moduleID});

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
</script>