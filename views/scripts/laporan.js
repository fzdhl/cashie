
const ctx3 = document.getElementById('pengeluaran').getContext('2d');
const totalChart3 = new Chart(ctx3, {
    type: 'line',
    data: {
        labels: datePengeluaran,
        datasets: [{
            label: 'total pengeluaran',
            data: totalPengeluaran,
            borderColor: '#FD6A02',
            tension: 0.1,
            fill: false
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: false }
        }
    }
});

const ctx2 = document.getElementById('pemasukan').getContext('2d');
const totalChart2 = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: datePemasukan,
        datasets: [{
            label: 'total pemasukan',
            data: totalPemasukan,
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1,
            fill: false
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: false }
        }
    }
});

// const ctx1 = document.getElementById('saldo').getContext('2d');
// const totalChart1 = new Chart(ctx1, {
//     type: 'line',
//     data: {
//         labels: dateSaldo,
//         datasets: [{
//             label: 'total saldo',
//                 data: totalSaldo,
//                 borderColor: '#00A86B',
//                 tension: 0.1,
//                 fill: false
//             }]
//         },
//     options: {
//         scales: {
//             y: { beginAtZero: false }
//         }
//     }
// });
