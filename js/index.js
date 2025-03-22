// Toggle Profile Dropdown
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('hidden');
}

// Show and Hide Post Modal
function showPostModal() {
    const postModal = document.getElementById('postModal');
    postModal.classList.remove('hidden');
}

function hidePostModal() {
    const postModal = document.getElementById('postModal');
    postModal.classList.add('hidden');
}

// Show and Hide Messages Popup
function showMessagesPopup() {
    const messagesPopup = document.getElementById('messagesPopup');
    messagesPopup.classList.remove('hidden');
}

function hideMessagesPopup() {
    const messagesPopup = document.getElementById('messagesPopup');
    messagesPopup.classList.add('hidden');
}

// Show and Hide Logout Modal
function showLogoutModal() {
    const logoutModal = document.getElementById('logoutModal');
    logoutModal.classList.remove('hidden');
}

function hideLogoutModal() {
    const logoutModal = document.getElementById('logoutModal');
    logoutModal.classList.add('hidden');
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

document.addEventListener("DOMContentLoaded", () => {
    const openMessagesButton = document.getElementById("openMessages");
    const closeMessagesButton = document.getElementById("closeMessages");
    const messagesPopup = document.getElementById("messagesPopup");

    if (openMessagesButton && closeMessagesButton && messagesPopup) {
        openMessagesButton.addEventListener("click", () => {
            messagesPopup.classList.remove("hidden");
        });

        closeMessagesButton.addEventListener("click", () => {
            messagesPopup.classList.add("hidden");
        });

        // Close messages popup when clicking outside of it
        document.addEventListener("click", (e) => {
            if (!messagesPopup.contains(e.target) && e.target !== openMessagesButton) {
                messagesPopup.classList.add("hidden");
            }
        });
    }

    const sortPopularButton = document.getElementById("sortPopular");
    const sortRecentButton = document.getElementById("sortRecent");

    if (sortPopularButton && sortRecentButton) {
        sortPopularButton.addEventListener("click", () => {
            // Logic for sorting by Popular
            console.log("Sorting by Popular");
            // Add your sorting logic here (e.g., fetch sorted data via AJAX)
        });

        sortRecentButton.addEventListener("click", () => {
            // Logic for sorting by Recent
            console.log("Sorting by Recent");
            // Add your sorting logic here (e.g., fetch sorted data via AJAX)
        });
    }

    // Profile Dropdown Toggle
    const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");

    if (profileButton && profileDropdown) {
        profileButton.addEventListener("click", () => {
            profileDropdown.classList.toggle("hidden");
        });

        // Close profile dropdown when clicking outside of it
        document.addEventListener("click", (e) => {
            if (!profileDropdown.contains(e.target) && e.target !== profileButton) {
                profileDropdown.classList.add("hidden");
            }
        });
    }

    // "What's on your mind" Post Modal
    const openPostModalButton = document.getElementById("openPostModal");
    const closePostModalButton = document.getElementById("closePostModal");
    const postModal = document.getElementById("postModal");

    if (openPostModalButton && closePostModalButton && postModal) {
        openPostModalButton.addEventListener("click", () => {
            postModal.classList.remove("hidden");
        });

        closePostModalButton.addEventListener("click", () => {
            postModal.classList.add("hidden");
        });

        // Close post modal when clicking outside of it
        document.addEventListener("click", (e) => {
            if (!postModal.contains(e.target) && e.target !== openPostModalButton) {
                postModal.classList.add("hidden");
            }
        });
    }
});
