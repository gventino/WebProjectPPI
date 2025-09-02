document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const window = document.defaultView;
    var controller = false;

    mobileMenuToggle.addEventListener('click', function () {
        controller = !controller;
        mobileNav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
    });

    window.addEventListener('resize', function(){
        if(controller){
        mobileNav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
        controller = false;
        }
    });

});