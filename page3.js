// page3.js 
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
        // aplly here also
    var imageGallery = document.querySelector('.image-gallery');
    imageGallery.classList.add('dark-mode');
    }
});

function showUploadForm() {
    document.querySelector('.uploadForm').innerHTML = `
      <h4>Edit Upload Details</h4>
      <form class="my-s" method="post" enctype="multipart/form-data" id="uploadForm">
        <div class="form-group">
          <label for="image">Choose an image:</label>
          <input type="file" name="image" class="form-control" id="image" required>
        </div>
        <div class="form-group">
          <label for="heading">Heading:</label>
          <input type="text" name="heading" class="form-control" id="heading" required>
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea name="description" class="form-control" id="description" required></textarea>
        </div>
        <div class="form-group">
          <button type="button" onclick="submitUploadForm()" class="btn btn-success">Upload</button>
          <button type="button" onclick="cancelUploadForm()" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    `;
  }
  
  function submitUploadForm() {
    var form = document.querySelector('#uploadForm');
    form.submit();
  }
  
function cancelUploadForm() {
    document.querySelector('.uploadForm').innerHTML = `
        <button onclick="showUploadForm()" class="btn btn-primary">Upload Details</button>
    `;
}
function showEditForm() {
  var editForm = document.querySelector('.editForm');
  editForm.style.display = 'block';
}

function cancelEditForm() {
  var editForm = document.querySelector('.editForm');
  editForm.style.display = 'none';
}
