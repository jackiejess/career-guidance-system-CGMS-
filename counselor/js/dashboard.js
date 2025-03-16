// Add interactivity here (e.g., handling notifications, profile dropdown, etc.)
document.querySelector('.notification').addEventListener('click', () => {
  alert('You have no new notifications.');
});

// Example: Toggle profile dropdown
const profile = document.querySelector('.profile');
profile.addEventListener('click', () => {
  alert('Profile dropdown clicked!');
  // You can add a dropdown menu here
});