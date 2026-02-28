@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="{{ asset('css/luxury.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


<div class="container">

<div class="profile-card">
    <div class="avatar-large">👤</div>
    <div class="profile-name">{{ session('employee_name') }}</div>
    <div class="profile-role">{{ session('role') ?? '-' }}</div>
    <div class="profile-tel">📞 {{ session('tel') ?? '-' }}</div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<div class="kpi-grid">
    <div class="card">
        <div>รอดำเนินการ</div>
        <h2 id="pending">0</h2>
    </div>
    <div class="card">
        <div>ชำระแล้ว</div>
        <h2 id="paid">0</h2>
    </div>
    <div class="card">
        <div>ยอดขายวันนี้</div>
        <h2 id="todaySales">0 ฿</h2>
    </div>
</div>

<div class="card">
    <h4>ยอดขายวันนี้</h4>
    <canvas id="salesChart"></canvas>
</div>

<div class="card" style="margin-top:25px;">
    <h4>สินค้าใกล้หมด</h4>
    <table id="stockTable"></table>
</div>

</div>



<script>

let chart;

function formatNumber(num){
    return new Intl.NumberFormat('th-TH').format(num);
}

function loadDashboard(){

    fetch("{{ route('dashboard.data') }}")
    .then(res => res.json())
    .then(data => {

        document.getElementById('pending').innerText = data.summary.pending ?? 0;
        document.getElementById('paid').innerText = data.summary.paid ?? 0;
        document.getElementById('todaySales').innerText = formatNumber(data.todaySales) + " ฿";

        let table = "<tr><th>ชื่อสินค้า</th><th>คงเหลือ</th></tr>";
        data.lowStock.forEach(p=>{
            table += `<tr>
                        <td>${p.productname}</td>
                        <td style="color:red;font-weight:bold;">${p.stock}</td>
                      </tr>`;
        });
        document.getElementById('stockTable').innerHTML = table;


        const todayLabel = new Date().toLocaleDateString('en-US',{ weekday:'short' });

        const colors = data.chart.labels.map(label =>
            label === todayLabel ? '#8b5e3c' : '#c7a17a'
        );

        if(!chart){

            const ctx = document.getElementById('salesChart');

            chart = new Chart(ctx,{
                type:'bar',
                data:{
                    labels:data.chart.labels,
                    datasets:[{
                        label:'ยอดขาย 7 วัน',
                        data:data.chart.data,
                        backgroundColor:colors,
                        borderRadius:8
                    }]
                },
                options:{
                    plugins:{ legend:{ display:false }},
                    scales:{
                        y:{
                            beginAtZero:true,
                            ticks:{
                                callback:value =>
                                    new Intl.NumberFormat('th-TH').format(value)
                            }
                        }
                    }
                }
            });

        }else{

            chart.data.labels = data.chart.labels;
            chart.data.datasets[0].data = data.chart.data;
            chart.data.datasets[0].backgroundColor = colors;
            chart.update();
        }

    });
}

loadDashboard();
setInterval(loadDashboard,30000);

</script>

@endsection
