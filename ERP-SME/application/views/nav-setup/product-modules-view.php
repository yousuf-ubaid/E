<?php
$config = $this->input->post('config');
$selected_modules = [];
$modules_sub = [];

foreach($nav_modules_arr as $module){
    $modID = $module['moduleID'];
    $sortOrder = $module['sortOrder'];
    $checked = '';   

    if($module['checked'] == 'Y'){
        $sortOrder = $module['srOrder'];
        $checked = 'checked';   
        $selected_modules[] = (int)$modID;
    }

    $sub_view = '';
    if( array_key_exists($modID, $sub_data) ){
        $sub_view = $sub_data[$modID]['view'];
        $modules_sub[$modID] = $sub_data[$modID]['master_arr'];
    }

    ?>
        <div class="col-sm-12 prod-srh" id="pod_module_<?=$modID?>" data-heading="<?=$module['description']?>" data-module="<?=$modID?>">
            <div class="panel-group" role="tablist">
                <div class="panel panel-default">
                    <div class="panel-heading panel-heading-nav" role="tab" id="prod-nav_<?=$modID?>">
                        <h4 class="panel-title">
                            <i class="<?=$module['pageIcon']?> module-icon"></i> &nbsp; <?=$module['description']?>
                                    
                            <strong class="btn-box-tool pull-right"> 
                                <a class="" title="Toggle" role="button" data-toggle="collapse" href="#collapse-prod-nav_<?=$modID?>" 
                                    aria-expanded="true" aria-controls="collapse-prod-nav_<?=$modID?>"
                                    onclick="toggleSingle('prod_toggle_', <?=$modID?>)">
                                    <i class="fa fa-plus prod-common-collapse tool-box-icon" id="prod_toggle_<?=$modID?>"></i>
                                </a>                          
                            </strong>  
                            <span class="pull-right" style="margin-right: 100px">   
                                <?php if($config == 'Y'){ ?>                                 
                                <input type="checkbox" name="navID[]" class="prod-chk-all prod-chk-<?=$modID?>" value="<?=$modID?>" 
                                    id="module-nav-check-<?=$modID?>" onclick="check_all_nav(this, <?=$modID?>)" <?=$checked?>/>
                                <input type="text" name="sort_order[<?=$modID?>]" class="nav-sort-order number" 
                                    value="<?=$sortOrder?>" placeholder="sort order" />
                                <input type="hidden" name="moduleID[<?=$modID?>]" value="0"/>
                                <?php }     ?>
                            </span>

                            <span class="pull-right" style="margin-right: 100px">
                                <input type="text" placeholder="Search navigation" class="search_in_module" onkeyup="search_in_module(this, <?=$modID?>)"/>
                            </span>                            
                        </h4>
                    </div>
                    <div id="collapse-prod-nav_<?=$modID?>" class="panel-collapse collapse prod-collapse-body" role="tabpanel" 
                        aria-labelledby="prod-nav_<?=$modID?>" aria-expanded="true" style="">
                        <ul class="list-group" id="prod-nav-sub-list-<?=$modID?>">
                            <?=$sub_view?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
<?php
}
?>

