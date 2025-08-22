const toggleMenu = document.querySelector(".toggle-menu");
const navMenu = document.querySelector(".nav-links");

const main = document.querySelector("main");

toggleMenu.addEventListener('click', () => {
    toggleMenu.classList.toggle('active');
    navMenu.classList.toggle('active');
})

main.addEventListener('click', () => {
    if (toggleMenu.classList.contains('active') && navMenu.classList.contains('active')) {
        toggleMenu.classList.remove('active');
        navMenu.classList.remove('active');
    }
})