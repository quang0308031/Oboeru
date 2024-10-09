// JavaScript to toggle dropdown menu when clicking the "+" button
document.querySelector('.plus-btn').addEventListener('click', function() {
    const dropdown = document.getElementById('dropdownMenu');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});
