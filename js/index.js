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
});

function showPostModal() {
    document.getElementById('postModal').classList.remove('hidden');
}

function hidePostModal() {
    document.getElementById('postModal').classList.add('hidden');
}
