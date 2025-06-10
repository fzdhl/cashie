const btn = document.getElementById("fileName");
const fileInput = document.querySelector('.icon-btn');
const preview = document.querySelector('.foto-preview');
const reader = new FileReader();

reader.onload = function (e) {
    preview.src = e.target.result;
}

btn.addEventListener("click", function() {
    fileInput.click();
})

fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
    reader.readAsDataURL(file);
    }
    btn.innerText = `File: "${file.name}"`; 
})