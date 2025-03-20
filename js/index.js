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

// Toggle button styles
document.getElementById('sortPopular').addEventListener('click', function() {
    this.classList.add('bg-gray-100', 'text-black', 'font-semibold');
    document.getElementById('sortRecent').classList.remove('bg-gray-100', 'text-black', 'font-semibold');
});

document.getElementById('sortRecent').addEventListener('click', function() {
    this.classList.add('bg-gray-100', 'text-black', 'font-semibold');
    document.getElementById('sortPopular').classList.remove('bg-gray-100', 'text-black', 'font-semibold');
});
