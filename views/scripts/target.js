document.getElementById("targetForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  fetch("submit.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("User saved with ID: " + data.id);
    } else {
      alert("Error: " + data.error);
    }
  })
  .catch(err => console.error("Fetch error:", err));
});