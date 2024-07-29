document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navMenu = document.getElementById('nav-menu');
    const closeMenu = document.getElementById('close-menu');

    hamburgerMenu.addEventListener('click', function() {
        navMenu.classList.add('open');
    });

    closeMenu.addEventListener('click', function() {
        navMenu.classList.remove('open');
    });
});
