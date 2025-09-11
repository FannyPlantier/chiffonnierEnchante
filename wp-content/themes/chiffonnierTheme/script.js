/* ================= menu burger ================= */
const menuToggle = document.getElementById('menu-toggle');
const header = document.getElementById('mon-header');
const nav = document.getElementById('primary-nav');

if (menuToggle && header && nav) {
  menuToggle.addEventListener('click', () => {
    header.classList.toggle('menu-open'); // burger → croix

    const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
    menuToggle.setAttribute('aria-expanded', String(!expanded));
    nav.setAttribute('aria-hidden', String(expanded));
  });
}



/* ================= conférence toggle ================= */
document.addEventListener("DOMContentLoaded", function() {
  const items = document.querySelectorAll(".conf-item");

  items.forEach((item, index) => {
    const detail = item.querySelector(".conf-detail");

    // ouvrir le premier élément
    if (index === 0) {
      item.classList.add("open");
      detail.style.maxHeight = detail.scrollHeight + "px";
      detail.style.opacity = 1;
    }

    item.addEventListener("click", () => {
      if (item.classList.contains("open")) {
        // fermeture
        detail.style.maxHeight = detail.scrollHeight + "px"; // nécessaire pour déclencher la transition
        requestAnimationFrame(() => {
          detail.style.maxHeight = 0;
          detail.style.opacity = 0;
        });
        item.classList.remove("open");
      } else {
        // ouverture
        detail.style.maxHeight = detail.scrollHeight + "px";
        detail.style.opacity = 1;
        item.classList.add("open");
      }
    });
  });
});



