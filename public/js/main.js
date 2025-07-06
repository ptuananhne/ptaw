document.addEventListener("DOMContentLoaded", function () {
  // --- LOGIC MENU DI ĐỘNG ---
  const menuToggle = document.getElementById("mobile-menu-toggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("menu-overlay");
  const closeBtn = document.getElementById("sidebar-close-btn");

  const openMenu = () => {
    sidebar.classList.add("is-open");
    overlay.classList.add("is-active");
    document.body.style.overflow = "hidden";
  };

  const closeMenu = () => {
    sidebar.classList.remove("is-open");
    overlay.classList.remove("is-active");
    document.body.style.overflow = "";
  };

  if (menuToggle && sidebar && overlay && closeBtn) {
    menuToggle.addEventListener("click", (e) => {
      e.stopPropagation();
      openMenu();
    });
    overlay.addEventListener("click", closeMenu);
    closeBtn.addEventListener("click", closeMenu);
  }

  // --- LOGIC SLIDER HOÀN CHỈNH V3 ---
  function initializeSlider(sliderElement) {
    const track = sliderElement.querySelector(".slider-track");
    const prevBtn = sliderElement.querySelector(".slider-btn.prev");
    const nextBtn = sliderElement.querySelector(".slider-btn.next");

    if (!track || !prevBtn || !nextBtn) return;

    let isDragging = false,
      startX,
      startScrollLeft,
      draggedDistance = 0;

    const updateButtons = () => {
      const currentScroll = Math.round(track.scrollLeft);
      const maxScroll = track.scrollWidth - track.clientWidth;
      prevBtn.disabled = currentScroll <= 0;
      nextBtn.disabled = currentScroll >= maxScroll;
    };

    const startDrag = (e) => {
      isDragging = true;
      draggedDistance = 0;
      track.classList.add("active-drag");
      startX = e.pageX || e.touches[0].pageX;
      startScrollLeft = track.scrollLeft;
    };

    const onDrag = (e) => {
      if (!isDragging) return;
      e.preventDefault();
      const currentX = e.pageX || e.touches[0].pageX;
      const walk = currentX - startX;
      draggedDistance = Math.abs(walk);
      track.scrollLeft = startScrollLeft - walk;
    };

    const stopDrag = () => {
      isDragging = false;
      track.classList.remove("active-drag");
    };

    // Ngăn click vào link khi đang kéo
    track.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", (e) => {
        if (draggedDistance > 10) {
          // Nếu kéo nhiều hơn 10px thì không cho click
          e.preventDefault();
        }
      });
    });

    // Gắn sự kiện
    track.addEventListener("mousedown", startDrag);
    track.addEventListener("touchstart", startDrag, { passive: true });

    track.addEventListener("mousemove", onDrag);
    track.addEventListener("touchmove", onDrag);

    document.addEventListener("mouseup", stopDrag);
    document.addEventListener("touchend", stopDrag);

    // Nút bấm
    const scrollAmount = () => track.clientWidth * 0.8; // Cuộn 80% chiều rộng
    nextBtn.addEventListener("click", () =>
      track.scrollBy({ left: scrollAmount(), behavior: "smooth" })
    );
    prevBtn.addEventListener("click", () =>
      track.scrollBy({ left: -scrollAmount(), behavior: "smooth" })
    );

    // Cập nhật
    track.addEventListener("scroll", updateButtons);
    window.addEventListener("resize", () => setTimeout(updateButtons, 250));
    setTimeout(updateButtons, 150);
  }

  document.querySelectorAll(".product-slider").forEach(initializeSlider);
});
