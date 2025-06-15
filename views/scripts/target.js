// untuk format ribuan real-time pada input tambah 'target'
document.getElementById("nominalAdd").addEventListener("input", function (e) {
  // Remove all non-digit characters
  const cleared = e.target.value.replace(/\D/g, "");

  // Format with separator
  e.target.value = new Intl.NumberFormat("id-ID").format(cleared);
});

// untuk format ribuan real-time pada input tambah 'target'
document.getElementById("edit-target-amount").addEventListener("input", function (e) {
  // Remove all non-digit characters
  const cleared = e.target.value.replace(/\D/g, "");

  // Format with separator
  e.target.value = new Intl.NumberFormat("id-ID").format(cleared);
});

// untuk kirim data dari form penambahan 'target' baru
document.getElementById("targetForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formElement = e.target;

  const amountInput = formElement.querySelector('input[name="amount"]');
  amountInput.value = amountInput.value.replace(/\D/g, ""); // remove non-digits
  
  const formData = new FormData(formElement);

  fetch("?c=TargetController&m=createProcess", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((data) => {
      alert(data);
      formElement.reset();
      // untuk reload list 'target' setelah berhasil ditambahkan 'target' baru
      return fetch("?c=TargetController&m=getTargetCards");
    })
    .then((res) => res.text())
    .then((html) => {
      document.querySelector(".row-cols-md-2").innerHTML = html;
    })
    .catch((err) => console.error("Fetch error:", err));
});

// untuk menampilkan modal ketika tombol 'edit' diklik
// Bootstrap modal instance
const editModal = new bootstrap.Modal(
  document.getElementById("editTargetModal")
);

document
.querySelector(".row-cols-md-2")
.addEventListener("click", function (e) {
    // Mengecek apakah yang diklik tombol edit atau hapus
    // Jika tombol edit, maka masukkan data button ke value, dan tampilkan modal
    if (btn = e.target.closest(".edit-btn")) {
      document.getElementById("edit-target-id").value = btn.dataset.id;
      document.getElementById("edit-target-name").value = btn.dataset.name;
      document.getElementById("edit-target-amount").value = new Intl.NumberFormat("id-ID").format(btn.dataset.amount);
      
      editModal.show();
    // Jika tombol hapus, maka masukkan data button ke value
    } else if (btn = e.target.closest(".delete-btn")) {
      const confirmDelete = confirm("Apakah anda yakin ingin menghapus target ini?");

      if (!confirmDelete) {
        return;
      }

      fetch("?c=TargetController&m=deleteProcess&target_id=" + btn.dataset.id) 
      .then((res) => res.text())
      .then((text) => {
        alert(text);
        return fetch("?c=TargetController&m=getTargetCards");
      })
      .then((res) => res.text())
      .then((html) => {
        document.querySelector(".row-cols-md-2").innerHTML = html;
      })
      .catch((err) => console.error("Fetch error:", err));
    }
  });

// Handle form submit
const form = document.getElementById("editTargetForm");

form.addEventListener("submit", function (e) {
  e.preventDefault();

  const amountInput = form.querySelector('input[name="amount"]');
  amountInput.value = amountInput.value.replace(/\D/g, ""); // remove non-digits

  const formData = new FormData(form);

  fetch("?c=TargetController&m=updateProcess", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        editModal.hide();
        alert(data.success);

        return fetch("?c=TargetController&m=getTargetCards");
      } else {
        throw new Error("Gagal memperbarui target.");
      }
    })
    .then((res) => res.text())
    .then((html) => {
      document.querySelector(".row-cols-md-2").innerHTML = html;
    })
    .catch((err) => alert("Terjadi kesalahan: " + err.message));
});

