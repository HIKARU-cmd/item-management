@extends('adminlte::page')

@section('title', '月毎の購入費用データ')

@section('content_header')
    <h4>月毎の購入費用データ</h4>
@stop

@section('content')
    <div style="width: 100%; height: 100vh; margin:0;">

        {{-- 年を選択するプルダウン --}}
        <label for="year-select">年を選択</label>
        <select name="" id="year-select">
            @foreach ($years as $year)
                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
        <div class="container mt-5" style="padding: 0;">
            <div class="row">
                <!-- 1つ目のグラフ -->
                <div class="col-md-6">
                    <canvas id="itemsChart" style="width: 100%; height: 100%;"></canvas>
                </div>
        
                <!-- 2つ目のグラフ -->
                <div class="col-md-6">
                    <canvas id="processChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(){   
            const ctx = document.getElementById("itemsChart").getContext("2d");
            let chartInstance = null;
            
            function updateChart(data)
            {
                // 年月と購入データを配列に変換
                const labels = data.map(item => item.month);
                const items = data.map(item => item.total);

                // 入力された年がDBに存在しない場合
                const exists = @json($exists);
                if(!exists){
                    alert('選択した年のデータはありません。');
                    window.location.href = "/chart";
                }

                 // 前のグラフがある場合は削除
                if (chartInstance) {
                    chartInstance.destroy();
                }

                // new Chart Chart.jsのグラフ作成
                chartInstance = new Chart(ctx, 
                {
                    type: 'bar',
                    data: {
                            labels: labels,
                            datasets: [{
                                label: '購入費(円)',
                                data: items,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                    options: {
                        // 画面サイズによってグラフサイズを自動調整
                        responsive: true,
                        maintainAspectRatio: false, 
                        plugins: {
                            legend: {
                                labels: {
                                            font:{size:20}
                                        }
                                    }
                                },
                                scales: {
                                            y: {
                                                    beginAtZero: true,
                                                    title: {
                                                        display: true,
                                                        text: '金額(円)',
                                                        font: {size: 16}
                                                    },
                                                    ticks: {
                                                        font: {size: 16}
                                                    }
                                                },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: '購入月',
                                                    font: {size: 16}
                                                },
                                                ticks: {
                                                    font: {size: 16}
                                                }
                                            }
                                        }
                            }
                });
            }

            const processCtx = document.getElementById("processChart").getContext("2d");
            let processChartInstance = null;
            
            function updateProcessChart(data)
            {
                // 年月と購入データを配列に変換
                const labels = data.map(item => item.process);
                const items = data.map(item => item.total);

                // 前のグラフがある場合は削除
                if (processChartInstance) {
                    processChartInstance.destroy();
                }

                // new Chart Chart.jsのグラフ作成
                processChartInstance = new Chart(processCtx, 
                {
                    type: 'bar',
                    data: {
                            labels: labels,
                            datasets: [{
                                label: '工程別購入費(円)',
                                data: items,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                    options: {
                        responsive: true, // 画面サイズによってグラフサイズを自動調整
                        maintainAspectRatio: false, // グラフ縦横比固定を解除
                        plugins: {
                            legend: {
                                labels: {
                                            font:{size:20}
                                        }
                                    }
                                },
                                scales: {
                                            y: {
                                                    beginAtZero: true,
                                                    title: {
                                                        display: true,
                                                        text: '金額(円)',
                                                        font: {size: 16}
                                                    },
                                                    ticks: {
                                                        font: {size: 16}
                                                    }
                                                },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: '工程名',
                                                    font: {size: 16}
                                                },
                                                ticks: {
                                                    font: {size: 16}
                                                }
                                            }
                                        }
                            }
                });
            }

            document.getElementById("year-select").addEventListener("change", function(){
                const selectedYear = this.value;
                window.location.href = `/chart?year=${selectedYear}`;
            });

            // 受け取ったデータをJSON形式にする
            const data = @json($monthlyData);
            updateChart(data);

            const processData = @json($processChartData);
            updateProcessChart(processData);
        });
    </script>
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop