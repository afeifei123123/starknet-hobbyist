<?php
include './admin/php/api.php';
class index extends _api
{
    public $dbdata = [
        'servername' => '127.0.0.1',
        'username' => 'web',
        'password' => 'NnhsND3Rm6w3CNjY',
        'dbname' => 'web',
        'port' => '3306'
    ];
    public function _echarts()
    {
        $conn = $this->c(1, $this->dbdata);
        $sql = "SELECT DATE_FORMAT(time_moren, '%Y-%m-%d %H:00:00') as hour, MIN(zhuanzhang) as min_zhuanzhang FROM hangqing WHERE time_moren >= NOW() - INTERVAL 10 DAY GROUP BY hour";
        $result = $conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date = date('Y-m-d', strtotime($row['hour']));
                $host = date('H', strtotime($row['hour']));
                $data[] = [
                    $date,
                    intval($host),
                    floatval($row['min_zhuanzhang'])
                ];
            }
        }
        $conn->close();

        $start = date("Y-m-d", strtotime("-10 day"));
        $end = date("Y-m-d");
        $count = round((strtotime($end) - strtotime($start)) / 3600 / 24);
        $date = [];
        for ($i = $count; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime($end . " -" . $i . " day"));
            $date[] = $day;
        }

        $hour = [];
        for ($i = 0; $i < 24; $i++) {
            $hour[] = $i;
        }

        $this->res('ok', 1, [
            'date' => $date,
            'hour' => $hour,
            'data' => $data
        ]);
    }

    public function _two()
    {
        $conn = $this->c(1, $this->dbdata);
        $sql = "SELECT DATE_FORMAT(time_moren, '%Y-%m-%d %H:00:00') as hour, MIN(swap) as min_swap FROM hangqing WHERE time_moren >= NOW() - INTERVAL 10 DAY GROUP BY hour";
        $result = $conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date = date('Y-m-d', strtotime($row['hour']));
                $host = date('H', strtotime($row['hour']));
                $data[] = [
                    $date,
                    intval($host),
                    floatval($row['min_swap'])
                ];
            }
        }
        $conn->close();

        $start = date("Y-m-d", strtotime("-10 day"));
        $end = date("Y-m-d");
        $count = round((strtotime($end) - strtotime($start)) / 3600 / 24);
        $date = [];
        for ($i = $count; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime($end . " -" . $i . " day"));
            $date[] = $day;
        }

        $hour = [];
        for ($i = 0; $i < 24; $i++) {
            $hour[] = $i;
        }

        $this->res('ok', 1, [
            'date' => $date,
            'hour' => $hour,
            'data' => $data
        ]);
    }
};
$web = new index(1);
$web->method('');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>
        Starknet Gas
    </title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo $web->v; ?>" />
    <link rel="stylesheet" type="text/css" href="./css/style.css?v=<?php echo $web->v; ?>" />
    <script src="https://www.layuicdn.com/layui/layui.js?v=<?php echo $web->v; ?>"></script>
    <style>
        .price>.title {
            font-size: 30px;
            font-weight: bold;
            margin-top: 50px;
            color: #ffffff;
        }

        .price>.desc {
            font-size: 16px;
            margin-top: 20px;
            color: #ffffff;
        }

        .chart {
            height: 600px;
            margin-top: 30px;
        }

        #chart2 {
            margin-bottom: 30px;
        }

        .current {
            color: #ffffff;
            text-align: center;
            margin-top: 30px;
            font-size: 30px;
            position: relative;
            height: 35px;
            line-height: 35px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include './header.php'; ?>
    <div class="main">
        <div class="price">
            <div class="title">
                <span>Starknet Transfer Gas</span>
            </div>
            <div class="desc">
                <span>starknet转账消耗的GAS，单位是美元，北京时间UTC+8（GAS consumed by starknet transfer, in USD，The table time is UTC+8）</span>
            </div>
            <div class="current">
                <span>Latest transfer gas:$</span>
                <span class="latest_price">0.0</span>
            </div>
            <div id="chart1" class="chart"></div>
            <div class="title">
                <span>Starknet Swap Token Gas</span>
            </div>
            <div class="desc">
                <span>Starknet Swap Token大概消耗的GAS，单位是美元，北京时间UTC+8（The approximate gas consumption of starknet swap Token GAS, in US dollars，The table time is UTC+8）。</span>
            </div>
            <div class="current">
                <span>Latest Swap Token gas :$</span>
                <span class="latest_price">0.0</span>
            </div>
            <div id="chart2" class="chart"></div>
        </div>
    </div>
    <?php include './footer.php'; ?>
</body>
<script type="text/javascript" src="https://cdn.staticfile.org/echarts/5.4.0/echarts.min.js"></script>
<script>
    const $ = layui.jquery;
    const char1 = {
        init() {
            const c = window.echarts.init($('#chart1')[0]);
            const s = (r, i) => {
                if (!i) {
                    c.clear();
                    c.setOption({
                        grid: {
                            left: 50,
                            top: 0,
                            right: 0,
                            bottom: 100
                        },
                        xAxis: {
                            type: "category",
                            data: r.data.date,
                            axisTick: {
                                show: !1
                            },
                            axisLine: {
                                show: !1
                            },
                            axisLabel: {
                                lineHeight: 16
                            }
                        },
                        yAxis: {
                            type: "category",
                            data: r.data.hour,
                            axisTick: {
                                show: !1
                            },
                            axisLine: {
                                show: !1
                            },
                            axisLabel: {
                                formatter: function(e) {
                                    return e < 10 ? "0".concat(e, ":00") : "".concat(e, ":00")
                                }
                            }
                        },
                        visualMap: {
                            bottom: 0,
                            left: "center",
                            align: "top",
                            orient: "horizontal",
                            min: 0,
                            max: 1000,
                            pieces: [{
                                    min: 0,
                                    max: 0.09,
                                    color: '#C2D9FB'
                                },
                                {
                                    min: 0.1,
                                    max: 0.19,
                                    color: '#CBFDC2'
                                },
                                {
                                    min: 0.2,
                                    max: 0.29,
                                    color: '#F7EEAC'
                                }, {
                                    min: 0.3,
                                    max: 0.49,
                                    color: '#F0AF9C'
                                }, {
                                    min: 0.5,
                                    max: 0.79,
                                    color: '#EA6461'
                                },
                                {
                                    min: 0.8,
                                    max: 10,
                                    color: '#822925'
                                }
                            ],
                            textStyle: {
                                color: '#ffffff'
                            },
                            formatter: function(value, params) {
                                if (value < 0.1) {
                                    return '< 0.1';
                                }

                                if (value < 0.2) {
                                    return '< 0.2';
                                }

                                if (value < 0.3) {
                                    return '< 0.3';
                                }

                                if (value < 0.5) {
                                    return '< 0.5';
                                }

                                if (value < 0.8) {
                                    return '< 0.8';
                                }

                                return '>= 0.8';
                            }
                        },
                        tooltip: {
                            show: !0,
                            formatter: function(e) {
                                const date = e.value[0];
                                const hour = e.value[1];
                                const price = e.value[2];
                                return `<div><b>${date} ${hour}:00</b>  gas = ${price}$</div>`
                            }
                        },
                        series: [{
                            type: "heatmap",
                            data: r.data.data,
                            label: {
                                show: !0,
                                color: "#1F2533"
                            },
                            itemStyle: {
                                borderColor: "#FFFFFF",
                                borderWidth: 1
                            },
                            animation: !r
                        }]
                    });
                    // 获取最后一条数据
                    const last = r.data.data[r.data.data.length - 1];
                    $('.latest_price').eq(0).text(last[2]);
                } else {
                    c.setOption({
                        series: [{
                            data: r.data.data
                        }]
                    });
                }
            };
            const get = v => {
                $.ajax({
                    url: 'price.php?method=echarts',
                    type: 'GET',
                    dataType: 'json',
                    success: r => {
                        if (r.code !== 1) {
                            return layer.msg(r.msg, {
                                icon: r.code
                            });
                        }
                        s(r, v);
                    },
                    error: (r) => layer.alert(r.responseText, {
                        icon: 2
                    })
                });
            };
            get(0);
            $(window).resize(() => c.resize());
        }
    };
    const char2 = {
        init() {
            const c = window.echarts.init($('#chart2')[0]);
            const s = (r, i) => {
                if (!i) {
                    c.clear();
                    c.setOption({
                        grid: {
                            left: 50,
                            top: 0,
                            right: 0,
                            bottom: 100
                        },
                        xAxis: {
                            type: "category",
                            data: r.data.date,
                            axisTick: {
                                show: !1
                            },
                            axisLine: {
                                show: !1
                            },
                            axisLabel: {
                                lineHeight: 16
                            }
                        },
                        yAxis: {
                            type: "category",
                            data: r.data.hour,
                            axisTick: {
                                show: !1
                            },
                            axisLine: {
                                show: !1
                            },
                            axisLabel: {
                                formatter: function(e) {
                                    return e < 10 ? "0".concat(e, ":00") : "".concat(e, ":00")
                                }
                            }
                        },
                        visualMap: {
                            bottom: 0,
                            left: "center",
                            align: "top",
                            orient: "horizontal",
                            min: 0,
                            max: 1000,
                            pieces: [{
                                    min: 0,
                                    max: 0.29,
                                    color: '#C2D9FB'
                                },
                                {
                                    min: 0.3,
                                    max: 0.49,
                                    color: '#CBFDC2'
                                },
                                {
                                    min: 0.5,
                                    max: 0.79,
                                    color: '#F7EEAC'
                                }, {
                                    min: 0.8,
                                    max: 0.99,
                                    color: '#F0AF9C'
                                }, {
                                    min: 1,
                                    max: 1.99,
                                    color: '#EA6461'
                                },
                                {
                                    min: 2,
                                    max: 100,
                                    color: '#822925'
                                }
                            ],
                            textStyle: {
                                color: '#ffffff'
                            },
                            formatter: function(value, params) {
                                if (value < 0.3) {
                                    return '< 0.3';
                                }

                                if (value < 0.5) {
                                    return '< 0.5';
                                }

                                if (value < 0.8) {
                                    return '< 0.8';
                                }

                                if (value < 1) {
                                    return '< 1';
                                }

                                if (value < 2) {
                                    return '< 2';
                                }

                                return '>= 2';
                            }
                        },
                        tooltip: {
                            show: !0,
                            formatter: function(e) {
                                const date = e.value[0];
                                const hour = e.value[1];
                                const price = e.value[2];
                                return `<div><b>${date} ${hour}:00</b>  gas = ${price}$</div>`
                            }
                        },
                        series: [{
                            type: "heatmap",
                            data: r.data.data,
                            label: {
                                show: !0,
                                color: "#1F2533"
                            },
                            itemStyle: {
                                borderColor: "#FFFFFF",
                                borderWidth: 1
                            },
                            animation: !r
                        }]
                    });
                    // 获取最后一条数据
                    const last = r.data.data[r.data.data.length - 1];
                    $('.latest_price').eq(1).text(last[2]);
                } else {
                    c.setOption({
                        series: [{
                            data: r.data.data
                        }]
                    });
                }
            };
            const get = v => {
                $.ajax({
                    url: 'price.php?method=two',
                    type: 'GET',
                    dataType: 'json',
                    success: r => {
                        if (r.code !== 1) {
                            return layer.msg(r.msg, {
                                icon: r.code
                            });
                        }
                        s(r, v);
                    },
                    error: (r) => layer.alert(r.responseText, {
                        icon: 2
                    })
                });
            };
            get(0);
            $(window).resize(() => c.resize());
        }
    };
    char1.init();
    char2.init();
</script>

</html>