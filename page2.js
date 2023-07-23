// page2.js
function toggleDarkMode(event) {
  event.preventDefault(); // Disable default form submission behavior

  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table = document.querySelector('.styled-table');
  var isDarkMode = body.classList.toggle('dark-mode');

  // Update card and table class based on dark mode
  card.classList.toggle('dark-mode', isDarkMode);
  table.classList.toggle('dark-mode', isDarkMode);

  // Store dark mode preference
  localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
 
  
}
darkModeCheckbox.addEventListener('change', function(event) {
  toggleDarkMode(event);
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