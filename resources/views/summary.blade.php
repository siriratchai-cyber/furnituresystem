@extends('layouts.app')

@section('content')

<div class="page-animate">
    <div class="summary-wrapper">

        <h2 class="summary-title">สรุปยอดขาย</h2>

        <!-- TABS -->
        <div class="summary-tabs">
            <button class="tab active" onclick="loadSummary('week')">สัปดาห์</button>
            <button class="tab" onclick="loadSummary('month')">เดือน</button>
            <button class="tab" onclick="loadSummary('year')">ปี</button>
        </div>

        <!-- CHART -->
        <div class="card-box slide-up">
            <canvas id="summaryChart"></canvas>
        </div>

        <!-- INCOME / EXPENSE -->
        <div class="card-box info-card slide-up delay-1">
            <h5>สรุปรายรับ / รายจ่าย</h5>
            <p>รายรับ: <span id="income">-</span></p>
            <p>รายจ่าย: <span id="expense">-</span></p>
            <p>กำไรสุทธิ: <span id="net">-</span></p>
            <p>การเปลี่ยนแปลง: <span id="changePercent">-</span></p>
        </div>

        <!-- TOP PRODUCTS -->
        <div class="card-box info-card slide-up delay-2">
            <h5>สินค้ายอดนิยม</h5>
            <ul id="topProducts"></ul>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;

function formatCurrency(val){
    return new Intl.NumberFormat('th-TH',{
        style:'currency',
        currency:'THB'
    }).format(val ?? 0);
}

function setActiveTab(period){
    document.querySelectorAll('.tab').forEach(btn=>{
        btn.classList.remove('active');
    });

    const map = {
        week:'สัปดาห์',
        month:'เดือน',
        year:'ปี'
    };

    document.querySelectorAll('.tab').forEach(btn=>{
        if(btn.innerText === map[period]){
            btn.classList.add('active');
        }
    });
}

function loadSummary(period='week'){

    setActiveTab(period);

    fetch("{{ route('sales.summary.data') }}?period=" + period, {
        headers:{ 'X-Requested-With':'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {

        // ===== KPI =====
        document.getElementById('income').innerText =
            formatCurrency(data.income);

        document.getElementById('expense').innerText =
            formatCurrency(data.outcome);

        document.getElementById('net').innerText =
            formatCurrency(data.net);

        const changeEl = document.getElementById('changePercent');

        if (data.change_percent === '-' || data.change_percent === null) {
            changeEl.innerText = '-';
            changeEl.style.color = "#555";
        } else {
            const percent = Number(data.change_percent);
            changeEl.innerText = percent.toFixed(2) + ' %';

            if (percent > 0) {
                changeEl.style.color = "#1b5e20";
            } else if (percent < 0) {
                changeEl.style.color = "#b71c1c";
            } else {
                changeEl.style.color = "#555";
            }
        }

        // ===== TOP PRODUCTS =====
        const ul = document.getElementById('topProducts');
        ul.innerHTML = '';

        if (Array.isArray(data.top) && data.top.length > 0) {
            data.top.forEach((p,i)=>{
                ul.innerHTML += `
                    <li>
                        <span class="rank">#${i+1}</span>
                        ${p.productname}
                        <span class="qty">${p.qty} ชิ้น</span>
                    </li>
                `;
            });
        } else {
            ul.innerHTML = `<li>-</li>`;
        }

        updateChart(data);

    })
    .catch(err => {
        console.error("Summary Error:", err);
    });
}

function updateChart(data){

    const ctx = document.getElementById('summaryChart');

    if(chart) chart.destroy();

    chart = new Chart(ctx,{
        type:'bar',
        data:{
            labels: data.labels ?? [],
            datasets:[
                {
                    type:'bar',
                    label:'รายรับ',
                    data: data.sales ?? [],
                    backgroundColor:'rgba(201,162,39,0.85)',
                    borderRadius:8
                },
                {
                    type:'bar',
                    label:'รายจ่าย',
                    data: data.expense ?? [],
                    backgroundColor:'rgba(90,62,43,0.8)',
                    borderRadius:8
                },
                {
                    type:'line',
                    label:'กำไรสุทธิ',
                    data: data.profit ?? [],
                    borderColor:'#1b5e20',
                    tension:0.4,
                    borderWidth:3,
                    fill:false,
                    pointRadius:5
                }
            ]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{ labels:{ color:'#5a3e2b' } }
            },
            scales:{
                y:{
                    beginAtZero:true,
                    ticks:{
                        callback:(v)=>formatCurrency(v)
                    }
                }
            }
        }
    });
}

document.addEventListener("DOMContentLoaded",()=>{
    loadSummary('week');
});
</script>
@endpush


<style>

/* Background Luxury */
body{
    background: linear-gradient(135deg,#3b2a1f,#5a3e2b);
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}

/* Page Fade */
.page-animate{
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn{
    from{ opacity:0; transform: translateY(10px); }
    to{ opacity:1; transform: translateY(0); }
}

/* Main Card */
.summary-wrapper{
    max-width: 820px;
    margin: 70px auto;
    padding: 35px;
    background: #ffffff;
    border-radius: 30px;
    box-shadow: 0 30px 70px rgba(0,0,0,0.25);
}

/* Title */
.summary-title{
    font-weight: 700;
    margin-bottom: 30px;
    font-size: 24px;
    color: #5a3e2b;
    text-align:center;
}

/* Tabs */
.summary-tabs{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-bottom:35px;
}

.tab{
    padding:8px 24px;
    border:none;
    border-radius:30px;
    background:#f2f2f2;
    cursor:pointer;
    transition:0.3s;
    font-weight:500;
}

.tab.active{
    background: linear-gradient(135deg,#c9a227,#e6c35c);
    color:#3b2a1f;
    box-shadow: 0 8px 20px rgba(201,162,39,0.4);
}

/* Cards */
.card-box{
    background:#fafafa;
    padding:22px;
    border-radius:22px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    margin-bottom:25px;
}

/* Slide Animation */
.slide-up{
    opacity:0;
    transform: translateY(25px);
    animation: slideUp 0.6s forwards;
}

.delay-1{ animation-delay:0.2s; }
.delay-2{ animation-delay:0.4s; }

@keyframes slideUp{
    to{
        opacity:1;
        transform: translateY(0);
    }
}

/* Income / Expense */
#income{
    color:#c9a227;
    font-weight:600;
}

#expense{
    color:#5a3e2b;
    font-weight:600;
}

/* Top Products */
ul{
    padding-left:0;
    list-style:none;
}

ul li{
    display:flex;
    justify-content:space-between;
    padding:8px 0;
    border-bottom:1px solid #eee;
}

.rank{
    color:#c9a227;
    font-weight:600;
}

.qty{
    color:#777;
}

/* Chart */
canvas{
    max-height:300px;
}

</style>
