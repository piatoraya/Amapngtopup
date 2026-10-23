<?php
require '../connect.php';
require _DIR_('library/session/user');
require _DIR_('library/layout/header.user');

$benefit_target = conf('benefit-transaksi',1);
if(in_array($data_user['level'],['Basic','Premium'])) {
    $order_total = $call->query("SELECT SUM(price) AS x FROM trx_ppob WHERE user = '$sess_username' AND status = 'success' AND MONTH(date_cr) = '".date('m')."'")->fetch_assoc()['x']
    + $call->query("SELECT SUM(price) AS x FROM trx_game WHERE user = '$sess_username' AND status = 'success' AND MONTH(date_cr) = '".date('m')."'")->fetch_assoc()['x'];
   
    /* Perhitungan Persen */
    $benefit_percent = round(($order_total/$benefit_target)*100,2);
           
} else {
    $order_total = $call->query("SELECT SUM(price) AS x FROM trx_ppob WHERE user = '$sess_username' AND status = 'success' AND YEAR(date_cr) = '".date('Y')."'")->fetch_assoc()['x']
    + $call->query("SELECT SUM(price) AS x FROM trx_game WHERE user = '$sess_username' AND status = 'success' AND YEAR(date_cr) = '".date('Y')."'")->fetch_assoc()['x'];
    $benefit_percent = round(($benefit_target/$benefit_target)*100,2);   
}

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-end">
                <h4 class="mb-0">Benefit Overview</h4>
                <p class="font-medium-5 mb-0">
                    <i class="feather icon-help-circle text-muted cursor-pointer"></i>
                </p>
            </div>
            <div class="card-content">
                <div class="card-body px-0 pb-0">
                    <div id="goal-overview-chart" class="mt-75"></div>
                    <div class="row text-center mx-0">
                        <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                            <p class="mb-50">
                                Target
                            </p>
                            <p class="font-large-10 text-bold-700">
                                <?= $data_user['level'] == 'Admin' ? '<i class="mdi mdi-infinity" style="font-weight:800;"></i>' : 'Rp '.currency($benefit_target) ?>
                            </p>
                        </div>
                        <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                            <p class="mb-50">
                                Order Total
                            </p>
                            <p class="font-large-10 text-bold-700">
                                Rp <?= currency($order_total) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>
<script type="text/javascript">
$(window).on("load", function () {
    var $primary = '#7367F0';
    var $success = '#28C76F';
    var $danger = '#EA5455';
    var $warning = '#FF9F43';
    var $info = '#00cfe8';
    var $primary_light = '#A9A2F6';
    var $danger_light = '#f29292';
    var $success_light = '#55DD92';
    var $warning_light = '#ffc085';
    var $info_light = '#1fcadb';
    var $strok_color = '#b9c3cd';
    var $label_color = '#e7e7e7';
    var $white = '#fff';

    var goalChartoptions = {
        chart: {
            height: 250,
            type: 'radialBar',
            sparkline: {
                enabled: true,
            },
            dropShadow: {
                enabled: true,
                blur: 3,
                left: 1,
                top: 1,
                opacity: 0.1
            },
        },
        colors: [$success],
        plotOptions: {
            radialBar: {
                size: 110,
                startAngle: -150,
                endAngle: 150,
                hollow: {
                    size: '77%',
                },
                track: {
                    background: $strok_color,
                    strokeWidth: '50%',
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        offsetY: 18,
                        color: '#99a2ac',
                        fontSize: '4rem'
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#00b5b5'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            },
        },
        series: ['<?= ($benefit_percent > 100) ? '100' : $benefit_percent ?>'],
        stroke: {
            lineCap: 'round'
        },
    }
    var goalChart = new ApexCharts(
        document.querySelector("#goal-overview-chart"),
        goalChartoptions
    ); goalChart.render();
});
</script>