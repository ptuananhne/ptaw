document.addEventListener("DOMContentLoaded", function () {
  // --- LOGIC MENU DI ĐỘNG (ĐÃ SỬA LỖI) ---
  // Lấy đúng ID của các phần tử từ file header.php mới
  const menuToggle = document.getElementById("mobile-menu-toggle");
  const sidebar = document.getElementById("sidebar-mobile"); // Quan trọng: ID là "sidebar-mobile"
  const overlay = document.getElementById("menu-overlay");
  const closeBtn = document.getElementById("sidebar-close-btn");

  // Chỉ chạy code nếu tất cả các phần tử tồn tại
  if (menuToggle && sidebar && overlay && closeBtn) {
    const openMenu = () => {
      sidebar.classList.add("is-open");
      overlay.classList.add("is-active");
      document.body.style.overflow = "hidden"; // Chặn cuộn trang khi menu mở
    };

    const closeMenu = () => {
      sidebar.classList.remove("is-open");
      overlay.classList.remove("is-active");
      document.body.style.overflow = ""; // Cho phép cuộn lại
    };

    // Gán sự kiện click
    menuToggle.addEventListener("click", (e) => {
      e.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
      openMenu();
    });

    overlay.addEventListener("click", closeMenu);
    closeBtn.addEventListener("click", closeMenu);
  }

  // --- LOGIC SLIDER SẢN PHẨM ---
  function initializeProductSlider(sliderElement) {
    const track = sliderElement.querySelector(".slider-track");
    const prevBtn = sliderElement.querySelector(".slider-btn.prev");
    const nextBtn = sliderElement.querySelector(".slider-btn.next");
    if (!track || !prevBtn || !nextBtn) return;

    let isDragging = false,
      startX,
      startScrollLeft,
      draggedDistance = 0;

    const updateButtons = () => {
      const maxScroll = track.scrollWidth - track.clientWidth;
      const currentScroll = Math.round(track.scrollLeft);
      const isScrollable = maxScroll > 5;
      sliderElement.classList.toggle("is-scrollable", isScrollable);
      prevBtn.disabled = currentScroll < 5;
      nextBtn.disabled = currentScroll >= maxScroll - 5;
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

    track.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", (e) => {
        if (draggedDistance > 10) e.preventDefault();
      });
    });

    track.addEventListener("mousedown", startDrag);
    track.addEventListener("touchstart", startDrag, { passive: true });
    track.addEventListener("mousemove", onDrag);
    track.addEventListener("touchmove", onDrag, { passive: true });
    document.addEventListener("mouseup", stopDrag);
    document.addEventListener("touchend", stopDrag);

    const scrollAmount = () => track.clientWidth * 0.8;
    nextBtn.addEventListener("click", () =>
      track.scrollBy({ left: scrollAmount(), behavior: "smooth" })
    );
    prevBtn.addEventListener("click", () =>
      track.scrollBy({ left: -scrollAmount(), behavior: "smooth" })
    );

    track.addEventListener("scroll", updateButtons, { passive: true });
    new ResizeObserver(() => setTimeout(updateButtons, 150)).observe(track);
    setTimeout(updateButtons, 150);
  }
  document.querySelectorAll(".product-slider").forEach(initializeProductSlider);

  // --- LOGIC SLIDER BANNER ---
  const bannerSlider = document.querySelector(".banner-slider-container");
  if (bannerSlider) {
    const track = bannerSlider.querySelector(".banner-track");
    const slides = Array.from(track.children);
    if (slides.length <= 1) return;

    const nextButton = bannerSlider.querySelector(".banner-btn.next");
    const prevButton = bannerSlider.querySelector(".banner-btn.prev");
    let currentIndex = 0;
    let intervalId;

    const moveToSlide = (targetIndex) => {
      if (targetIndex < 0 || targetIndex >= slides.length) return;
      const slideWidth = slides[0].getBoundingClientRect().width;
      track.style.transform = `translateX(-${slideWidth * targetIndex}px)`;
      currentIndex = targetIndex;
      updateBannerButtons();
    };
    const updateBannerButtons = () => {
      if (!prevButton || !nextButton) return;
      prevButton.disabled = currentIndex === 0;
      nextButton.disabled = currentIndex === slides.length - 1;
    };
    const startAutoPlay = () => {
      stopAutoPlay();
      intervalId = setInterval(() => {
        let nextIndex = (currentIndex + 1) % slides.length;
        moveToSlide(nextIndex);
      }, 5000);
    };
    const stopAutoPlay = () => clearInterval(intervalId);

    if (prevButton && nextButton) {
      prevButton.addEventListener("click", () => {
        moveToSlide(currentIndex - 1);
        stopAutoPlay();
      });
      nextButton.addEventListener("click", () => {
        moveToSlide(currentIndex + 1);
        stopAutoPlay();
      });
    }
    bannerSlider.addEventListener("mouseenter", stopAutoPlay);
    bannerSlider.addEventListener("mouseleave", startAutoPlay);
    new ResizeObserver(() => moveToSlide(currentIndex)).observe(bannerSlider);

    updateBannerButtons();
    startAutoPlay();
  }

  // --- LOGIC TRANG CHI TIẾT SẢN PHẨM ---
  const productDetailPage = document.querySelector(".product-detail-page");
  if (productDetailPage) {
    const mainImage = document.getElementById("main-product-image");
    const thumbnails = document.querySelectorAll(".thumbnail-item");
    if (mainImage && thumbnails.length > 0) {
      thumbnails.forEach((thumb) => {
        thumb.addEventListener("click", function () {
          thumbnails.forEach((t) => t.classList.remove("active"));
          this.classList.add("active");
          mainImage.src = this.querySelector("img").src;
        });
      });
    }
    const fbButton = document.getElementById("fb-messenger-btn");
    if (fbButton) {
      fbButton.addEventListener("click", function (e) {
        e.preventDefault();
        const productUrl = window.location.href;
        const originalText = this.querySelector("span").innerText;
        const textToCopy = `Chào bạn, tôi quan tâm đến sản phẩm này: ${productUrl}`;
        navigator.clipboard
          .writeText(textToCopy)
          .then(() => {
            this.querySelector("span").innerText = "Đã sao chép link!";
            window.open(this.href, "_blank");
            setTimeout(() => {
              this.querySelector("span").innerText = originalText;
            }, 2500);
          })
          .catch((err) => {
            console.error("Không thể sao chép link: ", err);
            this.querySelector("span").innerText = "Lỗi sao chép";
            setTimeout(() => {
              this.querySelector("span").innerText = originalText;
            }, 2500);
          });
      });
    }
  }
});
