@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    background:linear-gradient(135deg,#f5f1ea,#efe7dd);
    font-family:'Segoe UI',sans-serif;
}

.container{
    max-width:1200px;
    margin:60px auto;
    padding:0 20px;
}

/* ===== Profile Card ===== */
.profile-card{
    background:white;
    padding:35px;
    border-radius:25px;
    box-shadow:0 25px 60px rgba(0,0,0,0.07);
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    margin-bottom:50px;
    position:relative;
}

.avatar-large{
    width:90px;
    height:90px;
    border-radius:50%;
    background:linear-gradient(135deg,#8b5e3c,#c7a17a);
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:40px;
    margin-bottom:15px;
}

.profile-name{
    font-size:20px;
    font-weight:600;
    color:#4b3f35;
}

.profile-role{
    font-size:14px;
    color:#8c7b6a;
}

.profile-tel{
    font-size:13px;
    color:#a08b7c;
}

.logout-btn{
    position:absolute;
    right:25px;
    top:25px;
    padding:8px 18px;
    border:none;
    border-radius:25px;
    background:#6b4f3a;
    color:white;
    cursor:pointer;
    font-size:13px;
}

/* ===== KPI ===== */
.kpi-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    margin-bottom:50px;
}

.card{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 15px 40px rgba(0,0,0,0.05);
    transition:all .3s ease;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 20px 50px rgba(0,0,0,0.08);
}

.card h2{
    margin-top:10px;
    font-size:28px;
    color:#6b4f3a;
}

/* ===== Chart ===== */
.chart-card{
    margin-bottom:40px;
}

/* ===== Table ===== */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

th{
    text-align:left;
    padding:12px;
    background:#f3ede6;
    color:#6b4f3a;
    font-weight:600;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
}

td:last-child{
    color:#c0392b;
    font-weight:bold;
}

/* ===== Toast ===== */
.toast-box{
    position:fixed;
    top:30px;
    right:30px;
    background:#6b4f3a;
    color:white;
    padding:18px 25px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    display:none;
    z-index:9999;
    font-size:14px;
}
</style>


<div class="container">

   <div class="profile-card">

    <div class="avatar-large">
        👤
    </div>

    <div class="profile-name">
        {{ session('employee_name') }}
    </div>

    <div class="profile-role">
        {{ session('role') ?? '-' }}
    </div>

    <div class="profile-tel">
        📞 {{ session('tel') ?? '-' }}
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            Logout
        </button>
    </form>

</div>


</div>


    <!-- KPI -->
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


    <!-- CHART -->
    <div class="card">
        <h4>ยอดขายวันนี้</h4>
        <canvas id="salesChart" height="100"></canvas>
    </div>


    <!-- LOW STOCK -->
    <div class="card" style="margin-top:30px;">
        <h4>สินค้าใกล้หมด</h4>
        <table width="100%" id="stockTable"></table>
    </div>

</div>

<div class="toast-box" id="toast"></div>


<script>

let chart;

function loadDashboard(){

    fetch("{{ route('dashboard.data') }}")
    .then(res => res.json())
    .then(data => {

        // KPI
        document.getElementById('pending').innerText = data.summary.pending ?? 0;
        document.getElementById('paid').innerText = data.summary.paid ?? 0;
        document.getElementById('todaySales').innerText = data.todaySales + " ฿";

        // Low stock table
        let table = "<tr><th>ชื่อสินค้า</th><th>คงเหลือ</th></tr>";
        data.lowStock.forEach(p => {
            table += `<tr>
                        <td>${p.productname}</td>
                        <td style="color:red;font-weight:bold;">${p.stock}</td>
                      </tr>`;
        });
        document.getElementById('stockTable').innerHTML = table;

        // Toast
        if(data.critical.length > 0){
            let msg = "สินค้าใกล้หมดมาก: ";
            data.critical.forEach(p=>{
                msg += p.productname + " ("+p.stock+") ";
            });
            let toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.style.display = 'block';
            setTimeout(()=> toast.style.display='none',5000);
        }

        // Chart
        if(!chart){
            const ctx = document.getElementById('salesChart');
            chart = new Chart(ctx, {
                type:'bar',
                data:{
                    labels:['ยอดขายวันนี้'],
                    datasets:[{
                        data:[data.todaySales]
                    }]
                }
            });
        }else{
            chart.data.datasets[0].data = [data.todaySales];
            chart.update();
        }

    });
}

// โหลดครั้งแรก
loadDashboard();

// refresh ทุก 30 วิ (ไม่ reload หน้า)
setInterval(loadDashboard, 30000);

</script>

@endsection
