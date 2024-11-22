<?php
//$module_arr = fetch_all_modules();
$companies = fetch_all_companies();
?>
<style>
    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;

    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 13px;

    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 12px;
        padding-left: 10px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;

    }

    ul {
        list-style-type: none;
    }

    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        width: 10% !important;
    }

    /*tree css*/

     ul.naves {
        margin-bottom: 2px !important;
       /* font-size: 12px;*/ /* to change font-size, please change instead .lbl */
    }
     ul.naves ul,
     ul.naves ul li {
        list-style: none!important;
        list-style-type: none!important;
        margin-top: 1px;
        margin-bottom: 1px;
    }
     ul.nav ul {
        padding-left: 0;
        width: auto;
    }
     ul.nav ul.children {
        padding-left: 12px;
        width: auto;
    }
     ul.nav ul.children li{
        margin-left: 0px;
    }
     ul.nav li a:hover {
        text-decoration: none;
    }

     ul.nav li a:hover .lbl {
         color: #dc1515!important;
         /*padding-left: 9px;*/
    }

     ul.nav li.current>a .lbl {
        background-color: #999;
        color: #fff!important;
    }

    /* parent item */
     ul.nav li.parent a {
        padding: 0px;
        color: #ccc;
         margin-bottom: 3px;
    }
     ul.nav>li.parent>a {
        border: solid 1px #999;
        text-transform: uppercase;
    }
     ul.nav li.parent a:hover {
        background-color: #fff;
        -webkit-box-shadow:inset 0 3px 8px rgba(0,0,0,0.125);
        -moz-box-shadow:inset 0 3px 8px rgba(0,0,0,0.125);
        box-shadow:inset 0 3px 8px rgba(0,0,0,0.125);
    }

    /* link tag (a)*/
     ul.nav li.parent ul li a {
        color: #222;
        border: none;
        display:block;
        padding-left: 5px;
    }

     ul.nav li.parent ul li a:hover {
        background-color: #fff;
        -webkit-box-shadow:none;
        -moz-box-shadow:none;
        box-shadow:none;
    }

    /* sign for parent item */
     ul.nav li .sign {
        display: inline-block;
        width: 27px;
        padding: 5px 8px;
        background-color: transparent;
        color: #fff;
    }
     ul.nav li.parent>a>.sign{
        margin-left: 0px;
        background-color: #999;
    }

    /* label */
     ul.nav li .lbl {
        padding: 5px 12px;
        display: inline-block;
    }
     ul.nav li.current>a>.lbl {
        color: #fff;
    }
     ul.nav  li a .lbl{
        font-size: 14px;
    }

    /* THEMATIQUE
    ------------------------- */
    /* theme 1 */
     ul.nav>li.item-1.parent>a {
        border: solid 1px #ff6307;
    }
     ul.nav>li.item-1.parent>a>.sign,
     ul.nav>li.item-1 li.parent>a>.sign{
        margin-left: 0px;
        background-color: #ff6307;
    }
     ul.nav>li.item-1 .lbl {
        color: #ff6307;
    }
     ul.nav>li.item-1 li.current>a .lbl {
        background-color: #ff6307;
        color: #fff!important;
    }

    /* theme 2 */
     ul.nav>li.item-8.parent>a {
        border: solid 1px  #d7e7ec !important;
    }
     ul.nav>li.item-8.parent>a>.sign,
     ul.nav>li.item-8 li.parent>a>.sign{
        margin-left: 0px;
        background-color: #51c3eb;
    }
     ul.nav>li.item-8 .lbl {
        color: #164758;
    }
    ul.nav>li.item-8 li.current>a .lbl {
        background-color: #51c3eb;
        color: #fff!important;
    }

    /* theme 3 */
    ul.nav>li.item-15.parent>a {
        border: solid 1px #94cf00;
    }
    ul.nav>li.item-15.parent>a>.sign,
    ul.nav>li.item-15 li.parent>a>.sign{
        margin-left: 0px;
        background-color: #94cf00;
    }
    ul.nav>li.item-15 .lbl {
        color: #94cf00;
    }
    ul.nav>li.item-15 li.current>a .lbl {
        background-color: #94cf00;
        color: #fff!important;
    }

    /* theme 4 */
    ul.nav>li.item-22.parent>a {
        border: solid 1px #ef409c;
    }
    ul.nav>li.item-22.parent>a>.sign,
    ul.nav>li.item-22 li.parent>a>.sign{
        margin-left: 0px;
        background-color: #ef409c;
    }
    ul.nav>li.item-22 .lbl {
        color: #ef409c;
    }
    ul.nav>li.item-22 li.current>a .lbl {
        background-color: #ef409c;
        color: #fff!important;
    }

    ul.nav li .sign2 {
        display: inline-block;
        width: 27px;
        padding: 5px 12px;
        background-color: transparent;
        color: #fff;
    }

    ul.nav>li.item-8.parent>a>.sign2,
    ul.nav>li.item-8 li.parent>a>.sign2{
        margin-left: 0px;
        background-color: #51c3eb;
    }

    ul.nav li .signs {
        display: inline-block;
        width: 27px;
        padding: 5px 8px;
        background-color: transparent;
        color: #fff;
    }

    ul.nav>li.item-8.parent>a>.signs,
    ul.nav>li.item-8 li.parent>a>.signs{
        margin-left: 0px;
        background-color: #51c3eb;
    }



