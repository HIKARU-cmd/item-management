@extends('adminlte::page')

@section('title', '年月毎の購入データ')

@section('content_header')
    <h1>年月毎の購入データ</h1>
@stop

@section('content')
    <div style="width: 70%; height: 500px; margin: 0 auto;">
        <canvas id="salesChart"></canvas>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(){   
            
            // 受け取ったデータをJSON形式にする
            const data = @json($monthlyData);
            console.log(data);
            
            // 年月と購入データを配列に変換
            const labels = data.map(item => item.month);
            const sales = data.map(item => item.total);
            
            const ctx = document.getElementById("salesChart").getContext("2d");
            // new Chart Chart.jsのグラフ作成
            new Chart(ctx, {
                type: 'bar',
                data: {
                        labels: labels,
                        datasets: [{
                            label: '購入費(円)',
                            data: sales,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                options: {
                            // 画面サイズによってグラフサイズを自動調整
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        font:{size:25}
                                    }
                                }
                            },
                            scales: {
                                        y: {
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: '金額(円)',
                                                    font: {size: 20}
                                                },
                                                ticks: {
                                                    font: {size: 16}
                                                }
                                            },
                                        x: {
                                            title: {
                                                display: true,
                                                text: '購入月',
                                                font: {size: 20}
                                            },
                                            ticks: {
                                                font: {size: 16}
                                            }
                                        }
                                    }
                        }
            });
        });
    </script>
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stop
