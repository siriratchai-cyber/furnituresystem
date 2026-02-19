@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ===============================
   LUXURY COMPACT VERSION
================================ */

:root{
    --brown-dark:#5a3e2b;
    --brown-main:#8b5e3c;
    --brown-light:#c7a17a;
}

*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:
        radial-gradient(circle at 15% 15%, rgba(199,161,122,0.18), transparent 40%),
        radial-gradient(circle at 85% 85%, rgba(139,94,60,0.15), transparent 40%),
        linear-gradient(135deg,#faf7f3,#efe7dd);
    color:#3e342b;
}

.container{
    max-width:780px;
    margin:70px auto;
    padding:0 40px;
}

/* ================= PROFILE ================= */

.profile-card{
    width:220px;
    height:220px;
    margin:0 auto 45px auto;
    border-radius:50%;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    position:relative;
    background:linear-gradient(145deg,#ffffff,#f2e9df);
    box-shadow:
        0 25px 60px rgba(139,94,60,0.25),
        inset 0 0 25px rgba(199,161,122,0.25);
}

.avatar-large{
    width:70px;
    height:70px;
    border-radius:50%;
    background:linear-gradient(135deg,var(--brown-main),var(--brown-light));
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:28px;
    margin-bottom:10px;
    box-shadow:0 8px 20px rgba(139,94,60,0.5);
}

.profile-name{ font-size:18px;font-weight:600; }
.profile-role{ font-size:13px;color:#8c7b6a; }
.profile-tel{ font-size:12px;color:#a08b7c; }

.logout-btn{
    position:absolute;
    bottom:-15px;
    right:50%;
    transform:translateX(50%);
    border:none;
    padding:6px 16px;
    border-radius:30px;
    background:linear-gradient(135deg,var(--brown-dark),var(--brown-main));
    color:white;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(90,62,43,0.4);
}

/* ================= KPI ================= */

.kpi-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
    margin-bottom:40px;
}

.card{
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:16px;
    border:1px solid rgba(255,255,255,0.4);
    box-shadow:0 15px 40px rgba(139,94,60,0.15);
    transition:.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 25px 50px rgba(139,94,60,0.25);
}

.kpi-grid .card{
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    height:110px;
}

.kpi-grid .card h2{
    margin:0;
    font-size:22px;
    font-weight:700;
    background:linear-gradient(135deg,var(--brown-dark),var(--brown-light));
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

canvas{
    margin-top:15px;
    max-height:180px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
    border-radius:16px;
    overflow:hidden;
}

th{
    padding:12px;
    background:linear-gradient(135deg,var(--brown-dark),var(--brown-main));
    color:white;
    font-weight:500;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    background:white;
}

tr:hover td{ background:#f9f4ee; }

.toast-box{
    position:fixed;
    top:25px;
    left:25px;
    padding:14px 18px;
    border-radius:16px;
    background:linear-gradient(135deg,var(--brown-dark),var(--brown-main));
    color:white;
    font-size:13px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
    display:none;
}

</style>


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

<div class="toast-box" id="toast"></div>

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

        if(data.critical.length > 0){
            let msg = "สินค้าใกล้หมดมาก: ";
            data.critical.forEach(p=>{
                msg += p.productname + " ("+p.stock+") ";
            });
            let toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.style.display='block';
            setTimeout(()=> toast.style.display='none',5000);
        }

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
