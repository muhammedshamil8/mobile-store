function openSettings() {
  var settingsPage = document.querySelector('.settings-page');
  settingsPage.style.display = 'block';
}

function closeSettings() {
  var settingsPage = document.querySelector('.settings-page');
  settingsPage.style.display = 'none';
}

function toggleDarkMode() {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table2 = document.querySelector('.styled-table'); // Corrected variable name
  var headname = document.querySelector('.headname');
  var h1 = document.querySelector('.headname h1');
  var img = document.querySelector('.headname img');
  var darkMode = body.classList.toggle('dark-mode');

  // Update classes based on dark mode
  card.classList.toggle('dark-mode', darkMode);
  table2.classList.toggle('dark-mode', darkMode); // Corrected variable name
  headname.classList.toggle('dark-mode', darkMode);

  // Update dark mode toggle button state
  var darkModeCheckbox = document.querySelector('input[name="darkMode"]');
  darkModeCheckbox.checked = darkMode;

  // Store dark mode preference
  localStorage.setItem('darkMode', darkMode ? 'true' : 'false');
}

// Retrieve the dark mode preference from localStorage and apply the dark mode on page load
document.addEventListener('DOMContentLoaded', function () {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table2 = document.querySelector('.styled-table'); // Corrected variable name
  var headname = document.querySelector('.headname');
  var darkMode = localStorage.getItem('darkMode');

  if (darkMode === 'true') {
    body.classList.add('dark-mode');
    card.classList.add('dark-mode');
    table2.classList.add('dark-mode'); // Corrected variable name
    headname.classList.add('dark-mode');
  }

  // Update dark mode toggle button state
  var darkModeCheckbox = document.querySelector('input[name="darkMode"]');
  darkModeCheckbox.checked = darkMode === 'true';
});

  
  const searchFun = () => {
        let filter = document.getElementById('myInput').value.toUpperCase();
        let mytable = document.getElementById('mytable');
        let tr = mytable.getElementsByTagName('tr');
  
        for (var i = 0; i < tr.length; i++) {
          let td = tr[i].getElementsByTagName('td')[1];
  
          if (td) {
            let textvalue = td.textContent || td.innerHTML;
  
            if (textvalue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }
        }
      }
      function clearSearch() {
        var input = document.getElementById('myInput');
        var tr = document.getElementsByTagName('tr');
        input.value = ''; // Clear the input value
  
        // Display all table rows
        for (var i = 0; i < tr.length; i++) {
          tr[i].style.display = "";
        }
      }
  
      function showAddproductForm() {
        var addGroupForm = document.querySelector('.add-group-form');
        addGroupForm.style.display = 'block';
      }
  
      function backspace() {
        var addGroupForm = document.querySelector('.add-group-form');
        addGroupForm.style.display = 'none';
      }
  
      function confirmRemove() {
        return confirm("Are you sure you want to remove this product?");
      }
      // completed the project