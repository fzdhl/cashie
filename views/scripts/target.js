document.getElementById("targetForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formElement = e.target;

  const formData = new FormData(formElement);

  fetch("?c=TargetController&m=createProcess", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    alert(data);
    formElement.reset();
  })
  .catch(err => console.error("Fetch error:", err));
});