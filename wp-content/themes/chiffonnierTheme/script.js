/* ================= menu burger ================= */
document.addEventListener('DOMContentLoaded', function() {
  const body = document.body;
  const menuToggle = document.getElementById('menu-toggle');
  const menuOverlay = document.getElementById('menu-overlay');
  const primaryNav = document.getElementById('primary-nav');

  function openMenu() {
    body.classList.add('menu-open');
  }

  function closeMenu() {
    body.classList.remove('menu-open');
  }

  menuToggle.addEventListener('click', function() {
    if (body.classList.contains('menu-open')) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  menuOverlay.addEventListener('click', closeMenu);
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



