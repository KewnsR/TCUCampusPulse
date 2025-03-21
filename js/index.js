function showLogoutModal() {
    document.getElementById('logoutModal').classList.remove('hidden');
}

function hideLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
}

function toggleProfileDropdown() {
    document.getElementById('profileDropdown').classList.toggle('hidden');
}

// Prevent back navigation after login
(function() {
    history.pushState(null, null, location.href);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, location.href);
    });

    window.addEventListener('keydown', function (e) {
        if (e.altKey && e.key === 'ArrowLeft') {
            e.preventDefault();
        }
    });
})();

// Toggle profile dropdown
document.getElementById('profileButton')?.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent click from propagating to the body
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.body.addEventListener('click', () => {
    const dropdown = document.getElementById('profileDropdown');
    if (!dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
    }
});

document.getElementById("deleteProfilePicture").addEventListener("click", function () {
    if (confirm("Are you sure you want to reset your profile picture to the default?")) {
        fetch("resetProfilePicture.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: "reset" }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Profile picture has been reset to default.");
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert("Failed to reset profile picture. Please try again.");
                }
            })
            .catch((error) => console.error("Error:", error));
    }
});

// Toggle button styles
document.getElementById('sortPopular').addEventListener('click', function() {
    this.classList.add('bg-gray-100', 'text-black', 'font-semibold');
    document.getElementById('sortRecent').classList.remove('bg-gray-100', 'text-black', 'font-semibold');
});

document.getElementById('sortRecent').addEventListener('click', function() {
    this.classList.add('bg-gray-100', 'text-black', 'font-semibold');
    document.getElementById('sortPopular').classList.remove('bg-gray-100', 'text-black', 'font-semibold');
});

//NOTIFICATION
document.addEventListener("DOMContentLoaded", function () {
    fetchNotifications();
});

function fetchNotifications() {
    fetch('notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            updateNotificationBadge(data.length);
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById("notification-badge");
    if (count > 0) {
        badge.innerText = count;
        badge.style.display = "block";
    } else {
        badge.style.display = "none";
    }
}

//DELETE PROFILE PICTURE
document.getElementById("deleteProfilePicture")?.addEventListener("click", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to reset your profile picture to the default?")) {
        fetch("resetProfilePicture.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: "reset" }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Profile picture has been reset to default.");
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert("Failed to reset profile picture. Please try again.");
                }
            })
            .catch((error) => console.error("Error:", error));
    }
});