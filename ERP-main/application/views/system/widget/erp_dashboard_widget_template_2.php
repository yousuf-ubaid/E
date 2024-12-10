<!---- =============================================
-- File Name : erp_item_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item counting.

-- REVISION HISTORY
-- =============================================-->
<style>
    .box .overlay> button {
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -15px;
        margin-top: -11px;
    }
</style>
<div style="border:  1px solid;padding: 5px">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body1T"  style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay1T"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(1,1,'1T',1)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body1C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay1C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,2,'1C',3)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body2C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay2C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,3,'2C',4)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body3C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay3C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,4,'3C',5)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body4C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay4C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,5,'4C',6)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body5C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay5C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,6,'5C',7)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body6C" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay6C"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(2,7,'6C',10)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning" style="border: 1px dashed #b9bdb6;">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body" id="body1F" style="display: block;width: 100%">
                </div>
                <div class="overlay" id="overlay1F"><button type="button" class="btn btn-primary btn-xs" onclick="load_widget(1,8,'1F',1)"><i class="fa fa-plus"></i> Add
                    </button></div>
                <input type="hidden" name="userdashboardWidget[]" value="">
            </div>
        </div>
    </div>
</div>
