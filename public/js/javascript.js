const hamMenu = document.querySelector(".ham-menu");
const menu = document.querySelector(".menu");
hamMenu.addEventListener("click", () => {
    menu.classList.toggle("active");
});