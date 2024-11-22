<script type="text/javascript">    
    let nav_modules = $('#nav_modules');    
    let add_nav_form = $('#add_nav_form');    
    let pr_module = $('#pr_module');
    let sub_module = $('#sub_module');
    let pageIcons = $('#pageIcons');
    let page_url = $('#page_url');
    let sortOrder = $('#sortOrder');
    let isReport = $('#isReport');
    let max_sort_order = 1;
    let module_sort_order = parseInt(<?=$module_sort_order?>);
    let is_reload_required = false;    

    let selected_modules = [];
    let modules_sub = {};

    $(document).ready(function () {
        sortOrder.numeric({decimal: false, negative:false});
        $('.select2').select2();
       
        $("#select2-nav-srch").select2({
            placeholder: "Navigations search",
            minimumInputLength: 3,
            ajax: {
                url: "<?=site_url('Dashboard/search_nav');?>",
                dataType: 'json',
                type: "GET",                
                data: function (params) {
                    return {
                        searchKey: params.term
                    };
                },
                processResults: function (data) {                 
                    return {
                        results: data.items
                    };
                }
            }
        });

        load_font_awesome_list();
    });

    function add_navigation(isEdit=false){
        $('.nav-cus-btn').hide();
        let title = 'Add New Navigation';

        if(isEdit){
            title = 'Edit Navigation';
            $('.nav-cus-btn').show();
        }

        $('.disable-input').prop('disabled', isEdit);    
        $('#title_nav_modal').text(title);
        $('#nav_edit_id').val(0);
        $('#add_nav_modal').modal('show');
        add_nav_form[0].reset();
        pr_module.val('').change();
        pageIcons.change();
        $('.common-select').change();
    }

    function isReport_change(){
        $('#reportID').prop('readonly', true);
        if( isReport.val() == 1 ){
            $('#reportID').prop('readonly', false);
        }
        else{
            $('#reportID').val('');
        }
    }

    function save_navigation(){
        let post_data = add_nav_form.serializeArray();
        let url_slag = ($('#nav_edit_id').val() > 0)? '_update': '_save';

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?=site_url('Dashboard/erp_navigation');?>"+url_slag,
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();            
                
                if( data[0] == 's' ){
                    $('#add_nav_modal').modal('hide');
                                        
                    if(url_slag == '_save'){
                        if( data['new_module'] !== null ){
                            module_sort_order += 1;
                            let nav_des = $('#description').val();
                            pr_module.append( '<option value="'+data['new_module']+'"> '+nav_des+' </option>' );                     
                        }

                        if( data['failed_db'] !== null ){
                            failDB_msg( data['failed_db'], 'insert' );
                        }
                        else{
                            setTimeout( () => {
                                you_want_more();
                            }, 100);
                        }
                    }
                    else{
                        if( data['failed_db'] !== null ){
                            failDB_msg( data['failed_db'], 'update' );
                        }
                        else{
                            swal(
                                {
                                    title: "",
                                    text: "Navigation updated successfully",
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",                                
                                    confirmButtonText: "Ok"
                                },
                                function () {
                                    window.location.href = "<?=site_url('nav_setup');?>";
                                }
                            );
                        }
                    }                    
                                                   
                }
                else{
                    ajax_toaster( data );
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function edit_nav(){
        let id = $('#select2-nav-srch').val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/get_navigation_det');?>",
            data: {'nav_id': id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                if(data[0] == 's'){                    
                    add_navigation(true);
                    
                    let nav_data = data['nav_data'];

                    $('#nav_edit_id').val( id );
                    $('#description').val( nav_data['description'] );
                    pr_module.val( nav_data['moduleID'] ).removeAttr('onchange').change().attr('onchange', 'load_sub_menus()');                    
                    sortOrder.val( nav_data['sortOrder'] );
                    pageIcons.val( nav_data['pageIcon'] ).change();
                    $('#isBasic').val( nav_data['basicYN'] ).change();
                    $('#isGroup').val( nav_data['isGroup'] ).change();
                    $('#isExternal').val( nav_data['isExternalLink'] ).change();
                    $('#page_url').val( nav_data['url'] );
                    $('#isReport').val( nav_data['isReport'] ).change();
                    $('#reportID').val( nav_data['pageID'] );
                    $('#createPage').val( nav_data['createPageLink'] );

                    /* $('#documentCode').val( nav_data['documentCode'] ).change();
                    $('#templateKey').val( nav_data['templateKey'] ); */

                    if(nav_data['levelNo'] != 2){
                        $('#manage-btn').hide();
                    }
                    
                    load_sub_menus( nav_data['masterID'] );

                    stopLoad();
                }
                else{
                    stopLoad();          
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_sub_menus(sub_id=null){
        let module_id = pr_module.val();
        sub_module.empty();

        if(module_id === ''){
            sortOrder.val( module_sort_order );
            sub_module.html('<option value=""> None </option>');
            return false;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/load_nav_sub_modules');?>",
            data: {'module_id': module_id, 'sub_id': sub_id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){                    
                    sub_module.html(data['html']);    
                    max_sort_order = data['sort_order'];

                    if($('#nav_edit_id').val() == 0){
                        sortOrder.val( max_sort_order );
                    }
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function sub_menus_data(){
        let sub_module_id = sub_module.val();        
            
        if(sub_module_id === ''){
            sortOrder.val( max_sort_order );       
            return false;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/get_nav_sub_module_sortOrder');?>",
            data: {'sub_module_id': sub_module_id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){                     
                    sortOrder.val( data['sort_order'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }
    
    function load_font_awesome_list(){
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            async: true,
            url: "<?= base_url('plugins/font-awesome/font-awesome-4.7.0.json'); ?>",                        
            beforeSend: function () {
                startLoad();            
            },
            success: function (data) {
                pageIcons.append( '<option value=""> Select a Icon </option>' );
                $.each(data, (i, fn) =>{                    
                    pageIcons.append( '<option value="fa '+fn+'"> '+fn+' </option>' );
                });                 
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });

        setTimeout( () => {
            pageIcons.select2({
                templateResult: formatIcons,
                templateSelection: formatIcons,
            });
        }, 300);
    }

    function formatIcons (obj) {
        var $state = $(
            '<span><i class="fa ' + obj.text +'"/> &nbsp; &nbsp; '+obj.text+' </span>'
        );
        return $state; 
    };

    function failDB_msg(data, type){
        let msg = '<b>Please note that, Fail to ';
        msg += (type == 'pr_nav_save')? 'update': type;
        msg += ' the navigation for following DB`s.</b>';
        msg += '<br/> &nbsp; - &nbsp; '+data;

        bootbox.alert({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Warning!</strong>',
            message: msg, 
            callback: function() {
                if(type == 'insert'){
                    you_want_more();
                }
                if(type == 'update' || type == 'delete'){ 
                    window.location.href = "<?=site_url('nav_setup');?>";
                }                
            }
        });
    }
 
    function you_want_more(isEdit){
        swal(
            {
                title: "",
                text: "Navigation added successfully, You want to add more navigations",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "No",
                confirmButtonText: "Yes"
            },
            function (isConf) {
                if(isConf){
                    is_reload_required = true;
                    add_navigation();
                }
                else{                    
                    window.location.href = "<?=site_url('nav_setup');?>";
                }
            }
        );
    }

    function nav_setup(){
        return true;
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/load_setup');?>",
            data: {'sub_module_id': sub_module_id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){                     
                    sortOrder.val( data['sort_order'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function save_product_nav(){
        let postData = $('#frm-nav-product').serializeArray();

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '<?=site_url('Dashboard/save_product_nav')?>',
            data: postData,
            beforeSend: function () {
                $('#view-modal-title').html('');
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    if( data['failed_db'] !== null ){
                        failDB_msg( data['failed_db'], 'pr_nav_save' );
                        return false;
                    }                                       
                }

                ajax_toaster(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });        
    }

    function view_module_nav(modID){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '<?=site_url('Dashboard/nav_module_view')?>',
            data: {'id': modID, 'config': 'V'},
            beforeSend: function () {
                $('#view-modal-title').html('');
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    let title = data['title'];                    
                    $('#ajax-nav-view').html( data['view'] );
                    $('#view-modal-title').html( '<i class="'+title['pageIcon']+'"></i>' + ' &nbsp; ' + title['description'] );
                    $('#nav-view-modal').modal('show');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function close_nav_modal(){
        if( is_reload_required ){   
            window.location.href = "<?=site_url('nav_setup');?>";
        }
    }

    function load_product_nav(obj){
        $('#wrap-top').hide();
        $('.prod-chk-all').prop('checked', false);
        

        if(obj.value == ''){
            return false;
        }

        $.ajax({
            type: 'post',
            dataType: 'JSON',            
            url: "<?= site_url('Dashboard/load_product_modules');?>",
            data: {'productID': obj.value, 'config': 'Y'},
            cache: false,
            beforeSend: function () {                
                startLoad();     

                prod_toggle.removeClass('fa-plus').addClass('fa-minus');    
                fn_nav_common_collapse('prod');
                $('#drop-module-disp').val('all');  

            },
            success: function (data) {
                stopLoad();            
                $('#wrap-top').show();
                if( data[0] == 's' ){
                    $('#pr-module-container').html(data['view']);                                    
                }                           
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }
    
    function common_btn_display(disSpan){
        $('.common-btn-top').hide();
        $('#'+disSpan).show();
    }

    function delete_navigation(){
        let id = $('#nav_edit_id').val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/delete_navigation');?>",
            data: {'nav_id': id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $('#add_nav_modal').modal('hide');

                    if( data['failed_db'] !== null ){
                        failDB_msg( data['failed_db'], 'delete' );
                    }
                    else{
                        swal(
                            {
                                title: "",
                                text: data[1],
                                type: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",                            
                                confirmButtonText: "Ok"
                            },
                            function (isConf) {
                                window.location.href = "<?=site_url('nav_setup');?>";
                            }
                        );
                    }                    
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function load_manage_view(){
        let id = $('#nav_edit_id').val();
        $.ajax({
            type: 'get',
            dataType: 'JSON',
            url: "<?= site_url('Dashboard/load_template_management_view');?>",
            data: {'nav_id': id },
            cache: false,
            beforeSend: function () {
                startLoad();                
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $('#add_nav_modal').modal('hide');
                    
                    $('#temp_management_content').html( data['view'] );
                    $('#temp_management_modal').modal('show');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }
</script>