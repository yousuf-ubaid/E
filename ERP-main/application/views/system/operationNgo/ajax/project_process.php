<style>
.progress-pie-chart {
width:200px;
height: 200px;
border-radius: 50%;
background-color: #E5E5E5;
position: relative;
}
.progress-pie-chart.gt-50 {
background-color: #81CE97;
}

.ppc-progress {
content: "";
position: absolute;
border-radius: 50%;
left: calc(50% - 100px);
top: calc(50% - 100px);
width: 200px;
height: 200px;
clip: rect(0, 200px, 200px, 100px);
}
.ppc-progress .ppc-progress-fill {
content: "";
position: absolute;
border-radius: 50%;
left: calc(50% - 100px);
top: calc(50% - 100px);
width: 200px;
height: 200px;
clip: rect(0, 100px, 200px, 0);
background: #81CE97;
transform: rotate(60deg);
}
.gt-50 .ppc-progress {
clip: rect(0, 100px, 200px, 0);
}
.gt-50 .ppc-progress .ppc-progress-fill {
clip: rect(0, 200px, 200px, 100px);
background: #E5E5E5;
}

.ppc-percents {
content: "";
position: absolute;
border-radius: 50%;
left: calc(50% - 173.91304px/2);
top: calc(50% - 173.91304px/2);
width: 173.91304px;
height: 173.91304px;
background: #fff;
text-align: center;
display: table;
}
.ppc-percents span {
display: block;
font-size: 2.6em;
font-weight: bold;
color: #81CE97;
}

.pcc-percents-wrapper {
display: table-cell;
vertical-align: middle;
}

.progress-pie-chart {
margin: 50px auto 0;
}</style>

<div class="bar_container">
    <div id="main_container">
        <div id="pbar" class="progress-pie-chart" data-percent="0">
            <div class="ppc-progress">
                <div class="ppc-progress-fill"></div>
            </div>
            <div class="ppc-percents">
                <div class="pcc-percents-wrapper">
                    <span>%</span>
                </div>
            </div>
        </div>

        <progress style="display: none" id="progress_bar" value="0" max="100"></progress>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
var progressbar = $('#progress_bar');
max = progressbar.attr('max');
time = (1000 / max) * 5;
value = progressbar.val();

var loading = function() {
value += 1;
addValue = progressbar.val(value);

$('.progress-value').html(value + '%');
var $ppc = $('.progress-pie-chart'),
deg = 360 * value / 100;
if (value > 50) {
$ppc.addClass('gt-50');
}

$('.ppc-progress-fill').css('transform', 'rotate(' + deg + 'deg)');
$('.ppc-percents span').html(value + '%');

if (value == max) {
clearInterval(animate);
}
};

var animate = setInterval(function() {
loading();
}, time);
});
</script>