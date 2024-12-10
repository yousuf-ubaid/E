<?php

if($returnType == 'view'){  ?>
    <style>
        .header-div{
            background-color: #afc6dc;
            padding: 1%;
        }

        .period-header{
            background: #9ccff4;
            font-size: 12px !important;
            font-weight: bold;
            text-align: center;
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
<?php } ?>


<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong><?php echo $company_name ?></strong>
        </div>
        <div class="text-center reportHeader reportHeaderColor">Income Statement</div>
        <div class="text-center reportHeaderColor"> Year - <?php echo $year ?></div>
    </div>
</div>

<table class="borderSpace report-table-condensed" id="fm-rpt-table" style="width: 100% !important; border:  0px solid">
    <?php echo $tBody; ?>
</table>

<?php
