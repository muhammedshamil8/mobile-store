function toggleDarkMode() {
  var body = document.querySelector('body');
  body.classList.toggle('dark-mode');

  // Store dark mode preference
  var darkModeCheckbox = document.querySelector('input[name="darkMode"]');
  localStorage.setItem('darkMode', darkModeCheckbox.checked);
}

// Retrieve the dark mode preference from localStorage and apply the dark mode on page load
document.addEventListener('DOMContentLoaded', function () {
  var body = document.querySelector('body');
  var darkModeCheckbox = document.querySelector('input[name="darkMode"]');
  darkModeCheckbox.checked = localStorage.getItem('darkMode') === 'true';
  if (darkModeCheckbox.checked) {
      body.classList.add('dark-mode');

      var imageGallery = document.querySelector('.image-gallery');
      imageGallery.classList.add('dark-mode');
      }
});
function showEditForm() {
  var editForm = document.querySelector('.editForm');
  editForm.style.display = 'block';
}

function cancelEditForm() {
  var editForm = document.querySelector('.editForm');
  editForm.style.display = 'none';
}

function removeUpload() {
  if (confirm("Are you sure you want to remove the upload?")) {
      var form = document.createElement('form');
      form.method = 'post';
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'removeUpload';
      input.value = '1';
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
  }
}
// completed the project