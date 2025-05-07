@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .suggestions {
        list-style: none;
        padding: 0;
        margin: 0;
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        width: 85%;
        max-height: 150px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .suggestions li {
        padding: 8px;
        cursor: pointer;
    }

    .suggestions li:hover,
    .suggestions li.selected {
        background: #980003;
        color: white;
    }

    #tagSearch{
        position: sticky;
        overflow-y: auto;
        top: 50px;
        height: auto;
    }

    @media (max-width: 768px) {
        #tagSearch {
            position: relative;
        }
    }
</style>

<div class="page-heading">
    <!-- <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
                <p class="text-subtitle text-muted">Halaman yang akan menampilkan ringkasan data aplikasi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('home') }}">Dashboard</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div> -->
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon purple">
                                            <i class="fa fa-user-shield"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Total Aktivitas</h6>
                                        <h6 class="font-extrabold mb-0" id="total-aktivitas" style='font-size:14px;'></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="fa fa-user-clock"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Aktivitas Perbulan</h6>
                                        <h6 class="font-extrabold mb-0" id="aktivitas-perbulan" style='font-size:14px;'></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="fa fa-user-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Sedang Bertugas</h6>
                                        <h6 class="font-extrabold mb-0" id="sedang-bertugas" style='font-size:14px;'></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-3 py-4-5">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="fa fa-user-times"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Menunggu Tugas</h6>
                                        <h6 class="font-extrabold mb-0" id="menunggu-tugas" style='font-size:14px;'></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Grafik Aktivitas Bulan Ini</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-profile-visit"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-5">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg">
                                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="PM - HMA" />
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold" id="total_paket"></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Aktivitas Terkini</h4>
                    </div>
                    <div class="card-content pb-4" id="recent-activity">
                        {{-- JS Handle - function setRecentActivity --}}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div id="fullPageLoader" class="full-page-loader" style="display: none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    let data = {!! json_encode($data[0]) !!}
    let monthly = data.activities_this_month;
    let total = data.total_activity;
    let total_paket = data.total_activity_this_year;
    let total_is_process = data.total_is_process_true;
    let total_no_process = data.total_is_process_false;

    $(document).ready(function () {
        $('#total-aktivitas').text(total + " Aktivitas");
        $('#aktivitas-perbulan').text(monthly.length + " Aktivitas");
        $('#sedang-bertugas').text(total_is_process + " Karyawan");
        $('#menunggu-tugas').text(total_no_process + " Karyawan");
        $('#total_paket').text(total_paket + " Proyek");
        setChart();
        setRecentActivity();
    });

    function setRecentActivity() {
        let recent = monthly.slice(0, 5);

        $('#recent-activity').empty();
        for (let i = 0; i < recent.length; i++) {
            $('#recent-activity').append(`
                <div class="recent-message d-flex px-0 py-2">
                    <div class="name ms-4">
                        <h5 class="mb-1">${recent[i].project}</h5>
                        <h6 class="text-muted mb-0">${recent[i].title}</h6>
                    </div>
                </div>
            `);
        }
        $('#recent-activity').append(`
            <div class="px-4">
                <a href="{{ route('activity.index') }}" class='btn btn-block btn-xl btn-light-primary font-bold mt-3'>
                    Selengkapnya
                </a>
            </div>
        `);
    }

    function setChart() {
        let chart = Array(31).fill(0);

        for (let j = 0; j < monthly.length; j++) {
            const date = new Date(monthly[j].start_date);
            const day = date.getDate();
            chart[day - 1] += 1;
        }

        showChart(chart);
    }

    function showChart(dataChart){
        var optionsProfileVisit = {
            annotations: {
                position: 'back'
            },
            dataLabels: {
                enabled:false
            },
            chart: {
                type: 'line',
                height: 400
            },
            fill: {
                opacity:1
            },
            plotOptions: {
            },
            series: [{
                name: 'Total',
                data: dataChart
            }],
            colors: '#435ebe',
            xaxis: {
                categories: [
                    "01","02","03","04","05","06","07","08","09","10",
                    "11","12","13","14","15","16","17","18","19","20",
                    "21","22","23","24","25","26","27","28","29","30","31",
                ]
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return parseInt(val) + " Aktivitas"; // Menampilkan angka bulat
                    }
                },
                x: {
                    formatter: function (val) {
                        const monthNames = [
                            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                        ];
                        const currentMonth = new Date().getMonth();
                        return "Tanggal " + val + " " + monthNames[currentMonth];
                    }
                }
            }
        }
        var lineOptions = {
            chart: {
                type: "line",
            },
            series: [
                {
                name: "Total",
                data: dataChart,
                },
            ],
            xaxis: {
                categories: [
                    "01","02","03","04","05","06","07","08","09","10",
                    "11","12","13","14","15","16","17","18","19","20",
                    "21","22","23","24","25","26","27","28","29","30","31"
                ],
                tooltip: {
                    enabled: false
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return parseInt(val) + " Aktivitas";
                    }
                },
                x: {
                    formatter: function (val) {
                        const monthNames = [
                            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                        ];
                        const currentMonth = new Date().getMonth();
                        return "Tanggal " + val + " " + monthNames[currentMonth];
                    }
                }
            }
        };

        var chartProfileVisit = new ApexCharts(document.querySelector("#chart-profile-visit"), lineOptions);
        chartProfileVisit.render();
    }

</script>

{{-- <script>
    var optionsProfileVisit = {
        annotations: {
            position: 'back'
        },
        dataLabels: {
            enabled:false
        },
        chart: {
            type: 'bar',
            height: 400
        },
        fill: {
            opacity:1
        },
        plotOptions: {
        },
        series: [{
            name: 'sales',
            data: [
                9,20,30,20,10,20,30,20,10,20,
                19,210,130,120,140,210,130,210,110,120,
                99,10,30,100,40,80,30,50,110,220,120,
            ]
        }],
        colors: '#435ebe',
        xaxis: {
            categories: [
                "01","02","03","04","05","06","07","08","09","10",
                "11","12","13","14","15","16","17","18","19","20",
                "21","22","23","24","25","26","27","28","29","30","31",
            ]
        },
    }

    var chartProfileVisit = new ApexCharts(document.querySelector("#chart-profile-visit"), optionsProfileVisit);
    chartProfileVisit.render();
</script> --}}
@endsection