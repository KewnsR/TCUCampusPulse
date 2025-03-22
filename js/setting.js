function openModal() {
    document.getElementById('confirmationModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmationModal').classList.add('hidden');
}

// Profile Image Handling
const fileInput = document.getElementById("fileInput");
const profileContainer = document.getElementById("profileContainer"); // The div wrapping the profile image
const profileImg = document.getElementById("profileImg");
const cropperModal = document.getElementById("cropperModal");
const cropperImage = document.getElementById("cropperImage");

let cropper;

// Open Cropper Modal when file is chosen
fileInput.addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            cropperImage.src = event.target.result;
            cropperModal.classList.remove("hidden");
            if (cropper) cropper.destroy();
            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 2,
                movable: true,
                zoomable: true,
                scalable: true,
                rotatable: false,
            });
        };
        reader.readAsDataURL(file);
    }
});

// Zoom & Move Controls
function cropperZoomIn() {
    if (cropper) cropper.zoom(0.1);
}

function cropperZoomOut() {
    if (cropper) cropper.zoom(-0.1);
}

function cropperMoveLeft() {
    if (cropper) cropper.move(-10, 0);
}

function cropperMoveRight() {
    if (cropper) cropper.move(10, 0);
}

// Close Cropper Modal
function closeCropper() {
    cropperModal.classList.add("hidden");
}

// Save Cropped Image & Update Dynamically
function saveCroppedImage() {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas();
        if (canvas) {
            canvas.toBlob((blob) => {
                const formData = new FormData();
                formData.append("profile_image", blob, "cropped_image.png");

                fetch("../php/upload.php", {
                    method: "POST",
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        profileImg.src = data.image_url; // Update profile image without refresh
                        cropperModal.classList.add("hidden");

                        // Update all instances of profile image dynamically
                        document.querySelectorAll(".profile-image").forEach(img => {
                            img.src = data.image_url;
                        });
                    } else {
                        alert("Error uploading image.");
                    }
                })
                .catch(error => console.error("Error:", error));
            }, "image/png");
        }
    }
}

// File input customization for showing selected file name
document.getElementById('fileInput').addEventListener('change', function () {
    const fileName = this.files[0] ? this.files[0].name : "No file chosen";
    document.querySelector('.custom-file-upload').textContent = fileName;
});

function openPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

document.getElementById('newPassword').addEventListener('input', function () {
    let strengthText = document.getElementById('passwordStrength');
    let password = this.value;
    if (password.length < 6) {
        strengthText.textContent = "Weak";
        strengthText.className = "text-red-500";
    } else if (password.length < 10) {
        strengthText.textContent = "Moderate";
        strengthText.className = "text-yellow-500";
    } else {
        strengthText.textContent = "Strong";
        strengthText.className = "text-green-500";
    }
});

function changePassword() {
    let oldPassword = document.getElementById('oldPassword').value;
    let newPassword = document.getElementById('newPassword').value;
    let confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
        alert("New passwords do not match!");
        return;
    }

    fetch("settings.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `old_password=${encodeURIComponent(oldPassword)}&new_password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            closePasswordModal();
        }
    })
    .catch(error => console.error("Error:", error));
}

function updatePasswordLength() {
    const passwordField = document.getElementById('passwordField');
    const passwordLength = document.getElementById('passwordLength');
    passwordLength.textContent = ''.repeat(passwordField.value.length);
}

// Set initial password length on page load
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('passwordField');
    const passwordLength = document.getElementById('passwordLength');
    passwordLength.textContent = ''.repeat(passwordField.value.length);
});

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('ul.flex.border-b li a');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add active class to the clicked tab
            tab.classList.add('active');

            // Hide all tab contents
            tabContents.forEach(content => content.classList.remove('active'));
            // Show the content of the clicked tab
            const target = document.querySelector(tab.getAttribute('href'));
            target.classList.add('active');
        });
    });

    // Activate the first tab by default
    if (tabs.length > 0) {
        tabs[0].click();
    }
});

