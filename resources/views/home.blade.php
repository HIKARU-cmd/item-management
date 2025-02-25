@extends('adminlte::page')

@section('title', '月毎の購入費用データ')

@section('content_header')
    <h4>月毎の購入費用データ</h4>
@stop

@section('content')
    <div style="width: 100%; height: 100vh; margin:0;">

        {{-- 年を選択するプルダウン --}}
        <label for="yearSelect">年を選択</label>
        <select name="" id="yearSelect">
            @foreach ($years as $year)
                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>

        {{-- 月を選択するプルダウン --}}
        <label for="monthSelect">月を選択</label>
        <select name="" id="monthSelect">
            <option value="01" {{ '01' == $selectedMonth ? 'selected' : '' }}>01月</option>
            <option value="02" {{ '02' == $selectedMonth ? 'selected' : '' }}>02月</option>
            <option value="03" {{ '03' == $selectedMonth ? 'selected' : '' }}>03月</option>
            <option value="04" {{ '04' == $selectedMonth ? 'selected' : '' }}>04月</option>
            <option value="05" {{ '05' == $selectedMonth ? 'selected' : '' }}>05月</option>
            <option value="06" {{ '06' == $selectedMonth ? 'selected' : '' }}>06月</option>
            <option value="07" {{ '07' == $selectedMonth ? 'selected' : '' }}>07月</option>
            <option value="08" {{ '08' == $selectedMonth ? 'selected' : '' }}>08月</option>
            <option value="09" {{ '09' == $selectedMonth ? 'selected' : '' }}>09月</option>
            <option value="10" {{ '10' == $selectedMonth ? 'selected' : '' }}>10月</option>
            <option value="11" {{ '11' == $selectedMonth ? 'selected' : '' }}>11月</option>
            <option value="12" {{ '12' == $selectedMonth ? 'selected' : '' }}>12月</option>
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
                    window.location.href = "/";
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

                const yearMonth = `${selectedYear}-${selectedMonth}`;

                // new Chart Chart.jsのグラフ作成
                processChartInstance = new Chart(processCtx, 
                {
                    type: 'bar',
                    data: {
                            labels: labels,
                            datasets: [{
                                label: `${yearMonth}   工程別購入費(円)`,
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

            function updateUrl(){
                const selectedYear = yearSelect.value;
                const selectedMonth = monthSelect.value;
                window.location.href = `/chart?year=${selectedYear}&month=${selectedMonth}`;
            }

            document.getElementById('yearSelect').addEventListener('change', updateUrl);
            document.getElementById('monthSelect').addEventListener('change', updateUrl);


            // 受け取ったデータをJSON形式にする
            const data = @json($monthlyData);
            updateChart(data);

            const processData = @json($processChartData);
            const selectedYear = yearSelect.value;
            const selectedMonth = monthSelect.value;
            updateProcessChart(processData, selectedYear, selectedMonth);
        });
    </script>
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop