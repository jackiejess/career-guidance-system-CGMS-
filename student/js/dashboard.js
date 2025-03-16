const themeToggle = document.getElementById('themeToggle');
const profilePic = document.getElementById('profilePic');
const profileDropdown = document.getElementById('profileDropdown');

themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
});

profilePic.addEventListener('click', () => {
    profileDropdown.classList.toggle('active');
});
