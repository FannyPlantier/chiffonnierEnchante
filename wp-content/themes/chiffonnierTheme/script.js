/* ================= menu burger ================= */
const menuToggle = document.getElementById('menu-toggle');
const header = document.getElementById('mon-header');
const nav = document.getElementById('primary-nav');

if (menuToggle && header && nav) {
  menuToggle.addEventListener('click', () => {
    header.classList.toggle('menu-open');

    // Si le menu vient d'être fermé, on ferme tous les sous-menus
    if (!header.classList.contains('menu-open')) {
      const submenus = nav.querySelectorAll('ul ul');
      submenus.forEach(submenu => {
        submenu.style.display = 'none';
      });
    }
  });
}

document.addEventListener("DOMContentLoaded", function() {
  // Sélectionne tous les liens parents qui ont un sous-menu
  const parentLinks = document.querySelectorAll('#primary-nav ul > li > a');

  parentLinks.forEach(link => {
    const submenu = link.nextElementSibling;
    if (submenu && submenu.tagName === 'UL') {
      link.addEventListener('click', function(e) {
        // Seulement en mobile/tablette
        if (window.innerWidth <= 768) {
          e.preventDefault(); // Empêche la navigation
          submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        }
      });
    }
  });
});



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