<script>
    let nav_toggle = $('#nav-toggle');
    let nav_common_collapse = $('.nav-common-collapse');
    let nav_common_body = $('.nav-collapse-body');

    let prod_toggle = $('#prod-toggle');
    let prod_common_collapse = $('.prod-common-collapse');
    let prod_common_body = $('.prod-collapse-body');

    selected_modules = <?=json_encode($selected_modules)?>;
    modules_sub = <?=json_encode($modules_sub)?>;    

    function fn_nav_common_collapse(fnBtn){

        if( fnBtn === 'nav' ){

            if( nav_toggle.hasClass('fa-minus') ){
                nav_toggle.removeClass('fa-minus').addClass('fa-plus');
                nav_common_collapse.removeClass('fa-minus').addClass('fa-plus');
                nav_common_body.removeClass('in');
            }
            else{
                nav_toggle.removeClass('fa-plus').addClass('fa-minus');
                nav_common_collapse.removeClass('fa-plus').addClass('fa-minus');
                nav_common_body.addClass('in');
            }

        }        
        else{

            if( prod_toggle.hasClass('fa-minus') ){
                prod_toggle.removeClass('fa-minus').addClass('fa-plus');
                prod_common_collapse.removeClass('fa-minus').addClass('fa-plus');
                prod_common_body.removeClass('in');
            }
            else{
                prod_toggle.removeClass('fa-plus').addClass('fa-minus');
                prod_common_collapse.removeClass('fa-plus').addClass('fa-minus');
                prod_common_body.addClass('in');
            }

        }
    }

    function toggleSingle(prefix,id){
        let obj = $('#'+prefix+''+id);

        if( obj.hasClass('fa-minus') ){
            obj.removeClass('fa-minus').addClass('fa-plus');
        }
        else{
            obj.removeClass('fa-plus').addClass('fa-minus');
        }
    }
    

    function check_sub_navs(obj, id, moduleID){
        let chk = ( $(obj).prop('checked') )? true: false;
        $('.sub-nav-check-'+id).prop('checked', chk);

        if(chk){
            $('#module-nav-check-'+moduleID).prop('checked', true);
            addRemove_selectedModules(moduleID, true);
        }
        else{
            module_nav_check(moduleID);
        }
    }

    function check_master_navs(obj, id, moduleID){
        let chk = ( $(obj).prop('checked') )? true: false;
        let chkCount = 0;
        
        $('.sub-nav-check-'+id).each((i, sub) => {
            if( $(sub).prop('checked') ){
                chkCount++;
            }
        });

        $('#sub-nav-master-'+id).prop('checked', (chkCount > 0));

        if( chkCount > 0 ){
            $('#module-nav-check-'+moduleID).prop('checked', true);
            addRemove_selectedModules(moduleID, true);
        }
        else{
            module_nav_check(moduleID);
        }
    }

    function selected_modules_only(){
        $('#nav-search').val('');

        $('.prod-srh').show(); 
        let _status = $('#drop-module-disp').val();
        if(_status == 'selected'){            
            $('.prod-srh').hide();
            $.each(selected_modules, (i, modID) => {                
                $('#pod_module_'+modID).show();            
            });
        }        
    }

    function module_nav_check(moduleID){
        let chkCount = 0;
        $.each( modules_sub[moduleID], (i, subItems) => {            
            if( $('#sub-nav-master-'+subItems).prop('checked') ){
                chkCount++;
            }
        });
        
        let isChecked = (chkCount > 0);
        $('#module-nav-check-'+moduleID).prop('checked', isChecked);
        
        addRemove_selectedModules(moduleID, isChecked);
    }

    function addRemove_selectedModules(moduleID, isChecked){
        let modIndex = selected_modules.findIndex( elm => elm === moduleID );
        if( isChecked ){
            if(modIndex == -1){ // if module not in the array
                selected_modules.push( moduleID );
            }            
        }
        else{            
            selected_modules.splice( modIndex , 1);
        }
    }

    function check_all_nav(obj, id){
        let chk = ( $(obj).prop('checked') )? false: true;

        $.each( modules_sub[id], (i, subItems) => {            
            $('#sub-nav-master-'+subItems).prop('checked', chk).click();                   
        });

        let isChecked = (!chk);
        addRemove_selectedModules(id, isChecked);
    }

    let prod_srh = $('.prod-srh');
    let no_data_container = $('#search-no-data');
    function search_prod_nav(isManualTrigger=false){
        let searchStr = $.trim( $('#nav-search').val() );
        no_data_container.hide();
        
        if( searchStr === '' ){            
            prod_srh.show();
            $('#drop-module-disp').change();            
            return false;
        }
        
        prod_srh.hide();            
        searchStr = searchStr.toLowerCase();

        let j = 0;
        let displayOnlySelectedModule = ( $('#drop-module-disp').val() == 'selected' )? true: false;
        
        prod_srh.each((i, obj) => {
            navText = $.trim( $(obj).data('heading') );
            navText = navText.toLowerCase();
            
            if( displayOnlySelectedModule ){
                let this_moduleID = $(obj).data('module');                
                if( !selected_modules.includes( this_moduleID ) ){
                    return true;
                }                
            }

            if( navText.indexOf(searchStr) !== -1 ){
                $(obj).show();
                j = 1;
            }                
        });        
        
        if( j === 0 ){
            no_data_container.show();
        }               
    }

    function search_in_module(srObj, module){        
        let searchStr = $.trim( $(srObj).val() );        

        if( searchStr === '' ){            
            $('.module-search-li-'+module).show();
            return false;
        }
        
        $('.module-search-li-'+module).hide();            
        searchStr = searchStr.toLowerCase();

        let j = 0;
        $('.module-search-li-'+module).each((i, obj) => {
            navText = $.trim( $(obj).data('search') );
            navText = navText.toLowerCase();
            
            if( navText.indexOf(searchStr) !== -1 ){
                $(obj).show();
                j = 1;
            }                
        });
                     
    }

$(function() {
    var slideToTop = $("<div />");
    slideToTop.html('<i class="fa fa-chevron-up"></i>');
    slideToTop.css({
        position: 'fixed',
        bottom: '50px',
        right: '75px',
        width: '40px',
        height: '40px',
        color: '#eee',
        'font-size': '',
        'line-height': '40px',
        'text-align': 'center',
        'background-color': '#222d32',
        cursor: 'pointer',
        'border-radius': '5px',
        'z-index': '99999',
        opacity: '.7',
        'display': 'none'
    });

    slideToTop.on('mouseenter', function () {
        $(this).css('opacity', '1');
    });

    slideToTop.on('mouseout', function () {
        $(this).css('opacity', '.7');
    });

    $('#wrap-top').append(slideToTop);

    $(window).scroll(function () {
        if ($(window).scrollTop() >= 150) {
        if (!$(slideToTop).is(':visible')) {
            $(slideToTop).fadeIn(500);
        }
        } else {
        $(slideToTop).fadeOut(500);
        }
    });

    $(slideToTop).click(function () {        
        $("html,body").animate({
            scrollTop: 0
        }, 500);
    });

    $(".sidebar-menu li:not(.treeview) a").click(function () {
        var $this = $(this);
        var target = $this.attr("href");
        if (typeof target === 'string') {
        $("body").animate({
            scrollTop: ($(target).offset().top) + "px"
        }, 500);
        }
    });
});
</script>