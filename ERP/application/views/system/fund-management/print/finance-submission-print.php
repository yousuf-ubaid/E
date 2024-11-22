<?php
$period = $masterData['fn_period_org'];
$period = date('Y F', strtotime($period));

$close_btn = '<button type="button" class="close" data-dismiss="modal">&times;</button>';

if($returnType == 'view'){  ?>
    <style>
        .header-div{
            background-color: #afc6dc;
            padding: 1%;
        }

        .details-td{ font-weight: bold; }

        legend{ font-size: 16px !important; }

        .td-main-header{
            color: #000080;
            text-transform: uppercase;
            font-weight: bold;
        }

        .index-td, .sub1{ font-weight: bold; }

        .glDescription{ padding-left: 55px !important;}

        .sub_total_rpt {
            border-top: 1px solid #D2D6DE !important;
            border-bottom: 1px solid #D2D6DE !important;
            font-weight: bold;
        }

        .total_black_rpt {
            border-top: 1px double #000000 !important;
            border-bottom: 3px double #000000 !important;
            font-weight: bold;
            font-size: 12px !important;
            background-color: #DBDBDB;
        }

        tr .hoverTr:hover{
            background-color: #dee8fc !important;
        }

        tr .hoverTr:hover.numeric{
            background-color: #dee8fc !important;
        }

        #submission-confirm-btn{
            margin-top: 15px;
        }
    </style>
<?php
} else{
    $close_btn = '';
}
?>


<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong><?php echo $masterData['company_name'] ?></strong>
            <?php echo $close_btn; ?>
        </div>
        <div class="text-center reportHeader reportHeaderColor"><?php echo $masterData['reportDes'];?></div>
        <div class="text-center reportHeaderColor"> Period - <?php echo $period ?></div>
    </div>
</div>

<table class="borderSpace report-table-condensed" id="fm-rpt-table" style="width: 100% !important; border:  0px solid">
<?php
echo $tBody;
?>
</table>

<?php
