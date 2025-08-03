document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');

    mobileMenuToggle.addEventListener('click', function () {
        mobileNav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
    });
});