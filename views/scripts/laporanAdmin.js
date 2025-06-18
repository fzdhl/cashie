document.addEventListener("DOMContentLoaded", () => {
    fetch("?c=LaporanController&m=getListLaporan")
        .then((res) => res.text())
        .then((html) => {
            document.getElementById("listReport").innerHTML = html;
        })
        .catch((err) => console.error("Gagal reload list:", err));
});

//Ajax
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

            // Reload daftar laporan
            fetch("?c=LaporanController&m=getListLaporan")
                .then((res) => res.text())
                .then((html) => {
                    document.getElementById("listReport").innerHTML = html;
                })
                .catch((err) => console.error("Gagal reload list:", err));
        })
        .catch((err) => console.error("Fetch error:", err));
    }
});