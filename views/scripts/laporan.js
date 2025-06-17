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


function toggleLaporan(jenis) {
    const mingguan = document.getElementById('laporan_mingguan_section');
    const bulanan = document.getElementById('laporan_bulanan_section');

    if (jenis === 'mingguan') {
        mingguan.classList.remove('d-none');
        bulanan.classList.add('d-none');
    } else {
        bulanan.classList.remove('d-none');
        mingguan.classList.add('d-none');
    }
}

// Opsional: munculkan default ke mingguan/bulanan
document.addEventListener("DOMContentLoaded", () => {
    toggleLaporan('mingguan'); // atau 'bulanan'
    fetch("?c=LaporanController&m=getListLaporan")
        .then((res) => res.text())
        .then((html) => {
            document.getElementById("listReport").innerHTML = html;
        })
    .catch((err) => console.error("Gagal reload list:", err));
});


// AJAX


// untuk kirim data dari form penambahan 'target' baru
document.getElementById("addReport").addEventListener("submit", function (e) {
    e.preventDefault();

    const formElement = this;
    const formData = new FormData(formElement);

    fetch("?c=LaporanController&m=addLaporan", {
        method: "POST",
        body: formData,
    })
    .then((res) => res.json())
    .then((data) => {
        const errorElement = document.getElementById("errorReport");

        if (data.status === "error") {
            errorElement.textContent = data.message;
        } else if (data.status === "success") {
            errorElement.textContent = "";
            formElement.reset();

            // Reload list laporan setelah berhasil menambahkan
            fetch("?c=LaporanController&m=getListLaporan")
                .then((res) => res.text())
                .then((html) => {
                    document.getElementById("listReport").innerHTML = html;
                })
                .catch((err) => console.error("Fetch list error:", err));
        }
    })
    .catch((err) => {
        console.error("Fetch error:", err);
        document.getElementById("errorReport").textContent = "Terjadi kesalahan saat mengirim data.";
    });
});


// untuk delete data Laporan
document.addEventListener("submit", function (e) {
    if (e.target && e.target.name === "deleteReport") {
        e.preventDefault();

        const formElement = e.target;
        const formData = new FormData(formElement);

        fetch("?c=LaporanController&m=deleteLaporan", {
            method: "POST",
            body: formData,
        })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "success") {
                // Reload daftar laporan
                fetch("?c=LaporanController&m=getListLaporan")
                    .then((res) => res.text())
                    .then((html) => {
                        document.getElementById("listReport").innerHTML = html;
                    })
                    .catch((err) => console.error("Gagal reload list:", err));
            } else {
                console.error("Gagal hapus:", data.message);
            }
        })
        .catch((err) => console.error("Fetch error:", err));
    }
});



// // untuk menampilkan modal ketika tombol 'edit' diklik
// // Bootstrap modal instance
// const editModal = new bootstrap.Modal(
//   document.getElementById("editTargetModal")
// );

// document
// .querySelector(".row-cols-md-2")
// .addEventListener("click", function (e) {
//     // Mengecek apakah yang diklik tombol edit atau hapus
//     // Jika tombol edit, maka masukkan data button ke value, dan tampilkan modal
//     if (btn = e.target.closest(".edit-btn")) {
//       document.getElementById("edit-target-id").value = btn.dataset.id;
//       document.getElementById("edit-target-name").value = btn.dataset.name;
//       document.getElementById("edit-target-amount").value = new Intl.NumberFormat("id-ID").format(btn.dataset.amount);
      
//       editModal.show();
//     // Jika tombol hapus, maka masukkan data button ke value
//     } else if (btn = e.target.closest(".delete-btn")) {
//       const confirmDelete = confirm("Apakah anda yakin ingin menghapus target ini?");

//       if (!confirmDelete) {
//         return;
//       }

//       fetch("?c=TargetController&m=deleteProcess&target_id=" + btn.dataset.id) 
//       .then((res) => res.text())
//       .then((text) => {
//         alert(text);
//         return fetch("?c=TargetController&m=getTargetCards");
//       })
//       .then((res) => res.text())
//       .then((html) => {
//         document.querySelector(".row-cols-md-2").innerHTML = html;
//       })
//       .catch((err) => console.error("Fetch error:", err));
//     }
//   });

// // Handle form submit
// const form = document.getElementById("editTargetForm");

// form.addEventListener("submit", function (e) {
//   e.preventDefault();

//   const amountInput = form.querySelector('input[name="amount"]');
//   amountInput.value = amountInput.value.replace(/\D/g, ""); // remove non-digits

//   const formData = new FormData(form);

//   fetch("?c=TargetController&m=updateProcess", {
//     method: "POST",
//     body: formData,
//   })
//     .then((res) => res.json())
//     .then((data) => {
//       if (data.success) {
//         editModal.hide();
//         alert(data.success);

//         return fetch("?c=TargetController&m=getTargetCards");
//       } else {
//         throw new Error("Gagal memperbarui target.");
//       }
//     })
//     .then((res) => res.text())
//     .then((html) => {
//       document.querySelector(".row-cols-md-2").innerHTML = html;
//     })
//     .catch((err) => alert("Terjadi kesalahan: " + err.message));
// });