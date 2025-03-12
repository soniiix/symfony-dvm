const navbarToggle = document.querySelector('.navbar-toggle');
const navbarMenu = document.querySelector('.navbar-menu');

  navbarToggle.addEventListener('click', () => {
    navbarMenu.classList.toggle('active');
  });

// Barre de recherche
let searchInput = document.querySelector("#searchDriver");
let conducteurRows = document.querySelectorAll(".driverRow"); 

searchInput.addEventListener('input', (e) => {
    let searchValue = e.target.value.toLowerCase();
    conducteurRows.forEach((row) => {
        let driverName = row.querySelector(".driverName").innerText.toLowerCase();
        if (driverName.includes(searchValue)) {
            row.removeAttribute('hidden');
        } else {
            row.setAttribute('hidden', '');
        }
    })
})