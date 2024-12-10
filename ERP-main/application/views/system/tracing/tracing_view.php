<style>
    @media print {
        .hortree-label {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
        }
    }
    @page
    {
        size: auto;   /* auto is the initial value */
        margin-left: 4mm;  /* this affects the margin in the printer settings */
        margin-top: 8mm;  /* this affects the margin in the printer settings */
        margin-bottom: 0mm;  /* this affects the margin in the printer settings */
    }
    </style>
<input type="hidden" id="purchaseOrderIDs" name="purchaseOrderIDs" value="<?php echo $purchaseOrderID ?>">

<input type="hidden" id="DocumentID" name="DocumentID" value="<?php echo $DocumentID ?>">

<div id="my-container"></div>

<script src="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.js'); ?>"></script>
<script src="<?php echo base_url('plugins/Horizontal-Hierarchical/demo/jquery.line.js'); ?>"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        get_tracing_data();
    });
    function get_tracing_data(){
        var id=$('#purchaseOrderIDs').val();
        var DocumentID=$('#DocumentID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'autoID': id,'DocumentID': DocumentID},
            url: "<?php echo site_url('Tracing/get_tracing_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                data = JSON.parse(data);
                stopLoad();
               // var datas=[{"description":"PO", "children":[{"description":"GRV","children":[{"description":"BSI","children":[{"description":"PV","children":[]},{"description":"PV","children":[]},{"description":"PV","children":[]}]},{"description":"BSI","children":[{"description":"PV","children":[]}]},{"description":"BSI","children":[]}]},{"description":"GRV","children":[{"description":"BSI","children":[{"description":"PV","children":[]},{"description":"PV","children":[]},{"description":"PV","children":[]}]}]},{"description":"GRV","children":[{"description":"BSI","children":[{"description":"PV","children":[]},{"description":"PV","children":[]},{"description":"PV","children":[]}]},{"description":"BSI","children":[]}]}]}];
                //var datas=[{"description":"<div style='font-weight: bold; color:white;background-color: #6F2DAB;border-bottom: solid;border-width: thin;border-color:rgb(75, 134, 183);font-size: 1.2em !important;text-align: center;'><span>Purchase Order</span></div><span style='text-align:left;cursor: pointer; font-size: 1.1em !important; font-weight:bold; ' class='texttree'><a onclick='documentPageView_modal(\"PO\",394)'>HMS/PO-2018-07-000209</a></span><br><span style='text-align:left;' class='texttree'><b>Date :- </b>2018-07-26</span><br><div><span style='text-align:left;' class='texttree'><b>Currency :-</b> OMR </span></div> <div><span style='text-align:left;' class='texttree'><b>Doc Amount :-</b> 2281.125 </span></div><div><span style='text-align:left;' class='texttree'><b>Tot Amount :-</b> 2281.125</span></div> <div><span style='text-align:left;' class='texttree'><b>Narration :-</b> \dfs</span></div>", "children":[]}];


                $("#my-container").empty();
                setTimeout(function(){
                    $('#my-container').hortree({
                    data: data
                    });
                    $("[rel=tooltip]").tooltip();
                }, 500);


            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function print_tracing_view() {
        $.print("#my-container");
    }


</script>