</style>
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Template Setup</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="Type">Company </label>
                        <?php echo form_dropdown('companyID', $companies, '', 'class="form-control"  id="companyID" required'); ?>
                    </div>
                    <div class="form-group col-sm-8">
                        <button type="submit" id="addmenubtn" style="margin-top: 24px;" onclick="AddMenu()" class="btn btn-primary btn-sm pull-right">Add Menu</button>
                    </div>
                </div>
                <hr>
                <div class="form-group" id="div_reload">

                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        /*$('.headerclose').click(function(){
         fetchPage('system/navigation/erp_navigation_group_setup','','Navigation Group Setup');
         });*/
        $('#companyID').change(function () {
            loadform();
        });
        $('#addmenubtn').addClass("hidden");
    });

    function loadform() {
        if($('#companyID').val()==''){
            $('#div_reload').html('');
            $('#addmenubtn').addClass("hidden");
        }else{
            $('#addmenubtn').removeClass("hidden");
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {companyid: $('#companyID').val()},
                url: "<?php echo site_url('Dashboard/load_navigation_usergroup_setup'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_reload').html(data);
                    stopLoad();

                }, error: function () {

                }
            });
        }
    }

    function AddMenu() {
        $('#type').val('');
        hiderelaventfields();
        $('#navigation_menu_form')[0].reset();
        $("#navigation_menu_add_model").modal({backdrop: "static"});
    }

    function hiderelaventfields(levl){
      var type= $('#type').val();
        $('#typedv').removeClass("hidden");
        $('#leveldv').removeClass("hidden");
        if(type==1){
            $('#level').val(0);
            $('#level').attr('disabled',true);
            $('#mastersdv').addClass('hidden');
            $('#modulesdv').addClass('hidden');
            $('#urldv').addClass('hidden');
            $('#url').val('#');
            /*$('#subexist').val(0);
            $('#subexist').attr('disabled',true);*/
        }else{
            if(levl){
                $('#level').val(levl);
            }else{
                $('#level').val(1);
            }
            $('#level').attr('disabled',false);
            $('#mastersdv').removeClass('hidden');
            $('#modulesdv').removeClass('hidden');
            $('#urldv').removeClass('hidden');
            /*$('#subexist').val(1);
            $('#subexist').attr('disabled',false);*/
            hidemaster()
        }
    }

    function showURL(){
        var subexist=$('#subexist').val();
        if(subexist==1){
            $('#urldv').addClass('hidden');
        }else{
            $('#urldv').removeClass('hidden');
        }
    }

    function hidemaster(){
        var level= $('#level').val();
        if(level==1){
            $('#mastersdv').addClass('hidden');
            $('#modulesdv').removeClass('hidden');
            $('#modules').val('');
            $('#masters').empty();
        }else if(level==2){
            $('#mastersdv').removeClass('hidden');
            $('#modulesdv').removeClass('hidden');
        }else{
            $('#type').val(1);
            $('#mastersdv').addClass('hidden');
            $('#modulesdv').addClass('hidden');
            $('#urldv').addClass('hidden');
            $('#modules').val('');
            $('#masters').empty();
        }
    }

    function loadMaster(){
        var modules= $('#modules').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Dashboard/load_master"); ?>',
            dataType: 'json',
            data: {'modules': modules,companyid: $('#companyID').val()},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#masters').empty();
                    var mySelect = $('#masters');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['navigationMenuID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function saveNavigation(){

        var type= $('#type').val();
        if(type==1){
            $('#level').attr('disabled',false);
            //$('#subexist').attr('disabled',false);
        }
        var data=$('#navigation_menu_form').serializeArray();
        data.push({'name':'companyid', 'value':$('#companyID').val()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Dashboard/save_navigation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    if(type==1){
                        $('#level').attr('disabled',true);
                       // $('#subexist').attr('disabled',true);
                    }
                    myAlert(data[0],data[1]);
                    if(data[0]=="s"){
                        $("#navigation_menu_add_model").modal('hide');
                        loadform();
                    }

                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    !function ($) {

        // Le left-menu sign
        /* for older jquery version
         $('#left ul.nav li.parent > a > span.sign').click(function () {
         $(this).find('i:first').toggleClass("icon-minus");
         }); */
        $(document).on("click"," ul.nav li.parent > a > span.sign", function(){
            $(this).find('i:first').toggleClass("fa-minus");
        });

        // Open Le current menu
        $(" ul.nav li.parent.active > a > span.sign").find('i:first').removeClass("fa fa-plus");
        $(" ul.nav li.parent.active > a > span.sign").find('i:first').addClass("fa fa-minus");
        $(" ul.nav li.current").parents('ul.children').addClass("in");

    }(window.jQuery);

    function open_add_singl_nav_model(type,level,navigationMenuID,masterID){
        hiderelaventfields();
        if(level==1){
            $('#type').val(type);
            $('#level').val(level);
            hiderelaventfields();
            $('#modules').val(navigationMenuID);
            $('#modulesdv').addClass("hidden");
            $('#typedv').addClass("hidden");
            $('#leveldv').addClass("hidden");
        }else if(level==2){
            $('#type').val(type);
            $('#level').val(level);
            hiderelaventfields(level);
            $('#modules').val(navigationMenuID);
            loadMaster();
            $('#masters').val(masterID);
            $('#modulesdv').addClass("hidden");
            $('#mastersdv').addClass("hidden");
            $('#typedv').addClass("hidden");
            $('#leveldv').addClass("hidden");
        }

        $("#navigation_menu_add_model").modal({backdrop: "static"});
    }


</script>