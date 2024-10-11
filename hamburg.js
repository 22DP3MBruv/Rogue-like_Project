document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const open = document.getElementById('open');
    const modal = new bootstrap.Modal('#modalid');
    
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        // menu-toggle ir checkbox kurš attaisa hamburger menu
        // ja ir atvērts tad aizver, ja ir aizvērts tad atver
        document.getElementById('menu-toggle').checked = !document.getElementById('menu-toggle').checked;
    });

    open.addEventListener('click', () => {
        modal.show();
    });
});