document.addEventListener("DOMContentLoaded", function () {
    const roleButtons = document.querySelectorAll('.role-select button');
    const studentInput = document.querySelector('.student-input');
    const roleInputRegister = document.getElementById('roleInputRegister');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');

    // Function to toggle student number field visibility
    function toggleStudentInput(role) {
        if (studentInput) {
            if (role === "Student") {
                studentInput.style.display = "block"; // Show student number input
            } else {
                studentInput.style.display = "none";  // Hide it for other roles
                studentInput.value = ""; // Clear the input field
            }
        }
    }

    // Default selection: Always set "Student" as selected on page load
    function setDefaultRole() {
        const defaultRoleButton = document.querySelector('#registerForm .role-select button[data-role="Student"]');
        if (defaultRoleButton) {
            defaultRoleButton.classList.add('selected'); // Add selected class to "Student" button
            roleInputRegister.value = "Student"; // Set hidden input to "Student"
            toggleStudentInput("Student"); // Ensure student number field is visible
        }
    }

    // Role selection logic
    roleButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent button's default behavior

            const formContainer = this.closest("form"); // Find the correct form (login/register)
            const roleInput = formContainer.querySelector('input[name="role"]'); // Get the role input for that form

            // Remove 'selected' class from all buttons within the same form
            this.parentElement.querySelectorAll('button').forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');

            const roleValue = this.getAttribute('data-role');
            roleInput.value = roleValue;

            // Hide/show student number only in register form
            if (formContainer.id === "registerForm") {
                toggleStudentInput(roleValue);
            }
        });
    });

    // ✅ Attach functions to window so they are globally accessible
    window.showLogin = function () {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        loginTab?.classList.add('active');
        registerTab?.classList.remove('active');
    };

    window.showRegister = function () {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
        registerTab?.classList.add('active');
        loginTab?.classList.remove('active');

        // Ensure the correct role is selected and student number visibility is updated
        setDefaultRole();
    };

    // Detect if 'register=true' is in URL to auto-switch tabs
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('register') === 'true') {
        showRegister();
    }

    // Attach event listeners to tabs
    loginTab?.addEventListener("click", showLogin);
    registerTab?.addEventListener("click", showRegister);

    // Ensure Student is selected by default on page load
    setDefaultRole();
});
