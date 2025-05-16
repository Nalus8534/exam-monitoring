function navigate(tab) {
    window.location.href = tab;
}
document.addEventListener("DOMContentLoaded", function() {
  const modal = document.getElementById("userModal");
  const addUserBtn = document.getElementById("addUserBtn");
  const closeBtn = document.getElementsByClassName("close")[0];
  const userForm = document.getElementById("userForm");
  const modalTitle = document.getElementById("modalTitle");
  const actionInput = document.getElementById("action");

  // Open modal when "Add New User" is clicked
  addUserBtn.addEventListener("click", function() {
    modal.style.display = "block";
    modalTitle.textContent = "Add New User";
    actionInput.value = "add";
    userForm.reset();
  });

  // Close modal
  closeBtn.addEventListener("click", function() {
    modal.style.display = "none";
  });

  window.addEventListener("click", function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });

  // Handle form submission using AJAX
  userForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(userForm);
    fetch('process_user.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      // Ideally, refresh the table or update the DOM instead of reloading the page.
      window.location.reload();
    })
    .catch(error => console.error('Error:', error));
  });

  // Optionally, add similar event listeners for edit and delete buttons
});
